
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
                    this.params.prev_message
                )
            }).catch(err => this.app.logger.error({order_menu_run: err}))
        })
    }

    makeMenu(data) {

        let buttons = []

        // Actions on orders
        if (data.count_order) {
            // Remove orders by select
            buttons.push({
                text: '❌ Удалить',
                callback: (callbackQuery, message) => {

                    // Help message to select order
                    this.botScope.sendMessage(
                        'Отправьте в ответ порядковый номер заказа, который вы хотите удалить. Если вам нужно удалить' +
                        ' несколько заказов одновременно, отправляйте порядковые номера через запятую, пример 1,2,3'
                    )

                    // Waiting a answer with orders ids
                    this.botScope.waitForRequest.then($ => {

                        let ids = this.parseIdsFromMessage($.message.text)

                        if (this.checkMessageOrderIds(ids)) {

                            if (typeof ids == 'object') {
                                let ordersIds = ids.map(id => this.orderIdsIndex.get(id))

                                return this.menuConfirmation(`Подтвердите удаление заказов: ${$.message.text}`,
                                    (callbackQuery, message) => {
                                        this.params.prev_message = message

                                        return this.apiRemoveOrders(ordersIds).then((res) => {
                                            return this.run()
                                        }).catch(err => {

                                            this.app.logger.error({order_menu_remove_order: err})

                                            return this.botScope.sendMessage('Произошла ошибка при удалении заказов')
                                        })
                                    },
                                    (callbackQuery, message) => {
                                        this.params.prev_message = message

                                        return this.run()
                                    }
                                )
                            } else {
                                let orderId = this.orderIdsIndex.get(ids)

                                return this.menuConfirmation(`Подтвердите удаление заказа: ${$.message.text}`,
                                    (callbackQuery, message) => {
                                        this.params.prev_message = message

                                        return this.apiRemoveOrders([orderId]).then(res => {
                                            return this.run()
                                        }).catch(err => {

                                            this.app.logger.error({order_menu_remove_order: err})

                                            return this.botScope.sendMessage('Произошла ошибка при удалении заказов')
                                        })
                                    },
                                    (callbackQuery, message) => {
                                        this.params.prev_message = message

                                        return this.run()
                                    }
                                )
                            }
                        } else {
                            this.menuFailSelectOrder()
                        }
                    })
                }
            })

            // Remove all a orders
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

    menuFailSelectOrder() {
        let message = 'Ошибка, неверный формат ответа или порядковые номера'

        let menu = [
            this._commonButtons.orders(this.params.type_order),
            this._commonButtons.start()
        ]

        return this.botScope.runInlineMenu({
            layout: 2,
            method: 'sendMessage',
            params: [message, {parse_mode: 'markdown'}],
            menu: menu
        })
    }

    menuConfirmation(messageText, cbConfirmPassed, cbConfirmNotPassed) {

        let menu = [
            {
                text: 'Да, удалить',
                callback: cbConfirmPassed
            },
            {
                text: 'Отмена',
                callback: cbConfirmNotPassed
            },
            this._commonButtons.orders(this.params.type_order),
            this._commonButtons.start()
        ]

        return this.botScope.runInlineMenu({
            layout: 2,
            method: 'sendMessage',
            params: [messageText, {parse_mode: 'markdown'}],
            menu: menu
        })
    }

    apiRemoveOrders(ids = []) {

        let removingOrders = ids.map((id => {
            return new Promise((resolve, reject) => {
                return this.app.api.api('order.del', 'POST', {
                    client_id: this.botScope.user.userId,
                    order_id: id
                }).then(response => {
                    if (response.status == 'ok') resolve(response)
                    else
                        reject()
                })
            })
        }))

        return Promise.all(removingOrders)
    }

    /**
     * Parse ids from message id,id,id,id
     * @param text
     * @returns {Array.<*> | integer | boolean}
     */
    parseIdsFromMessage(text) {

        if (/(\d+,\d+)/.test(text)) {
            return text.split(',').map(item => parseInt(item)).filter(item => item != '')
        }

        text = +text

        return text > 0 ? text : false
    }

    /**
     * Check to exists in this.orderIdsIndex
     * @param ids
     * @returns {boolean}
     */
    checkMessageOrderIds(ids) {
        return typeof ids == 'object'
            ? ids.every(id => this.orderIdsIndex.has(id))
            : this.orderIdsIndex.has(ids)
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