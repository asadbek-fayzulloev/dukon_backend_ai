<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Unit;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProductCategory;
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationLabel = 'Mahsulotlar';
    protected static ?string $label = 'Mahsulot';
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Do\'kon';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nomi')->searchable(),
                Tables\Columns\TextColumn::make('code')->label('Kod')->searchable(),
                
                Tables\Columns\TextColumn::make('unit.name')->label("O'lchov birligi"),
                Tables\Columns\ImageColumn::make('image')->label('Rasmi'),
            ])
            ->filters([
                Filter::make('is_less')
                    ->label('Soni')
                    ->form([
                        Forms\Components\Checkbox::make('is_less')
                            ->label('Kam qolgan tovarlar'),
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['is_less'],
                                fn(Builder $query): Builder => $query->whereColumn('quantity', '<', 'notify_limit')
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->paginated([10, 25]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nomi')
                    ->required()
                    ->unique(ignorable: fn($record) => $record)
                    ->string(),
                TextInput::make('code')
                    ->label('Kod')
                    ->required()
                    ->unique(ignorable: fn($record) => $record)
                    ->string(),
                Select::make('category_id')
                    ->label('Kategoriya')
                    ->options(ProductCategory::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('notify_limit')
                    ->label("Eslatma miqdori")
                    ->required()
                    ->integer()->minValue(0),
                
                
                Select::make('unit_id')
                    ->label("O'lchov birligi")
                    ->options(Unit::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label('Rasm')
                    ->nullable(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
