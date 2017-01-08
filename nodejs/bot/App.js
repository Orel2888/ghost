'use strict'

/**
 * Base application
 */

const path = require('path')

class App {

    constructor(config) {
        this.pathControllers = './controllers'
        this.mapControllers  = []
        this._controllers   = {}

        if (Object.keys(config).length) {
            for (let k in config) {
                this[k] = config[k]
            }
        }

        this.bootstrapControllers()
    }

    bootstrapControllers() {

        if (!this.mapControllers.length) {
            throw new Error('Not controllers for bootstrap')
        }

        this.mapControllers.forEach(controller => {
            try {
                let Controller = require(this.pathControllers + '/' + controller)

                this._controllers[Controller.name] = new Controller(this)
            } catch (e) {
                console.log(e)
                throw new Error(`Error load controller ${controller}`)
            }
        })
    }

    getController(name) {

        if (!this._controllers.hasOwnProperty(name)) {
            throw new Error(`Error get controller ${name}`)
        }

        return this._controllers[name]
    }
}

module.exports = App