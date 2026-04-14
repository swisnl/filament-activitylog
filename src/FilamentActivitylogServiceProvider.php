<?php

namespace Swis\Filament\Activitylog;

use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Swis\Filament\Activitylog\AttributeTable\Builder as AttributeTableBuilder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ModelRelationFinder as ModelRelationFinderContract;
use Swis\Filament\Activitylog\AttributeTable\LabelProviders;
use Swis\Filament\Activitylog\AttributeTable\ModelRelationFinder;
use Swis\Filament\Activitylog\AttributeTable\ValueFormatters;
use Swis\Filament\Activitylog\EntryContent\EntryContentManager;
use Swis\Filament\Activitylog\Livewire\Activitylog;
use Swis\Filament\Activitylog\View\AttributesTable;

class FilamentActivitylogServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-activitylog';

    public static string $viewNamespace = 'filament-activitylog';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        app()->singleton(AttributeTableBuilder::class);
        app()->singleton(EntryContentManager::class);
        app()->singleton(ModelRelationFinderContract::class, ModelRelationFinder::class);

        app()->afterResolving(AttributeTableBuilder::class, function (AttributeTableBuilder $builder) {
            // Model specific value formatters
            $builder->registerValueFormatter(ValueFormatters\ModelSpecificFormatter::make(), 256);

            // Formatter for specific interfaces and objects
            $builder->registerValueFormatter(ValueFormatters\HasValueFormatter::make(), -10);
            $builder->registerValueFormatter(ValueFormatters\HasLabelFormatter::make(), -10);
            $builder->registerValueFormatter(ValueFormatters\ModelFormatter::make(), -10);

            // Formatters for casts and relations
            $builder->registerValueFormatter(ValueFormatters\DateCastFormatter::make(), -25);
            $builder->registerValueFormatter(ValueFormatters\DateTimeCastFormatter::make(), -25);
            $builder->registerValueFormatter(ValueFormatters\BelongsToRelationFormatter::make(), -25);

            // Formatters for generic interfaces
            $builder->registerValueFormatter(ValueFormatters\HtmlableFormatter::make(), -49);
            $builder->registerValueFormatter(ValueFormatters\StringableFormatter::make(), -50);

            // Simple formatters for scalar values
            $builder->registerValueFormatter(ValueFormatters\NullFormatter::make(), -75);
            $builder->registerValueFormatter(ValueFormatters\BoolFormatter::make(), -75);
            $builder->registerValueFormatter(ValueFormatters\EmptyFormatter::make(), -75);
            $builder->registerValueFormatter(ValueFormatters\ScalarFormatter::make(), -75);
            $builder->registerValueFormatter(ValueFormatters\StringableFormatter::make(), -75);

            // Fallback formatters for objects and arrays
            $builder->registerValueFormatter(ValueFormatters\JsonFormatter::make(), -100);

            // Label providers
            $builder->registerLabelProvider(LabelProviders\ModelSpecificProvider::make(), 256);
            $builder->registerLabelProvider(LabelProviders\HeadlineProvider::make(), -100);
        });

        app()->afterResolving(EntryContentManager::class, function (EntryContentManager $manager) {
            foreach ([
                'commented',
                'created',
                'updated',
                'deleted',
            ] as $event) {
                $manager->registerEventViewResolver($event, 'filament-activitylog::entry-content.' . $event, -100);
            }
        });
    }

    public function packageBooted(): void
    {
        Livewire::component('filament-activitylog', Activitylog::class);
        Blade::component(AttributesTable::class, 'filament-activitylog::attributes-table');
    }
}
