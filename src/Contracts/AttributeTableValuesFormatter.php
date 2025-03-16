<?php

namespace Swis\Filament\Activitylog\Contracts;

use Stringable;
use Swis\Filament\Activitylog\AttributeTableBuilder;

interface AttributeTableValuesFormatter
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function formatAttributeTableValue(AttributeTableBuilder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null;
}
