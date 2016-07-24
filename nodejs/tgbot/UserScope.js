'use strict'

class UserScope {

    constructor(Powers) {
        this.powers     = Powers;
        this.ghostApi   = Powers.ghostApi;
    }

    findUser(clientId, scope) {
        let udata = scope.userSession.udata;

        return udata ? Promise.resolve(udata) : this.ghostApi.api('users.find', 'GET', {
            tg_chatid: clientId
        }).then(response => {
            return scope.userSession.udata = response.data;
        }).catch(err => Promise.reject(err));
    }
}

module.exports = UserScope;