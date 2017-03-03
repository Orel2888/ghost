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
            return this.app.includeMenu('Start', $, {product}).run()
        }).catch(err => {
            this.app.logger.error({model_product_load: err})
        })
    }

    get routes() {
        return {
            'startCommand': 'startHandler'
        }
    }
}

module.exports = MainController