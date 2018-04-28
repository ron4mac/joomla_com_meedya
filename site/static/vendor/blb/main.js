!function(e) {
    if ("object" == typeof exports && "undefined" != typeof module)
        module.exports = e();
    else if ("function" == typeof define && define.amd)
        define([], e);
    else {
        ("undefined" != typeof window ? window : "undefined" != typeof global ? global : "undefined" != typeof self ? self : this).basicLightbox = e()
    }
}(function() {
    return function e(t, o, n) {
        function r(f, a) {
            if (!o[f]) {
                if (!t[f]) {
                    var c = "function" == typeof require && require;
                    if (!a && c)
                        return c(f, !0);
                    if (i)
                        return i(f, !0);
                    var l = new Error("Cannot find module '" + f + "'");
                    throw l.code = "MODULE_NOT_FOUND",
                    l
                }
                var u = o[f] = {
                    exports: {}
                };
                t[f][0].call(u.exports, function(e) {
                    var o = t[f][1][e];
                    return r(o || e)
                }, u, u.exports, e, t, o, n)
            }
            return o[f].exports
        }
        for (var i = "function" == typeof require && require, f = 0; f < n.length; f++)
            r(n[f]);
        return r
    }({
        1: [function(e, t, o) {
            "use strict";
            Object.defineProperty(o, "__esModule", {
                value: !0
            });
            var n = function(e, t) {
                var o = e.children;
                return 1 === o.length && o[0].tagName === t
            }
              , r = o.visible = function(e) {
                return null != (e = e || document.querySelector(".basicLightbox")) && !0 === e.ownerDocument.body.contains(e)
            }
            ;
            o.create = function(e, t) {
                var o = function() {
                    var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : ""
                      , t = arguments[1]
                      , o = document.createElement("div");
                    o.classList.add("basicLightbox"),
                    null != t.className && o.classList.add(t.className),
                    o.innerHTML = "\n\t\t" + t.beforePlaceholder + '\n\t\t<div class="basicLightbox__placeholder" role="dialog">\n\t\t\t' + e + "\n\t\t</div>\n\t\t" + t.afterPlaceholder + "\n\t";
                    var r = o.querySelector(".basicLightbox__placeholder")
                      , i = n(r, "IMG")
                      , f = n(r, "VIDEO")
                      , a = n(r, "IFRAME");
                    return !0 === i && o.classList.add("basicLightbox--img"),
                    !0 === f && o.classList.add("basicLightbox--video"),
                    !0 === a && o.classList.add("basicLightbox--iframe"),
                    o
                }(e, t = function() {
                    var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {};
                    return !1 !== (e = Object.assign({}, e)).closable && (e.closable = !0),
                    "function" == typeof e.className && (e.className = e.className()),
                    "string" != typeof e.className && (e.className = null),
                    "function" != typeof e.beforeShow && (e.beforeShow = function() {}
                    ),
                    "function" != typeof e.afterShow && (e.afterShow = function() {}
                    ),
                    "function" != typeof e.beforeClose && (e.beforeClose = function() {}
                    ),
                    "function" != typeof e.afterClose && (e.afterClose = function() {}
                    ),
                    "function" == typeof e.beforePlaceholder && (e.beforePlaceholder = e.beforePlaceholder()),
                    "string" != typeof e.beforePlaceholder && (e.beforePlaceholder = ""),
                    "function" == typeof e.afterPlaceholder && (e.afterPlaceholder = e.afterPlaceholder()),
                    "string" != typeof e.afterPlaceholder && (e.afterPlaceholder = ""),
                    e
                }(t))
                  , i = function(e) {
                    return !1 !== t.beforeClose(f) && function(e, t) {
                        return e.classList.remove("basicLightbox--visible"),
                        setTimeout(function() {
                            requestAnimationFrame(function() {
                                return !1 === r(e) ? t() : (e.parentElement.removeChild(e),
                                t())
                            })
                        }, 410),
                        !0
                    }(o, function() {
                        if (t.afterClose(f),
                        "function" == typeof e)
                            return e(f)
                    })
                };
                !0 === t.closable && (o.onclick = function(e) {
                    e.target === this && (i(),
                    function(e) {
                        "function" == typeof e.stopPropagation && e.stopPropagation(),
                        "function" == typeof e.preventDefault && e.preventDefault()
                    }(e))
                }
                );
                !0 === t.closable && (o.onkeydown = function(e) {
                	console.log(e);
                    e.keycode === 27 && (i(),
                    function(e) {
                        "function" == typeof e.stopPropagation && e.stopPropagation(),
                        "function" == typeof e.preventDefault && e.preventDefault()
                    }(e))
                }
                );
                var f = {
                    element: function() {
                        return o
                    },
                    visible: function() {
                        return r(o)
                    },
                    show: function(e) {
                        return !1 !== t.beforeShow(f) && function(e, t) {
                            return document.body.appendChild(e),
                            setTimeout(function() {
                                requestAnimationFrame(function() {
                                    return e.classList.add("basicLightbox--visible"),
                                    t()
                                })
                            }, 10),
                            !0
                        }(o, function() {
                            if (t.afterShow(f),
                            "function" == typeof e)
                                return e(f)
                        })
                    },
                    close: i
                };
                return f
            }
        }
        , {}]
    }, {}, [1])(1)
});
