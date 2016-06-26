'use strict';

const request     = require('request-promise'),
      querystring = require('querystring');

class GhostApi {

    constructor(conf = {}) {

        this.request = request;
        this.accessTokenUser  = null;
        this.accessTokenAdmin = null;

        this.config = {
            apiUrl: 'http://ghost.projects.dev/api'
        };

        if (Object.keys(conf).length) {
            this.config = Object.assign(this.config, conf);
        }

        if (!this.config.apiKey) {
            throw new Error('Argument apiKey is required');
        }

    }

    api(apiMethod, method = 'GET', params = {}) {

        if (this.accessTokenUser || this.accessTokenAdmin) {
            params.access_token = apiMethod.match(/^admin/) ? this.accessTokenAdmin : this.accessTokenUser;
        }

        let options = {
            url: this.config.apiUrl + '/' + apiMethod,
            method,
            json: true
        };

        if (method == 'GET' && Object.keys(params).length) {
            options.url += '?' + querystring.stringify(params);
        }

        if (method == 'POST') {
            options.formData = params;
        }

        return request(options);
    }

    authentication(adminUsername = false) {
        return this.api(adminUsername ? 'authenticate/' + adminUsername : 'authenticate', 'POST', {key: this.config.apiKey}).then(response => {
            this[adminUsername ? 'accessTokenAdmin' : 'accessTokenUser'] = response.access_token;

            return response;
        });
    }

    authenticationUser() {
        return this.authentication();
    }

    authenticationAdmin(username) {
        return this.authentication(username);
    }
}

module.exports = GhostApi;