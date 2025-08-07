<footer class="border-top w-100 pt-4 mt-7">
    <div class="d-flex justify-content-between">
        @role('super_admin')
        <p class="fs-6 text-gray-600">{{ __('messages.all_rights_reserved') }} <span> &copy; {{ \Carbon\Carbon::now()->year }}</span> <a href="#" class="text-decoration-none">{{ $settingValue['app_name']['value'] }}</a></p>
        @endrole
        @role('admin|client')
        <p class="fs-6 text-gray-600">{{ __('messages.all_rights_reserved') }} <span> &copy; {{ \Carbon\Carbon::now()->year }}</span> <a href="#" class="text-decoration-none">{{ getAppName() }}</a></p>
        @endrole
        <div>v{{ getCurrentVersion() }}</div>
    </div>
</footer>
