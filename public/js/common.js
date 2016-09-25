
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

    // Select date and paste to input
    $('#btn-group-select button').click(function (e) {
        e.preventDefault();

        let datePeriodId = $(this).attr('data-period');

        let currentDate = new Date();

        let datePeriod = {
            1: [
                [`${currentDate.getDay()}-${currentDate.getMonth() + 1}-${currentDate.getFullYear()} 00:00:00`],
                [`${currentDate.getDay()}-${currentDate.getMonth() + 1}-${currentDate.getFullYear()} 23:59:59`]
            ]
        };

        $('#f_created_at_to').val(datePeriod[datePeriodId][0]);
        $('#f_created_at_from').val(datePeriod[datePeriodId][1]);
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