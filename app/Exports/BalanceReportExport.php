<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BalanceReportExport implements FromArray, WithEvents
{
    public function __construct(
        private readonly string $warehouseName,
        private readonly string $fromDate,
        private readonly string $toDate,
        private readonly array $rows,
        private readonly array $summary,
    ) {
    }

    public function array(): array
    {
        $data = [];
        $data[] = ["Saldo hisoboti — {$this->warehouseName}"];
        $data[] = ["Davr: {$this->fromDate} — {$this->toDate}"];
        $data[] = [];
        $data[] = ['№', 'Nomi', 'Kodi', 'Birligi', 'Narx', 'Davr boshida', '', 'Kirim', '', 'Chiqim', '', 'Davr oxirida', '', 'Hozirgi qoldiq', ''];
        $data[] = ['', '', '', '', '', 'Miqdor', 'Summa', 'Miqdor', 'Summa', 'Miqdor', 'Summa', 'Miqdor', 'Summa', 'Miqdor', 'Summa'];

        $i = 1;
        foreach ($this->rows as $row) {
            $data[] = [
                $i++,
                $row['product_name'],
                $row['product_code'],
                $row['unit_name'],
                $row['net_price'],
                round($row['beginning_qty'], 3), round($row['beginning_sum'], 2),
                round($row['incoming_qty'], 3), round($row['incoming_sum'], 2),
                round($row['outgoing_qty'], 3), round($row['outgoing_sum'], 2),
                round($row['ending_qty'], 3), round($row['ending_sum'], 2),
                round($row['current_qty'], 3), round($row['current_sum'], 2),
            ];
        }

        $data[] = [
            '', 'JAMI', '', '', '',
            round($this->summary['total_beginning_qty'], 3), round($this->summary['total_beginning_sum'], 2),
            round($this->summary['total_incoming_qty'], 3), round($this->summary['total_incoming_sum'], 2),
            round($this->summary['total_outgoing_qty'], 3), round($this->summary['total_outgoing_sum'], 2),
            round($this->summary['total_ending_qty'], 3), round($this->summary['total_ending_sum'], 2),
            round($this->summary['total_current_qty'], 3), round($this->summary['total_current_sum'], 2),
        ];

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:O1');
                $sheet->mergeCells('A2:O2');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

                foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
                    $sheet->mergeCells("{$col}4:{$col}5");
                }
                $sheet->mergeCells('F4:G4');
                $sheet->mergeCells('H4:I4');
                $sheet->mergeCells('J4:K4');
                $sheet->mergeCells('L4:M4');
                $sheet->mergeCells('N4:O4');

                $sheet->getStyle('A4:O5')->getFont()->setBold(true);
                $sheet->getStyle('A4:O5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A4:O5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A6:O{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A{$lastRow}:O{$lastRow}")->getFont()->setBold(true);

                foreach (range('A', 'O') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
