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
            $("div#grid-tags").hide();
            $("div#grid-categories").hide();
            $("div#grid-permissions").hide();
        }
    });

    $("#grid-tags-button").click(function () {
        if ($("div#grid-tags").is(":visible")) {
            $("div#grid-tags").hide();
        } else {
            $("div#grid-tags").show();
            $("div#grid-filters").hide();
            $("div#grid-categories").hide();
            $("div#grid-permissions").hide();
        }
    });

    $("#grid-categories-button").click(function () {
        if ($("div#grid-categories").is(":visible")) {
            $("div#grid-categories").hide();
        } else {
            $("div#grid-categories").show();
            $("div#grid-filters").hide();
            $("div#grid-tags").hide();
            $("div#grid-permissions").hide();
        }
    });

    $("#grid-permissions-button").click(function () {
        if ($("div#grid-permissions").is(":visible")) {
            $("div#grid-permissions").hide();
        } else {
            $("div#grid-permissions").show();
            $("div#grid-filters").hide();
            $("div#grid-tags").hide();
            $("div#grid-categories").hide();
        }
    });
});
