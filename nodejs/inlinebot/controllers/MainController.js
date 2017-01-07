/**
 * Main controller
 **/

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;

class MainController extends TelegramBaseController {

    startHandler($) {

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
        };

        $.runInlineMenu();
    }

    get routes() {
        return {
            'startCommand': 'startHandler'
        };
    }
}

module.exports = MainController;