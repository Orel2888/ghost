'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const config = require('../config');
const GhostApi = require('../ghost-api/GhostApi');

class AdminController extends TelegramBaseController {

    constructor() {
        super();

        this.ghostApi = new GhostApi({
            apiKey: config.get('API_KEY')
        });
    }

    before(command, scope) {

        scope.checkAuth = this.ghostApi.checkAuth(true).then(auth => {

            if (!auth) {
                return this.ghostApi.authenticationAdmin(scope._update._message._from._username);
            }

            return true;
        });

        return scope;
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

        $.checkAuth.then(auth => {

            if (!auth) return;

            responseQiwiTransaction();
        });
    }

    get routes() {
        return {
            'транс': 'transHandle'
        };
    }
}

module.exports = AdminController;