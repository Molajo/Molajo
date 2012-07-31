jQuery(document).ready(function ($) {

    /* Use this js doc for all application specific JS */

    /* TABS --------------------------------- */
    /* Remove if you don't need :) */

    function activateTab($tab) {
        var $activeTab = $tab.closest('dl').find('dd.active'),
            contentLocation = $tab.children('a').attr("href") + 'Tab';

        console.log($activeTab );

        // Strip off the current url that IE adds
        contentLocation = contentLocation.replace(/^.+#/, '#');

        //Make Tab Active
        $activeTab.removeClass('active');
        $tab.addClass('active');

        //Show Tab Content
        $(contentLocation).closest('.tabs-content').children('li').hide();
        $(contentLocation).css('display', 'block');
    }

    $('dl.tabs dd a').on('click.fndtn', function (event) {
        activateTab($(this).parent('dd'));
    });

    alert(window.location.hash);


    if (window.location.hash) {
        activateTab($('a[href="' + window.location.hash + '"]'));
        $.foundation.customForms.appendCustomMarkup();
    }

});
