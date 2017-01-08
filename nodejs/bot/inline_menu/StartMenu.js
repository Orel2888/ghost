'use strict'

/**
 * Start menu
 */

const BaseMenu = require('./BaseMenu')

class StartMenu extends BaseMenu {

    run() {

        let cities = {
            1: 'City1',
            2: 'City2',
            3: 'City3'
        }

        let goods = {
            'City1': [{
                id: 1,
                name: 'Ananas',
                count: 2
            }],
            'City2': [{
                id: 2,
                name: 'Banan',
                count: 1
            }]
        }

        let productWeights = {
            1: [{
                weight: 0.5,
                cost: 1500,
                count: 2
            },
            {
                weight: 1,
                cost: 2500,
                count: 4
            }]
        }

        let homeMessage = '❄️ Добро пожаловать в круглосуточный магазин 👀 GHOST.';

        let menu = {
            layout: 2, //some layouting here
            method: 'sendMessage', //here you must pass the method name
            params: [homeMessage, {'parse_mode': 'markdown'}], //here you must pass the parameters for that method
            menu: [
                {
                    text: '📄 ПРАЙС', //text of the button
                    message: '*Выберите город:*',
                    options: 'markdown',
                    layout: 2,
                    menu: [
                        {
                            text: '🏡 Светлоград',
                            callback: () => {

                            }
                        },
                        {
                            text: '🏡 Буденновск',
                            callback: () => {

                            }
                        }
                    ]
                },
                {
                    text: '📜 Лобби'
                },
                {
                    text: '🔋 Корзина',
                    message: 'Are you sure?',
                    layout: 2,
                    menu: [ //Sub menu (current message will be edited)
                        {
                            text: 'Yes!',
                            callback: () => {

                            }
                        },
                        {
                            text: 'No!',
                            callback: () => {

                            }
                        }
                    ]
                },
                {
                    text: '🔄 Обновить',
                    callback: (continueQuery, message) => {

                    }
                },
                {
                    text: '🅰 Админ',
                    callback: (continueQuery, message) => {

                    }
                }
            ]
        }

        return this.botScope.runInlineMenu(menu)
    }
}

module.exports = StartMenu