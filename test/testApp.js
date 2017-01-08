'use strict'

const assert = require('chai').assert

describe('Test class App', function() {

    /*const App = new (require('../nodejs/bot/App'))({
        k1: 'val',
        k2: 'val2'
    })

    it ('Init', () => {
        console.log(App.k1);
    })*/

    it ('Parse price', () => {

        let cities = {
            1: 'City1',
            2: 'City2',
            3: 'City3'
        }

        let goods = {
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
        }

        let productWeights = {
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

        let priceData = {
            cities,
            goods,
            productWeights
        }

        let hasProduct = (city, priceData) => {
            if (!priceData.goods.hasOwnProperty(city)) return false;

            return priceData.goods[city].filter(item => item.count).length
        }

        let getGoods = (city, priceData) => {
            return priceData.goods[city].filter(item => item.count);
        }

        let getProduct = (goodsId, priceData) => {
            if (!priceData.productWeights.hasOwnProperty(goodsId)) return null;

            return priceData.productWeights[goodsId]
        }

        for (let cityId in priceData.cities) {
            let cityName = priceData.cities[cityId];

            console.log(cityName)

            if (hasProduct(cityName, priceData)) {
                getGoods(cityName, priceData).forEach(goods => {
                    console.log('-', goods.name)

                    let product = getProduct(goods.id, priceData)

                    if (product !== null) {
                        product.forEach(product => {
                            console.log('->', product.weight, '$' + product.cost, `(${product.count})`)
                        })
                    }
                })
            } else {
                console.log('Not products')
            }
        }
    })

})