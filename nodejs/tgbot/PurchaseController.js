'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const config = require('../config');
const GhostApi = require('../ghost-api/GhostApi');
const emoji = require('node-emoji');

let emojify = emoji.emojify;

class PurchaseController extends TelegramBaseController {

    constructor() {
        super();

        this.ghostApi = new GhostApi({
            apiKey: config.get('API_KEY'),
            apiUrl: config.get('API_URL')
        });
    }

    before(command, scope) {

        scope.checkAuth = this.ghostApi.checkAuth().then(auth => {

            if (!auth) {
                return this.ghostApi.authenticationUser();
            }

            return true;
        });

        return scope;
    }

    handle() {

    }

    handleBeforeBuy($) {

        let getGoodsIdAndWeight = () => {
            const [, goodsId, weight] = $._message._text.match(/\/buy([0-9]*)_([0-9]*)/i);

            return [goodsId, weight];
        };

        let sendMessage = () => {
            var message = `Какое количество вам требуется?\n\n`;

            for (let i = 1; i<=6; i++) {
                message += `${emoji.emojify(':package:')} ${i} ${emoji.emojify(':point_right:')} ${$._message._text}_${i}\n`;
                message += `${'-'.repeat(25)}\n`;
            }

            message += `В начало ${emoji.emojify(':point_right:')} /start`;

            return $.sendMessage(message);
        };

        $.checkAuth.then(auth => {
            if (!auth) throw new Error('Error authenticate');

            const [goodsId, weight] = getGoodsIdAndWeight();

            return sendMessage();

        }).catch(console.log);
    }

    handleBuy($) {

        let chunksCommand = $._message._text.split('_');
        let prevCommand   = [chunksCommand[0], chunksCommand[1]].join('_').replace('_', '\\_');

        let getGoodsIdAndWeightAndCount = () => {
            const [, goodsId, weight, count] = $._message._text.match(/\/buy([0-9]*)_([0-9]*)_([0-9]*)/i);

            return [goodsId, weight, count];
        };

        let findUser = (chatId) => {
            return this.ghostApi.api('users.find', 'GET', {
                tg_chatid: chatId
            }).then(response => {
                return $.userSession.userdata = response.data;
            })
        };

        let orderCreate = (sendData) => {
            return this.ghostApi.api('order.create', 'POST', sendData).then(response => {
                if (response.status == 'fail') {
                    let message = response.message + '\n';

                    message += `К выбору количества ${emojify(':point_right:')} ${prevCommand} \n`;
                    message += `В начало ${emojify(':point_right:')} /start`;

                    return $.sendMessage(message, {parse_mode: 'markdown'});
                } else {
                    let data = response.data;

                    let tab = ' '.repeat(4);

                    let message = `${emojify(':house_with_garden:')} *${data.city_name}*\n`;

                    message += `${tab}${emojify(':gift:')} Товар: *${data.goods_name}*\n`;
                    message += `${tab}${emojify(':package:')} Вес: *${data.weight}*\n`;
                    message += `${tab}${emojify(':bowling:')} Количество: *${data.count}*\n`;
                    message += `${tab}${emojify(':dollar:')} Цена: *${data.cost}*\n`;
                    message += `\n`;

                    message += `*Реквизиты для оплаты*\n`;
                    message += `QIWI: *+${data.purse}*\n`;
                    message += `Комментарий: *${$.userSession.udata.comment}*\n`;
                    message += `Сумма: *${data.cost}*\n\n`;
                    message += `*Оплата и покупка*\n`;
                    message += `Ваш заказ успешно создан и будет автоматически выполнен в течении 30 секунд после поступления оплаты. `;
                    message += `Сумма оплаты может быть больше, но никак не меньше указанной *${data.cost}*.\n`;
                    message += `Вы можете отправлять любые суммы, средства зачисляются на ваш баланс, когда на вашем балансе окажеться `;
                    message += `достаточная сумма для оплаты заказа, последует выполнение.\n`;
                    message += `Вы можете создать несколько заказов, они будут выполнены в порядки их создания.\n`;
                    message += `${emojify(':warning:')} Будьте внимательны, следите за вашим списком заказов в разделе ${emojify(':point_right:')} /myorder. `;
                    message += `Если вы создали первый заказ, а затем передумали и создали второй не удалив первый, при поступлении оплаты `;
                    message += `последует выполнение заказов в порядке их создания.\n\n`;

                    message += `После оплаты проверяйте ваш список заказов.\n`;
                    message += `Мой список заказов ${emojify(':point_right:')} /myorder\n`;

                    message += `\n`;
                    message += `К выбору количества ${emojify(':point_right:')} ${prevCommand} \n`;
                    message += `В начало ${emojify(':point_right:')} /start`;

                    return $.sendMessage(message, {parse_mode: 'markdown'});
                }
            });
        };

        $.checkAuth.then(auth => {
            if (!auth) throw new Error('Error authenticate');

            const [goodsId, weight, count] = getGoodsIdAndWeightAndCount();

            return findUser($._message._from._id).then(udata => {
                $.userSession.udata = udata;

                return orderCreate({
                    goods_id: goodsId,
                    weight,
                    count,
                    client_id: $.userSession.udata.id
                });
            });

        }).catch(console.log);
    }

    get routes() {
        return {
            '/\/buy[0-9]*_[0-9]*/gi': 'handleBeforeBuy',
            '/\/buy[0-9]*_[0-9]*_[0-9]*/gi': 'handleBuy'
        }
    }
}

module.exports = PurchaseController;