$(document).ready(function(){

    setInterval(function(){
        refreshNotification();
    }, 1000);

    function refreshNotification(){
        var list = [];
        let notificationsWapper = $('#notification');
        let notifications = $('.notifications-wrapper a');
        notifications.each(function (index) {
            list.push(parseInt($(this).attr('data-id')));
        });
        list = (isNaN(list[0])) ? "" : list;
        $.ajax({
            type: "POST",
            url: "/notification/getAllNotification",
            mimeType: "text/html",
            data: "list=" + JSON.stringify(list),
            success: function(result, status, xhr) {
                if (status == 'success'){
                    notificationsWapper.empty();
                    notificationsWapper.append(result);
                    bindEventActionMark();
                }
                //console.log(status);
                //console.log(xhr);
            },
            error: function(xhr, status, error) {
                //console.log(status);
                //console.log(xhr);
                if (xhr.status == 401){
                    let url = $.url();
                    document.location.href = url.attr('host') + ':' + url.attr('port') + '/' + 'signin';
                }
            }
        });
    };

    function bindEventActionMark(){
        let arObjBut = $('button[role="readAll"]');
        let notifications = $('.notifications-wrapper a');

        arObjBut.each(function(index){
            $(this).on("click", function (event){
                event.preventDefault();
                markAllRead();
            });
        });

        notifications.each(function(index){
            $(this).on("click", function (event){
                let idNotif = $(this).attr('data-id');
                markOneRead(idNotif);
            });
        });
    }

    function markAllRead(){
        $.ajax({
            url: "/notification/markAllAsRead",
            type: "POST",
            success: function(result, status, xhr) {
                if (status == 'success'){
                }
                //console.log(status);
                //console.log(xhr);
            },
            error: function(xhr, status, error) {
                //console.log(status);
                //console.log(xhr);
                if (xhr.status == 401)
                {
                    let url = $.url();
                    document.location.href = url.attr('host') + ':' + url.attr('port') + '/' + 'signin';
                }
            }
        });
    }

    function markOneRead(id){
        $.ajax({
            url: "/notification/markAsRead",
            type: "POST",
            data: "id=" + id,
            success: function(result, status, xhr) {
                if (status == 'success'){
                }
                //console.log(status);
                //console.log(xhr);
            },
            error: function(xhr, status, error) {
                //console.log(status);
                //console.log(xhr);
                if (xhr.status == 401)
                {
                    let url = $.url();
                    document.location.href = url.attr('host') + ':' + url.attr('port') + '/' + 'signin';
                }
            }
        });
    }
});