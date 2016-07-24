<?php

namespace App\Ghost\Api\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Client;
use Validator;
use App\Purse;

class UsersApiController extends BaseApiController
{
    /**
     * users.find
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFind()
    {
        $valid = Validator::make($this->request->all(), [
            'tg_chatid' => 'required|numeric'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        try {
            $client = $this->clientManager->findByTgChatId($this->request->input('tg_chatid'));
        } catch (ModelNotFoundException $e) {
            return response()->json($this->apiResponse->fail([
                'message' => 'Client with this chat id not found. For a registration call request api method users.reg'
            ]), 404);
        }

        return response()->json($this->apiResponse->ok(['data' => $client]));
    }

    /**
     * users.reg
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReg()
    {
        $valid = Validator::make($this->request->all(), [
            'name'          => 'required',
            'tg_username'   => 'required|alpha_dash',
            'tg_chatid'     => 'required|numeric'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        try {
            $this->clientManager->findByTgChatId($this->request->input('tg_chatid'));

            return response()->json($this->apiResponse->fail(['message' => 'Client is already registered']), 400);
        } catch (ModelNotFoundException $e) {

        }

        $client = Client::create($this->request->only('name', 'tg_username', 'tg_chatid') + ['comment' => $this->request->input('tg_username')]);

        return response()->json($this->apiResponse->ok(['client_id' => $client->id]), 201);
    }

    public function getPurse()
    {
        return response()->json($this->apiResponse->ok([
            'data' => ['phone' => Purse::whereSelected(1)->first()->phone]
        ]));
    }
}