(function () {
    'use strict';

    //if we're in choice = 2 (= display by categories)
    if ($('#nextpage').length != 0) {
        $('#nextpage').on('click', function(event){
console.log("#nextpage submit");
            event.preventDefault();
            $('#direction').val('next');
            //saveAnswers('all');
            $('#paperResponse').submit();
        });
    }
    if ($('#prevpage').length != 0) {
        $('#prevpage').on('click', function(event){
console.log("#prevpage submit");
            event.preventDefault();
            $('#direction').val('prev');
            //saveAnswers('all');
            $('#paperResponse').submit();
        });
    }
    function saveAnswers() {
        var $sid = $('#sid').val();
        var choice = [];
        $('input.propositionchoice:checked').each(function(){
            choice.push([$(this).data('question'), $(this).val()]);
        });

        if (choice.length != $('#questions').val()) {
            $('#messagediv').html('Attention');
        }

        var data ={};
        data.choice = choice;

        $.ajax({
            type:"POST",
            data:data,
            url: Routing.generate('cpasimusante_simupoll_paper_save', {sid: $sid}),
            success: function(response) {
console.log(response);
                $('#messagediv').html(translation.trans('answer_saved', {}, 'resource'));
            },
            error: function(jqXHR, textStatus, errorThrown) { }
        });
    }
}());
