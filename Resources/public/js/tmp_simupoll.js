(function () {
    'use strict';

    //question management
    var $collectionQuestionHolder = $('ul.groupquestion');
    var $addQuestionLink = $('<a href="#" class="add_question_link btn btn-info"><span class="fa fa-plus"></span> Ajouter une question</a>');
    var $newQuestionLink = $('<li></li>').append($addQuestionLink);

    // add a delete link to all of the existing Question form li elements
    $collectionQuestionHolder.find('li.question').each(function() {
        addQuestionFormDeleteLink(this);
    });

    // add the "add a question" anchor and li to the tags ul
    $collectionQuestionHolder.append($newQuestionLink);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new question (e.g. 2)
    $collectionQuestionHolder.data('index', $collectionQuestionHolder.find(':input').length);

    var $collectionPropositionHolder;
    var $addPropositionLink;
    var $newPropositionLink;

    $collectionQuestionHolder.on('click', '.add_question_link', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        var index = $collectionQuestionHolder.data('index');
console.log(index);

        //Proposition management
        $collectionPropositionHolder = $('#liquestion'+index).find('.groupproposition');
console.log($('#liquestion'+index));
        $addPropositionLink = $('<a href="#" class="add_proposition_link btn btn-info"><span class="fa fa-plus"></span> Ajouter une proposition</a>');
        $newPropositionLink = $('<li></li>').append($addPropositionLink);

        // add a new proposition form
        addQuestionForm($collectionQuestionHolder, $newQuestionLink);

        // add a delete link to all of the existing Proposition form li elements
        $collectionPropositionHolder.find('li.proposition').each(function() {
            addPropositionFormDeleteLink(this);
        });
console.log($collectionPropositionHolder);
        // add the "add an item" anchor and li to the tags ul
        $collectionPropositionHolder.append($newPropositionLink);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionPropositionHolder.data('index', $collectionPropositionHolder.find(':input').length);

        $addPropositionLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new proposition form
            addPropositionForm($collectionPropositionHolder, $newPropositionLink);
        });
    });

    function addQuestionFormDeleteLink($questionFormLi) {
        var $removeFormA = $('<tr><td><a href="#" class="remove-question btn btn-danger"><span class="fa fa-trash"></span> Remove this question</a></td></tr>');
        $($questionFormLi).find("tr:last").after($removeFormA);

        $($removeFormA).find("a").on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $questionFormLi.remove();
        });
    }

    function addQuestionForm($collectionQuestionHolder, $newQuestionLink) {
        //1 - Get the data-prototype
        var prototype = $collectionQuestionHolder.data('prototype');

        //2 - get the new index
        var index = $collectionQuestionHolder.data('index');

        //3 - Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on how many propositions we have
        var newForm = prototype.replace(/__name__/g, index);

        //4 - increase the index with one for the next queston
        $collectionQuestionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a Proposition" link li
        var $newQuestionFormLi = $('<li class="question" id="liquestion'+index+'"></li>').append(newForm);

        // also add a remove button, just for this example
        //$newQuestionFormLi.append('<a href="#" class="remove-question btn btn-danger">x</a>');

        $newQuestionLink.before($newQuestionFormLi);

        // add a delete link to the new form
        addQuestionFormDeleteLink($newQuestionFormLi);

        // handle the removal
        $('.remove-question').click(function(e) {
            e.preventDefault();
            $(this).parent().remove();
            return false;
        });
    }

    function addPropositionForm($collectionPropositionHolder, $newPropositionLink) {
        // Get the data-prototype
        var prototype = $collectionPropositionHolder.data('prototype');

        // get the new index
        var index = $collectionPropositionHolder.data('index');

        // Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on how many propositions we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next proposition
        $collectionPropositionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a Proposition" link li
        var $newPropositionFormLi = $('<li class="proposition"></li>').append(newForm);

        // also add a remove button, just for this example
        //$newPropositionFormLi.append('<a href="#" class="remove-item btn btn-danger">x</a>');

        $newPropositionLink.before($newPropositionFormLi);

        // add a delete link to the new form
        addPropositionFormDeleteLink($newPropositionFormLi);

        // handle the removal
        $('.remove-proposition').click(function(e) {
            e.preventDefault();
            $(this).parent().remove();
            return false;
        });
    }

    function addPropositionFormDeleteLink($propositionFormLi) {
        var $removeFormA = $('<td><a href="#" class="remove-proposition btn btn-danger"><span class="fa fa-trash"></span></a></td>');
        $($propositionFormLi).find("tr").append($removeFormA);

        $($removeFormA).find("a").on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $propositionFormLi.remove();
        });
    }
}());