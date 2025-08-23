<div class="row gx-10 mb-5">
    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('name', __('messages.client_group.group_name') . ':', ['class' => 'form-label required mb-3']) }}
            {{ Form::text('name', $clientGroup->name ?? null, ['class' => 'form-control form-control-solid', 'placeholder' => __('messages.client_group.group_name'), 'required']) }}
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('group_type', __('messages.client_group.group_type') . ':', ['class' => 'form-label mb-3']) }}
            {{ Form::select('group_type', [
                'family' => __('messages.client_group.family'),
                'business' => __('messages.client_group.business'),
                'organization' => __('messages.client_group.organization'),
                'other' => __('messages.client_group.other')
            ], $clientGroup->group_type ?? null, ['class' => 'form-select form-select-solid', 'placeholder' => __('messages.client_group.select_group_type'), 'data-control' => 'select2']) }}
        </div>
    </div>
    <div class="col-lg-12">
        <div class="mb-5">
            {{ Form::label('description', __('messages.client_group.description') . ':', ['class' => 'form-label mb-3']) }}
            {{ Form::textarea('description', $clientGroup->description ?? null, ['class' => 'form-control form-control-solid', 'placeholder' => __('messages.client_group.description'), 'rows' => '4']) }}
        </div>
    </div>
    <div class="d-flex justify-content-end mt-5">
        {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2']) }}
        <a href="{{ route('client-groups.index') }}" type="reset"
            class="btn btn-secondary btn-active-light-primary">{{ __('messages.common.cancel') }}</a>
    </div>
</div>
