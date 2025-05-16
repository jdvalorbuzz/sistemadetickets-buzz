<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Reports\BottlenecksAnalysisWidget;
use App\Filament\Widgets\Reports\ClosedTicketsByUserWidget;
use App\Filament\Widgets\Reports\ResolutionTimeWidget;
use App\Filament\Widgets\Reports\SatisfactionRateWidget;
use App\Filament\Widgets\Reports\TicketsOverviewChart;
use App\Filament\Widgets\Reports\TicketsVolumeChart;
use App\Filament\Widgets\Reports\TopAgentsWidget;
use Filament\Actions;
use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Reportes y Análisis';
    
    protected static ?string $title = 'Análisis de Rendimiento del Sistema de Tickets';
    
    protected static ?string $navigationGroup = 'Administración';
    
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.reports';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportPdf')
                ->label('Exportar a PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->button()
                ->url(route('reports.export.pdf'))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->check() && auth()->user()->isSuperAdmin()),
            
            Actions\Action::make('exportExcel')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-table-cells')
                ->color('primary')
                ->button()
                ->url(route('reports.export.excel'))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->check() && auth()->user()->isSuperAdmin()),
            
            Actions\Action::make('exportCsv')
                ->label('Exportar a CSV')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->button()
                ->url(route('reports.export.csv'))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->check() && auth()->user()->isSuperAdmin()),
        ];
    }
    
    public function getHeaderWidgets(): array
    {
        return [
            TicketsOverviewChart::class,
            ResolutionTimeWidget::class,
            SatisfactionRateWidget::class,
        ];
    }
    
    public function getFooterWidgets(): array
    {
        return [
            TicketsVolumeChart::class,
            TopAgentsWidget::class,
            BottlenecksAnalysisWidget::class,
            ClosedTicketsByUserWidget::class,
        ];
    }
    
    /**
     * Controla que solo super administradores y administradores puedan acceder a la página de reportes
     */
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->isStaff();
    }
}
