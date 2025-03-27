<?php

namespace Swis\Filament\Activitylog;

use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Swis\Filament\Activitylog\AttributeTable\Builder as AttributeTableBuilder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ModelRelationFinder as ModelRelationFinderContract;
use Swis\Filament\Activitylog\AttributeTable\ModelRelationFinder;
use Swis\Filament\Activitylog\Facades\FilamentActivitylogAttributeTable;
use Swis\Filament\Activitylog\Livewire\Activitylog;
use Swis\Filament\Activitylog\Tables\EntryContent;
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
        app()->singleton(ModelRelationFinderContract::class, ModelRelationFinder::class);
    }

    public function packageBooted(): void
    {
        Livewire::component('filament-activitylog', Activitylog::class);
        Blade::component(AttributesTable::class, 'filament-activitylog-attributes-table');

        foreach ([
            'commented',
            'created',
            'updated',
            'deleted',
        ] as $event) {
            EntryContent::mapEventToView($event, 'filament-activitylog::entry-content.' . $event);
        }

        FilamentActivitylogAttributeTable::registerDefaultFormatters();
        FilamentActivitylogAttributeTable::registerDefaultLabelProviders();
    }
}
