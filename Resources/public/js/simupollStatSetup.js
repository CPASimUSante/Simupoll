(function () {
    'use strict';

    var su = $('#simupoll_userlist').val();
    var wsn = $('#wsn').val();
    var selectedUsers = (su === '') ? [] : su.split(',');
    var allUsersInWS = [];
    //get users of this WS
    $.ajax({
        url: Routing.generate('cpasimusante_simupoll_get_user_in_ws', {wslist:wsn}),
        type: 'GET',
        success: function (datas) {
            allUsersInWS = (datas !== null) ? datas : [];
        }
    });
    //pick users
    $('#pick-user-for-stats').on('click', '#pick-users-btn', function () {
        var userPicker = new UserPicker();
        var settings = {
            multiple: true,
            show_filters: false,
            picker_name: 'simupolluser_picker',
            picker_title: Translator.trans('select_users_to_add_to_simupollgraph', {},'resource'),
            whitelist: allUsersInWS,
            return_datas: true,
            selected_users:selectedUsers
        };
        userPicker.configure(settings, addUsersToGraph);
        userPicker.open();
    });
    //callback, add users to widget user field
    var addUsersToGraph = function(users) {
        var userIds = [];
        if (users !== null) {
            for (var i= 0,tot=users.length;i<tot;i++) {
                userIds.push(users[i].id);
            }
        }
        $('#simupoll_userlist').val(userIds);
    };

    //manage checkboxes
    $('.group-titles').each(function(i, el){
        var gid = $(el).data('id');
        var input = $(el).find('input')[0];
        //init
        if ($.trim(input.value) == ""){$('.categorygroup'+gid).prop( "disabled", true )}
        //
        $(input).on('change', function(){
            if ($.trim(input.value) == ""){
                $('.categorygroup'+gid).prop( "disabled", true );
            } else {
                $('.categorygroup'+gid).prop( "disabled", false );
            }
        });
    });
}());
