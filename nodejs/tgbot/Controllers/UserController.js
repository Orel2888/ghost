'use strict';

const Telegram = require('telegram-node-bot')
const TelegramBaseController = Telegram.TelegramBaseController
const emoji = require('node-emoji')
const UserScope = require('../UserScope')

class UserController extends TelegramBaseController {

    constructor(Powers) {
        super();

        this.powers    = Powers;
        this.ghostApi  = this.powers.ghostApi;
        this.userScope = new UserScope(this.powers);
    }

    startHandle($) {

        let registerUser = (scope) => {
            return this.ghostApi.api('users.reg', 'POST', {
                name: scope._message._from._firstName + (scope._message._from.lastName ? ' '+ scope._message._from.lastName : ''),
                tg_username: scope._message._from._username,
                tg_chatid: scope._message._chat._id
            }).then(response => {
                return findUser(scope._message._chat._id);
            });
        };

        let priceList = () => {
            return this.ghostApi.api('goods.pricelist', 'GET').then(response => {
                let priceList = response.data;

                var message = `Добро пожаловать в круглосуточный магазин ${emoji.emojify(':eyes:')} Ghost.\n\n`;

                for (let city of Object.keys(priceList)) {
                    message += `${emoji.emojify(':house_with_garden:')} *${city}*\n`;

                    for (let goodsType of Object.keys(priceList[city])) {

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

                $.sendMessage(message, {parse_mode: 'markdown'});
            });
        };

        // Start running
        if (!$.userSession.userdata) {
            return this.userScope.findUser($._message._chat._id, $).catch(err => {
                // If user not registered else register
                if (err.statusCode == 404) {
                    return registerUser($);
                } else {
                    console.log(err)
                    return err;
                }
            }).then(userdata => {
                $.userSession.udata = userdata;

                priceList();
            });
        } else {
            priceList();
        }
    }

    get routes() {
        return {
            '/start': 'startHandle'
        }
    }
}

module.exports = UserController;