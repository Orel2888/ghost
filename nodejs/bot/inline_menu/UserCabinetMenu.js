
const BaseMenu = require('./BaseMenu')

class UserCabinetMenu extends BaseMenu {

    constructor(app, menuName, botScope, params) {
        super(...arguments)
    }

    run() {
        return this.botScope.user.load(true).then(udata => {
            return this.getUserPage(udata).then(content => {
                return this.sendMainMenu(content)
            })
        })
    }

    getUserPage(udata) {
        return this.app.render('user.profile', {udata})
            .catch(err => {
                this.app.logger.error({user_cabinet_get_user_page: err})

                return this.botScope.sendMessage('Произошла ошибка при открытии профиля')
            })
    }

    sendMainMenu(content) {

        let buttons = [
            this._commonButtons.showcase(),
            this._commonButtons.payment(),
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

module.exports = UserCabinetMenu