'use strict'

/**
 * Start menu
 */

const BaseMenu = require('./BaseMenu')
const Product = require('../models/Product')

class StartMenu extends BaseMenu {

    constructor(app, menuName, botScope, params) {
        super(...arguments)

        this.product = new Product(this.app, this.botScope)
    }

    run() {

        let dataTpl = {
            product: this.product
        }

        return this.product.load().then(() => {
            return this.app.render('main.start_message', dataTpl).then(content => {
                return this.botScope.runInlineMenu(this.makeMenu({message: content}), this.params.prev_message)
            }).catch(err => this.app.logger.error({start_menu: err}))
        }).catch(err => this.app.logger.error({start_menu_product_load: err}))
    }

    makeMenu(data) {

        let menuButtons = [
            this._commonButtons.showcase(this.params),
            this._commonButtons.lk(this.params),
            this._commonButtons.orders('pending'),
            this._commonButtons.orders('successful'),
            this._commonButtons.payment(this.params),
            this._commonButtons.help(),
            {
                text: '🔄 Обновить',
                callback: (continueQuery, message) => {
                    return this.app.includeMenu('Start', this.botScope).run()
                }
            }
        ]

        let menuScheme

        if (!this.params.hasOwnProperty('prev_message')) {
            menuScheme = {
                layout: 2,
                method: 'sendMessage',
                params: [data.message, {parse_mode: 'markdown', disable_web_page_preview: true}],
                menu: menuButtons
            }
        } else {
            menuScheme = {
                layout: 2,
                message: data.message,
                params: [{parse_mode: 'markdown', disable_web_page_preview: true}],
                menu: menuButtons
            }
        }

        return menuScheme
    }
}

module.exports = StartMenu