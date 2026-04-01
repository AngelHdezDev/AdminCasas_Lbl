<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'correo' => 'required|email',
            'contra' => 'required',
        ]);


        if (Auth::attempt(['correo' => $request->correo, 'password' => $request->contra])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }


        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no son correctas.',
        ])->withInput($request->only('correo'));
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 
        return redirect('/login'); 
    }
}