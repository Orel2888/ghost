<?php

namespace App\Ghost\Api\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\{
    Client,
    Purse
};
use Validator;

class UsersApiController extends BaseApiController
{
    /**
     * users.find
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFind()
    {
        $valid = Validator::make(app('request')->all(), [
            'tg_chatid' => 'required|numeric'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        try {
            $client = $this->clientManager->findByTgChatId(app('request')->input('tg_chatid'));
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
        $valid = Validator::make(app('request')->all(), [
            'name'          => 'required',
            'tg_username'   => 'required|alpha_dash',
            'tg_chatid'     => 'required|numeric'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        try {
            $this->clientManager->findByTgChatId(app('request')->input('tg_chatid'));

            return response()->json($this->apiResponse->fail(['message' => 'Client is already registered']), 400);
        } catch (ModelNotFoundException $e) {

        }

        $client = Client::create(app('request')->only('name', 'tg_username', 'tg_chatid') + ['comment' => app('request')->input('tg_username')]);

        return response()->json($this->apiResponse->ok(['client_id' => $client->id]), 201);
    }

    public function getPurse()
    {
        return response()->json($this->apiResponse->ok([
            'data' => ['phone' => Purse::whereSelected(1)->first()->phone]
        ]));
    }

    public function postUpdate()
    {
        $valid = Validator::make(app('request')->all(), [
            'name'          => 'required',
            'tg_username'   => 'required',
            'tg_chatid'     => 'required|numeric'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        try {
            $this->clientManager->findByTgChatId(app('request')->input('tg_chatid'))
                ->update(app('request')->only('name', 'tg_username', 'tg_chatid', 'comment'));

            return response()->json($this->apiResponse->ok());
        } catch (ModelNotFoundException $e) {
            return response()->json($this->apiResponse->fail(['message' => 'User with id '. app('request')->input('tg_chatid') .' not found']), 400);
        }
    }
}