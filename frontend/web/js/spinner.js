
var spinner = (function () {
    var spinnerHtml =
        $('<div class="modal fade" id="spinner" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-body">' +
            '<h5 align="center"><div class="spinner_block"></div>Пожалуйста, подождите...</h5>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');

    return {
        start: function start() {
            spinnerHtml.modal('show');
            // setTimeout(function () {
            //     spinnerHtml.modal('hide');
            // }, 30000);
        },
        stop: function stop() {
            spinnerHtml.modal('hide');
        }
    };
})();
var forms = document.querySelector('form');
forms.addEventListener('submit', function(event){

    $(document).on('afterValidate', 'form', function (event, messages, errors) {
        if (errors.length != 0) {
            spinner.stop();
            return;
        } else {
            spinner.start();
            return;
        }
    });
});
