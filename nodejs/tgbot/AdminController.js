'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const config = require('../config');
const GhostApi = require('../ghost-api/GhostApi');

class AdminController extends TelegramBaseController {

    constructor() {
        super();

        if (!this.ghostApi) {
            this.ghostApi = new GhostApi({
                apiKey: config.get('API_KEY')
            });
        }
    }

    transHandle($) {

        let responseQiwiTransaction = () => {
            return this.ghostApi.api('admin/qiwi-transaction', 'GET').then(response => {
                var message = 'Список последних 10-ти qiwi транзакций\n\n';

                response.data.forEach((item, index) => {
                    let provider = item.provider.trim().replace(/[\s]{2,}/g, ' ');

                    message += `${++index}) Qiwi ID: ${item.qiwi_id}\n`;
                    message += `Провайдер: ${provider}\n`;
                    message += `Коммент: ${item.comment ? item.comment : 'нету'}\n`;
                    message += `Сумма: ${item.amount}\n`;
                    message += `Дата: ${item.qiwi_date}\n`;
                    message += `${'-'.repeat(item.qiwi_date.length * 2)}\n`;
                });

                $.sendMessage(message);
            });
        };

        if (!this.ghostApi.accessTokenAdmin) {
            this.ghostApi.authenticationAdmin($._message._from._username).then(response => {
                responseQiwiTransaction();
            });
        } else {
            responseQiwiTransaction();
        }
    }

    get routes() {
        return {
            'транс': 'transHandle'
        };
    }
}

module.exports = AdminController;