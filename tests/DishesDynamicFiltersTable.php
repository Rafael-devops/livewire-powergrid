<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    Detail,
    DynamicInput,
    Filters\Filter,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent,
    Rules\Rule};

class DishesDynamicFiltersTable extends PowerGridComponent
{
    use ActionButton;

    public bool $join = false;

    public function setUp(): array
    {
        return [
            Header::make()
                ->showSearchInput(),
        ];
    }

    public function datasource(): Builder
    {
        if ($this->join) {
            return $this->join();
        }

        return $this->query();
    }

    public function query(): Builder
    {
        return Dish::with('category');
    }

    public function join(): Builder
    {
        return Dish::query()
            ->join('categories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'categories.id');
            })
            ->select('dishes.*', 'categories.name as category_name');
    }

    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('in_stock');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Dish', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Category', 'in_stock'),
        ];
    }

    public function actions(): array
    {
        return [
            Button::make('toggleDetail', 'Toggle Detail')
                ->class('text-center')
                ->toggleDetail(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::dynamic('in_stock', 'in_stock')
                ->filterType(DynamicInput::FILTER_BOOLEAN)
                ->component('tests::dynamic-select')
                ->attributes([
                    'class'   => 'min-w-[170px]',
                    'options' => [
                        ['name' => 'Active',  'value' => true],
                        ['name' => 'Inactive', 'value' => false],
                    ],
                    'option-label' => 'name',
                    'option-value' => 'value',
                    'placeholder'  => 'Choose']),
        ];
    }

    public function bootstrap()
    {
        config(['livewire-powergrid.theme' => 'bootstrap']);
    }

    public function tailwind()
    {
        config(['livewire-powergrid.theme' => 'tailwind']);
    }
}
