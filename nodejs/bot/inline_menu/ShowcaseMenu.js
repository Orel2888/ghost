/**
 * Showcase menu
 */

const BaseMenu = require('./BaseMenu')
const Product = require('../models/Product')
const Purse = require('../models/Purse')

class ShowcaseMenu extends BaseMenu {

    constructor() {
        super(...arguments)

        this.product = new Product(this.app, this.botScope)
        this.selectedItems = {}
    }

    run() {
        return this.product.load().then(() => {
            return this.menuCity()
        })
    }

    menuCity() {

        let buttonsCities = this.product.citiesSorted.map(city => {
            return {
                text: `🏡 ${city.name}`,
                callback: (callbackQuery, message) => {

                    this.selectedItems.city_id = city.id

                    return this.menuGoods(message)
                }
            }
        })

        buttonsCities.push(this._commonButtons.start({prev_message: this.params.prev_message}))

        return this.app.render('showcase.index').then(content => {

            let menuCitiesScheme = {
                layout: this.layoutTwoColumns(this.product.cities.length, 1),
                message: content,
                params: [{parse_mode: 'markdown'}],
                menu: buttonsCities
            }

            return this.botScope.runInlineMenu(menuCitiesScheme, this.params.prev_message)
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

        let menuGoodsButtons = this.product.hasProduct({city_id: this.selectedItems.city_id}) ? menuGoods : []

        menuGoodsButtons.push({
            text: '❰ К выбору города',
            callback: () => this.menuCity()
        })

        menuGoodsButtons.push(this._commonButtons.start({prev_message: prevMessage}))

        let tplData = {
            selected_items: this.selectedItems,
            product: this.product,
            goods: goodsOfcity
        }

        return this.app.render('showcase.choice_goods', tplData).then(content => {

            let menuGoodsScheme = {
                layout: this.layoutTwoColumns(goodsOfcity.length, 2),
                message: content,
                params: [{parse_mode: 'markdown'}],
                menu: menuGoodsButtons
            }

            return this.botScope.runInlineMenu(menuGoodsScheme, prevMessage)
        }).catch(err => this.app.logger.error(err))
    }

    menuWeightOfGoods(prevMessage) {

        let tplData = {
            product: this.product,
            selected_items: this.selectedItems
        }

        let products = this.product.getProduct(this.selectedItems.goods_id)

        // Menu choice weight of goods
        let menuWeightOfGoodsButtons = products.map(product => {
            return {
                text: `📦 ${this.product.weightForHumans(product.weight)} - 💰 ${product.cost}`,
                callback: (callbackQuery, message) => {
                    this.selectedItems.weight = product.weight

                    return this.menuCountPackage(message)
                }
            }
        })

        menuWeightOfGoodsButtons.push({
            text: '❰ К выбору товара',
            callback: () => {
                delete this.selectedItems.goods_id

                return this.menuGoods(prevMessage)
            }
        })
        menuWeightOfGoodsButtons.push({
            text: '❰ К выбору города',
            callback: () => this.menuCity()
        })
        menuWeightOfGoodsButtons.push(this._commonButtons.start({prev_message: prevMessage}))

        return this.app.render('showcase.choice_weight_of_goods', tplData).then(content => {

            let menuWeightOfGoodsScheme = {
                layout: this.layoutTwoColumns(products.length, 2, 1),
                message: content,
                params: [{parse_mode: 'markdown'}],
                menu: menuWeightOfGoodsButtons
            }

            return this.botScope.runInlineMenu(menuWeightOfGoodsScheme, prevMessage)
        }).catch(err => this.app.logger.error(err))
    }

    menuCountPackage(prevMessage, errorMessage) {

        if (errorMessage) delete this.selectedItems.count_package

        let tplData = {
            product: this.product,
            selected_items: this.selectedItems,
            errorMessage,
            count_package: 1
        }

        // Buttons count
        let menuCountPackageButtons = []

        for (let i = 1; i<=6; i++) {
            menuCountPackageButtons.push({
                text: `📦 ${i.toString()}`,
                callback: () => {
                    this.selectedItems.count_package = i

                    // Check count product
                    let products = this.product.getProduct(this.selectedItems.goods_id, this.selectedItems.weight)

                    if (!products.length)
                        return this.menuCountPackage(prevMessage, 'Нет товара или он кончился в самый неподходящий момент')
                    else if (products.length < i)
                        return this.menuCountPackage(prevMessage, 'Нет такого количества товара')

                    return this.menuPreOrder(prevMessage)
                }
            })
        }

        // Navigation buttons
        menuCountPackageButtons.push({
            text: '❰ К выбору веса',
            callback: () => {
                delete this.selectedItems.weight

                return this.menuWeightOfGoods(prevMessage)
            }
        })

        menuCountPackageButtons.push({
            text: '❰ К выбору товара',
            callback: () => {
                delete this.selectedItems.goods_id
                delete this.selectedItems.weight

                return this.menuGoods(prevMessage)
            }
        })

        menuCountPackageButtons.push({
            text: '❰ К выбору города',
            callback: () => {
                delete this.selectedItems.city_id
                delete this.selectedItems.goods_id
                delete this.selectedItems.weight

                return this.menuCity(prevMessage)
            }
        })

        menuCountPackageButtons.push(this._commonButtons.start({prev_message: prevMessage}))

        return this.app.render('showcase.choice_count_package', tplData).then(content => {

            let menuCountPackageScheme = {
                layout: [3, 3, 2, 2],
                message: content,
                params: [{parse_mode: 'markdown'}],
                menu: menuCountPackageButtons
            }

            return this.botScope.runInlineMenu(menuCountPackageScheme, prevMessage)
        }).catch(err => this.app.logger.error(err))
    }

    menuPreOrder(prevMessage) {

        let wordOrder = (count) => {
            if (count == 1)
                return 'заказ'
            else if (count > 1 && count <= 4)
                return 'заказа'

            return 'Заказов'
        }

        let tplData = {
            product: this.product,
            selected_items: this.selectedItems,
            count_package: this.selectedItems.count_package,
            word_order: wordOrder
        }

        let menuOrderButtons = [{
            text: `✅ Оформить ${this.selectedItems.count_package == 1 ? 'заказ' : 'заказы'}`,
            callback: () => {

            }
        }]

        menuOrderButtons.push({
            text: '❰ Выбрать другое кол-во',
            callback: () => {
                delete this.selectedItems.count_package

                return this.menuCountPackage(prevMessage)
            }
        })

        menuOrderButtons.push({
            text: '❰ К выбору веса',
            callback: () => {
                delete this.selectedItems.count_package
                delete this.selectedItems.weight

                return this.menuWeightOfGoods(prevMessage)
            }
        })

        menuOrderButtons.push({
            text: '❰ К выбору товара',
            callback: () => {
                delete this.selectedItems.count_package
                delete this.selectedItems.weight
                delete this.selectedItems.goods_id

                return this.menuGoods(prevMessage)
            }
        })

        menuOrderButtons.push(this._commonButtons.start({prev_message: prevMessage}))

        return this.app.render('showcase.pre_order', tplData).then(content => {

            let menuOrderScheme = {
                layout: [1, 2],
                message: content,
                params: [{parse_mode: 'markdown'}],
                menu: menuOrderButtons
            }

            return this.botScope.runInlineMenu(menuOrderScheme, prevMessage)
        })
    }
}

module.exports = ShowcaseMenu