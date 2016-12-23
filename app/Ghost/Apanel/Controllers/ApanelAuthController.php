<?php

namespace App\Ghost\Apanel\Controllers;

use Auth;

class ApanelAuthController extends ApanelBaseController
{

    public function getLogin()
    {
        return view('apanel.login');
    }

    public function postLogin()
    {
        $this->validate(app('request'), [
            'login'     => 'required|alpha_dash',
            'password'  => 'required|alpha_dash'
        ]);

        if (!Auth::guard('admin')->attempt(['login' => app('request')->input('login'), 'password' => app('request')->input('password')], app('request')->input('remember'))) {
            return redirect()->back()->withErrors(['Ошибка аутентификации']);
        }

        return redirect('apanel');
    }
}