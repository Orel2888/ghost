'use strict'

/**
 * Base controller
 */

const Telegram = require('telegram-node-bot')
const TelegramBaseController = Telegram.TelegramBaseController
const path = require('path')

class BaseController extends TelegramBaseController {

    constructor(app) {
        super()

        this.app = app;
    }

    runMenu(menuName, $, params) {
        const className    = menuName.split('').map((s, index) => index == 0 ? s.toUpperCase() : s).join('') + 'Menu';
        const pathLoadMenu = '../' + this.app.config.bot_mode +'/'+ className;

        try {
            var Menu = require(pathLoadMenu)
        } catch (e) {
            throw new Error(`Error load menu ${menu}`)

            return
        }

        return new Menu(this.app, menuName, $, params)
    }
}

module.exports = BaseController;