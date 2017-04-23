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

        const [usernick, username] = [
            this.botScope.message.from.firstName + (this.botScope.message.from.lastName ? ` ${this.botScope.message.from.lastName}` : ''),
            this.botScope.message.from.username
        ]

        let saveUserDataSession = (udata) => {

            // Set time activity
            udata.lastTimeActivity = Date.now()

            return this.botScope.setUserSession('user_data', udata).then(() => {

                this.userData = udata

                return udata
            })
        }

        let getUserData = () => {
            return this.find(this.botScope.userId).then(udata => {

                // Check is changed telegram data
                if (username != udata.tg_username || usernick != udata.name) {
                    // Update to back-end data
                    return this.update({
                        name: usernick,
                        tg_username: username,
                        tg_chatid: this.botScope.userId
                    }).then(res => this.find(this.botScope.userId).then(saveUserDataSession))
                }

                return saveUserDataSession(udata)
            }).catch(err => {

                // Registration a user
                if (err.statusCode == 404) {
                    return this.register({
                        name: usernick,
                        tg_username: username,
                        tg_chatid: this.botScope.userId
                    }).then(() => {
                        return getUserData()
                    })
                }

                this.app.logger.error({user_model_get_user_data: err})

                return err;
            })
        }

        if (forceUpdate) {
            return getUserData()
        }

        return this.botScope.getUserSession('user_data').then(udata => {

            if (!Object.keys(this.userData).length && Object.keys(udata).length) {

                // Set time activity
                udata.lastTimeActivity = Date.now()

                this.userData = udata

                // Refresh last time to session and return user data
                return this.botScope.setUserSession('user_data').then(() => {
                    return udata
                })
            }

            return getUserData()
        })

    }

    find(userId) {
        return this.app.api.api('users.find', 'GET', {tg_chatid: userId})
            .then(response => response.data)
    }

    update(attributes) {
        return this.app.api.api('users.update', 'POST', attributes)
    }

    register(attributes) {
        return this.app.api.api('users.reg', 'POST', attributes)
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