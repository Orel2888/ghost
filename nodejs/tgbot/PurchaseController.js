'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const config = require('../config');
const GhostApi = require('../ghost-api/GhostApi');
const emoji = require('node-emoji');

class PurchaseController extends TelegramBaseController {

    constructor() {
        super();

    }

    handle($) {
        console.log('LOG');
    }

    get routes() {
        return {
            '/buy': 'handle'
        }
    }
}

module.exports = PurchaseController;