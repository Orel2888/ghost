<?php

namespace App\Ghost\Apanel\Controllers;

use App\Client;

class ApanelClientController extends ApanelBaseController
{
    public function getIndex()
    {
        $tplData = [];
        
        $client = Client::query();

        // Reset filter
        if (app('request')->has('filter_reset')) {
            return redirect('apanel/client');
        }

        // Query build of filter
        if (app('request')->has('filter')) {
            $this->apanelRepo->dbQueryBuilder($client, app('request')->except('filter', 'filter_reset', 'page'));
        }

        $tplData['clients']     = $client->paginate(20)->appends(app('request')->all());

        $tplData['form_filter'] = $this->apanelRepo->formFilter([
            'inputs'    => [
                'ID'    => ['name' => 'id'],
                'Ник'  => [
                    'name'      => 'name',
                    'compare'   => ['=', 'like']
                ],
                'TG ID' => ['name' => 'tg_chatid'],
                'Tg username'   => [
                    'name'      => 'tg_username',
                    'compare'   => ['=', 'like']
                ],
                'Рейтинг'       => ['name' => 'rating'],
                'Баланс'        => ['name' => 'balance'],
                'Кол-во покупок'    => ['name' => 'count_purchases']
            ],
            'sorting'   => [
                'columns'   => [
                    'ID'        => 'id',
                    'Рейтинг'   => 'rating',
                    'Баланс'    => 'balance',
                    'Кол-во покупок' => 'count_purchases'
                ]
            ]
        ], app('request')->all());

        return view('apanel.client.index', $tplData);
    }
}