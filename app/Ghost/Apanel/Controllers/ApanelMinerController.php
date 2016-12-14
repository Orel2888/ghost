<?php

namespace App\Ghost\Apanel\Controllers;

use Illuminate\Http\Request;
use App\Ghost\Domains\Miner\MinerInfoDataProvider;
use App\Http\Requests;
use App\{
    MinerPayment,
    Miner
};

class ApanelMinerController extends ApanelBaseController
{
    /**
     * @var MinerInfoDataProvider
     */
    public $minerInfoDataProvider;

    public function __construct()
    {
        parent::__construct();

        $this->minerInfoDataProvider = new MinerInfoDataProvider();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tplData = [];

        $miners = Miner::query();

        // Reset filter
        if (app('request')->has('filter_reset')) {
            return redirect('apanel/miner');
        }

        // Query build of filter
        if (app('request')->has('filter')) {
            $this->apanelRepo->dbQueryBuilder($miners, app('request')->except('filter', 'filter_reset', 'page'));
        }

        $tplData['miners']      = $miners->paginate(20)->appends(app('request')->all());
        $tplData['form_filter'] = $this->apanelRepo->formFilter([
            'inputs'    => [
                'ID'    => ['name' => 'id'],
                'Имя'   => [
                    'name'      => 'name',
                    'compare'   => ['=', 'like']
                ],
                'Ставка'      => ['name' => 'ante'],
                'Баланс'    => ['name' => 'balance'],
                'Кол-во товара'                 => ['name' => 'counter_goods'],
                'Кол-во товара продано'         => ['name' => 'counter_goods_ok'],
                'Кол-во товара не найдено'      => ['name' => 'counter_goods_fail'],
                'Кол-во товара всего'            => ['name' => 'counter_total_goods']
            ],
            'sorting'   => [
                'columns' => [
                    'ID'            => 'id',
                    'Ставка'        => 'ante',
                    'Баланс'        => 'balance',
                    'Кол-во товара'                 => 'counter_goods',
                    'Кол-во товара продано'         => 'counter_goods_ok',
                    'Кол-во товара не найдено'      => 'counter_goods_fail',
                    'Кол-во товара всего'           => 'counter_total_goods'
                ]
            ]
        ], app('request')->all());

        return view('apanel.miner.index', $tplData);
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
    public function show($minerId)
    {
        //
        $minerData = $this->minerInfoDataProvider->mainStat($minerId);

        return view('apanel.miner.info', [
            'miner' => $minerData
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($minerId)
    {
        //
        $minerData = $this->minerInfoDataProvider->mainStat($minerId);

        return view('apanel.miner.edit', [
            'miner' => $minerData
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $minerId)
    {
        //
        $minerUpdatesParams = $request->only('ante', 'balance', 'counter_goods', 'counter_goods_ok', 'counter_goods_fail', 'counter_total_goods');

        $this->minerInfoDataProvider->minerModel->find($minerId)->update($minerUpdatesParams);

        return redirect(\URL::route('apanel.miner.edit', [$minerId]) .'#form-edit')->with('edited-success', 'Данные курьера успешно обновлены');
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
    }


    public function payment_create($minerId)
    {
        $miner = $this->minerInfoDataProvider->minerModel->find($minerId);

        $message = 'Создать заявку на выплату курьеру '. $miner->name .'? Счетчики будут сброшены и создан чек на выплату.';

        return $this->apanelRepo->confirmAction($message, \URL::route('apanel.miner.payment_store', [$minerId]), \URL::previous(), compact('miner'));
    }

    public function payment_store($minerId)
    {
        $miner = $this->minerInfoDataProvider->minerModel->find($minerId);

        MinerPayment::create([
            'miner_id'  => $miner->id,
            'amount'    => $miner->balance,
            'counter_goods_ok'      => $miner->counter_goods_ok,
            'counter_goods_fail'    => $miner->counter_goods_fail
        ]);

        $miner->update([
            'balance'   => 0,
            'counter_goods' => 0,
            'counter_goods_ok'  => 0,
            'counter_goods_fail'    => 0
        ]);

        return redirect()->route('apanel.miner.show', [$minerId])->with('notify', 'Заявка на выплату успешно создана');
    }

    public function deleteConfirm($minerId)
    {
        $miner = $this->minerInfoDataProvider->minerModel->find($minerId);

        $message = 'Удалить курьера '. $miner->name .' из базы?';

        return $this->apanelRepo->confirmAction($message, \URL::route('apanel.miner.delete', [$minerId]), \URL::previous());
    }

    public function delete($minerId)
    {
        $this->minerInfoDataProvider->minerModel->find($minerId)->delete();

        return redirect()->route('apanel.miner.index')->with('notify', 'Курьер успешно удален');
    }
}
