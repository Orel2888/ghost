'use strict'

/**
 * Base menu
 */

class BaseMenu {

    constructor(app, menu, botScope, params) {
        this.app      = app
        this.menu     = menu
        this.botScope = botScope
        this.params   = params || {}

        this._commonButtons = {
            start: (params) => {
                return {
                    text: '❰ Главная',
                    callback: () => {
                        return this.app.includeMenu('Start', this.botScope, params).run()
                    }
                }
            }
        }
    }

    run() {
        throw new Error('Not implemented')
    }

    /**
     * No tested
     * @param requiredParams
     */
    canRequiredParams(requiredParams) {
        for (let paramName in requiredParams) {

            if (!this.params.hasOwnProperty(paramName) || !(this.params[paramName] instanceof requiredParams[paramName])) {
                let error = `Not required param ${paramName}`

                this.app.logger.error({BaseMenu: error})

                throw new Error(error)
            }
        }
    }

    /**
     * Create layout two column and end one
     * @param countItems
     * @param nextLayout
     * @param nextNums
     * @returns {Array}
     */
    layoutTwoColumns(countItems, nextLayout, ...nextNums) {
        let layout = []
        let i = 0;

        while (i < Math.floor(countItems / 2)) {
            layout.push(2)
            i++
        }

        if (countItems % 2 > 0) layout.push(1)

        if (typeof nextLayout == 'object' && nextLayout.length) {
            nextLayout.forEach(num => layout.push(num))
        } else {
            layout.push(nextLayout)
        }

        if (nextNums.length) nextNums.forEach(num => layout.push(num))

        return layout
    }
}

module.exports = BaseMenu