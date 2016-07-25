'use strict';

const config    = require('../config');
const ghostApi  = require('../ghost-api/GhostApi');
const path      = require('path');

class Powers {

    constructor() {
        this.logger = console.log;

        this.ghostApi = null;

        if (!this.ghostApi) {
            this.ghostApi = new ghostApi({
                apiKey: config.get('API_KEY'),
                apiUrl: config.get('API_URL')
            });
        }
    }

    checkSessionAndAuthenticate(admin = false) {
        return this.ghostApi.checkAuth(admin).then(auth => {

            if (!auth) {
                return admin ? this.ghostApi.authenticationAdmin(admin) : this.ghostApi.authenticationUser();
            }

            return true;
        }).catch(this.logger)
    }
}

module.exports = Powers;