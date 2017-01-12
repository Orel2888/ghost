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
    // Bot controllers
    mapControllers: [
        'MainController'
    ]
})

// Initialization bot
const tg = new Telegram.Telegram(config.TGBOT_TOKEN)

// Send message a user about required username
let messageUsernameNotRegistered = userId => {
    return App.render('other.error_username').then(content => {
        return tg.api.sendMessage(userId, content, {parse_mode: 'markdown'})
    })
}

// Before
tg.before((update, cb) => {

    let telegramUsername = update.message.from
        ? update.message.from.username
        : (update.callbackQuery ? update.callbackQuery.from.username : null)

    let telegramUserId = update.message.from
        ? update.message.from.id
        : (update.callbackQuery ? update.callbackQuery.from.id : null)

    // Check username
    if (!telegramUsername) {
        messageUsernameNotRegistered(telegramUserId)

        return cb(false)
    }

    let isAdmin = App.getAdminUsernames().includes(telegramUsername);

    // Authentication
    App.api.checkAuth(isAdmin).then(auth => {
        console.log(auth)
        if (!auth) {
            return isAdmin ? App.api.authenticationAdmin(telegramUsername) : App.api.accessTokenUser()
        }

        return true
    }).then(respone => tg.logger.log({respone_auth: respone})).catch(err => {
        tg.logger.error({error_authentication_api: err})

        cb(false)
    })

    return cb(true)
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