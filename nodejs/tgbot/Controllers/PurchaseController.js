'use strict';

const Telegram = require('telegram-node-bot')
const TelegramBaseController = Telegram.TelegramBaseController
const emoji = require('node-emoji')
const UserScope = require('../UserScope')

let emojify = emoji.emojify;

class PurchaseController extends TelegramBaseController {

    constructor(Powers) {
        super();

        this.powers   = Powers;
        this.ghostApi = this.powers.ghostApi;
        this.userScope = new UserScope(this.powers);
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

            message += `Будьте внимательны, если на вашем балансе уже есть достаточная сумма для выбранного товара. `;
            message += `Покупка произойдет сразу же после выбора количества.\n\n`;

            for (let i = 1; i<=6; i++) {
                message += `${emoji.emojify(':package:')} ${i} ${emoji.emojify(':point_right:')} ${$._message._text}_${i}\n`;
                message += `${'-'.repeat(25)}\n`;
            }

            message += `Мой профиль ${emoji.emojify(':point_right:')} /myprofile\n`;
            message += `В начало ${emoji.emojify(':point_right:')} /start`;

            return $.sendMessage(message);
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

                    if (data.order_processed > 0) {
                        message += `${emojify(':white_check_mark:')} Успешно выполнено *${data.order_processed}* заказов\n`;
                    }

                    message += `*Реквизиты для оплаты*\n`;
                    message += `QIWI: *+${data.purse}*\n`;
                    message += `Комментарий: *${$.userSession.udata.comment}*\n`;
                    message += `Сумма: *${data.cost}*\n\n`;
                    message += `*Оплата и покупка*\n`;
                    message += `Ваш заказ успешно создан и будет автоматически выполнен в течении 30 секунд после поступления оплаты. `;
                    message += `Сумма оплаты может быть больше, но никак не меньше указанной *${data.cost}*, можно отправлять средства любыми \n`;
                    message += `частями, средства зачисляются на ваш баланс, когда на вашем балансе окажеться `;
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
            return this.userScope.findUser($._message._from._id).then(udata => {
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