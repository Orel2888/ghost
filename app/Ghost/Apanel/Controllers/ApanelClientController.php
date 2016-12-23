<?php

namespace App\Ghost\Apanel\Controllers;

use Illuminate\Http\Request;
use App\Ghost\Repositories\Goods\GoodsOrder as GoodsOrderRepo;
use App\Ghost\Repositories\Goods\GoodsManager as GoodsManager;
use App\Ghost\Repositories\Goods\Exceptions\NotEnoughMoney;
use App\{
    Client,
    Miner,
    City,
    Goods
};
use Validator;

class ApanelClientController extends ApanelBaseController
{
    /**
     * @var GoodsOrderRepo
     */
    public $goodsOrderRepo;

    public $goodsManager;

    public function __construct()
    {
        parent::__construct();

        $this->goodsOrderRepo = new GoodsOrderRepo();
        $this->goodsManager   = new GoodsManager();
    }

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

    public function getDestroy($id)
    {
        $client = Client::find($id);

        $message = "Удалить клиента {$client->name} @{$client->tg_username} из базы навсегда?";

        return $this->apanelRepo->confirmAction($message, route('client.destroy', $client->id), url()->previous(), [
            'form'  => 'delete'
        ]);
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
        Client::find($id)->delete();

        return redirect()->route('client.index')->with('notify', 'Клиент успешно удален');
    }

    /**
     * Create purchase
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function purchase($id)
    {
        $client = Client::find($id);

        $miners = Miner::all();

        $minerList = [];

        foreach ($miners as $miner) {
            $minerList[$miner->id] = $miner->name;
        }

        $cities = City::with('goods')->get();

        return view('apanel.client.purchase_create', [
            'client'         => $client,
            'miner_list'     => $minerList,
            'cities'         => $cities
        ]);
    }

    public function purchaseStore($id)
    {
        $valid = Validator::make(app('request')->all(), [
            'goods_id'  => 'required|exists:goods,id',
            'miner_id'  => 'required|exists:miners,id',
            'weight'    => 'required|min:0.01',
            'cost'      => 'required|min:0',
            'address'   => 'required'
        ], [
            'goods_id.exists'   => 'Такой товар ненайден',
            'miner_id.exists'   => 'Курьер не найден',
            'goods_id.required' => 'Не выбран товар',
            'miner_id.required' => 'Не выбран курьер'
        ])->validate();

        $request = app('request');

        $cost = $request->input('cost') ?? 0;

        $client = Client::find($id);

        $goods  = Goods::find($request->input('goods_id'));

        // Add goods to price list
        $this->goodsManager->addGoodsPrice($request->only('goods_id', 'miner_id', 'weight', 'address', 'cost'));

        // Add order
        $orderGoods = $this->goodsOrderRepo->create([
            'goods_id'  => $request->input('goods_id'),
            'client_id' => $client->id,
            'weight'    => $request->input('weight'),
            'comment'   => $client->comment,
            'cost'      => $cost
        ]);

        $buying = $this->goodsOrderRepo->buyProcessingOrder($orderGoods, $request->input('send_telegram'));

        if ($cost > 0 && $buying instanceof NotEnoughMoney) {
            return redirect()->back()->withErrors(['error' => 'У клиента недостаточно средств на балансе']);
        }

        return redirect()->back()->with('notify', 'Покупка для клиента успешно создана');
    }
}
