(function () {
    'use strict';

    var modal = window.Claroline.Modal;

    //modify a period
    $('#period_content').on('click', '.period-modify-btn', function () {
        var idperiod = $(this).data('id');
        var sid = $(this).data('sid');
        modal.displayForm(
            Routing.generate('cpasimusante_simupoll_period_modify', {'idperiod': idperiod, 'sid':sid}),
            refreshPage,
            function() {}
        );
    });

    //add a period
    $('#period_content').on('click', '.period-add-btn', function () {
        var idperiod = $(this).data('id');
        var sid = $(this).data('sid');

        modal.displayForm(
            Routing.generate('cpasimusante_simupoll_period_add_form', {'idperiod': idperiod, 'sid':sid}),
            refreshPage,
            function() {}
        );
    });

    //remove a period
    $('#period_content').on('click', '.period-delete-btn', function (event) {
        var idperiod = $(this).data('id');
        var sid = $(this).data('sid');
        event.preventDefault();
        modal.confirmRequest(
            Routing.generate('cpasimusante_simupoll_period_delete_form', {'idperiod': idperiod}),    //url
            refreshPage,                                                              //successHandler
            undefined,                                                                //successParameter
            Translator.trans('period_delete_confirm', {}, 'resource'),              //content
            Translator.trans('period_delete', {}, 'resource')                       //title
        );
    });

    var refreshPage = function () {
        window.location.reload();
    };
}());
