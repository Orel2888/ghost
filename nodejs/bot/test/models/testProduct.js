
const assert = require('chai').assert
const Product = require('../../models/Product')

describe('Testing product model', function () {

    let dataGoods = {
        cities: {
            1: 'City1',
            2: 'City2',
            3: 'City3'
        },
        goods: {
            'City1': [{
                id: 1,
                name: 'Ananas',
                count: 2
            }],
            'City2': [{
                id: 2,
                name: 'Banan',
                count: 1
            }]
        },
        products: {
            1: [{
                weight: 0.5,
                cost: 1500,
                count: 2
            },
                {
                    weight: 1,
                    cost: 2500,
                    count: 4
                }]
        }
    }

    Product.prototype.getDataGoods = function () {
        return Promise.resolve(dataGoods)
    }

    const product = new Product()

    it ('Load data goods', () => {
        return product.load()
    })

    it ('Get cities', () => {
        assert.equal(product.cities, dataGoods.cities)
    })

    it ('Has product by city', () => {
        assert.isOk(product.hasProduct({city_id: 1}))
        assert.isNotOk(product.hasProduct({city_id: 2}))
    })

    it ('Has product by goods', () => {
        assert.isOk(product.hasProduct({goods_id: 1}))
        assert.isNotOk(product.hasProduct({goods_id: 2}))
    })

})