<?php

namespace Swis\Filament\Activitylog\AttributeTable\Concerns;

trait SkipsHiddenAttributes
{
    public function skipAttributeTableAttributes(): array
    {
        return $this->getHidden();
    }
}
