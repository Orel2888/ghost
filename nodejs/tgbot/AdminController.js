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
        }).catch(console.log)
    }

    goodsPriceHandle($) {

        let responseGoodsPrice = () => {
            return this.ghostApi.api('admin/goods-price').then(response => {

                if (!Object.keys(response.data).length) {
                    return $.sendMessage('Товара нету');
                }

                var goodsSession = new Map();
                var goodsIndex = 0;

                var message = 'Список товаров по городам\n\n';

                for (let city in response.data) {
                    message += `🏡 ${city}\n`;

                    for (let goodsName in response.data[city]) {
                        message += `  🍗 ${goodsName}\n`;

                        let goodsWeights = Object.keys(response.data[city][goodsName]);

                        goodsWeights.forEach((goodsWeight, index) => {
                            message += `  ⚖ ${goodsWeight}\n`;

                            let goodsItems = response.data[city][goodsName][goodsWeight];

                            goodsItems.forEach((goods, index) => {
                                message += `${++goodsIndex}) ${parseFloat(goods.weight).toFixed(1)}, ${goods.address}\n`;

                                goodsSession.set(goodsIndex, goods.id);
                            })
                        })

                    }
                }

                $.userSession.goodsPrice = goodsSession;

                $.sendMessage(message);
            })
        };

        $.checkAuth.then(auth => {

            if (!auth) return;

            responseGoodsPrice();

        }).catch(console.log)
    }

    goodsPurchase($) {

        let parchase = () => {

            let goodsPriceIds = $.query.arg1.split(',').map((item, index) => {
                return $.userSession.goodsPrice.get(parseInt(item));
            });

            return this.ghostApi.api('admin/goods-price/purchase', 'POST', {
                goods_price_id: goodsPriceIds.join(',')
            }).then(response => {
                response.data.forEach((item, index) => {
                    let message = `${++index}) ⚖ ${parseFloat(item.weight).toFixed(1)}, ${item.address}`;

                    $.sendMessage(message);
                })
            });
        };

        $.checkAuth.then(auth => {

            if (!auth) return;

            parchase()
        })
    }

    get routes() {
        return {
            '/транс': 'transHandle',
            '/товар': 'goodsPriceHandle',
            '/товар взять :arg1': 'goodsPurchase'
        };
    }
}

module.exports = AdminController;