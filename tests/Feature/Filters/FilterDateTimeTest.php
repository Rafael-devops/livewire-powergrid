<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Filters\Filter;

it('properly filter the produced_at field between two dates', function (string $component, object $params) {
    livewire($component, [
        'testFilters' => [
            Filter::datepicker('produced_at'),
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterDateTime('produced_at', ['2021-02-02 00:00:00', '2021-04-04 00:00:00']))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertDontSeeHtml('Francesinha vegana');
})->group('filters')->with('themes');

it('properly filter the created_at field between two dates using collection & array table', function (string $component, string $theme) {
    livewire($component, [
        'testFilters' => [
            Filter::datepicker('produced_at'),
        ],
    ])
        ->call($theme)
        ->set('filters', filterDateTime('created_at', ['2021-01-01 00:00:00', '2021-04-04 00:00:00']))
        ->assertSeeText('Name 1')
        ->assertSeeText('Name 2')
        ->assertSeeText('Name 3')
        ->assertSeeText('Name 4')
        ->assertDontSeeHtml('Name 5');
})->group('filters')->with('themes with collection table', 'themes with array table');

it('properly filter the produced_at field between another two dates', function (string $component, object $params) {
    livewire($component, [
        'testFilters' => [
            Filter::datepicker('produced_at'),
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterDateTime('produced_at', ['2021-11-11 00:00:00', '2021-12-31 00:00:00']))
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
})->group('filters')->with('themes');

it('properly filter the produced_at field between another two dates - custom builder', function (string $component, object $params) {
    livewire($component, [
        'testFilters' => [
            Filter::datepicker('produced_at')
                ->query(function ($builder, $field, $values) {
                    expect($field)
                        ->toBe('produced_at')
                        ->and($values)->toBe([
                            '2021-11-11 00:00:00',
                            '2021-12-31 00:00:00',
                        ])
                        ->and($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

                    return $builder->where('dishes.id', 1);
                }),
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterDateTime('produced_at', ['2021-11-11 00:00:00', '2021-12-31 00:00:00']))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
})->group('filters')->with('themes');

function filterDateTime(string $dataField, array $value): array
{
    return [
        'date_picker' => [
            $dataField => $value,
        ],
    ];
}
