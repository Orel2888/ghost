/**
 * Purse
 **/

class Purse {

    constructor(app, botScope) {
        this.app      = app
        this.botScope = botScope
    }

    getPurse() {
        return this.app.api.api('users.purse', 'GET').then(response => {
            return response.data.phone
        })
    }
}

module.exports = Purse