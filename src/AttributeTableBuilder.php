<?php

namespace Swis\Filament\Activitylog;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeInterface;
use Filament\Support\Contracts\HasLabel;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionNamedType;
use Stringable;
use Swis\Filament\Activitylog\Contracts\AttributeTableLabelProvider;
use Swis\Filament\Activitylog\Contracts\AttributeTableValuesFormatter;
use Swis\Filament\Activitylog\Contracts\HasAttributeTableValue;
use Swis\Filament\Activitylog\Contracts\SkipsAttributeTableAttributes;

class AttributeTableBuilder
{
    /**
     * @var array<int, array<array-key, \Closure|\Swis\Filament\Activitylog\Contracts\AttributeTableValuesFormatter>>
     */
    protected array $valueFormatters = [];

    /**
     * @var array<array-key, \Closure|\Swis\Filament\Activitylog\Contracts\AttributeTableValuesFormatter>|null
     */
    protected ?array $sortedValueFormatters = null;

    /**
     * @var array<int, array<array-key, \Closure|\Swis\Filament\Activitylog\Contracts\AttributeTableLabelProvider>>
     */
    protected array $labelProviders = [];

    /**
     * @var array<array-key, \Closure|\Swis\Filament\Activitylog\Contracts\AttributeTableLabelProvider>|null
     */
    protected ?array $sortedLabelProviders = null;

    /**
     * @var array<string, array<string, string>>
     */
    protected array $belongsToRelations = [];

