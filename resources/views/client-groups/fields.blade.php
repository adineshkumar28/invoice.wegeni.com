<div class="row gx-10 mb-5">
    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('name', __('messages.client_group.name') . ':', ['class' => 'form-label required mb-3']) }}
            {{ Form::text('name', null, ['class' => 'form-control form-control-solid', 'placeholder' => __('messages.client_group.name'), 'required']) }}
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('description', __('messages.client_group.description') . ':', ['class' => 'form-label mb-3']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control form-control-solid', 'placeholder' => __('messages.client_group.description'), 'rows' => '3']) }}
        </div>
    </div>
</div>

<div class="float-end d-flex mt-5">
    {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2']) }}
    <a href="{{ route('client-groups.index') }}" type="reset"
        class="btn btn-secondary btn-active-light-primary">{{ __('messages.common.cancel') }}</a>
</div>
