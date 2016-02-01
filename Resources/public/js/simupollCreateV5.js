(function () {
    'use strict';
// js/form.collection.js
    function FormCollection(div_id, removetext, protoname)
    {
        // keep reference to self in all child functions
        var self=this;

        self.construct = function () {
            // set some shortcuts
            self.element = $('#'+div_id);
            self.element.data('index', self.element.find(':input').length);

            // add delete link to existing children
            self.element.children().each(function() {
                self.addDeleteLink($(this));
            });

            // add click event to the Add new button
            self.element.next().on('click', function(e) {
                // prevent the link from creating a "#" on the URL
                e.preventDefault();

                // add a new tag form (see next code block)
                self.addNew();
            });
        };

        /**
         * onClick event handler -- adds a new input
         */
        self.addNew = function () {
            // Get the data-prototype explained earlier
            var prototype = self.element.data('prototype');

            // get the new index
            var index = self.element.data('index');

            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var re = new RegExp("__"+protoname+"__","g");
            var newForm = prototype.replace(re, index);

            // increase the index with one for the next item
            self.element.data('index', index + 1);

            // Display the form in the page in an li, before the "Add a tag" link li
            self.element.append($(newForm));

            // add a delete link to the new form
            self.addDeleteLink( $(self.element.children(':last-child')[0]) );

            // not a very nice intergration.. but when creating stuff that has help icons,
            // the popovers will not automatically be instantiated
            //initHelpPopovers();

            return $(newForm);
        };

        /**
         * add Delete icon after input
         * @param Element row
         */
        self.addDeleteLink = function (row) {
            var $removeFormA = $('<a href="#" class="btn btn-danger" tabindex="-1"><i class="fa fa-trash"></i> '+removetext+'</a>');
            $(row).find('select').after($removeFormA);
            row.append($removeFormA);
            $removeFormA.on('click', function(e) {
                // prevent the link from creating a "#" on the URL
                e.preventDefault();

                // remove the li for the tag form
                row.remove();
            });
        };

        self.construct();
    }
    new FormCollection('questiongroup', 'remove question', '__question_proto__');
    new FormCollection('propositiongroup', '', '__proposition_proto__');

    /*
    //question management
    var $collectionQuestionHolder = $('.questionholder');
    var $collectionQuestion = $('.questiongroup');
    var $addQuestionLink = $('<a href="#" class="add_question_link btn btn-info"><span class="fa fa-plus"></span> '+Translator.trans('question_add', {}, 'resource') +'</a>');
    var $newQuestionLink = $('<li></li>').append($addQuestionLink);

    //Proposition management
    var $collectionPropositionHolder = $('.propositionholder');
    var $collectionProposition = $('.propositiongroup');
    var $addPropositionLink = $('<a href="#" class="add_proposition_link btn btn-info"><span class="fa fa-plus"></span> '+Translator.trans('proposition_add', {}, 'resource')+'</a>');
    var $newPropositionLink = $('<li></li>').append($addPropositionLink);

    // add the "add a question" anchor
    $collectionQuestion.append($newQuestionLink);
    // add the "add a proposition" anchor
    $collectionProposition.append($newPropositionLink);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new question (e.g. 2)
    $collectionQuestionHolder.data('index', $collectionQuestionHolder.find(':input').length);
    $collectionPropositionHolder.data('index', $collectionPropositionHolder.find(':input').length);

    //add a new question
    $collectionQuestion.on('click', '.add_question_link', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();
        // add a new proposition form
        var indexQ = addQuestionForm($collectionQuestionHolder, $newQuestionLink);
    });
    //add a new proposition
    $collectionProposition.on('click', $addPropositionLink, function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();
        // add a new proposition form
        addPropositionForm($collectionPropositionHolder, $newPropositionLink);
    });

    // add a delete link to all of the existing questions form li elements
    $collectionQuestion.find('li.question').each(function() {
        addQuestionFormDeleteLink(this);
    });
    // add a delete link to all of the existing questions form li elements
    $collectionProposition.find('li.proposition').each(function() {
        addPropositionFormDeleteLink(this);
    });

    //add question button
    function addQuestionForm($collectionQuestionHolder, $newQuestionLink) {
        //1 - Get the data-prototype
        var prototype = $collectionQuestionHolder.data('questionprototype');

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
        var prototype = $collectionPropositionHolder.data('propositionprototype');

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
    */
}());