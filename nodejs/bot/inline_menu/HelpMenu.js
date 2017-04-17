'use strict'

const BaseMenu = require('./BaseMenu')

class HelpMenu extends BaseMenu {

    constructor(app, menuName, botScope, params) {
        super(...arguments)
    }

    run() {
        return this.getHelpPage().then(content => {
            return this.sendMainMenu(content)
        })
    }

    getHelpPage() {
        return this.app.render('help.index').catch(err => {
            this.app.logger.error({help_menu_get_help_page: err})

            return this.botScope.sendMessage('Произошла ошибка')
        })
    }

    sendMainMenu(content) {

        let buttons = [
            this._commonButtons.lk(),
            this._commonButtons.orders('pending'),
            this._commonButtons.orders('successful'),
            this._commonButtons.start()
        ]

        let menuScheme = {
            layout: 2,
            message: content,
            params: [{parse_mode: 'markdown'}],
            menu: buttons
        }

        return this.botScope.runInlineMenu(menuScheme, this.params.prev_message)
    }
}

module.exports = HelpMenu