'use strict'

/**
 * Start menu
 */

const BaseMenu = require('./BaseMenu')

class StartMenu extends BaseMenu {

    run() {

        /*let cities = {
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
        }*/

        let menuCreator = (data) => {
            let menu = {
                layout: 2, //some layouting here
                method: 'sendMessage', //here you must pass the method name
                params: [data.message, {'parse_mode': 'markdown'}], //here you must pass the parameters for that method
                menu: [
                    {
                        text: '📄 ПРАЙС', //text of the button
                        callback: () => {

                        }
                    },
                    {
                        text: '📜 Личный кабинет'
                    },
                    {
                        text: '🔋 Корзина',
                    },
                    {
                        text: '🔄 Обновить',
                        callback: (continueQuery, message) => {

                        }
                    }
                ]
            }

            return menu
        }

        return this.app.render('main.start_message').then(content => {
            return this.botScope.runInlineMenu(menuCreator({message: content}))
        }).catch(console.error)
    }
}

module.exports = StartMenu