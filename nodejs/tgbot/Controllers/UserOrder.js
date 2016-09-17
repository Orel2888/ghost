'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const emoji = require('node-emoji');
const UserScope = require('../UserScope');
const Tools = require('../Tools');

let emojify = emoji.emojify;

class UserOrder extends TelegramBaseController {

    constructor(Powers) {
        super()

        this.powers    = Powers;
        this.ghostApi  = this.powers.ghostApi;
        this.userScope = new UserScope(this.powers);
        this.tools     = new Tools();
    }

    handleOrderList($) {

        let clientId = $._message._from._id;

        let messageOrderList = (orders) => {

            let data = {
                nvg: ['refreshMyOrder', 'myprofile', 'in start'],
                orders,
                balance: $.userSession.udata.balance,
                separator: '-'.repeat(25)
            };

            return this.tools.render('user/order', data).then(content => {
                return $.sendMessage(content, {parse_mode: 'markdown'});
            });
        };

        let orderList = (clientId) => {
            return this.ghostApi.api('order.list', 'GET', {
                client_id: clientId
            }).then(response => {
                return messageOrderList(response.data);
            }).catch(err => {
                console.log(err)

                $.sendMessage('Произошла ошибка')
            })
        };

        return this.userScope.findUser(clientId, $).then(udata => orderList(udata.id))
    }

    handleOrderDelConfirm($) {
        let orderId = $._message._text.match(/(\d+)/)[1];

        let message_confirm = 'Подтвердите удаление заказа\n';

        message_confirm += `Удалить ${emojify(':point_right:')} /myorder_delcon_${orderId}\n`;

        let data = {
            message_confirm,
            nvg: ['myorder', 'in start']
        };

        return this.tools.render('confirm', data)
            .then(content => $.sendMessage(content))
            .catch(err => {
                console.log(err);
                return $.sendMessage('Произошла ошибка');
            });
    }

    handleOrderDelete($) {
        let orderId = $._message._text.match(/(\d+)/)[1];

        return this.userScope.findUser($._message._from._id, $).then(udata => {
            return this.ghostApi.api('order.del', 'POST', {
                order_id: orderId,
                client_id: udata.id
            }).then(response => {

                let message_confirm = `${emojify(':white_check_mark:')} Заказ успешно удален\n`;

                let data = {
                    message_confirm,
                    nvg: ['myorder', 'in start']
                };

                return this.tools.render('confirm', data)
                    .then(content => $.sendMessage(content))
                    .catch(err => {
                        console.log(err);
                        return $.sendMessage('Произошла ошибка');
                    });
            }).catch(err => {
                return $.sendMessage('Произошла ошибка при удалении заказа');
            })
        })
    }

    handleOrderDelAll($) {
        let message_confirm = 'Подтвердите удаление всех заказов\n';

        message_confirm += `Удалить ${emojify(':point_right:')} /myorder_delallcon\n`;

        let data = {
            message_confirm,
            nvg: ['myorder', 'in start']
        };

        return this.tools.render('confirm', data)
            .then(content => $.sendMessage(content))
            .catch(err => {
                console.log(err);
                return $.sendMessage('Произошла ошибка');
            });
    }

    handleOrderDeleteAll($) {
        return this.userScope.findUser($._message._from._id, $).then(udata => {
            return this.ghostApi.api('order.delall', 'POST', {
                client_id: udata.id
            }).then(response => {

                let message_confirm = `${emojify(':white_check_mark:')} Заказы успешно удалены\n`;

                let data = {
                    message_confirm,
                    nvg: ['myorder', 'in start']
                };

                return this.tools.render('confirm', data)
                    .then(content => $.sendMessage(content))
                    .catch(err => {
                        console.log(err);
                        return $.sendMessage('Произошла ошибка');
                    })
            }).catch(err => {
                return $.sendMessage('Произошла ошибка при удалении заказов');
            })
        })
    }

    get routes() {
        return {
            '/myorder': 'handleOrderList',
            '/\/myorder_del_[0-9]*/g': 'handleOrderDelConfirm',
            '/\/myorder_delcon_[0-9]*/g': 'handleOrderDelete',
            '/\/myorder_delall/g': 'handleOrderDelAll',
            '/\/myorder_delallcon/g': 'handleOrderDeleteAll'
        }
    }
}

module.exports = UserOrder;