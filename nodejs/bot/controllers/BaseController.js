'use strict'

/**
 * Base controller
 */

const Telegram = require('telegram-node-bot')
const TelegramBaseController = Telegram.TelegramBaseController
const User = require('../models/User')

class BaseController extends TelegramBaseController {

    constructor(app) {
        super()

        this.app = app
    }

    before(scope) {
        scope.user = new User(this.app, scope)

        return scope.user.load()
            .then(udata => scope)
            .catch(err => {
                this.app.logger.error(err)

                scope.sendMessage('Произошла ошибка')

                return false
            })
    }
}

module.exports = BaseController