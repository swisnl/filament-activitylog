<?php

namespace Swis\Filament\ActivityLog\EntryContent;

use Filament\Tables\Columns\Layout\Component;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentAttributeBag;

class EntryContent extends Component
{
    protected EntryContentManager $manager;

    public function __construct(EntryContentManager $manager)
    {
        $this->manager = $manager;
    }

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function getView(): string
    {
        $record = $this->getRecord();

        return $this->manager->resolveView($record);
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
