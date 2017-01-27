/**
 * Product model
 */

class Product {

    constructor(app, botScope) {
        this.app      = app
        this.botScope = botScope

        this._cities   = []
        this._goods    = {}
        this._products = {}
    }

    load() {
        if (this.app.config.model_product_test_mode) return Promise.resolve(this.fillTestData())

        return this.getDataGoods().then(goodsData => {
            this._cities   = goodsData.cities
            this._goods    = goodsData.goods
            this._products = goodsData.products

            return goodsData
        })
    }

    getDataGoods() {
        return this.app.api.api('goods.pricelist', 'GET').then(response => {
            return response.data
        })
    }

    hasProduct({city_id, goods_id} = data) {

        if (city_id) {
            let cityName = this.getCity(city_id).name

            return this._goods.hasOwnProperty(cityName)
                ? this._goods[cityName].some((goods, index) => {
                    return this._products.hasOwnProperty(goods.id) && this._products[goods.id].length
                })
                : false
        }

        if (goods_id) {
            return this._products.hasOwnProperty(goods_id) && this._products[goods_id].length
        }

        return false
    }

    get cities() {
        return this._cities
    }

    get citiesSorted() {
        // Creating list with the counter the products
        let sortable = this._cities.map((city, index) => {
            return Object.assign(city, {
                count_product: this._goods.hasOwnProperty(city.name)
                    ? this._goods[city.name].filter(goods => goods.count > 0).map(goods => goods.count).reduce((a, b) => a + b, 0)
                    : 0
            })
        })

        return sortable.sort((a, b) => {
            if (a.count_product > b.count_product) return -1
            if (a.count_product < b.count_product) return 1

            return 0
        })
    }

    getGoodsByCityId(id) {
        return this._goods[this.getCity(id).name] || [];
    }

    getGoodsByCityName(cityName) {
        return this._goods[cityName] || []
    }

    getProduct(goodsId) {
        return this._products[goodsId].sort((a, b) => {
            if (a.weight < b.weight) return -1
            if (a.weight > b.weight) return 1

            return 0
        })
    }

    hasCity(cityId) {
        return this._cities.some(item => item.id == cityId)
    }

    getCity(cityId) {
        return this._cities.filter(item => item.id == cityId)[0]
    }

    getGoods(goodsId) {
        for (let city in this._goods) {
            let goods = this._goods[city].filter(goods => goods.id == goodsId)

            if (goods.length) return goods[0]
        }

        return null
    }

    weightForHumans(weight) {
        return weight < 0.99 ? `${weight}мг` : `${weight}г`
    }

    /**
     * Fill test data
     * @param data [{
     *      name,
     *      goods: [
     *          {
     *              name: 'name',
     *              products: [{
     *                  weight,
     *                  cost,
     *                  count
     *              }]
     *          }
     *      ]
     * }]
     */
    makeData(data) {
        data.forEach((item, index) => {
            let cityId = ++index

            this._cities.push({name: item.name, id: cityId})

            item.goods.forEach((goods, goodsIndex) => {
                let goodsId = ++goodsIndex

                if (!this._goods[item.name]) this._goods[item.name] = []

                this._goods[item.name].push({
                    name: goods.name,
                    id: goodsId,
                    count: goods.products.map(product => product.count).reduce((a, b) => a + b, 0)
                })

                goods.products.forEach((product, productIndex) => {

                    if (!this._products[goodsId]) this._products[goodsId] = []

                    this._products[goodsId].push({
                        weight: product.weight,
                        cost: product.cost,
                        count: product.count
                    })
                })

            })
        })
    }

    fillTestData() {
        try {
            return this.makeData([{
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
            return this.app.logger.error(e.message)
        }
    }

    clearData() {
        this._cities   = []
        this._goods    = {}
        this._products = {}
    }
}

module.exports = Product