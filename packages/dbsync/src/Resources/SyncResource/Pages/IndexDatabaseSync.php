<?php

namespace Aldids\FilamentDbSync\Resources\SyncResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Aldids\FilamentDbSync\Http\Controllers\SyncController;
use Aldids\FilamentDbSync\Resources\SyncResource;

class IndexDatabaseSync extends ListRecords
{
    protected static string $resource = SyncResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pushToServer')
                ->label('Push To Server')
                ->icon('heroicon-o-arrow-up-on-square-stack')
                ->action('pushToServer'),

            Action::make('pullToServer')
                ->label('Pull From Server')
                ->outlined()
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->action('pullToServer'),
        ];
    }

    public function pullToServer(): void
    {
        try {
            app(SyncController::class)->pull();

            Notification::make()
                ->title('Database synchronized')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Database sync failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function pushToServer(): void
    {
        try {
            app(SyncController::class)->sync();

            Notification::make()
                ->title('Database synchronized')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Database sync failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
