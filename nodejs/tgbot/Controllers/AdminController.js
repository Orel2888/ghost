'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const emoji = require('node-emoji');

class AdminController extends TelegramBaseController {

    constructor(Powers) {
        super();

        this.powers   = Powers;
        this.ghostApi = this.powers.ghostApi;
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

                if (!response.data) {
                    message += 'Нету транзакций';
                }

                $.sendMessage(message);
            });
        };

        return responseQiwiTransaction();
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
                                message += `${++goodsIndex}) ${goods.weight}, ${goods.address}\n`;

                                goodsSession.set(goodsIndex, goods.id);
                            })
                        })

                    }
                }

                $.userSession.goodsPrice = goodsSession;

                $.sendMessage(message);
            })
        };

        return responseGoodsPrice();
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
                    let message = `${++index}) ⚖ ${item.weight}, ${item.address}`;

                    $.sendMessage(message)
                })
            });
        };

        return parchase();
    }

    goodsPriceAvailableHandle($) {

        let responseGoodsAvailable = () => {
            this.ghostApi.api('admin/goods-price/available').then(response => {

                if (!Object.keys(response.data).length) {
                    return $.sendMessage('В наличии ничего нет');
                }

                var message = 'Товар в наличии\n\n';

                let cities = Object.keys(response.data);

                for (let city of cities) {
                    message += `🏡 ${city}\n`;

                    let goodsTypes = Object.keys(response.data[city]);

                    for (let goodsType of goodsTypes) {
                        message += `  🍗 ${goodsType}\n`;

                        let weights = Object.keys(response.data[city][goodsType]);

                        for (let weight of weights) {
                            message += `  ⚖ ${weight} - ${response.data[city][goodsType][weight]}\n`;
                        }
                    }
                }

                $.sendMessage(message);

            }).catch(console.log)
        };

        return responseGoodsAvailable();
    }

    adminHelpHandle($) {

        let responseHelpMessage = () => {
            let message = 'Помощь по командам\n\n';

            message += '/транс - возвращает список последних 10-ти транзакций по киви кошельку\n';
            message += '/товар - возвращает список товаров по городам\n';
            message += '/товар взять {номер} - возвращает товар по номеру (без скобок), удаляя его из списка товар. Для выбора нескольких товаров, перечислить номера через запятую 1,2,3\n';
            message += '/наличие - возвращает список товара в наличии по городам\n';
            message += '/кош - возвращает список кошельков и баланс, помеченный галочкой, текущий\n';
            message += '/кош выбрать {номер} - установить кошелек как основной для приема средств\n';
            message += '/админ помощь - описание по командам\n';

            $.sendMessage(message);
        };

        return responseHelpMessage();
    }

    purseHandle($) {

        let responsePurseList = () => {
            this.ghostApi.api('admin/purse').then(response => {

                if (!response.data.length) {
                    return $.sendMessage('Нет кошельков');
                }

                var purseSession = new Map();
                var message = 'Список кошельков и баланс\n';

                response.data.forEach((item, index) => {
                    message += `${++index}) ${item.selected == 1 ? emoji.emojify(':white_check_mark:') : ''} ${item.phone} `;
                    message += `${emoji.emojify(':dollar:')} ${item.balance}\n`;

                    purseSession.set(index, item.id);
                });

                $.userSession.purse = purseSession;

                $.sendMessage(message);
            });
        };

        return responsePurseList();
    }

    purseSelectHandle($) {

        let changePurse = () => {
            let id = $.userSession.purse.get(parseInt($.query.arg1));

            if (!id) {
                return $.sendMessage('Неверный выбор');
            }

            return this.ghostApi.api('admin/purse/set', 'POST', {
                id
            }).then(response => {
                return $.sendMessage('Кошелек изменен');
            });
        };

        return changePurse();
    }

    get routes() {
        return {
            '/транс': 'transHandle',
            '/товар': 'goodsPriceHandle',
            '/товар взять :arg1': 'goodsPurchase',
            '/наличие': 'goodsPriceAvailableHandle',
            '/админ помощь': 'adminHelpHandle',
            '/кош': 'purseHandle',
            '/кош выбрать :arg1': 'purseSelectHandle'
        };
    }
}

module.exports = AdminController;