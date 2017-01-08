/**
 * Telegram bot shop
 *
 **/

'use strict'

const Telegram = require('telegram-node-bot')
const TelegramBaseController = Telegram.TelegramBaseController
const TextCommand = Telegram.TextCommand
const path = require('path')
const config = require('dotenv').config({path: require('path').join(__dirname, '../../.env')})

// Initialization App
const App = new (require('./App'))({
    config: Object.assign(require('./config'), config),
    mapControllers: [
        'MainController'
    ]
})

// Initialization bot
const tg = new Telegram.Telegram(config.TGBOT_TOKEN)

// Before
tg.before((update, cb) => {

    console.log(update);
    cb(true)
})

// Routes
tg.router
    .when(new TextCommand('start', 'startCommand'), App.getController('MainController'))
    .otherwise(new class OtherwiseController extends TelegramBaseController {
        // For any messages run main controller startHandler
        handle($) {
            return controllers.get('main').startHandler($);
        }
    })