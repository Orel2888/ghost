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

    it ('Check authenticate', () => {
        return ghostApi.checkAuth().then(auth => {
            assert.equal(auth, 0);
        });
    });


    /**
     * Users methdos
     */

    it ('Authentication user', () => {
        return ghostApi.authenticationUser().then(response => {
            console.log(response);
        });
    });

    it ('Check authorization after authenticate', () => {
        return ghostApi.checkAuth().then(assert.isTrue);
    });

    it ('Method users.reg', () => {
        return ghostApi.api('users.reg', 'POST', {
            name: 'Vasya',
            tg_username: 'vasya01',
            tg_chatid: 1234
        }).then(response => {
            console.log(response);
        }).catch(err => {
            assert.equal(err.response.body.message, 'Client is already registered');
        });
    });

    it ('Method users.find', () => {
        return ghostApi.api('users.find', 'GET', {tg_chatid: 1234}).then(response => {
            console.log(response);
        });
    });

    /**
     * Admin methods
     */

    it ('Authentication admin', () => {
        return ghostApi.authenticationAdmin('vasechka').then(response => {
            console.log(response);
        });
    });

    it ('Method admin/qiwi-transaction', () => {
        return ghostApi.api('admin/qiwi-transaction').then(response => {

        });
    });

    it ('Method admin/goods-price', () => {
        return ghostApi.api('admin/goods-price').then(response => {
            console.log(response);
        });
    });
});