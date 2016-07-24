'use strict';

const config = require('../config')
const Telegram = require('telegram-node-bot')
const tg = new Telegram.Telegram(config.get('TGBOT_TOKEN'))
const AdminController = require('./Controllers/AdminController')
const UserController = require('./Controllers/UserController')
const UserOrder = require('./Controllers/UserOrder')
const PurchaseController = require('./Controllers/PurchaseController')
const powers = require('./Powers')

let Powers = new powers();

const adminUsernames = config.get('TGBOT_ADMINS').split(',');
const adminCommands  = [
    '/транс',
    '/товар',
    '/наличие',
    '/админ',
    '/кош'
];

const userCommands = [
    '/start',
    /\/buy[0-9]*_[0-9]*_[0-9]*/gi,
    /\/buy[0-9]*_[0-9]*/gi,
    /\/myorder_del_[0-9]*/g,
    /\/myorder_delcon_[0-9]*/g,
    /\/myorder_delallcon/g,
    /\/myorder_delall/g,
    '/myorder',
    '/myprofile'
];

let checkCommand = (command, commandsList) => {
    return commandsList.some((item, index) => {
        return item instanceof RegExp ? item.test(command) : item === command;
    })
}

tg.before(function (updates, cb) {

    let commandText = updates._message.text;
    let command = updates._message.text.split(' ')[0];

    // Check is exists username on account
    if (!updates._message._from._username) {
        tg.api.sendMessage(updates._message._chat.id, 'Извините, вам необходимо зарегистрировать username. Сделать это можно в настройках.');

        cb(false);

        return false;
    }

    // Authentication a user
    if (checkCommand(commandText, userCommands)) {
        return Powers.checkSessionAndAuthenticate().then(() => {
            return cb(true)
        })
    }

    // Authentication a admin
    if (checkCommand(command, adminCommands) && adminUsernames.includes(updates._message._from._username)) {
        return Powers.checkSessionAndAuthenticate(updates._message._from._username).then(() => {
            cb(true)
        })
    }

    cb(false)
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
    ], new AdminController(Powers));

/**
 * Shop
 **/

tg.router
    .when([
        '/start',
        '/myprofile'
    ], new UserController(Powers));

tg.router
    .when([
        /\/buy[0-9]*_[0-9]*_[0-9]*/gi,
        /\/buy[0-9]*_[0-9]*/gi
    ], new PurchaseController(Powers));

tg.router
    .when([
        /\/myorder_del_[0-9]*/g,
        /\/myorder_delcon_[0-9]*/g,
        /\/myorder_delallcon/g,
        /\/myorder_delall/g,
        '/myorder'
    ], new UserOrder(Powers))