
const config = require('dotenv').config({path: require('path').join(__dirname, '../../../.env')})
const app = require('../App')
const Logger = require('../logger/Logger')

// Initialization App
const App = new app({
    config: Object.assign(require('../config'), config),
    // Bot controllers
    mapControllers: [
        'MainController'
    ],
    logger: new Logger()
})

module.exports = App