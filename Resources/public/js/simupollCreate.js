(function () {
    'use strict';
    var translation= window.Translator;

    //question management
    var $collectionQuestionHolder = $('ul.questiongroup');
    var $addQuestionLink = $('<a href="#" class="add_question_link btn btn-info"><span class="fa fa-plus"></span> '+translation.trans('question_add', {}, 'resource') +'</a>');
    var $newQuestionLink = $('<li></li>').append($addQuestionLink);
    var indexPr = [];
    var indexQ;
    // add the "add a question" anchor and li to the tags ul
    $collectionQuestionHolder.append($newQuestionLink);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new question (e.g. 2)
    $collectionQuestionHolder.data('index', $collectionQuestionHolder.find(':input').length);

    //add a new question
    $collectionQuestionHolder.on('click', '.add_question_link', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new proposition form
        indexQ = addQuestionForm($collectionQuestionHolder, $newQuestionLink);

        if (indexPr[indexQ] == undefined){indexPr[indexQ] = 0;}
        //Proposition management
        var $collectionPropositionHolder = $('#liquestion'+indexQ).find('.propositiongroup');
        var $addPropositionLink = $('<a href="#" class="add_proposition_link btn btn-info"><span class="fa fa-plus"></span> '+translation.trans('proposition_add', {}, 'resource')+'</a>');
        var $newPropositionLink = $('<li></li>').append($addPropositionLink);

        // add the "add a proposition" anchor and li to the tags ul
        $collectionPropositionHolder.append($newPropositionLink);

        //add a new proposition
        $addPropositionLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
console.log('$collectionQuestionHolder.on(click .add_question_link');
console.log(indexPr);
            // add a new proposition form
            indexPr[indexQ] = addPropositionForm($collectionPropositionHolder, $newPropositionLink, indexQ);
        });

    });
    //Existing elements :
    // add a delete link for questions
    $collectionQuestionHolder.find('li.question').each(function() {
        addQuestionFormDeleteLink(this);
    });

    // add a delete link for propositions
    $('.propositiongroup').find('li.proposition').each(function() {
        addPropositionFormDeleteLink(this);
    });

    $('.propositiongroup').each(function() {
        var propositionGroup = $(this);
        var $addPropositionLink = $('<a href="#" class="add_proposition_link btn btn-info"><span class="fa fa-plus"></span> '+translation.trans('proposition_add', {}, 'resource')+'</a>');
        var $newPropositionLink = $('<li></li>').append($addPropositionLink);
        //get number of already created propositions
        var propositionCount = $(this).find('.proposition').length;
        //get question index
        var iQ = $(this).data('question');
        //Set data index for propositionGroup
        propositionGroup.data('index', propositionCount);

        propositionGroup.append($newPropositionLink);
        //add a new proposition
        $addPropositionLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new proposition form
            indexPr[iQ] = addPropositionForm(propositionGroup, $newPropositionLink, indexQ);
        });
    });

    //add question button
    function addQuestionForm($collectionQuestionHolder, $newQuestionLink) {
        //1 - Get the data-prototype
        var prototype = $collectionQuestionHolder.data('prototype');
//console.log('addQuestionForm---------------');console.log(prototype);
        //2 - get the new index
        indexQ = $collectionQuestionHolder.data('index');
//console.log('indexQ='+indexQ);
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
        var $removeFormA = $('<p><a href="#" class="remove-question btn btn-danger"><span class="fa fa-trash"></span> '+ translation.trans('question_delete', {}, 'resource')+'</a></p>');
        $removeFormA.appendTo($questionFormLi);

        $($removeFormA).find("a").on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $questionFormLi.remove();
        });
    }

    //add proposition button
    function addPropositionForm($collectionPropositionHolder, $newPropositionLink, indexQ) {
        // Get the data-prototype
        var prototype = $collectionPropositionHolder.data('prototype');
//console.log('$collectionPropositionHolder');console.log($collectionPropositionHolder);
//console.log("addPropositionForm---------------\n");console.log(prototype);
        // get the new index
        indexPr[indexQ] = $collectionPropositionHolder.data('index');
        if (typeof indexPr[indexQ] == 'undefined'){indexPr[indexQ] = 0;}

        // Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on how many propositions we have
        var newForm = prototype.replace(/__proposition_proto__/g, indexPr[indexQ]);

        // increase the index with one for the next proposition
        $collectionPropositionHolder.data('index', indexPr[indexQ] + 1);

        // Display the form in the page in an li, before the "Add a Proposition" link li
        var $newPropositionFormLi = $('<li class="proposition" id="liproposition'+indexPr[indexQ]+'"></li>').append(newForm);

        $newPropositionLink.before($newPropositionFormLi);

        // add a delete link to the new form
        addPropositionFormDeleteLink($newPropositionFormLi);

        // handle the removal
        $('.remove-proposition').click(function(e) {
            e.preventDefault();
            $(this).parent().remove();
            return false;
        });

        return indexPr[indexQ];
    }

    //remove proposition button
    function addPropositionFormDeleteLink($propositionFormLi) {
        var $removeFormA = $('<td><a href="#" class="remove-proposition btn btn-danger" title="'+ translation.trans('proposition_delete', {}, 'resource')+'"><span class="fa fa-trash"></span></a></td>');
        $($propositionFormLi).find("tr").append($removeFormA);

        $($removeFormA).find("a").on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $propositionFormLi.remove();
        });
    }
}());
