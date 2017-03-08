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
        /**
         * Instances a controllers
         * @type {{}}
         * @private
         */
        this._controllers    = {}
        this.templatter      = null
        this.logger          = null

        if (Object.keys(config).length) {
            for (let k in config) {
                this[k] = config[k]
            }
        }

        this.api = null

        this._bootstrapControllers()
        this._bootstrapTemplatter()
        this._bootstrapApi()

        this._templateShareData = {
            divider: this.config.divider,
            space: ' '.repeat(4)
        }
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
                this.logger.error(e)
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

        if (Object.keys(data).length)
            data = Object.assign(this._templateShareData, data)
        else
            data = this._templateShareData

        if (templateFile.indexOf('.') != -1) {
            templateFile = templateFile.replace('.', '/');
        }

        return this.templatter(`${__dirname}/views/${templateFile}.html`, null).then(tpl => tpl(data))
    }

    getAdminUsernames() {
        return this.config.TGBOT_ADMINS.split(',')
    }

    includeMenu(menuName, botScope, params) {
        const className    = menuName.split('').map((s, index) => index == 0 ? s.toUpperCase() : s).join('') + 'Menu'
        const pathLoadMenu = `./${this.config.bot_mode}/${className}`

        try {
            var Menu = require(pathLoadMenu)
        } catch (e) {
            this.logger.error({error_app_include_menu: e});
            throw new Error(`Error load menu ${menuName}`)

            return
        }

        return new Menu(this, menuName, botScope, params)
    }
}

module.exports = App