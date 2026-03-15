<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function showFormLogin()
    {
        if (isBeLogin()) {
            return redirect()->route(backendRouteName('dashboard'));
        }

        return view('backend.auth.login');
    }

    public function postLogin()
    {
        $checkLogin = beGuard()->attempt([
            'email'    => request('email'),
            'password' => request('password'),
        ]);

        if ($checkLogin) {
            return redirect()->route(backendRouteName('dashboard'));
        }

        return redirect()->back()
            ->withErrors('Email hoặc Password không chính xác')
            ->withInput(request()->all());
    }

    public function logout()
    {
        beGuard()->logout();
        return redirect()->route(backendRouteName('auth.login'));
    }
}
