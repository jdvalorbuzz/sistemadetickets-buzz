<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReplyResource\Pages;
use App\Filament\Resources\ReplyResource\RelationManagers;
use App\Models\Reply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ReplyResource extends Resource
{
    protected static ?string $model = Reply::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationGroup = 'Tickets Management';
    
    protected static ?int $navigationSort = 2;
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['ticket', 'user']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_id')
                    ->relationship('ticket', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                
                Forms\Components\Toggle::make('is_from_admin')
                    ->label('Is Admin Reply')
                    ->default(fn() => auth()->user()->isAdmin()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ticket.title')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Replied by')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('content')
                    ->limit(50),
                
                Tables\Columns\IconColumn::make('is_from_admin')
                    ->label('Admin Reply')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ticket')
                    ->relationship('ticket', 'title')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('is_from_admin')
                    ->label('Admin Replies'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReplies::route('/'),
            'create' => Pages\CreateReply::route('/create'),
            'edit' => Pages\EditReply::route('/{record}/edit'),
        ];
    }
}
