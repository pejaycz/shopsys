$(function () {
    var targets = $('.form-error__icon');
    var target = false;
    var tooltip = false;

    targets.bind('mouseenter', function () {
        target = $(this);
        tooltip = $('.form-error__list');

        var init_tooltip = function () {
            if ($(window).width() < tooltip.outerWidth() * 1.5) {
                tooltip.css('max-width', $(window).width() / 2);
            } else {
                tooltip.css('max-width', 340);
            }

            var pos_left = target.offset().left + (target.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                pos_top = target.offset().top - tooltip.outerHeight() - 20;

            if (pos_left < 0) {
                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass('left');
            }
            else
                tooltip.removeClass('left');

            if (pos_left + tooltip.outerWidth() > $(window).width()) {
                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass('right');
            }
            else
                tooltip.removeClass('right');

            if (pos_top < 0) {
                var pos_top = target.offset().top + target.outerHeight();
                tooltip.addClass('top');
            }
            else
                tooltip.removeClass('top');

        };

        init_tooltip();
        $(window).resize(init_tooltip);

    });
});