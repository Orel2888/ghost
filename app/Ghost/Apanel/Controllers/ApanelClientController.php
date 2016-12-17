<?php

namespace App\Ghost\Apanel\Controllers;

use Illuminate\Http\Request;
use App\Client;

class ApanelClientController extends ApanelBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $client = Client::find($id);

        return view('apanel.client.show', [
            'client'    => $client
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $client = Client::find($id);

        return view('apanel.client.edit', [
            'client'    => $client
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        Client::find($id)->update(
            app('request')->only('name', 'tg_username', 'rating', 'balance', 'count_purchases', 'notify')
        );

        return redirect()->back()->with('notify', 'Клиент отредактирован успешно');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        echo 'Destroy';
    }
}
