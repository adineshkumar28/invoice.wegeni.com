<?php

namespace App\Livewire;

use App\Models\ClientGroup;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ClientGroupTable extends LivewireTableComponent
{
    protected $model = ClientGroup::class;

    protected string $tableName = 'client_groups';
    protected $listeners = ['refreshDatatable' => '$refresh', 'resetPageTable'];

    // for table header button
    public bool $showButtonOnHeader = true;

    public string $buttonComponent = 'client-groups.components.add-button';

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setPageName('page');
        $this->setDefaultSort('created_at', 'desc');
        $this->setQueryStringStatus(false);

        $this->setThAttributes(function (Column $column) {
            if ($column->getField() == 'id') {
                return [
                    'style' => 'width:9%',
                ];
            }

            return [];
        });
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.client_group.name'), 'name')
                ->searchable()
                ->sortable(),

            Column::make(__('messages.client_group.description'), 'description')
                ->searchable()
                ->sortable(),

            Column::make(__('messages.client_group.clients_count'), 'id')
                ->format(function ($value, $row, Column $column) {
                    return $row->clients()->count();
                }),

            Column::make(__('messages.common.action'), 'id')
                ->format(function ($value, $row, Column $column) {
                    return view('livewire.action-button')
                        ->withValue([
                            'edit-route' => route('client-groups.edit', $row->id),
                            'view-route' => route('client-groups.show', $row->id),
                            'delete-route' => route('client-groups.destroy', $row->id), // ✅ Delete route
                            'data-id' => $row->id,
                            'data-delete-id' => 'client-group-delete-btn',
                        ]);
                }),
        ];
    }

    public function builder(): Builder
    {
        return ClientGroup::with('clients');
    }

    public function resetPageTable()
    {
        $this->customResetPage('page');
    }

    public function placeholder()
    {
        return view('livewire.listing_skeleton');
    }
}
