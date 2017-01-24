/**
 * Logger
 */

const BaseLogger = require('./BaseLogger')

class Logger extends BaseLogger {

    warn(message) {
        return this.log('warn', message)
    }

    error(message) {
        return this.log('error', message)
    }

    info(message) {
        return this.log('info', message)
    }

    log(type, content) {
        return console[type](content)
    }
}

module.exports = Logger