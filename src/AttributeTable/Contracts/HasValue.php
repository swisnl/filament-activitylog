<?php

namespace Swis\Filament\Activitylog\AttributeTable\Contracts;

use Stringable;

interface HasValue
{
    public function getAttributeTableValue(): string | Stringable;
}
