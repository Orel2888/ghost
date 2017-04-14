
const BaseMenu = require('./BaseMenu')
const Product = require('../models/Product')

/**
 * OrderMenu
 * Incoming params
 *  this.params.prev_message
 *  this.params.type_order = pending | successful
 */
class OrdersMenu extends BaseMenu {

    constructor(app, menuName, botScope, params) {
        super(...arguments)

        this.product = new Product(this.app, this.botScope)
        this.orderIdsIndex = new Map()
    }

    run() {

        let tplData = {
            formatWeight: this.product.weightForHumans,
            type_order: this.params.type_order
        }

        return this.getOrders(this.botScope.user.userId, this.params.type_order).then(data => {

            data.orders.forEach((order, index) => {
                this.orderIdsIndex.set(++index, order.id)
            })

            // var data {orders: [], count: integer}
            tplData = Object.assign(tplData, data)

            return this.getMessage(tplData).then(content => {
                return this.botScope.runInlineMenu(
                    this.makeMenu({message: content, count_order: tplData.count}),
                    this.params.prev_message)
            }).catch(err => this.app.logger.error({order_menu_run: err}))
        })
    }

    makeMenu(data) {

        let buttons = []

        let parseNumIndex = (text) => {

            if (text.test(/(\d+,*)/)) {
                return text.split(',').map(item => parseInt(item))
            }

            return +text
        }

        // Actions on orders
        if (data.count_order) {
            buttons.push({
                text: '❌ Удалить',
                callback: (callbackQuery, message) => {

                    this.botScope.sendMessage(
                        'Отправьте в ответ порядковый номер заказа, который вы хотите удалить. Если вам нужно удалить' +
                        ' несколько заказов одновременно, отправляйте порядковые номера через запятую, пример 1,2,3'
                    )

                    this.botScope.waitForRequest.then($ => {
                        let mess = parseNumIndex($.message.text)

                        if (mess) {
                            if (typeof mess == 'object' && this.orderIdsIndex.keys()) {
                                return
                            }
                            return this.botScope.sendMessage('Выбрано ' + this.orderIdsIndex.get(mess))
                        } else {console.log('No pass')
                            return this.run()
                        }
                    })

                }
            })
            buttons.push({
                text: '❌ Удалить все',
                callback: (callbackQuery, message) => {

                }
            })
            buttons.push({
                text: '✉️ выслать',
                callback: (callbackQuery, message) => {

                }
            })
        }

        buttons.push(this._commonButtons.orders(this.params.type_order == 'pending' ? 'successful' : 'pending'))
        buttons.push(this._commonButtons.start())

        let menuScheme = {
            layout: [3, 2],
            params: [{parse_mode: 'markdown'}],
            message: data.message,
            menu: buttons
        }

        return menuScheme
    }

    getMessage(tplData) {

        let tplFile = tplData.type_order == 'pending' ? 'orderslist_pending' : 'orderslist_complete'

        return this.app.render(`orders.${tplFile}`, tplData)
            .catch(err => this.app.logger.error({order_menu_render_index: err}))
    }

    getOrders(clientId, status = 'pending') {
        return this.app.api.api('order.list', 'GET', {
            client_id: clientId,
            status
        }).then(response => {
            return {
                orders: response.data,
                count: response.count
            }
        }).catch(err => this.app.logger.error({order_menu_get_orders: err}))
    }
}

module.exports = OrdersMenu