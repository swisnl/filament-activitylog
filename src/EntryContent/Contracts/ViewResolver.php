<?php

namespace Swis\Filament\ActivityLog\EntryContent\Contracts;

use Illuminate\Database\Eloquent\Model;
use Spatie\ActivityLog\Contracts\Activity;

interface ViewResolver
{
    public function resolveActivityView(Activity & Model $activity): ?string;
}
