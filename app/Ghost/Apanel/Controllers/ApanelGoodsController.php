<?php

namespace App\Ghost\Apanel\Controllers;

use App\City;
use App\Miner;

class ApanelGoodsController extends ApanelBaseController
{
    public function getIndex()
    {
        // Get city and goods
        $cities = City::with('goods')->get();

        // Get weighs by goods and counts
        $goodsWeights = [];
        
        foreach ($cities as $city) {
            $cityId = $city->id;

            $goodsWeights[$cityId] = [];

            foreach ($city->goods as $goods) {
                $goodsWeights[$cityId][$goods->id] = $this->goodsManager->getGoodsWeightsAndCount($goods->id);
            }
        }

        return view('apanel.goods.index', [
            'cities'        => $cities,
            'goods_weights' => $goodsWeights
        ]);
    }

    public function getGoodsPrice()
    {
        $tplData = [];

        $goodsPrice = $this->goodsManager->goodsPrice->query();

        // Reset filter
        if (app('request')->has('filter_reset')) {
            return redirect('apanel/goods-price');
        }

        // Query build of filter
        if (app('request')->has('filter')) {
            $this->apanelRepo->dbQueryBuilder($goodsPrice, app('request')->except('filter', 'filter_reset', 'page'));
        }
        //echo $goodsPrice->toSql();
        $tplData['goods_price']  = $goodsPrice->paginate(20)->appends(app('request')->all());

        // Form filter
        $tplData['form_filter']  = $this->apanelRepo->formFilter([
            'inputs'    => [
                'ID'       => ['name' => 'id'],
                'Товар ID' => ['name' => 'goods_id'],
                'Минер ID' => ['name' => 'miner_id'],
                'Вес'      => ['name' => 'weight'],
                'Адрес'    => [
                    'name'      => 'address',
                    'compare'   => ['=', 'like']
                ],
                'Цена'      => ['name' => 'cost']
            ],
            'selects'       => [
                'В резерве' => [
                    'name'  => 'reserve',
                    'fields' => [
                        -1  => '---',
                        0   => 'Нет',
                        1   => 'Да'
                    ],
                    'selected' => -1
                ]
            ],
            'sorting'   => [
                'columns'   => [
                    'ID'        => 'id',
                    'Вес'       => 'weight',
                    'Цена'      => 'cost'
                ]
            ]
        ], app('request')->all());
        
        return view('apanel.goods.goods_price', $tplData);
    }

    public function getAddCity()
    {
        return view('apanel.goods.addcity');
    }

    public function postAddCity()
    {
        $this->validate(app('request'), [
            'name'  => 'required'
        ]);

        City::create(['name' => app('request')->input('name')]);

        return redirect('apanel/goods/addcity')->with('note', 'Город добавлен');
    }

    public function getEditCity()
    {
        $this->validate(app('request'), [
            'city_id'   => 'required|integer|exists:citys,id'
        ]);
        
        $city = City::find(app('request')->input('city_id'));
        
        return view('apanel.goods.edit_city', compact('city'));
    }

    public function postEditCity()
    {
        $this->validate(app('request'), [
            'city_id'   => 'required|integer|exists:citys,id',
            'name'      => 'required'
        ]);

        City::find(app('request')->input('city_id'))->update(['name' => app('request')->input('name')]);

        return redirect('apanel/goods/edit-city?city_id='. app('request')->input('city_id'))->with('note', 'Изменения успешно сохранены');
    }

    public function getDeleteCity()
    {
        $this->validate(app('request'), [
            'city_id'   => 'required|integer|exists:citys,id'
        ]);

        $city = City::find(app('request')->input('city_id'));

        return view('apanel.goods.delete_city', compact('city'));
    }

    public function postDeleteCity()
    {
        $this->validate(app('request'), [
            'city_id'   => 'required|integer|exists:citys,id'
        ]);

        City::find(app('request')->input('city_id'))->delete();

        return redirect('apanel/goods')->with('note', 'Город успешно удален');
    }

    public function getAddGoods()
    {
        $city = City::find(app('request')->input('city_id'));

        return view('apanel.goods.addgoods', [
            'city'  => $city
        ]);
    }

    public function postAddGoods()
    {
        $this->validate(app('request'), [
            'city_id'       => 'required|numeric',
            'goods_name'    => 'required'
        ]);

        $this->goodsManager->addGoods(app('request')->only('goods_name', 'city_id'));

        return redirect('apanel/goods/addgoods?city_id='. app('request')->input('city_id'))->with('note', 'Категория товара создана');
    }

