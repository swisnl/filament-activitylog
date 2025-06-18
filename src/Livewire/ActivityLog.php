<?php

namespace Swis\Filament\ActivityLog\Livewire;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\ActivityLog\ActivityLogServiceProvider;
use Swis\Filament\ActivityLog\EntryContent\EntryContent;
use Swis\Filament\ActivityLog\Facades\FilamentActivityLog;

use function Filament\authorize;

class ActivityLog extends Component implements Forms\Contracts\HasForms, Tables\Contracts\HasTable
{
    use Actions\Concerns\InteractsWithActions;
    use Forms\Concerns\InteractsWithForms;
    use Pages\Concerns\InteractsWithFormActions;
    use Tables\Concerns\InteractsWithTable;

    #[Locked]
    public Model $record;

    #[Locked]
    public bool $enableComments;

    public string $comment = '';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\MarkdownEditor::make('comment')
                ->label(__('filament-activitylog::activitylog.form.fields.comment.label'))
                ->helperText(__('filament-activitylog::activitylog.form.fields.comment.helper_text')),
        ]);
    }

    public function saveComment(): void
    {
        $comment = trim($this->comment);

        if (empty($comment)) {
            return;
        }

        activity()
            ->performedOn($this->record)
            ->causedBy(Filament::auth()->user())
            ->event('commented')
            ->withProperties(['comment' => $this->comment])
            ->log('commented');

        $this->comment = '';
    }

    public function canComment(): bool
    {
        if (! $this->enableComments) {
            return false;
        }

        try {
            return authorize('commentActivityLog', $this->record)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    public function table(Table $table): Table
    {
        $modelClass = ActivityLogServiceProvider::determineActivityModel();

        return $table
            ->query(
                $modelClass::query()
                    ->whereMorphedTo('subject', $this->record)
                    ->with(['subject', 'causer'])
            )
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('causer')
                        ->formatStateUsing(function ($state) use ($modelClass) {
                            if ($state === null) {
                                return null;
                            }

                            return FilamentActivityLog::attributeTableBuilder()->formatValue($state, 'causer', [], $modelClass);
                        })
                        ->weight(FontWeight::SemiBold),
                    Tables\Columns\TextColumn::make('created_at')
                        ->since()
                        ->inline()
                        ->tooltip(function ($state) {
                            return Carbon::parse($state)->format(Table::$defaultDateTimeDisplayFormat);
                        })
                        ->color('gray')
                        ->sortable(),
                    EntryContent::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([50, 100, 250, 500, 'all']);
    }

    public function render(): View
    {
        return view('filament-activitylog::container');
    }
}
