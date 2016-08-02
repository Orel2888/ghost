
const swig  = require('swig');
const path  = require('path');
const emoji = require('node-emoji');
const promisify = require("promisify-node");

class Tools {

    constructor() {

        this.emojiColletion = {
            1: emoji.emojify(':eyes:'),
            2: emoji.emojify(':point_right:'),
            3: emoji.emojify(':house_with_garden:'),
            4: emoji.emojify(':gift:'),
            5: emoji.emojify(':package:'),
            6: emoji.emojify(':dollar:'),
            7: emoji.emojify(':point_up:'),
            8: emoji.emojify(':bowling:'),
            9: emoji.emojify(':white_check_mark:'),
            10: emoji.emojify(':warning:'),
            11: emoji.emojify(':page_facing_up:'),
            12: emoji.emojify(':speech_balloon:'),
            13: emoji.emojify(':date:'),
            14: emoji.emojify(':running:')
        };

        this.nvgCommands = {
            'in start': `В начало ${this.emojiColletion[2]} /start`,
            'myorder': `Мои заказы ${this.emojiColletion[2]} /myorder`,
            'refreshMyProfile': `Обновить ${this.emojiColletion[2]} /myprofile`,
            'myprofile': `Мой профиль ${this.emojiColletion[2]} /myprofile`,
            'refreshMyOrder': `Обновить ${this.emojiColletion[2]} /myorder`
        };

    }

    render(pathFile, data = {}) {

        // Template for confirm the action
        if (pathFile == 'confirm') {
            pathFile = 'main/confirm';
        }

        swig.setDefaults({autoescape: false});

        let compileFile = promisify(swig.compileFile);

        data = Object.assign(data, {
            emojis: this.emojiColletion,
            nvg: data.hasOwnProperty('nvg') ?
                 data.nvg.map(cmd => this.nvgCommands[cmd] ? this.nvgCommands[cmd] : cmd).join('\n')
                 : []
        });

        return compileFile(path.join(__dirname, 'views', pathFile + '.html'), null).then(tpl => tpl(data));
    }
}

module.exports = Tools;