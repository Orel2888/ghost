<?php

namespace App\Ghost\Apanel\Controllers;

use App\Ghost\Libs\GibberishAES;
use Crypt;

class ApanelNotebookController extends ApanelBaseController
{
    public function getIndex()
    {
        return view('apanel.other.notebook', [
            'content'   => GibberishAES::dec(file_get_contents(storage_path('description.txt')), env('K5'))
        ]);
    }

    public function postSave()
    {
        if (!$this->request->ajax()) {
            return response()->json([
                'status'    => 'fail'
            ], 400);
        }

        // Verify a content
        if (!GibberishAES::dec($this->request->input('content'), env('K5'))) {
            return response()->json([
                'status'    => 'fail'
            ], 400);
        }

        file_put_contents(storage_path('description.txt'), $this->request->input('content'));

        return response()->json([
            'status'    => 'ok'
        ]);
    }
}