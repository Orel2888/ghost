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
        $payments = MinerPayment::query();

        // Reset a filter
        if ($this->request->has('filter_reset')) {
            return redirect()->current();
        }

        // Apply a filter for query
        if ($this->request->has('filter')) {
            $this->apanelRepo->eloquentFilter($payments, $this->request->except('filter', 'filter_reset', 'page'));
        }

        $tplData = [];

        $tplData['payments'] = $payments->paginate(20)->appends($this->request->all());

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
