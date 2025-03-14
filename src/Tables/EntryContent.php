<?php

namespace Swis\Filament\Activitylog\Tables;

use Closure;
use Filament\Tables\Columns\Layout\Component;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentAttributeBag;
use Spatie\Activitylog\Models\Activity;

class EntryContent extends Component
{
    public static array $viewResolvers = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public static function prependViewResolver(Closure $resolver): void
    {
        array_unshift(static::$viewResolvers, $resolver);
    }

    public static function appendViewResolver(Closure $resolver): void
    {
        static::$viewResolvers[] = $resolver;
    }

    public static function clearViewResolvers(): void
    {
        static::$viewResolvers = [];
    }

    public static function mapEventToView(string $event, string $view, bool $prepend = false): void
    {
        $resolver = function (Activity $record) use ($event, $view) {
            if ($record->event === $event) {
                return $view;
            }

            return null;
        };

        if ($prepend) {
            static::prependViewResolver($resolver);
        } else {
            static::appendViewResolver($resolver);
        }
    }

    public static function resolveView(Activity $record): string
    {
        foreach (static::$viewResolvers as $resolver) {
            if ($view = $resolver($record)) {
                return $view;
            }
        }

        throw new \Exception('No view resolver found for activity record');
    }

    public function getView(): string
    {
        $record = $this->getRecord();

        return static::resolveView($this->getRecord());
    }

    public function render(): View
    {
        return view(
            $this->getView(),
            [
                'attributes' => new ComponentAttributeBag,
                ...$this->extractPublicMethods(),
                ...(isset($this->viewIdentifier) ? [$this->viewIdentifier => $this] : []),
                ...$this->viewData,
                'record' => $this->getRecord(),
            ],
        );
    }
}
