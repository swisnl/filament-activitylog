<?php

namespace Swis\Filament\Activitylog\AttributeTable\Contracts;

interface SkipsAttributes
{
    /**
     * @return string[]
     */
    public function skipAttributeTableAttributes(): array;
}
