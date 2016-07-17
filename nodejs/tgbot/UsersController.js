'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const config = require('../config');
const GhostApi = require('../ghost-api/GhostApi');
const emoji = require('node-emoji');

class UsersController extends TelegramBaseController {

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

    startHandle($) {

        let findUser = (chatId) => {
            return this.ghostApi.api('users.find', 'GET', {
                tg_chatid: chatId
            }).then(response => {
                return $.userSession.userdata = response.data;
            })
        };

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

        $.checkAuth.then(auth => {

            if (!auth) throw new Error('Error authenticate');

            if (!$.userSession.userdata) {
                return findUser($._message._chat._id).catch(err => {
                    // If user not registered else register
                    if (err.statusCode == 404) {
                        return registerUser($);
                    } else {
                        return err;
                    }
                }).then(userdata => {
                    priceList();
                });
            } else {
                priceList();
            }

        }).catch(console.log)
    }

    get routes() {
        return {
            '/start': 'startHandle'
        }
    }
}

module.exports = UsersController;