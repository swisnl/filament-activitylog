<?php

namespace Swis\Filament\Activitylog;

use Closure;
use Swis\Filament\Activitylog\AttributeTable\Builder as AttributeTableBuilder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\LabelProvider;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;
use Swis\Filament\Activitylog\EntryContent\Contracts\ViewResolver;
use Swis\Filament\Activitylog\EntryContent\EntryContentManager;

class FilamentActivitylogManager
{
    public function attributeTableBuilder(): AttributeTableBuilder
    {
        return app(AttributeTableBuilder::class);
    }

    protected function withResolvedAttributeTableBuilder(Closure $callback): void
    {
        if (app()->resolved(AttributeTableBuilder::class)) {
            $callback(app(AttributeTableBuilder::class));
        } else {
            app()->afterResolving(AttributeTableBuilder::class, $callback);
        }
    }

    public function registerAttributeTableValueFormatter(Closure | ValueFormatter $formatter, int $priority = 0): void
    {
        $this->withResolvedAttributeTableBuilder(function (AttributeTableBuilder $builder) use ($formatter, $priority) {
            $builder->registerValueFormatter($formatter, $priority);
        });
    }

    public function registerAttributeTableLabelProvider(Closure | LabelProvider $provider, int $priority = 0): void
    {
        $this->withResolvedAttributeTableBuilder(function (AttributeTableBuilder $builder) use ($provider, $priority) {
            $builder->registerLabelProvider($provider, $priority);
        });
    }

    public function entryContentManager(): EntryContentManager
    {
        return app(EntryContentManager::class);
    }

    protected function withResolvedEntryContentManager(Closure $callback): void
    {
        if (app()->resolved(EntryContentManager::class)) {
            $callback(app(EntryContentManager::class));
        } else {
            app()->afterResolving(EntryContentManager::class, $callback);
        }
    }

    public function registerEntryContentViewResolver(Closure | ViewResolver $resolver, int $priority = 0): void
    {
        $this->withResolvedEntryContentManager(function (EntryContentManager $manager) use ($resolver, $priority) {
            $manager->registerViewResolver($resolver, $priority);
        });
    }

    public function registerEntryContentEventViewResolver(string $event, string $view, int $priority = 0): void
    {
        $this->withResolvedEntryContentManager(function (EntryContentManager $manager) use ($event, $view, $priority) {
            $manager->registerEventViewResolver($event, $view, $priority);
        });
    }
}
