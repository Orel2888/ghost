
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

            return this.getMessageOrderList(tplData).then(content => {
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

                        let messageAnswer = $.message.text
                        let ids = this.parseIdsFromMessage($.message.text)

                        if (this.checkMessageOrderIds(ids)) {

                            let ordersIds = ids.map(id => this.orderIdsIndex.get(id))

                            let messageRemoveConfirm = 'Подтвердите удаление ' +
                                (ordersIds.length == 1 ? 'заказа' : 'заказов') + ': ' + messageAnswer

                            return this.menuConfirmation(messageRemoveConfirm,
                                (callbackQuery, message) => {
                                    this.params.prev_message = message

                                    return this.removeOrders(ids)
                                },
                                (callbackQuery, message) => {
                                    this.params.prev_message = message

                                    return this.run()
                                }
                            )

                        } else {
                            this.menuFailSelectOrder()
                        }
                    })
                }
            })

            // Remove all a orders
            buttons.push({
                text: '❌ Удалить все',
                message: 'Подтвердите удаление всех '+ (this.params.type_order == 'pending' ? 'заказов' : 'покупок'),
                menu: [
                    {
                        text: 'Удалить все ' + (this.params.type_order == 'pending' ? 'заказы' : 'покупки'),
                        callback: () => {
                            return this.removeAllOrders(this.params.type_order)
                        }
                    },
                    {
                        text: 'Отмена',
                        callback: (callbackQuery, message) => {
                            this.params.prev_message = message

                            return this.run()
                        }
                    }
                ]
            })

            // Send in answer info about a order
            buttons.push({
                text: '✉️ выслать',
                callback: (callbackQuery, message) => {

                    this.botScope.sendMessage(
                        'Отправьте в ответ порядковый номера заказа, который нужно выслать. Если вам нужно выслать ' +
                        'несколько заказов одновременно, отправляйте порядковые номера через запятую, пример 1,2,3. ' +
                        '_Бот вышлет сообщения с заказами, это требуется для отделение нужных заказов и перессылке_',
                        {parse_mode: 'markdown'}
                    )

                    return this.botScope.waitForRequest.then($ => {

                        let message = $.message.text
                        let ids     = this.parseIdsFromMessage(message)

                        if (this.checkMessageOrderIds(ids)) {

                            let ordersIds = ids.map(id => this.orderIdsIndex.get(id))

                            return this.sendOrders(ordersIds)
                        } else {
                            this.menuFailSelectOrder()
                        }
                    })
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

    /**
     * Send order to client
     * @param ids
     * @returns {Promise.<T>}
     */
    sendOrders(ids) {
        return this.findOrders(ids).then(orders => {
            return this.app.render('orders.order_sending', {orders: orders}).then(content => {
                return this.botScope.sendMessage(content, {parse_mode: 'markdown'})
            })
        }).catch(err => {
            this.botScope.sendMessage('Произошла ошибка при высылании заказов')

            return this.app.logger.error({orders_menu_button_send_orders: err})
        })
    }

    /**
     * Remove a selected orders
     * @param ids
     * @returns {Promise.<T>}
     */
    removeOrders(ids) {
        return this.apiRemoveOrders(ids).then((res) => {
            return this.run()
        }).catch(err => {

            this.app.logger.error({order_menu_remove_orders: err})

            return this.botScope.sendMessage('Произошла ошибка при удалении заказов')
        })
    }

    removeAllOrders(type_order) {

    }

    /**
     * Fail a message about incorrect format message for select order
     * @returns {*}
     */
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

    /**
     * Menu confirmation remove a orders
     * @param messageText
     * @param cbConfirmPassed
     * @param cbConfirmNotPassed
     * @returns {*}
     */
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

    /**
     * Request to api for delete a orders
     * @param ids
     * @returns {Promise.<*>}
     */
    apiRemoveOrders(ids = []) {

        let removingOrders = ids.map((id => {
            return new Promise((resolve, reject) => {
                return this.app.api.api('order.del', 'POST', {
                    client_id: this.botScope.user.userId,
                    order_id: id
                }).then(response => {
                    if (response.status == 'ok') resolve(response)
                    else
                        reject(response)
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

        return text > 0 ? [text] : false
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

    /**
     * Get render message with list a orders
     * @param tplData
     * @returns {Promise.<T>|*}
     */
    getMessageOrderList(tplData) {

        let tplFile = tplData.type_order == 'pending' ? 'orderslist_pending' : 'orderslist_complete'

        return this.app.render(`orders.${tplFile}`, tplData)
            .catch(err => this.app.logger.error({order_menu_render_index: err}))
    }

    /**
     * Request to api for get orders to user
     * @param clientId
     * @param status
     * @returns {Promise.<T>}
     */
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

    /**
     * Request to api for get info a orders
     * @param ids
     * @returns {Promise.<TResult>}
     */
    findOrders(ids = []) {

        let requestsFindOrder = ids.map(id => {
            return new Promise((resolve, reject) => {
                return this.app.api.api('order.find', 'GET', {id, client_id: this.botScope.user.userId})
                    .then(response => {
                        if (response.status == 'ok') resolve(response)
                        else
                            reject(response)
                    })
            })
        })

        return Promise.all(requestsFindOrder).then(results => {
            return results.map(item => item.data)
        })
    }
}

module.exports = OrdersMenu