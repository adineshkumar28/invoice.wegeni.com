<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Models\SubscriptionPlan;
use Closure;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Symfony\Component\HttpFoundation\Response;

class CheckClientLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientCount = Client::count();
        $clientLimit = currentActiveSubscription()->subscriptionPlan->client_limit;
        $frequency = currentActiveSubscription()->subscriptionPlan->frequency;
        if (! ($clientLimit > $clientCount) && ($frequency != SubscriptionPlan::UNLIMITED)) {
            Flash::error(__('messages.flash.client_update_your_subscription'));

            return redirect()->route('clients.index');
        }

        return $next($request);
    }
}
