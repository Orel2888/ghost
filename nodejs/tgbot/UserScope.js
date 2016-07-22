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
        }).catch(err => {
            console.log(err)

            scope.sendMessage('Произошла ошибка')

            return err;
        });
    }
}

module.exports = UserScope;