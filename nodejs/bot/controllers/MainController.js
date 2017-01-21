/**
 * Main controller
 *
 **/

const BaseController = require('./BaseController')

class MainController extends BaseController {

    startHandler($) {
        return this.app.includeMenu('start', $).run()
    }

    get routes() {
        return {
            'startCommand': 'startHandler'
        }
    }
}

module.exports = MainController