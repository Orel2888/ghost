<?php

namespace App\Ghost\Apanel\Controllers;

use App\Miner;

class ApanelMinerController extends ApanelBaseController
{
    public function getIndex()
    {
        $tplData = [];

        $miners = Miner::query();

        // Reset filter
        if ($this->request->has('filter_reset')) {
            return redirect('apanel/miner');
        }

        // Query build of filter
        if ($this->request->has('filter')) {
            $this->apanelRepo->dbQueryBuilder($miners, $this->request->except('filter', 'filter_reset', 'page'));
        }

        $tplData['miners']      = $miners->paginate(20)->appends($this->request->all());
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
        ], $this->request->all());
        
        return view('apanel.miner.index', $tplData);
    }
}