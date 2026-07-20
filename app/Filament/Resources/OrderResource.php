<?php

namespace App\Filament\Resources;

use App\Enums\PaymentType;
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $label = 'Buyurtma';
    protected static ?int $navigationSort = -100;
    protected static ?string $navigationLabel = 'Sotuvlar';
    protected static ?string $navigationIcon = 'heroicon-s-document-plus';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('No'),
                TextColumn::make('user.name')->label('Xaridor')->searchable(),
                TextColumn::make('order_total_price')->label('Narxi')->searchable(),
                TextColumn::make('created_at')->label('Sana')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Xaridor')
                    ->options(User::query()->get()->pluck('name', 'id')->toArray()),
                Filter::make('payment_type')
                    ->label('Buyurtma sanasi')
                    ->form([
                        Forms\Components\Select::make('payment_type')
                            ->options(PaymentType::all())
                            ->label('To`lov turi'),
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['payment_type'],
                                fn(Builder $query, $payment_type): Builder => $query->whereHas('payments', function ($query) use ($payment_type) {
                                    return $query->where(['payment_type' => $payment_type]);
                                }),
                            );
                    }),
                Filter::make('created_at')
                    ->label('Buyurtma sanasi')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Sana dan'),
                        Forms\Components\DatePicker::make('created_until')->label('Sana gacha'),
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\CreateAction::make()
                    ->label('Sotish')
            ])
            ->bulkActions([

            ])->defaultSort('created_at', 'DESC')->paginated([10, 25]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->addActionLabel('Buyurtma qo\'shish')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Grid::make(21)
                                        ->schema([
                                            Forms\Components\Select::make('product_id')
                                                ->label('Mahsulot')
                                                ->placeholder('Mahsulot')
                                                ->relationship('product', 'name')
                                                ->searchable()
                                                ->reactive()
                                                ->columnSpan(7)
                                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                                    $product = Product::query()->find($state);
                                                    $oldPrice = $get('product_price');
                                                    if ($product === null) {
                                                        $set('product_price', 0);
                                                        $set('total_price', 0);
                                                        $set('../../order_total_price', $get('../../order_total_price')-$oldPrice);
                                                        $set('net_price', 0);
                                                        $set('price', 0);
                                                        $set('net_price_uzs', 0);
                                                    } else {
                                                        $set('price', $product->price);
                                                        $set('net_price', $product->net_price);
                                                        $productPrice = collect(get_currency())->where('Ccy', 'USD')->first()['Rate'];
                                                        $productPrice = ceil($productPrice / 1000) * 1000;
                                                        $set('net_price_uzs', $product->net_price * $productPrice);

                                                        $set('unit', $product->unit->name);
                                                        $set('product_price', $product->price);
                                                        $set('product_quantity', $product->quantity);
                                                        $quantityField = $get('quantity') ?? 0;
                                                        $set('total_price', $quantityField * $product->price);
                                                        $order_total_price = $get('../../order_total_price');
                                                        $set('../../order_total_price', $order_total_price + $quantityField * ($product->price - $oldPrice));
                                                    }
                                                }),

                                            Forms\Components\TextInput::make('net_price')
                                                ->numeric()
                                                ->readOnly()
                                                ->label('Tan narxi')
                                                ->columnSpan(3)
                                                ->reactive()
                                                ->hidden(function (Get $get) {
                                                    if ($get('../../net_price_show'))
                                                        return false;
                                                    return true;
                                                })
                                                ->default(0),
                                            Forms\Components\TextInput::make('net_price_uzs')
                                                ->numeric()
                                                ->readOnly()
                                                ->label('Tan narxi')
                                                ->columnSpan(3)
                                                ->reactive()
                                                ->hidden(function (Get $get) {
                                                    if ($get('../../net_price_show'))
                                                        return false;
                                                    return true;
                                                })
                                                ->default(0),
                                            Forms\Components\TextInput::make('product_price')
                                                ->numeric()
                                                ->label('Sotuv narxi')
                                                ->default(0)
                                                ->readOnly()
                                                ->columnSpan(3)
                                                ->helperText(function (Get $get) {
                                                    return 'Dollar kursi: ' . collect(get_currency())->where('Ccy', 'USD')->first()['Rate'];
                                                })
                                                ->hintAction(
                                                    Action::make('net_price')
                                                        ->modalContent()
                                                        ->label('')
                                                        ->icon('heroicon-s-eye')
                                                        ->color('primary')
                                                        ->action(function (Product $product) {
                                                            return 'Tan narxi ' . $product->net_price;
                                                        })
                                                ),
                                            Forms\Components\TextInput::make('quantity')
                                                ->numeric()
                                                ->reactive()
                                                ->live(onBlur: true)
                                                ->readOnly(function (Get $get) {
                                                    return $get('product_id') === null;
                                                })
                                                ->minValue(1)
                                                ->label('Miqdori')
                                                ->columnSpan(2)
                                                ->helperText(function (Get $get) {
                                                    $productQuantity = $get('product_quantity');
                                                    if ($productQuantity > 0) {
                                                        $unit = $get('unit');
                                                        return "Mavjud : $productQuantity $unit";
                                                    }
                                                    return '';
                                                })
                                                ->hintColor('danger')
                                                ->default(1)
                                                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                                    $priceField = floatval($get('product_price')) ?? 0;
                                                    $discountField = floatval($get('discount')) ?? 0;
                                                    $total_price = $priceField * $state - $discountField;
                                                    $old_price = $priceField * $old - $discountField;
                                                    $set('total_price', $total_price);
                                                    $order_total_price = floatval($get('../../order_total_price')) ?? 0;
                                                    $set('../../order_total_price', ($order_total_price - $old_price + $total_price));
                                                }),

                                            Forms\Components\TextInput::make('discount')
                                                ->integer()
                                                ->readOnly(function (Get $get) {
                                                    return $get('product_id') === null;
                                                })
                                                ->label('Chegirma UZS')
                                                ->reactive()
                                                ->columnSpan(3)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                                    $product_price = $get('net_price_uzs');
                                                    $quantity = $get('quantity');
                                                    if (floatval($product_price) < floatval($state)) {
                                                        $set('discount', 0);
                                                        Notification::make()
                                                            ->title('Chegirma oshib ketdi')
                                                            ->danger()
                                                            ->send();
                                                        return;
                                                    } else {
                                                        $set('discount', $state);
                                                        $set('total_price', ($get('product_price') * $quantity - $state));

                                                        $order_total_price = $get('../../order_total_price');
                                                        $old_val = $old;
                                                        if ($state == 0) {
                                                            $old_val = 0;
                                                        }
                                                        $set('../../order_total_price', ($order_total_price + ($old_val - $state)));
                                                    }
                                                })
                                                ->helperText(function (Get $get) {
                                                    $product_price = $get('net_price_uzs');
                                                    $discount = $get('discount');
                                                    if ($product_price < $discount) {

                                                        return 'Chegirma oshib ketdi';
                                                    }
                                                    return '';
                                                }),

                                            Forms\Components\TextInput::make('total_price')
                                                ->numeric()
                                                ->readOnly()
                                                ->columnSpan(3)
                                                ->label('Umumiy narxi')
                                                ->reactive()
                                                ->default(0),
                                        ]),
                                ]),
                        ])
                        ->columns(6)
                        ->label('Mahsulotlar'),
                    Forms\Components\Checkbox::make('net_price_show')
                        ->label("Tan narx")
                        ->live(),
                    TextInput::make('order_total_price')
                        ->label('Umumiy xarid narxi (UZS)')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->readOnly(),
                    TextInput::make('order_total_paid')
                        ->label('To`langan summa (UZS)')
                        ->numeric()
                        ->readOnly()
                        ->live()
                        ->reactive(),
                    Select::make('user_id')
                        ->label('Mijoz')
                        ->searchable()
                        ->required(function (Get $get) {
                            if ($get('order_total_price') === null)
                                return false;
                            if ($get('order_total_paid') === null)
                                return false;
                            if ($get('order_total_price') <= $get('order_total_paid'))
                                return false;
                            return true;
                        })
                        ->hidden(function (Get $get) {
                            if ($get('order_total_price') === null)
                                return true;
                            if ($get('order_total_paid') === null)
                                return true;
                            if ($get('order_total_price') <= $get('order_total_paid'))
                                return true;
                            return false;
                        })
                        ->relationship(name: 'user', titleAttribute: 'name')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required(),
                            Forms\Components\TextInput::make('phone')
                                ->required()
                                ->tel(),
                        ]),

                    Forms\Components\DateTimePicker::make('debt_return_date')
                        ->label('Qarz qaytarish vaqti')
                        ->hidden(function (Get $get) {
                            if ($get('order_total_price') === null)
                                return true;
                            if ($get('order_total_paid') === null)
                                return true;
                            if ($get('order_total_price') <= $get('order_total_paid'))
                                return true;
                            return false;
                        })


                ])
                    ->collapsible()
                    ->heading('Buyurtma yaratish'),
                Forms\Components\Section::make([
                    Forms\Components\Repeater::make('payments')
                        ->relationship('payments')
                        ->addActionLabel("To'lov turi qo'shish")
                        ->schema([
                            Forms\Components\Select::make('payment_type')
                                ->label('To\'lov turi')
                                ->default(2)
                                ->required()
                                ->options(PaymentType::all()),
                            TextInput::make('payed_price')
                                ->label("To'langan summa")
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                    $oldPayed = $get('../../order_total_paid');
                                    $newPayed = $oldPayed - $old + $state;
                                    $set('../../order_total_paid', $newPayed);
                                })
                                ->live(onBlur: true)
                                ->numeric(),
                        ])->columns()
                        ->label('To`lovlar'),
                ])
                    ->heading("To'lov")
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
        ];
    }
}
