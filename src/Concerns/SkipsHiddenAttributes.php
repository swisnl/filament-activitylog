<?php

namespace Swis\Filament\Activitylog\Concerns;

trait SkipsHiddenAttributes
{
    public function skipAttributeTableAttributes()
    {
        return $this->getHidden();
    }
}
