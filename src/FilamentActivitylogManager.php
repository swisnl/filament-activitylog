<?php

namespace Swis\Filament\Activitylog;

use Closure;
use Swis\Filament\Activitylog\AttributeTable\Builder as AttributeTableBuilder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\LabelProvider;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

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
}
