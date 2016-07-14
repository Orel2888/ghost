$(function () {
    $('[data-toggle="tooltip"]').tooltip();
})

function notebook(formSelector, secret) {
    document.querySelector(formSelector).querySelector('button').onclick = function (e) {
        e.preventDefault();
    };
}