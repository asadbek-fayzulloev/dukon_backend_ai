<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromQuery, WithHeadings
{
    use Exportable;

    public function query()
    {
        return User::query();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Ism',
            'Raqami',
            'Yaratilgan vaqt',
        ];
    }
}
