<div class="d-flex align-items-center">
    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
        <div class="symbol-label fs-3 bg-light-primary text-primary">
            {{ strtoupper(substr($row->name, 0, 2)) }}
        </div>
    </div>
    <div class="d-flex flex-column">
        <a href="{{route('client-groups.show', $row->id)}}" class="mb-1 text-decoration-none fw-bold">{{$row->name}}</a>
        <span class="text-muted">{{ ucfirst($row->group_type) }} • {{ $row->clients_count }} {{ __('messages.client_group.clients') }}</span>
    </div>
</div>
