<?php

namespace Swis\Filament\Activitylog\AttributeTable\Contracts;

interface LabelProvider
{
    public function getAttributeTableLabel(string $key, string $recordClass): ?string;
}
