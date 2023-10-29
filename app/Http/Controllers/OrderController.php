<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\Profile;
use App\Models\TransactionHistory;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use PDF;

class OrderController extends Controller
{
    public $items;

    public function index()
    {
        $data = [
            'title' => 'Halaman Pesanan',
            'page' => 'order',
            'products' => Product::all(),
        ];

        return view('order', $data);
    }

    private function updateStock($items)
    {
        $items = explode(',', $items);
        for($i = 0; $i < count($items); $i++) {
            $data = explode(' ', $items[$i]);
            $count = count($data) - 2;
            $countItems = $data[$count];

            $arr = [];
            for($j = 0; $j < $count; $j++) {
                if($data[$j] != ''){
                    array_push($arr, $data[$j]);
                }
            }
            $name = implode(' ', $arr);
            $product = Product::select('stock', 'price')->where('name', $name)->first();
            $stock = $product->stock;
            $price = $product->price;
            $remainingStock = $stock - $countItems;
            $total_amount = $countItems * $price;
            Product::where('name', $name)->update(['stock' => $remainingStock]);
            TransactionHistory::create(['item' => $name, 'stock_out' => $countItems, 'total_amount' => $total_amount]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        if($request->validator->fails())
            return ResponseFormater::error(null, 'Silahkan periksa kembali pesanan anda!', 'Gagal Memesan', 400);

        $order = [
            'profile_id' => $request->profile_id,
            'item' => $request->item,
            'address' => $request->role_id == 0 ? $request->full_address : '',
            'total_amount' => $request->total_amount,
            'message' => $request->message,
            'status' => $request->status
        ];

        if(Order::create($order))
            if($request->role_id == 0){
                $data_user = [
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'village' => $request->village,
                    'district' => $request->district,
                ];

                if(Profile::where('id', $request->profile_id)->update($data_user)){
                    return ResponseFormater::success(null, 'Pesanan anda berhasil tercatat', 'Berhasil Memesan');
                }

                return ResponseFormater::error(null, 'Silahkan periksa kembali pesanan anda!', 'Gagal Memesan', 400);
            }
            $this->updateStock($request->item);
            return ResponseFormater::success(null, 'Pesanan anda berhasil tercatat', 'Berhasil Memesan');

        return ResponseFormater::error(null, 'Silahkan periksa kembali pesanan anda!', 'Gagal Memesan', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Order::findOrFail($id);
        if($data)
            return ResponseFormater::success($data, 'Data yang anda cari ditemukan', 'Data Ditemukan');

        return ResponseFormater::error(null, 'Data yang anda cari tidak ditemukan!', 'Data Tidak Ditemukan', 400);
    }

    public function midtrans_pay(Request $request)
    {
        $this->status = $request->status;
        $this->items = $request->items;

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('app.midtrans_server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $detail_order = [
            'transaction_details' => [
                'order_id' => 'TRX|'. $request->order_id. '|' . rand(000000000, 999999999),
                'gross_amount' => $request->amount,
            ], 
            "enabled_payments" => [
                "credit_card",
                "shopeepay",
            ],
            "credit_card" => [
                "secure"=> true
            ],
            'customer_details' => [
                'first_name' =>Auth::user()->profile->name,
                'last_name' => '',
                'email' => Auth::user()->email,
                'phone' => Auth::user()->profile->phone_number,
            ]
        ];

        $snapUrl = \Midtrans\Snap::getSnapUrl($detail_order);
        $data = [
            'status' => 'midtrans-payment',
            'url' => $snapUrl,
        ];
        return ResponseFormater::success($data, 'Transaksi Berhasil Dibuat', 'Transaksi Sukses');
    }

    public function after_payment_handler(Request $request)
    {
        $midtrans_server_key = config('app.midtrans_server_key');
        $signature_hashed = hash('sha512', $request->order_id.$request->status_code.$request->gross_amount.$midtrans_server_key);

        if($signature_hashed == $request->signature_key){
            if($request->transaction_status == 'capture' || $request->transaction_status == 'settlement'){
                $id = explode('|', $request->order_id);
                $order_id = $id['1'];
                $order = Order::findOrFail($order_id);
                $this->updateStock($order->item);
                if(Order::where('id', $order_id)->update(['status' => 1]))
                    return redirect('admin/dashboard');
            }
        }
    }

    public function update_status(Request $request)
    {
        if($request->status == 1){
            $this->updateStock($request->items);
        }

        if($request->status == 5){
            $array= [];
            $order = Order::findOrFail($request->order_id);

            $items = explode(',', $order->item);
            foreach($items as $item){
                $temp = trim($item);
                array_push($array, $temp);
            }
        }

        if(Order::where('id', $request->order_id)->update(['status' => $request->status]))
            if($request->process == 'confirm'){
                return ResponseFormater::success(null, 'Pesanan anda berhasil dikonfirmasi, silahkan menunggu', 'Pesanan Terkonfirmasi');
            }

            return ResponseFormater::success(null, 'Pesanan berhasil diteruskan ke proses selanjutnya', 'Proses Selanjutnya');
        return ResponseFormater::error(null, 'Pesanan anda gagal terkonfirmasi', 'Pesanan Gagal Terkonfirmasi', 400);

    }

    public function getOrderByProfileId($id)
    {
        $data = Order::where('profile_id', $id)->where('status', '!=' , 5)->orderBy('id', 'ASC')->get();
        if($data)
            return ResponseFormater::success($data, 'Data yang anda cari ditemukan', 'Data Ditemukan');

        return ResponseFormater::error(null, 'Data yang anda cari tidak ditemukan!', 'Data Tidak Ditemukan', 400);
    }

    public function getRatingOrderByProfileId($id)
    {
        $data = Order::where('profile_id', $id)->where('status', 5)->orderBy('id', 'ASC')->get();
        if($data)
            return ResponseFormater::success($data, 'Data yang anda cari ditemukan', 'Data Ditemukan');

        return ResponseFormater::error(null, 'Data yang anda cari tidak ditemukan!', 'Data Tidak Ditemukan', 400);
    }

    public function getAllIncompleteOrders()
    {
        $data = Order::where('status', '!=', 5)->orderBy('id', 'ASC')->get();
        if($data)
            return ResponseFormater::success($data, 'Data yang anda cari ditemukan', 'Data Ditemukan');

        return ResponseFormater::error(null, 'Data yang anda cari tidak ditemukan!', 'Data Tidak Ditemukan', 400);
    }
}
