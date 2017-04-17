'use strict'

/**
 * Base menu
 */

class BaseMenu {

    constructor(app, menu, botScope, params) {
        this.app      = app
        this.menu     = menu
        this.botScope = botScope
        this.params   = params || {}

        let prevMessage = this.params.prev_message ? {prev_message: this.params.prev_message} : null

        this._commonButtons = {
            start: (params) => {
                return {
                    text: '❰ Главная',
                    callback: (callbackQuery, message) => {
                        return this.app.includeMenu('Start', this.botScope, {prev_message: message}).run()
                    }
                }
            },
            showcase: (params) => {
                return {
                    text: '📄 ПРАЙС',
                    callback: (callbackQuery, message) => {
                        this.app.includeMenu('Showcase', this.botScope, prevMessage || {prev_message: message}).run()
                    }
                }
            },
            lk: (params) => {
                return {
                    text: '📜 Личный кабинет',
                    callback: (callbackQuery, message) => {
                        return this.app.includeMenu('UserCabinet', this.botScope, prevMessage || {prev_message: message}).run()
                    }
                }
            },
            orders: (type_order = 'pending') => {

                let textButton = type_order == 'pending' ? '🔋 Корзина' : '🎁 Покупки'

                return {
                    text: textButton,
                    callback: (callbackQuery, message) => {
                        return this.app.includeMenu('Orders', this.botScope, {
                            prev_message: message,
                            type_order
                        }).run()
                    }
                }
            },
            shopping_cart: (params) => {
                return {
                    text: '🔋 Корзина',
                    callback: (callbackQuery, message) => {
                        return this.app.includeMenu('ShoppingCart', this.botScope, prevMessage || {prev_message: message}).run()
                    }
                }
            },
            purchases: (params) => {
                return {
                    text: '🎁 Покупки',
                    callback: (callbackQuery, message) => {
                        return this.app.includeMenu('Purchases', this.botScope, prevMessage || {prev_message: message}).run()
                    }
                }
            },
            payment: (params) => {
                return {
                    text: '💵 Оплата',
                    callback: (callbackQuery, message) => {
                        return this.app.includeMenu('Payment', this.botScope, prevMessage || {prev_message: message}).run()
                    }
                }
            },
            help: () => {
                return {
                    text: '📙 Помощь',
                    callback: (callbackQuery, message) => {
                        return this.app.includeMenu('Help', this.botScope, {prev_message: message}).run()
                    }
                }
            }
        }
    }

    run() {
        throw new Error('Not implemented')
    }

    /**
     * No tested
     * @param requiredParams
     */
    canRequiredParams(requiredParams) {
        for (let paramName in requiredParams) {

            if (!this.params.hasOwnProperty(paramName) || !(this.params[paramName] instanceof requiredParams[paramName])) {
                let error = `Not required param ${paramName}`

                this.app.logger.error({BaseMenu: error})

                throw new Error(error)
            }
        }
    }

    /**
     * Create layout two column and end one
     * @param countItems
     * @param nextLayout
     * @param nextNums
     * @returns {Array}
     */
    layoutTwoColumns(countItems, nextLayout, ...nextNums) {
        let layout = []
        let i = 0;

        while (i < Math.floor(countItems / 2)) {
            layout.push(2)
            i++
        }

        if (countItems % 2 > 0) layout.push(1)

        if (typeof nextLayout == 'object' && nextLayout.length) {
            nextLayout.forEach(num => layout.push(num))
        } else {
            layout.push(nextLayout)
        }

        if (nextNums.length) nextNums.forEach(num => layout.push(num))

        return layout
    }
}

module.exports = BaseMenu