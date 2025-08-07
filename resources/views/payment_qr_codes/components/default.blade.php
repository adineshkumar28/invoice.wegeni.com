@php
    if($row->is_default == 1){
        $checked = 'checked';
    }else{
        $checked = '';
    }
@endphp
@if($checked == '')
    <label class="form-switch form-check-custom form-check-solid form-switch-sm justify-content-center">
        <input name="is_default" data-id="{{$row->id}}" class="form-check-input qr-status" type="checkbox"
               value="1" {{$checked}} >
        <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
    </label>
@else
<label class="form-switch form-check-custom form-check-solid form-switch-sm justify-content-center">
    <input name="is_default" data-id="{{$row->id}}" class="form-check-input qr-status" type="checkbox"
           value="1" {{$checked}} >
    <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
</label>
@endif
