(function ($) {
    $.fn.extend({
        pupslider: function (options) {
            var settings = $.extend({ stick: 'right', speed: 1000, opacity: 1 }, options);
            var wrapper = $(this);
            var sw = $('.btn-show', wrapper).outerWidth();
            var w = $('.pushup-form', wrapper).outerWidth() + sw;
            var fw = $('.pushup-form', wrapper).outerWidth();
            $('.pushup-form', wrapper).css('opacity', settings.opacity);
            if (settings.stick == 'right') {
                var rLeft = wrapper.outerWidth();
                var sLeft = wrapper.outerWidth() + wrapper.offset().left - sw;
                //set initial position for form
                $('.btn-show', wrapper).css('left', sLeft);
                $('.btn-close', wrapper).addClass('right');
                $('.pushup-form', wrapper).addClass('right').css({ left: rLeft, display: '' });
                $('.btn-show', wrapper).click(function () {
                    $(this).css('display', 'none');
                    $('.pushup-form', wrapper).css('display', '').animate({ left: '-=' + fw }, settings.speed);
                });
                $('.btn-close', wrapper).click(function () {
                    $('.pushup-form', wrapper).animate({ left: '+=' + fw }, settings.speed, function () {
                        $(this).css('display', '');
                        $('.btn-show').css('display', '');
                    });
                });
            }
            else {
                //set initial position for form
                $('.btn-close', wrapper).addClass('left');
                $('.btn-show', wrapper).css('left', wrapper.offset().left);
                $('.pushup-form', wrapper).addClass('left').css({ left: -fw, display: '' });
                $('.btn-show', wrapper).click(function () {
                    $(this).css('display', 'none');
                    $('.pushup-form', wrapper).css('display', '').animate({ left: '+=' + fw }, settings.speed);
                });
                $('.btn-close', wrapper).click(function () {
                    $('.pushup-form', wrapper).animate({ left: '-=' + fw }, settings.speed, function () {
                        $(this).css('display', '');
                        $('.btn-show').css('display', '');
                    });
                });
            }
        }
    });
})(jQuery);
