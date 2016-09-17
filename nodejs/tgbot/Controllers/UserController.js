'use strict';

const Telegram = require('telegram-node-bot');
const TelegramBaseController = Telegram.TelegramBaseController;
const emoji = require('node-emoji');
const UserScope = require('../UserScope');
const Tools = require('../Tools');

let emojify = emoji.emojify;

class UserController extends TelegramBaseController {

    constructor(Container) {
        super();

        this.container = Container;
        this.powers    = this.container.make('Powers');
        this.ghostApi  = this.powers.ghostApi;
        this.userScope = this.container.make('UserScope');
        this.tools = new Tools();
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

                var priceMessage = '';

                for (let city of Object.keys(priceList)) {
                    priceMessage += `${emoji.emojify(':house_with_garden:')} *${city}*\n`;

                    // If in city not goods price that message about empty
                    if (!Object.keys(priceList[city]).filter(goodsType => Object.keys(priceList[city][goodsType]).length).length) {
                        priceMessage += `${emojify(':o:')} Пусто\n`;
                    }

                    for (let goodsType of Object.keys(priceList[city])) {

                        // Check is exists goods in price, if not goods, continue
                        if (!Object.keys(priceList[city][goodsType]).length) continue;

                        priceMessage += `  ${emoji.emojify(':gift:')} ${goodsType}\n`;

                        for (let weight of Object.keys(priceList[city][goodsType])) {
                            let goodsInfo = priceList[city][goodsType][weight];
                            let weightInt = weight.replace('.', '');

                            priceMessage += `${' '.repeat(4)}${emoji.emojify(':package:')} ${weight} - ${goodsInfo.cost}\n`;
                            priceMessage += `${' '.repeat(4)}купить ${emoji.emojify(':point_right:')} /buy${goodsInfo.goods_id}\\_${weightInt}\n`;
                            priceMessage += `${' '.repeat(4)}${'-'.repeat(30)}\n`
                        }

                    }
                }

                return this.tools.render('user/start', {priceMessage}).then(content => {
                    return $.sendMessage(content, {parse_mode: 'markdown'});
                }).catch(err => {
                    console.log(err);
                    return $.sendMessage('Произошла ошибка');
                });
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

            let data = {
                name: udata.name,
                balance: udata.balance,
                purse,
                comment: udata.comment,
                nvg: ['refreshMyProfile', 'myorder', 'in start']
            };

            return this.tools.render('user/profile', data).then(content => {
                return $.sendMessage(content, {parse_mode: 'markdown'});
            }).catch(err => {
                console.log(err);
                return $.sendMessage('Произошла ошибка');
            });
        };

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