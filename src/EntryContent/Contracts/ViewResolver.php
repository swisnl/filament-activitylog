<?php

namespace Swis\Filament\Activitylog\EntryContent\Contracts;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;

interface ViewResolver
{
    public function resolveActivityView(Activity & Model $activity): ?string;
}
