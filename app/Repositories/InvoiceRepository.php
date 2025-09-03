<?php

namespace App\Repositories;

use App\Mail\InvoiceCreateClientMail;
use App\Models\Client;
use App\Models\ClientGroup;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\InvoiceSetting;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentQrCode;
use App\Models\Product;
use App\Models\Insurance;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Tax;
use App\Models\TenantWiseClient;
use App\Models\UserSetting;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Database\TenantScope;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InvoiceRepository extends BaseRepository
{
    public $fieldSearchable = [];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Invoice::class;
    }

    public function getInsuranceNameList(): mixed
    {
        static $insurance;
        if (!isset($insurance) && empty($insurance)) {
            $insurance = Insurance::where('tenant_id', Auth::user()->tenant_id)
                                 ->orderBy('name', 'asc')
                                 ->pluck('name', 'id')
                                 ->toArray();
        }
        return $insurance;
    }

    public function getProductNameList(): mixed
    {
        static $product;
        if (!isset($product) && empty($product)) {
            $product = Product::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        }
        return $product;
    }

    public function getTaxNameList(): mixed
    {
        static $tax;
        if (!isset($tax) && empty($tax)) {
            $tax = Tax::get();
        }
        return $tax;
    }

    public function getInvoiceItemList(array $invoice = []): mixed
    {
        static $invoiceItems;
        if (!isset($invoiceItems) && empty($invoiceItems)) {
            $invoiceItems = InvoiceItem::when($invoice, function ($q) use ($invoice) {
                $q->whereInvoiceId($invoice[0]->id);
            })->whereNotNull('insurance_name')
              ->pluck('insurance_name', 'insurance_name')
              ->toArray();
        }
        return $invoiceItems;
    }

    public function getSyncList(array $invoice = []): array
    {
        $data['insurances'] = $this->getInsuranceNameList();
        $data['products'] = $this->getProductNameList();
        
        if (!empty($invoice)) {
            $data['insuranceItem'] = $this->getInvoiceItemList($invoice);
            $data['insurances'] += $data['insuranceItem'];
        }
        
        $data['associateInsurances'] = $this->getAssociateInsuranceList($invoice);
        $data['associateProducts'] = $this->getAssociateProductList($invoice);
        
        $clientWiseTenantIds = TenantWiseClient::whereTenantId(getLogInUser()->tenant_id)
                                             ->toBase()
                                             ->pluck('user_id')
                                             ->toArray();
        
        $data['clients'] = \App\Models\User::whereIn('id', $clientWiseTenantIds)
                                          ->withoutGlobalScope(new TenantScope())
                                          ->get()
                                          ->pluck('full_name', 'id')
                                          ->toArray();

        $data['clientGroups'] = ClientGroup::where('tenant_id', getLogInUser()->tenant_id)
                              ->pluck('name','id');
        
        $data['discount_type'] = Invoice::DISCOUNT_TYPE;
        $invoiceStatusArr = Invoice::STATUS_ARR;
        unset($invoiceStatusArr[Invoice::STATUS_ALL]);
        $invoiceRecurringArr = Invoice::RECURRING_ARR;
        $data['statusArr'] = $invoiceStatusArr;
        $data['recurringArr'] = $invoiceRecurringArr;
        $data['taxes'] = $this->getTaxNameList();
        $data['defaultTax'] = getDefaultTax();
        $data['associateTaxes'] = $this->getAssociateTaxList();
        $data['template'] = InvoiceSetting::toBase()->pluck('template_name', 'id')->toArray();
        $data['paymentQrCodes'] = PaymentQrCode::where('user_id', Auth::user()->id)->pluck('title', 'id') ?? null;
        $data['defaultPaymentQRCode'] = PaymentQrCode::whereIsDefault(true)->value('id') ?? null;
        
        return $data;
    }

    public function getAssociateInsuranceList(array $invoice = []): array
    {
        $result = $this->getInsuranceNameList();
        if (!empty($invoice)) {
            $invoiceItem = $this->getInvoiceItemList($invoice);
            $result += $invoiceItem;
        }
        
        $insurances = [];
        foreach ($result as $key => $item) {
            $insurances[] = [
                'key' => $key,
                'value' => $item,
            ];
        }
        return $insurances;
    }

    public function getAssociateProductList(array $invoice = []): array
    {
        $result = $this->getProductNameList();
        if (!empty($invoice)) {
            $invoiceItem = $this->getInvoiceItemList($invoice);
            $result += $invoiceItem;
        }
        
        $products = [];
        foreach ($result as $key => $item) {
            $products[] = [
                'key' => $key,
                'value' => $item,
            ];
        }
        return $products;
    }

    public function getAssociateTaxList(): array
    {
        $result = $this->getTaxNameList();
        $taxes = [];
        foreach ($result as $item) {
            $taxes[] = [
                'id' => $item->id,
                'name' => $item->name,
                'value' => $item->value,
                'is_default' => $item->is_default,
            ];
        }
        return $taxes;
    }

    public function saveInvoice(array $input): Invoice
    {
        try {
            Log::info('=== REPOSITORY SAVE START ===');
            Log::info('Input data received:', $input);
            
            // Set locale for consistent number handling
            setlocale(LC_NUMERIC, 'C');
            
            // Parse JSON fields safely
            $taxData = $this->parseJsonSafely($input['tax'] ?? '[]');
            $taxIdData = $this->parseJsonSafely($input['tax_id'] ?? '[]');
            
            Log::info('Parsed tax data:', ['tax' => $taxData, 'tax_id' => $taxIdData]);
            
            // Handle invoice ID prefix/suffix
            if (!empty(getSettingValue('invoice_no_prefix'))) {
                $input['invoice_id'] = getSettingValue('invoice_no_prefix') . '-' . $input['invoice_id'];
            }
            
            if (!empty(getSettingValue('invoice_no_suffix'))) {
                $input['invoice_id'] .= '-' . getSettingValue('invoice_no_suffix');
            }
            
            // Validate recurring cycle
            if (!empty($input['recurring_status']) && empty($input['recurring_cycle'])) {
                throw new UnprocessableEntityHttpException('Please enter the value in Recurring Cycle.');
            }
            
            // Check if invoice ID already exists
            $invoiceExist = Invoice::where('invoice_id', $input['invoice_id'])->exists();
            if ($invoiceExist) {
                throw new UnprocessableEntityHttpException('Invoice ID already exists');
            }
            
            // Prepare invoice items
            $invoiceItemInputArray = Arr::only($input, [
                'insurance_id', 'product_id', 'quantity', 'price'
            ]);
            
            $invoiceItemInput = $this->prepareInputForInvoiceItem($invoiceItemInputArray, $taxData, $taxIdData);
            
            Log::info('Prepared invoice items:', $invoiceItemInput);
            
            // Validate totals
            $total = 0;
            foreach ($invoiceItemInput as $item) {
                $quantity = (float) ($item['quantity'] ?? 1);
                $price = (float) ($item['price'] ?? 0);
                $total += $price * $quantity;
            }
            
            if (!empty($input['discount']) && $total <= $input['discount']) {
                throw new UnprocessableEntityHttpException('Discount amount should not be greater than sub total.');
            }
            
            // Get client properly
            $clientUser = null;

            if (!empty($input['client_id'])) {
                $clientUser = Client::whereUserId($input['client_id'])
                               ->withoutGlobalScope(new TenantScope())
                               ->first();
            } elseif (!empty($input['client_group_id'])) {
                // find first numeric insurance id in list
                $firstInsuranceId = null;
                if (!empty($input['insurance_id']) && is_array($input['insurance_id'])) {
                    foreach ($input['insurance_id'] as $iid) {
                        if (is_numeric($iid)) { $firstInsuranceId = (int)$iid; break; }
                    }
                }
                if ($firstInsuranceId) {
                    $ins = Insurance::where('tenant_id', Auth::user()->tenant_id)->find($firstInsuranceId);
                    if ($ins && $ins->client_id) {
                        $clientUser = Client::withoutGlobalScope(new TenantScope())->find($ins->client_id);
                    }
                }
            }

            if (!$clientUser) {
                throw new UnprocessableEntityHttpException('Client could not be determined from selection.');
            }
            
            // Prepare invoice data
            $invoiceData = [
                'invoice_id' => $input['invoice_id'],
                'invoice_date' => $input['invoice_date'],
                'due_date' => $input['due_date'],
                'discount_type' => $input['discount_type'],
                'discount' => $input['discount'],
                'amount' => $input['amount'],
                'final_amount' => $input['final_amount'],
                'note' => $input['note'] ?? null,
                'term' => $input['term'] ?? null,
                'template_id' => $input['template_id'],
                'payment_qr_code_id' => $input['payment_qr_code_id'] ?? null,
                'status' => $input['status'],
                'client_id' => $clientUser->id,
                'currency_id' => $input['currency_id'] ?? null,
                'recurring_status' => $input['recurring_status'] ?? false,
                'recurring_cycle' => $input['recurring_cycle'] ?? null,
                'tenant_id' => $input['tenant_id'],
            ];
            
            Log::info('Creating invoice with data:', $invoiceData);
            
            $invoice = Invoice::create($invoiceData);
            
            // Handle invoice taxes
            $inputInvoiceTaxes = $input['taxes'] ?? [];
            if (count($inputInvoiceTaxes) > 0) {
                $invoice->invoiceTaxes()->sync($inputInvoiceTaxes);
            }
            
            // Save invoice items
            $insurances = Insurance::where('tenant_id', Auth::user()->tenant_id)
                                  ->pluck('id')
                                  ->toArray();
            $products = Product::toBase()->pluck('id')->toArray();
            
            foreach ($invoiceItemInput as $key => $data) {
                Log::info('Processing invoice item:', ['item' => $data, 'key' => $key]);
                
                // Handle insurance items
                if (!empty($data['insurance_id'])) {
                    if (in_array($data['insurance_id'], $insurances)) {
                        $insurance = Insurance::find($data['insurance_id']);
                        $data['insurance_name'] = null;
                        $data['policy_number'] = $insurance->policy_number;
                        $data['premium_amount'] = $insurance->premium_amount;
                        $data['policy_start_date'] = $insurance->start_date;
                        $data['policy_end_date'] = $insurance->end_date;
                    } else {
                        $data['insurance_name'] = $data['insurance_id'];
                        $data['insurance_id'] = null;
                    }
                }
                // Handle product items
                elseif (!empty($data['product_id'])) {
                    if (in_array($data['product_id'], $products)) {
                        $data['product_name'] = null;
                    } else {
                        $data['product_name'] = $data['product_id'];
                        $data['product_id'] = null;
                    }
                }
                
                $data['amount'] = $data['price'] * $data['quantity'];
                $data['total'] = $data['amount'];
                
                $invoiceItem = new InvoiceItem($data);
                $invoiceItems = $invoice->invoiceItems()->save($invoiceItem);
                
                // Handle taxes for this item
                $itemTaxes = $data['taxes'] ?? [];
                $itemTaxIds = $data['tax_ids'] ?? [];
                
                if (!empty($itemTaxes)) {
                    foreach ($itemTaxes as $index => $tax) {
                        InvoiceItemTax::create([
                            'invoice_item_id' => $invoiceItems->id,
                            'tax_id' => $itemTaxIds[$index] ?? 0,
                            'tax' => $tax,
                        ]);
                    }
                }
            }
            
            Log::info('Invoice saved successfully:', ['invoice_id' => $invoice->id]);
            
            return $invoice;
            
        } catch (Exception $exception) {
            Log::error('=== REPOSITORY SAVE ERROR ===');
            Log::error('Error message: ' . $exception->getMessage());
            Log::error('Stack trace: ' . $exception->getTraceAsString());
            throw new UnprocessableEntityHttpException($exception->getMessage());
        }
    }

    public function prepareInputForInvoiceItem(array $input, array $taxData = [], array $taxIdData = []): array
    {
        $items = [];
        
        // Get the count of items
        $itemCount = count($input['insurance_id'] ?? $input['product_id'] ?? []);
        
        for ($i = 0; $i < $itemCount; $i++) {
            $item = [];
            
            // Basic item data
            if (isset($input['insurance_id'][$i])) {
                $item['insurance_id'] = $input['insurance_id'][$i];
            }
            if (isset($input['product_id'][$i])) {
                $item['product_id'] = $input['product_id'][$i];
            }
            if (isset($input['quantity'][$i])) {
                $item['quantity'] = (float) $input['quantity'][$i];
            }
            if (isset($input['price'][$i])) {
                $item['price'] = (float) $input['price'][$i];
            }
            
            // Tax data for this item
            if (isset($taxData[$i]) && is_array($taxData[$i])) {
                $item['taxes'] = $taxData[$i];
            }
            if (isset($taxIdData[$i]) && is_array($taxIdData[$i])) {
                $item['tax_ids'] = $taxIdData[$i];
            }
            
            $items[] = $item;
        }
        
        return $items;
    }
    
    private function parseJsonSafely($jsonString): array
    {
        if (empty($jsonString) || $jsonString === 'null') {
            return [];
        }
        
        if (is_array($jsonString)) {
            return $jsonString;
        }
        
        $decoded = json_decode($jsonString, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('JSON decode error', [
                'json' => $jsonString, 
                'error' => json_last_error_msg()
            ]);
            return [];
        }
        
        return $decoded ?? [];
    }

    public function getInvoiceData($invoice): array
    {
        $data = [];
        $invoice = Invoice::with([
            'client' => function ($query) {
                $query->select(['id', 'user_id', 'address', 'client_group_id'])
                      ->with([
                          'user' => function ($query) {
                              $query->select(['first_name', 'last_name', 'email', 'id', 'language']);
                          },
                          // eager-load group and its clients to avoid N+1 for count in PDF
                          'clientGroup.clients',
                      ]);
            },
            'parentInvoice',
            'payments',
            'invoiceItems' => function ($query) {
                $query->with(['product', 'insurance', 'invoiceItemTax']);
            },
            'invoiceTaxes'
        ])->withCount('childInvoices')->whereId($invoice->id)->first();
        
        $data['invoice'] = $invoice;
        $invoiceItems = $invoice->invoiceItems;
        $data['totalTax'] = [];
        
        foreach ($invoiceItems as $keys => $item) {
            $totalTax = $item->invoiceItemTax->sum('tax');
            $data['totalTax'][] = $item['quantity'] * $item['price'] * $totalTax / 100;
        }
        
        $data['dueAmount'] = 0;
        $data['paid'] = 0;
        
        if ($invoice->status != Invoice::PAID) {
            foreach ($invoice->payments as $payment) {
                if ($payment->payment_mode == Payment::MANUAL && 
                    $payment->is_approved !== Payment::APPROVED) {
                    continue;
                }
                $data['paid'] += $payment->amount;
            }
        } else {
            $data['paid'] += $invoice->final_amount;
        }
        
        $data['dueAmount'] = $invoice->final_amount - $data['paid'];
        
        return $data;
    }

    public function prepareEditFormData($invoice): array
    {
        $data = $this->getSyncList([$invoice]);
        $data['invoice'] = $invoice;
        $data['client_id'] = $invoice->client->user_id ?? null;
        
        return $data;
    }

    public function saveNotification($input, $invoice)
    {
        // Implementation for saving notifications
        if ($invoice->status != Invoice::DRAFT) {
            $input['invoiceData'] = $invoice;
            $input['clientData'] = $invoice->client->user;
            if (getSettingValue('mail_notification')) {
                Mail::to($input['clientData']['email'])->send(new InvoiceCreateClientMail($input));
            }
        }
    }

    public function draftStatusUpdate($invoice)
    {
        $invoice->update(['status' => Invoice::UNPAID]);
    }

    public function getPdfData($invoice): array
    {
        return $this->getInvoiceData($invoice);
    }

    public function getDefaultTemplate($invoice): string
    {
        // Get the template name from the invoice template relationship
        if ($invoice->invoiceTemplate && $invoice->invoiceTemplate->template_name) {
            $templateName = $invoice->invoiceTemplate->template_name;
            
            // Convert template name to kebab-case for view file naming
            $templateName = strtolower(str_replace([' ', '_'], '-', $templateName));
            
            // Check if the template view exists
            $viewPath = "invoices.invoice_template_pdf.{$templateName}";
            if (view()->exists($viewPath)) {
                return $templateName;
            }
        }
        
        // Fallback to default template
        return 'defaultTemplate';
    }

    public function updateInvoice($invoiceId, $input): Invoice
    {
        // Implement update logic (fields, items, taxes, status)
        try {
            Log::info('=== REPOSITORY UPDATE START ===', ['invoice_id' => $invoiceId]);
            setlocale(LC_NUMERIC, 'C');

            // Parse JSON fields safely
            $taxData = $this->parseJsonSafely($input['tax'] ?? '[]');
            $taxIdData = $this->parseJsonSafely($input['tax_id'] ?? '[]');

            // Prepare invoice items
            $invoiceItemInputArray = Arr::only($input, [
                'insurance_id', 'product_id', 'quantity', 'price'
            ]);
            $invoiceItemInput = $this->prepareInputForInvoiceItem($invoiceItemInputArray, $taxData, $taxIdData);

            // Find client (by submitted client_id=user_id or derive from first insurance)
            $clientUser = null;
            if (!empty($input['client_id'])) {
                $clientUser = Client::whereUserId($input['client_id'])
                    ->withoutGlobalScope(new TenantScope())
                    ->first();
            } elseif (!empty($input['insurance_id']) && is_array($input['insurance_id'])) {
                foreach ($input['insurance_id'] as $iid) {
                    if (is_numeric($iid)) {
                        $ins = Insurance::where('tenant_id', Auth::user()->tenant_id)->find((int)$iid);
                        if ($ins && $ins->client_id) {
                            $clientUser = Client::withoutGlobalScope(new TenantScope())->find($ins->client_id);
                            break;
                        }
                    }
                }
            }
            if (!$clientUser) {
                throw new UnprocessableEntityHttpException('Client could not be determined from selection.');
            }

            // Update invoice fields
            $invoice = Invoice::findOrFail($invoiceId);
            $invoice->invoice_date   = $input['invoice_date'];
            $invoice->due_date       = $input['due_date'];
            $invoice->discount_type  = $input['discount_type'];
            $invoice->discount       = $input['discount'];
            $invoice->amount         = $input['amount'];
            $invoice->final_amount   = $input['final_amount'];
            $invoice->note           = $input['note'] ?? null;
            $invoice->term           = $input['term'] ?? null;
            $invoice->template_id    = $input['template_id'];
            $invoice->payment_qr_code_id = $input['payment_qr_code_id'] ?? null;
            $invoice->status         = $input['status'];
            $invoice->client_id      = $clientUser->id;
            $invoice->currency_id    = $input['currency_id'] ?? null;
            $invoice->recurring_status = $input['recurring_status'] ?? false;
            $invoice->recurring_cycle  = $input['recurring_cycle'] ?? null;
            $invoice->save();

            // Sync invoice-level taxes
            $inputInvoiceTaxes = $input['taxes'] ?? [];
            $invoice->invoiceTaxes()->sync($inputInvoiceTaxes);

            // Rebuild items & item taxes
            $invoice->invoiceItems()->each(function ($item) {
                $item->invoiceItemTax()->delete();
            });
            $invoice->invoiceItems()->delete();

            $insurances = Insurance::where('tenant_id', Auth::user()->tenant_id)->pluck('id')->toArray();
            $products   = Product::toBase()->pluck('id')->toArray();

            foreach ($invoiceItemInput as $data) {
                if (!empty($data['insurance_id'])) {
                    if (in_array($data['insurance_id'], $insurances)) {
                        $insurance = Insurance::find($data['insurance_id']);
                        $data['insurance_name']   = null;
                        $data['policy_number']    = $insurance->policy_number;
                        $data['premium_amount']   = $insurance->premium_amount;
                        $data['policy_start_date']= $insurance->start_date;
                        $data['policy_end_date']  = $insurance->end_date;
                    } else {
                        $data['insurance_name'] = $data['insurance_id'];
                        $data['insurance_id']   = null;
                    }
                } elseif (!empty($data['product_id'])) {
                    if (in_array($data['product_id'], $products)) {
                        $data['product_name'] = null;
                    } else {
                        $data['product_name'] = $data['product_id'];
                        $data['product_id']   = null;
                    }
                }

                $data['amount'] = $data['price'] * $data['quantity'];
                $data['total']  = $data['amount'];

                $invoiceItem = new InvoiceItem($data);
                $savedItem = $invoice->invoiceItems()->save($invoiceItem);

                $itemTaxes = $data['taxes'] ?? [];
                $itemTaxIds = $data['tax_ids'] ?? [];
                if (!empty($itemTaxes)) {
                    foreach ($itemTaxes as $index => $tax) {
                        InvoiceItemTax::create([
                            'invoice_item_id' => $savedItem->id,
                            'tax_id' => $itemTaxIds[$index] ?? 0,
                            'tax'    => $tax,
                        ]);
                    }
                }
            }

            Log::info('=== REPOSITORY UPDATE DONE ===', ['invoice_id' => $invoice->id]);
            return $invoice;
        } catch (Exception $e) {
            Log::error('Invoice update failed in repository', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoiceId,
            ]);
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
