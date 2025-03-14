<?php

namespace Swis\Filament\Activitylog\Tables\Actions;

use Closure;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

use function Filament\authorize;

class ActivitylogAction extends Action
{
    protected Closure | bool $enableComments = true;

    public static function getDefaultName(): ?string
    {
        return 'activitylog';
    }

    public function enableComments(Closure | bool $enableComments = true): self
    {
        $this->enableComments = $enableComments;

        return $this;
    }

    public function disableComments(Closure | bool $disableComments = true): self
    {
        if (is_bool($disableComments)) {
            return $this->enableComments(! $disableComments);
        }

        return $this->enableComments(fn () => ! $this->evaluate($disableComments));
    }

    public function getEnableComments(): bool
    {
        return (bool) $this->evaluate($this->enableComments);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->authorize(function ($record) {
                try {
                    return authorize('viewActivitylog', $record)->allowed();
                } catch (AuthorizationException $exception) {
                    return $exception->toResponse()->allowed();
                }
            })
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->label(__('filament-activitylog::activitylog.action.label'))
            ->slideOver()
            ->modalContent(fn (Model $record): View => view('filament-activitylog::modal-content', [
                'record' => $record,
                'enableComments' => $this->getEnableComments(),
            ]))
            ->modalHeading(__('filament-activitylog::activitylog.action.heading'))
            ->modalWidth(MaxWidth::ScreenSmall)
            ->modalSubmitAction(false)
            ->modalCancelAction(false);
    }
}
