'use strict';

const config = require('../config');
const tg     = require('telegram-node-bot')(config.get('TGBOT_TOKEN'));

tg.router
    .when(['ping'], 'PingController')
    .otherwise('OtherwiseController');

tg.controller('PingController', ($) => {
    tg.for('ping', () => {
        $.sendMessage('Pong');
    });
});

tg.controller('OtherwiseController', ($) => {
    let priceList = `
Добро пожаловать в круглосуточный Ghost 👀 SHOP

⭐ В наличии
    `;
    $.sendMessage(priceList.trim());
});
