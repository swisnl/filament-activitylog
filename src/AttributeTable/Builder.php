<?php

namespace Swis\Filament\ActivityLog\AttributeTable;

use Closure;
use Illuminate\Support\Collection;
use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\LabelProvider;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ModelRelationFinder as ModelRelationFinderContract;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\SkipsAttributes;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

class Builder
{
    /**
     * @var array<int, array<array-key, \Closure|\Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter>>
     */
    protected array $valueFormatters = [];

    /**
     * @var array<array-key, \Closure|\Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter>|null
     */
    protected ?array $sortedValueFormatters = null;

    /**
     * @var array<int, array<array-key, \Closure|\Swis\Filament\ActivityLog\AttributeTable\Contracts\LabelProvider>>
     */
    protected array $labelProviders = [];

    /**
     * @var array<array-key, \Closure|\Swis\Filament\ActivityLog\AttributeTable\Contracts\LabelProvider>|null
     */
    protected ?array $sortedLabelProviders = null;

    protected ModelRelationFinderContract $modelRelationFinder;

    public function __construct(ModelRelationFinderContract $modelRelationFinder)
    {
        $this->modelRelationFinder = $modelRelationFinder;
    }

    /**
     * Register a value formatter.
     *
     * A value formatter is a closure that takes the following parameters:
     * - $builder: The AttributeTableBuilder instance.
     * - $value: The value to format.
     * - $key: The key of the attribute.
     * - $attributes: The attributes of the model.
     * - $recordClass: The class name of the model.
     * All parameters are optional and matched by name. The closure is called through the container so dependencies can
     * be injected by type hinting. The closure should return a string or an instance of Stringable. If the closure
     * returns null, the next formatter in the chain will be called.
     *
     * A value formatter can also be an instance of \Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter.
     *
     * The priority determines the order in which the value formatters are called. The higher the priority, the earlier
     * the value formatter is called. The default priority is 0. The default value formatters are all registered with a
     * negative priority, so they are called last, except for the model specific value formatter, which is registered
     * with a priority of 256. Custom value formatters should be registered with a priority between 0 and 100
     * (inclusive).
     */
    public function registerValueFormatter(Closure | ValueFormatter $valueFormatter, int $priority = 0): void
    {
        $this->sortedValueFormatters = null;

        if (! isset($this->valueFormatters[$priority])) {
            $this->valueFormatters[$priority] = [];
        }

        $this->valueFormatters[$priority][] = $valueFormatter;
    }

    /**
     * Get the registered value formatters in order of priority.
     *
     * @return array<array-key, \Closure|\Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter>
     */
    protected function getSortedValueFormatters(): array
    {
        if (isset($this->sortedValueFormatters)) {
            return $this->sortedValueFormatters;
        }

        $this->sortedValueFormatters = [];
        $keys = array_keys($this->valueFormatters);
        rsort($keys);

        foreach ($keys as $key) {
            $this->sortedValueFormatters = array_merge($this->sortedValueFormatters, $this->valueFormatters[$key]);
        }

        return $this->sortedValueFormatters;
    }

    /**
     * Execute a value formatter.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function executeValueFormatter(Closure | ValueFormatter $valueFormatter, mixed $value, string $key, array $attributes, string $recordClass): null | Stringable | string
    {
        if ($valueFormatter instanceof ValueFormatter) {
            return $valueFormatter->formatAttributeTableValue($this, $value, $key, $attributes, $recordClass);
        }

        /** @var null | Stringable | string $result */
        $result = app()->call($valueFormatter, [
            'builder' => $this,
            'value' => $value,
            'key' => $key,
            'attributes' => $attributes,
            'recordClass' => $recordClass,
        ]);

