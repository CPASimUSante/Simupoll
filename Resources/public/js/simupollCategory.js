(function () {
    'use strict';

    var modal = window.Claroline.Modal;

    //add a category
    $('#category_content').on('click', '.category-add-btn', function () {
        var idcategory = $(this).data('id');
        modal.displayForm(
            Routing.generate('cpasimusante_simupoll_category_add_form', {'idcategory': idcategory}),
            refreshPage,
            function() {}
        );
    });

    //remove a category
    $('#category_content').on('click', '.category-delete-btn', function (event) {
        var idcategory = $(this).data('id');

        event.preventDefault();
        modal.confirmRequest(
            Routing.generate('cpasimusante_simupoll_category_delete_form', {'idcategory': idcategory}),    //url
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