<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Profile;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');  
    }

    public function authenticate(AuthRequest $request)
    {
        if($request->validator->fails())
            return ResponseFormater::error(null, 'Username atau kata sandi salah!', 'Login Gagal', 400);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt($credentials) == false)
            return ResponseFormater::error(null, 'Username atau kata sandi salah!', 'Login Gagal', 400);

        $request->session()->regenerate();
            
        return ResponseFormater::success(null, 'Selamat datang kembali!', 'Login Berhasil');  
    }

    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
     
        $request->session()->regenerateToken();
     
        return redirect('auth');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registered(RegisterRequest $request)
    {
        if($request->validator->fails()){
            $message_error = $request->validator->errors();
            return ResponseFormater::error($message_error, 'Silahkan periksa kembali data yang anda masukan!', 'Registrasi Gagal', 400);
        }

        $user = [
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];

        $lastInsertId = User::create($user);

        if($lastInsertId)
            $id = $lastInsertId->id;

            $profile = [
                'user_id' => $id,
                'name' => $request->name,
            ];

            if(Profile::create($profile))
                return ResponseFormater::success(null, 'Registrasi yang anda lakukan sukses!', 'Registrasi Berhasil'); 

            return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Registrasi Gagal', 400);
        
        return ResponseFormater::error(null, 'Silahkan periksa kembali data yang anda masukan!', 'Registrasi Gagal', 400);
    }
}
