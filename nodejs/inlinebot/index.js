/**
 * Telegram bot shop
 *
 **/

'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const TextCommand = Telegram.TextCommand;
const path = require('path');
const config = require('dotenv').config({path: require('path').join(__dirname, '../../.env')});

// Initialization bot
const tg = new Telegram.Telegram(config.TGBOT_TOKEN);

// Before
tg.before((update, cb) => {

});

// Controllers map
const controllers = new Map()
    .set('main', require('./controllers/MainController'));

// Routes
tg.router
    .when(new TextCommand('start', 'startCommand'), new (controllers.get('main')))
    .otherwise(new class OtherwiseController extends TelegramBaseController {
        // For any messages run main controller startHandler
        handle($) {
            let contr = new (controllers.get('main'));

            return contr.startHandler($);
        }
    });