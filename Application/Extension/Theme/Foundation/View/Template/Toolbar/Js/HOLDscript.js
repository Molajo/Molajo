$(function () {
    $('.slider').click(function () {
        $('#toolbar').slideToggle(300);

        var img = $(this).find('img');
        if ($(img).attr('id') == 'bot') {
            $(img).attr('src', 'Extension/Theme/Molajo/View/Template/Toolbar/Images/arrow_top.png');
            $(img).attr('id', 'top');
        } else {
            $(img).attr('src', 'Extension/Theme/Molajo/View/Template/Toolbar/Images/arrow_bottom.png');
            $(img).attr('id', 'bot');
        }
    });

    $('.sub').click(function () {
        var cur = $(this).prev();
        $('#toolbar li ul').each(function () {
            if ($(this)[0] != $(cur)[0])
                $(this).slideUp(300);
        });
        $(cur).slideToggle(300);
    });
});
