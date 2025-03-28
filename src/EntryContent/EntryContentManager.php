<?php

namespace Swis\Filament\Activitylog\EntryContent;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Spatie\Activitylog\Contracts\Activity;
use Swis\Filament\Activitylog\EntryContent\Contracts\ViewResolver;
use Swis\Filament\Activitylog\EntryContent\ViewResolver\EventViewResolver;

class EntryContentManager
{
    /**
     * @var array<int, array<array-key, \Closure|\Swis\Filament\Activitylog\EntryContent\Contracts\ViewResolver>>
     */
    protected array $viewResolvers = [];

    /**
     * @var array<array-key, \Closure|\Swis\Filament\Activitylog\EntryContent\Contracts\ViewResolver>
     */
    protected ?array $sortedViewResolvers = null;

    /**
     * Register a view resolver.
     *
     * A view resolver is a closure that accepts an activity model instance and returns a view name or null. If the
     * closure returns null, the next view resolver in the chain will be called.
     *
     * A view resolver can also be an instance of \Swis\Filament\Activitylog\EntryContent\Contracts\ViewResolver.
     *
     * The priority determines the order in which the view resolver are called. The higher the priority, the earlier the
     * view resolver is called. The default priority is 0. The default view resolver are all registered with a
     * negative priority, so they are called last. Custom view resolver should be registered with priority 0 or higher.
     */
    public function registerViewResolver(Closure | ViewResolver $resolver, int $priority = 0): void
    {
        $this->sortedViewResolvers = null;

        if (! isset($this->viewResolvers[$priority])) {
            $this->viewResolvers[$priority] = [];
        }

        $this->viewResolvers[$priority][] = $resolver;
    }

    public function registerEventViewResolver(string $event, string $view, int $priority = 0): void
    {
        $this->registerViewResolver(new EventViewResolver($event, $view), $priority);
    }

    /**
     * Get the registered value formatters in order of priority.
     *
     * @return array<array-key, \Closure|\Swis\Filament\Activitylog\EntryContent\Contracts\ViewResolver>
     */
    protected function getSortedViewResolvers(): array
    {
        if (isset($this->sortedViewResolvers)) {
            return $this->sortedViewResolvers;
        }

        $this->sortedViewResolvers = [];
        $keys = array_keys($this->viewResolvers);
        rsort($keys);

        foreach ($keys as $key) {
            $this->sortedViewResolvers = array_merge($this->sortedViewResolvers, $this->viewResolvers[$key]);
        }

        return $this->sortedViewResolvers;
    }

    /**
     * Execute a view resolver.
     */
    protected function executeViewResolver(Closure | ViewResolver $resolver, Activity & Model $activity): ?string
    {
        if ($resolver instanceof ViewResolver) {
            return $resolver->resolveActivityView($activity);
        }

        /** @var ?string $result */
        $result = app()->call($resolver, [
            'activity' => $activity,
        ]);

        return $result;
    }

    public function resolveView(?Model $activity): string
    {
        if (! $activity instanceof Activity) {
            throw new InvalidArgumentException('The activity must be an instance of ' . Activity::class);
        }

        foreach ($this->getSortedViewResolvers() as $resolver) {
            if ($view = $this->executeViewResolver($resolver, $activity)) {
                return $view;
            }
        }

        throw new Exception('No view resolver found for activity record');
    }
}
