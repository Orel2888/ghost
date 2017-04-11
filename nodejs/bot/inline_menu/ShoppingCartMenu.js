
const BaseMenu = require('./BaseMenu')
const Product = require('../models/Product')

class ShoppingCartMenu extends BaseMenu {

    constructor() {
        super(...arguments)

        this.product = new Product(this.app, this.botScope)
    }

    run() {

        let tplData = {
            formatWeight: this.product.weightForHumans
        }

        return this.getOrders(this.botScope.user.userId).then(data => {

            tplData = Object.assign(tplData, data)

            return this.getMessage(tplData).then(content => {
                return this.botScope.runInlineMenu(this.makeMenu({message: content}), this.params.prev_message)
            }).catch(err => this.app.logger.error({shopping_cart_run: err}))
        })
    }

    makeMenu(data) {

        let buttons = []

        let menuScheme = {
            layout: [3, 2],
            params: [{parse_mode: 'markdown'}],
            message: data.message,
            menu: buttons
        }

        return menuScheme
    }

    getMessage(tplData) {
        return this.app.render('shoppingcart.index', tplData)
            .catch(err => this.app.logger.error({shopping_cart_render_index: err}))
    }

    getOrders(clientId) {
        return this.app.api.api('order.list', 'GET', {client_id: clientId, status: 'pending'}).then(response => {
            return {
                orders: response.data,
                count: response.count
            }
        }).catch(err => this.app.logger.error({shopping_cart_get_orders: err}))
    }
}

module.exports = ShoppingCartMenu