    public function getAddGoodsPrice()
    {
        $goodsId = app('request')->input('goods_id');

        $tplData = [
            'goods'     => $this->goodsManager->findGoods($goodsId),
            'goods_id'  => $goodsId,
            'miners'    => Miner::all()
        ];

        if (session()->has('goods_file')) {
            $tplData['addresses'] = $this->goodsManager->parseAddresses(file_get_contents(storage_path('goods_uploads/'. session('goods_file'))));

            session()->put('goods_addresses', $tplData['addresses']);
        }

        if (session()->has('goods_list')) {
            $tplData['addresses'] = session('goods_list');

            session()->put('goods_addresses', $tplData['addresses']);
        }

        return view('apanel.goods.addgoods_price', $tplData);
    }

    public function postAddGoodsPrice()
    {
        $goodsId = app('request')->input('goods_id');
        $type    = app('request')->input('type');

        // Save file
        if ($type == 'file') {
            if (app('request')->hasFile('goods_file') && app('request')->file('goods_file')->isValid()) {
                $nameFile = uniqid() . '.txt';

                app('request')->file('goods_file')->move(storage_path('goods_uploads'), $nameFile);

                session()->put('goods_file', $nameFile);

                return redirect('apanel/goods/addgoods-price?goods_id='. $goodsId .'#adding');
            } else {
                return redirect('apanel/goods/addgoods-price?type=file&goods_id='. $goodsId)->withErrors(['Ошибка загрузки файла']);
            }
        }

        // Parse addresses from list
        if ($type == 'list') {
            $goodsList = $this->goodsManager->parseAddresses(app('request')->input('goods_list'));

            if (!count($goodsList)) {
                return redirect('apanel/goods/addgoods-price?goods_id='. $goodsId .'&type=list')->withErrors('Нет ни одного адреса');
            }

            session()->put('goods_list', $goodsList);

            return redirect('apanel/goods/addgoods-price?goods_id='. $goodsId .'#adding');
        }

        // Add goods to price
        if ($type == 'adding') {
            $this->validate(app('request'), [
                'weight'    => 'required',
                'goods_id'  => 'required',
                'cost'      => 'required',
                'miner_id'  => 'required'
            ]);

            if (!session()->has('goods_addresses')) {
                return redirect()->back()->withErrors('Нет данных с адресами');
            }

            $addresses = session('goods_addresses');

            foreach ($addresses as $address) {
                $this->goodsManager->addGoodsPrice([
                    'goods_id'  => $goodsId,
                    'weight'    => app('request')->input('weight'),
                    'address'   => $address,
                    'cost'      => app('request')->input('cost'),
                    'miner_id'  => app('request')->input('miner_id')
                ]);
            }

            session()->forget('goods_addresses');
            session()->forget('goods_list');
            session()->forget('goods_file');

            return redirect('apanel/goods/addgoods-price?goods_id='. $goodsId)->with('note', 'Товар успешно добавлен в прайс лист');
        }
    }

    public function getDeleteGoods()
    {
        $this->validate(app('request'), [
            'goods_id'  => 'required|integer|exists:goods,id'
        ]);

        $goods = $this->goodsManager->goods->with('city')->find(app('request')->input('goods_id'));
        
        return view('apanel.goods.delete_goods', compact('goods'));
    }

    public function postDeleteGoods()
    {
        $this->validate(app('request'), [
            'goods_id'  => 'required|integer|exists:goods,id'
        ]);
        
        $this->goodsManager->goods->find(app('request')->input('goods_id'))->delete();
        
        return redirect('apanel/goods')->with('note', 'Выбранная категория товара, была успешно удалена');
    }

    public function getEditGoods()
    {
        $this->validate(app('request'), [
            'goods_id'  => 'required|integer|exists:goods,id'
        ]);

        $goods = $this->goodsManager->goods->with('city')->find(app('request')->input('goods_id'));
        
        return view('apanel.goods.edit_goods', compact('goods'));
    }

    public function postEditGoods()
    {
        $this->validate(app('request'), [
            'goods_id'  => 'required|integer|exists:goods,id',
            'name'      => 'required'
        ]);
        
        $this->goodsManager->goods->find(app('request')->input('goods_id'))->update(['name' => app('request')->input('name')]);
        
        return redirect('apanel/goods/edit-goods?goods_id='. app('request')->input('goods_id'))->with('note', 'Изменения успешно сохранены');
    }
    
}