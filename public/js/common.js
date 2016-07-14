$(function () {
    $('[data-toggle="tooltip"]').tooltip();
})

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