/**
 * User model
 */

class User {

    /**
     * @param botScope {Scope}
     */
    constructor(app, botScope) {
        this.botScope = botScope
        this.app      = app
        this.userData = {}
    }

    load(forceUpdate = false) {

        let getUserDataSaveSession = () => {
            return this.find(this.botScope.userId).then(udata => {
                return this.botScope.setUserSession('user_data', udata).then(() => {

                    this.userData = udata

                    return udata
                })
            })
        }

        if (forceUpdate) {
            return getUserDataSaveSession()
        }

        return this.botScope.getUserSession('user_data').then(udata => {
            return Object.keys(udata).length ? udata : getUserDataSaveSession()
        })

    }

    find(userId) {
        return this.app.api.api('users.find', 'GET', {tg_chatid: userId})
            .then(response => response.data)
            .catch(err => this.app.logger.error(err))
    }

    get userId() {
        return this.userData.id
    }

    get name() {
        return this.userData.name
    }

    get telegramUsername() {
        return this.userData.tg_username
    }

    get rating() {
        return this.userData.rating
    }

    get balance() {
        return this.userData.balance
    }

    get countPurchases() {
        return this.userData.count_purchases
    }

    get comment() {
        return this.userData.comment
    }

    get notify() {
        return this.userData.notify
    }

    get createdAt() {
        return this.userData.created_at
    }

    get updatedAt() {
        return this.userData.updated_at
    }
}

module.exports = User