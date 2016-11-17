<?php

namespace App\Services\Tgbot;

use Illuminate\Support\ServiceProvider;
use Longman\TelegramBot\Telegram;

class TgbotServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('tgbot', new Telegram(config('shop.TGBOT_TOKEN'), config('shop.TGBOT_NAME')));
    }
}