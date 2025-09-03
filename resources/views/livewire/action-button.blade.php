<div class="{{ \Illuminate\Support\Facades\Route::currentRouteName() == 'super.admin.subscribe.index' ? '' : 'width-80px' }}  text-center">
    @if(isset($value['show-route']))
        <a href="{{ $value['show-route'] }}" title="{{__('messages.show') }}"
           class="btn px-2 text-info fs-3 py-2" data-bs-toggle="tooltip">
            <i class="fas fa-eye"></i>
        </a>
    @endif

    @if(isset($value['edit-route']))
        <a href="{{ $value['edit-route'] }}" class="btn px-2 text-primary fs-3 py-2"
           title="{{__('messages.common.edit') }}"
           data-bs-toggle="tooltip">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
    @endif
     @if(isset($value['delete-route']))
        <form action="{{ $value['delete-route'] }}" method="POST" class="d-inline-block delete-form">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="btn px-2 text-danger fs-3 py-2"
                title="{{ __('messages.common.delete') }}"
                data-bs-toggle="tooltip"
                onclick="return confirm('Are you sure you want to delete this?')">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    @endif
</div>
