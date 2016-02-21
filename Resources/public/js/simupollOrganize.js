(function () {
    'use strict';

    //add a period
    $('#period_content').on('click', '.period-add-btn', function () {
        var idperiod = $(this).data('id');
        var idsimupoll = $(this).data('sid');

        modal.displayForm(
            Routing.generate('cpasimusante_simupoll_period_add_form', {'idperiod': idperiod, 'idsimupoll':idsimupoll}),
            refreshPage,
            function() {}
        );
    });
}());