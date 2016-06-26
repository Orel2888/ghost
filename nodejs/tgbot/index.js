'use strict';

const config = require('../config');
const Telegram = require('telegram-node-bot');
const tg = new Telegram.Telegram(config.get('TGBOT_TOKEN'));
const AdminController = require('./AdminController');


/**
 * Admin functions
 */
tg.router
    .when(['транс'], new AdminController());