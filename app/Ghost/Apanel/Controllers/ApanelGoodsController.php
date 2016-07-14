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

        $goodsPrice = $this->goodsManager->goodsPrice->paginate(20);


        $tplData['goods_price'] = $goodsPrice;

        return view('apanel.goods.goods_price', $tplData);
    }

    public function getAddCity()
    {
        return view('apanel.goods.addcity');
    }

    public function postAddCity()
    {
        $this->validate($this->request, [
            'name'  => 'required'
        ]);

        City::create(['name' => $this->request->input('name')]);

        return redirect('apanel/goods/addcity')->with('note', 'Город добавлен');
    }

    public function getAddGoods()
    {
        $city = City::find($this->request->input('city_id'));

        return view('apanel.goods.addgoods', [
            'city'  => $city
        ]);
    }

    public function postAddGoods()
    {
        $this->validate($this->request, [
            'city_id'       => 'required|numeric',
            'goods_name'    => 'required'
        ]);

        $this->goodsManager->addGoods($this->request->only('goods_name', 'city_id'));

        return redirect('apanel/goods/addgoods?city_id='. $this->request->input('city_id'))->with('note', 'Категория товара создана');
    }

    public function getAddGoodsPrice()
    {
        $goodsId = $this->request->input('goods_id');

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
        $goodsId = $this->request->input('goods_id');
        $type    = $this->request->input('type');

        // Save file
        if ($type == 'file') {
            if ($this->request->hasFile('goods_file') && $this->request->file('goods_file')->isValid()) {
                $nameFile = uniqid() . '.txt';

                $this->request->file('goods_file')->move(storage_path('goods_uploads'), $nameFile);

                session()->put('goods_file', $nameFile);

                return redirect('apanel/goods/addgoods-price?goods_id='. $goodsId .'#adding');
            } else {
                return redirect('apanel/goods/addgoods-price?type=file&goods_id='. $goodsId)->withErrors(['Ошибка загрузки файла']);
            }
        }

        // Parse addresses from list
        if ($type == 'list') {
            $goodsList = $this->goodsManager->parseAddresses($this->request->input('goods_list'));

            if (!count($goodsList)) {
                return redirect('apanel/goods/addgoods-price?goods_id='. $goodsId .'&type=list')->withErrors('Нет ни одного адреса');
            }

            session()->put('goods_list', $goodsList);

            return redirect('apanel/goods/addgoods-price?goods_id='. $goodsId .'#adding');
        }

        // Add goods to price
        if ($type == 'adding') {
            $this->validate($this->request, [
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
                    'weight'    => $this->request->input('weight'),
                    'address'   => $address,
                    'cost'      => $this->request->input('cost'),
                    'miner_id'  => $this->request->input('miner_id')
                ]);
            }

            session()->forget('goods_addresses');
            session()->forget('goods_list');
            session()->forget('goods_file');

            return redirect('apanel/goods/addgoods-price?goods_id='. $goodsId)->with('note', 'Товар успешно добавлен в прайс лист');
        }
    }
}