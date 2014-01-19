$(document).ready(function () {

    $("div#grid-filters").hide();
    $("div#grid-tags").hide();
    $("div#grid-categories").hide();
    $("div#grid-permissions").hide();

    $("#grid-filters-button").click(function () {
        if ($("div#grid-filters").is(":visible")) {
            $("div#grid-filters").hide();
        } else {
            $("div#grid-filters").show();
        }
    });

    $("#grid-tags-button").click(function () {
        if ($("div#grid-tags").is(":visible")) {
            $("div#grid-tags").hide();
        } else {
            $("div#grid-tags").show();
        }
    });

    $("#grid-categories-button").click(function () {
        if ($("div#grid-categories").is(":visible")) {
            $("div#grid-categories").hide();
        } else {
            $("div#grid-categories").show();
        }
    });

    $("#grid-permissions-button").click(function () {
        if ($("div#grid-permissions").is(":visible")) {
            $("div#grid-permissions").hide();
        } else {
            $("div#grid-permissions").show();
        }
    });
});
