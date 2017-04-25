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

    lkHandler($) {
        return this.app.includeMenu('UserCabinet', $).run()
    }

    pricelistHandler($) {
        return this.app.includeMenu('Showcase', $).run()
    }

    shoppingcartHandler($) {
        return this.app.includeMenu('Orders', $, {type_order: 'pending'}).run()
    }

    purchasesHandler($) {
        return this.app.includeMenu('Orders', $, {type_order: 'successful'}).run()
    }

    paymentHandler($) {
        return this.app.includeMenu('Payment', $).run()
    }

    helpHandler($) {
        return this.app.includeMenu('Help', $).run()
    }

    rejected() {
        console.log(arguments)
    }

    get routes() {
        return {
            'startCommand': 'startHandler',
            'lkCommand': 'lkHandler',
            'pricelistCommand': 'pricelistHandler',
            'shoppingcartCommand': 'shoppingcartHandler',
            'purchasesCommand': 'purchasesHandler',
            'paymentCommand': 'paymentHandler',
            'helpCommand': 'helpHandler'
        }
    }
}

module.exports = MainController