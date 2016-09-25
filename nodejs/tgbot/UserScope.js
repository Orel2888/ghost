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

            // User a data from db
            let udata = response.data;

            // Set user a data to session - userSession.udata
            scope.userSession.udata = udata;

            // Update users data to db
            let fromUser = scope._message._from;
            let tgUsernick = scope._message._from._firstName + (scope._message._from.lastName ? ' '+ scope._message._from.lastName : '');

            if (udata.name != tgUsernick || udata.tg_username != scope._message._from._username) {
                // Request method api for update user data to db
                this.update({
                    tg_chatid: fromUser._id,
                    tg_username: fromUser._username,
                    comment: fromUser._username,
                    name: tgUsernick
                }).then(response => {
                    console.log('User a data updated');
                }).catch(err => {
                    console.log('Error update user a data');
                });
            }

            // Set time activity to user
            scope.userSession.lastTimeActivity = Date.now();

            // Return and save user data to session
            return scope.userSession.udata = udata;
        }).catch(err => Promise.reject(err));
    }

    /**
     * Update user a data to db
     * Find user by attributes (tg_chatid)
     * @param attributes
     * @returns {*}
     */
    update(attributes) {
        // Request method api for update user data to db
        return this.ghostApi.api('users.update', 'POST', attributes);
    }
}

module.exports = UserScope;