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

        return this.app.render('main.start_message').then(content => {
            return this.botScope.runInlineMenu(this.makeMenu({message: content}))
        }).catch(err => this.app.logger.error(err))
    }

    makeMenu(data) {
        let menu = {
            layout: 2, //some layouting here
            method: 'sendMessage', //here you must pass the method name
            params: [data.message, {'parse_mode': 'markdown', 'disable_web_page_preview': true}], //here you must pass the parameters for that method
            menu: [
                {
                    text: '📄 ПРАЙС', //text of the button
                    callback: () => {
                        this.app.includeMenu('Showcase', $).run()
                    }
                },
                {
                    text: '📜 Личный кабинет',
                    callback: () => {
                        return this.app.includeMenu('UserCabinet').run()
                    }
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
}

module.exports = StartMenu