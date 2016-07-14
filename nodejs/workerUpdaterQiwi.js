'use strict';

const CheckingBalance = require('qiwimas/lib/CheckingBalance'),
      config          = require('./config'),
      fs              = require('fs-jetpack'),
      path            = require('path');

const fileWorkerStatus = path.join(__dirname, '../storage/node/worker_updater_qiwi.txt');
const purse = fs.read(path.join(__dirname, '../storage/node/purse.txt')).split('|').map(item => item.trim());

fs.write(fileWorkerStatus, 'worked');
console.log('Worker is start');

const intervalStatusWorker = 15;

let checkingBalance = new CheckingBalance({
    qiwi: {
        phone : purse[0],
        pass  : purse[1]
    },
    database: {
        host      : config.get('DB_HOST'),
        database  : config.get('DB_DATABASE'),
        user      : config.get('DB_USERNAME'),
        password  : config.get('DB_PASSWORD')
    }
});

checkingBalance.qiwiMaster.pathFileCookie = path.join(__dirname, '../storage/node/qiwi_cookies');

checkingBalance.updaterBalance((err, balance, changed) => {
    if (err) throw err;

    if (changed) {
        checkingBalance.updaterTransactions().then(countAdded => {

        }).catch(err => {
            throw err;
        });
    }
});

var checkWorkerStatus = setInterval(() => {
    if (fs.read(fileWorkerStatus) == 'stopped') {
        checkingBalance.updaterBalanceDisable();

        console.log('Worker is stopped');
        clearInterval(checkWorkerStatus);
    }
}, intervalStatusWorker * 1000);