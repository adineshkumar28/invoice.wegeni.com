@extends('layouts.app')
@section('title')
    {{ __('messages.client_groups') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-end mb-5">
                    <h1>@yield('title')</h1>
                    <a class="btn btn-primary" href="{{ route('client-groups.create') }}">
                        {{ __('messages.client_group.add_client_group') }}
                    </a>
                </div>

                <div class="col-12">
                    @include('flash::message')
                </div>
                <div class="card">
                    <div class="card-body">
                        @livewire('client-group-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    $(document).on('click', '.client-group-delete-btn', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let url = $(this).data('url');

    if (confirm("Are you sure you want to delete this client group?")) {
        $.ajax({
            url: url,
            type: "DELETE",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    Livewire.dispatch('refreshDatatable'); // ✅ refresh table
                } else {
                    toastr.error("Delete failed");
                }
            },
            error: function () {
                toastr.error("Something went wrong");
            }
        });
    }
});

</script>

