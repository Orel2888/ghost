'use strict';

const fs   = require('fs-jetpack'),
      path = require('path'),
      os   = require('os');

var config = new Map();

var ik = fs.read(path.join(__dirname, '../.env')).split(os.EOL).map((item, index) => {
    if (!item.trim()) return;

    let split = item.match(/^([^=].+?)\=(.*)/i);

    var replacer = val => {

        switch (val) {
            case 'null':
                val = null;
            break;
            case 'true':
                val = true;
            break;
            case 'false':
                val = false;
            break;
        }

        return val;
    };

    return [split[1].trim(), replacer(split[2].trim())];
}).filter(item => item).forEach((item, index) => {
    config.set(item[0], item[1]);
});

module.exports = config;