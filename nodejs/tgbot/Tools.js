
const swig = require('swig');
const path = require('path');
const emoji = require('node-emoji');

class Tools {

    constructor() {

        this.emojiColletion = {
            1: emoji.emojify(':eyes:'),
            2: emoji.emojify(':point_right:'),
            3: emoji.emojify(':house_with_garden:'),
            4: emoji.emojify(':gift:'),
            5: emoji.emojify(':package:'),
            6: emoji.emojify(':dollar:'),
            7: emoji.emojify(':point_up:')
        };

        this.nvgCommands = {
            'In start': `В начало ${this.emojiColletion[2]} /start`,
            'myorder': `Мои заказы ${this.emojiColletion[2]} /myorder`,
            'refreshMyProfile': `Обновить ${this.emojiColletion[2]} /myprofile`
        };

    }

    render(pathFile, data = {}) {
        swig.setDefaults({autoescape: false});

        let tpl = swig.compileFile(path.join(__dirname, 'views', pathFile + '.html'));

        data = Object.assign(data, {
            emojis: this.emojiColletion,
            nvg: data.hasOwnProperty('nvg') ? data.nvg.map(cmd => this.nvgCommands[cmd]).join('\n') : []
        });

        return tpl(data).replace(/\\n/g, '\n');
    }
}

module.exports = Tools;