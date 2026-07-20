<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin;
use App\Models\Shop;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;
    protected static ?string $navigationLabel = 'Adminlar';
    protected static ?string $label = 'Admin';
//    protected static bool $isDiscovered = false;

    protected static ?string $navigationIcon = 'heroicon-s-shield-check';
    protected static ?string $navigationGroup = 'Do\'kon';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ism')
                    ->required()
                    ->string(),
                Select::make('shop_id')
                    ->label('Do\'kon')
                    ->options(Shop::all()->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\TextInput::make('email')
                    ->label('Login')
                    ->unique(ignorable: fn($record) => $record)
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->label('Parol')
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create'),

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Lavozimi')
                    ->required()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ism'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Login'),
                Tables\Columns\TextColumn::make('shop.name')
                    ->label("Do'kon"),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label("Lavozimi")
            ])
            ->filters([
                //
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
