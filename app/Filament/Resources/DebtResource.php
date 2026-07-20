<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtResource\Pages;
use App\Models\Debt;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;
    protected static ?string $label = 'Qarz';
    protected static ?string $navigationLabel = 'Qarzlar';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')->label('Qarz summasi')->required(),
                DateTimePicker::make('return_date')->label('Qaytarish sanasi')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Qarzdor ismi'),
                Tables\Columns\TextColumn::make('user.phone')->label('Qarzdor telefoni'),
                Tables\Columns\TextColumn::make('amount')->label('Qarz summasi'),
                Tables\Columns\TextColumn::make('return_date')->label('Qaytarish vaqti')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('sendMessage')
                    ->label('Xabar jo`natish')
                    ->accessSelectedRecords()
                    ->action(function (Model $record, Collection $selectedRecords) {
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])->defaultSort('id', 'DESC');
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
            'index' => Pages\ListDebts::route('/'),
            'edit' => Pages\EditDebt::route('/{record}/edit'),
        ];
    }
}
