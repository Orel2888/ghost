/**
 * Product model
 */

class Product {

    constructor(app, botScope) {
        this.app      = app
        this.botScope = botScope

        this._cities   = {}
        this._goods    = {}
        this._products = {}
    }

    load() {
        return this.getDataGoods().then(goodsData => {
            this._cities   = goodsData.cities
            this._goods    = goodsData.goods
            this._products = goodsData.products

            return true
        })
    }

    getDataGoods() {
        return this.app.api.api('goods.pricelist', 'GET').then(response => {
            return response.data
        })
    }

    hasProduct({city_id, goods_id} = data) {

        if (city_id) {
            let cityName = this._cities[city_id]

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
}

module.exports = Product