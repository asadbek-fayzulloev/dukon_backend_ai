# Do‘konPOS Backend

Laravel 11 asosidagi kichik do‘konlar uchun savdo va ombor backend'i.

## 1-etap qoidalari

- Bitta do‘konda bitta faol kassa va ombor.
- Mahsulot nomi majburiy, barcode ixtiyoriy; variantlar yo‘q.
- Mobil ilova narx yubormaydi. Backend ombordagi faol partiyalarning eng yuqori sotuv narxini oladi.
- Qoldiq eng eski partiyadan boshlab FIFO usulida kamayadi.
- Chegirma butun buyurtmaga foiz (`percentage`) yoki summa (`fixed`) ko‘rinishida beriladi.
- To‘lov `cash`, `card` yoki ikkalasining kombinatsiyasi bo‘lishi mumkin.
- Yetmay qolgan summa mijoz qarzi bo‘ladi va keyinchalik qismlarga bo‘lib to‘lanadi.
- Tovar qaytarish 1-etapga kirmaydi.

## Savdo API kontrakti

`POST /api/v1/orders`

```json
{
  "uuid": "993773df-72d7-4698-9cd6-81906e1958f3",
  "warehouse_id": 1,
  "device_id": "android-kassa-1",
  "items": [
    {"product_id": 15, "quantity": 2.5}
  ],
  "discount_type": "percentage",
  "discount_value": 10,
  "payments": [
    {"payment_type": "cash", "payed_price": 300000},
    {"payment_type": "card", "payed_price": 100000}
  ],
  "user_id": 7,
  "debt_return_date": "2026-08-21 18:00:00",
  "sold_at": "2026-07-21 12:30:00"
}
```

`uuid` offline qayta yuborishlarda idempotency kaliti hisoblanadi. `sold_at` va `device_id` ixtiyoriy. Qarz bo‘lmasa mijoz va qarz muddati ham ixtiyoriy.

## Qarz to‘lovi

`POST /api/v1/debts/{debt}/payments`

```json
{
  "amount": 50000,
  "payment_type": "cash",
  "paid_at": "2026-07-21 15:00:00"
}
```

To‘lov qarz qoldig‘idan oshmaydi. Qoldiq nol bo‘lganda qarz va buyurtma avtomatik yopiladi.

## Ombordagi sotuv mahsulotlari

- `GET /api/v1/warehouse-products?warehouse_id=1`
- `GET /api/v1/warehouses/{warehouse}/products`

Javob har bir mahsulot uchun partiyalar bo‘yicha jami qoldiq va eng yuqori faol sotuv narxini beradi. Tannarx sotuvchi API javobiga chiqarilmaydi.

## Ishga tushirish

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Seeder orqali admin yaratish uchun `.env` ichida `ADMIN_EMAIL` va `ADMIN_PASSWORD` berilishi kerak.
