jQuery(function($) {
    let fields_clients = $('.fields_clients');
    let lis = fields_clients.find('.cf-complex__tabs li');

    lis.draggable(false);
    lis.css('position', 'inherit');
})