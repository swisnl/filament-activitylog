<?php

namespace Swis\Filament\Activitylog\Contracts;

interface AttributeTableLabelProvider
{
    public function getAttributeTableLabel(string $key, string $recordClass): ?string;
}
