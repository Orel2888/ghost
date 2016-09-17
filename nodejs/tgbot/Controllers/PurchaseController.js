'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const emoji = require('node-emoji');
const UserScope = require('../UserScope');
const Tools = require('../Tools');

let emojify = emoji.emojify;

class PurchaseController extends TelegramBaseController {

    constructor(Container) {
        super();

        this.container = Container;
        this.powers    = this.container.make('Powers');
        this.ghostApi  = this.powers.ghostApi;
        this.userScope = this.container.make('UserScope');
        this.tools = new Tools();
    }

    handle() {

    }

    handleBeforeBuy($) {

        let getGoodsIdAndWeight = () => {
            const [, goodsId, weight] = $._message._text.match(/\/buy([0-9]*)_([0-9]*)/i);

            return [goodsId, weight];
        };

        let sendMessage = () => {
            var itemsCount = '';

            for (let i = 1; i<=6; i++) {
                itemsCount += `${emojify(':package:')} ${i} ${emojify(':point_right:')} ${$._message._text}_${i}\n`;
                itemsCount += `${'-'.repeat(25)}\n`;
            }

            let data = {
                nvg: ['myprofile', 'in start'],
                items_count: itemsCount
            };

            return this.tools.render('main/before_buy', data).then(content => {
                return $.sendMessage(content);
            }).catch(err => {
                console.log(err);
                return $.sendMessage('Произошла ошибка');
            });
        };

        // Start running
        const [goodsId, weight] = getGoodsIdAndWeight();

        return sendMessage();
    }

    handleBuy($) {

        let chunksCommand = $._message._text.split('_');
        let prevCommand   = [chunksCommand[0], chunksCommand[1]].join('_').replace('_', '\\_');

        let getGoodsIdAndWeightAndCount = () => {
            const [, goodsId, weight, count] = $._message._text.match(/\/buy([0-9]*)_([0-9]*)_([0-9]*)/i);

            return [goodsId, weight, count];
        };

        let orderCreate = (sendData) => {
            return this.ghostApi.api('order.create', 'POST', sendData).then(response => {

                var data = {};

                if (response.status == 'fail') {
                    data.status  = 'fail';
                    data.message = response.message;
                    data.nvg     = [
                        `К выбору количества ${emojify(':point_right:')} ${prevCommand}`,
                        'in start'
                    ];
                } else {
                    data.status = 'ok';
                    data.data_api = response.data;
                    data.nvg = [
                        `К выбору количества ${emojify(':point_right:')} ${prevCommand}`,
                        'in start'
                    ];
                    data.comment = $.userSession.udata.comment;
                }

                return this.tools.render('main/buy', data).then(content => {
                    return $.sendMessage(content, {parse_mode: 'markdown'});
                }).catch(err => {
                    console.log(err);
                    return $.sendMessage('Произошла ошибка');
                });
            });
        };

        // Start running
        var [goodsId, weight, count] = getGoodsIdAndWeightAndCount();

        weight = String(weight);

        if (weight[0] == 0) {
            weight = `0.${weight.substring(1)}`;
        }

        if ($.userSession.udata) {
            return orderCreate({
                goods_id: goodsId,
                weight,
                count,
                client_id: $.userSession.udata.id
            })
        } else {
            return this.userScope.findUser($._message._from._id, $).then(udata => {
                $.userSession.udata = udata;

                return orderCreate({
                    goods_id: goodsId,
                    weight,
                    count,
                    client_id: $.userSession.udata.id
                });
            })
        }
    }

    get routes() {
        return {
            '/\/buy[0-9]*_[0-9]*/gi': 'handleBeforeBuy',
            '/\/buy[0-9]*_[0-9]*_[0-9]*/gi': 'handleBuy'
        }
    }
}

module.exports = PurchaseController;