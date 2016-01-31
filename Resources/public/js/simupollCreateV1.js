(function () {
    'use strict';

    //question management
    var $collectionQuestionHolder = $('ul.groupquestion');
    var $addQuestionLink = $('<a href="#" class="add_question_link btn btn-info"><span class="fa fa-plus"></span> '+Translator.trans('question_add', {}, 'resource') +'</a>');
    var $newQuestionLink = $('<li></li>').append($addQuestionLink);

    // add the "add a question" anchor and li to the tags ul
    $collectionQuestionHolder.append($newQuestionLink);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new question (e.g. 2)
    $collectionQuestionHolder.data('index', $collectionQuestionHolder.find(':input').length);
/*
    var indexSavedQ = addQuestionForm($collectionQuestionHolder, $newQuestionLink);
    var $savedCollectionPropositionHolder = $('#liquestion'+indexSavedQ).find('.groupproposition');
    var $addSavedPropositionLink = $('<a href="#" class="add_proposition_link btn btn-info"><span class="fa fa-plus"></span> '+Translator.trans('proposition_add', {}, 'resource')+'</a>');
    var $newSavedPropositionLink = $('<li></li>').append($addSavedPropositionLink);
    $savedCollectionPropositionHolder.append($newSavedPropositionLink);
*/
    //add a new question
    $collectionQuestionHolder.on('click', '.add_question_link', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new proposition form
        var indexQ = addQuestionForm($collectionQuestionHolder, $newQuestionLink);

        //Proposition management
        var $collectionPropositionHolder = $('#liquestion'+indexQ).find('.groupproposition');
        var $addPropositionLink = $('<a href="#" class="add_proposition_link btn btn-info"><span class="fa fa-plus"></span> '+Translator.trans('proposition_add', {}, 'resource')+'</a>');
        var $newPropositionLink = $('<li></li>').append($addPropositionLink);

        // add the "add a proposition" anchor and li to the tags ul
        $collectionPropositionHolder.append($newPropositionLink);

        //add a new proposition
        $addPropositionLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new proposition form
            addPropositionForm($collectionPropositionHolder, $newPropositionLink);
        });

    });

    // add a delete link to all of the existing questions form li elements
    $collectionQuestionHolder.find('li.question').each(function() {
        addQuestionFormDeleteLink(this);
    });

    //add question button
    function addQuestionForm($collectionQuestionHolder, $newQuestionLink) {
        //1 - Get the data-prototype
        var prototype = $collectionQuestionHolder.data('prototype');

        //2 - get the new index
        var indexQ = $collectionQuestionHolder.data('index');
console.log($collectionQuestionHolder);
        //3 - Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on how many propositions we have
        var newForm = prototype.replace(/__question_proto__/g, indexQ);

        //4 - increase the indexQ with one for the next queston
        $collectionQuestionHolder.data('index', indexQ + 1);

        // Display the form in the page in an li, before the "Add a Proposition" link li
        var $newQuestionFormLi = $('<li class="question" id="liquestion'+indexQ+'"></li>').append(newForm);

        $newQuestionLink.before($newQuestionFormLi);

        // add a delete link to the new form
        addQuestionFormDeleteLink($newQuestionFormLi);

console.log('indexQ='+indexQ);

        // handle the removal
        $('.remove-question').click(function(e) {
            e.preventDefault();
            $(this).parent().remove();
            return false;
        });

        return indexQ;
    }

    //remove question button
    function addQuestionFormDeleteLink($questionFormLi) {
        var $removeFormA = $('<p><a href="#" class="remove-question btn btn-danger"><span class="fa fa-trash"></span> '+ Translator.trans('question_delete', {}, 'resource')+'</a></p>');
        $removeFormA.appendTo($questionFormLi);

        $($removeFormA).find("a").on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $questionFormLi.remove();
        });
    }

    //add proposition button
    function addPropositionForm($collectionPropositionHolder, $newPropositionLink) {
        // Get the data-prototype
        var prototype = $collectionPropositionHolder.data('prototype');

        // get the new index
        var indexP = $collectionPropositionHolder.data('index');
console.log('indexP='+indexP);
console.log(prototype);
        // Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on how many propositions we have
        var newForm = prototype.replace(/__proposition_proto__/g, indexP);

        // increase the index with one for the next proposition
        $collectionPropositionHolder.data('index', indexP + 1);

        // Display the form in the page in an li, before the "Add a Proposition" link li
        var $newPropositionFormLi = $('<li class="proposition"></li>').append(newForm);

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

    //remove proposition button
    function addPropositionFormDeleteLink($propositionFormLi) {
        var $removeFormA = $('<td><a href="#" class="remove-proposition btn btn-danger" title="'+ Translator.trans('proposition_delete', {}, 'resource')+'"><span class="fa fa-trash"></span></a></td>');
        $($propositionFormLi).find("tr").append($removeFormA);

        $($removeFormA).find("a").on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $propositionFormLi.remove();
        });
    }
}());