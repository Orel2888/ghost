/**
 * Main controller
 *
 **/

const BaseController = require('./BaseController')

class MainController extends BaseController {

    startHandler($) {

        console.log('username', $.user.telegramUsername)
        return this.app.includeMenu('start', $).run()
    }

    get routes() {
        return {
            'startCommand': 'startHandler'
        }
    }
}

module.exports = MainController