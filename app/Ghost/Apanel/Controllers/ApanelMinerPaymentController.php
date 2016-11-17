<?php

namespace App\Ghost\Apanel\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\MinerPayment;

class ApanelMinerPaymentController extends ApanelBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = MinerPayment::orderBy('status', 'ASC');

        // Reset a filter
        if ($this->request->has('filter_reset')) {
            return redirect()->current();
        }

        // Apply a filter for query
        if ($this->request->has('filter')) {
            $this->apanelRepo->eloquentFilter($payments, $this->request->except('filter', 'filter_reset', 'page'));
        }

        $tplData = [];

        $tplData['payments']    = $payments->paginate(20)->appends($this->request->all());
        $tplData['form_filter'] = $this->apanelRepo->formFilter([
            'period_date'   => true,
            'inputs'    => [
                'ID'    => ['name' => 'id'],
                'Минер ID'  => ['name' => 'miner_id'],
                'Сумма'     => ['name' => 'amount'],
                'Количество найденных кладов'   => ['name' => 'counter_goods_ok'],
                'Количество ненайденных кладов' => ['name' => 'counter_goods_fail']
            ],
            'selects'   => [
                'Статус'    => [
                    'name'      => 'status',
                    'fields'    => [
                        -1  => '---',
                        0   => 'В обработке',
                        1   => 'Выплачено',
                        2   => 'Отклонено'
                    ],
                    'selected'  => -1
                ]
            ],
            'sorting' => [
                'columns'   => [
                    'ID'    => 'id',
                    'Сумма' => 'amount',
                    'Количество найденных кладов'      => 'counter_goods_ok',
                    'Количество ненайденных кладов'    => 'counter_goods_fail'
                ]
            ]
        ], $this->request->all());

        return view('apanel.miner.payment.index', $tplData);
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
}
