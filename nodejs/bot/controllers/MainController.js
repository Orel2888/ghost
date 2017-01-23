/**
 * Main controller
 *
 **/

const BaseController = require('./BaseController')
const Product = require('../models/Product')

class MainController extends BaseController {

    startHandler($) {

        const product = new Product(this.app, $)

        // Making a data for test
        product.loadDisable = true

        try {
            product.makeData([{
                name: 'Moscov',
                goods: []
            },{
                name: 'Netherlands',
                goods: []
            },{
                name: 'Albania',
                goods: [{
                    name: 'Banan',
                    products: [{
                        weight: 0.5,
                        cost: 1500,
                        count: 3
                    }, {
                        weight: 1,
                        cost: 2500,
                        count: 4
                    },{
                        weight: 0.33,
                        cost: 1000,
                        count: 3
                    }]
                }, {
                    name: 'Cocks',
                    products: [{
                        weight: 1,
                        cost: 2500,
                        count: 5
                    }, {
                        weight: 2,
                        cost: 4500,
                        count: 3
                    }]
                }]
            }])
        } catch(e) {
            console.log(e)
        }

        //------------------------------------------------------//

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