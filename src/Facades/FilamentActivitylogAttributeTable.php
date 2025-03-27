<?php

namespace Swis\Filament\Activitylog\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Swis\Filament\Activitylog\AttributeTable\Builder;

/**
 * @method static \Illuminate\Support\Collection<string, \Swis\Filament\Activitylog\AttributeTable\Attribute> buildAttributes(string $recordClass, array $newAttributes, array $oldAttributes = null)
 * @method static null | \Stringable | string executeValueFormatter(\Closure | \Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter $formatter, mixed $value, string $key, array $attributes, string $recordClass)
 * @method static null | \Stringable | string executeLabelProvider(\Closure | \Swis\Filament\Activitylog\AttributeTable\Contracts\LabelProvider $provider, string $key, string $recordClass)
 * @method static \Stringable | string formatValue(mixed $value, string $key, array $attributes, string $recordClass)
 * @method static string getLabel(string $recordClass, string $key)
 * @method static array<string, string> getBelongsToRelationsForModel(string $modelClass)
 * @method static array<string, string> getMorphToRelationsForModel(string $modelClass)
 * @method static \Closure[] getSortedValueFormatters()
 * @method static \Closure[] getSortedLabelProviders()
 */
class FilamentActivitylogAttributeTable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Builder::class;
    }

    public static function registerValueFormatter(Closure $callback, int $priority = 0): void
    {
        static::resolved(function (Builder $manager) use ($callback, $priority) {
            $manager->registerValueFormatter($callback, $priority);
        });
    }

    public static function registerDefaultFormatters(): void
    {
        static::resolved(function (Builder $manager) {
            $manager->registerDefaultFormatters();
        });
    }

    public static function registerLabelProvider(Closure $callback, int $priority = 0): void
    {
        static::resolved(function (Builder $manager) use ($callback) {
            $manager->registerLabelProvider($callback);
        });
    }

    public static function registerDefaultLabelProviders(): void
    {
        static::resolved(function (Builder $manager) {
            $manager->registerDefaultLabelProviders();
        });
    }
}
