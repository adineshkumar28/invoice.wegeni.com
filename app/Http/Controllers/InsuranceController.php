<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInsuranceRequest;
use App\Http\Requests\UpdateInsuranceRequest;
use App\Models\Insurance;
use App\Models\Client;
use App\Models\Category;
use App\Models\User;
use App\Models\TenantWiseClient;
use App\Repositories\InsuranceRepository;
use App\Notifications\InsuranceExpiryReminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Stancl\Tenancy\Database\TenantScope;
use Illuminate\Contracts\View\View;

class InsuranceController extends AppBaseController
{
    public $insuranceRepository;

    public function __construct(InsuranceRepository $insuranceRepo)
    {
        $this->insuranceRepository = $insuranceRepo;
    }

    public function index(): View
    {
        return view('insurances.index');
    }

    public function create(): View|RedirectResponse
    {
        try {
            // Get clients through the tenant relationship like in ClientController
            $tenantID = getLogInUser()->tenant_id;
            
            $clients = User::with(['client', 'clients'])
                ->whereHas('clients', function ($q) use ($tenantID) {
                    $q->where('tenant_id', $tenantID);
                })
                ->withoutGlobalScope(new TenantScope())
                ->get()
                ->mapWithKeys(function ($user) use ($tenantID) {
                    $clientTenant = $user->clients()->where('tenant_id', $tenantID)->first();
                    $clientData = $user->client;
                
                    if ($clientData && $clientTenant) {
                        $fullName = trim($user->first_name . ' ' . $user->last_name);
                        return [$clientData->id => $fullName];
                    }
                    return [];
                });

            $categories = Category::where('tenant_id', Auth::user()->tenant_id)
                                ->pluck('name', 'id');

            return view('insurances.create', compact('clients', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in insurance create: ' . $e->getMessage());
            Flash::error('Error loading create page: ' . $e->getMessage());
            return redirect()->route('insurances.index');
        }
    }

    public function store(CreateInsuranceRequest $request): RedirectResponse
    {
        $input = $request->all();
        $this->insuranceRepository->store($input);
        Flash::success(__('Insurance created successfully.'));
        return redirect()->route('insurances.index');
    }

    public function show($insuranceId): View|RedirectResponse
    {
        $insurance = Insurance::with(['client.user', 'category'])
                          ->where('id', $insuranceId)
                          ->where('tenant_id', Auth::user()->tenant_id)
                          ->first();

        if (!$insurance) {
            Flash::error(__('Insurance not found.'));
            return redirect()->route('insurances.index');
        }

        return view('insurances.show', compact('insurance'));
    }

    public function edit($insuranceId): View|RedirectResponse
    {
        $insurance = Insurance::where('id', $insuranceId)
                             ->where('tenant_id', Auth::user()->tenant_id)
                             ->first();

        if (!$insurance) {
            Flash::error(__('Insurance not found.'));
            return redirect()->route('insurances.index');
        }

        // Get clients the same way as in create method
        $tenantID = getLogInUser()->tenant_id;
        
        $clients = User::with(['client', 'clients'])
            ->whereHas('clients', function ($q) use ($tenantID) {
                $q->where('tenant_id', $tenantID);
            })
            ->withoutGlobalScope(new TenantScope())
            ->get()
            ->mapWithKeys(function ($user) use ($tenantID) {
                $clientTenant = $user->clients()->where('tenant_id', $tenantID)->first();
                $clientData = $user->client;
            
                if ($clientData && $clientTenant) {
                    $fullName = trim($user->first_name . ' ' . $user->last_name);
                    return [$clientData->id => $fullName];
                }
                return [];
            });

        $categories = Category::where('tenant_id', Auth::user()->tenant_id)
                            ->pluck('name', 'id');

        return view('insurances.edit', compact('insurance', 'clients', 'categories'));
    }

    public function update(UpdateInsuranceRequest $request, Insurance $insurance): RedirectResponse
    {
        if ($insurance->tenant_id != Auth::user()->tenant_id) {
            Flash::error(__('Seems, you are not allowed to access this record.'));
            return redirect()->route('insurances.index');
        }

        $input = $request->all();
        $this->insuranceRepository->updateInsurance($input, $insurance->id);
        Flash::success(__('Insurance updated successfully.'));
        return redirect()->route('insurances.index');
    }

    public function destroy(Insurance $insurance): JsonResponse
    {
        if ($insurance->tenant_id != Auth::user()->tenant_id) {
            return $this->sendError(__('Seems, you are not allowed to access this record.'));
        }

        $insurance->delete();
        return $this->sendSuccess(__('Insurance deleted successfully.'));
    }

    public function sendReminderEmail(Request $request): JsonResponse
    {
        try {
            $insuranceId = $request->get('insurance_id');
            $days = $request->get('days');
            $testEmail = $request->get('test_email');
            
            if (!$insuranceId) {
                return $this->sendError('Insurance ID is required');
            }
            
            // Get insurance with client details
            $insurance = Insurance::with(['client.user'])
                                 ->where('id', $insuranceId)
                                 ->where('tenant_id', Auth::user()->tenant_id)
                                 ->first();
                                 
            if (!$insurance) {
                return $this->sendError('Insurance not found');
            }
            
            // Determine recipient
            $recipient = null;
            $recipientEmail = null;
            
            if ($testEmail) {
                // Send to test email
                $recipient = (object) [
                    'email' => $testEmail,
                    'first_name' => 'Test',
                    'last_name' => 'User'
                ];
                $recipientEmail = $testEmail;
            } else {
                // Send to actual client
                if ($insurance->client && $insurance->client->user_id) {
                    $recipient = User::withoutGlobalScope(new TenantScope())
                                   ->find($insurance->client->user_id);
                    $recipientEmail = $recipient->email ?? null;
                }
            }
            
            if (!$recipient || !$recipientEmail) {
                return $this->sendError('No valid recipient email found');
            }
            
            // Send email
            $recipient->notify(new InsuranceExpiryReminder($insurance));
            
            // Record the reminder if not a test email
            if (!$testEmail && $days) {
                $insurance->recordReminderSent($days);
            }
            
            // Log the email
            Log::info('Insurance reminder email sent', [
                'insurance_id' => $insurance->id,
                'insurance_name' => $insurance->name,
                'recipient_email' => $recipientEmail,
                'days_until_expiry' => $days,
                'test_email' => $testEmail ? true : false,
                'sent_by' => Auth::user()->email,
                'sent_at' => now()
            ]);
            
            $message = $testEmail ? 
                "Test reminder email sent to {$recipientEmail}" : 
                "Reminder email sent to {$recipientEmail}";
                
            return $this->sendSuccess($message);
            
        } catch (\Exception $e) {
            Log::error('Insurance reminder email error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->sendError('Error sending reminder email: ' . $e->getMessage());
        }
    }

    public function getReminderStatus($insuranceId): JsonResponse
    {
        try {
            $insurance = Insurance::where('id', $insuranceId)
                                 ->where('tenant_id', Auth::user()->tenant_id)
                                 ->first();
                                 
            if (!$insurance) {
                return $this->sendError('Insurance not found');
            }
            
            return $this->sendResponse([
                'reminder_status' => $insurance->reminder_status,
                'days_until_expiry' => $insurance->days_until_expiry,
                'total_reminders_sent' => $insurance->total_reminders_sent,
                'last_reminder_sent_at' => $insurance->last_reminder_sent_at
            ], 'Reminder status retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->sendError('Error getting reminder status: ' . $e->getMessage());
        }
    }

    public function getClientDetails(Request $request): JsonResponse
    {
        try {
            Log::info('=== CLIENT DETAILS REQUEST START ===');
            Log::info('Request data: ', $request->all());
            Log::info('User: ' . Auth::user()->id);
            Log::info('Tenant: ' . getLogInUser()->tenant_id);

            $clientId = $request->get('client_id');
        
            if (!$clientId) {
                Log::error('No client_id provided');
                return response()->json([
                    'success' => false,
                    'message' => 'Client ID is required.'
                ], 400);
            }

            Log::info('Looking for client ID: ' . $clientId);

            // Get the current tenant ID
            $tenantID = getLogInUser()->tenant_id;
            Log::info('Current tenant ID: ' . $tenantID);
        
            // Get client details
            $client = Client::where('id', $clientId)
                           ->withoutGlobalScope(new TenantScope())
                           ->first();

            if (!$client) {
                Log::error('Client not found with ID: ' . $clientId);
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found.'
                ], 404);
            }

            Log::info('Client found: ', $client->toArray());

            // Verify this client belongs to current tenant
            $clientTenant = TenantWiseClient::where('client_id', $clientId)
                                       ->where('tenant_id', $tenantID)
                                       ->first();

            if (!$clientTenant) {
                Log::error('Client not found in tenant. ClientId: ' . $clientId . ', TenantId: ' . $tenantID);
                
                // Debug: Show all client-tenant relationships
                $allClientTenants = TenantWiseClient::where('client_id', $clientId)->get();
                Log::info('All tenant relationships for client: ', $allClientTenants->toArray());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found in your tenant.'
                ], 403);
            }

            Log::info('Client-tenant relationship found: ', $clientTenant->toArray());

            // Get user details for the name and contact info
            $user = User::where('id', $client->user_id)
                   ->withoutGlobalScope(new TenantScope())
                   ->first();

            if (!$user) {
                Log::error('User not found for client user_id: ' . $client->user_id);
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for this client.'
                ], 404);
            }

            Log::info('User found: ', $user->toArray());

            // Prepare client data with correct names
            $clientData = [
                'id' => $client->id,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'full_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'email' => $user->email ?? '',
                'contact' => $user->contact ?? '',
                'company_name' => $client->company_name ?? '',
                'address' => $client->address ?? '',
                'website' => $client->website ?? '',
                'postal_code' => $client->postal_code ?? '',
                'vat_no' => $client->vat_no ?? '',
            ];

            Log::info('Returning client data: ', $clientData);
            Log::info('=== CLIENT DETAILS REQUEST END ===');

            return response()->json([
                'success' => true,
                'data' => $clientData,
                'message' => 'Client details retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('=== CLIENT DETAILS ERROR ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving client details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getExpiringSoon(): JsonResponse
    {
        $expiring = Insurance::with(['client', 'category'])
                            ->where('tenant_id', Auth::user()->tenant_id)
                            ->expiringSoon(30)
                            ->get();

        return $this->sendResponse($expiring, __('Expiring insurances retrieved successfully.'));
    }
}
