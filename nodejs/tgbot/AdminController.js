'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;

class AdminController extends TelegramBaseController {

    transHandle($) {
        console.log(Object.keys($), $);
        $.sendMessage('YYYY')
    }

    get routes() {
        return {
            'транс': 'transHandle'
        };
    }
}

module.exports = AdminController;