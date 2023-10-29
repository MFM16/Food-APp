<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Http\Requests\StoreCostRequest;
use App\Http\Requests\UpdateCostRequest;

class CostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Halaman Daftar Ongkos Kirim',
            'page' => 'cost',
            'costs' => Cost::simplePaginate(5)
        ];

        return view('cost', $data);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCostRequest $request)
    {
        if($request->validator->fails())
            return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Tambah Ongkos Kirim', 400);
    
        $data = [
            'distance' => $request->distance,
            'cost' => $request->cost,
        ];
    
        if(Cost::create($data))
            return ResponseFormater::success(null, 'Ongkos Kirim berhasil ditambahkan', 'Sukses Tambah Data Ongkos Kirim');
    
        return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Tambah Produk Baru', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Cost::findOrFail($id);
        if($data)
            return ResponseFormater::success($data, 'Data yang anda cari ditemukan', 'Data Ditemukan');

        return ResponseFormater::error(null, 'Data yang anda cari tidak ada!', 'Data Tidak Ditemukan', 400);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCostRequest $request)
    {
        if($request->validator->fails())
            return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Perbaharui Ongkos Kirim', 400);

        $data = [
            'distance' => $request->distance,
            'cost' => $request->cost,
        ];

        if(Cost::where('id', $request->id)->update($data))
            return ResponseFormater::success(null, 'Ongkos kirim berhasil diperbaharui!', 'Sukses Perbaharui Data Ongkos Kirim');

        return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Perbaharui Ongkos Kirim', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(Cost::findOrFail($id)->delete())
        return ResponseFormater::success(null, 'Ongkos kirim berhasil terhapus!', 'Berhasil Hapus Data Ongkos Kirim');

        return ResponseFormater::error(null, 'Ongkos kirim gagal terhapus!', 'Gagal Hapus Data Ongkos Kirim', 400);
    }

    public function getShippingCost($distance)
    {
        if($distance > 20000)
            return ResponseFormater::error(null, 'Alamat pengiriman yang anda masukan diluar dari jangkauan kami !', 'Diluar Jangkauan', 400);

        $cost = Cost::where('distance', '<', $distance)->orderBy('distance', 'DESC')->first();
        
        return ResponseFormater::success($cost, 'Data berhasil ditemukan!', 'Data Ditemukan');
    }
}