        return $result;
    }

    /**
     * Format a value.
     *
     * This method calls the registered value formatters in order of priority until a formatter returns a value. If no
     * formatter returns a value, the default unknown value is returned.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function formatValue(mixed $value, string $key, array $attributes, string $recordClass): Stringable | string
    {
        foreach ($this->getSortedValueFormatters() as $formatter) {
            $formattedValue = $this->executeValueFormatter($formatter, $value, $key, $attributes, $recordClass);
            if ($formattedValue !== null) {
                return $formattedValue;
            }
        }

        return __('filament-activitylog::activitylog.attributes_table.values.unknown');
    }

    /**
     * Register a label provider.
     *
     * A label provider is a closure that takes the following parameters:
     * - $key: The key of the attribute.
     * - $recordClass: The class name of the model.
     * All parameters are optional and matched by name. The closure is called through the container so dependencies can
     * be injected by type hinting. The closure should return a string or null. If the closure returns null, the next
     * label provider in the chain will be called.
     *
     * A label provider can also be an instance of \Swis\Filament\ActivityLog\AttributeTable\Contracts\LabelProvider.
     *
     * The priority determines the order in which the label providers are called. The higher the priority, the earlier
     * the label provider is called. The default priority is 0. The default label providers are all registered with a
     * negative priority, so they are called last, except for the model specific label provider, which is registered
     * with a priority of 256. Custom label providers should be registered with a priority between 0 and 100
     * (inclusive).
     */
    public function registerLabelProvider(Closure | LabelProvider $labelProvider, int $priority = 0): void
    {
        $this->sortedLabelProviders = null;

        if (! isset($this->labelProviders[$priority])) {
            $this->labelProviders[$priority] = [];
        }

        $this->labelProviders[$priority][] = $labelProvider;
    }

    /**
     * Get the registered label providers in order of priority.
     *
     * @return array<array-key, \Closure|\Swis\Filament\ActivityLog\AttributeTable\Contracts\LabelProvider>
     */
    protected function getSortedLabelProviders(): array
    {
        if (isset($this->sortedLabelProviders)) {
            return $this->sortedLabelProviders;
        }

        $this->sortedLabelProviders = [];
        $keys = array_keys($this->labelProviders);
        rsort($keys);

        foreach ($keys as $key) {
            $this->sortedLabelProviders = array_merge($this->sortedLabelProviders, $this->labelProviders[$key]);
        }

        return $this->sortedLabelProviders;
    }

    /**
     * Execute a label provider.
     */
    protected function executeLabelProvider(Closure | LabelProvider $labelProvider, string $key, string $recordClass): ?string
    {
        if ($labelProvider instanceof LabelProvider) {
            return $labelProvider->getAttributeTableLabel($key, $recordClass);
        }

        /** @var ?string $result */
        $result = app()->call($labelProvider, [
            'key' => $key,
            'recordClass' => $recordClass,
        ]);

        return $result;
    }

    /**
     * Get the label for an attribute.
     *
     * This method calls the registered label providers in order of priority until a provider returns a label. If no
     * provider returns a label, the key is returned.
     */
    public function getLabel(string $key, string $recordClass): string
    {
        foreach ($this->getSortedLabelProviders() as $labelProvider) {
            $label = $this->executeLabelProvider($labelProvider, $key, $recordClass);
            if ($label !== null) {
                return $label;
            }
        }

        return $key;
    }

    /**
     * Build a collection of attributes for both new and old attributes.
     *
     * @param  array<string, mixed>  $newAttributes
     * @param  ?array<string, mixed>  $oldAttributes
     * @return \Illuminate\Support\Collection<string, Attribute>
     */
    public function buildAttributes(string $recordClass, array $newAttributes, ?array $oldAttributes = null): Collection
    {
        $attributes = [];

        foreach ($newAttributes as $key => $value) {
            if ($oldAttributes !== null && $oldAttributes[$key] === $value) {
                continue;
            }

            if ($this->shouldSkipAttribute($key, $recordClass)) {
                continue;
            }

            $attribute = Attribute::make(
                $key,
                $this->formatValue($value, $key, $newAttributes, $recordClass),
                $this->getLabel($key, $recordClass),
            );

            if ($oldAttributes !== null) {
                $attribute->withOldValue($this->formatValue($oldAttributes[$key] ?? null, $key, $oldAttributes, $recordClass));
            }

            $attributes[$key] = $attribute;
        }

        return collect($attributes);
    }

    /**
     * Check if an attribute should be skipped.
     */
    protected function shouldSkipAttribute(string $key, string $recordClass): bool
    {
        // Skip attributes that are explicitly skipped by the model
        if (is_a($recordClass, SkipsAttributes::class, true)) {
            $instance = new $recordClass;

            $skippedAttributes = $instance->skipAttributeTableAttributes();
            if (in_array($key, $skippedAttributes)) {
                return true;
            }
        }

        // Skip the type attribute for polymorphic relations
        return $this->modelRelationFinder->isMorphTypeKey($recordClass, $key);
    }
}