    /**
     * @var array<string, array<string, string>>
     */
    protected array $morphToRelations = [];

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
     * A value formatter can also be an instance of AttributeTableValuesFormatter.
     *
     * The priority determines the order in which the value formatters are called. The higher the priority, the earlier
     * the value formatter is called. The default priority is 0. The default value formatters are all registered with a
     * negative priority, so they are called last, except for the model specific value formatter, which is registered
     * with a priority of 256. Custom value formatters should be registered with a priority between 0 and 100
     * (inclusive).
     */
    public function registerValueFormatter(Closure | AttributeTableValuesFormatter $valueFormatter, int $priority = 0): void
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
     * @return array<array-key, \Closure|\Swis\Filament\Activitylog\Contracts\AttributeTableValuesFormatter>
     */
    public function getSortedValueFormatters(): array
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
    public function executeValueFormatter(Closure | AttributeTableValuesFormatter $valueFormatter, mixed $value, string $key, array $attributes, string $recordClass): null | Stringable | string
    {
        if ($valueFormatter instanceof AttributeTableValuesFormatter) {
            return $valueFormatter->formatAttributeTableValue($this, $value, $key, $attributes, $recordClass);
        }

        return app()->call($valueFormatter, [
            'builder' => $this,
            'value' => $value,
            'key' => $key,
            'attributes' => $attributes,
            'recordClass' => $recordClass,
        ]);
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
     * A label provider can also be an instance of AttributeTableLabelProvider.
     *
     * The priority determines the order in which the label providers are called. The higher the priority, the earlier
     * the label provider is called. The default priority is 0. The default label providers are all registered with a
     * negative priority, so they are called last, except for the model specific label provider, which is registered
     * with a priority of 256. Custom label providers should be registered with a priority between 0 and 100
     * (inclusive).
     */
    public function registerLabelProvider(Closure | AttributeTableLabelProvider $labelProvider, int $priority = 0): void
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
     * @return array<array-key, \Closure|\Swis\Filament\Activitylog\Contracts\AttributeTableLabelProvider>
     */
    public function getSortedLabelProviders(): array
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
    public function executeLabelProvider(Closure | AttributeTableLabelProvider $labelProvider, string $key, string $recordClass): ?string
    {
        if ($labelProvider instanceof AttributeTableLabelProvider) {
            return $labelProvider->getAttributeTableLabel($key, $recordClass);
        }

        return app()->call($labelProvider, [
            'key' => $key,
            'recordClass' => $recordClass,
        ]);
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
        $instance = new $recordClass;

        // Skip attributes that are explicitly skipped by the model
        if ($instance instanceof SkipsAttributeTableAttributes) {
            $skippedAttributes = $instance->skipAttributeTableAttributes();
            if (in_array($key, $skippedAttributes)) {
                return true;
            }
        }

        // Skip the type attribute for polymorphic relations
        $morphToRelations = $this->getMorphToRelationsForModel($recordClass);
        if (array_key_exists($key, $morphToRelations)) {
            return true;
        }

        return false;
    }

    /**
     * Build the belongsTo and morphTo relations for a model.
     */
    protected function buildRelationsForModel(string $modelClass): void
    {
        $this->belongsToRelations[$modelClass] = [];
        $this->morphToRelations[$modelClass] = [];

        if (! class_exists($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            return;
        }

        $reflection = new ReflectionClass($modelClass);
        $methods = $reflection->getMethods();
        $instance = new $modelClass;

        foreach ($methods as $method) {
            if (! $method->isPublic() || $method->isStatic()) {
                continue;
            }

            $methodName = $method->getName();
            $returnType = $method->getReturnType();

            if (! $returnType instanceof ReflectionNamedType || $returnType->isBuiltin() || ! is_a($returnType->getName(), BelongsTo::class, true)) {
                continue;
            }

            $relation = $instance->$methodName();
            if (! ($relation instanceof BelongsTo)) {
                continue;
            }

            $this->belongsToRelations[$modelClass][$relation->getForeignKeyName()] = $methodName;

            if ($relation instanceof MorphTo) {
                $this->morphToRelations[$modelClass][$relation->getMorphType()] = $methodName;
            }
        }
    }

    /**
     * Get the BelongsTo relations for a model.
     *
     * The return value is an array where the keys are the foreign key names and the values are the relation method
     * names. MorphTo relations are also considered BelongsTo relations. The relations are cached for performance
     * reasons.
     *
     * @return array<string, string>
     */
    public function getBelongsToRelationsForModel(string $modelClass): array
    {
        if (array_key_exists($modelClass, $this->belongsToRelations)) {
            return $this->belongsToRelations[$modelClass];
        }

        $this->buildRelationsForModel($modelClass);

        return $this->belongsToRelations[$modelClass];
    }

    /**
     * Get the MorphTo relations for a model.
     *
     * The return value is an array where the keys are the morph type column names and the values are the relation
     * method names. The relations are cached for performance reasons.
     *
     * @return array<string, string>
     */
    public function getMorphToRelationsForModel(string $modelClass): array
    {
        if (array_key_exists($modelClass, $this->morphToRelations)) {
            return $this->morphToRelations[$modelClass];
        }

        $this->buildRelationsForModel($modelClass);

        return $this->morphToRelations[$modelClass];
    }

    /**
     * Register the default value formatters.
     */
    public function registerDefaultFormatters(): void
    {
        // Model specific value formatters
        $this->registerValueFormatter(function (AttributeTableBuilder $builder, mixed $value, string $key, array $attributes, string $recordClass) {
            if (is_a($recordClass, AttributeTableValuesFormatter::class, true)) {
                return (new $recordClass)->formatAttributeTableValue($builder, $value, $key, $attributes, $recordClass);
            }

            return null;
        }, 256);

        // Formatter for specific interfaces and objects
        $this->registerValueFormatter(function (mixed $value) {
            if ($value instanceof HasAttributeTableValue) {
                return $value->getAttributeTableValue();
            }

            return null;
        });

        $this->registerValueFormatter(function (mixed $value) {
            if ($value instanceof HasLabel) {
                return $value->getLabel();
            }

            return null;
        }, -10);

        $this->registerValueFormatter(function (mixed $value) {
            if ($value instanceof Model) {
                $class = get_class($value);
                $alias = array_search($class, Relation::$morphMap, strict: true) ?: $class;

                if ($alias === $class) {
                    $alias = class_basename($class);
                }

                return $alias . ': ' . $value->getKey();
            }
        }, -10);

        // Formatters for casts and relations
        $this->registerValueFormatter(function (mixed $value, string $key, string $recordClass) {
            $casts = (new $recordClass)->getCasts();

            if (
                array_key_exists($key, $casts) &&
                in_array($casts[$key], ['date', 'immutable_date'])
            ) {
                if ($value instanceof DateTimeInterface) {
                    return CarbonImmutable::instance($value)->format(Table::$defaultDateDisplayFormat);
                }
                if (is_scalar($value)) {
                    return CarbonImmutable::parse($value)->format(Table::$defaultDateDisplayFormat);
                }
            }

            return null;
        }, -25);

        $this->registerValueFormatter(function (mixed $value, string $key, string $recordClass) {
            $casts = (new $recordClass)->getCasts();

            if (
                array_key_exists($key, $casts) &&
                in_array($casts[$key], ['datetime', 'immutable_datetime'])
            ) {
                if ($value instanceof DateTimeInterface) {
                    return CarbonImmutable::instance($value)->format(Table::$defaultDateTimeDisplayFormat);
                }
                if (is_scalar($value)) {
                    return CarbonImmutable::parse($value)->format(Table::$defaultDateTimeDisplayFormat);
                }
            }

            return null;
        }, -25);

        $this->registerValueFormatter(function (AttributeTableBuilder $builder, mixed $value, string $key, array $attributes, string $recordClass) {
            if (empty($value) || ! is_scalar($value)) {
                return;
            }

            $relations = $builder->getBelongsToRelationsForModel($recordClass);
            if (! array_key_exists($key, $relations)) {
                return;
            }

            $relationName = $relations[$key];
            $relation = (new $recordClass)->$relationName();

            if ($relation instanceof MorphTo) {
                $type_key = $relation->getMorphType();
                if (empty($attributes[$type_key])) {
                    return;
                }

                $relatedModel = Model::getActualClassNameForMorph($attributes[$type_key]);
                if (! $relatedModel || ! is_a($relatedModel, Model::class, true)) {
                    return;
                }

                $valueModel = $relatedModel::find($value);
                if ($valueModel) {
                    return $builder->formatValue($valueModel, $key, $attributes, $recordClass);
                } else {
                    $alias = $attributes[$type_key];

                    return $alias . ': ' . (string) $value;
                }
            }

            /** @var Model $relatedModel */
            $relatedModel = $relation->getRelated();

            $valueModel = $relatedModel->newQuery()->find($value);
            if ($valueModel) {
                return $builder->formatValue($valueModel, $key, $attributes, $recordClass);
            } else {
                $class = get_class($relatedModel);
                $alias = array_search($class, Relation::$morphMap, strict: true) ?: $class;

                if ($alias === $class) {
                    $alias = class_basename($class);
                }

                return $alias . ': ' . (string) $value;
            }
        }, -25);

        // Simple formatters for scalar values
        $this->registerValueFormatter(function (mixed $value) {
            if (is_null($value)) {
                return __('filament-activitylog::activitylog.attributes_table.values.null');
            }
        }, -50);

        $this->registerValueFormatter(function (mixed $value) {
            if (is_bool($value)) {
                return $value ? __('filament-activitylog::activitylog.attributes_table.values.yes') : __('filament-activitylog::activitylog.attributes_table.values.no');
            }

            return null;
        }, -50);

        $this->registerValueFormatter(function (mixed $value) {
            if ($value === '') {
                return __('filament-activitylog::activitylog.attributes_table.values.empty');
            }

            return null;
        }, -50);

        $this->registerValueFormatter(function (mixed $value) {
            if (is_scalar($value)) {
                return (string) $value;
            }

            return null;
        }, -50);

        $this->registerValueFormatter(function (mixed $value) {
            if ($value instanceof Stringable) {
                return $value;
            }

            return null;
        }, -50);

        // Fallback formatters for objects and arrays
        $this->registerValueFormatter(function (mixed $value) {
            if (is_array($value) || is_object($value)) {
                return json_encode($value);
            }

            return null;
        }, -100);
    }

    /**
     * Register the default label providers.
     */
    public function registerDefaultLabelProviders(): void
    {
        // Model specific label provider
        $this->registerLabelProvider(function (string $key, string $recordClass) {
            if (is_a($recordClass, AttributeTableLabelProvider::class, true)) {
                return (new $recordClass)->getAttributeTableLabel($key, $recordClass);
            }

            return null;
        }, 256);

        $this->registerLabelProvider(function (string $key) {
            return Str::headline($key);
        }, -100);
    }
}
