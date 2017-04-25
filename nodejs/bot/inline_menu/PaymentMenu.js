
const BaseMenu = require('./BaseMenu')
const PurseModel = require('../models/Purse')

class PaymentMenu extends BaseMenu {

    constructor(app, menuName, botScope, params) {
        super(...arguments)

        this.purseModel = new PurseModel(app, botScope)
    }

    run() {
        return this.purseModel.getPurse().then(phone => {
            return this.getPaymentPage({phone, comment: this.botScope.user.comment}).then(content => {
                return this.sendMainMenu(content)
            })
        }).catch(err => {
            return this.getPaymentPage({phone: null, comment: this.botScope.user.comment}).then(content => {
                return this.sendMainMenu(content)
            })
        })
    }

    getPaymentPage(vars) {
        return this.app.render('payment.index', vars).catch(err => {
            this.app.logger.error({payment_menu_get_payment_page: err})
        })
    }

    sendMainMenu(content) {

        let buttons = [
            this._commonButtons.lk(),
            this._commonButtons.orders('pending'),
            this._commonButtons.orders('successful'),
            this._commonButtons.help(),
            this._commonButtons.start(),
        ]

        let menuScheme = {
            layout: 2,
            message: content,
            params: [{parse_mode: 'markdown'}],
            menu: buttons
        }

        if (!this.params.prev_message) {
            menuScheme.method = 'sendMessage'
            menuScheme.params = [content, {parse_mode: 'markdown'}]
        }

        return this.botScope.runInlineMenu(menuScheme, this.params.prev_message)
    }
}

module.exports = PaymentMenu