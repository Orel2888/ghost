'use strict';

const Telegram = require('telegram-node-bot')
const TelegramBaseController = Telegram.TelegramBaseController
const emoji = require('node-emoji')
const UserScope = require('../UserScope')

let emojify = emoji.emojify;

class UserController extends TelegramBaseController {

    constructor(Powers) {
        super();

        this.powers    = Powers;
        this.ghostApi  = this.powers.ghostApi;
        this.userScope = new UserScope(this.powers);
    }

    handleStart($) {

        let registerUser = (scope) => {
            return this.ghostApi.api('users.reg', 'POST', {
                name: scope._message._from._firstName + (scope._message._from.lastName ? ' '+ scope._message._from.lastName : ''),
                tg_username: scope._message._from._username,
                tg_chatid: scope._message._chat._id
            }).then(response => {
                return this.userScope.findUser(scope._message._from._id, scope);
            });
        };

        let priceList = () => {
            return this.ghostApi.api('goods.pricelist', 'GET').then(response => {
                let priceList = response.data;

                var message = `Добро пожаловать в круглосуточный магазин ${emoji.emojify(':eyes:')} Ghost.\n\n`;

                message += `Решение технических вопросов: @Ghost228\n`;
                message += `Мой профиль ${emoji.emojify(':point_right:')} /myprofile\n`;
                message += `Мои заказы ${emoji.emojify(':point_right:')} /myorder\n\n`;

                for (let city of Object.keys(priceList)) {
                    message += `${emoji.emojify(':house_with_garden:')} *${city}*\n`;

                    // If in city not goods price that message about empty
                    if (!Object.keys(priceList[city]).filter(goodsType => Object.keys(priceList[city][goodsType]).length).length) {
                        message += `${emojify(':o:')} Пусто\n`;
                    }

                    for (let goodsType of Object.keys(priceList[city])) {

                        // Check is exists goods in price, if not goods, continue
                        if (!Object.keys(priceList[city][goodsType]).length) continue;

                        message += `  ${emoji.emojify(':gift:')} ${goodsType}\n`;

                        for (let weight of Object.keys(priceList[city][goodsType])) {
                            let goodsInfo = priceList[city][goodsType][weight];
                            let weightInt = weight.replace('.', '');

                            message += `${' '.repeat(4)}${emoji.emojify(':package:')} ${weight} - ${goodsInfo.cost}\n`;
                            message += `${' '.repeat(4)}купить ${emoji.emojify(':point_right:')} /buy${goodsInfo.goods_id}\\_${weightInt}\n`;
                            message += `${' '.repeat(4)}${'-'.repeat(30)}\n`
                        }

                    }
                }

                message += `\nОбновить /start`;

                return $.sendMessage(message, {parse_mode: 'markdown'});
            });
        };

        // Start running
        return this.userScope.findUser($._message._chat._id, $).then(udata => {
            return priceList();
        }).catch(err => {
            // If user not registered else register
            if (err.statusCode == 404) {
                return registerUser($).then(udata => {
                    $.userSession.udata = udata;

                    return priceList();
                })
            } else {
                $.sendMessage('Произошла ошибка');
                console.log(err);

                return err;
            }
        })
    }

    handleProfile($) {

        let profileInfo = (udata, purse) => {
            let message = `*Мой профиль*\n\n`;

            message += `Здравствуйте, *${udata.name}*\n`;
            message += `${emojify(':dollar:')} Ваш баланс: ${udata.balance}\n\n`;

            message += `*Реквизиты*\n`;
            message += `QIWI: +*${purse}*\n`;
            message += `Комментарий: *${udata.comment}*\n\n`;

            message += `Вы можете заранее пополнять свой баланс, что бы в любой момент совершать покупки. `;
            message += `Баланс пополняется в течении 30-ти секунд после перевода средств на выше ${emojify(':point_up:')} указанные реквизиты. `;
            message += `При оплате выбранного товара и не хватке средств, все платежи поступают на ваш счет. `;
            message += `Если у вас есть не выполненные заказы, они автоматически выполнятся при оказании достаточной суммы на вашем балансе. `;
            message += `Следите за своим списком заказов.`;

            message += `\n\nОбновить ${emojify(':point_right:')} /myprofile\n`;
            message += `Мои заказы ${emojify(':point_right:')} /myorder\n`;
            message += `В начало ${emojify(':point_right:')} /start\n`;

            return $.sendMessage(message, {parse_mode: 'markdown'});
        }

        return this.ghostApi.api('users.find', 'GET', {tg_chatid: $._message._from._id}).then(response => {
            let udata = response.data;

            return this.ghostApi.api('purse', 'GET').then(response => profileInfo(udata, response.data.phone));
        }).catch(console.log)
    }

    get routes() {
        return {
            '/start': 'handleStart',
            '/myprofile': 'handleProfile'
        }
    }
}

module.exports = UserController;