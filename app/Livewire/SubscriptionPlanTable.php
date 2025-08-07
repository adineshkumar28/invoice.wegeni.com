<?php

namespace App\Livewire;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SubscriptionPlanTable extends LivewireTableComponent
{
    protected $listeners = ['resetPageTable','changeFrequency'];

    protected $model = SubscriptionPlan::class;

    protected string $tableName = 'subscription_plans';

    // for table header button

    public bool $showButtonOnHeader = true;

    public string $buttonComponent = 'subscription_plans.components.add-button';

    public $frequency = 0;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
        $this->setThAttributes(function (Column $column) {
            if (in_array($column->getTitle(), [
                __('messages.subscription_plans.active_plan'),
            ], true)) {
                return [
                    'class' => 'text-center',
                ];
            }
            if ($column->getTitle() == __('messages.common.action')) {
                return [
                    'style' => 'width:10%;text-align:center',
                ];
            }
            if ($column->getTitle() === __('messages.subscription_plans.make_default')) {
                return [
                    'class' => 'd-flex justify-content-center',
                ];
            }
            if ($column->isField('price')) {
                return [
                    'class' => 'd-flex justify-content-end',
                ];
            }

            return [];
        });
        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($columnIndex > 4) {
                return [
                    'class' => 'text-center',
                ];
            }
            if ($column->getField() === 'price') {
                return [
                    'class' => 'text-end',
                ];
            }

            return [
                'class' => 'text-left',
            ];
        });
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.subscription_plans.name'), 'name')
                ->sortable()->searchable(),
            Column::make('Currency', 'currency')
                ->sortable()->hideIf(1),
            Column::make(__('messages.subscription_plans.price'), 'price')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row, Column $column) {
                    return superAdminCurrencyAmount($row->price, false,
                        getAdminSubscriptionPlanCurrencyIcon($row->currency_id));
                }),
            Column::make(__('messages.subscription_plans.frequency'), 'frequency')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row, Column $column) {
                    return SubscriptionPlan::PLAN_TYPE[$row->frequency];
                }),
            Column::make(__('messages.subscription_plans.trail_plan'), 'trial_days')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row, Column $column) {
                    return $row->trial_days.' Days';
                }),
            Column::make(__('messages.subscription_plans.active_plan'), 'id')
                ->sortable()
                ->searchable()
                ->label(function ($row, Column $column) {
                    return '<span class="badge bg-light-info fs-7">'.$row->subscription->count().'</span>';
                })
                ->html(),
            Column::make(__('messages.subscription_plans.make_default'), 'is_default')
                ->sortable()
                ->searchable()
                ->view('subscription_plans.components.default'),
            Column::make(__('messages.common.action'), 'id')
                ->view('livewire.subscription-plan-action'),
        ];
    }

    public function builder(): Builder
    {
        $query = SubscriptionPlan::with(['subscription', 'currencies'])->select('subscription_plans.*')
            ->when($this->getAppliedFilterWithValue('frequency'), function ($query, $type) {
                return $query->where('frequency', $type);
            });

        $query->when(!empty($this->frequency), function ($q) {
            $q->where('frequency', $this->frequency);
        });

        return $query;
    }

    public function changeFrequency($frequency)
    {
        $this->frequency = $frequency;
        $this->setBuilder($this->builder());
    }

    public function resetPageTable()
    {
        $this->customResetPage('subscription_plansPage');
    }

    public function placeholder()
    {
        return view('livewire.subscription_plan_skeleton');
    }
}
