'use strict'

/**
 * Base application
 */

const path = require('path')
const swig  = require('swig')
const promisify = require("promisify-node")
const GhostApi  = require('../ghost-api/GhostApi')

class App {

    constructor(config) {
        this.pathControllers = './controllers'
        this.mapControllers  = []
        this._controllers    = {}
        this.templatter      = null

        if (Object.keys(config).length) {
            for (let k in config) {
                this[k] = config[k]
            }
        }

        this.api = null

        this._bootstrapControllers()
        this._bootstrapTemplatter()
        this._bootstrapApi()
    }

    _bootstrapControllers() {

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

    _bootstrapTemplatter() {
        swig.setDefaults({autoescape: false})

        this.templatter = promisify(swig.compileFile)
    }

    _bootstrapApi() {
        return this.api = new GhostApi({
            apiKey: this.config.API_KEY,
            apiUrl: this.config.API_URL
        })
    }

    getController(name) {

        if (!this._controllers.hasOwnProperty(name)) {
            throw new Error(`Error get controller ${name}`)
        }

        return this._controllers[name]
    }

    render(templateFile, data = {}) {

        if (templateFile.indexOf('.') != -1) {
            templateFile = templateFile.replace('.', '/');
        }

        return this.templatter(`${__dirname}/views/${templateFile}.html`, null).then(tpl => tpl(data))
    }

    getAdminUsernames() {
        return this.config.TGBOT_ADMINS.split(',')
    }
}

module.exports = App