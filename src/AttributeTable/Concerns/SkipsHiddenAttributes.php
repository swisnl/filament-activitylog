<?php

namespace Swis\Filament\ActivityLog\AttributeTable\Concerns;

trait SkipsHiddenAttributes
{
    public function skipAttributeTableAttributes(): array
    {
        return $this->getHidden();
    }
}
