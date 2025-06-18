<?php

namespace Swis\Filament\ActivityLog\EntryContent\ViewResolver;

use Illuminate\Database\Eloquent\Model;
use Spatie\ActivityLog\Contracts\Activity;
use Swis\Filament\ActivityLog\EntryContent\Contracts\ViewResolver;

class EventViewResolver implements ViewResolver
{
    protected string $event;

    protected string $view;

    public function __construct(string $event, string $view)
    {
        $this->event = $event;
        $this->view = $view;
    }

    public function resolveActivityView(Model & Activity $activity): ?string
    {
        if (isset($activity->event) && $activity->event === $this->event) {
            return $this->view;
        }

        return null;
    }
}
