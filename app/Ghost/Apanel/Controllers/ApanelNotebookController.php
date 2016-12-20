<?php

namespace App\Ghost\Apanel\Controllers;

use App\Ghost\Libs\GibberishAES;
use Crypt;

class ApanelNotebookController extends ApanelBaseController
{
    public function getIndex()
    {
        $notebookFile = storage_path('description.txt');

        if (!file_exists($notebookFile)) {
            file_put_contents($notebookFile, '');
        }
        
        return view('apanel.other.notebook', [
            'content'   => GibberishAES::dec(file_get_contents($notebookFile), env('K5'))
        ]);
    }

    public function postSave()
    {
        if (!app('request')->ajax()) {
            return response()->json([
                'status'    => 'fail'
            ], 400);
        }

        // Verify a content
        if (!GibberishAES::dec(app('request')->input('content'), env('K5'))) {
            return response()->json([
                'status'    => 'fail'
            ], 400);
        }

        file_put_contents(storage_path('description.txt'), app('request')->input('content'));

        return response()->json([
            'status'    => 'ok'
        ]);
    }
}