'use strict';

const config = require('../config');
const Telegram = require('telegram-node-bot');
const tg = new Telegram.Telegram(config.get('TGBOT_TOKEN'));
const AdminController = require('./AdminController');

const adminUsernames = config.get('TGBOT_ADMINS').split(',');
const adminCommands  = [
    '/транс',
    '/товар',
    '/наличие',
    '/админ',
    '/кош'
];

tg.before(function (updates, cb) {

    let command = updates._message.text.split(' ')[0];

    if (adminCommands.includes(command) && adminUsernames.includes(updates._message._from._username)) {
        cb(true);
    } else {
        cb(false);
    }

});

/**
 * Admin functions
 */
tg.router
    .when([
        '/транс',
        '/товар взять :arg1',
        '/товар',
        '/наличие',
        '/админ помощь',
        '/кош выбрать :arg1',
        '/кош'
    ], new AdminController());
