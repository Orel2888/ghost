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

    api(apiMethod, method = 'GET', params = {}, requestOptions = {}) {

        if (!params.hasOwnProperty('access_token') && /^admin/.test(apiMethod) && this.accessTokenAdmin) {
            params.access_token = this.accessTokenAdmin;
        } else {
            if (!params.hasOwnProperty('access_token') && this.accessTokenUser) {
                params.access_token = this.accessTokenUser;
            }
        }

        var options = {
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

        if (Object.keys(requestOptions).length) {
            options = Object.assign(options, requestOptions);
        }

        return request(options);
    }

    checkAuth(admin = false) {

        if (admin && !this.accessTokenAdmin || !admin && !this.accessTokenUser) {
            return new Promise((resolve, reject) => {
                resolve(false)
            })
        }

        return this.api('authenticate/check-access-token', 'POST', {
            access_token: this.accessTokenUser ? this.accessTokenUser : this.accessTokenAdmin
        }).then(response => {
            return response.status == 'ok';
        });
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