'use strict'

/**
 * Base controller
 */

const Telegram = require('telegram-node-bot')
const TelegramBaseController = Telegram.TelegramBaseController
const User = require('../Models/User')

class BaseController extends TelegramBaseController {

    constructor(app) {
        super()

        this.app = app
    }

    before(scope) {
        scope.user = new User(this.app, scope)

        return scope.user.load().then(udata => scope)
    }
}

module.exports = BaseController