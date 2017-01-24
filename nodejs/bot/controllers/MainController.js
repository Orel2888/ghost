/**
 * Main controller
 *
 **/

const BaseController = require('./BaseController')
const Product = require('../models/Product')

class MainController extends BaseController {

    startHandler($) {

        const product = new Product(this.app, $)

        return product.load().then(() => {
            return this.app.includeMenu('start', $, {product}).run()
        })
    }

    get routes() {
        return {
            'startCommand': 'startHandler'
        }
    }
}

module.exports = MainController