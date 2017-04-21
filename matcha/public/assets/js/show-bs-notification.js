// function showNotification(message, type) {
//     let glyphicon = 'glyphicon glyphicon-ok';
//     if (type == 'danger') {
//         glyphicon = 'glyphicon glyphicon-remove';
//     }
//     $.notify({
//         // options
//         icon: glyphicon,
//         message: message,
//         target: '_blank'
//     }, {
//         // settings
//         type: type,
//         allow_dismiss: true,
//         delay: 5000,
//         newest_on_top: true,
//         showProgressbar: false,
//         // placement: {
//         //     from: "top",
//         //     align: "center"
//         // },
//         // template: '<div data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><a href="{3}" target="{4}" data-notify="url"></a></div>'
//     });
// };

function showNotification(message, type) {
    let glyphicon = 'glyphicon glyphicon-ok';
    if (type == 'danger') {
        glyphicon = 'glyphicon glyphicon-remove';
    }
    $.notify({
    // options
    icon: glyphicon,
    message: message,
    target: '_blank'
},{
    // settings
    type: type,
    allow_dismiss: true,
    newest_on_top: false,
    showProgressbar: false,
    offset: {x: 10, y: 62},
    delay: 5000,
    timer: 1000,
    animate: {
        enter: 'animated fadeInRight',
        exit: 'animated fadeOutRight'
 },
});
}