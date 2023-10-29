<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\HistoryProduct;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Halaman Produk',
            'page' => 'product',
            'products' => Product::simplePaginate(5)
        ];

        return view('product', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        if($request->validator->fails())
            return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Tambah Produk Baru', 400);

        $data = [
            'name' => $request->name,
            'stock' => $request->stock,
            'price' => $request->price
        ];

        $data['photo'] = $request->file('photo')->store('product-img');

        if(Product::create($data))
            return ResponseFormater::success(null, 'Produk baru berhasil ditambahkan', 'Sukses Tambah Data Produk');

        return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Tambah Produk Baru', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Product::findOrFail($id);
        if($data)
            return ResponseFormater::success($data, 'Data yang anda cari ditemukan', 'Data Ditemukan');

        return ResponseFormater::error(null, 'Data yang anda cari tidak ada!', 'Data Tidak Ditemukan', 400);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request)
    {
        if($request->validator->fails())
            return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Perbaharui Produk Baru', 400);

        $data = [
            'name' => $request->name,
            'stock' => $request->stock,
            'price' => $request->price
        ];

        $product = Product::find($request->id);
        
        if($product['stock'] < $request->stock){
            $stock_in = $request->stock - $product['stock'];
            
            $update_stock = [
                'product_name' => $product['name'],
                'stock_in' => $stock_in
            ];

            HistoryProduct::create($update_stock);
        }

        if($request->file('photo'))
            $data['photo'] = $request->file('photo')->store('product-img');

        if(Product::where('id', $request->id)->update($data))
            return ResponseFormater::success(null, 'Produk berhasil diperbaharui!', 'Sukses Perbaharui Data Produk');

        return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Perbaharui Produk Baru', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(Product::findOrFail($id)->delete())
            return ResponseFormater::success(null, 'Produk berhasil terhapus!', 'Berhasil Hapus Data Produk');

        return ResponseFormater::error(null, 'Produk gagal terhapus!', 'Gagal Hapus Data Produk', 400);
    }

    public function history_stock()
    {
        $data = [
            'title' => 'Halaman Stok',
            'page' => 'stock',
            'stocks' => HistoryProduct::orderBy('id', 'DESC')->simplePaginate(5),
            'products' => Product::where('stock', 0)->get()
        ];

        return view('stock', $data);
    }

    public function add_stock(Request $request)
    {
        $product = Product::where('name', $request->productId)->first();
        $add_stock = (int) $product->stock + (int) $request->stock;

        $update_stock = [
            'product_name' => $request->productId,
            'stock_in' => $add_stock
        ];

        if(HistoryProduct::create($update_stock)){
            if(Product::where('name', $request->productId)->update(['stock' => $add_stock])){
                return ResponseFormater::success(null, 'Stok Berhasil Ditambah', 'Berhasil tambah stok produk');
            }
            return ResponseFormater::error(null, 'Stok Gagal Ditambah', 'Gagal tambah stok produk', 400);
        }
        return ResponseFormater::error(null, 'Stok Gagal Ditambah', 'Gagal tambah stok produk', 400);
    }
}
