'use strict';

const CheckingBalance = require('qiwimas/lib/CheckingBalance'),
      config          = require('./config'),
      fs              = require('fs-jetpack'),
      path            = require('path'),
      GhostApi        = require('./ghost-api/GhostApi');

const ghostApi = new GhostApi({
    apiKey: config.get('API_KEY'),
    apiUrl: config.get('API_URL')
});

const systemUser = 'system44612';

const fileWorkerStatus = path.join(__dirname, '../storage/node/worker_updater_qiwi.txt');

var getPurse = () => {
    let purse = fs.read(path.join(__dirname, '../storage/node/purse.txt')).split('|').map(item => item.trim());

    return purse;
};

let purse = getPurse();

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

    // Ghost api authentication
    let ghostAuth = () => {
        return ghostApi.checkAuth('system44612').then(auth => {
            return auth ? true : ghostApi.authenticationAdmin('system44612');
        }).catch(console.log)
    }

    // Changes purse
    let currentPurse = getPurse();

    if (currentPurse[0] != checkingBalance.qiwiMaster.currentPurse()) {
        checkingBalance.qiwiMaster.changePurse({
            phone: currentPurse[0],
            pass: currentPurse[1]
        });
    }

    // If changed balance, update transactions
    if (changed) {
        checkingBalance.updaterTransactions().then(countAdded => {
            // If is exists new transaction call request api on processing a orders
            if (countAdded > 0) {
                ghostApi.checkAuth(systemUser).then(auth => {
                    return auth ? true : ghostApi.authenticationAdmin(systemUser);
                }).then(() => {
                    ghostApi.api('sys.processing_goods_orders').then(response => {
                        console.log(response.data)
                    }).catch(console.log)
                }).catch(console.log)
            }
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