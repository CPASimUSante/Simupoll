(function () {
    'use strict';

    var modal = window.Claroline.Modal;

    //modify a category
    $('#category_content').on('click', '.category-modify-btn', function () {
        var cid = $(this).data('id');
        var sid = $(this).data('sid');
        modal.displayForm(
            Routing.generate('cpasimusante_simupoll_category_modify', {'cid': cid, 'sid':sid}),
            refreshPage,
            function() {}
        );
    });

    //add a category
    $('#category_content').on('click', '.category-add-btn', function () {
        var cid = $(this).data('id');
        var sid = $(this).data('sid');

        modal.displayForm(
            Routing.generate('cpasimusante_simupoll_category_add_form', {'cid': cid, 'sid':sid}),
            refreshPage,
            function() {}
        );
    });

    //remove a category
    $('#category_content').on('click', '.category-delete-btn', function (event) {
        var cid = $(this).data('id');
        var sid = $(this).data('sid');
        event.preventDefault();
        modal.confirmRequest(
            Routing.generate('cpasimusante_simupoll_category_delete_form', {'cid': cid}),    //url
            refreshPage,                                                              //successHandler
            undefined,                                                                //successParameter
            Translator.trans('category_delete_confirm', {}, 'resource'),              //content
            Translator.trans('category_delete', {}, 'resource')                       //title
        );
    });

    var refreshPage = function () {
        window.location.reload();
    };
}());
