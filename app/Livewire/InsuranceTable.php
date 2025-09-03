<?php

namespace App\Livewire;

use App\Models\Insurance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use Carbon\Carbon;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

final class InsuranceTable extends PowerGridComponent
{
    // Add unique table name
    public string $tableName = 'insurance-table';

    // Add sorting configuration
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Insurance::query()
            ->with(['client', 'category'])
            ->where('tenant_id', Auth::user()->tenant_id);
    }

    public function relationSearch(): array
    {
        return [
            'client' => [
                'first_name',
                'last_name',
                'email',
            ],
            'category' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('policy_number')
            ->add('client_name', function (Insurance $model) {
                return $model->client ? $model->client->first_name . ' ' . $model->client->last_name : 'N/A';
            })
            ->add('client_name_lower', function (Insurance $model) {
                return $model->client ? strtolower($model->client->first_name . ' ' . $model->client->last_name) : 'n/a';
            })
            ->add('category_name', function (Insurance $model) {
                return $model->category ? $model->category->name : 'N/A';
            })
            ->add('premium_amount', function (Insurance $model) {
                return getCurrencyAmount($model->premium_amount, true);
            })
            ->add('premium_amount_raw', function (Insurance $model) {
                return $model->premium_amount;
            })
            ->add('start_date_formatted', function (Insurance $model) {
                return Carbon::parse($model->start_date)->format('d M Y');
            })
            ->add('end_date_formatted', function (Insurance $model) {
                return Carbon::parse($model->end_date)->format('d M Y');
            })
            ->add('status', function (Insurance $model) {
                if ($model->is_expired) {
                    return '<span class="badge badge-light-danger">Expired</span>';
                } elseif ($model->days_until_expiry <= 30) {
                    return '<span class="badge badge-light-warning">Expiring Soon</span>';
                } else {
                    return '<span class="badge badge-light-success">Active</span>';
                }
            })
            ->add('created_at_formatted', function (Insurance $model) {
                return Carbon::parse($model->created_at)->format('d M Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Policy Number', 'policy_number')
                ->sortable()
                ->searchable(),

            Column::make('Client', 'client_name', 'client_name_lower')
                ->sortable()
                ->searchable(),

            Column::make('Category', 'category_name')
                ->sortable()
                ->searchable(),

            Column::make('Premium Amount', 'premium_amount', 'premium_amount_raw')
                ->sortable(),

            Column::make('Start Date', 'start_date_formatted', 'start_date')
                ->sortable(),

            Column::make('End Date', 'end_date_formatted', 'end_date')
                ->sortable(),

            Column::make('Status', 'status'),

            Column::make('Created', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            // Add filters here if needed
        ];
    }

    public function actions(Insurance $row): array
    {
        return [
            Button::add('view')
                ->slot('<i class="fas fa-eye"></i>')
                ->class('btn btn-sm btn-icon btn-active-light-primary me-1')
                ->route('insurances.show', ['insurance' => $row->id]),

            Button::add('edit')
                ->slot('<i class="fas fa-edit"></i>')
                ->class('btn btn-sm btn-icon btn-active-light-primary me-1')
                ->route('insurances.edit', ['insurance' => $row->id]),

            Button::add('delete')
                ->slot('<i class="fas fa-trash"></i>')
                ->class('btn btn-sm btn-icon btn-active-light-danger')
                ->dispatch('deleteInsurance', ['insuranceId' => $row->id])
        ];
    }

    public function actionRules($row): array
    {
        return [
            // Hide/show actions based on conditions
        ];
    }

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'deleteInsurance' => 'deleteInsurance',
                'refreshComponent' => '$refresh'
            ]
        );
    }

    public function deleteInsurance($insuranceId)
    {
        try {
            $insurance = Insurance::where('id', $insuranceId)
                                 ->where('tenant_id', Auth::user()->tenant_id)
                                 ->first();

            if ($insurance) {
                $insurance->delete();
                $this->dispatch('showAlert', [
                    'type' => 'success',
                    'message' => 'Insurance deleted successfully!'
                ]);
                $this->fillData();
            } else {
                $this->dispatch('showAlert', [
                    'type' => 'error',
                    'message' => 'Insurance not found or access denied!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Error deleting insurance: ' . $e->getMessage()
            ]);
        }
    }
}
    