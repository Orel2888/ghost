<?php

namespace App\Ghost\Apanel\Controllers;

use App\GoodsPurchase;

class ApanelPurchaseController extends ApanelBaseController
{
    public function getIndex()
    {
        $tplData = [];
        
        $purchases = GoodsPurchase::query();

        // Reset filter
        if ($this->request->has('filter_reset')) {
            return redirect('apanel/purchase');
        }

        // Query build of filter
        if ($this->request->has('filter')) {
            $this->apanelRepo->dbQueryBuilder($purchases, $this->request->except('filter', 'filter_reset', 'page'));
        }

        $tplData['purchases']   = $purchases->paginate(20)->appends($this->request->all());
        $tplData['form_filter'] = $this->apanelRepo->formFilter([
            'inputs'    => [
                'ID'        => ['name' => 'id'],
                'Город ID'  => ['name' => 'city_id'],
                'Товар ID'  => ['name' => 'goods_id'],
                'Минер ID'  => ['name' => 'miner_id'],
                'Клиент ID' => ['name' => 'client_id'],
                'Вес'       => ['name' => 'weight'],
                'Цена'      => ['name' => 'cost'],
                'Адрес'     => [
                    'name'      => 'address',
                    'compare'   => ['=', 'like']
                ]
            ],
            'selects'   => [
                'Статус'    => [
                    'name'      => 'status',
                    'fields'    => [
                        -1 => '---',
                        1  => 'OK',
                        2  => 'Fail'
                    ],
                    'selected' => -1
                ]
            ],
            'sorting'   => [
                'columns'   => [
                    'ID'        => 'id',
                    'Город ID'  => 'city_id',
                    'Товар ID'  => 'goods_id',
                    'Минер ID'  => 'miner_id',
                    'Клиент ID' => 'client_id',
                    'Вес'       => 'weight',
                    'Цена'      => 'cost',
                    'Статус'    => 'status'
                ]
            ]
        ], $this->request->all());
        
        return view('apanel.purchase.index', $tplData);
    }
}