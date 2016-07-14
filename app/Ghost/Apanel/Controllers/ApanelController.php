<?php

namespace App\Ghost\Apanel\Controllers;

class ApanelController extends ApanelBaseController
{
    public function getIndex()
    {
        return view('apanel.index');
    }
}