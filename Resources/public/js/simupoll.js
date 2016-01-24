(function () {
    'use strict';

    var $collectionHolder = $('ul.isel-proposition');
    var $addItemLink = $('<a href="#" class="add_item_link btn btn-info"><span class="fa fa-plus"></span> Ajouter une proposition</a>');
    var $newLink = $('<li></li>').append($addItemLink);

    // add a delete link to all of the existing Item form li elements
    $collectionHolder.find('li.proposition').each(function() {
        addItemFormDeleteLink(this);
    });

    // add the "add an item" anchor and li to the tags ul
    $collectionHolder.append($newLink);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addItemLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new proposition form
        addItemForm($collectionHolder, $newLink);
    });

    function addItemForm($collectionHolder, $newLink) {
        // Get the data-prototype
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        // Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add an Item" link li
        var $newFormLi = $('<li class="proposition"></li>').append(newForm);

        // also add a remove button, just for this example
        //$newFormLi.append('<a href="#" class="remove-item btn btn-danger">x</a>');

        $newLink.before($newFormLi);

        // add a delete link to the new form
        addItemFormDeleteLink($newFormLi);

        // handle the removal
        $('.remove-proposition').click(function(e) {
            e.preventDefault();
            $(this).parent().remove();
            return false;
        });
    }

    function addItemFormDeleteLink($itemFormLi) {
        var $removeFormA = $('<td><a href="#" class="remove-proposition btn btn-danger"><span class="fa fa-trash"></span></a></td>');
        $($itemFormLi).find("tr").append($removeFormA);

        $($removeFormA).find("a").on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $itemFormLi.remove();
        });
    }
}());