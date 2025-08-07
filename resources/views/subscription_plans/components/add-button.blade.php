<div class="dropdown d-flex align-items-center my-3 my-sm-3 me-2" wire:ignore>
    <button class="btn btn btn-icon btn-primary text-white dropdown-toggle hide-arrow ps-2 pe-0" type="button"
        id="subscriptionPlanFilters" data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-expanded="false">
        <p class="text-center">
            <i class='fas fa-filter'></i>
        </p>
    </button>
    <div class="dropdown-menu py-0" aria-labelledby="subscriptionPlanFilters">
        <div class="text-start border-bottom py-4 px-7">
            <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options') }}</h3>
        </div>
        <div class="p-5">
            <div class="mb-5">
                <label class="form-label">{{ __('messages.subscription_plans.plan_type') }}:</label>
                {{ Form::select('frequency', ['0' => 'All'] + \App\Models\SubscriptionPlan::PLAN_TYPE, null, [
                    'class' => 'form-select',
                    'data-control' => 'select2',
                    'id' => 'frequencyId',
                ]) }}
            </div>
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-secondary"
                    id="subscriptionPlanResetFilters">{{ __('messages.common.reset') }}</button>
            </div>
        </div>
    </div>
</div>
<div class="my-3 my-sm-3">
    <a type="button" class="btn btn-primary" href="{{ route('subscription-plans.create') }}">
        {{ __('messages.subscription_plans.add_subscription_plan') }}
    </a>
</div>
