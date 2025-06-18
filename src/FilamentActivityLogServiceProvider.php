<?php

namespace Swis\Filament\ActivityLog;

use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Swis\Filament\ActivityLog\AttributeTable\Builder as AttributeTableBuilder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ModelRelationFinder as ModelRelationFinderContract;
use Swis\Filament\ActivityLog\AttributeTable\LabelProviders;
use Swis\Filament\ActivityLog\AttributeTable\ModelRelationFinder;
use Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;
use Swis\Filament\ActivityLog\EntryContent\EntryContentManager;
use Swis\Filament\ActivityLog\Livewire\ActivityLog;
use Swis\Filament\ActivityLog\View\AttributesTable;

class FilamentActivityLogServiceProvider extends PackageServiceProvider
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
            $builder->registerValueFormatter(app(ValueFormatters\ModelSpecificFormatter::class), 256);

            // Formatter for specific interfaces and objects
            $builder->registerValueFormatter(app(ValueFormatters\HasValueFormatter::class), -10);
            $builder->registerValueFormatter(app(ValueFormatters\HasLabelFormatter::class), -10);
            $builder->registerValueFormatter(app(ValueFormatters\ModelFormatter::class), -10);

            // Formatters for casts and relations
            $builder->registerValueFormatter(app(ValueFormatters\DateCastFormatter::class), -25);
            $builder->registerValueFormatter(app(ValueFormatters\DateTimeCastFormatter::class), -25);
            $builder->registerValueFormatter(app(ValueFormatters\BelongsToRelationFormatter::class), -25);

            // Simple formatters for scalar values
            $builder->registerValueFormatter(app(ValueFormatters\NullFormatter::class), -50);
            $builder->registerValueFormatter(app(ValueFormatters\BoolFormatter::class), -50);
            $builder->registerValueFormatter(app(ValueFormatters\EmptyFormatter::class), -50);
            $builder->registerValueFormatter(app(ValueFormatters\ScalarFormatter::class), -50);
            $builder->registerValueFormatter(app(ValueFormatters\StringableFormatter::class), -50);

            // Fallback formatters for objects and arrays
            $builder->registerValueFormatter(app(ValueFormatters\JsonFormatter::class), -100);

            // Label providers
            $builder->registerLabelProvider(app(LabelProviders\ModelSpecificProvider::class), 256);
            $builder->registerLabelProvider(app(LabelProviders\HeadlineProvider::class), -100);
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
        Livewire::component('filament-activitylog', ActivityLog::class);
        Blade::component(AttributesTable::class, 'filament-activitylog::attributes-table');
    }
}
