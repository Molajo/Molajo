define(['aloha/jquery'], function (jQuery) {
    var $ = jQuery;

    /*
     jquery.layout 1.3.0 - Release Candidate 29.14
     $Date: 2011-02-13 08:00:00 (Sun, 13 Feb 2011) $
     $Rev: 302914 $

     Copyright (c) 2010
     Fabrizio Balliano (http://www.fabrizioballiano.net)
     Kevin Dalman (http://allpro.net)

     Dual licensed under the GPL (http://www.gnu.org/licenses/gpl.html)
     and MIT (http://www.opensource.org/licenses/mit-license.php) licenses.

     Changelog: http://layout.jquery-dev.net/changelog.cfm#1.3.0.rc29.13

     Docs: http://layout.jquery-dev.net/documentation.html
     Tips: http://layout.jquery-dev.net/tips.html
     Help: http://groups.google.com/group/jquery-ui-layout
     */
    (function (f) {
        var C = f.browser;
        f.layout = {browser:{mozilla:!!C.mozilla, webkit:!!C.webkit || !!C.safari, msie:!!C.msie, isIE6:!!C.msie && C.version == 6, boxModel:!1}, scrollbarWidth:function () {
            return window.scrollbarWidth || f.layout.getScrollbarSize("width")
        }, scrollbarHeight:function () {
            return window.scrollbarHeight || f.layout.getScrollbarSize("height")
        }, getScrollbarSize:function (j) {
            var o = f('<div style="position: absolute; top: -10000px; left: -10000px; width: 100px; height: 100px; overflow: scroll;"></div>').appendTo("body"),
                p = {width:o.width() - o[0].clientWidth, height:o.height() - o[0].clientHeight};
            o.remove();
            window.scrollbarWidth = p.width;
            window.scrollbarHeight = p.height;
            return j.match(/^(width|height)$/i) ? p[j] : p
        }, showInvisibly:function (j, o) {
            if (!j)return{};
            j.jquery || (j = f(j));
            var p = {display:j.css("display"), visibility:j.css("visibility")};
            return o || p.display == "none" ? (j.css({display:"block", visibility:"hidden"}), p) : {}
        }, getElemDims:function (j) {
            var o = {}, p = o.css = {}, r = {}, F, ga, H = j.offset();
            o.offsetLeft = H.left;
            o.offsetTop = H.top;
            f.each("Left,Right,Top,Bottom".split(","), function (x, z) {
                F = p["border" + z] = f.layout.borderWidth(j, z);
                ga = p["padding" + z] = f.layout.cssNum(j, "padding" + z);
                r[z] = F + ga;
                o["inset" + z] = ga
            });
            o.offsetWidth = j.innerWidth();
            o.offsetHeight = j.innerHeight();
            o.outerWidth = j.outerWidth();
            o.outerHeight = j.outerHeight();
            o.innerWidth = o.outerWidth - r.Left - r.Right;
            o.innerHeight = o.outerHeight - r.Top - r.Bottom;
            p.width = j.width();
            p.height = j.height();
            return o
        }, getElemCSS:function (f, o) {
            var p = {}, r = f[0].style, F = o.split(","), ga = "Top,Bottom,Left,Right".split(","),
                H = "Color,Style,Width".split(","), x, z, C, S, T, X;
            for (S = 0; S < F.length; S++)if (x = F[S], x.match(/(border|padding|margin)$/))for (T = 0; T < 4; T++)if (z = ga[T], x == "border")for (X = 0; X < 3; X++)C = H[X], p[x + z + C] = r[x + z + C]; else p[x + z] = r[x + z]; else p[x] = r[x];
            return p
        }, cssWidth:function (j, o) {
            var p = f.layout.borderWidth, r = f.layout.cssNum;
            if (o <= 0)return 0;
            if (!f.layout.browser.boxModel)return o;
            p = o - p(j, "Left") - p(j, "Right") - r(j, "paddingLeft") - r(j, "paddingRight");
            return Math.max(0, p)
        }, cssHeight:function (j, o) {
            var p = f.layout.borderWidth,
                r = f.layout.cssNum;
            if (o <= 0)return 0;
            if (!f.layout.browser.boxModel)return o;
            p = o - p(j, "Top") - p(j, "Bottom") - r(j, "paddingTop") - r(j, "paddingBottom");
            return Math.max(0, p)
        }, cssNum:function (j, o) {
            j.jquery || (j = f(j));
            var p = f.layout.showInvisibly(j), r = parseInt(f.curCSS(j[0], o, !0), 10) || 0;
            j.css(p);
            return r
        }, borderWidth:function (j, o) {
            j.jquery && (j = j[0]);
            var p = "border" + o.substr(0, 1).toUpperCase() + o.substr(1);
            return f.curCSS(j, p + "Style", !0) == "none" ? 0 : parseInt(f.curCSS(j, p + "Width", !0), 10) || 0
        }, isMouseOverElem:function (j, o) {
            var p = f(o || this), r = p.offset(), F = r.top, r = r.left, C = r + p.outerWidth(), p = F + p.outerHeight(), H = j.pageX, x = j.pageY;
            return f.layout.browser.msie && H < 0 && x < 0 || H >= r && H <= C && x >= F && x <= p
        }};
        f.fn.layout = function (j) {
            function o(a) {
                if (!a)return!0;
                var b = a.keyCode;
                if (b < 33)return!0;
                var c = {38:"north", 40:"south", 37:"west", 39:"east"}, d = a.shiftKey, e = a.ctrlKey, h, g, i, n;
                e && b >= 37 && b <= 40 && m[c[b]].enableCursorHotkey ? n = c[b] : (e || d) && f.each(k.borderPanes.split(","), function (a, c) {
                    h = m[c];
                    g = h.customHotkey;
                    i = h.customHotkeyModifier;
                    if (d &&
                        i == "SHIFT" || e && i == "CTRL" || e && d)if (g && b == (isNaN(g) || g <= 9 ? g.toUpperCase().charCodeAt(0) : g))return n = c, !1
                });
                if (!n || !q[n] || !m[n].closable || l[n].isHidden)return!0;
                ca(n);
                a.stopPropagation();
                return a.returnValue = !1
            }

            function p(a) {
                this && this.tagName && (a = this);
                var b;
                I(a) ? b = q[a] : f(a).data("layoutRole") ? b = f(a) : f(a).parents().each(function () {
                    if (f(this).data("layoutRole"))return b = f(this), !1
                });
                if (b && b.length) {
                    var c = b.data("layoutEdge"), a = l[c];
                    a.cssSaved && r(c);
                    if (a.isSliding || a.isResizing || a.isClosed)a.cssSaved =
                        !1; else {
                        var d = {zIndex:k.zIndex.pane_normal + 2}, e = {}, h = b.css("overflow"), g = b.css("overflowX"), i = b.css("overflowY");
                        if (h != "visible")e.overflow = h, d.overflow = "visible";
                        if (g && !g.match(/visible|auto/))e.overflowX = g, d.overflowX = "visible";
                        if (i && !i.match(/visible|auto/))e.overflowY = g, d.overflowY = "visible";
                        a.cssSaved = e;
                        b.css(d);
                        f.each(k.allPanes.split(","), function (a, b) {
                            b != c && r(b)
                        })
                    }
                }
            }

            function r(a) {
                this && this.tagName && (a = this);
                var b;
                I(a) ? b = q[a] : f(a).data("layoutRole") ? b = f(a) : f(a).parents().each(function () {
                    if (f(this).data("layoutRole"))return b =
                        f(this), !1
                });
                if (b && b.length) {
                    var a = b.data("layoutEdge"), a = l[a], c = a.cssSaved || {};
                    !a.isSliding && !a.isResizing && b.css("zIndex", k.zIndex.pane_normal);
                    b.css(c);
                    a.cssSaved = !1
                }
            }

            function F(a, b, c) {
                var d = f(a);
                if (d.length)if (k.borderPanes.indexOf(b) == -1)alert(D.errButton + D.Pane.toLowerCase() + ": " + b); else return a = m[b].buttonClass + "-" + c, d.addClass(a + " " + a + "-" + b).data("layoutName", m.name), d; else alert(D.errButton + D.selector + ": " + a);
                return null
            }

            function C(a, b, c) {
                switch (b.toLowerCase()) {
                    case "toggle":
                        H(a, c);
                        break;
                    case "open":
                        x(a, c);
                        break;
                    case "close":
                        z(a, c);
                        break;
                    case "pin":
                        Ba(a, c);
                        break;
                    case "toggle-slide":
                        H(a, c, !0);
                        break;
                    case "open-slide":
                        x(a, c, !0)
                }
            }

            function H(a, b, c) {
                (a = F(a, b, "toggle")) && a.click(function (a) {
                    ca(b, !!c);
                    a.stopPropagation()
                })
            }

            function x(a, b, c) {
                (a = F(a, b, "open")) && a.attr("title", D.Open).click(function (a) {
                    M(b, !!c);
                    a.stopPropagation()
                })
            }

            function z(a, b) {
                var c = F(a, b, "close");
                c && c.attr("title", D.Close).click(function (a) {
                    J(b);
                    a.stopPropagation()
                })
            }

            function Ba(a, b) {
                var c = F(a, b, "pin");
                if (c) {
                    var d = l[b];
                    c.click(function (a) {
                        T(f(this), b, d.isSliding || d.isClosed);
                        d.isSliding || d.isClosed ? M(b) : J(b);
                        a.stopPropagation()
                    });
                    T(c, b, !d.isClosed && !d.isSliding);
                    k[b].pins.push(a)
                }
            }

            function S(a, b) {
                f.each(k[a].pins, function (c, d) {
                    T(f(d), a, b)
                })
            }

            function T(a, b, c) {
                var d = a.attr("pin");
                if (!(d && c == (d == "down"))) {
                    var d = m[b].buttonClass + "-pin", e = d + "-" + b, b = d + "-up " + e + "-up", d = d + "-down " + e + "-down";
                    a.attr("pin", c ? "down" : "up").attr("title", c ? D.Unpin : D.Pin).removeClass(c ? b : d).addClass(c ? d : b)
                }
            }

            function X(a) {
                for (var a = f.extend({},
                    m.cookie, a || {}).name || m.name || "Layout", b = document.cookie, b = b ? b.split(";") : [], c, d = 0, e = b.length; d < e; d++)if (c = f.trim(b[d]).split("="), c[0] == a)return Ca(decodeURIResource(c[1]));
                return""
            }

            function pa(a, b) {
                var c = f.extend({}, m.cookie, b || {}), d = c.name || m.name || "Layout", e = "", h = "", g = !1;
                c.expires.toUTCString ? h = c.expires : typeof c.expires == "number" && (h = new Date, c.expires > 0 ? h.setDate(h.getDate() + c.expires) : (h.setYear(1970), g = !0));
                h && (e += ";expires=" + h.toUTCString());
                c.path && (e += ";path=" + c.path);
                c.domain && (e += ";domain=" +
                    c.domain);
                c.secure && (e += ";secure");
                g ? (l.cookie = {}, document.cookie = d + "=" + e) : (l.cookie = qa(a || c.keys), document.cookie = d + "=" + encodeURIResource(Da(l.cookie)) + e);
                return f.extend({}, l.cookie)
            }

            function Ea(a) {
                if (a = X(a))l.cookie = f.extend({}, a), Fa(a);
                return a
            }

            function Fa(a, b) {
                f.extend(!0, m, a);
                if (l.initialized) {
                    var c, d, e = !b;
                    f.each(k.allPanes.split(","), function (b, g) {
                        c = a[g];
                        if (typeof c == "object")d = c.initHidden, d === !0 && la(g, e), d === !1 && ha(g, !1, e), d = c.size, d > 0 && Y(g, d), d = c.initClosed, d === !0 && J(g, !1, e), d === !1 && M(g,
                            !1, e)
                    })
                }
            }

            function qa(a) {
                var b = {}, c = {isClosed:"initClosed", isHidden:"initHidden"}, d, e, h;
                if (!a)a = m.cookie.keys;
                f.isArray(a) && (a = a.join(","));
                for (var a = a.replace(/__/g, ".").split(","), g = 0, i = a.length; g < i; g++)d = a[g].split("."), e = d[0], d = d[1], k.allPanes.indexOf(e) < 0 || (h = l[e][d], h != void 0 && (d == "isClosed" && l[e].isSliding && (h = !0), (b[e] || (b[e] = {}))[c[d] ? c[d] : d] = h));
                return b
            }

            function Da(a) {
                function b(a) {
                    var d = [], e = 0, h, g, i;
                    for (h in a)g = a[h], i = typeof g, i == "string" ? g = '"' + g + '"' : i == "object" && (g = b(g)), d[e++] = '"' + h +
                        '":' + g;
                    return"{" + d.join(",") + "}"
                }

                return b(a)
            }

            function Ca(a) {
                try {
                    return window.eval("(" + a + ")") || {}
                } catch (b) {
                    return{}
                }
            }

            var D = {Pane:"Pane", Open:"Open", Close:"Close", Resize:"Resize", Slide:"Slide Open", Pin:"Pin", Unpin:"Un-Pin", selector:"selector", msgNoRoom:"Not enough room to show this pane.", errContainerMissing:"UI Layout Initialization Error\n\nThe specified layout-container does not exist.", errCenterPaneMissing:"UI Layout Initialization Error\n\nThe center-pane element does not exist.\n\nThe center-pane is a required element.",
                    errContainerHeight:"UI Layout Initialization Warning\n\nThe layout-container \"CONTAINER\" has no height.\n\nTherefore the layout is 0-height and hence 'invisible'!", errButton:"Error Adding Button \n\nInvalid "}, m = {name:"", containerClass:"ui-layout-container", scrollToBookmarkOnLoad:!0, resizeWithWindow:!0, resizeWithWindowDelay:200, resizeWithWindowMaxDelay:0, onresizeall_start:null, onresizeall_end:null, onload_start:null, onload_end:null, onunload_start:null, onunload_end:null, autoBindCustomButtons:!1, zIndex:null,
                    defaults:{applyDemoStyles:!1, closable:!0, resizable:!0, slidable:!0, initClosed:!1, initHidden:!1, contentSelector:".ui-layout-content", contentIgnoreSelector:".ui-layout-ignore", findNestedContent:!1, paneClass:"ui-layout-pane", resizerClass:"ui-layout-resizer", togglerClass:"ui-layout-toggler", buttonClass:"ui-layout-button", minSize:0, maxSize:0, spacing_open:6, spacing_closed:6, togglerLength_open:50, togglerLength_closed:50, togglerAlign_open:"center", togglerAlign_closed:"center", togglerTip_open:D.Close, togglerTip_closed:D.Open,
                        togglerContent_open:"", togglerContent_closed:"", resizerDblClickToggle:!0, autoResize:!0, autoReopen:!0, resizerDragOpacity:1, maskIframesOnResize:!0, resizeNestedLayout:!0, resizeWhileDragging:!1, resizeContentWhileDragging:!1, noRoomToOpenTip:D.msgNoRoom, resizerTip:D.Resize, sliderTip:D.Slide, sliderCursor:"pointer", slideTrigger_open:"click", slideTrigger_close:"mouseleave", slideDelay_open:300, slideDelay_close:300, hideTogglerOnSlide:!1, preventQuickSlideClose:!(!f.browser.webkit && !f.browser.safari), preventPrematureSlideClose:!1,
                        showOverflowOnHover:!1, enableCursorHotkey:!0, customHotkeyModifier:"SHIFT", fxName:"slide", fxSpeed:null, fxSettings:{}, fxOpacityFix:!0, triggerEventsOnLoad:!1, triggerEventsWhileDragging:!0, onshow_start:null, onshow_end:null, onhide_start:null, onhide_end:null, onopen_start:null, onopen_end:null, onclose_start:null, onclose_end:null, onresize_start:null, onresize_end:null, onsizecontent_start:null, onsizecontent_end:null, onswap_start:null, onswap_end:null, ondrag_start:null, ondrag_end:null}, north:{paneSelector:".ui-layout-north",
                        size:"auto", resizerCursor:"n-resize", customHotkey:""}, south:{paneSelector:".ui-layout-south", size:"auto", resizerCursor:"s-resize", customHotkey:""}, east:{paneSelector:".ui-layout-east", size:200, resizerCursor:"e-resize", customHotkey:""}, west:{paneSelector:".ui-layout-west", size:200, resizerCursor:"w-resize", customHotkey:""}, center:{paneSelector:".ui-layout-center", minWidth:0, minHeight:0}, useStateCookie:!1, cookie:{name:"", autoSave:!0, autoLoad:!0, domain:"", path:"", expires:"", secure:!1, keys:"north.size,south.size,east.size,west.size,north.isClosed,south.isClosed,east.isClosed,west.isClosed,north.isHidden,south.isHidden,east.isHidden,west.isHidden"}},
                ra = {slide:{all:{duration:"fast"}, north:{direction:"up"}, south:{direction:"down"}, east:{direction:"right"}, west:{direction:"left"}}, drop:{all:{duration:"slow"}, north:{direction:"up"}, south:{direction:"down"}, east:{direction:"right"}, west:{direction:"left"}}, scale:{all:{duration:"fast"}}}, l = {id:"layout" + (new Date).getTime(), initialized:!1, container:{}, north:{}, south:{}, east:{}, west:{}, center:{}, cookie:{}}, k = {allPanes:"north,south,west,east,center", borderPanes:"north,south,west,east", altSide:{north:"south",
                    south:"north", east:"west", west:"east"}, hidden:{visibility:"hidden"}, visible:{visibility:"visible"}, zIndex:{pane_normal:1, resizer_normal:2, iframe_mask:2, pane_sliding:100, pane_animate:1E3, resizer_drag:1E4}, resizers:{cssReq:{position:"absolute", padding:0, margin:0, fontSize:"1px", textAlign:"left", overflow:"hidden"}, cssDemo:{background:"#DDD", border:"none"}}, togglers:{cssReq:{position:"absolute", display:"block", padding:0, margin:0, overflow:"hidden", textAlign:"center", fontSize:"1px", cursor:"pointer", zIndex:1},
                    cssDemo:{background:"#AAA"}}, content:{cssReq:{position:"relative"}, cssDemo:{overflow:"auto", padding:"10px"}, cssDemoPane:{overflow:"hidden", padding:0}}, panes:{cssReq:{position:"absolute", margin:0}, cssDemo:{padding:"10px", background:"#FFF", border:"1px solid #BBB", overflow:"auto"}}, north:{side:"Top", sizeType:"Height", dir:"horz", cssReq:{top:0, bottom:"auto", left:0, right:0, width:"auto"}, pins:[]}, south:{side:"Bottom", sizeType:"Height", dir:"horz", cssReq:{top:"auto", bottom:0, left:0, right:0, width:"auto"}, pins:[]},
                    east:{side:"Right", sizeType:"Width", dir:"vert", cssReq:{left:"auto", right:0, top:"auto", bottom:"auto", height:"auto"}, pins:[]}, west:{side:"Left", sizeType:"Width", dir:"vert", cssReq:{left:0, right:"auto", top:"auto", bottom:"auto", height:"auto"}, pins:[]}, center:{dir:"center", cssReq:{left:"auto", right:"auto", top:"auto", bottom:"auto", height:"auto", width:"auto"}}}, E = {data:{}, set:function (a, b, c) {
                    E.clear(a);
                    E.data[a] = setTimeout(b, c)
                }, clear:function (a) {
                    var b = E.data;
                    b[a] && (clearTimeout(b[a]), delete b[a])
                }}, I = function (a) {
                    try {
                        return typeof a ==
                            "string" || typeof a == "object" && a.constructor.toString().match(/string/i) !== null
                    } catch (b) {
                        return!1
                    }
                }, y = function (a, b) {
                    return Math.max(a, b)
                }, Ta = function (a) {
                    var b, c = {cookie:{}, defaults:{fxSettings:{}}, north:{fxSettings:{}}, south:{fxSettings:{}}, east:{fxSettings:{}}, west:{fxSettings:{}}, center:{fxSettings:{}}}, a = a || {};
                    a.effects || a.cookie || a.defaults || a.north || a.south || a.west || a.east || a.center ? c = f.extend(!0, c, a) : f.each(a, function (a, e) {
                        b = a.split("__");
                        if (!b[1] || c[b[0]])c[b[1] ? b[0] : "defaults"][b[1] ? b[1] : b[0]] =
                            e
                    });
                    return c
                }, Ga = function (a, b, c) {
                    function d(h) {
                        var g = k[h];
                        g.doCallback ? (e.push(h), h = g.callback.split(",")[1], h != b && !f.inArray(h, e) >= 0 && d(h)) : (g.doCallback = !0, g.callback = a + "," + b + "," + (c ? 1 : 0))
                    }

                    var e = [];
                    f.each(k.borderPanes.split(","), function (a, b) {
                        if (k[b].isMoving)return d(b), !1
                    })
                }, Ha = function (a) {
                    a = k[a];
                    k.isLayoutBusy = !1;
                    delete a.isMoving;
                    if (a.doCallback && a.callback) {
                        a.doCallback = !1;
                        var b = a.callback.split(","), c = b[2] > 0 ? !0 : !1;
                        b[0] == "open" ? M(b[1], c) : b[0] == "close" && J(b[1], c);
                        if (!a.doCallback)a.callback =
                            null
                    }
                }, t = function (a, b) {
                    if (b) {
                        var c;
                        try {
                            if (typeof b == "function")c = b; else if (I(b))if (b.match(/,/)) {
                                var d = b.split(",");
                                c = eval(d[0]);
                                if (typeof c == "function" && d.length > 1)return c(d[1])
                            } else c = eval(b); else return;
                            if (typeof c == "function")return a && q[a] ? c(a, q[a], f.extend({}, l[a]), m[a], m.name) : c(da, f.extend({}, l), m, m.name)
                        } catch (e) {
                        }
                    }
                }, Ia = function (a, b) {
                    if (!a)return{};
                    a.jquery || (a = f(a));
                    var c = {display:a.css("display"), visibility:a.css("visibility")};
                    return b || c.display == "none" ? (a.css({display:"block", visibility:"hidden"}),
                        c) : {}
                }, Ja = function (a) {
                    if (!l.browser.mozilla) {
                        var b = q[a];
                        l[a].tagName == "IFRAME" ? b.css(k.hidden).css(k.visible) : b.find("IFRAME").css(k.hidden).css(k.visible)
                    }
                }, Z = function (a, b) {
                    a.jquery || (a = f(a));
                    var c = Ia(a), d = parseInt(f.curCSS(a[0], b, !0), 10) || 0;
                    a.css(c);
                    return d
                }, ia = function (a, b) {
                    a.jquery && (a = a[0]);
                    var c = "border" + b.substr(0, 1).toUpperCase() + b.substr(1);
                    return f.curCSS(a, c + "Style", !0) == "none" ? 0 : parseInt(f.curCSS(a, c + "Width", !0), 10) || 0
                }, K = function (a, b) {
                    var c = I(a), d = c ? q[a] : f(a);
                    isNaN(b) && (b = c ? P(a) : d.outerWidth());
                    if (b <= 0)return 0;
                    if (!l.browser.boxModel)return b;
                    c = b - ia(d, "Left") - ia(d, "Right") - Z(d, "paddingLeft") - Z(d, "paddingRight");
                    return y(0, c)
                }, L = function (a, b) {
                    var c = I(a), d = c ? q[a] : f(a);
                    isNaN(b) && (b = c ? P(a) : d.outerHeight());
                    if (b <= 0)return 0;
                    if (!l.browser.boxModel)return b;
                    c = b - ia(d, "Top") - ia(d, "Bottom") - Z(d, "paddingTop") - Z(d, "paddingBottom");
                    return y(0, c)
                }, ma = function (a) {
                    var b = k[a].dir, a = {minWidth:1001 - K(a, 1E3), minHeight:1001 - L(a, 1E3)};
                    if (b == "horz")a.minSize = a.minHeight;
                    if (b == "vert")a.minSize = a.minWidth;
                    return a
                },
                Ua = function (a, b, c) {
                    var d = a;
                    I(a) ? d = q[a] : a.jquery || (d = f(a));
                    a = L(d, b);
                    d.css({height:a, visibility:"visible"});
                    a > 0 && d.innerWidth() > 0 ? c && d.data("autoHidden") && (d.show().data("autoHidden", !1), l.browser.mozilla || d.css(k.hidden).css(k.visible)) : c && !d.data("autoHidden") && d.hide().data("autoHidden", !0)
                }, U = function (a, b, c) {
                    if (!c)c = k[a].dir;
                    I(b) && b.match(/%/) && (b = parseInt(b, 10) / 100);
                    if (b === 0)return 0; else if (b >= 1)return parseInt(b, 10); else if (b > 0) {
                        var a = m, d;
                        c == "horz" ? d = u.innerHeight - (q.north ? a.north.spacing_open :
                            0) - (q.south ? a.south.spacing_open : 0) : c == "vert" && (d = u.innerWidth - (q.west ? a.west.spacing_open : 0) - (q.east ? a.east.spacing_open : 0));
                        return Math.floor(d * b)
                    } else if (a == "center")return 0; else {
                        d = q[a];
                        var c = c == "horz" ? "height" : "width", a = Ia(d), e = d.css(c);
                        d.css(c, "auto");
                        b = c == "height" ? d.outerHeight() : d.outerWidth();
                        d.css(c, e).css(a);
                        return b
                    }
                }, P = function (a, b) {
                    var c = q[a], d = m[a], e = l[a], h = b ? d.spacing_open : 0, d = b ? d.spacing_closed : 0;
                    return!c || e.isHidden ? 0 : e.isClosed || e.isSliding && b ? d : k[a].dir == "horz" ? c.outerHeight() +
                        h : c.outerWidth() + h
                }, Q = function (a, b) {
                    var c = m[a], d = l[a], e = k[a], h = e.dir;
                    e.side.toLowerCase();
                    e.sizeType.toLowerCase();
                    var e = b != void 0 ? b : d.isSliding, g = c.spacing_open, i = k.altSide[a], f = l[i], O = q[i], A = !O || f.isVisible === !1 || f.isSliding ? 0 : h == "horz" ? O.outerHeight() : O.outerWidth(), i = (!O || f.isHidden ? 0 : m[i][f.isClosed !== !1 ? "spacing_closed" : "spacing_open"]) || 0, f = h == "horz" ? u.innerHeight : u.innerWidth, O = ma("center"), O = h == "horz" ? y(m.center.minHeight, O.minHeight) : y(m.center.minWidth, O.minWidth), e = f - g - (e ? 0 : U("center",
                        O, h) + A + i), h = d.minSize = y(U(a, c.minSize), ma(a).minSize), g = c.maxSize ? U(a, c.maxSize) : 1E5, e = d.maxSize = Math.min(g, e), d = d.resizerPosition = {}, g = u.insetTop, A = u.insetLeft, i = u.innerWidth, f = u.innerHeight, c = c.spacing_open;
                    switch (a) {
                        case "north":
                            d.min = g + h;
                            d.max = g + e;
                            break;
                        case "west":
                            d.min = A + h;
                            d.max = A + e;
                            break;
                        case "south":
                            d.min = g + f - e - c;
                            d.max = g + f - h - c;
                            break;
                        case "east":
                            d.min = A + i - e - c, d.max = A + i - h - c
                    }
                }, $ = function (a) {
                    var b = {}, c = b.css = {}, d = {}, e, h, g = a.offset();
                    b.offsetLeft = g.left;
                    b.offsetTop = g.top;
                    f.each("Left,Right,Top,Bottom".split(","),
                        function (g, f) {
                            e = c["border" + f] = ia(a, f);
                            h = c["padding" + f] = Z(a, "padding" + f);
                            d[f] = e + h;
                            b["inset" + f] = h
                        });
                    b.offsetWidth = a.innerWidth();
                    b.offsetHeight = a.innerHeight();
                    b.outerWidth = a.outerWidth();
                    b.outerHeight = a.outerHeight();
                    b.innerWidth = b.outerWidth - d.Left - d.Right;
                    b.innerHeight = b.outerHeight - d.Top - d.Bottom;
                    c.width = a.width();
                    c.height = a.height();
                    return b
                }, na = function (a, b) {
                    var c = {}, d = a[0].style, e = b.split(","), f = "Top,Bottom,Left,Right".split(","), g = "Color,Style,Width".split(","), i, n, k, l, m, j;
                    for (l = 0; l < e.length; l++)if (i =
                        e[l], i.match(/(border|padding|margin)$/))for (m = 0; m < 4; m++)if (n = f[m], i == "border")for (j = 0; j < 3; j++)k = g[j], c[i + n + k] = d[i + n + k]; else c[i + n] = d[i + n]; else c[i] = d[i];
                    return c
                }, sa = function (a, b) {
                    var c = f(a), d = c.data("layoutRole"), e = c.data("layoutEdge"), h = m[e][d + "Class"], e = "-" + e, g = c.hasClass(h + "-closed") ? "-closed" : "-open", i = g == "-closed" ? "-open" : "-closed", g = h + "-hover " + (h + e + "-hover ") + (h + g + "-hover ") + (h + e + g + "-hover ");
                    b && (g += h + i + "-hover " + (h + e + i + "-hover "));
                    d == "resizer" && c.hasClass(h + "-sliding") && (g += h + "-sliding-hover " +
                        (h + e + "-sliding-hover "));
                    return f.trim(g)
                }, ta = function (a, b) {
                    var c = f(b || this);
                    a && c.data("layoutRole") == "toggler" && a.stopPropagation();
                    c.addClass(sa(c))
                }, R = function (a, b) {
                    var c = f(b || this);
                    c.removeClass(sa(c, !0))
                }, Ka = function (a) {
                    f("body").disableSelection();
                    ta(a, this)
                }, ua = function (a, b) {
                    var c = b || this, d = f(c).data("layoutEdge"), e = d + "ResizerLeave";
                    E.clear(d + "_openSlider");
                    E.clear(e);
                    b ? l[d].isResizing || f("body").enableSelection() : (R(a, this), E.set(e, function () {
                        ua(a, c)
                    }, 200))
                }, Va = function () {
                    var a = Number(m.resizeWithWindowDelay) ||
                        100;
                    a > 0 && (E.clear("winResize"), E.set("winResize", function () {
                        E.clear("winResize");
                        E.clear("winResizeRepeater");
                        ea()
                    }, a), E.data.winResizeRepeater || La())
                }, La = function () {
                    var a = Number(m.resizeWithWindowMaxDelay);
                    a > 0 && E.set("winResizeRepeater", function () {
                        La();
                        ea()
                    }, a)
                }, Ma = function () {
                    var a = m;
                    l.cookie = qa();
                    t(null, a.onunload_start);
                    a.useStateCookie && a.cookie.autoSave && pa();
                    t(null, a.onunload_end || a.onunload)
                }, Na = function (a) {
                    if (!a || a == "all")a = k.borderPanes;
                    f.each(a.split(","), function (a, c) {
                        var d = m[c];
                        if (d.enableCursorHotkey ||
                            d.customHotkey)return f(document).bind("keydown." + v, o), !1
                    })
                }, Wa = function () {
                    function a(a) {
                        for (var c in b)a[c] != void 0 && (a[b[c]] = a[c], delete a[c])
                    }

                    j = Ta(j);
                    var b = {applyDefaultStyles:"applyDemoStyles"};
                    a(j.defaults);
                    f.each(k.allPanes.split(","), function (b, c) {
                        a(j[c])
                    });
                    j.effects && (f.extend(ra, j.effects), delete j.effects);
                    f.extend(m.cookie, j.cookie);
                    f.each("name,containerClass,zIndex,scrollToBookmarkOnLoad,resizeWithWindow,resizeWithWindowDelay,resizeWithWindowMaxDelay,onresizeall,onresizeall_start,onresizeall_end,onload,onload_start,onload_end,onunload,onunload_start,onunload_end,autoBindCustomButtons,useStateCookie".split(","),
                        function (a, b) {
                            j[b] !== void 0 ? m[b] = j[b] : j.defaults[b] !== void 0 && (m[b] = j.defaults[b], delete j.defaults[b])
                        });
                    f.each("paneSelector,resizerCursor,customHotkey".split(","), function (a, b) {
                        delete j.defaults[b]
                    });
                    f.extend(!0, m.defaults, j.defaults);
                    k.center = f.extend(!0, {}, k.panes, k.center);
                    var c = m.zIndex;
                    if (c === 0 || c > 0)k.zIndex.pane_normal = c, k.zIndex.resizer_normal = c + 1, k.zIndex.iframe_mask = c + 1;
                    f.extend(m.center, j.center);
                    var d = f.extend(!0, {}, m.defaults, j.defaults, m.center), c = "paneClass,contentSelector,applyDemoStyles,triggerEventsOnLoad,showOverflowOnHover,onresize,onresize_start,onresize_end,resizeNestedLayout,resizeContentWhileDragging,onsizecontent,onsizecontent_start,onsizecontent_end".split(",");
                    f.each(c, function (a, b) {
                        m.center[b] = d[b]
                    });
                    var e, h = m.defaults;
                    f.each(k.borderPanes.split(","), function (a, b) {
                        k[b] = f.extend(!0, {}, k.panes, k[b]);
                        e = m[b] = f.extend(!0, {}, m.defaults, m[b], j.defaults, j[b]);
                        if (!e.paneClass)e.paneClass = "ui-layout-pane";
                        if (!e.resizerClass)e.resizerClass = "ui-layout-resizer";
                        if (!e.togglerClass)e.togglerClass = "ui-layout-toggler";
                        f.each(["_open", "_close", ""], function (a, c) {
                            var d = "fxName" + c, g = "fxSpeed" + c, k = "fxSettings" + c;
                            e[d] = j[b][d] || j[b].fxName || j.defaults[d] || j.defaults.fxName ||
                                e[d] || e.fxName || h[d] || h.fxName || "none";
                            var l = e[d];
                            if (l == "none" || !f.effects || !f.effects[l] || !ra[l] && !e[k] && !e.fxSettings)l = e[d] = "none";
                            l = ra[l] || {};
                            d = l.all || {};
                            l = l[b] || {};
                            e[k] = f.extend({}, d, l, h.fxSettings || {}, h[k] || {}, e.fxSettings, e[k], j.defaults.fxSettings, j.defaults[k] || {}, j[b].fxSettings, j[b][k] || {});
                            e[g] = j[b][g] || j[b].fxSpeed || j.defaults[g] || j.defaults.fxSpeed || e[g] || e[k].duration || e.fxSpeed || e.fxSettings.duration || h.fxSpeed || h.fxSettings.duration || l.duration || d.duration || "normal"
                        })
                    })
                }, Oa = function (a) {
                    a =
                        m[a].paneSelector;
                    if (a.substr(0, 1) === "#")return G.find(a).eq(0); else {
                        var b = G.children(a).eq(0);
                        return b.length ? b : G.children("form:first").children(a).eq(0)
                    }
                }, Xa = function () {
                    f.each(k.allPanes.split(","), function (a, b) {
                        Pa(b)
                    });
                    va();
                    f.each(k.borderPanes.split(","), function (a, b) {
                        q[b] && l[b].isVisible && (Q(b), V(b))
                    });
                    W("center");
                    f.each(k.allPanes.split(","), function (a, b) {
                        var c = m[b];
                        q[b] && l[b].isVisible && (c.triggerEventsOnLoad && t(b, c.onresize_end || c.onresize), aa(b))
                    });
                    G.innerHeight() < 2 && alert(D.errContainerHeight.replace(/CONTAINER/,
                        u.ref))
                }, Pa = function (a) {
                    var b = m[a], c = l[a], d = k[a], e = d.dir, f = a == "center", g = {}, i = q[a], n;
                    i ? wa(a) : N[a] = !1;
                    i = q[a] = Oa(a);
                    if (i.length) {
                        i.data("layoutCSS") || i.data("layoutCSS", na(i, "position,top,left,bottom,right,width,height,overflow,zIndex,display,backgroundColor,padding,margin,border"));
                        i.data("parentLayout", da).data("layoutRole", "pane").data("layoutEdge", a).css(d.cssReq).css("zIndex", k.zIndex.pane_normal).css(b.applyDemoStyles ? d.cssDemo : {}).addClass(b.paneClass + " " + b.paneClass + "-" + a).bind("mouseenter." +
                            v, ta).bind("mouseleave." + v, R);
                        Qa(a, !1);
                        if (!f)n = c.size = U(a, b.size), d = U(a, b.minSize) || 1, f = U(a, b.maxSize) || 1E5, n > 0 && (n = y(Math.min(n, f), d)), c.isClosed = !1, c.isSliding = !1, c.isResizing = !1, c.isHidden = !1;
                        c.tagName = i.attr("tagName");
                        c.edge = a;
                        c.noRoom = !1;
                        c.isVisible = !0;
                        switch (a) {
                            case "north":
                                g.top = u.insetTop;
                                g.left = u.insetLeft;
                                g.right = u.insetRight;
                                break;
                            case "south":
                                g.bottom = u.insetBottom;
                                g.left = u.insetLeft;
                                g.right = u.insetRight;
                                break;
                            case "west":
                                g.left = u.insetLeft;
                                break;
                            case "east":
                                g.right = u.insetRight
                        }
                        if (e ==
                            "horz")g.height = y(1, L(a, n)); else if (e == "vert")g.width = y(1, K(a, n));
                        i.css(g);
                        e != "horz" && W(a, !0);
                        c.noRoom || i.css({visibility:"visible", display:"block"});
                        b.initClosed && b.closable ? J(a, !0, !0) : (b.initHidden || b.initClosed) && la(a);
                        b.showOverflowOnHover && i.hover(p, r);
                        l.initialized && (va(a), Na(a), ea(), c.isVisible && (b.triggerEventsOnLoad && t(a, b.onresize_end || b.onresize), aa(a)))
                    } else q[a] = !1
                }, va = function (a) {
                    if (!a || a == "all")a = k.borderPanes;
                    f.each(a.split(","), function (a, c) {
                        var d = q[c];
                        w[c] = !1;
                        B[c] = !1;
                        if (d) {
                            var d =
                                m[c], e = l[c], h = d.resizerClass, g = d.togglerClass;
                            k[c].side.toLowerCase();
                            var i = "-" + c, n = w[c] = f("<div></div>"), j = d.closable ? B[c] = f("<div></div>") : !1;
                            !e.isVisible && d.slidable && n.attr("title", d.sliderTip).css("cursor", d.sliderCursor);
                            n.attr("id", d.paneSelector.substr(0, 1) == "#" ? d.paneSelector.substr(1) + "-resizer" : "").data("parentLayout", da).data("layoutRole", "resizer").data("layoutEdge", c).css(k.resizers.cssReq).css("zIndex", k.zIndex.resizer_normal).css(d.applyDemoStyles ? k.resizers.cssDemo : {}).addClass(h +
                                " " + h + i).appendTo(G);
                            j && (j.attr("id", d.paneSelector.substr(0, 1) == "#" ? d.paneSelector.substr(1) + "-toggler" : "").data("parentLayout", da).data("layoutRole", "toggler").data("layoutEdge", c).css(k.togglers.cssReq).css(d.applyDemoStyles ? k.togglers.cssDemo : {}).addClass(g + " " + g + i).appendTo(n), d.togglerContent_open && f("<span>" + d.togglerContent_open + "</span>").data("layoutRole", "togglerContent").data("layoutEdge", c).addClass("content content-open").css("display", "none").appendTo(j), d.togglerContent_closed && f("<span>" +
                                d.togglerContent_closed + "</span>").data("layoutRole", "togglerContent").data("layoutEdge", c).addClass("content content-closed").css("display", "none").appendTo(j), Ra(c));
                            Ya(c);
                            e.isVisible ? xa(c) : (ya(c), ba(c, !0))
                        }
                    });
                    ja("all")
                }, Qa = function (a, b) {
                    var c = m[a], d = c.contentSelector, e = q[a], f;
                    d && (f = N[a] = c.findNestedContent ? e.find(d).eq(0) : e.children(d).eq(0));
                    f && f.length ? (f.data("layoutCSS") || f.data("layoutCSS", na(f, "height")), f.css(k.content.cssReq), c.applyDemoStyles && (f.css(k.content.cssDemo), e.css(k.content.cssDemoPane)),
                        l[a].content = {}, b !== !1 && fa(a)) : N[a] = !1
                }, Za = function () {
                    var a;
                    f.each("toggle,open,close,pin,toggle-slide,open-slide".split(","), function (b, c) {
                        f.each(k.borderPanes.split(","), function (b, e) {
                            f(".ui-layout-button-" + c + "-" + e).each(function () {
                                a = f(this).data("layoutName") || f(this).attr("layoutName");
                                (a == void 0 || a == m.name) && C(this, c, e)
                            })
                        })
                    })
                }, Ya = function (a) {
                    var b = typeof f.fn.draggable == "function", c;
                    if (!a || a == "all")a = k.borderPanes;
                    f.each(a.split(","), function (a, e) {
                        var h = m[e], g = l[e], i = k[e], n = i.dir == "horz" ? "top" :
                            "left", j, A;
                        if (!b || !q[e] || !h.resizable)return h.resizable = !1, !0;
                        var p = w[e], o = h.resizerClass, r = o + "-drag", y = o + "-" + e + "-drag", x = o + "-dragging", D = o + "-" + e + "-dragging", B = o + "-dragging-limit", C = o + "-" + e + "-dragging-limit", z = !1;
                        g.isClosed || p.attr("title", h.resizerTip).css("cursor", h.resizerCursor);
                        p.bind("mouseenter." + v, Ka).bind("mouseleave." + v, ua);
                        p.draggable({containment:G[0], axis:i.dir == "horz" ? "y" : "x", delay:0, distance:1, helper:"clone", opacity:h.resizerDragOpacity, addClasses:!1, zIndex:k.zIndex.resizer_drag, start:function () {
                            h =
                                m[e];
                            g = l[e];
                            A = h.resizeWhileDragging;
                            if (!1 === t(e, h.ondrag_start))return!1;
                            k.isLayoutBusy = !0;
                            g.isResizing = !0;
                            E.clear(e + "_closeSlider");
                            Q(e);
                            j = g.resizerPosition;
                            p.addClass(r + " " + y);
                            z = !1;
                            c = f(h.maskIframesOnResize === !0 ? "iframe" : h.maskIframesOnResize).filter(":visible");
                            var a, b = 0;
                            c.each(function () {
                                a = "ui-layout-mask-" + ++b;
                                f(this).data("layoutMaskID", a);
                                f('<div id="' + a + '" class="ui-layout-mask ui-layout-mask-' + e + '"/>').css({background:"#fff", opacity:"0.001", zIndex:k.zIndex.iframe_mask, position:"absolute",
                                    width:this.offsetWidth + "px", height:this.offsetHeight + "px"}).css(f(this).position()).appendTo(this.parentNode)
                            });
                            f("body").disableSelection()
                        }, drag:function (a, b) {
                            z || (b.helper.addClass(x + " " + D).css({right:"auto", bottom:"auto"}).children().css("visibility", "hidden"), z = !0, g.isSliding && q[e].css("zIndex", k.zIndex.pane_sliding));
                            var c = 0;
                            if (b.position[n] < j.min)b.position[n] = j.min, c = -1; else if (b.position[n] > j.max)b.position[n] = j.max, c = 1;
                            c ? (b.helper.addClass(B + " " + C), window.defaultStatus = "Panel has reached its " +
                                (c > 0 && e.match(/north|west/) || c < 0 && e.match(/south|east/) ? "maximum" : "minimum") + " size") : (b.helper.removeClass(B + " " + C), window.defaultStatus = "");
                            A && F(a, b, e)
                        }, stop:function (a, b) {
                            f("body").enableSelection();
                            window.defaultStatus = "";
                            p.removeClass(r + " " + y);
                            g.isResizing = !1;
                            k.isLayoutBusy = !1;
                            F(a, b, e, !0)
                        }});
                        var F = function (a, b, d, e) {
                            var a = b.position, b = k[d], g;
                            switch (d) {
                                case "north":
                                    g = a.top;
                                    break;
                                case "west":
                                    g = a.left;
                                    break;
                                case "south":
                                    g = u.offsetHeight - a.top - h.spacing_open;
                                    break;
                                case "east":
                                    g = u.offsetWidth - a.left -
                                        h.spacing_open
                            }
                            if (e) {
                                if (f("div.ui-layout-mask").each(function () {
                                    this.parentNode.removeChild(this)
                                }), !1 === t(d, h.ondrag_end || h.ondrag))return!1
                            } else c.each(function () {
                                f("#" + f(this).data("layoutMaskID")).css(f(this).position()).css({width:this.offsetWidth + "px", height:this.offsetHeight + "px"})
                            });
                            za(d, g - u["inset" + b.side])
                        }
                    })
                }, wa = function (a, b, c) {
                    if (q[a]) {
                        var d = q[a], e = N[a], h = w[a], g = B[a], i = m[a].paneClass, n = i + "-" + a, i = [i, i + "-open", i + "-closed", i + "-sliding", n, n + "-open", n + "-closed", n + "-sliding"];
                        f.merge(i, sa(d,
                            !0));
                        d && d.length && (b && !d.data("layoutContainer") && (!e || !e.length || !e.data("layoutContainer")) ? d.remove() : (d.removeClass(i.join(" ")).removeData("layoutParent").removeData("layoutRole").removeData("layoutEdge").removeData("autoHidden").unbind("." + v), d.data("layoutContainer") || d.css(d.data("layoutCSS")).removeData("layoutCSS"), e && e.length && !e.data("layoutContainer") && e.css(e.data("layoutCSS")).removeData("layoutCSS")));
                        g && g.length && g.remove();
                        h && h.length && h.remove();
                        q[a] = N[a] = w[a] = B[a] = !1;
                        c || (ea(),
                            l[a] = {})
                    }
                }, la = function (a, b) {
                    var c = m[a], d = l[a], e = q[a], f = w[a];
                    if (e && !d.isHidden && !(l.initialized && !1 === t(a, c.onhide_start)))if (d.isSliding = !1, f && f.hide(), !l.initialized || d.isClosed) {
                        if (d.isClosed = !0, d.isHidden = !0, d.isVisible = !1, e.hide(), W(k[a].dir == "horz" ? "all" : "center"), l.initialized || c.triggerEventsOnLoad)t(a, c.onhide_end || c.onhide)
                    } else d.isHiding = !0, J(a, !1, b)
                }, ha = function (a, b, c, d) {
                    var e = l[a];
                    if (q[a] && e.isHidden && !1 !== t(a, m[a].onshow_start))e.isSliding = !1, e.isShowing = !0, b === !1 ? J(a, !0) : M(a, !1, c, d)
                },
                ca = function (a, b) {
                    I(a) || (a.stopImmediatePropagation(), a = f(this).data("layoutEdge"));
                    var c = l[I(a) ? f.trim(a) : a == void 0 || a == null ? "" : a];
                    c.isHidden ? ha(a) : c.isClosed ? M(a, !!b) : J(a)
                }, $a = function (a) {
                    var b = l[a];
                    q[a].hide();
                    b.isClosed = !0;
                    b.isVisible = !1
                }, J = function (a, b, c, d) {
                    function e() {
                        if (i.isClosed) {
                            ba(a, !0);
                            var b = k.altSide[a];
                            l[b].noRoom && (Q(b), V(b));
                            if (!d && (l.initialized || g.triggerEventsOnLoad))n || t(a, g.onclose_end || g.onclose), n && t(a, g.onshow_end || g.onshow), j && t(a, g.onhide_end || g.onhide)
                        }
                        Ha(a)
                    }

                    if (l.initialized) {
                        var f =
                            q[a], g = m[a], i = l[a], c = !c && !i.isClosed && g.fxName_close != "none", n = i.isShowing, j = i.isHiding;
                        delete i.isShowing;
                        delete i.isHiding;
                        if (f && (g.closable || n || j) && (b || !i.isClosed || n))if (k.isLayoutBusy)Ga("close", a, b); else if (n || !1 !== t(a, g.onclose_start)) {
                            k[a].isMoving = !0;
                            k.isLayoutBusy = !0;
                            i.isClosed = !0;
                            i.isVisible = !1;
                            if (j)i.isHidden = !0; else if (n)i.isHidden = !1;
                            i.isSliding ? ka(a, !1) : W(k[a].dir == "horz" ? "all" : "center", !1);
                            ya(a);
                            c ? (oa(a, !0), f.hide(g.fxName_close, g.fxSettings_close, g.fxSpeed_close, function () {
                                oa(a, !1);
                                e()
                            })) : (f.hide(), e())
                        }
                    } else $a(a)
                }, ya = function (a) {
                    var b = w[a], c = B[a], d = m[a], e = k[a].side.toLowerCase(), h = d.resizerClass, g = d.togglerClass, i = "-" + a;
                    b.css(e, u["inset" + k[a].side]).removeClass(h + "-open " + h + i + "-open").removeClass(h + "-sliding " + h + i + "-sliding").addClass(h + "-closed " + h + i + "-closed").unbind("dblclick." + v);
                    d.resizable && typeof f.fn.draggable == "function" && b.draggable("disable").removeClass("ui-state-disabled").css("cursor", "default").attr("title", "");
                    c && (c.removeClass(g + "-open " + g + i + "-open").addClass(g +
                        "-closed " + g + i + "-closed").attr("title", d.togglerTip_closed), c.children(".content-open").hide(), c.children(".content-closed").css("display", "block"));
                    S(a, !1);
                    l.initialized && ja("all")
                }, M = function (a, b, c, d) {
                    function e() {
                        i.isVisible && (Ja(a), i.isSliding || W(k[a].dir == "vert" ? "center" : "all", !1), xa(a));
                        Ha(a)
                    }

                    var f = q[a], g = m[a], i = l[a], c = !c && i.isClosed && g.fxName_open != "none", n = i.isShowing;
                    delete i.isShowing;
                    if (f && (g.resizable || g.closable || n) && (!i.isVisible || i.isSliding))if (i.isHidden && !n)ha(a, !0); else if (k.isLayoutBusy)Ga("open",
                        a, b); else if (Q(a, b), !1 !== t(a, g.onopen_start))if (i.minSize > i.maxSize)S(a, !1), !d && g.noRoomToOpenTip && alert(g.noRoomToOpenTip); else {
                        k[a].isMoving = !0;
                        k.isLayoutBusy = !0;
                        b ? ka(a, !0) : i.isSliding ? ka(a, !1) : g.slidable && ba(a, !1);
                        i.noRoom = !1;
                        V(a);
                        i.isVisible = !0;
                        i.isClosed = !1;
                        if (n)i.isHidden = !1;
                        c ? (oa(a, !0), f.show(g.fxName_open, g.fxSettings_open, g.fxSpeed_open, function () {
                            oa(a, !1);
                            e()
                        })) : (f.show(), e())
                    }
                }, xa = function (a, b) {
                    var c = q[a], d = w[a], e = B[a], h = m[a], g = l[a], i = k[a].side.toLowerCase(), n = h.resizerClass, j = h.togglerClass,
                        A = "-" + a;
                    d.css(i, u["inset" + k[a].side] + P(a)).removeClass(n + "-closed " + n + A + "-closed").addClass(n + "-open " + n + A + "-open");
                    g.isSliding ? d.addClass(n + "-sliding " + n + A + "-sliding") : d.removeClass(n + "-sliding " + n + A + "-sliding");
                    h.resizerDblClickToggle && d.bind("dblclick", ca);
                    R(0, d);
                    h.resizable && typeof f.fn.draggable == "function" ? d.draggable("enable").css("cursor", h.resizerCursor).attr("title", h.resizerTip) : g.isSliding || d.css("cursor", "default");
                    e && (e.removeClass(j + "-closed " + j + A + "-closed").addClass(j + "-open " + j + A +
                        "-open").attr("title", h.togglerTip_open), R(0, e), e.children(".content-closed").hide(), e.children(".content-open").css("display", "block"));
                    S(a, !g.isSliding);
                    f.extend(g, $(c));
                    l.initialized && (ja("all"), fa(a, !0));
                    if (!b && (l.initialized || h.triggerEventsOnLoad) && c.is(":visible"))t(a, h.onopen_end || h.onopen), g.isShowing && t(a, h.onshow_end || h.onshow), l.initialized && (t(a, h.onresize_end || h.onresize), aa(a))
                }, Sa = function (a) {
                    function b() {
                        e.isClosed ? k[d].isMoving || M(d, !0) : ka(d, !0)
                    }

                    var c = I(a) ? null : a, d = c ? f(this).data("layoutEdge") :
                        a, e = l[d], a = m[d].slideDelay_open;
                    c && c.stopImmediatePropagation();
                    e.isClosed && c && c.type == "mouseenter" && a > 0 ? E.set(d + "_openSlider", b, a) : b()
                }, Aa = function (a) {
                    function b() {
                        e.isClosed ? ka(d, !1) : k[d].isMoving || J(d)
                    }

                    var c = I(a) ? null : a, d = c ? f(this).data("layoutEdge") : a, a = m[d], e = l[d], h = k[d].isMoving ? 1E3 : 300;
                    if (!e.isClosed && !e.isResizing)if (a.slideTrigger_close == "click")b(); else if (!a.preventQuickSlideClose || !k.isLayoutBusy)if (!a.preventPrematureSlideClose || !c || !f.layout.isMouseOverElem(c, q[d]))c ? E.set(d + "_closeSlider",
                        b, y(a.slideDelay_close, h)) : b()
                }, oa = function (a, b) {
                    var c = q[a];
                    if (b)c.css({zIndex:k.zIndex.pane_animate}), a == "south" ? c.css({top:u.insetTop + u.innerHeight - c.outerHeight()}) : a == "east" && c.css({left:u.insetLeft + u.innerWidth - c.outerWidth()}); else {
                        c.css({zIndex:l[a].isSliding ? k.zIndex.pane_sliding : k.zIndex.pane_normal});
                        a == "south" ? c.css({top:"auto"}) : a == "east" && c.css({left:"auto"});
                        var d = m[a];
                        l.browser.msie && d.fxOpacityFix && d.fxName_open != "slide" && c.css("filter") && c.css("opacity") == 1 && c[0].style.removeAttribute("filter")
                    }
                },
                ba = function (a, b) {
                    var c = m[a], d = w[a], e = c.slideTrigger_open.toLowerCase();
                    if (d && (!b || c.slidable)) {
                        if (e.match(/mouseover/))e = c.slideTrigger_open = "mouseenter"; else if (!e.match(/click|dblclick|mouseenter/))e = c.slideTrigger_open = "click";
                        d[b ? "bind" : "unbind"](e + "." + v, Sa).css("cursor", b ? c.sliderCursor : "default").attr("title", b ? c.sliderTip : "")
                    }
                }, ka = function (a, b) {
                    function c(b) {
                        E.clear(a + "_closeSlider");
                        b.stopPropagation()
                    }

                    var d = m[a], e = l[a], f = k.zIndex, g = d.slideTrigger_close.toLowerCase(), i = b ? "bind" : "unbind", n =
                        q[a], j = w[a];
                    e.isSliding = b;
                    E.clear(a + "_closeSlider");
                    b && ba(a, !1);
                    n.css("zIndex", b ? f.pane_sliding : f.pane_normal);
                    j.css("zIndex", b ? f.pane_sliding : f.resizer_normal);
                    if (!g.match(/click|mouseleave/))g = d.slideTrigger_close = "mouseleave";
                    j[i](g, Aa);
                    g == "mouseleave" && (n[i]("mouseleave." + v, Aa), j[i]("mouseenter." + v, c), n[i]("mouseenter." + v, c));
                    b ? g == "click" && !d.resizable && (j.css("cursor", b ? d.sliderCursor : "default"), j.attr("title", b ? d.togglerTip_open : "")) : E.clear(a + "_closeSlider")
                }, V = function (a, b, c, d) {
                    var b = m[a],
                        e = l[a], f = k[a], g = q[a], i = w[a], n = f.dir == "vert", j = !1;
                    if (a == "center" || n && e.noVerticalRoom)if ((j = e.maxHeight > 0) && e.noRoom) {
                        g.show();
                        i && i.show();
                        e.isVisible = !0;
                        e.noRoom = !1;
                        if (n)e.noVerticalRoom = !1;
                        Ja(a)
                    } else if (!j && !e.noRoom)g.hide(), i && i.hide(), e.isVisible = !1, e.noRoom = !0;
                    if (a != "center")if (e.minSize <= e.maxSize) {
                        if (e.size > e.maxSize ? Y(a, e.maxSize, c, d) : e.size < e.minSize ? Y(a, e.minSize, c, d) : i && g.is(":visible") && (c = f.side.toLowerCase(), d = e.size + u["inset" + f.side], Z(i, c) != d && i.css(c, d)), e.noRoom)e.wasOpen && b.closable ?
                            b.autoReopen ? M(a, !1, !0, !0) : e.noRoom = !1 : ha(a, e.wasOpen, !0, !0)
                    } else if (!e.noRoom)e.noRoom = !0, e.wasOpen = !e.isClosed && !e.isSliding, e.isClosed || (b.closable ? J(a, !0, !0) : la(a, !0))
                }, za = function (a, b, c) {
                    var d = m[a], e = d.resizeWhileDragging && !k.isLayoutBusy;
                    d.autoResize = !1;
                    Y(a, b, c, e)
                }, Y = function (a, b, c, d) {
                    var e = m[a], h = l[a], g = q[a], i = w[a], n = k[a].side.toLowerCase(), j = "inset" + k[a].side, A = k.isLayoutBusy && !e.triggerEventsWhileDragging, o;
                    Q(a);
                    o = h.size;
                    b = U(a, b);
                    b = y(b, U(a, e.minSize));
                    b = Math.min(b, h.maxSize);
                    if (b < h.minSize)V(a,
                        !1, c); else if (d || b != o)!c && l.initialized && h.isVisible && t(a, e.onresize_start), g.css(k[a].sizeType.toLowerCase(), y(1, k[a].dir == "horz" ? L(a, b) : K(a, b))), h.size = b, f.extend(h, $(g)), i && g.is(":visible") && i.css(n, b + u[j]), fa(a), !c && !A && l.initialized && h.isVisible && (t(a, e.onresize_end || e.onresize), aa(a)), c || (h.isSliding || W(k[a].dir == "horz" ? "all" : "center", A, d), ja("all")), a = k.altSide[a], b < o && l[a].noRoom && (Q(a), V(a, !1, c))
                }, W = function (a, b, c) {
                    if (!a || a == "all")a = "east,west,center";
                    f.each(a.split(","), function (a, e) {
                        if (q[e]) {
                            var h =
                                m[e], g = l[e], i = q[e], n = !0, k = {}, n = {top:P("north", !0), bottom:P("south", !0), left:P("west", !0), right:P("east", !0), width:0, height:0};
                            n.width = u.innerWidth - n.left - n.right;
                            n.height = u.innerHeight - n.bottom - n.top;
                            n.top += u.insetTop;
                            n.bottom += u.insetBottom;
                            n.left += u.insetLeft;
                            n.right += u.insetRight;
                            f.extend(g, $(i));
                            if (e == "center") {
                                if (!c && g.isVisible && n.width == g.outerWidth && n.height == g.outerHeight)return!0;
                                f.extend(g, ma(e), {maxWidth:n.width, maxHeight:n.height});
                                k = n;
                                k.width = K(e, n.width);
                                k.height = L(e, n.height);
                                n = k.width >
                                    0 && k.height > 0;
                                if (!n && !l.initialized && h.minWidth > 0) {
                                    var j = h.minWidth - g.outerWidth, o = m.east.minSize || 0, p = m.west.minSize || 0, r = l.east.size, w = l.west.size, v = r, x = w;
                                    j > 0 && l.east.isVisible && r > o && (v = y(r - o, r - j), j -= r - v);
                                    j > 0 && l.west.isVisible && w > p && (x = y(w - p, w - j), j -= w - x);
                                    if (j == 0) {
                                        r != o && Y("east", v, !0);
                                        w != p && Y("west", x, !0);
                                        W("center", b, c);
                                        return
                                    }
                                }
                            } else {
                                g.isVisible && !g.noVerticalRoom && f.extend(g, $(i), ma(e));
                                if (!c && !g.noVerticalRoom && n.height == g.outerHeight)return!0;
                                k.top = n.top;
                                k.bottom = n.bottom;
                                k.height = L(e, n.height);
                                g.maxHeight = y(0, k.height);
                                n = g.maxHeight > 0;
                                if (!n)g.noVerticalRoom = !0
                            }
                            n ? (!b && l.initialized && t(e, h.onresize_start), i.css(k), g.noRoom && !g.isClosed && !g.isHidden && V(e), g.isVisible && (f.extend(g, $(i)), l.initialized && fa(e))) : !g.noRoom && g.isVisible && V(e);
                            if (!g.isVisible)return!0;
                            if (e == "center")g = l.browser, g = g.isIE6 || g.msie && !g.boxModel, q.north && (g || l.north.tagName == "IFRAME") && q.north.css("width", K(q.north, u.innerWidth)), q.south && (g || l.south.tagName == "IFRAME") && q.south.css("width", K(q.south, u.innerWidth));
                            !b &&
                                l.initialized && (t(e, h.onresize_end || h.onresize), aa(e))
                        }
                    })
                }, ea = function () {
                    f.extend(l.container, $(G));
                    if (u.outerHeight) {
                        if (!1 === t(null, m.onresizeall_start))return!1;
                        var a, b, c;
                        f.each(["south", "north", "east", "west"], function (a, e) {
                            q[e] && (c = l[e], b = m[e], b.autoResize && c.size != b.size ? Y(e, b.size, !0, !0) : (Q(e), V(e, !1, !0, !0)))
                        });
                        W("all", !0, !0);
                        ja("all");
                        b = m;
                        f.each(k.allPanes.split(","), function (c, e) {
                            if ((a = q[e]) && l[e].isVisible)t(e, b[e].onresize_end || b[e].onresize), aa(e)
                        });
                        t(null, b.onresizeall_end || b.onresizeall)
                    }
                },
                aa = function (a) {
                    var b = q[a], c = N[a];
                    m[a].resizeNestedLayout && (b.data("layoutContainer") ? b.layout().resizeAll() : c && c.data("layoutContainer") && c.layout().resizeAll())
                }, fa = function (a, b) {
                    if (!a || a == "all")a = k.allPanes;
                    f.each(a.split(","), function (a, d) {
                        function e(a) {
                            return y(j.css.paddingBottom, parseInt(a.css("marginBottom"), 10) || 0)
                        }

                        function f() {
                            var a = m[d].contentIgnoreSelector, a = i.nextAll().not(a || ":lt(0)"), b = a.filter(":visible"), c = b.filter(":last");
                            o = {top:i[0].offsetTop, height:i.outerHeight(), numFooters:a.length,
                                hiddenFooters:a.length - b.length, spaceBelow:0};
                            o.spaceAbove = o.top;
                            o.bottom = o.top + o.height;
                            o.spaceBelow = c.length ? c[0].offsetTop + c.outerHeight() - o.bottom + e(c) : e(i)
                        }

                        var g = q[d], i = N[d], n = m[d], j = l[d], o = j.content;
                        if (!g || !i || !g.is(":visible"))return!0;
                        if (!1 !== t(null, n.onsizecontent_start)) {
                            if (!k.isLayoutBusy || o.top == void 0 || b || n.resizeContentWhileDragging)f(), o.hiddenFooters > 0 && g.css("overflow") == "hidden" && (g.css("overflow", "visible"), f(), g.css("overflow", "hidden"));
                            g = j.innerHeight - (o.spaceAbove - j.css.paddingTop) -
                                (o.spaceBelow - j.css.paddingBottom);
                            if (!i.is(":visible") || o.height != g)Ua(i, g, !0), o.height = g;
                            l.initialized && (t(d, n.onsizecontent_end || n.onsizecontent), aa(d))
                        }
                    })
                }, ja = function (a) {
                    if (!a || a == "all")a = k.borderPanes;
                    f.each(a.split(","), function (a, c) {
                        var d = m[c], e = l[c], h = q[c], g = w[c], i = B[c], j;
                        if (h && g) {
                            var o = k[c].dir, p = e.isClosed ? "_closed" : "_open", r = d["spacing" + p], t = d["togglerAlign" + p], p = d["togglerLength" + p], v;
                            if (r == 0)g.hide(); else {
                                !e.noRoom && !e.isHidden && g.show();
                                o == "horz" ? (v = h.outerWidth(), e.resizerLength = v,
                                    g.css({width:y(1, K(g, v)), height:y(0, L(g, r)), left:Z(h, "left")})) : (v = h.outerHeight(), e.resizerLength = v, g.css({height:y(1, L(g, v)), width:y(0, K(g, r)), top:u.insetTop + P("north", !0)}));
                                R(d, g);
                                if (i) {
                                    if (p == 0 || e.isSliding && d.hideTogglerOnSlide) {
                                        i.hide();
                                        return
                                    } else i.show();
                                    if (!(p > 0) || p == "100%" || p > v)p = v, t = 0; else if (I(t))switch (t) {
                                        case "top":
                                        case "left":
                                            t = 0;
                                            break;
                                        case "bottom":
                                        case "right":
                                            t = v - p;
                                            break;
                                        default:
                                            t = Math.floor((v - p) / 2)
                                    } else h = parseInt(t, 10), t = t >= 0 ? h : v - p + h;
                                    if (o == "horz") {
                                        var x = K(i, p);
                                        i.css({width:y(0,
                                            x), height:y(1, L(i, r)), left:t, top:0});
                                        i.children(".content").each(function () {
                                            j = f(this);
                                            j.css("marginLeft", Math.floor((x - j.outerWidth()) / 2))
                                        })
                                    } else {
                                        var z = L(i, p);
                                        i.css({height:y(0, z), width:y(1, K(i, r)), top:t, left:0});
                                        i.children(".content").each(function () {
                                            j = f(this);
                                            j.css("marginTop", Math.floor((z - j.outerHeight()) / 2))
                                        })
                                    }
                                    R(0, i)
                                }
                                if (!l.initialized && (d.initHidden || e.noRoom))g.hide(), i && i.hide()
                            }
                        }
                    })
                }, Ra = function (a) {
                    var b = B[a], c = m[a];
                    if (b)c.closable = !0, b.bind("click." + v,function (b) {
                        b.stopPropagation();
                        ca(a)
                    }).bind("mouseenter." +
                        v, ta).bind("mouseleave." + v, R).css("visibility", "visible").css("cursor", "pointer").attr("title", l[a].isClosed ? c.togglerTip_closed : c.togglerTip_open).show()
                }, G = f(this).eq(0);
            if (!G.length)return null;
            if (G.data("layoutContainer") && G.data("layout"))return G.data("layout");
            var q = {}, N = {}, w = {}, B = {}, u = l.container, v = l.id, da = {options:m, state:l, container:G, panes:q, contents:N, resizers:w, togglers:B, toggle:ca, hide:la, show:ha, open:M, close:J, slideOpen:Sa, slideClose:Aa, slideToggle:function (a) {
                ca(a, !0)
            }, initContent:Qa,
                sizeContent:fa, sizePane:za, swapPanes:function (a, b) {
                    function c(a) {
                        var b = q[a], c = N[a];
                        return!b ? !1 : {pane:a, P:b ? b[0] : !1, C:c ? c[0] : !1, state:f.extend({}, l[a]), options:f.extend({}, m[a])}
                    }

                    function d(a, b) {
                        if (a) {
                            var c = a.P, d = a.C, e = a.pane, h = k[b], j = h.side.toLowerCase(), o = "inset" + h.side, p = f.extend({}, l[b]), r = m[b], t = {resizerCursor:r.resizerCursor};
                            f.each("fxName,fxSpeed,fxSettings".split(","), function (a, b) {
                                t[b] = r[b];
                                t[b + "_open"] = r[b + "_open"];
                                t[b + "_close"] = r[b + "_close"]
                            });
                            q[b] = f(c).data("layoutEdge", b).css(k.hidden).css(h.cssReq);
                            N[b] = d ? f(d) : !1;
                            m[b] = f.extend({}, a.options, t);
                            l[b] = f.extend({}, a.state);
                            c.className = c.className.replace(RegExp(r.paneClass + "-" + e, "g"), r.paneClass + "-" + b);
                            va(b);
                            h.dir != k[e].dir ? (c = g[b] || 0, Q(b), c = y(c, l[b].minSize), za(b, c, !0)) : w[b].css(j, u[o] + (l[b].isVisible ? P(b) : 0));
                            a.state.isVisible && !p.isVisible ? xa(b, !0) : (ya(b), ba(b, !0));
                            a = null
                        }
                    }

                    l[a].edge = b;
                    l[b].edge = a;
                    var e = !1;
                    !1 === t(a, m[a].onswap_start) && (e = !0);
                    !e && !1 === t(b, m[b].onswap_start) && (e = !0);
                    if (e)l[a].edge = a, l[b].edge = b; else {
                        var e = c(a), h = c(b), g = {};
                        g[a] = e ?
                            e.state.size : 0;
                        g[b] = h ? h.state.size : 0;
                        q[a] = !1;
                        q[b] = !1;
                        l[a] = {};
                        l[b] = {};
                        B[a] && B[a].remove();
                        B[b] && B[b].remove();
                        w[a] && w[a].remove();
                        w[b] && w[b].remove();
                        w[a] = w[b] = B[a] = B[b] = !1;
                        d(e, b);
                        d(h, a);
                        e = h = g = null;
                        q[a] && q[a].css(k.visible);
                        q[b] && q[b].css(k.visible);
                        ea();
                        t(a, m[a].onswap_end || m[a].onswap);
                        t(b, m[b].onswap_end || m[b].onswap)
                    }
                }, resizeAll:ea, destroy:function () {
                    f(window).unbind("." + v);
                    f(document).unbind("." + v);
                    f.each(k.allPanes.split(","), function (a, c) {
                        wa(c, !1, !0)
                    });
                    var a = G.removeData("layout").removeData("layoutContainer").removeClass(m.containerClass);
                    !a.data("layoutEdge") && a.data("layoutCSS") && a.css(a.data("layoutCSS")).removeData("layoutCSS");
                    u.tagName == "BODY" && (a = f("html")).data("layoutCSS") && a.css(a.data("layoutCSS")).removeData("layoutCSS");
                    Ma()
                }, addPane:Pa, removePane:wa, setSizeLimits:Q, bindButton:C, addToggleBtn:H, addOpenBtn:x, addCloseBtn:z, addPinBtn:Ba, allowOverflow:p, resetOverflow:r, encodeJSON:Da, decodeJSON:Ca, getState:qa, getCookie:X, saveCookie:pa, deleteCookie:function () {
                    pa("", {expires:-1})
                }, loadCookie:Ea, loadState:Fa, cssWidth:K, cssHeight:L,
                enableClosable:Ra, disableClosable:function (a, b) {
                    var c = B[a];
                    if (c)m[a].closable = !1, l[a].isClosed && M(a, !1, !0), c.unbind("." + v).css("visibility", b ? "hidden" : "visible").css("cursor", "default").attr("title", "")
                }, enableSlidable:function (a) {
                    var b = w[a];
                    if (b && b.data("draggable"))m[a].slidable = !0, s.isClosed && ba(a, !0)
                }, disableSlidable:function (a) {
                    var b = w[a];
                    if (b)m[a].slidable = !1, l[a].isSliding ? J(a, !1, !0) : (ba(a, !1), b.css("cursor", "default").attr("title", ""), R(null, b[0]))
                }, enableResizable:function (a) {
                    var b = w[a],
                        c = m[a];
                    if (b && b.data("draggable"))c.resizable = !0, b.draggable("enable").bind("mouseenter." + v, Ka).bind("mouseleave." + v, ua), l[a].isClosed || b.css("cursor", c.resizerCursor).attr("title", c.resizerTip)
                }, disableResizable:function (a) {
                    var b = w[a];
                    if (b && b.data("draggable"))m[a].resizable = !1, b.draggable("disable").unbind("." + v).css("cursor", "default").attr("title", ""), R(null, b[0])
                }};
            (function () {
                Wa();
                var a = m;
                if (!1 === t(null, a.onload_start))return!1;
                if (!Oa("center").length)return alert(D.errCenterPaneMissing), null;
                a.useStateCookie && a.cookie.autoLoad && Ea();
                l.browser = {mozilla:f.browser.mozilla, webkit:f.browser.webkit || f.browser.safari, msie:f.browser.msie, isIE6:f.browser.msie && f.browser.version == 6, boxModel:f.support.boxModel};
                var b = G, c = u.tagName = b.attr("tagName"), d = c == "BODY", e = {};
                u.selector = b.selector.split(".slice")[0];
                u.ref = c + "/" + u.selector;
                b.data("layout", da).data("layoutContainer", v).addClass(m.containerClass);
                b.data("layoutCSS") || (d ? (e = f.extend(na(b, "position,margin,padding,border"), {height:b.css("height"),
                    overflow:b.css("overflow"), overflowX:b.css("overflowX"), overflowY:b.css("overflowY")}), c = f("html"), c.data("layoutCSS", {height:"auto", overflow:c.css("overflow"), overflowX:c.css("overflowX"), overflowY:c.css("overflowY")})) : e = na(b, "position,margin,padding,border,top,bottom,left,right,width,height,overflow,overflowX,overflowY"), b.data("layoutCSS", e));
                try {
                    if (d)f("html").css({height:"100%", overflow:"hidden", overflowX:"hidden", overflowY:"hidden"}), f("body").css({position:"relative", height:"100%", overflow:"hidden",
                        overflowX:"hidden", overflowY:"hidden", margin:0, padding:0, border:"none"}); else {
                        var e = {overflow:"hidden"}, h = b.css("position");
                        b.css("height");
                        if (!b.data("layoutRole") && (!h || !h.match(/fixed|absolute|relative/)))e.position = "relative";
                        b.css(e);
                        b.is(":visible") && b.innerHeight() < 2 && alert(D.errContainerHeight.replace(/CONTAINER/, u.ref))
                    }
                } catch (g) {
                }
                f.extend(l.container, $(b));
                Xa();
                fa();
                if (a.scrollToBookmarkOnLoad)b = self.location, b.hash && b.replace(b.hash);
                Na();
                a.autoBindCustomButtons && Za();
                a.resizeWithWindow &&
                    !G.data("layoutRole") && f(window).bind("resize." + v, Va);
                f(window).bind("unload." + v, Ma);
                l.initialized = !0;
                t(null, a.onload_end || a.onload)
            })();
            return da
        }
    })(jQuery);

});
