<?php

namespace Swis\Filament\Activitylog\View;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Swis\Filament\Activitylog\Facades\FilamentActivitylog;

class AttributesTable extends Component
{
    /**
     * @param  array<string, string|int|float|bool>  $newAttributes
     * @param  array<string, string|int|float|bool>|null  $oldAttributes
     */
    public function __construct(
        protected Model $record,
        protected array $newAttributes,
        protected ?array $oldAttributes = null
    ) {}

    public function render(): View | Closure | string
    {
        $rows = FilamentActivitylog::attributeTableBuilder()->buildAttributes(
            get_class($this->record),
            $this->newAttributes,
            $this->oldAttributes
        );

        return view('filament-activitylog::attributes-table', [
            'rows' => $rows,
            'showOld' => $this->oldAttributes !== null,
        ]);
    }
}
