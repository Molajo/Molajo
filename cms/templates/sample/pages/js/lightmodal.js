/*
 project: lightModal
 version: 1.0 (11.21.2009)
 author: Jamison Morrow
 project home: http://code.google.com/p/light-modal/
 license: MIT, http://www.opensource.org/licenses/mit-license.php
 */

var lightModal = { loadingImage: "http://light-modal.googlecode.com/files/loading.gif" };
lightModal.show = function(h) {
    var c = $("object:visible,embed:visible").addClass("__visibleFlash");
    $(".__visibleFlash").hide();
    var a = $("body");
    a.css("overflow", "hidden");
    var d = $(".lightModal_modal_container").length;
    var b = $("<div class='lightModal_modal_container'/>");
    var f = $("<div class='lightModal_modal' style='overflow:auto;padding: 20px;position: fixed;left: 0;top: 0;background: #fff;border: 3px solid #222;'/>");
    f.width(h.width);
    f.height(h.height);
    f.css("z-index", d * 1000 + 1);
    f.css("top", ($(document).height() - h.height - 50) / 2);
    f.css("left", (screen.width - h.width - 20) / 2);
    b.append(f);
    var e = $("<div class='lightModal_modal_overlay' style='position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: #222;' />");
    e.css("opacity", 0.7);
    e.css("filter", "alpha(opacity=70)");
    e.click(function() {
        lightModal.remove(b)
    });
    e.css("z-index", d * 1000);
    b.append(e);
    $(a).append(b);
    if (!h.html) {
        var g = $('<img src="' + lightModal.loadingImage + '"/>');
        f.append(g);
        $.ajax({ type: "GET", url: h.url, success: function(i) {
            f.html(i);
            $(".lightModal_focus").eq(0).focus()
        }, error: function(k, j, i) {
            lightModal.alert(k.responseText)
        } })
    } else {
        f.append(h.html)
    }
    return false
};
lightModal.remove = function(b) {
    b = b || $(".lightModal_modal_container");
    b.remove();
    var a = $("body");
    a.css("overflow", "auto");
    $(".__visibleFlash").show().removeClass("__visibleFlash");
    $(".lightModal_focus").focus()
};
lightModal.closeParentModal = function(a) {
    lightModal.remove(a.parents(".lightModal_modal_container"))
};
lightModal.alert = function(b) {
    var a = $("<div />").html(b.html);
    b.callBack = b.callBack || function() {
        lightModal.closeParentModal(a)
    };
    var c = $("<div style='padding:30px 0 10px 0;text-align:center'><button>OK</button></div>");
    c.find("button").click(b.callBack);
    a.append(c);
    lightModal.show({ width: b.width || 400, height: b.height || 200, html: a })
};
lightModal.submit = function(a) {
    return lightModal.ajax({ type: "POST", url: a.action, data: $(a).serialize() })
};
lightModal.get = function(a) {
    return lightModal.ajax({ type: "GET", url: a })
};
lightModal.ajax = function(parameters) {
    $.ajax({ type: parameters.type, url: parameters.url, data: parameters.data, success: parameters.onSuccess ? parameters.onSuccess() : function(js) {
        eval(js)
    }, error: parameters.onError ? parameters.onError() : function(xhr, text, error) {
        lightModal.alert({ html: xhr.responseText, width: 600, height: 400 })
    } });
    return false
};