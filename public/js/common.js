
class App {

    constructor() {
        this.env            = 'local';
        this.debug          = true;
        this.tasks          = new Map();
        this.taskLoadedPage = new Map();
        this.$j             = $;

        try {
            jQuery;
        } catch (e) {
            console.log('jQuery not found');
        }
    }

    run() {

    }
}

class Tools extends App {

    constructor() {

    }
}

$(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );

    $( "#f_created_at_to" ).datepicker({
        dateFormat: "dd-mm-yy 00:00:00"
    });
    $( "#f_created_at_from" ).datepicker({
        dateFormat: "dd-mm-yy 23:59:59"
    });
});

function notebook(formSelector, secret) {
    document.querySelector(formSelector).querySelector('button').onclick = function (e) {
        e.preventDefault();

        var btnElement = this;

        btnElement.setAttribute('disabled', true);

        $.ajax({
            url: this.parentNode.getAttribute('action'),
            method: 'POST',
            data: {
                content: GibberishAES.enc(this.parentNode.querySelector('textarea').value, key)
            },
            success: function (response) {
                btnElement.removeAttribute('disabled');
            },
            error: function (err) {
                btnElement.setAttribute('class', 'btn btn-danger');

                console.log(err.responseText);
            }
        });
    };
}