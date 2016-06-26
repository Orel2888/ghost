'use strict';

const assert   = require('chai').assert,
      GhostApi = require('../nodejs/ghost-api/GhostApi'),
      config   = require('../nodejs/config');

describe('Test GhostApi class', function () {

    this.timeout(30000);

    var data = {};

    var ghostApi = new GhostApi({
        apiKey: config.get('API_KEY')
    });

    /**
     * Users methdos
     */

    it ('Authentication user', () => {
        return ghostApi.authenticationUser().then(response => {
            console.log(response);
        });
    });


    /**
     * Admin methods
     */

    it ('Method users.reg', () => {
        return ghostApi.api('users.reg', 'POST', {
            name: 'Vasya',
            tg_username: 'vasya01',
            tg_chatid: 1234
        }).then(response => {
            console.log(response);
        });
    });

    it ('Method users.find', () => {
        return ghostApi.api('users.find', 'GET', {tg_chatid: 1234}).then(response => {
            console.log(response);
        });
    });

});