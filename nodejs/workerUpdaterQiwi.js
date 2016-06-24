'use strict';

const CheckingBalance = require('qiwimas/lib/CheckingBalance'),
      config          = require('./config'),
      fs              = require('fs-jetpack'),
      path            = require('path');

const fileWorkerStatus = path.join(__dirname, '../storage/node/worker_updater_qiwi.txt');

fs.write(fileWorkerStatus, 'worked');
console.log('Worker is start');

const intervalStatusWorker = 15;

let checkingBalance = new CheckingBalance({
    qiwi: {
        phone : config.get('QIWI1_PHONE'),
        pass  : config.get('QIWI1_PASS')
    },
    database: {
        host      : config.get('DB_HOST'),
        database  : config.get('DB_DATABASE'),
        user      : config.get('DB_USERNAME'),
        password  : config.get('DB_PASSWORD')
    }
});

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