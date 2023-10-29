<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelReportController extends Controller
{
    public static function export($mindate, $maxdate)
    {
        $enddate = date('Y-m-d H:i:s', strtotime($maxdate. ' + 1 days'));

        $data = json_decode(DB::table('transaction_histories')->select(DB::raw('item, sum(stock_out) as stock_out, sum(total_amount) as total_amount'))->whereBetween('created_at', [$mindate, $enddate])->groupBy('item')->get(), true);

        $total_amount = json_decode(DB::table('transaction_histories')->select(DB::raw('sum(total_amount) as total_amount'))->whereBetween('created_at', [$mindate, $enddate])->get(), true);

        $produtcs = Product::all();
        
        $stock_in = json_decode(DB::table('history_products')->select(DB::raw('product_name, sum(stock_in) as stock_in'))->whereBetween('created_at', [$mindate, $enddate])->groupBy('product_name')->get(), true);

        $no = 1;
        $index = 0;
        $count = count($produtcs) + 2;

        $date_from = strtotime($mindate);
        $from = date('j M Y', $date_from);

        $date_to = strtotime($maxdate);
        $to = date('j M Y', $date_to);

        $spreadsheet = new Spreadsheet();

        $style_row = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ]
        ];
        $style_col = [
            'font' => ['bold' => true],
            'fontSize' => ['size' => 12],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ]
        ];

        $style_cap = [
            'font' => ['bold' => true],
            'fontSize' => ['size' => 12],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        $spreadsheet->getActiveSheet()
            ->setCellValue('A1', "LAPORAN PENCATATAN\nWARUNG JAWA TIMUR\n". $from ." - " . $to ."\n")
            ->mergeCells('A1:G1')
            ->getStyle('A1:G1')->applyFromArray($style_col);
        $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(12);
        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(50);

        $spreadsheet->getActiveSheet()
            ->setCellValue('A2', 'No.')
            ->setCellValue('B2', 'Produk')
            ->setCellValue('C2', 'Jumlah Stok Tersedia')
            ->setCellValue('D2', 'Jumlah Stok Masuk')
            ->setCellValue('E2', 'Jumlah Stok Terjual')
            ->setCellValue('F2', 'Harga produk')
            ->setCellValue('G2', 'Total Penjualan')
            ->getStyle('A2:G2')->applyFromArray($style_col);

        $spreadsheet->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(100, 'pt');

        $spreadsheet->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(120, 'pt');

        $spreadsheet->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(120, 'pt');

        $spreadsheet->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(120, 'pt');

        $spreadsheet->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(70, 'pt');

        $spreadsheet->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(120, 'pt');

        $spreadsheet->getActiveSheet()
            ->getRowDimension('1')
            ->setRowHeight(40, 'pt');

        for($i = 3; $i <= $count; $i++){

            $stock_out = '-';
            $total_price = '-';
            $stock_in_update = '-';

            for ($iterate=0; $iterate <count($data) ; $iterate++) { 
                switch ($data[$iterate]['item']) {
                    case $produtcs[$index]['name']:
                        $stock_out = $data[$iterate]['stock_out'];
                        $total_price = $data[$iterate]['total_amount'];
                        break;
                }
            }

            for ($iteration=0; $iteration <count($stock_in) ; $iteration++) { 
                switch ($stock_in[$iteration]['product_name']) {
                    case $produtcs[$index]['name']:
                        $stock_in_update = $stock_in[$iteration]['stock_in'];
                        break;
                }
            }

            $spreadsheet->getActiveSheet()
            ->setCellValue('A' . $i. '', $no)
            ->setCellValue('B' . $i. '', $produtcs[$index]['name'])
            ->setCellValue('C' . $i. '', number_format($produtcs[$index]['stock']))
            ->setCellValue('D' . $i. '', $stock_in_update == '-' ? '-' : number_format($stock_in_update))
            ->setCellValue('E' . $i. '', $stock_out == '-' ? '-' : number_format($stock_out))
            ->setCellValue('F' . $i. '', number_format($produtcs[$index]['price']))
            ->setCellValue('G' . $i. '', $total_price == '-' ? '-' : number_format($total_price))
            ->getStyle('A' . $i .':G'. $i .'')->applyFromArray($style_row);

            $no++;
            $index++;
        }

        $spreadsheet->getActiveSheet()
            ->setCellValue('A'.$i.'', "Jumlah Pemasukan")
            ->mergeCells('A'.$i. ':F'.$i.'')
            ->getStyle('A'.$i. ':G'.$i.'')->applyFromArray($style_col);

        $spreadsheet->getActiveSheet()
            ->setCellValue('G'.$i.'', number_format($total_amount[0]['total_amount']))
            ->getStyle('G'.$i.'')->applyFromArray($style_col);

        $spreadsheet->getActiveSheet()
            ->setCellValue('G'.$i + 2 .'', "Depok, " . date('j M Y') . "\n\n\n\n\n Herni Susilowati")
            ->getStyle('G'.$i + 2 .'')->applyFromArray($style_cap);

        $spreadsheet->getActiveSheet()->getStyle('G'.$i + 2 .'')->getAlignment()->setWrapText(true);

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Laporan Pencatatan Bulan ' . $mindate . ' - ' . $maxdate . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}