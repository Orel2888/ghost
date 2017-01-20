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
const app = require('./App')

// Initialization App
const App = new app({
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

    // Get username from update
    let telegramUsername = update.message
        ? update.message.from.username
        : (update.callbackQuery ? update.callbackQuery.from.username : null)

    // Get user id from update
    let telegramUserId = update.message
        ? update.message.from.id
        : (update.callbackQuery ? update.callbackQuery.from.id : null)

    // Check username
    if (!telegramUsername) {
        messageUsernameNotRegistered(telegramUserId)

        return cb(false)
    }

    // Check username for admin
    let isAdmin = App.getAdminUsernames().includes(telegramUsername);

    // Authentication and getting user data
    App.api.checkAuth(isAdmin).then(auth => {

        if (!auth) {
            return isAdmin
                ? App.api.authenticationUser().then(authData => App.api.authenticationAdmin(telegramUsername))
                : App.api.authenticationUser()
        }

        return true
    }).then(authData => {
        return cb(true)
    }).catch(err => {
        tg.logger.error({error_authentication_api: err})

        return cb(false)
    })

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