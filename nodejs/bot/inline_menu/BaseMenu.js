'use strict'

/**
 * Base menu
 */

class BaseMenu {

    constructor(app, menu, botScope, params) {
        this.app      = app
        this.menu     = menu
        this.botScope = botScope
        this.params   = params
    }

    run() {
        throw new Error('Not implemented')
    }
}

module.exports = BaseMenu