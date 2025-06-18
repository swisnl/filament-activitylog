<?php

namespace Swis\Filament\ActivityLog\AttributeTable\Contracts;

use Stringable;

interface HasValue
{
    public function getAttributeTableValue(): string | Stringable;
}
