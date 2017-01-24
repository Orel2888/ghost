/**
 * Logger
 */

const WebAdminLogger = require('telegram-node-bot/lib/logger/WebAdminLogger')

class Logger extends WebAdminLogger {

    warn(data) {
        return super.warn(this.stringToObject(data))
    }

    error(data) {
        return super.error(this.stringToObject(data))
    }

    info(data) {
        return super.log(this.stringToObject(data))
    }

    stringToObject(data) {
        return typeof data == 'object' ? data : {message: data}
    }
}

module.exports = Logger