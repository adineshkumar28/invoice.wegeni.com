<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SubscriptionPlan;
use App\Repositories\PaystackRepository;
use App\Repositories\SubscriptionRepository;
use Exception;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Unicodeveloper\Paystack\Facades\Paystack;
use Auth;

class PaystackController extends Controller
{
    /** @var PaystackRepository $paystackRepository */
    public $paystackRepo;

    /** @var SubscriptionRepository $subscriptionRepository */
    public $subscriptionRepository;

    public function __construct(PaystackRepository $paystackRepository, SubscriptionRepository $subscriptionRepository)
    {
        $this->paystackRepo = $paystackRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function configPaystackCredentials()
    {
        $paystackKey = getSettingValue('paystack_key');
        $paypalSecretKey = getSettingValue('paystack_secret');

        $publicKey = !empty($paystackKey) ? $paystackKey : config('paystack.publicKey');
        $secretKey = !empty($paypalSecretKey) ? $paypalSecretKey : config('paystack.secretKey');

        config([
            'paystack.publicKey' => $publicKey,
            'paystack.secretKey' => $secretKey,
        ]);
    }

    public function configPaystackSuperAdminCredentials()
    {
        $paystackKey = getSuperAdminCreds('paystack_key');
        $paypalSecretKey = getSuperAdminCreds('paystack_secret');

        $publicKey = !empty($paystackKey) ? $paystackKey : config('paystack.publicKey');
        $secretKey = !empty($paypalSecretKey) ? $paypalSecretKey : config('paystack.secretKey');

        config([
            'paystack.publicKey' => $publicKey,
            'paystack.secretKey' => $secretKey,
        ]);
    }

    public function redirectToGateway(Request $request)
    {
        $supportedCurrency = ['NGN', 'USD', 'GHS', 'ZAR','KES'];
        $invoiceId = $request->get('invoiceId');
        $amount = $request->get('amount');
        $note = $request->get('note');
        $planId = $request->get('planId');

        // for subscription plan payment
        if(!empty($planId)) {
            $this->configPaystackSuperAdminCredentials();
            $subscriptionsPricingPlan = SubscriptionPlan::findOrFail($planId);

            if (! in_array(getAdminSubscriptionPlanCurrencyCode(strtoupper($subscriptionsPricingPlan->currency_id)), $supportedCurrency)) {
                Flash::error(getAdminSubscriptionPlanCurrencyCode(strtoupper($subscriptionsPricingPlan->currency_id)).' is not currently supported.');

                return redirect()->back();
            }

            $subscriptionData = $this->subscriptionRepository->manageSubscription($planId);

            if (! isset($data['plan'])) { // 0 amount plan or try to switch the plan if it is in trial mode
                // returning from here if the plan is free.
                if (isset($data['status']) && $data['status'] == true) {
                    Flash::error($data['subscriptionPlan']->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'));
                    return redirect()->back();
                } else {
                    if (isset($data['status']) && $data['status'] == false) {
                        Flash::error(__('messages.flash.not_switch_to_zero_plan'));

                        return redirect()->back();
                    }
                }
            }

            $user = Auth::user();
            session()->put('planId', $planId);

            try {
                $data = [
                    'email' => $user->email, // email of recipients
                    'amount' => $subscriptionData['amountToPay'] * 100,
                    'quantity' => 1, // always 1
                    "orderID" => rand(10000, 99999), // generate a random order ID for the client
                    'currency' => getAdminSubscriptionPlanCurrencyCode(strtoupper($subscriptionData['plan']->currency_id)),
                    'reference' => Paystack::genTranxRef(),
                    'metadata' => json_encode(['subscription_id' => $subscriptionData['subscription']->id, 'plan_id' => $subscriptionData['plan']->id]), // this should be related data
                ];

                return Paystack::getAuthorizationUrl($data)->redirectNow();
            } catch (Exception $e) {
                session()->forget('planId');
                \Log::error($e->getMessage());
                Flash::error(__('messages.flash.paystack_token_expired'));

                return redirect()->back();
            }
        }else {  // for invoice payment
            $this->configPaystackCredentials();
            session(['note' => $note]);
            $invoice = Invoice::with('client.user')->find($invoiceId);
            $user = $invoice->client->user;
            $invoiceCurrencyId = $invoice->currency_id;

            $currencyCode = strtoupper(getInvoiceCurrencyCode($invoiceCurrencyId, $user->tenant_id));
            if (! in_array($currencyCode, $supportedCurrency)) {
                Flash::error($currencyCode.' is not currently supported.');

                return redirect()->back();
            }

            try {
                $data = [
                    'email' => $user->email, // email of recipients
                    'amount' => $amount * 100,
                    'quantity' => 1, // always 1
                    "orderID" => rand(10000, 99999), // generate a random order ID for the client
                    'currency' => $currencyCode,
                    'reference' => Paystack::genTranxRef(),
                    'metadata' => json_encode(['invoiceId' => $invoiceId]), // this should be related data
                ];

                return Paystack::getAuthorizationUrl($data)->redirectNow();
            } catch (Exception $e) {
                \Log::error($e->getMessage());
                Flash::error(__('messages.flash.paystack_token_expired'));

                return redirect()->back();
            }
        }
    }

    public function handleGatewayCallback()
    {
        $planId = session()->get('planId');

        if(!empty($planId)) {
            $this->configPaystackSuperAdminCredentials();
        }else {
            $this->configPaystackCredentials();
        }

        $paymentDetails = Paystack::getPaymentData();

        if(! $paymentDetails['status']) {
            Flash::error('Payment failed.');

            return redirect(route('client.invoices.index'));
        }

        session()->forget('planId');

        if(!empty($paymentDetails['data']['metadata']) && isset($paymentDetails['data']['metadata']['invoiceId']))
        {
            $invoiceId = $paymentDetails['data']['metadata']['invoiceId'];
            $transactionId = $paymentDetails['data']['reference'];
            $amount = $paymentDetails['data']['amount'] / 100;
            $metaData = json_encode($paymentDetails['data']);

            $this->paystackRepo->paymentSuccessForInvoice($invoiceId, $transactionId,$amount, $metaData);

            Flash::success('Payment successfully done.');

            if (! Auth()->check()) {
                $invoice = Invoice::find($invoiceId);
                $invoiceUniqueId = $invoice->invoice_id;
                return redirect(route('invoice-show-url', $invoiceUniqueId));
            }

            return redirect(route('client.invoices.index'));
        } else {
            $subscriptionId = $paymentDetails['data']['metadata']['subscription_id'];
            $transactionId = $paymentDetails['data']['reference'];
            $amount = $paymentDetails['data']['amount'] / 100;
            $metaData = json_encode($paymentDetails['data']);

            $subscription = $this->paystackRepo->paymentSuccessForPlan($subscriptionId, $transactionId, $amount, $metaData);

            Flash::success($subscription->subscriptionPlan->name.' '.__('messages.flash.has_subscribed'));

            return redirect(route('subscription.pricing.plans.index'));
        }
    }
}
