<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Halaman Pengguna',
            'page' => 'user',
            'users' => User::where('role', '!=', 0)->simplePaginate(5),
        ];

        return view('user', $data);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        if($request->validator->fails()){
            $message_error = $request->validator->errors();
            return ResponseFormater::error($message_error, 'Silahkan periksa kembali data yang anda masukan!', 'Registrasi Gagal', 400);
        }

        $user = [
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => $request->role
        ];

        $lastInsertId = User::create($user);

        if($lastInsertId){
            $id = $lastInsertId->id;

            $profile = [
                'user_id' => $id,
                'name' => $request->name,
            ];

            if(Profile::create($profile)){
                return ResponseFormater::success(null, 'Pendaftaran pengguna yang anda lakukan sukses!', 'Pendaftaran Pengguna Berhasil'); 
            }
            return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Pendaftaran Pengguna Gagal', 400);
        }
        
        return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Pendaftaran Pengguna Gagal', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = User::with('profile')->findOrFail($id);

        if($data)
            return ResponseFormater::success($data, 'Data yang anda cari ditemukan', 'Data Ditemukan');

        return ResponseFormater::error(null, 'Data yang anda cari tidak ada!', 'Data Tidak Ditemukan', 400);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request)
    {
        if($request->validator->fails())
            return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Perbaharui Produk Baru', 400);

        $data = [
            'email' => $request->email,
            'role' => $request->role,
        ];

        if(User::where('id', $request->id)->update($data))
            if(Profile::where('user_id', $request->id)->update(['name' => $request->name]))
                return ResponseFormater::success(null, 'Pengguna berhasil diperbaharui!', 'Sukses Perbaharui Data Pengguna');
            return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Perbaharui Pengguna Baru', 400);
        return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Gagal Perbaharui Pengguna Baru', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(User::findOrFail($id)->delete())
            return ResponseFormater::success(null, 'Pengguna berhasil terhapus!', 'Berhasil Hapus Data Pengguna');

        return ResponseFormater::error(null, 'Pengguna gagal terhapus!', 'Gagal Hapus Data Produk', 400);
    }
}
