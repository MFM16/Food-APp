<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $report;

    public function __construct(array $report)
    {
        $this->report = $report;
    }

    public function array(): array
    {
        return $this->report;
    }

    public function headings(): array
    {
        return ["Produk", "Jumlah Terjual", "Total Harga"];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setCellValue('A6', 'sdfsd');
    }
}
