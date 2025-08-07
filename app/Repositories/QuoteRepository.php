<?php

namespace App\Repositories;

use Exception;
use App\Models\Tax;
use App\Models\Quote;
use App\Models\Client;
use App\Models\Product;
use App\Models\Setting;
use App\Models\QuoteItem;
use App\Models\UserSetting;
use Illuminate\Support\Arr;
use App\Models\Notification;
use App\Models\InvoiceSetting;
use App\Models\TenantWiseClient;
use Illuminate\Support\Facades\DB;
use App\Mail\QuoteCreateClientMail;
use App\Models\QuoteItemTax;
use Illuminate\Support\Facades\Mail;
use Stancl\Tenancy\Database\TenantScope;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class QuoteRepository
 */
class QuoteRepository extends BaseRepository
{
    /**
     * @var string[]
     */
    public $fieldSearchable = [];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Quote::class;
    }

    public function getProductNameList(): mixed
    {
        /** @var Product $product */
        static $product;

        if (! isset($product) && empty($product)) {
            $product = Product::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        }

        return $product;
    }

    public function getQuoteItemList(array $quote = [])
    {
        $quoteItems = [];

        if (!empty($quote[0]->id)) {
            $quoteItems = QuoteItem::when($quote, function ($q) use ($quote) {
                $q->whereQuoteId($quote[0]->id);
            })->whereNotNull('product_name')->toBase()->pluck('product_name', 'product_name')->toArray();
        }

        return $quoteItems;
    }

    public function getSyncList(array $quote = []): array
    {
        $data['products'] = $this->getProductNameList();
        if (! empty($quote)) {
            $data['productItem'] = $this->getQuoteItemList($quote);
            $data['products'] = $data['products'] + $data['productItem'];
        }
        $data['associateProducts'] = $this->getAssociateProductList($quote);
        $clientWiseTenantIds = TenantWiseClient::whereTenantId(getLogInUser()->tenant_id)->toBase()->pluck('user_id')->toArray();
        $data['clients'] = \App\Models\User::whereIn(
            'id',
            $clientWiseTenantIds
        )->withoutGlobalScope(new TenantScope())->get()->pluck('full_name', 'id')->toArray();
        $data['discount_type'] = Quote::DISCOUNT_TYPE;
        $quoteStatusArr = Arr::only(Quote::STATUS_ARR, Quote::DRAFT);
        $quoteRecurringArr = Quote::RECURRING_ARR;
        $data['taxes'] = $this->getTaxNameList();
        $data['statusArr'] = $quoteStatusArr;
        $data['recurringArr'] = $quoteRecurringArr;
        $data['template'] = InvoiceSetting::toBase()->pluck('template_name', 'id')->toArray();

        return $data;
    }

    public function getTaxNameList(): mixed
    {
        /** @var Tax $tax */
        static $tax;

        if (! isset($tax) && empty($tax)) {
            $tax = Tax::get();
        }

        return $tax;
    }

    public function getAssociateProductList(array $quote = []): array
    {
        $result = $this->getProductNameList();
        if (! empty($quote)) {
            $quoteItem = $this->getQuoteItemList($quote);
            $result += $quoteItem;
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

    public function saveQuote(array $input): Quote
    {
        try {
            DB::beginTransaction();
            // $input['final_amount'] = $input['amount'];
            $input['tax_id'] = json_decode($input['tax_id']);
            $input['tax'] = json_decode($input['tax']);

            if ($input['final_amount'] == 'NaN') {
                $input['final_amount'] = 0;
            }
            $quoteItemInputArray = Arr::only($input, ['product_id', 'quantity', 'price', 'tax_id', 'tax']);
            $quoteExist = Quote::where('quote_id', $input['quote_id'])->exists();
            $quoteItemInput = $this->prepareInputForQuoteItem($quoteItemInputArray);
            $total = [];
            foreach ($quoteItemInput as $value) {
                $total[] = $value['price'] * $value['quantity'];
            }
            if (! empty($input['discount'])) {
                if (array_sum($total) <= $input['discount']) {
                    throw new UnprocessableEntityHttpException('Discount amount should not be greater than sub total.');
                }
            }

            if ($quoteExist) {
                throw new UnprocessableEntityHttpException('Quote id already exist');
            }

            /** @var Quote $quote */
            $clientUser = Client::whereUserId($input['client_id'])->withoutGlobalScope(new TenantScope())->first();
            $inputQuoteTaxes = isset($input['taxes']) ? $input['taxes'] : [];

            $input['client_id'] = $clientUser->id;
            $input = Arr::only($input, [
                'client_id',
                'quote_id',
                'quote_date',
                'due_date',
                'discount_type',
                'discount',
                'final_amount',
                'tax_id',
                'tax',
                'note',
                'term',
                'template_id',
                'status',
                'tenant_id',
                'discount_before_tax'
            ]);
            $quote = Quote::create($input);
            if (count($inputQuoteTaxes) > 0) {
                $quote->qouteTaxes()->sync($inputQuoteTaxes);
            }

            $totalAmount = 0;
            $products = Product::toBase()->pluck('id')->toArray();
            foreach ($quoteItemInput as $key => $data) {
                $validator = Validator::make($data, QuoteItem::$rules, QuoteItem::$messages);

                if ($validator->fails()) {
                    throw new UnprocessableEntityHttpException($validator->errors()->first());
                }
                $data['product_name'] = is_numeric($data['product_id']);
                if (in_array($data['product_id'], $products)) {
                    $data['product_name'] = null;
                } else {
                    $data['product_name'] = $data['product_id'];
                    $data['product_id'] = null;
                }
                $data['amount'] = $data['price'] * $data['quantity'];

                $data['total'] = $data['amount'];
                $totalAmount += $data['amount'];
                $quoteItem = new QuoteItem($data);

                $quoteItem = $quote->quoteItems()->save($quoteItem);

                $quoteItemTaxIds = ($input['tax_id'][$key] != 0) ? $input['tax_id'][$key] : $input['tax_id'][$key] = [0 => 0];
                $quoteItemTaxes = ($input['tax'][$key] != 0) ? $input['tax'][$key] : $input['tax'][$key] = [0 => null];

                foreach ($quoteItemTaxes as $index => $tax) {
                    QuoteItemTax::create([
                        'quote_item_id' => $quoteItem->id,
                        'tax_id' => $quoteItemTaxIds[$index],
                        'tax' => $tax,
                    ]);
                }
            }

            $quote->amount = $totalAmount;
            $quote->save();

            DB::commit();
            if (getSettingValue('mail_notification')) {
                $user = \App\Models\User::whereId($clientUser->user_id)->withoutGlobalScope(new TenantScope())->first();
                $input['quoteData'] = $quote;
                $input['clientData'] = $user;
                Mail::to($input['clientData']['email'])->send(new QuoteCreateClientMail($input));
            }

            return $quote;
        } catch (Exception $exception) {
            throw new UnprocessableEntityHttpException($exception->getMessage());
        }
    }

    public function updateQuote($quoteId, $input)
    {
        try {
            DB::beginTransaction();
            $input['tax_id'] = json_decode($input['tax_id']);
            $input['tax'] = json_decode($input['tax']);
            if ($input['discount_type'] == 0) {
                $input['discount'] = 0;
            }
            // $input['final_amount'] = $input['amount'];
            $quoteItemInputArr = Arr::only($input, ['product_id', 'quantity', 'price', 'tax', 'tax_id', 'id']);
            $quoteItemInput = $this->prepareInputForQuoteItem($quoteItemInputArr);
            $total = [];
            foreach ($quoteItemInput as $key => $value) {
                $total[] = $value['price'] * $value['quantity'];
            }
            if (! empty($input['discount'])) {
                if (array_sum($total) <= $input['discount']) {
                    throw new UnprocessableEntityHttpException('Discount amount should not be greater than sub total.');
                }
            }

            $quoteInvoiceTaxes = isset($input['taxes']) ? $input['taxes'] : [];
            /** @var Quote $quote */
            $input['client_id'] = Client::whereUserId($input['client_id'])->withoutGlobalScope(new TenantScope())->first()->id;
            $quote = $this->update(Arr::only(
                $input,
                [
                    'client_id',
                    'quote_date',
                    'due_date',
                    'discount_type',
                    'discount',
                    'final_amount',
                    'note',
                    'term',
                    'tax',
                    'tax_id',
                    'template_id',
                    'price',
                    'status',
                    'discount_before_tax'
                ]
            ), $quoteId);
            $quote->qouteTaxes()->detach();
            if (count($quoteInvoiceTaxes) > 0) {
                $quote->qouteTaxes()->attach($quoteInvoiceTaxes);
            }
            $totalAmount = 0;

            foreach ($quoteItemInput as $key => $data) {
                $validator = Validator::make($data, QuoteItem::$rules, QuoteItem::$messages);
                if ($validator->fails()) {
                    throw new UnprocessableEntityHttpException($validator->errors()->first());
                }
                $data['product_name'] = is_numeric($data['product_id']);
                if ($data['product_name'] == true) {
                    $data['product_name'] = null;
                } else {
                    $data['product_name'] = $data['product_id'];
                    $data['product_id'] = null;
                }
                $data['amount'] = $data['price'] * $data['quantity'];
                $data['total'] = $data['amount'];
                $totalAmount += $data['amount'];
                $quoteItemInput[$key] = $data;
            }

            /** @var QuoteItemRepository $quoteItemRepo */
            $quoteItemRepo = app(QuoteItemRepository::class);
            $quoteItemRepo->updateQuoteItem($quoteItemInput, $quote->id);
            $quote->amount = $totalAmount;
            $quote->save();
            DB::commit();

            return $quote;
        } catch (Exception $exception) {
            throw new UnprocessableEntityHttpException($exception->getMessage());
        }
    }

    public function getPdfData($quote): array
    {
        $data = [];
        $data['quote'] = $quote;
        $data['client'] = $quote->client;

        $quoteItems = $quote->quoteItems;
        $data['quote_template_color'] = !empty($quote->quoteTemplate) ? $quote->quoteTemplate->template_color : '';
        $data['setting'] = Setting::toBase()->pluck('value', 'key')->toArray();
        $data['userSetting'] = UserSetting::pluck('value', 'key')->toArray();
        $data['totalTax'] = [];
        foreach ($quoteItems as $keys => $item) {
            $totalTax =  $item->quoteItemTax->sum('tax');
            $data['totalTax'][] = $item['quantity'] * $item['price'] * $totalTax / 100;
        }

        return $data;
    }

    public function getDefaultTemplate($quote): mixed
    {
        $data['quote_template_name'] = !empty($quote->quoteTemplate) ? $quote->quoteTemplate->key : 'defaultTemplate';

        return $data['quote_template_name'];
    }

    public function prepareInputForQuoteItem(array $input): array
    {
        $items = [];
        foreach ($input as $key => $data) {
            foreach ($data as $index => $value) {
                $items[$index][$key] = $value;
                if (! (isset($items[$index]['price']) && $key == 'price')) {
                    continue;
                }
                $items[$index]['price'] = removeCommaFromNumbers($items[$index]['price']);
            }
        }

        return $items;
    }

    public function saveNotification(array $input, $quote = null): void
    {
        $userId = $input['client_id'];
        $input['quote_id'] = $quote->quote_id;
        $title = 'New Quote created #' . $input['quote_id'] . '.';
        if ($input['status'] != Quote::DRAFT) {
            addNotification([
                Notification::NOTIFICATION_TYPE['Quote Created'],
                $userId,
                $title,
            ]);
        }
    }

    public function updateNotification($quote, $input, array $changes = [])
    {
        $quote->load('client.user');
        $userId = $quote->client->user_id;
        $title = 'Your Quote #' . $quote->quote_id . ' was updated.';
        if ($input['status'] != Quote::DRAFT) {
            if (isset($changes['status'])) {
                $title = 'Status of your Quote #' . $quote->quote_id . ' was updated.';
            }
            addNotification([
                Notification::NOTIFICATION_TYPE['Quote Updated'],
                $userId,
                $title,
            ]);
        }
    }

    public function getQuoteData($quote): array
    {
        $data = [];

        $quote = Quote::with([
            'client' => function ($query) {
                $query->select(['id', 'user_id', 'address']);
                $query->with([
                    'user' => function ($query) {
                        $query->select(['first_name', 'last_name', 'email', 'id', 'language']);
                    },
                ]);
            },
            'quoteItems' => function ($query) {
                $query->with(['product', 'quoteItemTax']);
            },
            'qouteTaxes'
        ])->whereId($quote->id)->first();

        $data['quote'] = $quote;
        $quoteItems = $quote->quoteItems;
        foreach ($quoteItems as $keys => $item) {
            $totalTax = $item->quoteItemTax->sum('tax');
            $data['totalTax'][] = $item['quantity'] * $item['price'] * $totalTax / 100;
        }

        return $data;
    }

    public function prepareEditFormData($quote): array
    {
        /** @var Quote $quote */
        $quote = Quote::with([
            'quoteItems' => function ($query) {
                $query->with(['quoteItemTax']);
            },
            'client',
        ])->whereId($quote->id)->firstOrFail();

        $data = $this->getSyncList([$quote]);
        $data['client_id'] = $quote->client->user_id;
        $data['$quote'] = $quote;
        $quoteItems = $quote->quoteItems;

        $data['selectedTaxes'] = [];
        foreach ($quoteItems as $quoteItem) {
            $quoteItemTaxes = $quoteItem->quoteItemTax;
            foreach ($quoteItemTaxes as $quoteItemTax) {
                $data['selectedTaxes'][$quoteItem->id][] = $quoteItemTax->tax_id;
            }
        }


        return $data;
    }
}
