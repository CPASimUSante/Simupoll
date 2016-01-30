(function () {
    'use strict';
    //add a category
    $('#category_content').on('click', '.category-add-btn', function () {
        var idcategory = $(this).data('id');
        window.Claroline.Modal.displayForm(
            Routing.generate('cpasimusante_simupoll_category_add_form', {'idcategory': idcategory}),
            refreshPage,
            function() {}
        );
    });
    //remove a category
    $('#category_content').on('click', '.category-delete-btn', function () {
        var idcategory = $(this).data('id');
        window.Claroline.Modal.displayForm(
            Routing.generate('cpasimusante_simupoll_category_delete_form', {'idcategory': idcategory}),
            refreshPage,
            function() {}
        );
    });
    var refreshPage = function () {
        window.location.reload();
    };
}());