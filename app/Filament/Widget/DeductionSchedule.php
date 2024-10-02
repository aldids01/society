<?php

namespace App\Filament\Widget;

use App\Filament\Exports\ApplicantExporter;
use App\Models\Applicant;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class DeductionSchedule extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $model = Applicant::class;
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.widget.deduction-schedule';
    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('Export')
                ->label('Export Schedule')
                ->exporter(ApplicantExporter::class)
                ->filename(date('F, Y') . ' DEDUCTION SCHEDULE'),
            Action::make('delete')
                ->requiresConfirmation(),
        ];
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __(date('F, Y').' Deduction Schedule');
    }
    public function table(Table $table): Table
    {
        $currentMonth = date('F');
        $currentYear = date('Y');
        return $table
            ->query(
                Applicant::query()
                    ->where('status', '=', 'active')
                    ->orderBy('name', 'asc')
                    ->with([
                        'loanAmort' => function ($query) use ($currentMonth, $currentYear) {
                            $query->where('period', $currentMonth)
                                ->where('annual', $currentYear)
                                ->where('status', '=', 'pending');
                        },
                        'grainAmort' => function ($query) use ($currentMonth, $currentYear) {
                            $query->where('period', $currentMonth)
                                ->where('annual', $currentYear)
                                ->where('status', '=', 'pending');
                        },
                    ])
            )
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('saving')
                    ->searchable(),
                TextColumn::make('loanAmort.interest')
                    ->label('Loan Interest')
                    ->placeholder(0.0)
                    ->formatStateUsing(fn ($state) => $state ?? 0),
                TextColumn::make('loanAmort.principal')
                    ->label('Loan Principal')
                    ->placeholder(0.0)
                    ->formatStateUsing(fn ($state) => $state ?? 0),
                TextColumn::make('grainAmort.interest')
                    ->label('Grain Interest')
                    ->placeholder(0.0)
                    ->formatStateUsing(fn ($state) => $state ?? 0),
                TextColumn::make('grainAmort.principal')
                    ->label('Grain Principal')
                    ->placeholder(0.0)
                    ->formatStateUsing(fn ($state) => $state ?? 0),
            ]);
    }

}
