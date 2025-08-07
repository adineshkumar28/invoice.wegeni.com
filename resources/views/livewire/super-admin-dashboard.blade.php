<div>
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                <a href="{{ route('users.index') }}" class="mb-xl-8 text-decoration-none">
                    <div
                        class="bg-primary shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                        <div class="bg-cyan-300 widget-icon rounded-10 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user display-4 card-icon text-white"></i>
                        </div>
                        <div class="text-end text-white">
                            <h2 class="fs-1-xxl fw-bolder text-white"> {{ formatTotalAmount($totalUsers) }}
                            </h2>
                            <h3 class="mb-0 fs-4 fw-light">{{ __('messages.total_users') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                <a href="{{ route('subscriptions.transactions.index') }}" class="mb-xl-8 text-decoration-none">
                    <div
                        class="bg-success shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                        <div
                            class="bg-green-300 widget-icon rounded-10 d-flex align-items-center justify-content-center">
                            <i class="fas fa-rupee-sign display-4 card-icon text-white"></i>
                        </div>
                        <div class="text-end text-white">
                            <h2 class="fs-1-xxl fw-bolder text-white">{{ formatTotalAmount($totalRevenues) }}
                            </h2>
                            <h3 class="mb-0 fs-4 fw-light">{{ __('messages.total_revenue') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                <a href="{{ route('subscription-plans.index') }}" class="mb-xl-8 text-decoration-none">
                    <div
                        class="bg-info shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                        <div
                            class="bg-blue-300 widget-icon rounded-10 d-flex align-items-center justify-content-center">
                            <i class="fas fa-toggle-on display-4 card-icon text-white"></i>
                        </div>
                        <div class="text-end text-white">
                            <h2 class="fs-1-xxl fw-bolder text-white">
                                {{ formatTotalAmount($totalUserPlans) }}</h2>
                            <h3 class="mb-0 fs-4 fw-light">{{ __('messages.total_active_user_plan') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                <a href="{{ route('super.admin.enquiry.index') }}" class="mb-xl-8 text-decoration-none">
                    <div
                        class="bg-warning shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                        <div
                            class="bg-yellow-300 widget-icon rounded-10 d-flex align-items-center justify-content-center">
                            <i class="fab fa-elementor display-4 card-icon text-white"></i>
                        </div>
                        <div class="text-end text-white">
                            <h2 class="fs-1-xxl fw-bolder text-white">{{ formatTotalAmount($totalEnquiries) }}</h2>
                            <h3 class="mb-0 fs-4 fw-light">{{ __('messages.total_enquiries') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
