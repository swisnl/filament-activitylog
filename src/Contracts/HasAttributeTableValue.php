<?php

namespace Swis\Filament\Activitylog\Contracts;

use Stringable;

interface HasAttributeTableValue
{
    public function getAttributeTableValue(): string | Stringable;
}
