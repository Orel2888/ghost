'use strict';

const config = require('../config');
const Telegram = require('telegram-node-bot');
const tg = new Telegram.Telegram(config.get('TGBOT_TOKEN'));
const AdminController = require('./AdminController');
const UsersController = require('./UsersController');
const PurchaseController = require('./PurchaseController');

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

    // Check is exists username to account
    if (!updates._message._from._username) {
        tg.api.sendMessage(updates._message._chat.id, 'Извините, вам необходимо зарегистрировать username. Сделать это можно в настройках.');

        cb(false);

        return false;
    }

    // Check is admin command and authenticate admin
    if (adminCommands.includes(command)) {
        if (adminUsernames.includes(updates._message._from._username)) {
            cb(true);
        } else {
            cb(false);
        }
    } else {
        cb(true);
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

/**
 * Shop
 **/

tg.router
    .when([
        '/start'
    ], new UsersController())
    .when(new RegExp('/(/buy)\d*_\d*/i'), new PurchaseController());