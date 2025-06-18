<?php

namespace Swis\Filament\ActivityLog\AttributeTable\Contracts;

interface SkipsAttributes
{
    /**
     * @return string[]
     */
    public function skipAttributeTableAttributes(): array;
}
