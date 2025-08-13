<?php

namespace App\Http\Controllers;

use App\Mail\InvoicePaymentReminderMail;
use Exception;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Insurance;
use App\Models\Currency;
use Illuminate\Support\Facades\App;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Exports\AdminInvoicesExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Repositories\InvoiceRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Payment;
use App\Mail\InvoiceCreateClientMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends AppBaseController
{
    public $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepo)
    {
        $this->invoiceRepository = $invoiceRepo;
    }

    public function index(Request $request): View|Factory|Application
    {
        $this->updateInvoiceOverDueStatus();
        $statusArr = Invoice::STATUS_ARR;
        $status = $request->status;
        return view('invoices.index', compact('statusArr', 'status'));
    }

    public function create(): View|Factory|Application
    {
        $data = $this->invoiceRepository->getSyncList();
        unset($data['statusArr'][0]);
        $data['currencies'] = getCurrencies();
        
        // Add insurances to the data
        $data['insurances'] = $this->invoiceRepository->getInsuranceNameList();
        $data['associateInsurances'] = $this->invoiceRepository->getAssociateInsuranceList();
        
        return view('invoices.create')->with($data);
    }

    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        try {
            Log::info('=== INVOICE STORE START ===');
            Log::info('Raw request data:', $request->all());
            
            // Set locale to ensure consistent number formatting
            setlocale(LC_NUMERIC, 'C');
            
            DB::beginTransaction();
            
            // Get cleaned input data
            $input = $request->validated();
            
            Log::info('Validated input data:', $input);
            
            // Additional data preparation
            $input = $this->prepareInvoiceData($input);
            
            Log::info('Final prepared data:', $input);
            
            $invoice = $this->invoiceRepository->saveInvoice($input);
            
            DB::commit();
            
            Log::info('Invoice saved successfully', ['invoice_id' => $invoice->id]);
            
            if ($input['status'] != Invoice::DRAFT) {
                $this->invoiceRepository->saveNotification($input, $invoice);
                return $this->sendResponse($invoice, __('Invoice saved and sent successfully.'));
            }
            
            return $this->sendResponse($invoice, __('Invoice saved successfully.'));
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('=== INVOICE STORE ERROR ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            
            return $this->sendError('Error saving invoice: ' . $e->getMessage());
        }
    }

    private function prepareInvoiceData(array $input): array
    {
        // Convert string numbers to actual numbers
        $input['discount'] = $this->convertToNumber($input['discount'] ?? '0');
        $input['amount'] = $this->convertToNumber($input['amount'] ?? '0');
        $input['final_amount'] = $this->convertToNumber($input['final_amount'] ?? '0');
        
        // Convert array values
        if (isset($input['price']) && is_array($input['price'])) {
            foreach ($input['price'] as $key => $price) {
                $input['price'][$key] = $this->convertToNumber($price);
            }
        }
        
        if (isset($input['quantity']) && is_array($input['quantity'])) {
            foreach ($input['quantity'] as $key => $quantity) {
                $input['quantity'][$key] = $this->convertToNumber($quantity);
            }
        }
        
        // Ensure tenant_id is set
        $input['tenant_id'] = Auth::user()->tenant_id;
        
        // Handle recurring status
        $input['recurring_status'] = isset($input['recurring_status']) && $input['recurring_status'];
        
        return $input;
    }

    private function convertToNumber($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        if (is_string($value)) {
            // Remove any non-numeric characters except decimal point and minus
            $cleaned = preg_replace('/[^0-9.-]/', '', $value);
            
            // Handle multiple decimal points
            $parts = explode('.', $cleaned);
            if (count($parts) > 2) {
                $cleaned = $parts[0] . '.' . implode('', array_slice($parts, 1));
            }
            
            return is_numeric($cleaned) ? (float) $cleaned : 0.0;
        }
        
        return 0.0;
    }

    public function show(Invoice $invoice): View|Factory|Application
    {
        $invoiceData = $this->invoiceRepository->getInvoiceData($invoice);
        return view('invoices.show')->with($invoiceData);
    }

    public function edit(Invoice $invoice): View|Factory|RedirectResponse|Application
    {
        if ($invoice->status == Invoice::PAID || $invoice->status == Invoice::PARTIALLY) {
            Flash::error(__('Paid invoices cannot be edited.'));
            return redirect()->route('invoices.index');
        }
        
        $data = $this->invoiceRepository->prepareEditFormData($invoice);
        $data['currencies'] = getCurrencies();
        $data['selectedInvoiceTaxes'] = $invoice->invoiceTaxes()->pluck('tax_id')->toArray();
        $data['insurances'] = $this->invoiceRepository->getInsuranceNameList();
        $data['associateInsurances'] = $this->invoiceRepository->getAssociateInsuranceList();
        
        return view('invoices.edit')->with($data);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        try {
            setlocale(LC_NUMERIC, 'C');
            
            DB::beginTransaction();
            
            $input = $this->prepareInvoiceData($request->validated());
            $invoice = $this->invoiceRepository->updateInvoice($invoice->id, $input);
            
            DB::commit();
            
            if ($input['status'] == 1) {
                return $this->sendResponse($invoice, __('Invoice updated and sent successfully.'));
            }
            
            return $this->sendResponse($invoice, __('Invoice updated successfully.'));
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Invoice update failed', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id
            ]);
            
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        if ($invoice->tenant_id != Auth::user()->tenant_id) {
            return $this->sendError('You are not allowed to access this record.');
        }
        
        $invoice->delete();
        return $this->sendSuccess(__('Invoice deleted successfully.'));
    }

    public function getInsurance($insuranceId): JsonResponse
    {
        try {
            $insurance = Insurance::where('id', $insuranceId)
                                 ->where('tenant_id', Auth::user()->tenant_id)
                                 ->first();
            
            if (!$insurance) {
                return $this->sendError('Insurance not found.');
            }
            
            return $this->sendResponse([
                'premium_amount' => number_format($insurance->premium_amount, 2, '.', ''),
                'policy_number' => $insurance->policy_number,
                'start_date' => $insurance->start_date->format('Y-m-d'),
                'end_date' => $insurance->end_date->format('Y-m-d'),
                'name' => $insurance->name
            ], __('Insurance details retrieved successfully.'));
            
        } catch (Exception $e) {
            Log::error('Error fetching insurance details', [
                'insurance_id' => $insuranceId,
                'error' => $e->getMessage()
            ]);
            
            return $this->sendError('Error retrieving insurance details.');
        }
    }

    public function convertToPdf($invoiceId)
    {
        try {
            $invoice = Invoice::whereId($invoiceId)
                             ->whereTenantId(Auth::user()->tenant_id)
                             ->firstOrFail();
            
            $invoice->load([
                'client.user',
                'invoiceTemplate',
                'invoiceItems.product',
                'invoiceItems.insurance',
                'invoiceItems.invoiceItemTax',
                'invoiceTaxes',
            ]);
            
            $invoiceData = $this->invoiceRepository->getPdfData($invoice);
            $invoiceTemplate = $this->invoiceRepository->getDefaultTemplate($invoice);
            
            Log::info('PDF Generation', [
                'invoice_id' => $invoiceId,
                'template' => $invoiceTemplate,
                'view_path' => "invoices.invoice_template_pdf.{$invoiceTemplate}"
            ]);
            
            // Check if template view exists
            $viewPath = "invoices.invoice_template_pdf.{$invoiceTemplate}";
            if (!view()->exists($viewPath)) {
                Log::warning("Template view not found: {$viewPath}, using default");
                $invoiceTemplate = 'defaulttemplate';
                $viewPath = "invoices.invoice_template_pdf.{$invoiceTemplate}";
            }
            
            $pdf = PDF::loadView($viewPath, $invoiceData);
            
            return $pdf->stream("invoice-{$invoice->invoice_id}.pdf");
            
        } catch (Exception $e) {
            Log::error('PDF generation failed', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Flash::error('Error generating PDF: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function updateInvoiceStatus(Invoice $invoice, $status): mixed
    {
        $this->invoiceRepository->draftStatusUpdate($invoice);
        return $this->sendSuccess(__('Invoice sent successfully.'));
    }

    public function updateInvoiceOverDueStatus()
    {
        $invoice = Invoice::whereStatus(Invoice::UNPAID)->get();
        $currentDate = Carbon::today()->format('Y-m-d');
        
        foreach ($invoice as $invoices) {
            if ($invoices->due_date < $currentDate) {
                $invoices->update([
                    'status' => Invoice::OVERDUE,
                ]);
            }
        }
    }

    public function getInvoiceCurrency($currencyId): JsonResponse
    {
        $currency = Currency::find($currencyId);
        
        if (!$currency) {
            return $this->sendError('Currency not found.');
        }
        
        return $this->sendResponse([
            'currency_icon' => $currency->icon,
            'currency_code' => $currency->code,
        ], __('Currency retrieved successfully.'));
    }

    public function getProduct($productId): JsonResponse
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return $this->sendError('Product not found.');
        }
        
        return $this->sendResponse([
            'unit_price' => number_format($product->unit_price, 2, '.', ''),
            'name' => $product->name
        ], __('Product details retrieved successfully.'));
    }

    public function exportInvoicesExcel(): BinaryFileResponse
    {
        return Excel::download(new AdminInvoicesExport, 'invoices-' . time() . '.xlsx');
    }

        public function updateRecurringStatus(Invoice $invoice)
    {
        if ($invoice->tenant_id != Auth::user()->tenant_id) {
            return $this->sendError(__('Seems, you are not allowed to access this record.'));
        }

        $recurringCycle = empty($invoice->recurring_cycle) ? 1 : $invoice->recurring_cycle;
        $invoice->update([
            'recurring_status' => !$invoice->recurring_status,
            'recurring_cycle' => $recurringCycle,
        ]);

        return $this->sendSuccess(__('messages.flash.recurring_status_updated'));
    }
      public function sendInvoiceOnWhatsapp(Request $request): JsonResponse
    {
        $request->validate([
            'invoice_id' => 'required',
            'phone_number' => 'required',
        ]);

        $data = [];
        $input = $request->all();
        $invoice = Invoice::with(['client.user', 'payments'])->whereId($input['invoice_id'])->firstOrFail();
        $data['appName'] = getAppName();
        $data['invoice'] = $invoice;
        $data['invoicePdfLink'] = route('public-view-invoice.pdf', ['invoice' => $invoice->invoice_id]);
        $data['phoneNumber'] = '+' . $input['region_code'] . $input['phone_number'];

        return $this->sendResponse($data, 'send invoice data retrieved successfully.');
    }

    public function invoicePaymentReminder($invoiceId): mixed
    {
        $invoice = Invoice::with(['client.user', 'payments'])->whereId($invoiceId)->whereTenantId(Auth::user()->tenant_id)->firstOrFail();
        $paymentReminder = Mail::to($invoice->client->user->email)->send(new InvoicePaymentReminderMail($invoice));

        return $this->sendResponse($paymentReminder, __('messages.flash.payment_reminder_mail_send'));
    }

    public function getPublicInvoicePdf($invoiceId): Response
    {
        $invoice = Invoice::whereInvoiceId($invoiceId)->firstOrFail();
        $invoice->load('client.user', 'invoiceTemplate', 'invoiceItems.product', 'invoiceItems.invoiceItemTax');

        $invoiceData = $this->invoiceRepository->getPdfData($invoice);
        $invoiceTemplate = $this->invoiceRepository->getDefaultTemplate($invoice);
        $pdf = PDF::loadView("invoices.invoice_template_pdf.$invoiceTemplate", $invoiceData);

        return $pdf->stream('invoice.pdf');
    }

    public function showPublicInvoice($invoiceId): View|Factory|Application
    {
        $invoice = Invoice::with('client.user')->whereInvoiceId($invoiceId)->firstOrFail();
        $invoiceData = $this->invoiceRepository->getInvoiceData($invoice);
        $paymentTypes = Payment::PAYMENT_TYPE;
        $paymentModes = getPaymentMode($invoice->client->user->tenant_id);
        unset($paymentModes[Payment::CASH]);
        $tenantId = $invoice->client->user->tenant_id;
        $language = $invoice->client->user->language;
        App::setLocale($language);

        return view('invoices.public_view', compact('paymentTypes', 'paymentModes', 'tenantId'))->with($invoiceData);
    }

     public function exportInvoicesPdf()
    {
        $invoices = Invoice::with('client.user', 'payments')->orderBy('created_at', 'desc')->get();
        if ($invoices->count() == 0) {
            Flash::error(__('messages.no_records_found'));
            return redirect(route('invoices.index'));
        }
        ini_set('max_execution_time', 36000000);
        ini_set('memory_limit', '512M');
        $data['invoices'] = Invoice::with('client.user', 'payments')->orderBy('created_at', 'desc')->get();
        $invoicesPdf = PDF::loadView('invoices.export_invoices_pdf', $data);

        return $invoicesPdf->download('Invoices.pdf');
    }

}
