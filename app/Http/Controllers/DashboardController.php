<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Rating;
use App\Models\TransactionHistory;

class DashboardController extends Controller
{
    public function index()
    {

        $data = [
            'title' => 'Dashboard',
            'page' => 'dashboard'
        ];
        if(Auth::user()->role == 0){
            $data['orders'] = Order::where('profile_id', Auth::user()->profile->id)->where('status' , 5)->get();
            $data['ordersIncomplete'] = Order::where('profile_id', Auth::user()->profile->id)->where('status', '!=' , 5)->orderBy('id', 'ASC')->get();

            return view('dashboard_user', $data);
        }

        if(Auth::user()->role == 1){
            $data['orders'] = Order::where('status', '!=', 5)->orderBy('status', 'ASC')->get();
            $data['completeOrders'] = Order::where('status', 5)->orderBy('status', 'ASC')->simplePaginate(5);
            return view('dashboard', $data);
        }

        if(Auth::user()->role == 2){
            $data['reports'] = TransactionHistory::simplePaginate(10);
            $data['totalOrder'] = DB::table('orders')->where('created_at', 'like', '%' . date('Y') . '%')->count();
            $data['total_amount'] = json_decode(DB::table('transaction_histories')->select(DB::raw('sum(total_amount) as total_amount'))->where('created_at', 'like', '%' . date('Y') . '%')->get(), true);
            $data['ratings'] = Rating::all();
            $total_rating = json_decode(DB::table('ratings')->select(DB::raw('sum(rating) as total_rating'))->get(), true);
            if(!$total_rating[0]['total_rating']){
                $data['avg_rating'] = 0;
            }else{
                $data['avg_rating'] = $total_rating[0]['total_rating'] / count($data['ratings']);  
            }
            return view('report', $data);
        }
    }

}
