<?php

namespace Swis\Filament\Activitylog\Livewire;

use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Swis\Filament\Activitylog\EntryContent\EntryContent;
use Swis\Filament\Activitylog\Facades\FilamentActivitylog;

use function Filament\authorize;

class Activitylog extends Component implements HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithFormActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public Model $record;

    #[Locked]
    public bool $enableComments;

    public string $comment = '';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            MarkdownEditor::make('comment')
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
            return authorize('commentActivitylog', $this->record)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    public function table(Table $table): Table
    {
        $modelClass = ActivitylogServiceProvider::determineActivityModel();

        return $table
            ->query(
                $modelClass::query()
                    ->whereMorphedTo('subject', $this->record)
                    ->with(['subject', 'causer'])
            )
            ->columns([
                Stack::make([
                    TextColumn::make('causer')
                        ->formatStateUsing(function ($state) use ($modelClass) {
                            if ($state === null) {
                                return null;
                            }

                            return FilamentActivitylog::attributeTableBuilder()->formatValue($state, 'causer', [], $modelClass);
                        })
                        ->weight(FontWeight::SemiBold),
                    TextColumn::make('created_at')
                        ->since()
                        ->inline()
                        ->tooltip(function ($state, Table $table) {
                            if (! is_string($table->getDefaultDateTimeDisplayFormat())) {
                                return null;
                            }

                            return Carbon::parse($state)->format($table->getDefaultDateTimeDisplayFormat());
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
