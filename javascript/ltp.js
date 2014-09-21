var div2 = $('#div2');

var wrapper = $('#div1')
    .contents()
    .wrap($('<div>').css('position','absolute'))
    .parent();

wrapper.animate(div2.offset(), 1000, function() {
    $(this).contents().appendTo(div2);
    wrapper.remove();
});
