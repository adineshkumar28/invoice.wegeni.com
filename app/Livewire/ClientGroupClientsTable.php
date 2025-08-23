<?php

namespace App\Http\Livewire;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ClientGroupClientsTable extends DataTableComponent
{
    public $clientGroupId;
    
    protected $model = Client::class;

    public function mount($clientGroupId)
    {
        $this->clientGroupId = $clientGroupId;
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
        $this->setQueryStringStatus(false);
        $this->setOfflineIndicatorStatus(false);
        $this->setEagerLoadAllRelationsStatus(true);
    }

    public function builder(): Builder
    {
        return Client::with(['user', 'country', 'state', 'city'])
            ->where('client_group_id', $this->clientGroupId)
            ->select('clients.*');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.client.client'), 'user.first_name')
                ->sortable()
                ->searchable()
                ->view('clients.full_name'),
            
            Column::make(__('messages.client.contact_no'), 'user.contact')
                ->sortable()
                ->searchable(),
            
            Column::make(__('messages.client.country'), 'country.name')
                ->sortable()
                ->searchable(),
            
            Column::make(__('messages.common.created_at'), 'created_at')
                ->sortable()
                ->format(fn($value) => $value->format('M d, Y')),
            
            Column::make(__('messages.common.action'))
                ->label(fn($row) => view('clients.action', compact('row'))->render()),
        ];
    }
}
