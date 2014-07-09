$(document).on('click', '.js-vote', function (e) {
    e.preventDefault();

    var href = $(this).attr('href');
    if (undefined === href) {
        return;
    }

    $('.js-vote').each(function () {
        $(this).removeAttr('href');
    });

    window.location.href = href;
});