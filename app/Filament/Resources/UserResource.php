<?php

namespace App\Filament\Resources;

use App\Exports\UsersExport;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $navigationLabel = 'Foydalanuchilar';
    protected static ?string $label = 'Foydalanuvchi';
    protected static ?string $navigationGroup = 'Sotuv';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ismi')
                    ->string()
                    ->nullable(),
                Forms\Components\TextInput::make('phone')
                    ->label('Raqam')
                    ->unique(ignorable: fn($record) => $record)
                    ->nullable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nomi'),
                Tables\Columns\TextColumn::make('phone')->label('Raqami')
            ])
            ->filters([
                //
            ])
            ->actions([
//                Action::make('export')
//                    ->label('Export to Excel')
//                    ->action(function () {
//                        return Excel::download(new UsersExport, 'users.xlsx');
//                    })
//                    ->icon('heroicon-o-document-download')
//,
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Foydalanuvchilar exporti')
                        ->action(function () {
                            return Excel::download(new UsersExport, 'users.xlsx');
                        })
                        ->color(Color::Green)
                        ->icon('heroicon-o-arrow-down-tray')
                ]),
            ])->paginated([10, 25]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
