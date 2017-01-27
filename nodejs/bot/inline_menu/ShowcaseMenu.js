/**
 * Showcase menu
 */

const BaseMenu = require('./BaseMenu')
const Product = require('../models/Product')

class ShowcaseMenu extends BaseMenu {

    constructor() {
        super(...arguments)

        this.product = new Product(this.app, this.botScope)
        this.selectedItems = {}

        this.menuCitiesScheme = {}
        this.buttonsCities = []

        this.menuGoodsScheme = {}
        this.menuGoodsButtons = []

        this.menuWeightOfGoodsScheme = {}
        this.menuWeightOfGoodsButtons = []
    }

    run() {
        return this.product.load().then(() => {
            return this.menuCity()
        })
    }

    menuCity() {

        this.buttonsCities = this.product.citiesSorted.map(city => {
            return {
                text: `🏡 ${city.name}`,
                callback: (callbackQuery, message) => {

                    this.selectedItems.city_id = city.id

                    return this.menuGoods(message)
                }
            }
        })

        this.buttonsCities.push(this._commonButtons.start({prev_message: this.params.prev_message}))

        return this.app.render('showcase.index').then(content => {

            this.menuCitiesScheme = {
                layout: this.layoutTwoColumns(this.product.cities.length, 1),
                message: content,
                params: [{parse_mode: 'markdown'}],
                menu: this.buttonsCities
            }

            return this.botScope.runInlineMenu(this.menuCitiesScheme, this.params.prev_message)
        }).catch(err => this.app.logger.error(err))
    }

    menuGoods(prevMessage) {

        let goodsOfcity = this.product.getGoodsByCityId(this.selectedItems.city_id)

        let menuGoods = goodsOfcity.map(goods => {
            return {
                text: `🎁 ${goods.name}`,
                callback: (callbackQuery, message) => {

                    this.selectedItems.goods_id = goods.id

                    return this.menuWeightOfGoods(message)
                }
            }
        })

        this.menuGoodsButtons = this.product.hasProduct({city_id: this.selectedItems.city_id}) ? menuGoods : []

        this.menuGoodsButtons.push({
            text: '❰ К выбору города',
            callback: () => this.menuCity()
        })

        this.menuGoodsButtons.push(this._commonButtons.start({prev_message: prevMessage}))

        let tplData = {
            selected_items: this.selectedItems,
            product: this.product,
            goods: goodsOfcity
        }

        return this.app.render('showcase.choice_goods', tplData).then(content => {

            this.menuGoodsScheme = {
                layout: this.layoutTwoColumns(goodsOfcity.length, 2),
                message: content,
                params: [{parse_mode: 'markdown'}],
                menu: this.menuGoodsButtons
            }

            return this.botScope.runInlineMenu(this.menuGoodsScheme, prevMessage)
        }).catch(err => this.app.logger.error(err))
    }

    menuWeightOfGoods(prevMessage) {

        let tplData = {
            product: this.product,
            selected_items: this.selectedItems
        }

        let products = this.product.getProduct(this.selectedItems.goods_id)

        // Menu choice weight of goods
        this.menuWeightOfGoodsButtons = products.map(product => {
            return {
                text: `📦 ${this.product.weightForHumans(product.weight)} - 💰 ${product.cost}`,
                callback: (callbackQuery, message) => {
                    this.selectedItems.weight = product.weight

                    return this.menuCountPackage(message)
                }
            }
        })

        this.menuWeightOfGoodsButtons.push({
            text: '❰ К выбору товара',
            callback: () => {
                delete this.selectedItems.goods_id

                return this.menuGoods(prevMessage)
            }
        })
        this.menuWeightOfGoodsButtons.push({
            text: '❰ К выбору города',
            callback: () => this.menuCity()
        })
        this.menuWeightOfGoodsButtons.push(this._commonButtons.start({prev_message: prevMessage}))

        return this.app.render('showcase.choice_weight_of_goods', tplData).then(content => {

            this.menuWeightOfGoodsScheme = {
                layout: this.layoutTwoColumns(products.length, 2, 1),
                message: content,
                params: [{parse_mode: 'markdown'}],
                menu: this.menuWeightOfGoodsButtons
            }

            return this.botScope.runInlineMenu(this.menuWeightOfGoodsScheme, prevMessage)
        }).catch(err => this.app.logger.error(err))
    }

    menuCountPackage(prevMessage) {

    }
}

module.exports = ShowcaseMenu