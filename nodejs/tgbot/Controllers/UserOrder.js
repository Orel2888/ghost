'use strict';

const Telegram = require('telegram-node-bot')
const TelegramBaseController = Telegram.TelegramBaseController
const emoji = require('node-emoji')
const UserScope = require('../UserScope')

let emojify = emoji.emojify;

class UserOrder extends TelegramBaseController {

    constructor(Powers) {
        super()

        this.powers    = Powers;
        this.ghostApi  = this.powers.ghostApi;
        this.userScope = new UserScope(this.powers);
    }

    handleOrderList($) {

        let clientId = $._message._from._id;

        let messageOrderList = (orders) => {
            var message = `${emojify(':page_facing_up:')} Список заказов\n\n`;

            if (!orders.length) {
                message += 'Нет заказов\n';
            } else {
                orders.forEach((item, index) => {
                    message += `${emojify(':house_with_garden:')} *${item.city_name}*\n`;
                    message += `${emojify(':gift:')} Товар: *${item.goods_name}*\n`;
                    message += `${emojify(':package:')} Вес: *${item.weight}*\n`;
                    message += `${emojify(':speech_balloon:')} Статус: *${item.status_message}*\n`;
                    message += `${emojify(':date:')} Дата: *${item.date}*\n`;

                    if (item.status == 1) {
                        message += `${emojify(':running:')} Адрес: ${item.address}\n`;
                    }

                    message += `Удалить ${emojify(':point_right:')} /myorder\\_del\\_${item.id}\n`;
                    message += `${'-'.repeat(25)}\n`;
                })
            }

            if (orders.length) {
                message += `Удалить все заказы ${emojify(':point_right:')} /myorder\\_delall\n`;
            }

            message += `${emojify(':arrows_counterclockwise:')} Обновить ${emojify(':point_right:')} /myorder\n`;
            message += `${emojify(':house:')} В начало ${emojify(':point_right:')} /start\n`;

            return $.sendMessage(message, {parse_mode: 'markdown'})
        }

        let orderList = (clientId) => {
            return this.ghostApi.api('order.list', 'GET', {
                client_id: clientId
            }).then(response => {
                return messageOrderList(response.data);
            }).catch(err => {
                console.log(err)

                $.sendMessage('Произошла ошибка')
            })
        }

        return this.userScope.findUser(clientId, $).then(udata => orderList(udata.id))
    }

    handleOrderDelConfirm($) {
        let orderId = $._message._text.match(/(\d+)/)[1];

        let message = 'Подтвердите удаление заказа\n';

        message += `Удалить ${emojify(':point_right:')} /myorder_delcon_${orderId}\n`;

        message += `К заказам ${emojify(':point_right:')} /myorder\n`;
        message += `В начало ${emojify(':point_right:')} /start\n`;

        return $.sendMessage(message);
    }

    handleOrderDelete($) {
        let orderId = $._message._text.match(/(\d+)/)[1];

        return this.userScope.findUser($._message._from._id, $).then(udata => {
            return this.ghostApi.api('order.del', 'POST', {
                order_id: orderId,
                client_id: udata.id
            }).then(response => {
                let message = `${emojify(':white_check_mark:')} Заказ успешно удален\n`;

                message += `К заказам ${emojify(':point_right:')} /myorder\n`;
                message += `В начало ${emojify(':point_right:')} /start\n`;

                return $.sendMessage(message);
            }).catch(err => {
                return $.sendMessage('Произошла ошибка при удалении заказа');
            })
        })
    }

    handleOrderDelAll($) {
        let message = 'Подтвердите удаление всех заказов\n';

        message += `Удалить ${emojify(':point_right:')} /myorder_delallcon\n`;

        message += `К заказам ${emojify(':point_right:')} /myorder\n`;
        message += `В начало ${emojify(':point_right:')} /start\n`;

        return $.sendMessage(message);
    }

    handleOrderDeleteAll($) {
        return this.userScope.findUser($._message._from._id, $).then(udata => {
            return this.ghostApi.api('order.delall', 'POST', {
                client_id: udata.id
            }).then(response => {
                let message = `${emojify(':white_check_mark:')} Заказы успешно удалены\n`;

                message += `К заказам ${emojify(':point_right:')} /myorder\n`;
                message += `В начало ${emojify(':point_right:')} /start\n`;

                return $.sendMessage(message);
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
            '/\/myorder_delallcon/g': 'handleOrderDeleteAll',
        }
    }
}

module.exports = UserOrder;