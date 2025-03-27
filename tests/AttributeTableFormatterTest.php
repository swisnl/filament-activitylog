<?php

use Illuminate\Support\HtmlString;
use Swis\Filament\Activitylog\Facades\FilamentActivitylogAttributeTable;
use Swis\Filament\Activitylog\Tests\Models\ModelWithCastsRelations;
use Swis\Filament\Activitylog\Tests\Models\ModelWithLabel;
use Swis\Filament\Activitylog\Tests\Models\ModelWithValue;

it('formats with model specific overrides', function () {
    $value = FilamentActivitylogAttributeTable::formatValue('foo', 'property_with_model_override', [], ModelWithCastsRelations::class);

    expect($value)->toBe('model_override');
});

it('formats object with HasAttributeTableValue', function () {
    $obj = ModelWithValue::factory()->create(['name' => 'foo']);

    /** @var \Illuminate\Support\HtmlString $value */
    $value = FilamentActivitylogAttributeTable::formatValue($obj, 'property', [], ModelWithCastsRelations::class);

    expect($value->toHtml())->toBe('<strong>foo</strong>');
});

it('formats object with HasLabel', function () {
    $obj = ModelWithLabel::factory()->create();

    $value = FilamentActivitylogAttributeTable::formatValue($obj, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe($obj->name);
});

it('formats models without HasLabel or HasAttributeTableValue', function () {
    $model = ModelWithCastsRelations::factory()->create();

    $value = FilamentActivitylogAttributeTable::formatValue($model, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe('ModelWithCastsRelations: ' . $model->id);
});

it('formats dates', function () {
    $value = FilamentActivitylogAttributeTable::formatValue('2021-01-01', 'date_field', [], ModelWithCastsRelations::class);

    expect($value)->toBe('Jan 1, 2021');
});

it('formats datetimes', function () {
    $value = FilamentActivitylogAttributeTable::formatValue('2021-01-01 12:00:00', 'datetime_field', [], ModelWithCastsRelations::class);

    expect($value)->toBe('Jan 1, 2021 12:00:00');
});

it('formats BelongsTo relations', function () {
    $model = ModelWithLabel::factory()->create();

    $value = FilamentActivitylogAttributeTable::formatValue($model->id, 'model_with_label_id', [], ModelWithCastsRelations::class);

    expect($value)->toBe($model->name);
});

it('formats BelongsTo relations with custom foreign key', function () {
    $model = ModelWithLabel::factory()->create();

    $value = FilamentActivitylogAttributeTable::formatValue($model->id, 'unexpected_foreign_key', [], ModelWithCastsRelations::class);

    expect($value)->toBe($model->name);
});

it('formats MorphTo relations', function () {
    $model = ModelWithLabel::factory()->create();

    $value = FilamentActivitylogAttributeTable::formatValue($model->id, 'morphed_model_id', ['morphed_model_type' => ModelWithLabel::class], ModelWithCastsRelations::class);

    expect($value)->toBe($model->name);
});

it('formats MorphTo relations with custom keys', function () {
    $model = ModelWithLabel::factory()->create();

    $value = FilamentActivitylogAttributeTable::formatValue($model->id, 'unexpected_morph_to_id_field', ['unexpected_morph_to_type_field' => ModelWithLabel::class], ModelWithCastsRelations::class);

    expect($value)->toBe($model->name);
});

it('formats null', function () {
    $value = FilamentActivitylogAttributeTable::formatValue(null, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe(__('filament-activitylog::activitylog.attributes_table.values.null'));
});

it('formats booleans', function () {
    $value = FilamentActivitylogAttributeTable::formatValue(true, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe(__('filament-activitylog::activitylog.attributes_table.values.yes'));

    $value = FilamentActivitylogAttributeTable::formatValue(false, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe(__('filament-activitylog::activitylog.attributes_table.values.no'));
});

it('formats empty strings', function () {
    $value = FilamentActivitylogAttributeTable::formatValue('', 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe(__('filament-activitylog::activitylog.attributes_table.values.empty'));
});

it('formats integers', function () {
    $value = FilamentActivitylogAttributeTable::formatValue(123, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe('123');
});

it('formats floats', function () {
    $value = FilamentActivitylogAttributeTable::formatValue(123.45, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe('123.45');
});

it('formats strings', function () {
    $value = FilamentActivitylogAttributeTable::formatValue('foo', 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe('foo');
});

it('formats Stringable objects', function () {
    $obj = new HtmlString('<span>test string</span>');
    $value = FilamentActivitylogAttributeTable::formatValue($obj, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe($obj);
});

it('formats arrays', function () {
    $value = FilamentActivitylogAttributeTable::formatValue(['foo'], 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe('["foo"]');
});

it('formats objects', function () {
    $value = FilamentActivitylogAttributeTable::formatValue((object) ['foo' => 'bar'], 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe('{"foo":"bar"}');
});

it('returns unknown for values without formatter', function () {
    // The default formatters have no support for resources.
    $resource = fopen('php://stdin', 'r');
    $value = FilamentActivitylogAttributeTable::formatValue($resource, 'property', [], ModelWithCastsRelations::class);

    expect($value)->toBe(__('filament-activitylog::activitylog.attributes_table.values.unknown'));
});

it('gets label', function () {
    $label = FilamentActivitylogAttributeTable::getLabel('property', ModelWithCastsRelations::class);

    expect($label)->toBe('Property');
});

it('builds attributes', function () {
    $newAttributes = [
        'property' => 'foo',
    ];

    $oldAttributes = [
        'property' => 'bar',
    ];

    $attributes = FilamentActivitylogAttributeTable::buildAttributes(ModelWithCastsRelations::class, $newAttributes, $oldAttributes);

    expect($attributes)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($attributes->count())->toBe(1)
        ->and($attributes->first())->toEqual(\Swis\Filament\Activitylog\AttributeTable\Attribute::make('property', 'foo', 'Property')->withOldValue('bar'));
});

it('skips attributes', function () {
    $newAttributes = [
        'property' => 'foo',
        'property_to_skip' => 'bar',
    ];

    $oldAttributes = [
        'property' => 'bar',
        'property_to_skip' => 'baz',
    ];

    $attributes = FilamentActivitylogAttributeTable::buildAttributes(ModelWithCastsRelations::class, $newAttributes, $oldAttributes);

    expect($attributes)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($attributes->count())->toBe(1)
        ->and($attributes->first()?->getKey())->toBe('property');
});
