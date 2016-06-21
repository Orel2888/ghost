'use strict';

const assert = require('chai').assert;

describe('Test config', function () {

    var config = require('../nodejs/config');

    it ('Read config', () => {
        assert(config.size > 0);
    });

});