<?php

namespace Swis\Filament\Activitylog\EntryContent\Contracts;

use Spatie\Activitylog\Contracts\Activity;

interface ViewResolver
{
    public function resolveActivityView(Activity $activity): ?string;
}
