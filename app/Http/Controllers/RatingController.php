<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Order;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Requests\UpdateRatingRequest;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request)
    {
        if($request->validator->fails())
            return ResponseFormater::error(null, 'Penilaian gagal', 'Penilaian Gagal', 400);

        $data = [
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ];

        if(Rating::create($data))
            if(Order::where('id', $request->order_id)->update(['rate' => 1]))
                return ResponseFormater::success(null, 'Penilaian berhasil diberikan', 'Sukses Pemberian Penilaian');
                
            return ResponseFormater::error(null, 'Penilaian gagal', 'Penilaian Gagal', 400);

        return ResponseFormater::error(null, 'Penilaian gagal', 'Penilaian Gagal', 400);
    }
}
