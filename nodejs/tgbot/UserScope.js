'use strict';

class UserScope {

    constructor(Container) {
        this.container  = Container;
        this.powers     = this.container.make('Powers');
        this.ghostApi   = this.powers.ghostApi;
    }

    findUser(clientId, scope) {
        let udata = scope.userSession.udata;

        return udata ? Promise.resolve(udata) : this.ghostApi.api('users.find', 'GET', {
            tg_chatid: clientId
        }).then(response => {

            // Set time activity to user
            scope.userSession.lastTimeActivity = Date.now();

            return scope.userSession.udata = response.data;
        }).catch(err => Promise.reject(err));
    }
}

module.exports = UserScope;