<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionHistory;
use App\Models\Product;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'title' => 'Laporan Penjualan',
            'page' => 'report',
        ];
        if($request->minDate && $request->maxDate){
            $minDate = $request->minDate;
            $maxDate = $request->maxDate;
            $enddate = date('Y-m-d H:i:s', strtotime($maxDate. ' + 1 days'));

            $data['stock_out'] = json_decode(DB::table('transaction_histories')->select(DB::raw('item, sum(stock_out) as stock_out, sum(total_amount) as total_amount'))->whereBetween('created_at', [$minDate, $enddate])->groupBy('item')->get(), true);

            $data['total_amount'] = json_decode(DB::table('transaction_histories')->select(DB::raw('sum(total_amount) as total_amount'))->whereBetween('created_at', [$minDate, $enddate])->get(), true);

            $data['products'] = Product::all();

            $data['stock_in'] = json_decode(DB::table('history_products')->select(DB::raw('product_name, sum(stock_in) as stock_in'))->whereBetween('created_at', [$minDate, $enddate])->groupBy('product_name')->get(), true);

            $data['minDate'] = $minDate;
            $data['maxDate'] = $maxDate;
        }

        return view('report_index', $data);
    }

    public function excelReport(Request $request){
        $minDate = $request->minDate;
        $maxDate = $request->maxDate;

        ExcelReportController::export($minDate, $maxDate);
    }
}
