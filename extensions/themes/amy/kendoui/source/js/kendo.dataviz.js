/*
* Kendo UI v2011.3.1129 (http://kendoui.com)
* Copyright 2011 Telerik AD. All rights reserved.
*
* Kendo UI commercial licenses may be obtained at http://kendoui.com/license.
* If you do not own a commercial license, this file shall be governed by the
* GNU General Public License (GPL) version 3. For GPL requirements, please
* review: http://www.gnu.org/copyleft/gpl.html
*/

;(function($, undefined) {
    /**
     * @name kendo
     * @namespace This object contains all code introduced by the Kendo project, plus helper functions that are used across all widgets.
     */
    var kendo = window.kendo = window.kendo || {},
        extend = $.extend,
        each = $.each,
        proxy = $.proxy,
        noop = $.noop,
        isFunction = $.isFunction,
        math = Math,
        Template,
        JSON = JSON || {},
        support = {},
        boxShadowRegExp = /(\d+?)px\s*(\d+?)px\s*(\d+?)px\s*(\d+?)?/i,
        FUNCTION = "function",
        STRING = "string",
        NUMBER = "number",
        OBJECT = "object",
        NULL = "null",
        BOOLEAN = "boolean",
        globalize = window.Globalize;

    function Class() {}

    Class.extend = function(proto) {
        var base = function() {},
            member,
            that = this,
            subclass = proto && proto.init ? proto.init : function () {
                that.apply(this, arguments);
            },
            fn;

        base.prototype = that.prototype;
        fn = subclass.fn = subclass.prototype = extend(new base, proto);

        for (member in fn) {
            if (typeof fn[member] === OBJECT) {
                fn[member] = extend(true, {}, base.prototype[member], proto[member]);
            }
        }

        fn.constructor = subclass;
        subclass.extend = that.extend;

        return subclass;
    };

    var Observable = Class.extend(/** @lends kendo.Observable.prototype */{
        /**
         * Creates an observable instance.
         * @constructs
         * @class Represents a class that can trigger events, along with methods that subscribe handlers to these events.
         */
        init: function() {
            this._events = {};
        },

        bind: function(eventName, handlers, one) {
            var that = this,
                idx,
                eventNames = $.isArray(eventName) ? eventName : [eventName],
                length,
                handler,
                original,
                events;

            for (idx = 0, length = eventNames.length; idx < length; idx++) {
                eventName = eventNames[idx];

                handler = isFunction(handlers) ? handlers : handlers[eventName];

                if (handler) {
                    if (one) {
                        original = handler;
                        handler = function() {
                            that.unbind(eventName, handler);
                            original.call(that, arguments);
                        }
                    }
                    events = that._events[eventName] || [];
                    events.push(handler);
                    that._events[eventName] = events;
                }
            }

            return that;
        },

        one: function(eventName, handlers) {
            return this.bind(eventName, handlers, true);
        },

        trigger: function(eventName, parameter) {
            var that = this,
                events = that._events[eventName],
                isDefaultPrevented = false,
                args = extend(parameter, {
                    preventDefault: function() {
                        isDefaultPrevented = true;
                    },
                    isDefaultPrevented: function() {
                        return isDefaultPrevented;
                    }
                }),
                idx,
                length;

            if (events) {
                for (idx = 0, length = events.length; idx < length; idx++) {
                    events[idx].call(that, args);
                }
            }

            return isDefaultPrevented;
        },

        unbind: function(eventName, handler) {
            var that = this,
                events = that._events[eventName],
                idx,
                length;

            if (events) {
                if (handler) {
                    for (idx = 0, length = events.length; idx < length; idx++) {
                        if (events[idx] === handler) {
                            events.splice(idx, 1);
                        }
                    }
                } else {
                    that._events[eventName] = [];
                }
            }

            return that;
        }
    });

    /**
     * @name kendo.Template.Description
     *
     * @section Templates offer way of creating html chunks.
     *  Options such as html encoding and compilation for optimal performance are available.
     *
     * @exampleTitle Basic template
     * @example
     *
     *  var inlineTemplate = kendo.template("Hello, #= firstName # #= lastName #");
     *  var inlineData = { firstName: "John", lastName: "Doe" };
     *  $("#inline").html(inlineTemplate(inlineData));
     *
     * @exampleTitle Output:
     * @example
     *  Hello, John Doe!
     *
     * @exampleTitle Encoding HTML
     * @example
     *
     * var encodingTemplate = kendo.template("HTML tags are encoded like this - ${ html }");
     * var encodingData = { html: "<strong>lorem ipsum</strong>" };
     * $("#encoding").html(encodingTemplate(encodingData));
     *
     * @exampleTitle Output:
     * @example
     *  HTML tags are encoded like this - <strong>lorem ipsum</strong>
     */

     function compilePart(part, stringPart) {
         if (stringPart) {
             return "'" +
                 part.split("'").join("\\'")
                 .replace(/\n/g, "\\n")
                 .replace(/\r/g, "\\r")
                 .replace(/\t/g, "\\t")
                 + "'";
         } else {
             if (part.charAt(0) === "=") {
                 return "+(" + part.substring(1) + ")+";
             } else {
                 return ";" + part + ";o+=";
             }
         }
     }

    /**
     * @name kendo.Template
     * @namespace
     */
    Template = /** @lends kendo.Template */ {
        paramName: "data", // name of the parameter of the generated template
        useWithBlock: true, // whether to wrap the template in a with() block
        /**
         * Renders a template for each item of the data.
         * @ignore
         * @name kendo.Template.render
         * @static
         * @function
         * @param {String} [template] The template that will be rendered
         * @param {Array} [data] Data items
         * @returns {String} The rendered template
         */
        render: function(template, data) {
            var idx,
                length,
                html = "";

            for (idx = 0, length = data.length; idx < length; idx++) {
                html += template(data[idx]);
            }

            return html;
        },
        /**
         * Compiles a template to a function that builds HTML. Useful when a template will be used several times.
         * @ignore
         * @name kendo.Template.compile
         * @static
         * @function
         * @param {String} [template] The template that will be compiled
         * @param {Object} [options] Compilation options
         * @returns {Function} The compiled template
         */
        compile: function(template, options) {
            var settings = extend({}, this, options),
                paramName = settings.paramName,
                useWithBlock = settings.useWithBlock,
                functionBody = "var o,e=kendo.htmlEncode;",
                encodeRegExp = /\${([^}]*)}/g,
                parts,
                part,
                idx;

            if (isFunction(template)) {
                if (template.length === 2) {
                    //looks like jQuery.template
                    return function(d) {
                        return template($, { data: d }).join("");
                    }
                }
                return template;
            }

            functionBody += useWithBlock ? "with(" + paramName + "){" : "";

            functionBody += "o=";

            parts = template
                .replace(/\\}/g, "__CURLY__")
                .replace(encodeRegExp, "#=e($1)#")
                .replace(/__CURLY__/g, "}")
                .replace(/\\#/g, "__SHARP__")
                .split("#");

            for (idx = 0; idx < parts.length; idx ++) {
                functionBody += compilePart(parts[idx], idx % 2 === 0);
            }

            functionBody += useWithBlock ? ";}" : ";";

            functionBody += "return o;";

            functionBody = functionBody.replace(/__SHARP__/g, "#");

            try {
                return new Function(paramName, functionBody);
            } catch(e) {
                throw new Error(kendo.format("Invalid template:'{0}' Generated code:'{1}'", template, functionBody));
            }
        }
    };

    //JSON stringify
(function() {
    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {
            "\b": "\\b",
            "\t": "\\t",
            "\n": "\\n",
            "\f": "\\f",
            "\r": "\\r",
            "\"" : '\\"',
            "\\": "\\\\"
        },
        rep,
        formatters,
        toString = {}.toString,
        hasOwnProperty = {}.hasOwnProperty;

    if (typeof Date.prototype.toJSON !== FUNCTION) {

        /** @ignore */
        Date.prototype.toJSON = function (key) {
            var that = this;

            return isFinite(that.valueOf()) ?
                that.getUTCFullYear()     + "-" +
                pad(that.getUTCMonth() + 1) + "-" +
                pad(that.getUTCDate())      + "T" +
                pad(that.getUTCHours())     + ":" +
                pad(that.getUTCMinutes())   + ":" +
                pad(that.getUTCSeconds())   + "Z" : null;
        };

        String.prototype.toJSON = Number.prototype.toJSON = /** @ignore */ Boolean.prototype.toJSON = function (key) {
            return this.valueOf();
        };
    }

    function quote(string) {
        escapable.lastIndex = 0;
        return escapable.test(string) ? "\"" + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === STRING ? c :
                "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4);
        }) + "\"" : "\"" + string + "\"";
    }

    function str(key, holder) {
        var i,
            k,
            v,
            length,
            mind = gap,
            partial,
            value = holder[key],
            type;

        if (value && typeof value === OBJECT && typeof value.toJSON === FUNCTION) {
            value = value.toJSON(key);
        }

        if (typeof rep === FUNCTION) {
            value = rep.call(holder, key, value);
        }

        type = typeof value;
        if (type === STRING) {
            return quote(value);
        } else if (type === NUMBER) {
            return isFinite(value) ? String(value) : NULL;
        } else if (type === BOOLEAN || type === NULL) {
            return String(value);
        } else if (type === OBJECT) {
            if (!value) {
                return NULL;
            }
            gap += indent;
            partial = [];
            if (toString.apply(value) === "[object Array]") {
                length = value.length;
                for (i = 0; i < length; i++) {
                    partial[i] = str(i, value) || NULL;
                }
                v = partial.length === 0 ? "[]" : gap ?
                    "[\n" + gap + partial.join(",\n" + gap) + "\n" + mind + "]" :
                    "[" + partial.join(",") + "]";
                gap = mind;
                return v;
            }
            if (rep && typeof rep === OBJECT) {
                length = rep.length;
                for (i = 0; i < length; i++) {
                    if (typeof rep[i] === STRING) {
                        k = rep[i];
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ": " : ":") + v);
                        }
                    }
                }
            } else {
                for (k in value) {
                    if (hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ": " : ":") + v);
                        }
                    }
                }
            }

            v = partial.length === 0 ? "{}" : gap ?
                "{\n" + gap + partial.join(",\n" + gap) + "\n" + mind + "}" :
                "{" + partial.join(",") + "}";
            gap = mind;
            return v;
        }
    }

    if (typeof JSON.stringify !== FUNCTION) {
        JSON.stringify = function (value, replacer, space) {
            var i;
            gap = "";
            indent = "";

            if (typeof space === NUMBER) {
                for (i = 0; i < space; i += 1) {
                    indent += " ";
                }

            } else if (typeof space === STRING) {
                indent = space;
            }

            rep = replacer;
            if (replacer && typeof replacer !== FUNCTION && (typeof replacer !== OBJECT || typeof replacer.length !== NUMBER)) {
                throw new Error("JSON.stringify");
            }

            return str("", {"": value});
        };
    }
})();

// Date and Number formatting
(function() {
    var formatRegExp = /{(\d+)(:[^\}]+)?}/g,
        dateFormatRegExp = /dddd|ddd|dd|d|MMMM|MMM|MM|M|yyyy|yy|HH|H|hh|h|mm|m|fff|ff|f|tt|ss|s|"[^"]*"|'[^']*'/g,
        standardFormatRegExp =  /^(n|c|p|e)(\d*)$/i,
        EMPTY = "",
        POINT = ".",
        COMMA = ",",
        SHARP = "#",
        ZERO = "0",
        EN = "en-US";

    //cultures
    kendo.cultures = {"en-US" : {
        name: EN,
        numberFormat: {
            pattern: ["-n"],
            decimals: 2,
            ",": ",",
            ".": ".",
            groupSize: [3],
            percent: {
                pattern: ["-n %", "n %"],
                decimals: 2,
                ",": ",",
                ".": ".",
                groupSize: [3],
                symbol: "%"
            },
            currency: {
                pattern: ["($n)", "$n"],
                decimals: 2,
                ",": ",",
                ".": ".",
                groupSize: [3],
                symbol: "$"
            }
        },
        calendars: {
            standard: {
                days: {
                    names: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                    namesAbbr: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                    namesShort: [ "Su", "Mo", "Tu", "We", "Th", "Fr", "Sa" ]
                },
                months: {
                    names: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                    namesAbbr: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                },
                AM: [ "AM", "am", "AM" ],
                PM: [ "PM", "pm", "PM" ],
                patterns: {
                    d: "M/d/yyyy",
                    D: "dddd, MMMM dd, yyyy",
                    F: "dddd, MMMM dd, yyyy h:mm:ss tt",
                    g: "M/d/yyyy h:mm tt",
                    G: "M/d/yyyy h:mm:ss tt",
                    m: "MMMM dd",
                    M: "MMMM dd",
                    s: "yyyy'-'MM'-'ddTHH':'mm':'ss",
                    t: "h:mm tt",
                    T: "h:mm:ss tt",
                    u: "yyyy'-'MM'-'dd HH':'mm':'ss'Z'",
                    y: "MMMM, yyyy",
                    Y: "MMMM, yyyy"
                },
                "/": "/",
                ":": ":",
                firstDay: 0
            }
        }
    }};

    /**
     * @name kendo.Globalization
     * @namespace
     */
     /**
     * @name kendo.Globalization.Description
     *
     * @section Globalization is the process of designing and developing an
     * application that works in multiple cultures. The culture defines specific information
     * for the number formats, week and month names, date and time formats and etc.
     *
     * @section Kendo exposes <strong><em>culture(cultureName)</em></strong> method which allows to select the culture
     * script coresponding to the "culture name". kendo.culture() method uses the passed culture name
     * to select culture from the culture scripts that you have included and then sets the current culture.
     * If there is no such culture, the default one is used.
     *
     * <h3>Define current culture settings</h3>
     *
     * @exampleTitle Include culture scripts and select culture
     * @example
     *
     * <script src="jquery.js" />
     * <script src="kendo.all.min.js" />
     * <script src="kendo.culture.en-GB.js" />
     * <script type="text/javascript">
     *    //set current culture to the "en-GB" culture script.
     *    kendo.culture("en-GB");
     * </script>
     *
     * @exampleTitle Get current culture
     * @example
     * var cultureInfo = kendo.culture();
     *
     * @section
     * <p> Widgets that depend on current culture are:
     *    <ul>
     *        <li> Calendar </li>
     *        <li> DatePicker </li>
     *        <li> TimePicker </li>
     *        <li> NumericTextBox </li>
     *    </ul>
     * </p>
     */
    kendo.culture = function(cultureName) {
        if (cultureName !== undefined) {
            var cultures = kendo.cultures,
                culture = cultures[cultureName] || cultures[EN];

            culture.calendar = culture.calendars.standard;
            cultures.current = culture;
        } else {
            return kendo.cultures.current;
        }
    };

    //set current culture to en-US.
    kendo.culture(EN);

    function pad(number) {
        return number < 10 ? "0" + number : number;
    }

    function formatDate(date, format) {
        var calendar = kendo.cultures.current.calendar,
            days = calendar.days,
            months = calendar.months;

        format = calendar.patterns[format] || format;

        return format.replace(dateFormatRegExp, function (match) {
            var result;

            if (match === "d") {
                result = date.getDate();
            } else if (match === "dd") {
                result = pad(date.getDate());
            } else if (match === "ddd") {
                result = days.namesAbbr[date.getDay()];
            } else if (match === "dddd") {
                result = days.names[date.getDay()];
            } else if (match === "M") {
                result = date.getMonth() + 1;
            } else if (match === "MM") {
                result = pad(date.getMonth() + 1);
            } else if (match === "MMM") {
                result = months.namesAbbr[date.getMonth()];
            } else if (match === "MMMM") {
                result = months.names[date.getMonth()];
            } else if (match === "yy") {
                result = pad(date.getFullYear() % 100);
            } else if (match === "yyyy") {
                result = date.getFullYear();
            } else if (match === "h" ) {
                result = date.getHours() % 12 || 12
            } else if (match === "hh") {
                result = pad(date.getHours() % 12 || 12);
            } else if (match === "H") {
                result = date.getHours();
            } else if (match === "HH") {
                result = pad(date.getHours());
            } else if (match === "m") {
                result = date.getMinutes();
            } else if (match === "mm") {
                result = pad(date.getMinutes());
            } else if (match === "s") {
                result = date.getSeconds();
            } else if (match === "ss") {
                result = pad(date.getSeconds());
            } else if (match === "f") {
                result = math.floor(date.getMilliseconds() / 100);
            } else if (match === "ff") {
                result = math.floor(date.getMilliseconds() / 10);
            } else if (match === "fff") {
                result = date.getMilliseconds();
            } else if (match === "tt") {
                result = date.getHours() < 12 ? calendar.AM[0] : calendar.PM[0]
            }

            return result !== undefined ? result : match.slice(1, match.length - 1);
        });
    }

    //number formatting
    function formatNumber(number, format) {
        var culture = kendo.cultures.current,
            numberFormat = culture.numberFormat,
            groupSize = numberFormat.groupSize[0],
            groupSeparator = numberFormat[COMMA],
            decimal = numberFormat[POINT],
            precision = numberFormat.decimals,
            pattern = numberFormat.pattern[0],
            symbol,
            isCurrency, isPercent,
            customPrecision,
            formatAndPrecision,
            negative = number < 0,
            integer,
            fraction,
            integerLength,
            fractionLength,
            replacement = EMPTY,
            value = EMPTY,
            idx,
            length,
            ch,
            decimalIndex,
            sharpIndex,
            zeroIndex,
            start = -1,
            end;

        //return empty string if no number
        if (number === undefined) {
            return EMPTY;
        }

        if (!isFinite(number)) {
            return number;
        }

        //if no format then return number.toString() or number.toLocaleString() if culture.name is not defined
        if (!format) {
            return culture.name.length ? number.toLocaleString() : number.toString();
        }

        formatAndPrecision = standardFormatRegExp.exec(format);

        /* standard formatting */
        if (formatAndPrecision) {
            format = formatAndPrecision[1].toLowerCase();

            isCurrency = format === "c";
            isPercent = format === "p";

            if (isCurrency || isPercent) {
                //get specific number format information if format is currency or percent
                numberFormat = isCurrency ? numberFormat.currency : numberFormat.percent;
                groupSize = numberFormat.groupSize[0];
                groupSeparator = numberFormat[COMMA];
                decimal = numberFormat[POINT];
                precision = numberFormat.decimals;
                symbol = numberFormat.symbol;
                pattern = numberFormat.pattern[negative ? 0 : 1];
            }

            customPrecision = formatAndPrecision[2];

            if (customPrecision) {
                precision = +customPrecision;
            }

            //return number in exponential format
            if (format === "e") {
                return customPrecision ? number.toExponential(precision) : number.toExponential(); // toExponential() and toExponential(undefined) differ in FF #653438.
            }

            // multiply if format is percent
            if (isPercent) {
                number *= 100;
            }

            number = number.toFixed(precision);
            number = number.split(POINT);

            integer = number[0];
            fraction = number[1];

            //exclude "-" if number is negative.
            if (negative) {
                integer = integer.substring(1);
            }

            value = integer;
            integerLength = integer.length;

            //add group separator to the number if it is longer enough
            if (integerLength >= groupSize) {
                value = EMPTY;
                for (idx = 0; idx < integerLength; idx++) {
                    if (idx > 0 && (integerLength - idx) % groupSize === 0) {
                        value += groupSeparator;
                    }
                    value += integer.charAt(idx);
                }
            }

            if (fraction) {
                value += decimal + fraction;
            }

            if (format === "n" && !negative) {
                return value;
            }

            number = EMPTY;

            for (idx = 0, length = pattern.length; idx < length; idx++) {
                ch = pattern.charAt(idx);

                if (ch === "n") {
                    number += value;
                } else if (ch === "$" || ch === "%") {
                    number += symbol;
                } else {
                    number += ch;
                }
            }

            return number;
        }

        //custom formatting
        //
        //separate format by sections.
        format = format.split(";");
        if (negative && format[1]) {
            //make number positive and get negative format
            number = -number;
            format = format[1];
        } else if (number === 0) {
            //format for zeros
            format = format[2] || format[0];
            if (format.indexOf(SHARP) == -1 && format.indexOf(ZERO) == -1) {
                //return format if it is string constant.
                return format;
            }
        } else {
            format = format[0];
        }

        isCurrency = format.indexOf("$") != -1;
        isPercent = format.indexOf("%") != -1;

        //multiply number if the format has percent
        if (isPercent) {
            number *= 100;
        }

        if (isCurrency || isPercent) {
            //get specific number format information if format is currency or percent
            numberFormat = isCurrency ? numberFormat.currency : numberFormat.percent;
            groupSize = numberFormat.groupSize[0];
            groupSeparator = numberFormat[COMMA];
            decimal = numberFormat[POINT];
            precision = numberFormat.decimals;
            symbol = numberFormat.symbol;
        }

        decimalIndex = format.indexOf(POINT);
        length = format.length;

        if (decimalIndex != -1) {
            sharpIndex = format.lastIndexOf(SHARP);
            zeroIndex = format.lastIndexOf(ZERO);

            if (zeroIndex != -1) {
                value = number.toFixed(zeroIndex - decimalIndex);
                number = number.toString();
                number = number.length > value.length && sharpIndex > zeroIndex ? number : value;
            }
        } else {
            number = number.toFixed(0);
        }

        sharpIndex = format.indexOf(SHARP);
        zeroIndex = format.indexOf(ZERO);

        //define the index of the first digit placeholder
        if (sharpIndex == -1 && zeroIndex != -1) {
            start = zeroIndex;
        } else if (sharpIndex != -1 && zeroIndex == -1) {
            start = sharpIndex;
        } else {
            start = sharpIndex > zeroIndex ? zeroIndex : sharpIndex;
        }

        sharpIndex = format.lastIndexOf(SHARP);
        zeroIndex = format.lastIndexOf(ZERO);

        //define the index of the last digit placeholder
        if (sharpIndex == -1 && zeroIndex != -1) {
            end = zeroIndex;
        } else if (sharpIndex != -1 && zeroIndex == -1) {
            end = sharpIndex;
        } else {
            end = sharpIndex > zeroIndex ? sharpIndex : zeroIndex;
        }

        if (start == length) {
            end = start;
        }

        if (start != -1) {
            value = number.toString().split(POINT);
            integer = value[0];
            fraction = value[1] || EMPTY;

            integerLength = integer.length;
            fractionLength = fraction.length;

            //add group separator to the number if it is longer enough
            if (integerLength >= groupSize && format.indexOf(COMMA) != -1) {
                value = EMPTY;
                for (idx = 0; idx < integerLength; idx++) {
                    if (idx > 0 && (integerLength - idx) % groupSize === 0) {
                        value += groupSeparator;
                    }
                    value += integer.charAt(idx);
                }
                integer = value;
            }

            number = format.substring(0, start);

            for (idx = start; idx < length; idx++) {
                ch = format.charAt(idx);

                if (decimalIndex == -1) {
                    if (end - idx < integerLength) {
                        number += integer;
                        break;
                    }
                } else {
                    if (zeroIndex != -1 && zeroIndex < idx) {
                        replacement = EMPTY;
                    }

                    if ((decimalIndex - idx) <= integerLength && decimalIndex - idx > -1) {
                        number += integer;
                        idx = decimalIndex;
                    }

                    if (decimalIndex === idx) {
                        number += (fraction ? decimal : EMPTY) + fraction;
                        idx += end - decimalIndex + 1;
                        continue;
                    }
                }

                if (ch === ZERO) {
                    number += ch;
                    replacement = ch;
                } else if (ch === SHARP) {
                    number += replacement;
                } else if (ch === COMMA) {
                    continue;
                }
            }

            if (end >= start) {
                number += format.substring(end + 1);
            }

            //replace symbol placeholders
            if (isCurrency || isPercent) {
                value = EMPTY;
                for (idx = 0, length = number.length; idx < length; idx++) {
                    ch = number.charAt(idx);
                    value += (ch === "$" || ch === "%") ? symbol : ch;
                }
                number = value;
            }
        }

        return number;
    }

    function toString(value, fmt) {
        if (fmt) {
            if (value instanceof Date) {
                return formatDate(value, fmt);
            } else if (typeof value === NUMBER) {
                return formatNumber(value, fmt);
            }
        }

        return value !== undefined ? value : "";
    }

    if (globalize) {
        toString = proxy(globalize.format, globalize);
    }

    kendo.format = function(fmt) {
        var values = arguments;

        return fmt.replace(formatRegExp, function(match, index, placeholderFormat) {
            var value = values[parseInt(index) + 1];

            return toString(value, placeholderFormat ? placeholderFormat.substring(1) : "");
        });
    };

    kendo.toString = toString;
    })();


(function() {

    var nonBreakingSpaceRegExp = /\u00A0/g,
        formatsSequence = ["G", "g", "d", "F", "D", "y", "m", "T", "t"];

    function outOfRange(value, start, end) {
        return !(value >= start && value <= end);
    }

    function parseExact(value, format, culture) {
        if (!value) {
            return null;
        }

        var lookAhead = function (match) {
                var i = 0;
                while (format[idx] === match) {
                    i++;
                    idx++;
                }
                if (i > 0) {
                    idx -= 1;
                }
                return i;
            },
            getNumber = function(size) {
                var rg = new RegExp('^\\d{1,' + size + '}'),
                    match = value.substr(valueIdx, size).match(rg);

                if (match) {
                    match = match[0];
                    valueIdx += match.length;
                    return parseInt(match, 10);
                }
                return null;
            },
            getIndexByName = function (names) {
                var i = 0,
                    length = names.length,
                    name, nameLength;

                for (; i < length; i++) {
                    name = names[i];
                    nameLength = name.length;

                    if (value.substr(valueIdx, nameLength) == name) {
                        valueIdx += nameLength;
                        return i + 1;
                    }
                }
                return null;
            },
            checkLiteral = function() {
                if (value.charAt(valueIdx) == format[idx]) {
                    valueIdx++;
                }
            },
            calendar = culture.calendar,
            year = null,
            month = null,
            day = null,
            hours = null,
            minutes = null,
            seconds = null,
            milliseconds = null,
            idx = 0,
            valueIdx = 0,
            literal = false,
            date = new Date(),
            defaultYear = date.getFullYear(),
            shortYearCutOff = 30,
            ch, count, AM, PM, pmHour, length, pattern;

        if (!format) {
            format = "d"; //shord date format
        }

        //if format is part of the patterns get real format
        pattern = calendar.patterns[format];
        if (pattern) {
            format = pattern;
        }

        format = format.split("");
        length = format.length;

        for (; idx < length; idx++) {
            ch = format[idx];

            if (literal) {
                if (ch === "'") {
                    literal = false;
                } else {
                    checkLiteral();
                }
            } else {
                if (ch === "d") {
                    count = lookAhead("d");
                    day = count < 3 ? getNumber(2) : getIndexByName(calendar.days[count == 3 ? "namesAbbr" : "names"]);

                    if (day === null || outOfRange(day, 1, 31)) {
                        return null;
                    }
                } else if (ch === "M") {
                    count = lookAhead("M");
                    month = count < 3 ? getNumber(2) : getIndexByName(calendar.months[count == 3 ? 'namesAbbr' : 'names']);

                    if (month === null || outOfRange(month, 1, 12)) {
                        return null;
                    }
                    month -= 1; //because month is zero based
                } else if (ch === "y") {
                    count = lookAhead("y");
                    year = getNumber(count < 3 ? 2 : 4);
                    if (year === null) {
                        year = defaultYear;
                    }
                    if (year < shortYearCutOff) {
                        year = (defaultYear - defaultYear % 100) + year;
                    }
                } else if (ch === "h" ) {
                    lookAhead("h");
                    hours = getNumber(2);
                    if (hours == 12) {
                        hours = 0;
                    }
                    if (hours === null || outOfRange(hours, 0, 11)) {
                        return null;
                    }
                } else if (ch === "H") {
                    lookAhead("H");
                    hours = getNumber(2);
                    if (hours === null || outOfRange(hours, 0, 23)) {
                        return null;
                    }
                } else if (ch === "m") {
                    lookAhead("m");
                    minutes = getNumber(2);
                    if (minutes === null || outOfRange(minutes, 0, 59)) {
                        return null;
                    }
                } else if (ch === "s") {
                    lookAhead("s");
                    seconds = getNumber(2);
                    if (seconds === null || outOfRange(seconds, 0, 59)) {
                        return null;
                    }
                } else if (ch === "f") {
                    count = lookAhead("f");
                    milliseconds = getNumber(count);
                    if (milliseconds === null || outOfRange(milliseconds, 0, 999)) {
                        return null;
                    }
                } else if (ch === "t") {
                    count = lookAhead("t");
                    pmHour = getIndexByName(calendar.PM);
                } else if (ch === "'") {
                    checkLiteral();
                    literal = true;
                } else {
                    checkLiteral();
                }
            }
        }

        if (pmHour && hours < 12) {
            hours += 12;
        }

        if (day === null) {
            day = 1;
        }

        return new Date(year, month, day, hours, minutes, seconds, milliseconds);
    }

    kendo.parseDate = function(value, formats, culture) {
        if (value instanceof Date) {
            return value;
        }

        var idx = 0,
            date = null,
            length, property, patterns;

        if (!culture) {
            culture = kendo.culture();
        } else if (typeof culture === STRING) {
            kendo.culture(culture);
            culture = kendo.culture();
        }

        if (!formats) {
            formats = [];
            patterns = culture.calendar.patterns;
            length = formatsSequence.length;

            for (; idx < length; idx++) {
                formats[idx] = patterns[formatsSequence[idx]];
            }
            formats[idx] = "ddd MMM dd yyyy HH:mm:ss";

            idx = 0;
        }

        formats = $.isArray(formats) ? formats: [formats];
        length = formats.length;

        for (; idx < length; idx++) {
            date = parseExact(value, formats[idx], culture);
            if (date) {
                return date;
            }
        }

        return date;
    }

    kendo.parseInt = function(value, culture) {
        var result = kendo.parseFloat(value, culture);
        if (result) {
            result = result | 0;
        }
        return result;
    }

    kendo.parseFloat = function(value, culture) {
        if (!value && value !== 0) {
           return null;
        }

        if (typeof value === NUMBER) {
           return value;
        }

        value = value.toString();
        culture = kendo.cultures[culture] || kendo.cultures.current;

        var number = culture.numberFormat,
            percent = number.percent,
            currency = number.currency,
            symbol = currency.symbol,
            percentSymbol = percent.symbol,
            negative = value.indexOf("-") > -1,
            parts;

        if (value.indexOf(symbol) > -1) {
            number = currency;
            parts = number.pattern[0].replace("$", symbol).split("n");
            if (value.indexOf(parts[0]) > -1 && value.indexOf(parts[1]) > -1) {
                value = value.replace(parts[0], "").replace(parts[1], "");
                negative = true;
            }
        } else if (value.indexOf(percentSymbol) > -1) {
            number = percent;
            symbol = percentSymbol;
        }

        value = value.replace("-", "")
                     .replace(symbol, "")
                     .split(number[","].replace(nonBreakingSpaceRegExp, " ")).join("")
                     .replace(number["."], ".");

        value = parseFloat(value);

        if (isNaN(value)) {
            value = null;
        } else if (negative) {
            value *= -1;
        }

        return value;
    }

    if (globalize) {
        kendo.parseDate = proxy(globalize.parseDate, globalize);
        kendo.parseFloat = proxy(globalize.parseFloat, globalize);
    }
})();

    function wrap(element) {
        var browser = $.browser;

        if (!element.parent().hasClass("k-animation-container")) {
            var shadow = element.css(kendo.support.transitions.css + "box-shadow") || element.css("box-shadow"),
                radius = shadow ? shadow.match(boxShadowRegExp) || [ 0, 0, 0, 0, 0 ] : [ 0, 0, 0, 0, 0 ],
                blur = math.max((+radius[3]), +(radius[4] || 0)),
                right = (+radius[1]) + blur,
                bottom = (+radius[2]) + blur;

            if (browser.opera) { // Box shadow can't be retrieved in Opera
                right = bottom = 5;
            }

            element.wrap(
                         $("<div/>")
                         .addClass("k-animation-container")
                         .css({
                             width: element.outerWidth(),
                             height: element.outerHeight(),
                             paddingRight: right,
                             paddingBottom: bottom
                         }));
        } else {
            var wrap = element.parent(".k-animation-container");

            if (wrap.is(":hidden")) {
                wrap.show();
            }

            wrap.css({
                    width: element.outerWidth(),
                    height: element.outerHeight()
                });
        }

        if (browser.msie && math.floor(browser.version) <= 7) {
            element.css({
                zoom: 1
            });
        }

        return element.parent();
    }

    /**
     * Contains results from feature detection.
     * @name kendo.support
     * @namespace Contains results from feature detection.
     */
    (function() {
        /**
         * Indicates the width of the browser scrollbar. A value of zero means that the browser does not show a visual representation of a scrollbar (i.e. mobile browsers).
         * @name kendo.support.scrollbar
         * @property {Boolean}
         */
        support.scrollbar = function() {
            var div = document.createElement("div"),
                result;

            div.style.cssText = "overflow:scroll;overflow-x:hidden;zoom:1";
            div.innerHTML = "&nbsp;";
            document.body.appendChild(div);

            result = div.offsetWidth - div.scrollWidth;

            document.body.removeChild(div);
            return result;
        };

        var table = document.createElement("table");

        // Internet Explorer does not support setting the innerHTML of TBODY and TABLE elements
        try {
            table.innerHTML = "<tr><td></td></tr>";

            /**
             * Indicates whether the browser supports setting of the &lt;tbody&gt; innerHtml.
             * @name kendo.support.tbodyInnerHtml
             * @property {Boolean}
             */
            support.tbodyInnerHtml = true;
        } catch (e) {
            support.tbodyInnerHtml = false;
        }

        /**
         * Indicates whether the browser supports touch events.
         * @name kendo.support.touch
         * @property {Boolean}
         */
        support.touch = "ontouchstart" in window;
        support.pointers = navigator.msPointerEnabled;

        /**
         * Indicates whether the browser supports CSS transitions.
         * @name kendo.support.transitions
         * @property {Boolean}
         */
        var transitions = support.transitions = false;

        /**
         * Indicates whether the browser supports hardware 3d transitions.
         * @name kendo.support.hasHW3D
         * @property {Boolean}
         */
        support.hasHW3D = "WebKitCSSMatrix" in window && "m11" in new WebKitCSSMatrix();
        support.hasNativeScrolling = typeof document.documentElement.style.webkitOverflowScrolling == "string";

        each([ "Moz", "webkit", "O", "ms" ], function () {
            var prefix = this.toString();

            if (typeof table.style[prefix + "Transition"] === STRING) {
                var lowPrefix = prefix.toLowerCase();

                transitions = {
                    css: "-" + lowPrefix + "-",
                    prefix: prefix,
                    event: (lowPrefix === "o" || lowPrefix === "webkit") ? lowPrefix : ""
                };

                transitions.event = transitions.event ? transitions.event + "TransitionEnd" : "transitionend";

                return false;
            }
        });

        support.transitions = transitions;

        function detectOS(ua) {
            var os = false, match = [],
                agentRxs = {
                    android: /(Android)\s+(\d+)\.(\d+(\.\d+)?)/,
                    iphone: /(iPhone|iPod).*OS\s+(\d+)[\._]([\d\._]+)/,
                    ipad: /(iPad).*OS\s+(\d+)[\._]([\d_]+)/,
                    meego: /(MeeGo).+NokiaBrowser\/(\d+)\.([\d\._]+)/,
                    webos: /(webOS)\/(\d+)\.(\d+(\.\d+)?)/,
                    blackberry: /(BlackBerry|PlayBook).*?Version\/(\d+)\.(\d+(\.\d+)?)/
                };
            for (var agent in agentRxs) {
                if (agentRxs.hasOwnProperty(agent)) {
                    match = ua.match(agentRxs[agent]);
                    if (match) {
                        os = {};
                        os.device = agent;
                        os.name = /^i(phone|pad|pod)$/i.test(agent) ? "ios" : agent;
                        os[os.name] = true;
                        os.majorVersion = match[2];
                        os.minorVersion = match[3].replace("_", ".");
                        os.flatVersion = os.majorVersion + os.minorVersion.replace(".", "");
                        os.flatVersion = os.flatVersion + (new Array(4 - os.flatVersion.length).join("0")); // Pad with zeroes
                        os.appMode = window.navigator.standalone || typeof window._nativeReady !== "undefined";

                        break;
                    }
                }
            }
            return os;
        }

        /**
         * Parses the mobile OS type and version from the browser user agent.
         * @name kendo.support.mobileOS
         */
        support.mobileOS = detectOS(navigator.userAgent);

        support.zoomLevel = function() {
            return support.touch ? (document.documentElement.clientWidth / window.innerWidth) : 1;
        };

        /**
         * Indicates the browser device pixel ratio.
         * @name kendo.support.devicePixelRatio
         * @property {Float}
         */
        support.devicePixelRatio = window.devicePixelRatio === undefined ? 1 : window.devicePixelRatio;
    })();

    /**
     * Exposed by jQuery.
     * @ignore
     * @name jQuery.fn
     * @namespace Handy jQuery plug-ins that are used by all Kendo widgets.
     */

    function size(obj) {
        var size = 0, key;
        for (key in obj) {
            obj.hasOwnProperty(key) && size++;
        }

        return size;
    }

    function getOffset(element, type) {
        if (!type) {
            type = "offset";
        }

        var result = element[type](),
            mobileOS = support.mobileOS;

        if (support.touch && mobileOS.ios && mobileOS.flatVersion < 410) { // Extra processing only in broken iOS'
            var offset = type == "offset" ? result : element.offset(),
                positioned = (result.left == offset.left && result.top == offset.top);

            if (positioned) {
                return {
                    top: result.top - window.scrollY,
                    left: result.left - window.scrollX
                };
            }
        }

        return result;
    }

    var directions = {
        left: { reverse: "right" },
        right: { reverse: "left" },
        down: { reverse: "up" },
        up: { reverse: "down" },
        "in": { reverse: "out" },
        out: { reverse: "in" }
    };

    function parseEffects(input) {
        var effects = {};

        each((typeof input === "string" ? input.split(" ") : input), function(idx) {
            effects[idx] = this;
        });

        return effects;
    }

    var fx = {
        promise: function (element, options) {
            if (options.show) {
                element.css({ display: element.data("olddisplay") || "block" }).css("display");
            }

            if (options.hide) {
                element.data("olddisplay", element.css("display")).hide();
            }

            if (options.completeCallback) {
                options.completeCallback(element); // call the external complete callback with the element
            }

            element.dequeue();
        },

        transitionPromise: function(element, destination, options) {
            var container = kendo.wrap(element);
            container.append(destination);

            element.hide();
            destination.show();

            if (options.completeCallback) {
                options.completeCallback(element); // call the external complete callback with the element
            }

            return element;
        }
    };

    function prepareAnimationOptions(options, duration, reverse, complete) {
        if (typeof options === STRING) {
            // options is the list of effect names separated by space e.g. animate(element, "fadeIn slideDown")

            // only callback is provided e.g. animate(element, options, function() {});
            if (isFunction(duration)) {
                complete = duration;
                duration = 400;
                reverse = false;
            }

            if (isFunction(reverse)) {
                complete = reverse;
                reverse = false;
            }

            if (typeof duration === BOOLEAN){
                reverse = duration;
                duration = 400;
            }

            options = {
                effects: options,
                duration: duration,
                reverse: reverse,
                complete: complete
            };
        }

        return extend({
            //default options
            effects: {},
            duration: 400, //jQuery default duration
            reverse: false,
            init: noop,
            teardown: noop,
            hide: false,
            show: false
        }, options, { completeCallback: options.complete, complete: noop }); // Move external complete callback, so deferred.resolve can be always executed.

    }

    function animate(element, options, duration, reverse, complete) {
        element.each(function (idx, el) { // fire separate queues on every element to separate the callback elements
            el = $(el);
            el.queue(function () {
                fx.promise(el, prepareAnimationOptions(options, duration, reverse, complete));
            });
        });

        return element;
    }

    function animateTo(element, destination, options, duration, reverse, complete) {
        return fx.transitionPromise(element, destination, prepareAnimationOptions(options, duration, reverse, complete));
    }

    extend($.fn, /** @lends jQuery.fn */{
        kendoStop: function(clearQueue, gotoEnd) {
            return this.stop(clearQueue, gotoEnd);
        },

        kendoAnimate: function(options, duration, reverse, complete) {
            return animate(this, options, duration, reverse, complete);
        },

        kendoAnimateTo: function(destination, options, duration, reverse, complete) {
            return animateTo(this, destination, options, duration, reverse, complete);
        }
    });

    function toggleClass(element, classes, options, add) {
        if (classes) {
            classes = classes.split(" ");

            each(classes, function(idx, value) {
                element.toggleClass(value, add);
            });
        }

        return element;
    }

    extend($.fn, /** @lends jQuery.fn */{
        kendoAddClass: function(classes, options){
            return toggleClass(this, classes, options, true);
        },
        kendoRemoveClass: function(classes, options){
            return toggleClass(this, classes, options, false);
        },
        kendoToggleClass: function(classes, options, toggle){
            return toggleClass(this, classes, options, toggle);
        }
    });

    var ampRegExp = /&/g,
        ltRegExp = /</g,
        gtRegExp = />/g;
    /**
     * Encodes HTML characters to entities.
     * @name kendo.htmlEncode
     * @function
     * @param {String} value The string that needs to be HTML encoded.
     * @returns {String} The encoded string.
     */
    function htmlEncode(value) {
        return ("" + value).replace(ampRegExp, "&amp;").replace(ltRegExp, "&lt;").replace(gtRegExp, "&gt;");
    }

    var touchLocation = function(e) {
        return {
            idx: 0,
            x: e.pageX,
            y: e.pageY
        };
    };

    var eventTarget = function (e) {
        return e.target;
    };

    if (support.touch) {
        /** @ignore */
        touchLocation = function(e, id) {
            var changedTouches = e.changedTouches || e.originalEvent.changedTouches;

            if (id) {
                var output = null;
                each(changedTouches, function(idx, value) {
                    if (id == value.identifier) {
                        output = {
                            idx: value.identifier,
                            x: value.pageX,
                            y: value.pageY
                        };
                    }
                });
                return output;
            } else {
                return {
                    idx: changedTouches[0].identifier,
                    x: changedTouches[0].pageX,
                    y: changedTouches[0].pageY
                };
            }
        };

        eventTarget = function(e) {
            var touches = "originalEvent" in e ? e.originalEvent.changedTouches : "changedTouches" in e ? e.changedTouches : null;

            return touches ? document.elementFromPoint(touches[0].clientX, touches[0].clientY) : null;
        };

        each(["swipe", "swipeLeft", "swipeRight", "swipeUp", "swipeDown", "doubleTap", "tap"], function(m, value) {
            $.fn[value] = function(callback) {
                return this.bind(value, callback)
            }
        });
    }

    if (support.touch) {
        support.mousedown = "touchstart";
        support.mouseup = "touchend";
        support.mousemove = "touchmove";
    } else {
        support.mousemove = "mousemove";
        support.mousedown = "mousedown";
        support.mouseup = "mouseup";
    }

    var wrapExpression = function(members) {
        var result = "d",
            index,
            idx,
            length,
            member,
            count = 1;

        for (idx = 0, length = members.length; idx < length; idx++) {
            member = members[idx];
            if (member !== "") {
                index = member.indexOf("[");

                if (index != 0) {
                    if (index == -1) {
                        member = "." + member;
                    } else {
                        count++;
                        member = "." + member.substring(0, index) + " || {})" + member.substring(index);
                    }
                }

                count++;
                result += member + ((idx < length - 1) ? " || {})" : ")");
            }
        }
        return new Array(count).join("(") + result;
    },
    localUrlRe = /^([a-z]+:)?\/\//i;

    extend(kendo, /** @lends kendo */ {
        /**
         * @name kendo.ui
         * @namespace Contains all classes for the Kendo UI widgets.
         */
        ui: {
            /**
             * Shows an overlay with a loading message, indicating that an action is in progress.
             * @name kendo.ui.progress
             * @function
             * @param {jQueryObject} container The container that will hold the overlay
             * @param {Boolean} toggle Whether the overlay should be shown or hidden
             */
            progress: function(container, toggle) {
                var mask = container.find(".k-loading-mask");

                if (toggle) {
                    if (!mask.length) {
                        mask = $("<div class='k-loading-mask'><span class='k-loading-text'>Loading...</span><div class='k-loading-image'/><div class='k-loading-color'/></div>")
                            .width("100%").height("100%")
                            .prependTo(container);
                    }
                } else if (mask) {
                    mask.remove();
                }
            }
        },
        fx: fx,
        data: {},
        keys: {
            BACKSPACE: 8,
            TAB: 9,
            ENTER: 13,
            ESC: 27,
            LEFT: 37,
            UP: 38,
            RIGHT: 39,
            DOWN: 40,
            END: 35,
            HOME: 36,
            SPACEBAR: 32,
            PAGEUP: 33,
            PAGEDOWN: 34,
            F12: 123
        },
        support: support,
        animate: animate,
        ns: "",
        attr: function(value) {
            return "data-" + kendo.ns + value;
        },
        wrap: wrap,
        size: size,
        getOffset: getOffset,
        parseEffects: parseEffects,
        toggleClass: toggleClass,
        directions: directions,
        Observable: Observable,
        Class: Class,
        Template: Template,
        /**
         * Shorthand for {@link kendo.Template.compile}.
         * @name kendo.template
         * @function
         */
        template: proxy(Template.compile, Template),
        /**
         * Shorthand for {@link kendo.Template.render}.
         * @name kendo.render
         * @function
         */
        render: proxy(Template.render, Template),
        stringify: proxy(JSON.stringify, JSON),
        touchLocation: touchLocation,
        eventTarget: eventTarget,
        htmlEncode: htmlEncode,
        isLocalUrl: function(url) {
            return url && !localUrlRe.test(url);
        },
        /** @ignore */
        expr: function(expression, safe) {
            expression = expression || "";

            if (expression && expression.charAt(0) !== "[") {
                expression = "." + expression;
            }

            if (safe) {
                expression =  wrapExpression(expression.split("."));
            } else {
                expression = "d" + expression;
            }

            return expression;
        },
        /** @ignore */
        getter: function(expression, safe) {
            return new Function("d", "return " + kendo.expr(expression, safe));
        },
        /** @ignore */
        setter: function(expression) {
            return new Function("d,value", "d." + expression + "=value");
        },
        /** @ignore */
        accessor: function(expression) {
            return {
                get: kendo.getter(expression),
                set: kendo.setter(expression)
            };
        },
        /** @ignore */
        guid: function() {
            var id = "", i, random;

            for (i = 0; i < 32; i++) {
                random = math.random() * 16 | 0;

                if (i == 8 || i == 12 || i == 16 || i == 20) {
                    id += "-";
                }
                id += (i == 12 ? 4 : (i == 16 ? (random & 3 | 8) : random)).toString(16);
            }

            return id;
        }
    });

    var Widget = Observable.extend( /** @lends kendo.ui.Widget.prototype */ {
        /**
         * Initializes widget. Sets `element` and `options` properties.
         * @constructs
         * @class Represents a UI widget. Base class for all Kendo widgets
         * @extends kendo.Observable
         */
        init: function(element, options) {
            var that = this;

            Observable.fn.init.call(that);
            that.element = $(element);
            that.options = extend(true, {}, that.options, options);
        }
    });

    extend(kendo.ui, /** @lends kendo.ui */{
        Widget: Widget,
        /**
         * Helper method for writing new widgets.
         * Exposes a jQuery plug-in that will handle the widget creation and attach its client-side object in the appropriate data-* attribute.
         * Also triggers the init event, when the widget has been created.
         * @name kendo.ui.plugin
         * @function
         * @param {kendo.ui.Widget} widget The widget function.
         * @example
         * function TextBox(element, options);
         * kendo.ui.plugin(TextBox);
         *
         * // initialize a new TextBox for each input, with the given options object.
         * $("input").kendoTextBox({ });
         * // get the TextBox object and call the value API method
         * $("input").data("kendoTextBox").value();
         */
        plugin: function(widget) {
            // expose it in the kendo.ui namespace
            var name = widget.fn.options.name;

            kendo.ui[name] = widget;

            name = "kendo" + name;
            // expose a jQuery plugin
            $.fn[name] = function(options) {
                $(this).each(function() {
                    var comp = new widget(this, options);
                    $(this).data(name, comp);
                });
                return this;
            }
        }
    });
})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        odataFilters = {
            eq: "eq",
            neq: "ne",
            gt: "gt",
            gte: "ge",
            lt: "lt",
            lte: "le",
            contains : "substringof",
            endswith: "endswith",
            startswith: "startswith"
        };

    function toOdataFilter(filter) {
        var result = [],
            logic = filter.logic || "and",
            idx,
            length,
            field,
            type,
            format,
            operator,
            value,
            filters = filter.filters;

        for (idx = 0, length = filters.length; idx < length; idx++) {
            filter = filters[idx];
            field = filter.field;
            value = filter.value;
            operator = filter.operator;

            if (filter.filters) {
                filter = toOdataFilter(filter);
            } else {
                field = field.replace(/\./g, "/"),

                filter = odataFilters[operator];

                if (filter && value !== undefined) {
                    type = $.type(value);
                    if (type === "string") {
                        format = "'{1}'";
                    } else if (type === "date") {
                        format = "datetime'{1:yyyy-MM-ddTHH:mm:ss}'";
                    } else {
                        format = "{1}";
                    }

                    if (filter.length > 3) {
                        if (filter !== "substringof") {
                            format = "{0}({2}," + format + ")";
                        } else {
                            format = "{0}(" + format + ",{2})";
                        }
                    } else {
                        format = "{2} {0} " + format;
                    }

                    filter = kendo.format(format, filter, value, field);
                }
            }

            result.push(filter);
        }

        filter = result.join(" " + logic + " ");

        if (result.length > 1) {
            filter = "(" + filter + ")";
        }

        return filter;
    }

    $.extend(true, kendo.data, {
        schemas: {
            odata: {
                type: "json",
                data: "d.results",
                total: "d.__count"
            }
        },
        transports: {
            odata: {
                read: {
                    cache: true, // to prevent jQuery from adding cache buster
                    dataType: "jsonp",
                    jsonpCallback: "callback", //required by OData
                    jsonp: false // to prevent jQuery from adding the jsonpCallback in the query string - we will add it ourselves
                },
                parameterMap: function(options) {
                    var result = ["$format=json", "$inlinecount=allpages", "$callback=callback"],
                        data = options || {};

                    if (data.skip) {
                        result.push("$skip=" + data.skip);
                    }

                    if (data.take) {
                        result.push("$top=" + data.take);
                    }

                    if (data.sort) {
                        result.push("$orderby=" + $.map(data.sort, function(value) {
                            var order = value.field.replace(/\./g, "/");

                            if (value.dir === "desc") {
                                order += " desc";
                            }

                            return order;
                        }).join(","));
                    }

                    if (data.filter) {
                        result.push("$filter=" + toOdataFilter(data.filter));
                    }

                    return result.join("&");
                }
            }
        }
    });
})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        isArray = $.isArray,
        isPlainObject = $.isPlainObject,
        map = $.map,
        each = $.each,
        getter = kendo.getter,
        Class = kendo.Class;

    var XmlDataReader = Class.extend({ init: function(options) {
            var that = this,
                total = options.total,
                model = options.model,
                data = options.data;

            if (model) {
                if (isPlainObject(model)) {
                    model.id = that.getter(model.id);
                    if (model.fields) {
                        each(model.fields, function(field, value) {
                            if (isPlainObject(value) && value.field) {
                                value = value.field;
                            }
                            model.fields[field] = that.getter(value);
                        });
                    }
                    model = kendo.data.Model.define(model);
                }

                that.model = model;
            }

            if (total) {
                total = that.getter(total);
                that.total = function(data) {
                    return parseInt(total(data));
                };
            }

            if (data) {
                data = that.xpathToMember(data);
                that.data = function(value) {
                    var record, field, result = that.evaluate(value, data);

                    result = isArray(result) ? result : [result];

                    if (that.model && model.fields) {
                        return map(result, function(value) {
                            record = {};
                            for (field in model.fields) {
                                record[field] = model.fields[field](value);
                            }
                            return record;
                        });
                    }

                    return result;
                };
            }
        },
        total: function(result) {
            return this.data(result).length;
        },
        parseDOM: function(element) {
            var result = {},
                parsedNode,
                node,
                nodeType,
                nodeName,
                member,
                attribute,
                attributes = element.attributes,
                attributeCount = attributes.length,
                idx;

            for (idx = 0; idx < attributeCount; idx++) {
                attribute = attributes[idx];
                result["@" + attribute.nodeName] = attribute.nodeValue;
            }

            for (node = element.firstChild; node; node = node.nextSibling) {
                nodeType = node.nodeType;

                if (nodeType === 3 || nodeType === 4) {
                    // text nodes or CDATA are stored as #text field
                    result["#text"] = node.nodeValue;
                } else if (nodeType === 1) {
                    // elements are stored as fields
                    parsedNode = this.parseDOM(node);

                    nodeName = node.nodeName;

                    member = result[nodeName];

                    if (isArray(member)) {
                        // elements of same nodeName are stored as array
                        member.push(parsedNode);
                    } else if (member !== undefined) {
                        member = [member, parsedNode];
                    } else {
                        member = parsedNode;
                    }

                    result[nodeName] = member;
                }
            }
            return result;
        },

        evaluate: function(value, expression) {
            var members = expression.split("."),
                member,
                result,
                length,
                intermediateResult,
                idx;

            while (member = members.shift()) {
                value = value[member];

                if (isArray(value)) {
                    result = [];
                    expression = members.join(".");

                    for (idx = 0, length = value.length; idx < length; idx++) {
                        intermediateResult = this.evaluate(value[idx], expression);

                        intermediateResult = isArray(intermediateResult) ? intermediateResult : [intermediateResult];

                        result.push.apply(result, intermediateResult);
                    }

                    return result;
                }
            }

            return value;
        },

        parse: function(xml) {
            var documentElement,
                tree,
                result = {};

            documentElement = xml.documentElement || $.parseXML(xml).documentElement;

            tree = this.parseDOM(documentElement);

            result[documentElement.nodeName] = tree;

            return result;
        },

        xpathToMember: function(member) {
            if (!member) {
                return "";
            }

            member = member.replace(/^\//, "") // remove the first "/"
                           .replace(/\//g, "."); // replace all "/" with "."

            if (member.indexOf("@") >= 0) {
                // replace @attribute with '["@attribute"]'
                return member.replace(/\.?(@.*)/, '["$1"]');
            }

            if (member.indexOf("text()") >= 0) {
                // replace ".text()" with '["#text"]'
                return member.replace(/(\.?text\(\))/, '["#text"]');
            }

            return member;
        },
        getter: function(member) {
            return getter(this.xpathToMember(member), true);
        }
    });

    $.extend(true, kendo.data, {
        XmlDataReader: XmlDataReader,
        readers: {
            xml: XmlDataReader
        }
    });
})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        extend = $.extend,
        type = $.type,
        getter = kendo.getter,
        setter = kendo.setter,
        accessor = kendo.accessor,
        each = $.each,
        isPlainObject = $.isPlainObject,
        CHANGE = "change",
        ERROR = "error",
        MODELCHANGE = "modelChange",
        Observable = kendo.Observable,
        dateRegExp = /^\/Date\((.*?)\)\/$/;

    function equal(x, y) {
        if (x === y) {
            return true;
        }

        var xtype = type(x), ytype = type(y), field;

        if (xtype !== ytype) {
            return false;
        }

        if (xtype === "date") {
            return x.getTime() === y.getTime();
        }

        if (xtype !== "object" && xtype !== "array") {
            return false;
        }

        for (field in x) {
            if (!equal(x[field], y[field])) {
                return false;
            }
        }

        return true;
    }

    var parsers = {
        "number": function(value) {
            return kendo.parseFloat(value);
        },

        "date": function(value) {
            if (typeof value === "string") {
                var date = dateRegExp.exec(value);
                if (date) {
                    return new Date(parseInt(date[1]));
                }
            }
            return kendo.parseDate(value);
        },

        "boolean": function(value) {
            if (typeof value === "string") {
                return value.toLowerCase() === "true";
            }
            return !!value;
        },

        "string": function(value) {
            return value + "";
        },

        "default": function(value) {
            return value;
        }
    };

    var defaultValues = {
        "string": "",
        "number": 0,
        "date": new Date(),
        "boolean": false,
        "default": ""
    }

    var Model = Observable.extend({
        init: function(data) {
            var that = this;

            Observable.fn.init.call(that);

            that._accessors = {};

            that._modified = false;

            that.data = data && !$.isEmptyObject(data) ? data : extend(true, {}, that.defaultItem);
            that.pristine = extend(true, {}, that.data);

            if (that.id() === undefined || that.id() === that.defaultId) {
                that._isNew = true;
                that.data["__id"] = kendo.guid();
            }
        },

        _accessor: function(field) {
            var accessors = this._accessors;

            return accessors[field] = accessors[field] || accessor(field);
        },

        get: function(field) {
            return this._accessor(field).get(this.data);
        },

        _parse: function(field, value) {
            var that = this,
                parse;

            field = (that.fields || {})[field];
            if (field) {
                parse = field.parse;
                if (!parse && field.type) {
                    parse = parsers[field.type.toLowerCase()];
                }
            }

            return parse ? parse(value) : value;
        },

        editable: function(field) {
            field = (this.fields || {})[field];
            return field ? field.editable !== false : true;
        },

        set: function(fields, value) {
            var that = this,
                field,
                values = {},
                modified = false,
                accessor;

            if (typeof fields === "string") {
                values[fields] = value;
            } else {
                values = fields;
            }

            for (field in values) {
                if(!that.editable(field)) {
                    continue;
                }

                accessor = that._accessor(field);

                value = that._parse(field, values[field]);

                if (!equal(value, accessor.get(that.data))) {
                    accessor.set(that.data, value);
                    that._modified = modified = true;
                }
            }

            if (modified) {
                that.trigger(CHANGE);
            }
        },

        reset: function() {
            var that = this;

            extend(that.data, that.pristine);
            that._modified = false;
        },

        _accept: function(data) {
            var that = this;

            that._isNew = false;
            that._modified = false;

            extend(that.data, data);

            that.pristine = extend(true, {}, that.data);
        },

        hasChanges: function() {
            return this._modified;
        },

        isNew: function() {
            return this._isNew === true;
        },

        changes: function() {
            var modified = null,
                field,
                that = this,
                data = that.data,
                pristine = that.pristine;

            for (field in data) {
                if (field !== "__id" && (that.isNew() || !equal(pristine[field], data[field]))) {
                    modified = modified || {};
                    modified[field] = data[field];
                }
            }

            return modified;
        }
    });

    Model.define = function(options) {
        var model,
            proto = extend({}, { defaultItem: {} }, options),
            id = proto.id || "id",
            defaultId,
            set,
            get;

        if ($.isFunction(id)) {
            get = id;
            set = id;
        } else {
            get = getter(id);
            set = setter(id);
        }

        for (var name in proto.fields) {
            var field = proto.fields[name],
                type = field.type || "default",
                value = null;

            name = field.field || name;

            if (!field.nullable) {
                value = proto.defaultItem[name] = field.defaultValue !== undefined ? field.defaultValue : defaultValues[type.toLowerCase()];
            }

            if (options.id === name) {
                defaultId = proto._defaultId = value;
            }

            proto.defaultItem[name] = value;

            field.parse = field.parse || parsers[type];
        }

        id = function(data, value) {
            var result;
            if (value === undefined) {
                result = get(data);
                return result !== undefined && result !== null && result !== defaultId ? result : data["__id"];
            } else {
                set(data, value);
            }
        }

        proto.id = function(value) {
            return id(this.data, value);
        }

        model = Model.extend(proto);
        model.id = id;

        if (proto.fields) {
            model.fields = proto.fields;
        }

        return model;
    }

    var ModelSet = Observable.extend({
        init: function(options) {
            var that = this;

            that.options = options = extend({}, that.options, options);
            that._reader = options.reader;
            that._data = options.data || [];
            that._destroyed = [];
            that._transport = options.transport;
            that._models = {};
            that._map();

            Observable.fn.init.call(that);

            that.bind([CHANGE, MODELCHANGE, ERROR], options);
        },

        options: {
            batch: false,
            sendAllFields: false,
            autoSync: false
        },

        indexOf: function(dataItem) {
            var that = this,
                model = that.options.model,
                id = model.id(dataItem);

            return that._idMap[id];
        },

        _map: function() {
            var that = this,
                idx,
                length,
                data = that._data,
                model = that.options.model;

            that._idMap = {};

            for (idx = 0, length = data.length; idx < length; idx++) {
                that._idMap[model.id(data[idx])] = idx;
            }
        },

        data: function(data) {
            var that = this;

            if (data) {
                that._data = data;
                that._models = {};
                that._destroyed = [];
                that._map();
            }
        },

        get: function(id) {
            var that = this,
                data,
                model = that._models[id];

            if (!model) {
                data = that._data[that._idMap[id]];

                if (data) {
                    model = that._models[id] = new that.options.model(data);
                    model.bind(CHANGE, function () {
                        that.trigger(MODELCHANGE, model);
                    });
                }
            }

            return model;
        },

        add: function(model) {
            var that = this;

            return that.insert(that._data.length, model);
        },

        insert: function(index, model) {
            var that = this, data;

            if (model === undefined && isPlainObject(index)) {
                model = index;
                index = 0;
            }

            if (!(model instanceof Model)) {
                model = new that.options.model(model);
            }

            data = model.data;

            model.bind(CHANGE, function () {
                that.trigger(MODELCHANGE, model);
            });

            that._data.splice(index, 0, data);

            that._map();

            that._models[model.id()] = model;

            that.trigger(CHANGE);

            if (that.options.autoSync) {
                that.sync();
            }
            return model;
        },

        remove: function(model) {
            var that = this, id = model, idx, length;

            if (model instanceof Model) {
                id = model.id();
            } else {
                model = that.get(id);
            }

            if (model) {
                that._data.splice(that._idMap[id], 1);
                that._map();

                model.unbind(CHANGE);

                delete that._models[id];

                if (!model.isNew()) {
                    that._destroyed.push(model);
                }

                that.trigger(CHANGE);

                if (that.options.autoSync) {
                    that.sync();
                }
            }

            return model;
        },

        sync: function() {
            var that = this,
                created = [],
                updated = [],
                destroyed = [],
                data,
                idx,
                length,
                options = that.options,
                sendAllFields = options.sendAllFields,
                model,
                models = that._models;

            for (idx in models) {
                model = models[idx];

                if (model.isNew()) {
                    created.push({
                        model: model,
                        data: model.changes()
                    });
                } else if (model.hasChanges()) {
                    data = sendAllFields ? model.data : model.changes();

                    options.model.id(data, model.id());
                    updated.push({
                        model: model,
                        data: data
                    });
                }
            }

            for (idx = 0, length = that._destroyed.length; idx < length; idx++ ) {
                model = that._destroyed[idx];

                data = sendAllFields ? model.data : {};

                options.model.id(data, model.id());

                destroyed.push({
                    model: model,
                    data: data
                });
            }

            $.when.apply(null, that._send({
                        create: created,
                        update: updated,
                        destroy: destroyed
                    })
                )
                .then(function() {
                    var idx,
                        length;

                    for (idx = 0, length = arguments.length; idx < length; idx++){
                        that._accept(arguments[idx]);
                    }

                    that.trigger(CHANGE);
                    that._map();
                });
        },

        _accept: function(result) {
            var that = this,
                models = result.models,
                response = result.response || {},
                idx = 0,
                length;

            response = that._reader.data(that._reader.parse(response));

            if (!$.isArray(response)) {
                response = [response];
            }

            if (result.type === "destroy") {
                that._destroyed = [];
            } else {
                for (idx = 0, length = models.length; idx < length; idx++) {
                    models[idx]._accept(response[idx]);
                }
            }
        },

        _promise: function(data, models, type) {
            var that = this,
                transport = that._transport;

            return $.Deferred(function(deferred) {
                transport[type].call(transport, extend({
                        success: function(response) {
                            deferred.resolve({
                                response: response,
                                models: models,
                                type: type
                            });
                        },
                        error: function(response) {
                            deferred.reject(response);
                            that.trigger(ERROR, response);
                        }
                    }, data)
                );
            }).promise();
        },

        _send: function(data) {
            var that = this,
                promises = [],
                order = "create,update,destroy".split(",");

            each(order, function(index, type) {
                var payload = data[type],
                    idx,
                    length;

                if (that.options.batch) {
                    if (payload.length) {
                        promises.push(
                            that._promise( {
                                data: {
                                    models: $.map(payload, function(value) {
                                        return value.data;
                                    })
                                }
                            }, $.map(payload, function(value) {
                                    return value.model;
                            }),  type)
                        );
                    }
                } else {
                    for (idx = 0, length = payload.length; idx < length; idx++) {
                        promises.push(that._promise( { data: payload[idx].data }, [ payload[idx].model ], type));
                    }
                }
            });

            return promises;
        },

        cancelChanges: function() {
            var that = this,
                destroyed = that._destroyed,
                models = that._models,
                model,
                data = that._data,
                idx,
                length;

            for (idx = 0, length = destroyed.length; idx < length; idx++) {
                model = destroyed[idx];
                model.reset();

                data.push(model.data);
            }

            for (idx in models) {
                model = models[idx];

                if (model.isNew()) {
                    data.splice(that._idMap[idx], 1);
                } else if (model.hasChanges()) {
                    model.reset();
                }
            }

            that.data(data);

            that.trigger(CHANGE);
        }
    });

    kendo.data.Model = Model;
    kendo.data.ModelSet = ModelSet;
})(jQuery);
(function($, undefined) {
    /**
     * @name kendo.data
     * @namespace
     */

    /**
     * @name kendo.data.DataSource.Description
     *
     * @section
     *  <p>
     *      The DataSource component is an abstraction for using local (arrays of JavaScript objects) or
     *      remote (XML, JSON, JSONP) data. It fully supports CRUD (Create, Read, Update, Destroy) data
     *      operations and provides both local and server-side support for Sorting, Paging, Filtering, Grouping, and Aggregates.
     *  </p>
     *  <p>
     *      It is a powerful piece of the Kendo UI Framework, dramatically simplifying data binding and data operations.
     *  </p>
     *  <h3>Getting Started</h3>
     *
     * @exampleTitle Creating a DataSource bound to local data
     * @example
     * var movies = [ {
     *       title: "Star Wars: A New Hope",
     *       year: 1977
     *    }, {
     *      title: "Star Wars: The Empire Strikes Back",
     *      year: 1980
     *    }, {
     *      title: "Star Wars: Return of the Jedi",
     *      year: 1983
     *    }
     * ];
     * var localDataSource = new kendo.data.DataSource({data: movies});
     * @exampleTitle Creating a DataSource bound to a remote data service (Twitter)
     * @example
     * var dataSource = new kendo.data.DataSource({
     *     transport: {
     *         read: {
     *             // the remote service url
     *             url: "http://search.twitter.com/search.json",
     *
     *             // JSONP is required for cross-domain AJAX
     *             dataType: "jsonp",
     *
     *             // additional parameters sent to the remote service
     *             data: {
     *                 q: "html5"
     *             }
     *         }
     *     },
     *     // describe the result format
     *     schema: {
     *         // the data which the data source will be bound to is in the "results" field
     *         data: "results"
     *     }
     * });
     * @section
     *  <h3>Binding UI widgets to DataSource</h3>
     *  <p>
     *      Many Kendo UI widgets support data binding, and the Kendo UI DataSource is an ideal
     *      binding source for both local and remote data. A DataSource can be created in-line
     *      with other UI widget configuration settings, or a shared DataSource can be created
     *      to enable multiple UI widgets to bind to the same, observable data collection.
     *  </p>
     * @exampleTitle Creating a local DataSource in-line with UI widget configuration
     * @example
     * $("#chart").kendoChart({
     *     title: {
     *         text: "Employee Sales"
     *     },
     *     dataSource: new kendo.data.DataSource({
     *         data: [
     *         {
     *             employee: "Joe Smith",
     *             sales: 2000
     *         },
     *         {
     *             employee: "Jane Smith",
     *             sales: 2250
     *         },
     *         {
     *             employee: "Will Roberts",
     *             sales: 1550
     *         }]
     *     }),
     *     series: [{
     *         type: "line",
     *         field: "sales",
     *         name: "Sales in Units"
     *     }],
     *     categoryAxis: {
     *         field: "employee"
     *     }
     * });
     * @exampleTitle Creating and binding to a sharable remote DataSource
     * @example
     * var sharableDataSource = new kendo.data.DataSource({
     *     transport: {
     *         read: {
     *             url: "data-service.json",
     *             dataType: "json"
     *         }
     *     }
     * });
     *
     * // Bind two UI widgets to same DataSource
     * $("#chart").kendoChart({
     *     title: {
     *         text: "Employee Sales"
     *     },
     *     dataSource: sharableDataSource,
     *     series: [{
     *         field: "sales",
     *         name: "Sales in Units"
     *     }],
     *     categoryAxis: {
     *         field: "employee"
     *     }
     * });
     *
     * $("#grid").kendoGrid({
     *     dataSource: sharableDataSource,
     *         columns: [
     *         {
     *             field: "employee",
     *             title: "Employee"
     *         },
     *         {
     *             field: "sales",
     *             title: "Sales",
     *             template: '#= kendo.toString(sales, "N0") #'
     *     }]
     * });
     */
    var extend = $.extend,
        proxy = $.proxy,
        isFunction = $.isFunction,
        isPlainObject = $.isPlainObject,
        isEmptyObject = $.isEmptyObject,
        isArray = $.isArray,
        grep = $.grep,
        ajax = $.ajax,
        map,
        each = $.each,
        noop = $.noop,
        kendo = window.kendo,
        Observable = kendo.Observable,
        Class = kendo.Class,
        Model = kendo.data.Model,
        ModelSet = kendo.data.ModelSet,
        STRING = "string",
        CREATE = "create",
        READ = "read",
        UPDATE = "update",
        DESTROY = "destroy",
        CHANGE = "change",
        MODELCHANGE = "modelChange",
        MULTIPLE = "multiple",
        SINGLE = "single",
        ERROR = "error",
        REQUESTSTART = "requestStart",
        crud = [CREATE, READ, UPDATE, DESTROY],
        identity = function(o) { return o; },
        getter = kendo.getter,
        stringify = kendo.stringify,
        math = Math;

    var Comparer = {
        selector: function(field) {
            return isFunction(field) ? field : getter(field);
        },

        asc: function(field) {
            var selector = this.selector(field);
            return function (a, b) {
                a = selector(a);
                b = selector(b);

                return a > b ? 1 : (a < b ? -1 : 0);
            };
        },

        desc: function(field) {
            var selector = this.selector(field);
            return function (a, b) {
                a = selector(a);
                b = selector(b);

                return a < b ? 1 : (a > b ? -1 : 0);
            };
        },

        create: function(descriptor) {
            return Comparer[descriptor.dir.toLowerCase()](descriptor.field);
        },

        combine: function(comparers) {
             return function(a, b) {
                 var result = comparers[0](a, b),
                     idx,
                     length;

                 for (idx = 1, length = comparers.length; idx < length; idx ++) {
                     result = result || comparers[idx](a, b);
                 }

                 return result;
             }
        }
    };

    map = function (array, callback) {
        var idx, length = array.length, result = new Array(length);

        for (idx = 0; idx < length; idx++) {
            result[idx] = callback(array[idx], idx, array);
        }

        return result;
    }

    var operators = (function(){
        var dateRegExp = /^\/Date\((.*?)\)\/$/,
            quoteRegExp = /'/g;

        function operator(op, a, b, ignore) {
            var date;

            if (b != undefined) {
                if (typeof b === STRING) {
                    b = b.replace(quoteRegExp, "\\'");
                    date = dateRegExp.exec(b);
                    if (date) {
                        b = new Date(+date[1]);
                    } else if (ignore) {
                        b = "'" + b.toLowerCase() + "'";
                        a = a + ".toLowerCase()";
                    } else {
                        b = "'" + b + "'";
                    }
                }

                if (b.getTime) {
                    //b looks like a Date
                    a += ".getTime()";
                    b = b.getTime();
                }
            }

            return a + " " + op + " " + b;
        }

        return {
            eq: function(a, b, ignore) {
                return operator("==", a, b, ignore);
            },
            neq: function(a, b, ignore) {
                return operator("!=", a, b, ignore);
            },
            gt: function(a, b, ignore) {
                return operator(">", a, b, ignore);
            },
            gte: function(a, b, ignore) {
                return operator(">=", a, b, ignore);
            },
            lt: function(a, b, ignore) {
                return operator("<", a, b, ignore);
            },
            lte: function(a, b, ignore) {
                return operator("<=", a, b, ignore);
            },
            startswith: function(a, b, ignore) {
                if (ignore) {
                    a = a + ".toLowerCase()";
                    if (b) {
                        b = b.toLowerCase();
                    }
                }
                return a + ".lastIndexOf('" + b + "', 0) == 0";
            },
            endswith: function(a, b, ignore) {
                if (ignore) {
                    a = a + ".toLowerCase()";
                    if (b) {
                        b = b.toLowerCase();
                    }
                }
                return a + ".lastIndexOf('" + b + "') == " + a + ".length - " + (b || "").length;
            },
            contains: function(a, b, ignore) {
                if (ignore) {
                    a = a + ".toLowerCase()";
                    if (b) {
                        b = b.toLowerCase();
                    }
                }
                return a + ".indexOf('" + b + "') >= 0"
            }
        };
    })();

    function Query(data) {
        this.data = data || [];
    }

    Query.normalizeFilter = normalizeFilter;

    Query.filterExpr = function(expression) {
        var expressions = [],
            logic = { and: " && ", or: " || " },
            idx,
            length,
            filter,
            expr,
            fieldFunctions = [],
            operatorFunctions = [],
            field,
            operator,
            filters = expression.filters;

        for (idx = 0, length = filters.length; idx < length; idx++) {
            filter = filters[idx];
            field = filter.field;
            operator = filter.operator;

            if (filter.filters) {
                expr = Query.filterExpr(filter);
                //Nested function fields or operators - update their index e.g. __o[0] -> __o[1]
                filter = expr.expression
                             .replace(/__o\[(\d+)\]/g, function(match, index) {
                                index = +index;
                                return "__o[" + (operatorFunctions.length + index) + "]";
                             })
                             .replace(/__f\[(\d+)\]/g, function(match, index) {
                                index = +index;
                                return "__f[" + (fieldFunctions.length + index) + "]";
                             });

                operatorFunctions.push.apply(operatorFunctions, expr.operators);
                fieldFunctions.push.apply(fieldFunctions, expr.fields);
            } else {
                if (typeof field === "function") {
                    expr = "__f[" + fieldFunctions.length +"](d)";
                    fieldFunctions.push(field);
                } else {
                    expr = kendo.expr(field);
                }

                if (typeof operator === "function") {
                    filter = "__o[" + operatorFunctions.length + "](" + expr + ", " + filter.value + ")";
                    operatorFunctions.push(operator);
                } else {
                    filter = operators[(operator || "eq").toLowerCase()](expr, filter.value, filter.ignoreCase !== undefined? filter.ignoreCase : true);
                }
            }

            expressions.push(filter);
        }

        return  { expression: "(" + expressions.join(logic[expression.logic]) + ")", fields: fieldFunctions, operators: operatorFunctions };
    }

    function normalizeSort(field, dir) {
        if (field) {
            var descriptor = typeof field === STRING ? { field: field, dir: dir } : field,
                descriptors = isArray(descriptor) ? descriptor : (descriptor !== undefined ? [descriptor] : []);

            return grep(descriptors, function(d) { return !!d.dir; });
        }
    }

    var operatorMap = {
        "==": "eq",
        equals: "eq",
        isequalto: "eq",
        equalto: "eq",
        equal: "eq",
        "!=": "neq",
        ne: "neq",
        notequals: "neq",
        isnotequalto: "neq",
        notequalto: "neq",
        notequal: "neq",
        "<": "lt",
        islessthan: "lt",
        lessthan: "lt",
        less: "lt",
        "<=": "lte",
        le: "lte",
        islessthanorequalto: "lte",
        lessthanequal: "lte",
        ">": "gt",
        isgreaterthan: "gt",
        greaterthan: "gt",
        greater: "gt",
        ">=": "gte",
        isgreaterthanorequalto: "gte",
        greaterthanequal: "gte",
        ge: "gte"
    }

    function normalizeOperator(expression) {
        var idx,
            length,
            filter,
            operator,
            filters = expression.filters;

        if (filters) {
            for (idx = 0, length = filters.length; idx < length; idx++) {
                filter = filters[idx];
                operator = filter.operator;

                if (operator && typeof operator === STRING) {
                    filter.operator = operatorMap[operator.toLowerCase()] || operator;
                }

                normalizeOperator(filter);
            }
        }
    }

    function normalizeFilter(expression) {
        if (expression && !isEmptyObject(expression)) {
            if (isArray(expression) || !expression.filters) {
                expression = {
                    logic: "and",
                    filters: isArray(expression) ? expression : [expression]
                }
            }

            normalizeOperator(expression);

            return expression;
        }
    }

    function normalizeAggregate(expressions) {
        return expressions = isArray(expressions) ? expressions : [expressions];
    }

    function normalizeGroup(field, dir) {
       var descriptor = typeof field === STRING ? { field: field, dir: dir } : field,
           descriptors = isArray(descriptor) ? descriptor : (descriptor !== undefined ? [descriptor] : []);

        return map(descriptors, function(d) { return { field: d.field, dir: d.dir || "asc", aggregates: d.aggregates }; });
    }

    Query.prototype = {
        toArray: function () {
            return this.data;
        },
        range: function(index, count) {
            return new Query(this.data.slice(index, index + count));
        },
        skip: function (count) {
            return new Query(this.data.slice(count));
        },
        take: function (count) {
            return new Query(this.data.slice(0, count));
        },
        select: function (selector) {
            return new Query(map(this.data, selector));
        },
        orderBy: function (selector) {
            var result = this.data.slice(0),
                comparer = isFunction(selector) || !selector ? Comparer.asc(selector) : selector.compare;

            return new Query(result.sort(comparer));
        },
        orderByDescending: function (selector) {
            return new Query(this.data.slice(0).sort(Comparer.desc(selector)));
        },
        sort: function(field, dir) {
            var idx,
                length,
                descriptors = normalizeSort(field, dir),
                comparers = [];

            if (descriptors.length) {
                for (idx = 0, length = descriptors.length; idx < length; idx++) {
                    comparers.push(Comparer.create(descriptors[idx]));
                }

                return this.orderBy({ compare: Comparer.combine(comparers) });
            }

            return this;
        },

        filter: function(expressions) {
            var idx,
                current,
                length,
                compiled,
                predicate,
                data = this.data,
                fields,
                operators,
                result = [],
                filter;

            expressions = normalizeFilter(expressions);

            if (!expressions || expressions.filters.length === 0) {
                return this;
            }

            compiled = Query.filterExpr(expressions);
            fields = compiled.fields;
            operators = compiled.operators;

            predicate = filter = new Function("d, __f, __o", "return " + compiled.expression);

            if (fields.length || operators.length) {
                filter = function(d) {
                    return predicate(d, fields, operators);
                };
            }

            for (idx = 0, length = data.length; idx < length; idx++) {
                current = data[idx];

                if (filter(current)) {
                    result.push(current);
                }
            }
            return new Query(result);
        },

        group: function(descriptors, allData) {
            descriptors =  normalizeGroup(descriptors || []);
            allData = allData || this.data;

            var that = this,
                result = new Query(that.data),
                descriptor;

            if (descriptors.length > 0) {
                descriptor = descriptors[0];
                result = result.groupBy(descriptor).select(function(group) {
                    var data = new Query(allData).filter([ { field: group.field, operator: "eq", value: group.value } ]);
                    return {
                        field: group.field,
                        value: group.value,
                        items: descriptors.length > 1 ? new Query(group.items).group(descriptors.slice(1), data.toArray()).toArray() : group.items,
                        hasSubgroups: descriptors.length > 1,
                        aggregates: data.aggregate(descriptor.aggregates)
                    }
                });
            }
            return result;
        },
        groupBy: function(descriptor) {
            if (isEmptyObject(descriptor) || !this.data.length) {
                return new Query([]);
            }

            var field = descriptor.field,
                sorted = this.sort(field, descriptor.dir || "asc").toArray(),
                accessor = kendo.accessor(field),
                item,
                groupValue = accessor.get(sorted[0], field),
                group = {
                    field: field,
                    value: groupValue,
                    items: []
                },
                currentValue,
                idx,
                len,
                result = [group];

            for(idx = 0, len = sorted.length; idx < len; idx++) {
                item = sorted[idx];
                currentValue = accessor.get(item, field);
                if(groupValue !== currentValue) {
                    groupValue = currentValue;
                    group = {
                        field: field,
                        value: groupValue,
                        items: []
                    };
                    result.push(group);
                }
                group.items.push(item);
            }
            return new Query(result);
        },
        aggregate: function (aggregates) {
            var idx,
                len,
                result = {};

            if (aggregates && aggregates.length) {
                for(idx = 0, len = this.data.length; idx < len; idx++) {
                   calculateAggregate(result, aggregates, this.data[idx], idx, len);
                }
            }
            return result;
        }
    }
    function calculateAggregate(accumulator, aggregates, item, index, length) {
            aggregates = aggregates || [];
            var idx,
                aggr,
                functionName,
                fieldAccumulator,
                len = aggregates.length;

            for (idx = 0; idx < len; idx++) {
                aggr = aggregates[idx];
                functionName = aggr.aggregate;
                var field = aggr.field;
                accumulator[field] = accumulator[field] || {};
                accumulator[field][functionName] = functions[functionName.toLowerCase()](accumulator[field][functionName], item, kendo.accessor(field), index, length);
            }
        }

    var functions = {
        sum: function(accumulator, item, accessor) {
            return accumulator = (accumulator || 0) + accessor.get(item);
        },
        count: function(accumulator, item, accessor) {
            return (accumulator || 0) + 1;
        },
        average: function(accumulator, item, accessor, index, length) {
            accumulator = (accumulator || 0) + accessor.get(item);
            if(index == length - 1) {
                accumulator = accumulator / length;
            }
            return accumulator;
        },
        max: function(accumulator, item, accessor) {
            var accumulator =  (accumulator || 0),
                value = accessor.get(item);
            if(accumulator < value) {
                accumulator = value;
            }
            return accumulator;
        },
        min: function(accumulator, item, accessor) {
            var value = accessor.get(item),
                accumulator = (accumulator || value)
            if(accumulator > value) {
                accumulator = value;
            }
            return accumulator;
        }
    };

    function process(data, options) {
        var query = new Query(data),
            options = options || {},
            group = options.group,
            sort = normalizeSort(options.sort || []).concat(normalizeGroup(group || [])),
            total,
            filter = options.filter,
            skip = options.skip,
            take = options.take;

        if (filter) {
            query = query.filter(filter);
            total = query.toArray().length;
        }

        if (sort) {
            query = query.sort(sort);

            if (group) {
                data = query.toArray();
            }
        }

        if (skip !== undefined && take !== undefined) {
            query = query.range(skip, take);
        }

        if (group) {
            query = query.group(group, data);
        }

        return {
            total: total,
            data: query.toArray()
        };
    }

    function calculateAggregates(data, options) {
        var query = new Query(data),
            options = options || {},
            aggregates = options.aggregate,
            filter = options.filter;

        if(filter) {
            query = query.filter(filter);
        }
        return query.aggregate(aggregates);
    }

    var LocalTransport = Class.extend({
        init: function(options) {
            this.data = options.data;
        },

        read: function(options) {
            options.success(this.data);
        },
        update: function(options) {
            options.success(options.data);
        },
        create: noop,
        destory: noop
    });

    var RemoteTransport = Class.extend( {
        init: function(options) {
            var that = this, parameterMap;

            options = that.options = extend({}, that.options, options);

            each(crud, function(index, type) {
                if (typeof options[type] === STRING) {
                    options[type] = {
                        url: options[type]
                    };
                }
            });

            that.cache = options.cache? Cache.create(options.cache) : {
                find: noop,
                add: noop
            }

            parameterMap = options.parameterMap;

            that.parameterMap = isFunction(parameterMap) ? parameterMap : function(options) {
                var result = {};

                each(options, function(option, value) {
                    if (option in parameterMap) {
                        option = parameterMap[option];
                        if (isPlainObject(option)) {
                            value = option.value(value);
                            option = option.key;
                        }
                    }

                    result[option] = value;
                });

                return result;
            };
        },

        options: {
            parameterMap: identity
        },

        create: function(options) {
            return ajax(this.setup(options, CREATE));
        },

        read: function(options) {
            var that = this,
                success,
                error,
                result,
                cache = that.cache;

            options = that.setup(options, READ);

            success = options.success || noop;
            error = options.error || noop;

            result = cache.find(options.data);

            if(result !== undefined) {
                success(result);
            } else {
                options.success = function(result) {
                    cache.add(options.data, result);

                    success(result);
                };

                $.ajax(options);
            }
        },

        update: function(options) {
            return ajax(this.setup(options, UPDATE));
        },

        destroy: function(options) {
            return ajax(this.setup(options, DESTROY));
        },

        setup: function(options, type) {
            options = options || {};

            var that = this,
                operation = that.options[type],
                data = isFunction(operation.data) ? operation.data() : operation.data;

            options = extend(true, {}, operation, options);
            options.data = that.parameterMap(extend(data, options.data), type);

            return options;
        }
    });

    Cache.create = function(options) {
        var store = {
            "inmemory": function() { return new Cache(); }
        };

        if (isPlainObject(options) && isFunction(options.find)) {
            return options;
        }

        if (options === true) {
            return new Cache();
        }

        return store[options]();
    }

    function Cache() {
        this._store = {};
    }

    Cache.prototype = /** @ignore */ {
        add: function(key, data) {
            if(key !== undefined) {
                this._store[stringify(key)] = data;
            }
        },
        find: function(key) {
            return this._store[stringify(key)];
        },
        clear: function() {
            this._store = {};
        },
        remove: function(key) {
            delete this._store[stringify(key)];
        }
    }

    var DataReader = Class.extend({
        init: function(schema) {
            var that = this, member, get;

            schema = schema || {};

            for (member in schema) {
                get = schema[member];

                that[member] = typeof get === STRING ? getter(get) : get;
            }

            if (isPlainObject(that.model)) {
                that.model = Model.define(that.model);
            }
        },
        parse: identity,
        data: identity,
        total: function(data) {
            return data.length;
        },
        groups: identity,
        status: function(data) {
            return data.status;
        },
        aggregates: function() {
            return {};
        }
    });


    var DataSource = Observable.extend(/** @lends kendo.data.DataSource.prototype */ {
        /**
         * @constructs
         * @extends kendo.Observable
         * @param {Object} options Configuration options.
         * @option {Array} [data] The data in the DataSource.
         * @option {Boolean} [serverPaging] <false> Determines if paging of the data should be handled on the server.
         * @option {Boolean} [serverSorting] <false> Determines if sorting of the data should be handled on the server.
         * @option {Boolean} [serverGrouping] <false> Determines if grouping of the data should be handled on the server.
         * @option {Boolean} [serverFiltering] <false> Determines if filtering of the data should be handled on the server.
         * @option {Boolean} [serverAggregates] <false> Determines if aggregates should be calculated on the server.
         * @option {Number} [pageSize] <undefined> Sets the number of records which contains a given page of data.
         * @option {Number} [page] <undefined> Sets the index of the displayed page of data.
         * @option {Array|Object} [sort] <undefined> Sets initial sort order
         * _example
         * // sorts data ascending by orderId field
         * sort: { field: "orderId", dir: "asc" }
         *
         * // sorts data ascending by orderId field and then descending by shipmentDate
         * sort: [ { field: "orderId", dir: "asc" }, { field: "shipmentDate", dir: "desc" } ]
         *
         * @option {Array|Object} [filter] <undefined> Sets initial filter
         * _example
         * // returns only data where orderId is equal to 10248
         * filter: { field: "orderId", operator: "eq", value: 10248 }
         *
         * // returns only data where orderId is equal to 10248 and customerName starts with Paul
         * filter: [ { field: "orderId", operator: "eq", value: 10248 },
         *           { field: "customerName", operator: "startswith", value: "Paul" } ]
         *
         * @option {Array|Object} [group] <undefined> Sets initial grouping
         * _example
         * // groups data by orderId field
         * group: { field: "orderId" }
         *
         * // groups data by orderId and customerName fields
         * group: [ { field: "orderId", dir: "desc" }, { field: "customerName", dir: "asc" } ]
         *
         * @option {Array|Object} [aggregate] <undefined> Sets fields on which initial aggregates should be calculated
         * _example
         * // calculates total sum of unitPrice field's values.
         * [{ field: "unitPrice", aggregate: "sum" }]
         *
         * @option {Object} [transport] Sets the object responsible for loading and saving of data.
         *  This can be a remote or local/in-memory data.
         *
         * @option {Object|String} [transport.read] Options for remote read data operation or the URL of the remote service
         * _example
         * // settings various options for remote data transport
         * var dataSource = new kendo.data.DataSource({
         *     transport: {
         *         read: {
         *             // the remote service URL
         *             url: "http://search.twitter.com/search.json",
         *
         *             // JSONP is required for cross-domain AJAX
         *             dataType: "jsonp",
         *
         *             // additional parameters sent to the remote service
         *             data: {
         *                 q: function() {
         *                     return $("#searchFor").val();
         *                 }
         *             }
         *         }
         *     }
         * });
         *
         *  // consuming odata feed without setting additional options
         *  var dataSource = new kendo.data.DataSource({
         *      type: "odata",
         *      transport: {
         *          read: "http://odata.netflix.com/Catalog/Titles"
         *      }
         *  });
         *
         * @option {String} [transport.read.url] The remote service URL
         * @option {String} [transport.read.dataType] The type of data that you're expecting back from the server
         * @option {Object|Function} [transport.read.data] Additional data to be send to the server
         *
         * @option {Function} [transport.parameterMap] Convert the request parameters from dataSource format to remote service specific format.
         * _example
         *  var dataSource = new kendo.data.DataSource({
         *      transport: {
         *        read: "Catalog/Titles",
         *        parameterMap: function(options) {
         *           return {
         *              pageIndex: options.page,
         *              size: options.pageSize,
         *              orderBy: convertSort(options.sort)
         *           }
         *        }
         *      }
         *  });
         *
         * @option {Object} [schema] Set the object responsible for describing the raw data format
         * _example
         *  var dataSource = new kendo.data.DataSource({
         *      transport: {
         *        read: "Catalog/Titles",
         *      },
         *      schema: {
         *          data: function(data) {
         *              return data.result;
         *          },
         *          total: function(data) {
         *              return data.totalCount;
         *          },
         *          parse: function(data) {
         *              return data;
         *          }
         *      }
         *  });
         * @option {Function} [schema.parse] Executed before deserialized data is read.
         *  Appropriate for preprocessing of the raw data.
         *
         * @option {Function} [schema.data] Should return the deserialized data.
         * @option {Function} [schema.total] Should return the total number of records.
         * @option {Function} [schema.group] Used instead of data function if remote grouping operation is executed.
         *  Returns the deserialized data.
         **/
        init: function(options) {
            var that = this, id, model, transport;

            options = that.options = extend({}, that.options, options);

            extend(that, {
                _map: {},
                _prefetch: {},
                _data: [],
                _ranges: [],
                _view: [],
                _pageSize: options.pageSize,
                _page: options.page  || (options.pageSize ? 1 : undefined),
                _sort: normalizeSort(options.sort),
                _filter: normalizeFilter(options.filter),
                _group: normalizeGroup(options.group),
                _aggregate: options.aggregate
            });

            Observable.fn.init.call(that);

            transport = options.transport;

            if (transport) {
                transport.read = typeof transport.read === STRING ? { url: transport.read } : transport.read;

                if (options.type) {
                    transport = extend(true, {}, kendo.data.transports[options.type], transport);
                    options.schema = extend(true, {}, kendo.data.schemas[options.type], options.schema);
                }

                that.transport = isFunction(transport.read) ? transport: new RemoteTransport(transport);
            } else {
                that.transport = new LocalTransport({ data: options.data });
            }

            that.reader = new kendo.data.readers[options.schema.type || "json" ](options.schema);

            model = that.reader.model || {};

            id = model.id;

            if (Model && !isEmptyObject(model)) {
                that._set = new ModelSet({
                    model: model,
                    data: that._data,
                    reader: that.reader,
                    batch: options.batch,
                    sendAllFields: options.sendAllFields,
                    transport: that.transport,
                    change: function() {
                        var data = that.data();
                        that._total = that.reader.total(data);
                        that._process(data);
                    },
                    modelChange: function(model) {
                        that.trigger(MODELCHANGE, model);
                    },
                    error: function(response) {
                        that.trigger(ERROR, response);
                    }
                });
            }

            if (id) {
                that.id = function(record) {
                    return id(record);
                };
            }
            that.bind([ /**
                         * Fires when an error occurs during data retrieval.
                         * @name kendo.data.DataSource#error
                         * @event
                         */
                        ERROR,
                        /**
                         * Fires when data is changed
                         * @name kendo.data.DataSource#change
                         * @event
                         */
                        CHANGE,
                        CREATE, DESTROY, UPDATE, REQUESTSTART, MODELCHANGE], options);
        },

        options: {
            data: [],
            schema: {},
            serverSorting: false,
            serverPaging: false,
            serverFiltering: false,
            serverGrouping: false,
            serverAggregates: false,
            sendAllFields: true,
            batch: false
        },

        /**
         * Retrieves a Model instance by given id.
         * @param {Number} id of the model to be retrieved
         * @returns {Object} Model instance if found
         */
        get: function(id) {
            return this._set.get(id);
        },

        /**
         * Synchronizes changes through the transport.
         */
        sync: function() {
            this._set.sync();
        },

        /**
         * Adds a new Model instance to the DataSource
         * @param {Object} Either a Model instance or object from which the Model will be created
         * @returns {Object} The Model instance which has been added
         */
        add: function(model) {
            return this._set.add(model);
        },

        /**
         * Inserts a new Model instance to the DataSource.
         * @param {Object} Either a Model instance or object from which the Model will be created
         * @returns {Object} The Model instance which has been inserted
         */
        insert: function(index, model) {
            return this._set.insert(index, model);
        },

        /**
         * Cancel the changes made to the DataSource after the last sync.
         */
        cancelChanges : function() {
            this._set.cancelChanges();
        },

        /**
         * Populate the DataSource using the assign transport instance.
         */
        read: function(data) {
            var that = this, params = that._params(data);

            that._queueRequest(params, function() {
                that.trigger(REQUESTSTART);
                that._ranges = [];
                that.transport.read({
                    data: params,
                    success: proxy(that.success, that),
                    error: proxy(that.error, that)
                });
            });
        },

        indexOf: function(dataItem) {
            return this._set.indexOf(dataItem);
        },

        _params: function(data) {
            var that = this;

            return extend({
                take: that.take(),
                skip: that.skip(),
                page: that.page(),
                pageSize: that.pageSize(),
                sort: that._sort,
                filter: that._filter,
                group: that._group,
                aggregate: that._aggregate
            }, data);
        },

        _queueRequest: function(options, callback) {
            var that = this;
            if (!that._requestInProgress) {
                that._requestInProgress = true;
                that._pending = undefined;
                callback();
            } else {
                that._pending = { callback: proxy(callback, that), options: options };
            }
        },

        _dequeueRequest: function() {
            var that = this;
            that._requestInProgress = false;
            if (that._pending) {
                that._queueRequest(that._pending.options, that._pending.callback);
            }
        },

        /**
         * Removes a Model instance from the DataSource.
         * @param {Object} Model instance to be removed
         */
        remove: function(model) {
            this._set.remove(model);
        },

        error: function() {
            this.trigger(ERROR, arguments);
        },

        success: function(data) {
            var that = this,
                options = {},
                result,
                hasGroups = that.options.serverGrouping === true && that._group && that._group.length > 0;

            data = that.reader.parse(data);

            that._total = that.reader.total(data);

            if (that._aggregate && that.options.serverAggregates) {
                that._aggregateResult = that.reader.aggregates(data);
            }

            if (hasGroups) {
                data = that.reader.groups(data);
            } else {
                data = that.reader.data(data);
            }

            that._data = data;

            if (that._set) {
                that._set.data(data);
            }

            var start = that._skip || 0,
                end = start + data.length;

            that._ranges.push({ start: start, end: end, data: data });
            that._ranges.sort( function(x, y) { return x.start - y.start; } );

            that._dequeueRequest();
            that._process(data);
        },

        _process: function (data) {
            var that = this,
                options = {},
                result,
                hasGroups = that.options.serverGrouping === true && that._group && that._group.length > 0;

            if (that.options.serverPaging !== true) {
                options.skip = that._skip;
                options.take = that._take || that._pageSize;

                if(options.skip === undefined && that._page !== undefined && that._pageSize !== undefined) {
                    options.skip = (that._page - 1) * that._pageSize;
                }
            }

            if (that.options.serverSorting !== true) {
                options.sort = that._sort;
            }

            if (that.options.serverFiltering !== true) {
                options.filter = that._filter;
            }

            if (that.options.serverGrouping !== true) {
                options.group = that._group;
            }

            if (that.options.serverAggregates !== true) {
                options.aggregate = that._aggregate;
                that._aggregateResult = calculateAggregates(data, options);
            }

            result = process(data, options);

            that._view = result.data;

            if (result.total !== undefined && !that.options.serverFiltering) {
                that._total = result.total;
            }

            that.trigger(CHANGE);
        },

        /**
         * Returns the raw data record at the specified index
         * @param {Number} The zero-based index of the data record
         * @returns {Object}
         */
        at: function(index) {
            return this._data[index];
        },

        /**
         * Get data return from the transport
         * @returns {Array} Array of items
         */
        data: function(value) {
            var that = this;
            if (value !== undefined) {
                that._data = value;

                if (that._set) {
                    that._set.data(value);
                }

                that._process(value);
            } else {
                return that._data;
            }
        },

        /**
         * Returns a view of the data with operation such as in-memory sorting, paring, grouping and filtering are applied.
         * To ensure that data is available this method should be use from within change event of the dataSource.
         * @example
         * dataSource.bind("change", function() {
         *   renderView(dataSource.view());
         * });
         * @returns {Array} Array of items
         */
        view: function() {
            return this._view;
        },

        /**
         * Executes a query over the data. Available operations are paging, sorting, filtering, grouping.
         * If data is not available or remote operations are enabled data is requested through the transport,
         * otherwise operations are executed over the available data.
         * @param {Object} [options] Contains the settings for the operations. Note: If setting for previous operation is omitted, this operation is not applied to the resulting view
         * @example
         *
         * // create a view containing at most 20 records, taken from the
         * // 5th page and sorted ascending by orderId field.
         * dataSource.query({ page: 5, pageSize: 20, sort: { field: "orderId", dir: "asc" } });
         *
         * // moves the view to the first page returning at most 20 records
         * // but without particular ordering.
         * dataSource.query({ page: 1, pageSize: 20 });
         *
         */
        query: function(options) {
            var that = this,
                result,
                remote = that.options.serverSorting || that.options.serverPaging || that.options.serverFiltering || that.options.serverGrouping || that.options.serverAggregates;

            if (options !== undefined) {
                that._pageSize = options.pageSize;
                that._page = options.page;
                that._sort = options.sort;
                that._filter = options.filter;
                that._group = options.group;
                that._aggregate = options.aggregate;
                that._skip = options.skip;
                that._take = options.take;

                if(that._skip === undefined) {
                    that._skip = that.skip();
                    options.skip = that.skip();
                }

                if(that._take === undefined && that._pageSize !== undefined) {
                    that._take = that._pageSize;
                    options.take = that._take;
                }

                if (options.sort) {
                    that._sort = options.sort = normalizeSort(options.sort);
                }

                if (options.filter) {
                    that._filter = options.filter = normalizeFilter(options.filter);
                }

                if (options.group) {
                    that._group = options.group = normalizeGroup(options.group);
                }
                if (options.aggregate) {
                    that._aggregate = options.aggregate = normalizeAggregate(options.aggregate);
                }
            }

            if (remote || (that._data === undefined || that._data.length == 0)) {
                that.read(options);
            } else {
                that.trigger(REQUESTSTART);
                result = process(that._data, options);

                if (!that.options.serverFiltering) {
                    if (result.total !== undefined) {
                        that._total = result.total;
                    } else {
                        that._total = that.reader.total(that._data);
                    }
                }

                that._view = result.data;
                that._aggregateResult = calculateAggregates(that._data, options);
                that.trigger(CHANGE);
            }
        },

        /**
         * Fetches data using the current filter/sort/group/paging information.
         * If data is not available or remote operations are enabled data is requested through the transport,
         * otherwise operations are executed over the available data.
         */
        fetch: function(callback) {
            var that = this;

            if (callback && isFunction(callback)) {
                that.one(CHANGE, callback);
            }

            that._query();
        },

        _query: function(options) {
            var that = this;

            that.query(extend({}, {
                page: that.page(),
                pageSize: that.pageSize(),
                sort: that.sort(),
                filter: that.filter(),
                group: that.group(),
                aggregate: that.aggregate()
            }, options));
        },

        /**
         * Get current page index or request a page with specified index.
         * @param {Number} [val] <undefined> The index of the page to be retrieved
         * @example
         * dataSource.page(2);
         * @returns {Number} Current page index
         */
        page: function(val) {
            var that = this,
                skip;

            if(val !== undefined) {
                val = math.max(math.min(math.max(val, 1), that.totalPages()), 1);
                that._query({ page: val });
                return;
            }
            skip = that.skip();

            return skip !== undefined ? math.round((skip || 0) / (that.take() || 1)) + 1 : undefined;
        },

        /**
         * Get current pageSize or request a page with specified number of records.
         * @param {Number} [val] <undefined> The of number of records to be retrieved.
         * @example
         * dataSource.pageSiza(25);
         * @returns {Number} Current page size
         */
        pageSize: function(val) {
            var that = this;

            if(val !== undefined) {
                that._query({ pageSize: val });
                return;
            }

            return that.take();
        },

        /**
         * Get current sort descriptors or sorts the data.
         * @param {Object|Array} [val] <undefined> Sort options to be applied to the data
         * @example
         * dataSource.sort({ field: "orderId", dir: "desc" });
         * dataSource.sort([
         *      { field: "orderId", dir: "desc" },
         *      { field: "unitPrice", dir: "asc" }
         * ]);
         * @returns {Array} Current sort descriptors
         */
        sort: function(val) {
            var that = this;

            if(val !== undefined) {
                that._query({ sort: val });
                return;
            }

            return that._sort;
        },

        /**
         * Get current filters or filter the data.
         *<p>
         * <i>Supported filter operators/aliases are</i>:
         * <ul>
         * <li><strong>Equal To</strong>: "eq", "==", "isequalto", "equals", "equalto", "equal"</li>
         * <li><strong>Not Equal To</strong>: "neq", "!=", "isnotequalto", "notequals", "notequalto", "notequal", "ne"</li>
         * <li><strong>Less Then</strong>: "lt", "<", "islessthan", "lessthan", "less"</li>
         * <li><strong>Less Then or Equal To</strong>: "lte", "<=", "islessthanorequalto", "lessthanequal", "le"</li>
         * <li><strong>Greater Then</strong>: "gt", ">", "isgreaterthan", "greaterthan", "greater"</li>
         * <li><strong>Greater Then or Equal To</strong>: "gte", ">=", "isgreaterthanorequalto", "greaterthanequal", "ge"</li>
         * <li><strong>Starts With</strong>: "startswith"</li>
         * <li><strong>Ends With</strong>: "endswith"</li>
         * <li><strong>Contains</strong>: "contains", "substringof"</li>
         * </ul>
         * </p>
         * @param {Object|Array} [val] <undefined> Filter(s) to be applied to the data.
         * @example
         * dataSource.filter({ field: "orderId", operator: "eq", value: 10428 });
         * dataSource.filter([
         *      { field: "orderId", operator: "neq", value: 42 },
         *      { field: "unitPrice", operator: "ge", value: 3.14 }
         * ]);
         * @returns {Array} Current filter descriptors
         */
        filter: function(val) {
            var that = this;

            if (val === undefined) {
                return that._filter;
            }

            that._query({ filter: val });
        },

        /**
         * Get current group descriptors or group the data.
         * @param {Object|Array} [val] <undefined> Group(s) to be applied to the data.
         * @example
         * dataSource.group({ field: "orderId" });
         * @returns {Array} Current grouping descriptors
         */
        group: function(val) {
            var that = this;

            if(val !== undefined) {
                that._query({ group: val });
                return;
            }

            return that._group;
        },

        /**
         * Get the total number of records
         */
        total: function() {
            return this._total;
        },

        /**
         * Get current aggregate descriptors or applies aggregates to the data.
         * @param {Object|Array} [val] <undefined> Aggregate(s) to be applied to the data.
         * @example
         * dataSource.aggregate({ field: "orderId", aggregate: "sum" });
         * @returns {Array} Current aggregate descriptors
         */
        aggregate: function(val) {
            var that = this;

            if(val !== undefined) {
                that._query({ aggregate: val });
                return;
            }

            return that._aggregate;
        },

        /**
         * Get result of aggregates calculation
         * @returns {Array} Aggregates result
         */
        aggregates: function() {
            return this._aggregateResult;
        },

        /**
         * Get the number of available pages.
         * @returns {Number} Number of available pages.
         */
        totalPages: function() {
            var that = this,
                pageSize = that.pageSize() || that.total();

            return math.ceil((that.total() || 0) / pageSize);
        },

        inRange: function(skip, take) {
            var that = this,
                end = math.min(skip + take, that.total());

            if (!that.options.serverPaging && that.data.length > 0) {
                return true;
            }

            return that._findRange(skip, end).length > 0;
        },

        range: function(skip, take) {
            skip = math.min(skip || 0, this.total());
            var that = this,
                pageSkip = math.max(math.floor(skip / take), 0) * take,
                size = math.min(pageSkip + take, that.total()),
                data;

            data = that._findRange(skip, math.min(skip + take, that.total()));
            if (data.length) {
                that._skip = skip > that.skip() ? math.min(size, (that.totalPages() - 1) * that.take()) : pageSkip;

                that._take = take;

                var paging = that.options.serverPaging;
                try {
                    that.options.serverPaging = true;
                    that._process(data);
                } finally {
                    that.options.serverPaging = paging;
                }

                return;
            }

            if (take !== undefined) {
                if (!that._rangeExists(pageSkip, size)) {
                    that.prefetch(pageSkip, take, function() {
                        if (skip > pageSkip && size < that.total() && !that._rangeExists(size, math.min(size + take, that.total()))) {
                            that.prefetch(size, take, function() {
                                that.range(skip, take);
                            });
                        } else {
                            that.range(skip, take);
                        }
                    });
                } else if (pageSkip < skip) {
                    that.prefetch(size, take, function() {
                        that.range(skip, take);
                    });
                }
            }
        },

        _findRange: function(start, end) {
            var that = this,
                length,
                ranges = that._ranges,
                range,
                data = [],
                skipIdx,
                takeIdx,
                startIndex,
                endIndex,
                length;

            for (skipIdx = 0, length = ranges.length; skipIdx < length; skipIdx++) {
                range = ranges[skipIdx];
                if (start >= range.start && start <= range.end) {
                    var count = 0;

                    for (takeIdx = skipIdx; takeIdx < length; takeIdx++) {
                        range = ranges[takeIdx];
                        if (range.data.length && start + count >= range.start && count + count <= range.end) {
                            startIndex = 0;
                            if (start + count > range.start) {
                                startIndex = (start + count) - range.start;
                            }
                            endIndex = range.data.length;
                            if (range.end > end) {
                                endIndex = endIndex - (range.end - end);
                            }
                            count += endIndex - startIndex;
                            data = data.concat(range.data.slice(startIndex, endIndex));

                            if (end <= range.end && count == end - start) {
                                return data;
                            }
                        }
                    }
                    break;
                }
            }
            return [];
        },

        skip: function() {
            var that = this;

            if (that._skip === undefined) {
                return (that._page !== undefined ? (that._page  - 1) * (that.take() || 1) : undefined);
            }
            return that._skip;
        },

        take: function() {
            var that = this;
            return that._take || that._pageSize;
        },

        prefetch: function(skip, take, callback) {
            var that = this,
                size = math.min(skip + take, that.total()),
                range = { start: skip, end: size, data: [] },
                options = {
                    take: take,
                    skip: skip,
                    page: skip / take + 1,
                    pageSize: take,
                    sort: that._sort,
                    filter: that._filter,
                    group: that._group,
                    aggregate: that._aggregate
                };

            if (!that._rangeExists(skip, size)) {
                clearTimeout(that._timeout);

                that._timeout = setTimeout(function() {
                    that._queueRequest(options, function() {
                        that.transport.read({
                            data: options,
                            success: function (data) {
                                that._dequeueRequest();
                                var found = false;
                                for (var i = 0, len = that._ranges.length; i < len; i++) {
                                    if (that._ranges[i].start === skip) {
                                        found = true;
                                        range = that._ranges[i];
                                        break;
                                    }
                                }
                                if (!found) {
                                    that._ranges.push(range);
                                }
                                data = that.reader.parse(data);
                                range.data = that.reader.data(data);
                                range.end = range.start + range.data.length;
                                that._ranges.sort( function(x, y) { return x.start - y.start; } );
                                if (callback) {
                                    callback();
                                }
                            }
                        });
                    });
               }, 100);
            } else if (callback) {
                callback();
            }
        },

        _rangeExists: function(start, end) {
            var that = this,
                ranges = that._ranges,
                idx,
                length;

            for (idx = 0, length = ranges.length; idx < length; idx++) {
                if (ranges[idx].start <= start && ranges[idx].end >= end) {
                    return true;
                }
            }
            return false;
        }
    });

    /** @ignore */
    DataSource.create = function(options) {
        options = isArray(options) ? { data: options } : options;

        var dataSource = options || {},
            data = dataSource.data,
            fields = dataSource.fields,
            table = dataSource.table,
            select = dataSource.select;

        if(!data && fields && !dataSource.transport){
            if (table) {
                data = inferTable(table, fields);
            } else if (select) {
                data = inferSelect(select, fields);
            }
        }

        dataSource.data = data;

        return dataSource instanceof DataSource ? dataSource : new DataSource(dataSource);
    }

    function inferSelect(select, fields) {
        var options = $(select)[0].children,
            idx,
            length,
            data = [],
            record,
            firstField = fields[0],
            secondField = fields[1],
            option;

        for (idx = 0, length = options.length; idx < length; idx++) {
            record = {};
            option = options[idx];

            record[firstField.field] = option.text;
            record[secondField.field] = option.value;

            data.push(record);
        }

        return data;
    }

    function inferTable(table, fields) {
        var tbody = $(table)[0].tBodies[0],
        rows = tbody ? tbody.rows : [],
        idx,
        length,
        fieldIndex,
        fieldCount = fields.length,
        data = [],
        cells,
        record,
        cell,
        empty;

        for (idx = 0, length = rows.length; idx < length; idx++) {
            record = {};
            empty = true;
            cells = rows[idx].cells;

            for (fieldIndex = 0; fieldIndex < fieldCount; fieldIndex++) {
                cell = cells[fieldIndex];
                if(cell.nodeName.toLowerCase() !== "th") {
                    empty = false;
                    record[fields[fieldIndex].field] = cell.innerHTML;
                }
            }
            if(!empty) {
                data.push(record);
            }
        }

        return data;
    }

    extend(true, kendo.data, /** @lends kendo.data */ {
        readers: {
            json: DataReader
        },
        Query: Query,
        DataSource: DataSource,
        LocalTransport: LocalTransport,
        RemoteTransport: RemoteTransport,
        Cache: Cache,
        DataReader: DataReader
    });
})(jQuery);
(function ($, undefined) {

    // Imports ================================================================
    var doc = document,
        kendo = window.kendo,
        Class = kendo.Class,
        Widget = kendo.ui.Widget,
        DataSource = kendo.data.DataSource,
        baseTemplate = kendo.template,
        format = kendo.format,
        map = $.map,
        math = Math,
        proxy = $.proxy,
        getter = kendo.getter,
        extend = $.extend;

    var template = function(definition) {
        return baseTemplate(definition, { useWithBlock: false, paramName: "d" });
    }

    // Constants ==============================================================
    var ABOVE = "above",
        DEFAULT_FONT = "12px sans-serif",
        ANIMATION_STEP = 10,
        BASELINE_MARKER_SIZE = 1,
        BAR = "bar",
        BAR_BORDER_BRIGHTNESS = 0.8,
        BAR_GAP = 1.5,
        BAR_SPACING = 0.4,
        BELOW = "below",
        BLACK = "#000",
        BOTTOM = "bottom",
        CENTER = "center",
        CHANGE = "change",
        CIRCLE = "circle",
        CLICK = "click",
        CLIP = "clip",
        COLUMN = "column",
        COORD_PRECISION = 3,
        DATABOUND = "dataBound",
        DEFAULT_HEIGHT = 400,
        DEFAULT_PRECISION = 6,
        DEFAULT_WIDTH = 600,
        DEGREE = math.PI / 180,
        FADEIN = "fadeIn",
        GLASS = "glass",
        HEIGHT = "height",
        HORIZONTAL = "horizontal",
        INITIAL_ANIMATION_DURATION = 600,
        INSIDE_BASE = "insideBase",
        INSIDE_END = "insideEnd",
        INTERPOLATE = "interpolate",
        LEFT = "left",
        LINE = "line",
        LINE_MARKER_SIZE = 8,
        LINEAR = "linear",
        MOUSEMOVE_TRACKING = "mousemove.tracking",
        MOUSEOVER = "mouseover",
        NONE = "none",
        NUM_AXIS_PADDING = 1.05,
        OBJECT = "object",
        ON_MINOR_TICKS = "onMinorTicks",
        OUTSIDE = "outside",
        OUTSIDE_END = "outsideEnd",
        OUTLINE_SUFFIX = "_outline",
        PIE = "pie",
        PIE_SECTOR_ANIM_DELAY = 70,
        RADIAL = "radial",
        RIGHT = "right",
        ROUNDED_BEVEL = "roundedBevel",
        SERIES_CLICK = "seriesClick",
        SQUARE = "square",
        SWING = "swing",
        TOP = "top",
        TOOLTIP_ANIMATION_DURATION = 150,
        TOOLTIP_OFFSET = 5,
        TOOLTIP_SHOW_DELAY = 100,
        TRIANGLE = "triangle",
        UNDEFINED = "undefined",
        VERTICAL = "vertical",
        WIDTH = "width",
        WHITE = "#fff",
        X = "x",
        Y = "y",
        ZERO = "zero",
        ZERO_THRESHOLD = 0.2;

    // Chart ==================================================================
    var Chart = Widget.extend({
        init: function(element, options) {
            var chart = this,
                themeOptions,
                theme;

            Widget.fn.init.call(chart, element);

            if (options && options.dataSource) {
                chart.dataSource = DataSource
                    .create(options.dataSource)
                    .bind(CHANGE, proxy(chart._onDataChanged, chart));
            }

            options = deepExtend({}, chart.options, options);
            theme = options.theme;
            themeOptions = theme ? Chart.themes[theme] || Chart.themes[theme.toLowerCase()] : {};

            applyAxisDefaults(options, themeOptions);
            applySeriesDefaults(options, themeOptions);

            chart.options = deepExtend({}, themeOptions, options);

            applySeriesColors(chart.options);

            chart.bind([
                DATABOUND,
                SERIES_CLICK
            ], chart.options);

            $(element).addClass("k-chart");

            chart._refresh();
            chart._attachEvents();
        },

        options: {
            name: "Chart",
            chartArea: {},
            title: {
                visible: true
            },
            legend: {
                visible: true
            },
            valueAxis: {
                type: "Numeric"
            },
            categoryAxis: {
                categories: []
            },
            autoBind: true,
            seriesDefaults: {
                type: COLUMN,
                data: [],
                bar: {
                    gap: BAR_GAP,
                    spacing: BAR_SPACING
                },
                column: {
                    gap: BAR_GAP,
                    spacing: BAR_SPACING
                },
                line: {
                    width: 4
                },
                labels: {}
            },
            series: [],
            tooltip: {
                visible: false
            },
            transitions: true
        },

        refresh: function() {
            var chart = this;

            applySeriesDefaults(chart.options);
            applyAxisDefaults(chart.options);

            chart._refresh();
        },

        _refresh: function() {
            var chart = this;
            if (chart.options.dataSource && chart.options.autoBind) {
                chart.dataSource.query();
            } else {
                chart._redraw();
            }
        },

        _redraw: function() {
            var chart = this,
                options = chart.options,
                element = chart.element,
                model = chart._model = chart._getModel(),
                plotArea = chart._plotArea = model._plotArea,
                viewClass = chart._supportsSVG() ? Chart.SVGView : Chart.VMLView,
                view = chart._view = viewClass.fromModel(model);

            element.css("position", "relative");
            chart._viewElement = view.renderTo(element[0]);
            chart._tooltip = new Tooltip(element, options.tooltip);
            chart._highlight = new Highlight(view, chart._viewElement);
        },

        svg: function() {
            var model = this._getModel(),
                view = Chart.SVGView.fromModel(model);

            return view.render();
        },

        _getModel: function() {
            var chart = this,
                options = chart.options,
                element = chart.element,
                model = new RootElement(deepExtend({
                    width: element.width() || DEFAULT_WIDTH,
                    height: element.height() || DEFAULT_HEIGHT,
                    transitions: options.transitions
                    }, options.chartArea)),
                plotArea;

            if (options.title && options.title.visible && options.title.text) {
                model.append(new Title(options.title));
            }

            plotArea = model._plotArea = new PlotArea(options);
            if (options.legend.visible) {
                model.append(new Legend(plotArea.options.legend));
            }
            model.append(plotArea);
            model.reflow();

            return model;
        },

        // Needs to be overridable in tests
        _supportsSVG: supportsSVG,

        _attachEvents: function() {
            var chart = this,
                element = chart.element;

            element.bind(CLICK, proxy(chart._click, chart));
            element.bind(MOUSEOVER, proxy(chart._mouseOver, chart));
        },

        _getPoint: function(e) {
            var chart = this,
                model = chart._model,
                coords = chart._eventCoordinates(e),
                targetId = e.target.id,
                chartElement = model.idMap[targetId],
                metadata = model.idMapMetadata[targetId],
                point;

            if (chartElement) {
                if (chartElement.getNearestPoint && metadata) {
                    point = chartElement.getNearestPoint(coords.x, coords.y, metadata.seriesIx);
                } else {
                    point = chartElement;
                }
            }

            return point;
        },

        _eventCoordinates: function(e) {
            var element = this.element,
                offset = element.offset(),
                paddingLeft = parseInt(element.css("paddingLeft"), 10),
                paddingTop = parseInt(element.css("paddingTop"), 10),
                win = $(window);

            return({
                x: e.clientX - offset.left - paddingLeft + win.scrollLeft(),
                y: e.clientY - offset.top - paddingTop + win.scrollTop()
            });
        },

        _click: function(e) {
            var chart = this,
                point = chart._getPoint(e);

            if (point) {
                chart.trigger(SERIES_CLICK, {
                    value: point.value,
                    category: point.category,
                    series: point.series,
                    dataItem: point.dataItem,
                    element: $(e.target)
                });
            }
        },

        _mouseOver: function(e) {
            var chart = this,
                tooltip = chart._tooltip,
                highlight = chart._highlight,
                tooltipOptions,
                point;

            if (!highlight || highlight.element === e.target) {
                return;
            }

            point = chart._getPoint(e);
            if (point) {
                chart._activePoint = point;
                tooltipOptions = deepExtend({}, chart.options.tooltip, point.options.tooltip);
                if (tooltipOptions.visible) {
                    tooltip.show(point);
                }

                highlight.show(point);

                $(doc.body).bind(MOUSEMOVE_TRACKING, proxy(chart._mouseMove, chart));
            }
        },

        _mouseMove: function(e) {
            var chart = this,
                tooltip = chart._tooltip,
                highlight = chart._highlight,
                coords = chart._eventCoordinates(e),
                point = chart._activePoint,
                tooltipOptions,
                owner,
                seriesPoint;

            if (chart._plotArea.box.containsPoint(coords.x, coords.y)) {
                if (point && point.series.type === LINE) {
                    owner = point.owner;
                    seriesPoint = owner.getNearestPoint(coords.x, coords.y, point.seriesIx);
                    if (seriesPoint && seriesPoint != point) {
                        chart._activePoint = seriesPoint;

                        tooltipOptions = deepExtend({}, chart.options.tooltip, point.options.tooltip);
                        if (tooltipOptions.visible) {
                            tooltip.show(seriesPoint);
                        }
                        highlight.show(seriesPoint);
                    }
                }
            } else {
                $(doc.body).unbind(MOUSEMOVE_TRACKING);

                delete chart._activePoint;
                tooltip.hide();
                highlight.hide();
            }
        },

        _onDataChanged: function() {
            var chart = this,
                options = chart.options,
                series = options.series,
                categoryAxis = options.categoryAxis,
                data = chart.dataSource.view(),
                row,
                category,
                currentSeries,
                value;

            for (var seriesIdx = 0, seriesLength = series.length; seriesIdx < seriesLength; seriesIdx++) {
                currentSeries = series[seriesIdx];
                if (currentSeries.field || (currentSeries.xField && currentSeries.yField)) {
                    currentSeries.data = [];
                    currentSeries.dataItems = [];
                }
            }

            for (var dataIdx = 0, dataLength = data.length; dataIdx < dataLength; dataIdx++) {
                row = data[dataIdx];

                if (categoryAxis.field) {
                    category = getter(categoryAxis.field, true)(row);
                    if (dataIdx === 0) {
                        categoryAxis.categories = [category];
                    } else {
                        categoryAxis.categories.push(category);
                    }
                }

                for (var seriesIdx = 0, seriesLength = series.length; seriesIdx < seriesLength; seriesIdx++) {
                    currentSeries = series[seriesIdx];

                    if (currentSeries.field) {
                        value = getter(currentSeries.field, true)(row);
                    } else if (currentSeries.xField && currentSeries.yField) {
                        value = [getter(currentSeries.xField, true)(row), getter(currentSeries.yField, true)(row)];
                    } else {
                        value = undefined;
                    }

                    if (defined(value)) {
                        if (dataIdx === 0) {
                            currentSeries.data = [value];
                            currentSeries.dataItems = [row];
                        } else {
                            currentSeries.data.push(value);
                            currentSeries.dataItems.push(row);
                        }
                    }
                }
            }

            chart.trigger(DATABOUND);
            chart._redraw();
        }
    });


    // **************************
    // View Model
    // **************************
    var Point2D = Class.extend({
        init: function(x, y) {
            var point = this;
            point.x = round(x, COORD_PRECISION);
            point.y = round(y, COORD_PRECISION);
        }
    });

    var Box2D = Class.extend({
        init: function(x1, y1, x2, y2) {
            var box = this;
            box.x1 = x1 || 0;
            box.x2 = x2 || 0;
            box.y1 = y1 || 0;
            box.y2 = y2 || 0;
        },

        width: function() {
            return this.x2 - this.x1;
        },

        height: function() {
            return this.y2 - this.y1;
        },

        translate: function(dx, dy) {
            var box = this;

            box.x1 += dx;
            box.x2 += dx;
            box.y1 += dy;
            box.y2 += dy;

            return box;
        },

        move: function(x, y) {
            var box = this,
                height = box.height(),
                width = box.width();

            box.x1 = x;
            box.y1 = y;
            box.x2 = box.x1 + width;
            box.y2 = box.y1 + height;

            return box;
        },

        wrap: function(targetBox) {
            var box = this;

            box.x1 = math.min(box.x1, targetBox.x1);
            box.y1 = math.min(box.y1, targetBox.y1);
            box.x2 = math.max(box.x2, targetBox.x2);
            box.y2 = math.max(box.y2, targetBox.y2);

            return box;
        },

        snapTo: function(targetBox, axis) {
            var box = this;

            if (axis == X || !axis) {
                box.x1 = targetBox.x1;
                box.x2 = targetBox.x2;
            }

            if (axis == Y || !axis) {
                box.y1 = targetBox.y1;
                box.y2 = targetBox.y2;
            }

            return box;
        },

        alignTo: function(targetBox, edge) {
            var box = this,
                height = box.height(),
                width = box.width(),
                axis = edge == TOP || edge == BOTTOM ? Y : X,
                offset = axis == Y ? height : width;

            if (edge == TOP || edge == LEFT) {
                box[axis + 1] = targetBox[axis + 1] - offset;
            } else {
                box[axis + 1] = targetBox[axis + 2];
            }

            box.x2 = box.x1 + width;
            box.y2 = box.y1 + height;

            return box;
        },

        shrink: function(dw, dh) {
            var box = this;

            box.x2 -= dw;
            box.y2 -= dh;

            return box;
        },

        expand: function(dw, dh) {
            this.shrink(-dw, -dh);
            return this;
        },

        pad: function(padding) {
            var box = this,
                spacing = getSpacing(padding);

            box.x1 -= spacing.left;
            box.x2 += spacing.right;
            box.y1 -= spacing.top;
            box.y2 += spacing.bottom;

            return box;
        },

        unpad: function(padding) {
            var box = this,
                spacing = getSpacing(padding);

            spacing.left = -spacing.left;
            spacing.top = -spacing.top;
            spacing.right = -spacing.right;
            spacing.bottom = -spacing.bottom;

            return box.pad(spacing);
        },

        clone: function() {
            var box = this;

            return new Box2D(box.x1, box.y1, box.x2, box.y2);
        },

        center: function() {
            var box = this;

            return {
                x: box.x1 + box.width() / 2,
                y: box.y1 + box.height() / 2
            };
        },

        containsPoint: function(x, y) {
            var box = this;

            return x >= box.x1 && x <= box.x2 &&
                   y >= box.y1 && y <= box.y2;
        },

        points: function() {
            var box = this;

            return [
                new Point2D(box.x1, box.y1),
                new Point2D(box.x2, box.y1),
                new Point2D(box.x2, box.y2),
                new Point2D(box.x1, box.y2)
            ];
        }
    });

    var Sector = Class.extend({
        init: function(c, r, startAngle, angle) {
            var sector = this;

            sector.c = c;
            sector.r = r;
            sector.startAngle = startAngle;
            sector.angle = angle;
        },

        clone: function() {
            var s = this;
            return new Sector(s.c, s.r, s.startAngle, s.angle);
        },

        expand: function(value) {
            this.r += value;
            return this;
        },

        middle: function() {
            return this.startAngle + this.angle / 2;
        },

        radius: function(newRadius) {
            this.r = newRadius;
            return this;
        },

        point: function(angle) {
            var sector = this,
                radianAngle = angle * DEGREE,
                ax = math.cos(radianAngle),
                ay = math.sin(radianAngle),
                x = sector.c.x - (ax * sector.r),
                y = sector.c.y - (ay * sector.r);

            return new Point2D(x, y);
        }
    });

    var ChartElement = Class.extend({
        init: function(options) {
            var element = this;
            element.children = [];

            element.options = deepExtend({}, element.options, options);
        },

        reflow: function(targetBox) {
            var element = this,
                children = element.children,
                box,
                i,
                currentChild;

            for (i = 0; i < children.length; i++) {
                currentChild = children[i];

                currentChild.reflow(targetBox);
                box = box ? box.wrap(currentChild.box) : currentChild.box.clone();
            }

            element.box = box;
        },

        getViewElements: function(view) {
            var element = this,
                viewElements = [],
                children = element.children,
                childrenCount = children.length;

            for (var i = 0; i < childrenCount; i++) {
                viewElements.push.apply(viewElements,
                    children[i].getViewElements(view));
            }

            return viewElements;
        },

        registerId: function(id, metadata) {
            var element = this,
                root;

            root = element.getRoot();
            if (root) {
                root.idMap[id] = element;
                if (metadata) {
                    root.idMapMetadata[id] = metadata;
                }
            }
        },

        translateChildren: function(dx, dy) {
            var element = this,
                children = element.children,
                childrenCount = children.length,
                i;

            for (i = 0; i < childrenCount; i++) {
                children[i].box.translate(dx, dy);
            }
        },

        append: function() {
            var element = this,
                i,
                length = arguments.length;

            append(element.children, arguments);

            for (i = 0; i < length; i++) {
                arguments[i].parent = element;
            }
        },

        getRoot: function() {
            var element = this,
                parent = element.parent;

            return parent ? parent.getRoot() : null;
        }
    });

    var RootElement = ChartElement.extend({
        init: function(options) {
            var root = this;

            root.idMap = {};
            root.idMapMetadata = {};

            ChartElement.fn.init.call(root, options);
        },

        options: {
            width: DEFAULT_WIDTH,
            height: DEFAULT_HEIGHT,
            background: WHITE,
            border: {
                color: BLACK,
                width: 0
            },
            margin: getSpacing(5),
            zIndex: -1
        },

        reflow: function() {
            var root = this,
                options = root.options,
                children = root.children,
                currentBox = new Box2D(0, 0, options.width, options.height);

            root.box = currentBox.unpad(options.margin);

            for (var i = 0; i < children.length; i++) {
                children[i].reflow(currentBox);
                currentBox = boxDiff(currentBox, children[i].box);
            }
        },

        getViewElements: function(view) {
            var root = this,
                options = root.options,
                border = options.border || {},
                box = root.box.clone().pad(options.margin).unpad(border.width),
                elements = [
                    view.createRect(box, {
                        stroke: border.width ? border.color : "",
                        strokeWidth: border.width,
                        dashType: border.dashType,
                        fill: options.background,
                        zIndex: options.zIndex })
                ];

            return elements.concat(
                ChartElement.fn.getViewElements.call(root, view)
            );
        },

        getRoot: function() {
            return this;
        }
    });

    var BoxElement = ChartElement.extend({
        init: function(options) {
            ChartElement.fn.init.call(this, options);
        },

        options: {
            align: LEFT,
            vAlign: TOP,
            margin: {},
            padding: {},
            border: {
                color: BLACK,
                width: 0
            },
            background: "",
            width: 0,
            height: 0,
            visible: true
        },

        reflow: function(targetBox) {
            var element = this,
                box,
                contentBox,
                options = element.options,
                children = element.children,
                margin = getSpacing(options.margin),
                padding = getSpacing(options.padding),
                border = options.border,
                borderWidth = border.width;

            ChartElement.fn.reflow.call(element, targetBox);

            if (children.length === 0) {
                box = element.box = new Box2D(0, 0, options.width, options.height);
            } else {
                box = element.box;
            }

            contentBox = element.contentBox = box.clone();

            box.pad(padding).pad(borderWidth).pad(margin);

            element.align(targetBox, X, options.align);
            element.align(targetBox, Y, options.vAlign);

            element.paddingBox = box.clone().unpad(margin).unpad(borderWidth);

            element.translateChildren(
                box.x1 - contentBox.x1 + margin.left + borderWidth + padding.left,
                box.y1 - contentBox.y1 + margin.top + borderWidth + padding.top);
        },

        align: function(targetBox, axis, alignment) {
            var element = this,
                box = element.box,
                c1 = axis + 1,
                c2 = axis + 2,
                sizeFunc = axis === X ? WIDTH : HEIGHT,
                size = box[sizeFunc]();

            if (inArray(alignment, [LEFT, TOP])) {
                box[c1] = targetBox[c1];
                box[c2] = box[c1] + size;
            } else if (inArray(alignment, [RIGHT, BOTTOM])) {
                box[c2] = targetBox[c2];
                box[c1] = box[c2] - size;
            } else if (alignment == CENTER) {
                box[c1] = targetBox[c1] + (targetBox[sizeFunc]() - size) / 2;
                box[c2] = box[c1] + size;
            }
        },

        hasBox: function() {
            var options = this.options;
            return options.border.width || options.background;
        },

        getViewElements: function(view, renderOptions) {
            var boxElement = this,
                options = boxElement.options;

            if (!options.visible) {
                return [];
            }

            var border = options.border || {},
                elements = [];

            if (boxElement.hasBox()) {
                elements.push(
                    view.createRect(boxElement.paddingBox, deepExtend({
                        id: options.id,
                        stroke: border.width ? border.color : "",
                        strokeWidth: border.width,
                        dashType: border.dashType,
                        strokeOpacity: options.opacity,
                        fill: options.background,
                        fillOpacity: options.opacity,
                        animation: options.animation,
                        zIndex: options.zIndex
                    }, renderOptions))
                );
            }

            return elements.concat(
                ChartElement.fn.getViewElements.call(boxElement, view)
            );
        }
    });

    var Text = ChartElement.extend({
        init: function(content, options) {
            var text = this;

            ChartElement.fn.init.call(text, options);

            // Calculate size
            text.content = content;
            text.reflow(new Box2D());
        },

        options: {
            font: DEFAULT_FONT,
            color: BLACK,
            align: LEFT,
            vAlign: ""
        },

        reflow: function(targetBox) {
            var text = this,
                options = text.options,
                size = options.size = measureText(
                                        text.content,
                                        { font: options.font },
                                        options.rotation);

            text.baseline = size.baseline;

            if (options.align == LEFT) {
                text.box = new Box2D(
                    targetBox.x1, targetBox.y1,
                    targetBox.x1 + size.width, targetBox.y1 + size.height);
            } else if (options.align == RIGHT) {
                text.box = new Box2D(
                    targetBox.x2 - size.width, targetBox.y1,
                    targetBox.x2, targetBox.y1 + size.height);
            } else if (options.align == CENTER) {
                var margin = (targetBox.width() - size.width) / 2;
                text.box = new Box2D(
                    round(targetBox.x1 + margin, COORD_PRECISION), targetBox.y1,
                    round(targetBox.x2 - margin, COORD_PRECISION), targetBox.y1 + size.height);
            }

            if (options.vAlign == CENTER) {
                var margin = (targetBox.height() - size.height) /2;
                text.box = new Box2D(
                    text.box.x1, targetBox.y1 + margin,
                    text.box.x2, targetBox.y2 - margin);
            } else if (options.vAlign == BOTTOM) {
                text.box = new Box2D(
                    text.box.x1, targetBox.y2 - size.height,
                    text.box.x2, targetBox.y2);
            } else if (options.vAlign == TOP) {
                text.box = new Box2D(
                    text.box.x1, targetBox.y1,
                    text.box.x2, targetBox.y1 + size.height);
            }
        },

        getViewElements: function(view) {
            var text = this,
                options = text.options;

            ChartElement.fn.getViewElements.call(this, view);

            return [
                view.createText(text.content,
                    deepExtend({}, options, {
                        x: text.box.x1, y: text.box.y1,
                        baseline: text.baseline
                    })
                )
            ];
        }
    });

    var TextBox = BoxElement.extend({
        init: function(content, options) {
            var textBox = this,
                text;

            BoxElement.fn.init.call(textBox, options);
            options = textBox.options;

            if (!options.template) {
                content = options.format ? format(options.format, content) : content
            }

            text = new Text(content, deepExtend({ }, options, { align: LEFT, vAlign: TOP }));
            textBox.append(text);

            if (textBox.hasBox()) {
                text.options.id = uniqueId();
            }

            // Calculate size
            textBox.reflow(new Box2D());
        }
    });

    var BarLabel = ChartElement.extend({
        init: function(content, options) {
            var barLabel = this;
            ChartElement.fn.init.call(barLabel, options);

            barLabel.append(new TextBox(content, barLabel.options));
        },

        options: {
            position: OUTSIDE_END,
            margin: getSpacing(3),
            padding: getSpacing(4),
            color: BLACK,
            background: "",
            border: {
                width: 1,
                color: ""
            },
            aboveAxis: true,
            isVertical: false,
            animation: {
                type: FADEIN,
                delay: INITIAL_ANIMATION_DURATION
            },
            zIndex: 1
        },

        reflow: function(targetBox) {
            var barLabel = this,
                options = barLabel.options,
                isVertical = options.isVertical,
                aboveAxis = options.aboveAxis,
                text = barLabel.children[0],
                box = text.box,
                padding = text.options.padding;

            text.options.align = isVertical ? CENTER : LEFT;
            text.options.vAlign = isVertical ? TOP : CENTER;

            if (options.position == INSIDE_END) {
                if (isVertical) {
                    text.options.vAlign = TOP;

                    if (!aboveAxis && box.height() < targetBox.height()) {
                        text.options.vAlign = BOTTOM;
                    }
                } else {
                    text.options.align = aboveAxis ? RIGHT : LEFT;
                }
            } else if (options.position == CENTER) {
                text.options.vAlign = CENTER;
                text.options.align = CENTER;
            } else if (options.position == INSIDE_BASE) {
                if (isVertical) {
                    text.options.vAlign = aboveAxis ? BOTTOM : TOP;
                } else {
                    text.options.align = aboveAxis ? LEFT : RIGHT;
                }
            } else if (options.position == OUTSIDE_END) {
                if (isVertical) {
                    if (aboveAxis) {
                        targetBox = new Box2D(
                            targetBox.x1, targetBox.y1 - box.height(),
                            targetBox.x2, targetBox.y1
                        );
                    } else {
                        targetBox = new Box2D(
                            targetBox.x1, targetBox.y2,
                            targetBox.x2, targetBox.y2 + box.height()
                        );
                    }
                } else {
                    text.options.align = CENTER;
                    if (aboveAxis) {
                        targetBox = new Box2D(
                            targetBox.x2 + box.width(), targetBox.y1,
                            targetBox.x2, targetBox.y2
                        );
                    } else {
                        targetBox = new Box2D(
                            targetBox.x1 - box.width(), targetBox.y1,
                            targetBox.x1, targetBox.y2
                        );
                    }
                }
            }

            if (isVertical) {
                padding.left = padding.right =
                    (targetBox.width() - text.contentBox.width()) / 2;
            } else {
                padding.top = padding.bottom =
                    (targetBox.height() - text.contentBox.height()) / 2;
            }

            text.reflow(targetBox);
        }
    });

    var Title = ChartElement.extend({
        init: function(options) {
            var title = this;
            ChartElement.fn.init.call(title, options);

            title.append(
                new TextBox(title.options.text, deepExtend({}, title.options, {
                    vAlign: title.options.position
                }))
            );
        },

        options: {
            text: "",
            color: BLACK,
            position: TOP,
            align: CENTER,
            margin: getSpacing(5),
            padding: getSpacing(5)
        },

        reflow: function(targetBox) {
            var title = this;

            ChartElement.fn.reflow.call(title, targetBox);
            title.box.snapTo(targetBox, X);
        }
    });

    var Legend = ChartElement.extend({
        init: function(options) {
            var legend = this;

            ChartElement.fn.init.call(legend, options);

            legend.createLabels();
        },

        options: {
            position: RIGHT,
            items: [],
            labels: {},
            offsetX: 0,
            offsetY: 0,
            margin: getSpacing(10),
            padding: getSpacing(5),
            border: {
                color: BLACK,
                width: 0
            },
            background: "",
            zIndex: 1
        },

        createLabels: function() {
            var legend = this,
                items = legend.options.items,
                count = items.length,
                label,
                name,
                i;

            for (i = 0; i < count; i++) {
                name = items[i].name;
                    label = new Text(name, legend.options.labels);

                legend.append(label);
            }
        },

        reflow: function(targetBox) {
            var legend = this,
                options = legend.options,
                childrenCount = legend.children.length;

            if (childrenCount === 0) {
                legend.box = targetBox.clone();
                return;
            }

            if (options.position == "custom") {
                legend.customLayout(targetBox);
                return;
            }

            if (options.position == TOP || options.position == BOTTOM) {
                legend.horizontalLayout(targetBox);
            } else {
                legend.verticalLayout(targetBox);
            }
        },

        getViewElements: function(view) {
            var legend = this,
                children = legend.children,
                options = legend.options,
                items = options.items,
                count = items.length,
                markerSize = legend.markerSize(),
                group = view.createGroup({ zIndex: options.zIndex }),
                border = options.border || {},
                markerBox,
                labelBox,
                color,
                label,
                box,
                i;

            append(group.children, ChartElement.fn.getViewElements.call(legend, view));

            for (i = 0; i < count; i++) {
                color = items[i].color;
                label = children[i];
                markerBox = new Box2D();
                    box = label.box;

                labelBox = labelBox ? labelBox.wrap(box) : box.clone();

                markerBox.x1 = box.x1 - markerSize * 2;
                markerBox.x2 = markerBox.x1 + markerSize;

                if (options.position == TOP || options.position == BOTTOM) {
                    markerBox.y1 = box.y1 + markerSize / 2;
                } else {
                    markerBox.y1 = box.y1 + (box.height() - markerSize) / 2;
                }

                markerBox.y2 = markerBox.y1 + markerSize;

                group.children.push(view.createRect(markerBox, { fill: color, stroke: color }));
            }

            if (children.length > 0) {
                var padding = getSpacing(options.padding);
                padding.left += markerSize * 2;
                labelBox.pad(padding);
                group.children.unshift(view.createRect(labelBox, {
                    stroke: border.width ? border.color : "",
                    strokeWidth: border.width,
                    dashType: border.dashType,
                    fill: options.background })
                );
            }

            return [ group ];
        },

        verticalLayout: function(targetBox) {
            var legend = this,
                options = legend.options,
                children = legend.children,
                childrenCount = children.length,
                labelBox = children[0].box.clone(),
                offsetX,
                offsetY,
                margin = getSpacing(options.margin),
                markerSpace = legend.markerSize() * 2;

            // Position labels below each other
            for (var i = 1; i < childrenCount; i++) {
                var label = legend.children[i];
                label.box.alignTo(legend.children[i - 1].box, BOTTOM);
                labelBox.wrap(label.box);
            }

            // Vertical center is calculated relative to the container, not the parent!
            if (options.position == LEFT) {
                offsetX = targetBox.x1 + markerSpace + margin.left;
                offsetY = (targetBox.y2 - labelBox.height()) / 2;
                labelBox.x2 += markerSpace + margin.left + margin.right;
            } else {
                offsetX = targetBox.x2 - labelBox.width() - margin.right;
                offsetY = (targetBox.y2 - labelBox.height()) / 2;
                labelBox.translate(offsetX, offsetY);
                labelBox.x1 -= markerSpace + margin.left;
            }

            legend.translateChildren(offsetX + options.offsetX,
                    offsetY + options.offsetY);

            var labelBoxWidth = labelBox.width();
            labelBox.x1 = math.max(targetBox.x1, labelBox.x1);
            labelBox.x2 = labelBox.x1 + labelBoxWidth;

            labelBox.y1 = targetBox.y1;
            labelBox.y2 = targetBox.y2;

            legend.box = labelBox;
        },

        horizontalLayout: function(targetBox) {
            var legend = this,
                options = legend.options,
                children = legend.children,
                childrenCount = children.length,
                box = children[0].box.clone(),
                markerWidth = legend.markerSize() * 3,
                offsetX,
                offsetY,
                margin = getSpacing(options.margin),
                boxWidth = children[0].box.width() + markerWidth,
                plotAreaWidth = targetBox.width(),
                label,
                labelY = 0,
                i;

            // Position labels next to each other
            for (i = 1; i < childrenCount; i++) {
                label = children[i];

                boxWidth += label.box.width() + markerWidth;
                if (boxWidth > plotAreaWidth - markerWidth) {
                    label.box = new Box2D(box.x1, box.y2,
                        box.x1 + label.box.width(), box.y2 + label.box.height());
                    boxWidth = label.box.width() + markerWidth;
                    labelY = label.box.y1;
                } else {
                    label.box.alignTo(children[i - 1].box, RIGHT);
                    label.box.y2 = labelY + label.box.height();
                    label.box.y1 = labelY;
                    label.box.translate(markerWidth, 0);
                }
                box.wrap(label.box);
            }

            offsetX = (targetBox.width() - box.width() + markerWidth) / 2;
            if (options.position === TOP) {
                offsetY = targetBox.y1 + margin.top;
                box.y2 = targetBox.y1 + box.height() + margin.top + margin.bottom;
                box.y1 = targetBox.y1;
            } else {
                offsetY = targetBox.y2 - box.height() - margin.bottom;
                box.y1 = targetBox.y2 - box.height() - margin.top - margin.bottom;
                box.y2 = targetBox.y2;
            }

            legend.translateChildren(offsetX + options.offsetX,
                    offsetY + options.offsetY);

            box.x1 = targetBox.x1;
            box.x2 = targetBox.x2;

            legend.box = box;
        },

        customLayout: function (targetBox) {
            var legend = this,
                options = legend.options,
                children = legend.children,
                childrenCount = children.length,
                labelBox = children[0].box.clone(),
                markerWidth = legend.markerSize() * 2,
                i;

            // Position labels next to each other
            for (i = 1; i < childrenCount; i++) {
                labelBox = legend.children[i].box;
                labelBox.alignTo(legend.children[i - 1].box, BOTTOM);
                labelBox.wrap(labelBox);
            }

            legend.translateChildren(options.offsetX + markerWidth, options.offsetY);

            legend.box = targetBox;
        },

        markerSize: function() {
            var legend = this,
                children = legend.children;

            if (children.length > 0) {
                return children[0].box.height() / 2;
            } else {
                return 0;
            }
        }
    });

    var Axis = ChartElement.extend({
        init: function(options) {
            var axis = this;

            ChartElement.fn.init.call(axis, options);
        },

        options: {
            labels: {
                rotation: 0
            },
            line: {
                width: 1,
                color: BLACK
            },
            majorTickType: OUTSIDE,
            majorTickSize: 4,
            minorTickType: NONE,
            minorTickSize: 3,
            axisCrossingValue: 0,
            minorGridLines: {
                visible: false,
                width: 1,
                color: BLACK
            },
            margin: 5
        },

        renderTicks: function(view) {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                box = axis.box,
                majorTicks = axis.getMajorTickPositions(),
                ticks = [];

            if (options.majorTickType.toLowerCase() === OUTSIDE) {
                ticks = ticks.concat(map(majorTicks, function(pos) {
                    return {
                        pos: pos,
                        size: options.majorTickSize,
                        width: options.line.width,
                        color: options.line.color
                    };
                }));
            }

            if (options.minorTickType.toLowerCase()  === OUTSIDE) {
                ticks = ticks.concat(map(axis.getMinorTickPositions(), function(pos) {
                    if (options.majorTickType.toLowerCase() !== NONE) {
                        if (!inArray(pos, majorTicks)) {
                            return {
                                pos: pos,
                                size: options.minorTickSize,
                                width: options.line.width,
                                color: options.line.color
                            };
                        }
                    } else {
                        return {
                            pos: pos,
                            size: options.minorTickSize,
                            width: options.line.width,
                            color: options.line.color
                        };
                    }
                }));
            }

            return map(ticks, function(tick) {
                if (isVertical) {
                    return view.createLine(
                        box.x2 - tick.size, tick.pos, box.x2, tick.pos,
                        {
                            strokeWidth: tick.width,
                            stroke: tick.color
                        }
                    );
                } else {
                    return view.createLine(
                        tick.pos, box.y1, tick.pos, box.y1 + tick.size,
                        {
                            strokeWidth: tick.width,
                            stroke: tick.color
                        }
                    );
                }
            });
        },

        getActualTickSize: function () {
            var axis = this,
                options = axis.options,
                tickSize = 0;

            if (options.majorTickType != NONE && options.minorTickType != NONE ) {
                tickSize = math.max(options.majorTickSize, options.minorTickSize);
            } else if (options.majorTickType != NONE) {
                tickSize = options.majorTickSize;
            } else if (options.minorTickType != NONE) {
                tickSize = options.minorTickSize;
            }

            return tickSize;
        },

        arrangeLabels: function(maxLabelWidth, maxLabelHeight, positions) {
            var axis = this,
                options = axis.options,
                isVertical = axis.options.orientation === VERTICAL,
                children = axis.children,
                tickPositions = axis.getMajorTickPositions(),
                tickSize = axis.getActualTickSize(),
                labelBox,
                labelY,
                i;

            for (i = 0; i < children.length; i++) {
                var label = children[i],
                    tickIx = isVertical ? (children.length - 1 - i) : i,
                    labelSize = isVertical ? label.box.height() : label.box.width(),
                    labelPos = tickPositions[tickIx] - (labelSize / 2),
                    firstTickPosition,
                    nextTickPosition,
                    middle,
                    labelX;

                if (isVertical) {
                    if (positions == ON_MINOR_TICKS) {
                        firstTickPosition = tickPositions[i];
                        nextTickPosition = tickPositions[i + 1];

                        middle = firstTickPosition + (nextTickPosition - firstTickPosition) / 2;
                        labelPos = middle - (labelSize / 2);
                    }
                    labelX = axis.box.x2 - options.margin - tickSize;

                    labelBox = new Box2D(labelX - label.box.width(), labelPos,
                                         labelX, labelPos)
                } else {
                    if (positions == ON_MINOR_TICKS) {
                        firstTickPosition = tickPositions[i];
                        nextTickPosition = tickPositions[i + 1];
                    } else {
                        firstTickPosition = labelPos;
                        nextTickPosition = labelPos + labelSize;
                    }
                    labelY = axis.box.y1 + tickSize + options.margin;

                    labelBox = new Box2D(firstTickPosition, labelY,
                                         nextTickPosition, labelY);
                }

                label.reflow(labelBox);
            }
        }
    });

    var NumericAxis = Axis.extend({
        init: function(seriesMin, seriesMax, options) {
            var axis = this,
                defaultOptions = axis.initDefaults(seriesMin, seriesMax, options),
                labelTemplate,
                text,
                i;

            Axis.fn.init.call(axis, defaultOptions);
            options = axis.options;

            var majorDivisions = axis.getDivisions(options.majorUnit),
                currentValue = options.min,
                align = options.orientation === VERTICAL ? RIGHT : CENTER,
                labelOptions = deepExtend({ }, options.labels, {
                    align: align, zIndex: options.zIndex
                }),
                labelText;

            for (i = 0; i < majorDivisions; i++) {
                if (labelOptions.template) {
                    labelTemplate = baseTemplate(labelOptions.template);
                    labelText = labelTemplate({ value: currentValue });
                }

                text = new TextBox(labelText || currentValue, labelOptions);

                axis.append(text);

                currentValue = round(currentValue + options.majorUnit, DEFAULT_PRECISION);
            }
        },

        options: {
            min: 0,
            max: 1,
            orientation: VERTICAL,
            majorGridLines: {
                visible: true,
                width: 1,
                color: BLACK
            },
            zIndex: 1
        },

        initDefaults: function(seriesMin, seriesMax, options) {
            var axis = this,
                autoMin = axis.autoAxisMin(seriesMin, seriesMax),
                autoMax = axis.autoAxisMax(seriesMin, seriesMax),
                autoMajorUnit = axis.autoMajorUnit(autoMin, autoMax),
                autoOptions = {
                    min: autoMin,
                    max: autoMax,
                    majorUnit: autoMajorUnit
                },
                userSetLimits;

            autoOptions.min = floor(autoMin * NUM_AXIS_PADDING, autoMajorUnit);
            autoOptions.max = ceil(autoMax * NUM_AXIS_PADDING, autoMajorUnit);

            if (options) {
                userSetLimits = defined(options.min) || defined(options.max);
                if (userSetLimits) {
                    if (options.min === options.max) {
                        if (options.min > 0) {
                            options.min = 0;
                        } else {
                            options.max = 1;
                        }
                    }
                }

                if (options.majorUnit) {
                    autoOptions.min = floor(autoOptions.min, options.majorUnit);
                    autoOptions.max = ceil(autoOptions.max, options.majorUnit);
                } else if (userSetLimits) {
                    options = deepExtend(autoOptions, options);

                    // Determine an auto major unit after min/max have been set
                    autoOptions.majorUnit = axis.autoMajorUnit(options.min, options.max);
                }
            }

            return deepExtend(autoOptions, options);
        },

        reflow: function(targetBox) {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                children = axis.children,
                space = axis.getActualTickSize() + options.margin,
                maxLabelWidth = 0,
                maxLabelHeight = 0,
                count = children.length,
                label,
                i;

            for (i = 0; i < count; i++) {
                label = children[i];
                maxLabelWidth = math.max(maxLabelWidth, label.box.width());
                maxLabelHeight = math.max(maxLabelHeight, label.box.height());
            }

            if (isVertical) {
                axis.box = new Box2D(
                    targetBox.x1, targetBox.y1,
                    targetBox.x1 + maxLabelWidth + space, targetBox.y2
                );
            } else {
                axis.box = new Box2D(
                    targetBox.x1, targetBox.y1,
                    targetBox.x2, targetBox.y1 + maxLabelHeight + space
                );
            }

            axis.arrangeLabels(maxLabelWidth, maxLabelHeight);
        },

        getViewElements: function(view) {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                childElements = ChartElement.fn.getViewElements.call(axis, view),
                tickPositions = axis.getMinorTickPositions(),
                lineOptions;

            if (options.line.width > 0) {
                lineOptions = {
                        strokeWidth: options.line.width,
                        stroke: options.line.color,
                        dashType: options.line.dashType,
                        zIndex: options.zIndex
                    };
                if (isVertical) {
                    childElements.push(view.createLine(
                        axis.box.x2, tickPositions[0],
                        axis.box.x2, tickPositions[tickPositions.length - 1],
                        lineOptions));
                } else {
                    childElements.push(view.createLine(
                        tickPositions[0], axis.box.y1,
                        tickPositions[tickPositions.length - 1], axis.box.y1,
                        lineOptions));
                }

                append(childElements, axis.renderTicks(view));
            }

            return childElements;
        },

        autoMajorUnit: function (min, max) {
            var diff = max - min;

            if (diff == 0) {
                if (max == 0) {
                    return 0.1;
                }

                diff = math.abs(max);
            }

            var scale = math.pow(10, math.floor(math.log(diff) / math.log(10))),
                relativeValue = round((diff / scale), DEFAULT_PRECISION),
                scaleMultiplier = 1;

            if (relativeValue < 1.904762) {
                scaleMultiplier = 0.2;
            } else if (relativeValue < 4.761904) {
                scaleMultiplier = 0.5;
            } else if (relativeValue < 9.523809) {
                scaleMultiplier = 1;
            } else {
                scaleMultiplier = 2;
            }

            return round(scale * scaleMultiplier, DEFAULT_PRECISION);
        },

        autoAxisMax: function(min, max) {
            if (min == 0 && max == 0) {
                return 1;
            }

            var axisMax;
            if (min <= 0 && max <= 0) {
                max = min == max ? 0 : max;

                var diff = math.abs((max - min) / max);
                if(diff > ZERO_THRESHOLD) {
                    return 0;
                }

                axisMax = max - ((min - max) / 2);
            } else {
                min = min == max ? 0 : min;
                axisMax = max;
            }

            return axisMax;
        },

        autoAxisMin: function(min, max) {
            if (min == 0 && max == 0) {
                return 0;
            }

            var axisMin;
            if (min >= 0 && max >= 0) {
                min = min == max ? 0 : min;

                var diff = (max - min) / max;
                if(diff > ZERO_THRESHOLD) {
                    return 0;
                }

                axisMin = min - ((max - min) / 2);
            } else {
                max = min == max ? 0 : max;
                axisMin = min;
            }

            return axisMin;
        },

        getDivisions: function(stepValue) {
            var options = this.options,
                range = options.max - options.min;

            return math.floor(round(range / stepValue, COORD_PRECISION)) + 1;
        },

        getTickPositions: function(stepValue) {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                lineBox = axis.getAxisLineBox(),
                lineSize = isVertical ? lineBox.height() : lineBox.width(),
                range = options.max - options.min,
                scale = lineSize / range,
                step = stepValue * scale,
                divisions = axis.getDivisions(stepValue),
                pos = lineBox[isVertical ? "y2" : "x1"],
                multuplier = isVertical ? -1 : 1,
                positions = [],
                i;

            for (i = 0; i < divisions; i++) {
                positions.push(round(pos, COORD_PRECISION));
                pos = pos + step * multuplier;
            }

            return isVertical ? positions.reverse() : positions;
        },

        getMajorTickPositions: function() {
            var axis = this;

            return axis.getTickPositions(axis.options.majorUnit);
        },

        getMinorTickPositions: function() {
            var axis = this;

            return axis.getTickPositions(axis.options.majorUnit / 5);
        },

        getAxisLineBox: function() {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                labelSize = isVertical ? "height" : "width",
                children = axis.children,
                box = axis.box,
                startMargin = 0,
                endMargin = 0;

            if (children.length > 1) {
                startMargin = children[0].box[labelSize]() / 2;
                endMargin = children[children.length - 1].box[labelSize]() / 2;
            }

            if (isVertical) {
               return new Box2D(box.x2, box.y1 + startMargin,
                 box.x2, box.y2 - endMargin);
            } else {
               return new Box2D(box.x1 + startMargin, box.y1,
                 box.x2 - endMargin, box.y1);
            }
        },

        getSlot: function(a, b) {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                valueAxis = isVertical ? Y : X,
                lineBox = axis.getAxisLineBox(),
                lineStart = lineBox[valueAxis + 1],
                lineSize = isVertical ? lineBox.height() : lineBox.width(),
                scale = lineSize / (options.max - options.min),
                a = defined(a) ? a : options.axisCrossingValue,
                b = defined(b) ? b : options.axisCrossingValue,
                a = math.max(math.min(a, options.max), options.min),
                b = math.max(math.min(b, options.max), options.min),
                p1,
                p2,
                slotBox = new Box2D(lineBox.x1, lineBox.y1, lineBox.x1, lineBox.y1);

            if (isVertical) {
                p1 = lineStart + scale * (options.max - math.max(a, b));
                p2 = lineStart + scale * (options.max - math.min(a, b));
            } else {
                p1 = lineStart + scale * (math.min(a, b) - options.min);
                p2 = lineStart + scale * (math.max(a, b) - options.min);
            }

            slotBox[valueAxis + 1] = p1;
            slotBox[valueAxis + 2] = p2;

            return slotBox;
        }
    });

    var CategoryAxis = Axis.extend({
        init: function(options) {
            var axis = this;
            Axis.fn.init.call(axis, options);

            var options = axis.options,
                align = options.orientation === VERTICAL ? RIGHT : CENTER,
                labelOptions = deepExtend({ }, options.labels,
                    { align: align, zIndex: options.zIndex }
                ),
                labelTemplate,
                count = options.categories.length,
                content,
                i;

            for (i = 0; i < count; i++) {
                content = defined(options.categories[i]) ? options.categories[i] : "";

                if (labelOptions.template) {
                    labelTemplate = baseTemplate(labelOptions.template);
                    content = labelTemplate({ value: content });
                }

                axis.append(new TextBox(content, labelOptions));
            }
        },

        options: {
            categories: [],
            orientation: HORIZONTAL,
            majorGridLines: {
                visible: false,
                width: 1,
                color: BLACK
            },
            zIndex: 1
        },

        reflow: function(targetBox) {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                children = axis.children,
                space = axis.getActualTickSize() + options.margin,
                maxLabelHeight = 0,
                maxLabelWidth = 0,
                label,
                i;

            for (i = 0; i < children.length; i++) {
                label = children[i];
                maxLabelHeight = math.max(maxLabelHeight, label.box.height());
                maxLabelWidth = math.max(maxLabelWidth, label.box.width());
            }

            if (isVertical) {
                axis.box = new Box2D(
                    targetBox.x1, targetBox.y1,
                    targetBox.x1 + maxLabelWidth + space, targetBox.y2
                );
            } else {
                axis.box = new Box2D(
                    targetBox.x1, targetBox.y1,
                    targetBox.x2, targetBox.y1 + maxLabelHeight + space
                );
            }

            axis.arrangeLabels(maxLabelWidth, maxLabelHeight, ON_MINOR_TICKS);
        },

        getViewElements: function(view) {
            var axis = this,
                options = axis.options,
                line = options.line,
                isVertical = options.orientation === VERTICAL,
                childElements = ChartElement.fn.getViewElements.call(axis, view),
                lineOptions;

            if (line.width > 0) {
                lineOptions = {
                    strokeWidth: line.width,
                    stroke: line.color,
                    dashType: line.dashType,
                    zIndex: line.zIndex
                };

                if (isVertical) {
                    childElements.push(view.createLine(
                        axis.box.x2, axis.box.y1, axis.box.x2, axis.box.y2,
                        lineOptions));
                } else {
                    childElements.push(view.createLine(
                        axis.box.x1, axis.box.y1, axis.box.x2, axis.box.y1,
                        lineOptions));
                }

                append(childElements, axis.renderTicks(view));
            }

            return childElements;
        },

        getTickPositions: function(itemsCount) {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                size = isVertical ? axis.box.height() : axis.box.width(),
                step = size / itemsCount,
                pos = isVertical ? axis.box.y1 : axis.box.x1,
                positions = [],
                i;

            for (i = 0; i < itemsCount; i++) {
                positions.push(round(pos, COORD_PRECISION));
                pos += step;
            }

            positions.push(isVertical ? axis.box.y2 : axis.box.x2);

            return positions;
        },

        getMajorTickPositions: function() {
            var axis = this;

            return axis.getTickPositions(axis.options.categories.length);
        },

        getMinorTickPositions: function() {
            var axis = this;

            return axis.getTickPositions(axis.options.categories.length * 2);
        },

        getSlot: function(categoryIx) {
            var axis = this,
                options = axis.options,
                isVertical = options.orientation === VERTICAL,
                children = axis.children,
                box = axis.box,
                size = isVertical ? box.height() : box.width(),
                startPos = isVertical ? box.y1 : box.x1,
                step = size / math.max(1, children.length),
                p1 = startPos + (categoryIx * step),
                p2 = p1 + step;

            return isVertical ?
                   new Box2D(box.x2, p1, box.x2, p2) :
                   new Box2D(p1, box.y1, p2, box.y1);
        },

        getAxisLineBox: function() {
            var axis = this,
                options = axis.options;

            return axis.getSlot(0).wrap(axis.getSlot(options.categories.length - 1));
        }
    });

    var ClusterLayout = ChartElement.extend({
        init: function(options) {
            var cluster = this;
            ChartElement.fn.init.call(cluster, options);
        },

        options: {
            isVertical: false,
            gap: 0,
            spacing: 0
        },

        reflow: function(box) {
            var cluster = this,
                options = cluster.options,
                isVertical = options.isVertical,
                axis = isVertical ? Y : X,
                children = cluster.children,
                gap = options.gap,
                spacing = options.spacing,
                count = children.length,
                slots = count + gap + (spacing * (count - 1)),
                slotSize = (isVertical ? box.height() : box.width()) / slots,
                position = box[axis + 1] + slotSize * (gap / 2),
                childBox,
                i;

            for (i = 0; i < count; i++) {
                childBox = (children[i].box || box).clone();

                childBox[axis + 1] = position;
                childBox[axis + 2] = position + slotSize;

                children[i].reflow(childBox);
                if (i < count - 1) {
                    position += (slotSize * spacing);
                }

                position += slotSize;
            }
        }
    });

    var StackLayout = ChartElement.extend({
        init: function(options) {
            var stack = this;
            ChartElement.fn.init.call(stack, options);
        },

        options: {
            isVertical: true,
            isReversed: false
        },

        reflow: function(targetBox) {
            var stack = this,
                options = stack.options,
                isVertical = options.isVertical,
                positionAxis = isVertical ? X : Y,
                stackAxis = isVertical ? Y : X,
                stackBase = targetBox[stackAxis + 2],
                children = stack.children,
                box = stack.box = new Box2D(),
                childrenCount = children.length,
                stackDirection,
                i;

            if (options.isReversed) {
                stackDirection = isVertical ? BOTTOM : LEFT;
            } else {
                stackDirection = isVertical ? TOP : RIGHT;
            }

            for (i = 0; i < childrenCount; i++) {
                var currentChild = children[i],
                    childBox = currentChild.box.clone();

                childBox.snapTo(targetBox, positionAxis)
                if (currentChild.options) {
                    currentChild.options.stackBase = stackBase;
                }

                if (i == 0) {
                    box = stack.box = childBox.clone();
                } else {
                    childBox.alignTo(children[i - 1].box, stackDirection);
                }

                currentChild.reflow(childBox);

                box.wrap(childBox);
            }
        }
    });

    var Bar = ChartElement.extend({
        init: function(value, options) {
            var bar = this;

            bar.value = value;
            bar.options.id = uniqueId();

            ChartElement.fn.init.call(bar, options);
        },

        options: {
            color: WHITE,
            border: {
                width: 1
            },
            isVertical: true,
            overlay: {
                gradient: GLASS
            },
            aboveAxis: true,
            labels: {
                visible: false
            },
            animation: {
                type: BAR
            },
            opacity: 1
        },

        render: function() {
            var bar = this,
                value = bar.value,
                options = bar.options,
                labels = options.labels,
                labelText = value,
                labelTemplate;

            if (bar._rendered) {
                return;
            } else {
                bar._rendered = true;
            }

            if (labels.visible && value) {
                if (labels.template) {
                    labelTemplate = baseTemplate(labels.template);
                    labelText = labelTemplate({
                        dataItem: bar.dataItem,
                        category: bar.category,
                        value: bar.value,
                        series: bar.series
                    });
                }

                bar.append(
                    new BarLabel(labelText, deepExtend({
                            isVertical: options.isVertical,
                            id: uniqueId()},
                        options.labels)
                    )
                );
            }
        },

        reflow: function(targetBox) {
            this.render();

            var bar = this,
                options = bar.options,
                children = bar.children,
                label = children[0];

            bar.box = targetBox;

            if (label) {
                label.options.aboveAxis = options.aboveAxis;
                label.reflow(targetBox);
            }
        },

        getViewElements: function(view) {
            var bar = this,
                options = bar.options,
                isVertical = options.isVertical,
                normalAngle = isVertical ? 0 : 90,
                border = options.border.width > 0 ? {
                    stroke: bar.getBorderColor(),
                    strokeWidth: options.border.width,
                    dashType: options.border.dashType
                } : {},
                box = bar.box,
                rectStyle = deepExtend({
                    id: options.id,
                    fill: options.color,
                    normalAngle: normalAngle,
                    aboveAxis: options.aboveAxis,
                    fillOpacity: options.opacity,
                    strokeOpacity: options.opacity,
                    stackBase: options.stackBase,
                    animation: options.animation
                }, border),
                elements = [],
                label = bar.children[0];

            if (options.overlay) {
                rectStyle.overlay = deepExtend({rotation: normalAngle }, options.overlay);
            }

            elements.push(view.createRect(box, rectStyle));

            append(elements,
                ChartElement.fn.getViewElements.call(bar, view));

            bar.registerId(options.id);
            if (label) {
                bar.registerId(label.options.id);
            }

            return elements;
        },

        getOutlineElement: function(view, options){
            var bar = this,
                box = bar.box,
                outlineId = bar.options.id + OUTLINE_SUFFIX;

            bar.registerId(outlineId);
            options = deepExtend({}, options, { id: outlineId });

            return view.createRect(box, options);
        },

        getBorderColor: function() {
            var bar = this,
                options = bar.options,
                color = options.color,
                borderColor = options.border.color;

            if (!defined(borderColor)) {
                borderColor =
                    new Color(color).brightness(BAR_BORDER_BRIGHTNESS).toHex();
            }

            return borderColor;
        },

        tooltipAnchor: function(tooltipWidth, tooltipHeight) {
            var bar = this,
                options = bar.options,
                box = bar.box,
                isVertical = options.isVertical,
                aboveAxis = options.aboveAxis,
                x,
                y;

            if (isVertical) {
                x = box.x2 + TOOLTIP_OFFSET;
                y = aboveAxis ? box.y1 : box.y2 - tooltipHeight;
            } else {
                if (options.isStacked) {
                    x = box.x2 - tooltipWidth;
                    y = box.y1 - tooltipHeight - TOOLTIP_OFFSET;
                } else {
                    x = box.x2 + TOOLTIP_OFFSET;
                    y = box.y1;
                }
            }

            return new Point2D(x, y);
        },

        formatPointValue: function(format) {
            var point = this;

            return point.owner.formatPointValue(point.value, format);
        }
    });

    var CategoricalChart = ChartElement.extend({
        init: function(plotArea, options) {
            var chart = this;

            ChartElement.fn.init.call(chart, options);

            chart.plotArea = plotArea;
            chart._seriesMin = Number.MAX_VALUE;
            chart._seriesMax = - Number.MAX_VALUE;

            chart.points = [];
            chart.categoryPoints = [];
            chart.seriesPoints = [];

            chart.render();
        },

        options: {
            series: [],
            isVertical: true,
            isStacked: false
        },

        render: function() {
            var chart = this;

            chart.traverseDataPoints(proxy(chart.addValue, chart));
        },

        addValue: function(value, category, categoryIx, series, seriesIx) {
            var chart = this,
                point,
                categoryPoints = chart.categoryPoints[categoryIx],
                seriesPoints = chart.seriesPoints[seriesIx];

            if (!categoryPoints) {
                chart.categoryPoints[categoryIx] = categoryPoints = [];
            }

            if (!seriesPoints) {
                chart.seriesPoints[seriesIx] = seriesPoints = [];
            }

            chart.updateRange(value, categoryIx);

            point = chart.createPoint(value, category, categoryIx, series, seriesIx);
            if (point) {
                point.category = category;
                point.series = series;
                point.seriesIx = seriesIx;
                point.owner = chart;
                point.dataItem = series.dataItems ?
                    series.dataItems[categoryIx] : { value: value };
            }

            chart.points.push(point);
            seriesPoints.push(point);
            categoryPoints.push(point);
        },

        updateRange: function(value, categoryIx) {
            var chart = this;

            if (defined(value)) {
                chart._seriesMin = math.min(chart._seriesMin, value);
                chart._seriesMax = math.max(chart._seriesMax, value);
            }
        },

        valueRange: function() {
            var chart = this;

            if (chart.points.length) {
                return { min: chart._seriesMin, max: chart._seriesMax };
            }

            return null;
        },

        reflow: function(targetBox) {
            var chart = this,
                options = chart.options,
                isVertical = options.isVertical,
                plotArea = chart.plotArea,
                pointIx = 0,
                categorySlots = chart.categorySlots = [],
                chartPoints = chart.points,
                valueAxis = isVertical ? plotArea.axisY : plotArea.axisX,
                axisCrossingValue = valueAxis.options.axisCrossingValue,
                point;

            chart.traverseDataPoints(function(value, category, categoryIx) {
                point = chartPoints[pointIx++];
                if (point && point.plotValue) {
                    value = point.plotValue;
                }

                var slotX = plotArea.axisX.getSlot(isVertical ? categoryIx : value),
                    slotY = plotArea.axisY.getSlot(isVertical ? value : categoryIx),
                    pointSlot = new Box2D(slotX.x1, slotY.y1, slotX.x2, slotY.y2),
                    aboveAxis = value >= axisCrossingValue;

                if (point) {
                    point.options.aboveAxis = aboveAxis;
                    point.reflow(pointSlot);
                }

                if(!categorySlots[categoryIx]) {
                    categorySlots[categoryIx] = isVertical ? slotX : slotY;
                }
            });

            chart.reflowCategories(categorySlots);

            chart.box = targetBox;
        },

        reflowCategories: function() { },

        traverseDataPoints: function(callback) {
            var chart = this,
            options = chart.options,
            series = options.series,
            categories = chart.plotArea.options.categoryAxis.categories || [],
            count = categoriesCount(series),
            categoryIx,
            seriesIx,
            value,
            currentCategory,
            currentSeries;

            for (categoryIx = 0; categoryIx < count; categoryIx++) {
                for (seriesIx = 0; seriesIx < series.length; seriesIx++) {
                    currentCategory = categories[categoryIx];
                    currentSeries = series[seriesIx];
                    value = currentSeries.data[categoryIx];
                    callback(value, currentCategory, categoryIx, currentSeries, seriesIx);
                }
            }
        },

        formatPointValue: function(value, tooltipFormat) {
            return format(tooltipFormat, value);
        }
    });

    var BarChart = CategoricalChart.extend({
        init: function(plotArea, options) {
            var chart = this;

            chart._categoryTotalsPos = [];
            chart._categoryTotalsNeg = [];

            CategoricalChart.fn.init.call(chart, plotArea, options);
        },

        createPoint: function(value, category, categoryIx, series, seriesIx) {
            var barChart = this,
                options = barChart.options,
                children = barChart.children,
                isStacked = barChart.options.isStacked,
                labelOptions = deepExtend({}, series.labels);

            if (isStacked) {
                if (labelOptions.position == OUTSIDE_END) {
                    labelOptions.position = INSIDE_END;
                }
            }

            var bar = new Bar(value,
                deepExtend({}, {
                    isVertical: options.isVertical,
                    overlay: series.overlay,
                    labels: labelOptions,
                    isStacked: isStacked
                }, series));

            var cluster = children[categoryIx];
            if (!cluster) {
                cluster = new ClusterLayout({
                    isVertical: !options.isVertical,
                    gap: options.gap,
                    spacing: options.spacing
                });
                barChart.append(cluster);
            }

            if (isStacked) {
                var stackWrap = cluster.children[0],
                    positiveStack,
                    negativeStack;

                if (!stackWrap) {
                    stackWrap = new ChartElement();
                    cluster.append(stackWrap);

                    positiveStack = new StackLayout({
                        isVertical: options.isVertical
                    });
                    negativeStack = new StackLayout({
                        isVertical: options.isVertical,
                        isReversed: true
                    });
                    stackWrap.append(positiveStack, negativeStack);
                } else {
                    positiveStack = stackWrap.children[0];
                    negativeStack = stackWrap.children[1];
                }

                if (value > 0) {
                    positiveStack.append(bar);
                } else {
                    negativeStack.append(bar);
                }
            } else {
                cluster.append(bar);
            }

            return bar;
        },

        updateRange: function(value, categoryIx) {
            var chart = this,
                options = chart.options,
                isStacked = options.isStacked,
                totalsPos = chart._categoryTotalsPos,
                totalsNeg = chart._categoryTotalsNeg;

            if (defined(value)) {
                if (isStacked) {
                    incrementSlot(value > 0 ? totalsPos : totalsNeg, categoryIx, value);
                } else {
                    CategoricalChart.fn.updateRange.apply(chart, arguments);
                }
            }
        },

        valueRange: function() {
            var chart = this,
                options = chart.options,
                isStacked = options.isStacked,
                totalsPos = chart._categoryTotalsPos,
                totalsNeg = chart._categoryTotalsNeg;

            if (isStacked) {
                chart._seriesMin = sparseArrayMin(totalsNeg.concat(0));
                chart._seriesMax = sparseArrayMax(totalsPos.concat(0));
            }

            return CategoricalChart.fn.valueRange.call(chart);
        },

        reflowCategories: function(categorySlots) {
            var chart = this,
                children = chart.children,
                childrenLength = children.length,
                i;

            for (i = 0; i < childrenLength; i++) {
                children[i].reflow(categorySlots[i]);
            }
        }
    });

    var ShapeElement = BoxElement.extend({
        init: function(options) {
            var marker = this;

            BoxElement.fn.init.call(marker, options);
        },

        options: {
            type: SQUARE,
            align: CENTER,
            vAlign: CENTER
        },

        getViewElements: function(view, renderOptions) {
            var marker = this,
                options = marker.options,
                type = options.type,
                box = marker.paddingBox,
                element = BoxElement.fn.getViewElements.call(marker, view, renderOptions)[0],
                halfWidth = box.width() / 2;

            if (!element) {
                return [];
            }

            if (type === TRIANGLE) {
                element = view.createPolyline([
                    new Point2D(box.x1 + halfWidth, box.y1),
                    new Point2D(box.x1, box.y2),
                    new Point2D(box.x2, box.y2)
                ], true, element.options);
            } else if (type === CIRCLE) {
                element = view.createCircle([
                    round(box.x1 + halfWidth, COORD_PRECISION),
                    round(box.y1 + box.height() / 2, COORD_PRECISION)
                ], halfWidth, element.options);
            }

            return [ element ];
        }
    });

    var LinePoint = ChartElement.extend({
        init: function(value, options) {
            var point = this;

            point.value = value;

            ViewElement.fn.init.call(point, options);
        },

        options: {
            aboveAxis: true,
            isVertical: true,
            markers: {
                visible: true,
                background: WHITE,
                size: LINE_MARKER_SIZE,
                type: CIRCLE,
                border: {
                    width: 2
                },
                opacity: 1
            },
            labels: {
                visible: false,
                position: ABOVE,
                margin: getSpacing(3),
                padding: getSpacing(4),
                animation: {
                    type: FADEIN,
                    delay: INITIAL_ANIMATION_DURATION
                }
            }
        },

        render: function() {
            var point = this,
                options = point.options,
                markers = options.markers,
                labels = options.labels,
                markerBackground = markers.background,
                markerBorder = deepExtend({}, markers.border),
                labelText = point.value;

            if (point._rendered) {
                return;
            } else {
                point._rendered = true;
            }

            if (!defined(markerBorder.color)) {
                markerBorder.color =
                    new Color(markerBackground).brightness(BAR_BORDER_BRIGHTNESS).toHex();
            }

            point.marker = new ShapeElement({
                id: uniqueId(),
                visible: markers.visible,
                type: markers.type,
                width: markers.size,
                height: markers.size,
                background: markerBackground,
                border: markerBorder,
                opacity: markers.opacity
            });

            point.append(point.marker);

            if (labels.visible) {
                if (labels.template) {
                    var labelTemplate = baseTemplate(labels.template);
                    labelText = labelTemplate({
                        dataItem: point.dataItem,
                        category: point.category,
                        value: point.value,
                        series: point.series
                    });
                } else if (labels.format) {
                    labelText = point.formatPointValue(labels.format);
                }
                point.label = new TextBox(labelText,
                    deepExtend({
                        id: uniqueId(),
                        align: CENTER,
                        vAlign: CENTER,
                        margin: {
                            left: 5,
                            right: 5
                        }
                    }, labels, { format: "" })
                );
                point.append(point.label);
            }
        },

        markerBox: function() {
            return this.marker.box;
        },

        reflow: function(targetBox) {
            var point = this,
                options = point.options,
                isVertical = options.isVertical,
                aboveAxis = options.aboveAxis,
                childBox;

            point.render();

            point.box = targetBox;
            childBox = targetBox.clone();

            if (isVertical) {
                if (aboveAxis) {
                    childBox.y1 -= childBox.height();
                } else {
                    childBox.y2 += childBox.height();
                }
            } else {
                if (aboveAxis) {
                    childBox.x1 += childBox.width();
                } else {
                    childBox.x2 -= childBox.width();
                }
            }

            point.marker.reflow(childBox);
            point.reflowLabel(childBox);
        },

        reflowLabel: function(box) {
            var point = this,
                options = point.options,
                marker = point.marker,
                label = point.label,
                edge = options.labels.position;

            if (label) {
                edge = edge === ABOVE ? TOP : edge;
                edge = edge === BELOW ? BOTTOM : edge;

                label.reflow(box);
                label.box.alignTo(marker.box, edge);
                label.reflow(label.box);
            }
        },

        getViewElements: function(view) {
            var element = this,
                marker = element.marker,
                label = element.label;

            element.registerId(marker.options.id);

            if (label) {
                element.registerId(label.options.id);
            }

            return ChartElement.fn.getViewElements.call(element, view);
        },

        getOutlineElement: function(view, options) {
            var element = this,
                marker = element.marker,
                outlineId = element.marker.options.id + OUTLINE_SUFFIX;

            element.registerId(outlineId);
            options = deepExtend({}, options, { id: outlineId });

            return marker.getViewElements(view, deepExtend(options, {
                fill: marker.options.border.color,
                fillOpacity: 1,
                strokeOpacity: 0
            }))[0];
        },

        tooltipAnchor: function(tooltipWidth, tooltipHeight) {
            var point = this,
                markerBox = point.marker.box,
                aboveAxis = point.options.aboveAxis;

            return new Point2D(
                markerBox.x2 + TOOLTIP_OFFSET,
                aboveAxis ? markerBox.y1 - tooltipHeight : markerBox.y2
            );
        },

        formatPointValue: function(format) {
            var point = this;

            return point.owner.formatPointValue(point.value, format);
        }
    });

    var LineChartMixin = {
        createLines: function(view) {
            var chart = this,
                options = chart.options,
                series = options.series,
                seriesPoints = chart.seriesPoints,
                currentSeries,
                seriesIx,
                seriesCount = seriesPoints.length,
                currentSeriesPoints,
                linePoints,
                point,
                pointCount,
                lines = [];

            for (seriesIx = 0; seriesIx < seriesCount; seriesIx++) {
                currentSeriesPoints = seriesPoints[seriesIx];
                pointCount = currentSeriesPoints.length;
                currentSeries = series[seriesIx];
                linePoints = [];

                for (pointIx = 0; pointIx < pointCount; pointIx++) {
                    point = currentSeriesPoints[pointIx];
                    if (point) {
                        pointCenter = point.markerBox().center();
                        linePoints.push(new Point2D(pointCenter.x, pointCenter.y));
                    } else if (currentSeries.missingValues !== INTERPOLATE) {
                        if (linePoints.length > 1) {
                            lines.push(
                                chart.createLine(uniqueId(), view, linePoints, currentSeries, seriesIx)
                            );
                        }
                        linePoints = [];
                    }
                }

                if (linePoints.length > 1) {
                    lines.push(
                        chart.createLine(uniqueId(), view, linePoints, currentSeries, seriesIx));
                }
            }

            return lines;
        },

        createLine: function(lineId, view, points, series, seriesIx) {
            this.registerId(lineId, { seriesIx: seriesIx });
            return view.createPolyline(points, false, {
                id: lineId,
                stroke: series.color,
                strokeWidth: series.width,
                strokeOpacity: series.opacity,
                fill: "",
                dashType: series.dashType
            });
        },

        getNearestPoint: function(x, y, seriesIx) {
            var chart = this,
                isVertical = chart.options.isVertical,
                axis = isVertical ? X : Y,
                pos = isVertical ? x : y,
                points = chart.seriesPoints[seriesIx],
                nearestPointDistance = Number.MAX_VALUE,
                pointsLength = points.length,
                currentPoint,
                pointBox,
                pointDistance,
                nearestPoint,
                i;

            for (i = 0; i < pointsLength; i++) {
                currentPoint = points[i];

                if (currentPoint && defined(currentPoint.value) && currentPoint.value !== null) {
                    pointBox = currentPoint.box;
                    pointDistance = math.abs(pointBox.center()[axis] - pos);

                    if (pointDistance < nearestPointDistance) {
                        nearestPoint = currentPoint;
                        nearestPointDistance = pointDistance;
                    }
                }
            }

            return nearestPoint;
        }
    };

    var LineChart = CategoricalChart.extend({
        init: function(plotArea, options) {
            var chart = this;

            chart._categoryTotals = [];

            CategoricalChart.fn.init.call(chart, plotArea, options);
        },

        createPoint: function(value, category, categoryIx, series, seriesIx) {
            var chart = this,
                options = chart.options,
                isStacked = options.isStacked,
                categoryPoints = chart.categoryPoints[categoryIx],
                stackPoint,
                plotValue = 0;

            if (!defined(value) || value === null) {
                if (isStacked || series.missingValues === ZERO) {
                    value = 0;
                } else {
                    return null;
                }
            }

            var point = new LinePoint(value,
                deepExtend({
                    isVertical: options.isVertical,
                    markers: {
                        border: {
                            color: series.color
                        },
                        opacity: series.opacity
                    }
                }, series)
            );

            if (isStacked) {
                stackPoint = categoryPoints[categoryPoints.length - 1];
                if (stackPoint) {
                    plotValue = stackPoint.plotValue;
                }

                point.plotValue = value + plotValue;
            }

            chart.append(point);

            return point;
        },

        updateRange: function(value, categoryIx) {
            var chart = this,
                options = chart.options,
                isStacked = options.isStacked,
                totals = chart._categoryTotals;

            if (defined(value)) {
                if (isStacked) {
                    incrementSlot(totals, categoryIx, value);
                    chart._seriesMin = math.min(chart._seriesMin, sparseArrayMin(totals));
                    chart._seriesMax = math.max(chart._seriesMax, sparseArrayMax(totals));
                } else {
                    CategoricalChart.fn.updateRange.apply(chart, arguments);
                }
            }
        },

        getViewElements: function(view) {
            var chart = this,
                elements = CategoricalChart.fn.getViewElements.call(chart, view),
                group = view.createGroup({
                    animation: {
                        type: CLIP
                    }
                }),
                lines = chart.createLines(view);


            group.children = lines.concat(elements);
            return [group];
        }
    });
    deepExtend(LineChart.fn, LineChartMixin);

    var ScatterChart = ChartElement.extend({
        init: function(plotArea, options) {
            var chart = this;

            ChartElement.fn.init.call(chart, options);

            chart.plotArea = plotArea;
            chart._seriesMin = [Number.MAX_VALUE, Number.MAX_VALUE];
            chart._seriesMax = [-Number.MAX_VALUE, -Number.MAX_VALUE];
            chart.points = [];
            chart.seriesPoints = [];

            chart.render();
        },

        options: {
            series: [],
            tooltip: {
                format: "{0}, {1}"
            },
            labels: {
                format: "{0}, {1}"
            }
        },

        render: function() {
            var chart = this;

            chart.traverseDataPoints(proxy(chart.addValue, chart));
        },

        addValue: function(value, fields) {
            var chart = this,
                point,
                seriesIx = fields.seriesIx,
                seriesPoints = chart.seriesPoints[seriesIx];

            chart.updateRange(value);

            if (!seriesPoints) {
                chart.seriesPoints[seriesIx] = seriesPoints = [];
            }

            point = chart.createPoint(value, fields.series, seriesIx);
            if (point) {
                extend(point, fields);
            }

            chart.points.push(point);
            seriesPoints.push(point);
        },

        updateRange: function(value) {
            var chart = this,
                x = value.x,
                y = value.y,
                seriesMin = chart._seriesMin,
                seriesMax = chart._seriesMax;

            if (defined(x)) {
                seriesMin[0] = math.min(seriesMin[0], x);
                seriesMax[0] = math.max(seriesMax[0], x);
            }

            if (defined(y)) {
                seriesMin[1] = math.min(seriesMin[1], y);
                seriesMax[1] = math.max(seriesMax[1], y);
            }
        },

        valueRange: function() {
            var chart = this;

            if (chart.points.length) {
                return { min: chart._seriesMin, max: chart._seriesMax };
            }

            return null;
        },

        createPoint: function(value, series, seriesIx) {
            var chart = this;

            if (!defined(value.x) || !defined(value.y)) {
                return null;
            }

            var point = new LinePoint(value,
                deepExtend({
                    markers: {
                        border: {
                            color: series.color
                        },
                        opacity: series.opacity
                    },
                    tooltip: {
                        format: chart.options.tooltip.format
                    },
                    labels: {
                        format: chart.options.labels.format
                    }
                }, series)
            );

            chart.append(point);

            return point;
        },

        reflow: function(targetBox) {
            var chart = this,
                plotArea = chart.plotArea,
                chartPoints = chart.points,
                pointIx = 0,
                point;

            chart.traverseDataPoints(function(value) {
                point = chartPoints[pointIx++];

                var slotX = plotArea.axisX.getSlot(value.x, value.x),
                    slotY = plotArea.axisY.getSlot(value.y, value.y),
                    pointSlot = new Box2D(slotX.x1, slotY.y1, slotX.x2, slotY.y2);

                if (point) {
                    point.reflow(pointSlot);
                }
            });

            chart.box = targetBox;
        },

        getViewElements: function(view) {
            var chart = this,
                elements = ChartElement.fn.getViewElements.call(chart, view),
                group = view.createGroup({
                    animation: {
                        type: CLIP
                    }
                });

            group.children = elements;
            return [group];
        },

        traverseDataPoints: function(callback) {
            var chart = this,
                options = chart.options,
                series = options.series,
                pointIx = 0,
                seriesIx,
                currentSeries,
                dataItems,
                value,
                pointData;

            for (seriesIx = 0; seriesIx < series.length; seriesIx++) {
                currentSeries = series[seriesIx];
                for (pointIx = 0; pointIx < currentSeries.data.length; pointIx++) {
                    pointData = currentSeries.data[pointIx] || [];
                    dataItems = currentSeries.dataItems;
                    value = { x: pointData[0], y: pointData[1] };

                    callback(value, {
                        pointIx: pointIx,
                        series: currentSeries,
                        seriesIx: seriesIx,
                        dataItem: dataItems ? dataItems[pointIx] : value,
                        owner: chart
                    });
                }
            }
        },

        formatPointValue: function(value, tooltipFormat) {
            return format(tooltipFormat, value.x, value.y);
        }
    });

    var ScatterLineChart = ScatterChart.extend({
        getViewElements: function(view) {
            var chart = this,
                elements = ScatterChart.fn.getViewElements.call(chart, view),
                group = view.createGroup({
                    animation: {
                        type: CLIP
                    }
                }),
                lines = chart.createLines(view);

            group.children = lines.concat(elements);
            return [group];
        }
    });
    deepExtend(ScatterLineChart.fn, LineChartMixin);

    var PieSegment = ChartElement.extend({
        init: function(value, sector, options) {
            var segment = this;

            segment.value = value;
            segment.sector = sector;

            ChartElement.fn.init.call(segment, options);
        },

        options: {
            color: WHITE,
            overlay: {
                gradient: ROUNDED_BEVEL
            },
            border: {
                width: 0.5
            },
            labels: {
                visible: false,
                distance: 35,
                font: DEFAULT_FONT,
                margin: getSpacing(0.5),
                align: CIRCLE,
                zIndex: 1,
                position: OUTSIDE_END
            },
            animation: {
                type: PIE
            },
            highlight: {
                visible: true,
                border: {
                    width: 1
                }
            }
        },

        render: function() {
            var segment = this,
                options = segment.options,
                labels = options.labels,
                labelText = segment.value,
                labelTemplate;

            if (segment._rendered) {
                return;
            } else {
                segment._rendered = true;
            }

            if (labels.template) {
                labelTemplate = baseTemplate(labels.template);
                labelText = labelTemplate({
                    dataItem: segment.dataItem,
                    category: segment.category,
                    value: segment.value,
                    series: segment.series,
                    percentage: segment.percentage
                });
            }

            if (labels.visible) {
                segment.label = new TextBox(labelText, deepExtend({}, labels, {
                        id: uniqueId(),
                        align: CENTER,
                        vAlign: "",
                        animation: {
                            type: FADEIN,
                            delay: segment.categoryIx * PIE_SECTOR_ANIM_DELAY
                        }
                    }));

                segment.append(segment.label);
                segment.registerId(segment.label.options.id);
            }
        },

        reflow: function(targetBox) {
            var segment = this;

            segment.render();

            segment.box = targetBox;
            targetBox.clone();

            segment.reflowLabel();
        },

        reflowLabel: function() {
            var segment = this,
                sector = segment.sector.clone(),
                options = segment.options,
                label = segment.label,
                labelsOptions = options.labels,
                labelsDistance = labelsOptions.distance,
                lp,
                x1,
                angle = sector.middle(),
                labelWidth,
                labelHeight;

            if (label) {
                labelHeight = label.box.height();
                labelWidth = label.box.width();
                if (labelsOptions.position == "center") {
                    sector.r = math.abs((sector.r - labelHeight) / 2) + labelHeight;
                    lp = sector.point(angle);
                    label.reflow(new Box2D(lp.x, lp.y - labelHeight / 2, lp.x, lp.y));
                } else if (labelsOptions.position == "insideEnd") {
                    sector.r = sector.r - labelHeight / 2;
                    lp = sector.point(angle);
                    label.reflow(new Box2D(lp.x, lp.y - labelHeight / 2, lp.x, lp.y));
                } else {
                    lp = sector.clone().expand(labelsDistance).point(angle);
                    if (lp.x >= sector.c.x) {
                        x1 = lp.x + labelWidth;
                        label.orientation = RIGHT;
                    } else {
                        x1 = lp.x - labelWidth;
                        label.orientation = LEFT;
                    }
                    label.reflow(new Box2D(x1, lp.y - labelHeight, lp.x, lp.y));
                }
            }
        },

        getViewElements: function(view) {
            var segment = this,
                sector = segment.sector,
                options = segment.options,
                borderOptions = options.border || {},
                border = borderOptions.width > 0 ? {
                    stroke: borderOptions.color,
                    strokeWidth: borderOptions.width,
                    dashType: borderOptions.dashType
                } : {},
                elements = [],
                overlay = options.overlay;

            if (overlay) {
                overlay = deepExtend({}, options.overlay, {
                    r: sector.r,
                    cx: sector.c.x,
                    cy: sector.c.y
                })
            }

            elements.push(view.createSector(sector, deepExtend({
                id: options.id,
                fill: options.color,
                overlay: overlay,
                fillOpacity: options.opacity,
                strokeOpacity: options.opacity,
                animation: deepExtend(options.animation, {
                    delay: segment.categoryIx * PIE_SECTOR_ANIM_DELAY
                })
            }, border)));

            append(elements,
                ChartElement.fn.getViewElements.call(segment, view)
            );

            return elements;
        },

        getOutlineElement: function(view, options) {
            var segment = this,
                highlight = segment.options.highlight || {},
                border = highlight.border || {},
                outlineId = segment.options.id + OUTLINE_SUFFIX;

            segment.registerId(outlineId);
            options = deepExtend({}, options, { id: outlineId });

            return view.createSector(segment.sector, deepExtend({}, options, {
                fill: highlight.color,
                fillOpacity: highlight.opacity,
                strokeOpacity: border.opacity,
                strokeWidth: border.width,
                stroke: border.color
            }));
        },

        tooltipAnchor: function(tooltipWidth, tooltipHeight) {
            var w = tooltipWidth / 2,
                h = tooltipHeight / 2,
                r = math.sqrt((w * w) + (h * h)),
                sector = this.sector.clone().expand(r + TOOLTIP_OFFSET),
                tooltipCenter = sector.point(sector.middle());

            return new Point2D(tooltipCenter.x - w, tooltipCenter.y - h);
        },

        formatPointValue: function(format) {
            var point = this;

            return point.owner.formatPointValue(point.value, format);
        }
    });

    var PieChart = ChartElement.extend({
        init: function(plotArea, options) {
            var chart = this;

            ChartElement.fn.init.call(chart, options);

            chart.plotArea = plotArea;
            chart.segments = [];
            chart.seriesPoints = [];
            chart.render();
        },

        options: {
            startAngle: 90,
            padding: 60,
            connectors: {
                width: 1,
                color: "#939393",
                padding: 4
            }
        },

        render: function() {
            var chart = this;

            chart.traverseDataPoints(proxy(chart.addValue, chart));
        },

        traverseDataPoints: function(callback) {
            var chart = this,
                options = chart.options,
                colors = chart.plotArea.options.seriesColors || [],
                startAngle = options.startAngle,
                colorsCount = colors.length,
                series = options.series,
                dataItems,
                currentName,
                currentSeries,
                currentData,
                seriesIx,
                angle,
                data,
                anglePerValue,
                value,
                explode,
                total,
                i;

            for (seriesIx = 0; seriesIx < series.length; seriesIx++) {
                currentSeries = series[seriesIx];
                dataItems = currentSeries.dataItems;
                data = currentSeries.data;
                total = chart.pointsTotal(data)
                anglePerValue = 360 / total;

                for (i = 0; i < data.length; i++) {
                    currentData = chart.pointData(currentSeries, i);
                    value = currentData.value;
                    angle = value * anglePerValue;
                    currentName = currentData.category;
                    explode = data.length != 1 && !!currentData.explode;
                    currentSeries.color = currentData.color ?
                        currentData.color : colors[i % colorsCount];

                    callback(value, new Sector(null, 0, startAngle, angle), {
                        owner: chart,
                        category: currentName,
                        categoryIx: i,
                        series: currentSeries,
                        seriesIx: seriesIx,
                        dataItem: dataItems ? dataItems[i] : { value: currentData },
                        percentage: value / total,
                        explode: explode,
                        currentData: currentData
                    });

                    startAngle += angle;
                }
            }
        },

        addValue: function(value, sector, fields) {
            var chart = this,
                segment;

            segment = new PieSegment(value, sector, fields.series);
            segment.options.id = uniqueId();
            extend(segment, fields);
            chart.append(segment);
            chart.segments.push(segment);
        },

        pointValue: function(point) {
            return defined(point.value) ? point.value : point;
        },

        pointData: function(series, index) {
            var chart = this,
                data = series.data[index];

            return {
                value: chart.pointValue(data),
                category: chart.pointGetter(series, index, "category"),
                color: chart.pointGetter(series, index, "color"),
                explode: chart.pointGetter(series, index, "explode")
            };
        },

        pointGetter: function(series, index, prop) {
            var valueField = series[prop + "Field"],
                data = series.data[index],
                value = data[prop];

            if (valueField && series.dataItems) {
                return getter(valueField, true)(series.dataItems[index]);
            } else {
                return defined(value) ? value : "";
            }
        },

        pointsTotal: function(data) {
            var chart = this,
                length = data.length,
                sum = 0,
                i;

            for(i = 0; i < length; i++) {
                sum += chart.pointValue(data[i]);
            }

            return sum;
        },

        reflow: function(targetBox) {
            var chart = this,
                options = chart.options,
                box = targetBox.clone(),
                minWidth = math.min(box.width(), box.height()),
                space = 5,
                padding = options.padding > minWidth / 2 - space ? minWidth / 2 - space : options.padding,
                newBox = new Box2D(box.x1, box.y1,
                    box.x1 + minWidth, box.y1 + minWidth),
                newBoxCenter = newBox.center(),
                boxCenter = box.center(),
                segments = chart.segments,
                count = segments.length,
                leftSideLabels = [],
                rightSideLabels = [],
                label,
                segment,
                sector,
                i;

            newBox.translate(boxCenter.x - newBoxCenter.x, boxCenter.y - newBoxCenter.y);

            for (i = 0; i < count; i++) {
                segment = segments[i];

                sector = segment.sector;
                sector.r = minWidth / 2 - padding;
                sector.c = new Point2D(
                    sector.r + newBox.x1 + padding,
                    sector.r + newBox.y1 + padding
                );

                if (segment.explode) {
                    sector.c = sector.clone().radius(sector.r * 0.15).point(sector.middle());
                }

                segment.reflow(newBox);

                label = segment.label;
                if (label) {
                    if (label.options.position === OUTSIDE_END) {
                        if (label.orientation === RIGHT) {
                            rightSideLabels.push(label);
                        } else {
                            leftSideLabels.push(label);
                        }
                    }
                }
            }

            if (leftSideLabels.length > 0) {
                leftSideLabels.sort(chart.labelComparator(true));
                chart.leftLabelsReflow(leftSideLabels);
            }

            if (rightSideLabels.length > 0) {
                rightSideLabels.sort(chart.labelComparator(false));
                chart.rightLabelsReflow(rightSideLabels);
            }

            chart.box = newBox;
        },

        leftLabelsReflow: function(labels) {
            var chart = this,
                distances = chart.distanceBetweenLabels(labels);

            chart.distributeLabels(distances, labels);
        },

        rightLabelsReflow: function(labels) {
            var chart = this,
                distances = chart.distanceBetweenLabels(labels);

            chart.distributeLabels(distances, labels);
        },

        distanceBetweenLabels: function(labels) {
            var chart = this,
                segment = chart.segments[0],
                sector = segment.sector,
                firstBox = labels[0].box,
                secondBox,
                count = labels.length - 1,
                distances = [],
                distance,
                lr = sector.r + segment.options.labels.distance,
                i;

            distance = round(firstBox.y1 - (sector.c.y - lr - firstBox.height() - firstBox.height() / 2));
            distances.push(distance);
            for (i = 0; i < count; i++) {
                firstBox = labels[i].box;
                secondBox = labels[i + 1].box;
                distance = round(secondBox.y1 - firstBox.y2);
                distances.push(distance);
            }
            distance = round(sector.c.y + lr - labels[count].box.y2 - labels[count].box.height() / 2);
            distances.push(distance);

            return distances;
        },

        distributeLabels: function(distances, labels) {
            var chart = this,
                count = distances.length,
                remaining,
                left,
                right,
                i;

            for (i = 0; i < count; i++) {
                left = right = i;
                remaining = -distances[i];
                while(remaining > 0 && (left >= 0 || right < count)) {
                    remaining = chart._takeDistance(distances, i, --left, remaining);
                    remaining = chart._takeDistance(distances, i, ++right, remaining);
                }
            }

            chart.reflowLabels(distances, labels);
        },

        _takeDistance: function(distances, anchor, position, amount) {
            if (distances[position] > 0) {
                var available = math.min(distances[position], amount);
                amount -= available;
                distances[position] -= available;
                distances[anchor] += available;
            }

            return amount;
        },

        reflowLabels: function(distances, labels) {
            var chart = this,
                segments = chart.segments,
                segment = segments[0],
                sector = segment.sector,
                labelsCount = labels.length,
                labelOptions = segment.options.labels,
                labelDistance = labelOptions.distance,
                boxY = sector.c.y - (sector.r + labelDistance) - labels[0].box.height(),
                label,
                boxX,
                box,
                i;

            distances[0] += 2;
            for (i = 0; i < labelsCount; i++) {
                label = labels[i];
                boxY += distances[i];
                box = label.box;
                boxX = chart.hAlignLabel(
                    box.x2,
                    sector.clone().expand(labelDistance),
                    boxY,
                    boxY + box.height(),
                    label.orientation == RIGHT);

                if (label.orientation == RIGHT) {
                    if (labelOptions.align !== CIRCLE) {
                        boxX = sector.r + sector.c.x + labelDistance;
                    }
                    label.reflow(new Box2D(boxX + box.width(), boxY,
                        boxX, boxY));
                } else {
                    if (labelOptions.align !== CIRCLE) {
                        boxX = sector.c.x - sector.r - labelDistance;
                    }
                    label.reflow(new Box2D(boxX - box.width(), boxY,
                        boxX, boxY));
                }

                boxY += box.height();
            }
        },

        getViewElements: function(view) {
            var chart = this,
                options = chart.options,
                connectors = options.connectors,
                segments = chart.segments,
                connectorLine,
                sector,
                count = segments.length,
                space = 4,
                angle,
                lines = [],
                points,
                segment,
                seriesIx,
                label,
                i;

            for (i = 0; i < count; i++) {
                segment = segments[i];
                sector = segment.sector;
                angle = sector.middle();
                label = segment.label;
                seriesIx = { seriesId: segment.seriesIx };

                if (label) {
                    points = [];
                    if (label.options.position === OUTSIDE_END) {
                        var box = label.box,
                            centerPoint = sector.c,
                            start = sector.point(angle),
                            middle = new Point2D(box.x1, box.center().y),
                            sr,
                            end,
                            crossing;

                        start = sector.clone().expand(connectors.padding).point(angle);
                        points.push(start);
                        if (label.orientation == RIGHT) {
                            end = new Point2D(box.x1 - connectors.padding, box.center().y);
                            crossing = intersection(centerPoint, start, middle, end);
                            middle = new Point2D(end.x - space, end.y);
                            crossing = crossing || middle;
                            crossing.x = math.min(crossing.x, middle.x);

                            if (chart.pointInCircle(crossing, sector.c, sector.r + space) ||
                                crossing.x < sector.c.x) {
                                sr = sector.c.x + sector.r + space;
                                if (segment.options.labels.align !== COLUMN) {
                                    if (sr < middle.x) {
                                        points.push(new Point2D(sr, start.y));
                                    } else {
                                        points.push(new Point2D(start.x + space * 2, start.y));
                                    }
                                } else {
                                    points.push(new Point2D(sr, start.y));
                                }
                                points.push(new Point2D(middle.x, end.y));
                            } else {
                                crossing.y = end.y;
                                points.push(crossing);
                            }
                        } else {
                            end = new Point2D(box.x2 + connectors.padding, box.center().y);
                            crossing = intersection(centerPoint, start, middle, end);
                            middle = new Point2D(end.x + space, end.y);
                            crossing = crossing || middle;
                            crossing.x = math.max(crossing.x, middle.x);

                            if (chart.pointInCircle(crossing, sector.c, sector.r + space) ||
                                crossing.x > sector.c.x) {
                                sr = sector.c.x - sector.r - space;
                                if (segment.options.labels.align !== COLUMN) {
                                    if (sr > middle.x) {
                                        points.push(new Point2D(sr, start.y));
                                    } else {
                                        points.push(new Point2D(start.x - space * 2, start.y));
                                    }
                                } else {
                                    points.push(new Point2D(sr, start.y));
                                }
                                points.push(new Point2D(middle.x, end.y));
                            } else {
                                crossing.y = end.y;
                                points.push(crossing);
                            }
                        }

                        points.push(end);
                        connectorLine = view.createPolyline(points, false, {
                            id: uniqueId(),
                            stroke: connectors.color,
                            strokeWidth: connectors.width,
                            animation: {
                                type: FADEIN,
                                delay: segment.categoryIx * PIE_SECTOR_ANIM_DELAY
                            }
                        });
                        lines.push(connectorLine);
                        segment.registerId(connectorLine.options.id, seriesIx);
                    }
                    segment.registerId(label.options.id, seriesIx);
                }

                segment.registerId(segment.options.id, seriesIx);
            }

            append(lines,
                ChartElement.fn.getViewElements.call(chart, view));

            return lines;
        },

        labelComparator: function (reverse) {
            reverse = (reverse) ? -1 : 1;

            return function(a, b) {
                a = (a.parent.sector.middle() + 270) % 360;
                b = (b.parent.sector.middle() + 270) % 360;
                return (a - b) * reverse;
            }
        },

        hAlignLabel: function(originalX, sector, y1, y2, direction) {
            var cx = sector.c.x,
                cy = sector.c.y,
                r = sector.r,
                t = math.min(math.abs(cy - y1), math.abs(cy - y2));

            if (t > r) {
                return originalX;
            } else {
                return cx + math.sqrt((r * r) - (t * t)) * (direction ? 1 : -1);
            }
        },

        pointInCircle: function(point, c, r) {
            return sqr(c.x - point.x) + sqr(c.y - point.y) < sqr(r);
        },

        formatPointValue: function(value, tooltipFormat) {
            return format(tooltipFormat, value);
        }
    });

    var PlotArea = ChartElement.extend({
        init: function(options) {
            var plotArea = this;

            ChartElement.fn.init.call(plotArea, options);

            plotArea.render();
        },

        options: {
            categoryAxis: {},
            valueAxis: {},
            series: [],
            plotArea: {
                margin: {}
            },
            background: "",
            border: {
                color: BLACK,
                width: 0
            },
            range: {},
            legend: {}
        },

        render: function() {
            var plotArea = this,
                options = plotArea.options,
                series = options.series,
                seriesLength = series.length,
                currentSeries,
                pieSeries = [],
                barSeries = [],
                lineSeries = [],
                scatterSeries = [],
                scatterLineSeries = [],
                i;

            options.legend.items = [];
            options.range = { min: 0, max: 1 };
            plotArea.charts = [];
            for (i = 0; i < seriesLength; i++) {
                currentSeries = series[i];

                if (currentSeries.type === BAR || currentSeries.type === COLUMN) {
                    barSeries.push(currentSeries);
                } else if (currentSeries.type === LINE) {
                    lineSeries.push(currentSeries);
                } else if (currentSeries.type === PIE) {
                    pieSeries.push(currentSeries);
                } else if (currentSeries.type === "scatter") {
                    scatterSeries.push(currentSeries);
                } else if (currentSeries.type === "scatterLine") {
                    scatterLineSeries.push(currentSeries);
                }
            }

            if (barSeries.length > 0) {
                plotArea.createBarChart(barSeries);
            }

            if (lineSeries.length > 0) {
                plotArea.createLineChart(lineSeries);
            }

            if (pieSeries.length > 0) {
                plotArea.createPieChart(pieSeries);
            }

            if (scatterSeries.length > 0 || scatterLineSeries.length > 0) {
                if (scatterSeries.length > 0) {
                    plotArea.createScatterChart(scatterSeries);
                } else {
                    plotArea.createScatterLineChart(scatterLineSeries);
                }

                plotArea.axisX = new NumericAxis(options.range.min[0], options.range.max[0],
                    deepExtend({}, options.xAxis, { orientation: HORIZONTAL })
                );

                plotArea.axisY = new NumericAxis(options.range.min[1], options.range.max[1],
                    deepExtend({}, options.yAxis, { orientation: VERTICAL })
                );

                plotArea.append(plotArea.axisY);
                plotArea.append(plotArea.axisX);
            } else if (seriesLength != pieSeries.length || seriesLength == 0) {
                plotArea.createAxes(options.range.min, options.range.max, options.invertAxes);
            }

            plotArea.append.apply(plotArea, plotArea.charts);
        },

        addToLegend: function(series) {
            var plotArea = this,
                count = series.length,
                data = [],
                item,
                i;

            for (i = 0; i < count; i++) {
                item = { name: series[i].name || "", color: series[i].color };
                data.push(item);
            }

            append(plotArea.options.legend.items, data);
        },

        createBarChart: function(series) {
            var plotArea = this,
                options = plotArea.options,
                firstSeries = series[0],
                invertAxes = options.invertAxes = firstSeries.type === BAR,
                categories = options.categoryAxis.categories,
                barChart = new BarChart(plotArea, {
                    series: series,
                    isVertical: !invertAxes,
                    isStacked: firstSeries.stack,
                    gap: firstSeries.gap,
                    spacing: firstSeries.spacing
                }),
                categoriesToAdd = math.max(0, categoriesCount(series) - categories.length);

            append(categories, new Array(categoriesToAdd));
            options.range = barChart.valueRange() || options.range;
            plotArea.charts.push(barChart);

            plotArea.addToLegend(series);
        },

        createLineChart: function(series) {
            var plotArea = this,
                options = plotArea.options,
                firstSeries = series[0],
                categoryAxis = options.categoryAxis,
                categories = categoryAxis.categories,
                // Override the original invertAxes
                invertAxes = options.invertAxes = categoryAxis.orientation === VERTICAL,
                lineChart = new LineChart(plotArea, {
                    // TODO: Rename isVertical to invertAxes, flip logic
                    isVertical: !invertAxes,
                    isStacked: firstSeries.stack,
                    series: series
                }),
                categoriesToAdd = math.max(0, categoriesCount(series) - categories.length),
                lineChartRange = lineChart.valueRange() || options.range;

            append(categories, new Array(categoriesToAdd));
            // Override the original range
            options.range.min = math.min(options.range.min, lineChartRange.min);
            options.range.max = math.max(options.range.max, lineChartRange.max);
            plotArea.charts.push(lineChart);

            plotArea.addToLegend(series);
        },

        createScatterChart: function(series) {
            var plotArea = this,
                options = plotArea.options,
                // Override the original invertAxes
                scatterChart = new ScatterChart(plotArea, { series: series }),
                scatterChartRange = scatterChart.valueRange() || options.range;

            // Override the original range
            options.range = scatterChartRange;
            plotArea.charts.push(scatterChart);

            plotArea.addToLegend(series);
        },

        createScatterLineChart: function(series) {
            var plotArea = this,
                options = plotArea.options,
                // Override the original invertAxes
                scatterLineChart = new ScatterLineChart(plotArea, { series: series }),
                scatterLineChartRange = scatterLineChart.valueRange() || options.range;

            // Override the original range
            options.range = scatterLineChartRange;
            plotArea.charts.push(scatterLineChart);

            plotArea.addToLegend(series);
        },

        createPieChart: function(series) {
            var plotArea = this,
                options = plotArea.options,
                firstSeries = series[0],
                pieChart = new PieChart(plotArea, {
                    series: series,
                    padding: firstSeries.padding,
                    startAngle: firstSeries.startAngle,
                    connectors: firstSeries.connectors
                }),
                segments = pieChart.segments,
                count = segments.length,
                i;

            plotArea.charts.push(pieChart);
            for (i = 0; i < count; i++) {
                options.legend.items.push({
                    name: segments[i].category,
                    color: segments[i].options.color });
            }
        },

        createAxes: function(seriesMin, seriesMax, invertAxes) {
            var plotArea = this,
                options = plotArea.options,
                categoriesCount = options.categoryAxis.categories.length,
                categoryAxis = new CategoryAxis(deepExtend({
                        orientation: invertAxes ? VERTICAL : HORIZONTAL,
                        axisCrossingValue: invertAxes ? categoriesCount : 0
                    },
                    options.categoryAxis,
                    invertAxes ? options.yAxis : options.xAxis)
                ),
                valueAxis = new NumericAxis(seriesMin, seriesMax, deepExtend({
                        orientation: invertAxes ? HORIZONTAL : VERTICAL
                    },
                    options.valueAxis,
                    invertAxes ? options.xAxis : options.yAxis)
                );

            plotArea.axisX = invertAxes ? valueAxis : categoryAxis;
            plotArea.axisY = invertAxes ? categoryAxis : valueAxis;

            plotArea.append(plotArea.axisY);
            plotArea.append(plotArea.axisX);
        },

        alignAxes: function() {
            var plotArea = this,
                axisY = plotArea.axisY,
                axisX = plotArea.axisX,
                crossingValueY = axisY.options.axisCrossingValue,
                axisCrossingY = axisY.getSlot(crossingValueY, crossingValueY),
                crossingValueX = axisX.options.axisCrossingValue,
                axisCrossingX = axisX.getSlot(crossingValueX, crossingValueX);

            axisY.reflow(
                axisY.box.translate(axisCrossingX.x1 - axisCrossingY.x1, 0)
            );

            axisX.reflow(
                axisX.box.translate(0, axisCrossingY.y1 - axisCrossingX.y1)
            );
        },

        reflow: function(targetBox) {
            var plotArea = this,
                options = plotArea.options.plotArea,
                margin = getSpacing(options.margin)

            plotArea.box = targetBox.clone();

            plotArea.box.unpad(margin);
            plotArea.reflowAxes();
            plotArea.reflowCharts();
            plotArea.wrapAxes();
        },

        reflowAxes: function() {
            var plotArea = this,
                axisY = plotArea.axisY,
                axisX = plotArea.axisX,
                box = plotArea.box;

            if (axisY || axisX) {
                axisY.reflow(box);
                axisX.reflow(box);

                plotArea.alignAxes();

                var axisBox = axisY.box.clone().wrap(axisX.box),
                    overflowY = axisBox.height() - box.height(),
                    overflowX = axisBox.width() - box.width(),
                    offsetX = box.x1 - axisBox.x1,
                    offsetY = box.y1 - axisBox.y1;

                axisY.reflow(
                    axisY.box.translate(offsetX, offsetY).shrink(0, overflowY)
                );

                axisX.reflow(
                    axisX.box.translate(offsetX, offsetY).shrink(overflowX, 0)
                );

                plotArea.alignAxes();
            }
        },

        reflowCharts: function() {
            var plotArea = this,
                charts = plotArea.charts,
                count = charts.length,
                box = plotArea.box,
                i;

            for (i = 0; i < count; i++) {
                charts[i].reflow(box);
            }

            plotArea.box = box;
        },

        wrapAxes: function() {
            var plotArea = this,
                axisY = plotArea.axisY,
                axisX = plotArea.axisX,
                boxX,
                boxY;

            if (axisY || axisX) {
                boxX = axisX.getAxisLineBox();
                boxY = axisY.getAxisLineBox();
                plotArea.box = boxX.clone().wrap(boxY);
            }
        },

        renderGridLines: function(view, axis, secondaryAxis) {
            var options = axis.options,
                isVertical = options.orientation === VERTICAL,
                boundaries = secondaryAxis.getMajorTickPositions(),
                crossingSlot = axis.getSlot(options.axisCrossingValue),
                secAxisPos = round(crossingSlot[isVertical ? "y1" : "x1"]),
                lineStart = boundaries[0],
                lineEnd = boundaries.pop(),
                majorTicks = axis.getMajorTickPositions(),
                gridLines = [],
                gridLine = function (pos, options) {
                    return {
                        pos: pos,
                        options: options
                    };
                };

                if (options.majorGridLines.visible) {
                    gridLines = map(majorTicks, function(pos) {
                                    return gridLine(pos, options.majorGridLines);
                                });
                }

                if (options.minorGridLines.visible) {
                    gridLines = gridLines.concat(
                        map(axis.getMinorTickPositions(), function(pos) {
                            if (options.majorGridLines.visible) {
                                if (!inArray(pos, majorTicks)) {
                                    return gridLine(pos, options.minorGridLines);
                                }
                            } else {
                                return gridLine(pos, options.minorGridLines);
                            }
                        }
                    ));
                }

                return map(gridLines, function(line) {
                    var gridLineOptions = {
                            strokeWidth: line.options.width,
                            stroke: line.options.color,
                            dashType: line.options.dashType
                        },
                        linePos = round(line.pos);

                    if (secAxisPos === linePos) {
                        return null;
                    }

                    if (isVertical) {
                        return view.createLine(
                            lineStart, linePos, lineEnd, linePos,
                            gridLineOptions);
                    } else {
                        return view.createLine(
                            linePos, lineStart, linePos, lineEnd,
                            gridLineOptions);
                    }
                });
        },

        getViewElements: function(view) {
            var plotArea = this,
                options = plotArea.options.plotArea,
                axisY = plotArea.axisY,
                axisX = plotArea.axisX,
                gridLinesY = axisY ? plotArea.renderGridLines(view, axisY, axisX) : [],
                gridLinesX = axisX ? plotArea.renderGridLines(view, axisX, axisY) : [],
                childElements = ChartElement.fn.getViewElements.call(plotArea, view),
                border = options.border || {},
                elements = [
                    view.createRect(plotArea.box, {
                        fill: options.background,
                        zIndex: -1
                    }),
                    view.createRect(plotArea.box, {
                        stroke: border.width ? border.color : "",
                        strokeWidth: border.width,
                        fill: "",
                        zIndex: 0,
                        dashType: border.dashType
                    })
                ];

            return [].concat(gridLinesY, gridLinesX, childElements, elements);
        }

    });

    // **************************
    // Visual elements
    // **************************

    var ViewElement = Class.extend({
        init: function(options) {
            var element = this;
            element.children = [];
            element.options = deepExtend({}, element.options, options);
        },

        render: function() {
            return this.template(this);
        },

        renderContent: function() {
            var output = "",
                element = this,
                sortedChildren = element.sortChildren(),
                childrenCount = sortedChildren.length;

            for (var i = 0; i < childrenCount; i++) {
                output += sortedChildren[i].render();
            }

            return output;
        },

        sortChildren: function() {
            var element = this,
                children = element.children;

            for (var i = 0, length = children.length; i < length; i++) {
                children[i]._childIndex = i;
            }

            return children.slice(0).sort(element.compareChildren);
        },

        compareChildren: function(a, b) {
            var aValue = a.options.zIndex || 0,
                bValue = b.options.zIndex || 0;

            if (aValue !== bValue) {
                return aValue - bValue;
            }

            return a._childIndex - b._childIndex;
        },

        renderAttr: function (name, value) {
            return defined(value) ? " " + name + "='" + value + "' " : "";
        }
    });

    var ViewBase = ViewElement.extend({
        init: function(options) {
            var view = this;

            ViewElement.fn.init.call(view, options);

            view.definitions = {};
            view.decorators = [];
            view.animations = [];
        },

        renderDefinitions: function() {
            var view = this,
                definitions = view.definitions,
                definitionId,
                output = "";

            for (definitionId in definitions) {
                if (definitions.hasOwnProperty(definitionId)) {
                    output += definitions[definitionId].render();
                }
            }

            return output;
        },

        decorate: function(element) {
            var view = this,
                decorators = view.decorators,
                i,
                length = decorators.length,
                currentDecorator;

            for (i = 0; i < length; i++) {
                currentDecorator = decorators[i];
                view._decorateChildren(currentDecorator, element);
                element = currentDecorator.decorate.call(currentDecorator, element);
            }

            return element;
        },

        _decorateChildren: function(decorator, element) {
            var view = this,
                children = element.children,
                i,
                length = children.length;

            for (i = 0; i < length; i++) {
                view._decorateChildren(decorator, children[i]);
                children[i] = decorator.decorate.call(decorator, children[i]);
            }
        },

        setupAnimations: function() {
            var animations = this.animations,
                i,
                count = animations.length;

            for (i = 0; i < count; i++) {
                animations[i].setup();
            }
        },

        playAnimations: function() {
            var view = this,
                anim;

            while(anim = view.animations.shift()) {
                anim.play();
            }
        },

        buildGradient: function(options) {
            var view = this,
                cache = view._gradientCache,
                hashCode,
                overlay,
                definition;

            if (!cache) {
                cache = view._gradientCache = [];
            }

            if (options) {
                hashCode = getHash(options);
                overlay = cache[hashCode];
                definition = Chart.Gradients[options.gradient];
                if (!overlay && definition) {
                    overlay = deepExtend({ id: uniqueId() }, definition, options);
                    cache[hashCode] = overlay;
                }
            }

            return overlay;
        }
    });

    function supportsSVG() {
        return doc.implementation.hasFeature(
            "http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1");
    }

    // Animations
    var BarAnimationDecorator = Class.extend({
        init: function(view) {
            this.view = view;
        },

        decorate: function(element) {
            var decorator = this,
                view = decorator.view,
                animation = element.options.animation;

            if (animation && view.options.transitions) {
                if (animation.type === BAR) {
                    view.animations.push(
                        new BarAnimation(element)
                    );
                }
            }

            return element;
        }
    });

    var PieAnimationDecorator = Class.extend({
        init: function(view) {
            this.view = view;
        },

        decorate: function(element) {
            var decorator = this,
                view = decorator.view,
                animation = element.options.animation;

            if (animation && animation.type === PIE && view.options.transitions) {
                view.animations.push(
                    new PieAnimation(element, animation)
                );
            }

            return element;
        }
    });

    var FadeAnimationDecorator = Class.extend({
        init: function(view) {
            this.view = view;
        },

        decorate: function(element) {
            var decorator = this,
                view = decorator.view,
                options = view.options,
                animation = element.options.animation;

            if (animation && animation.type === FADEIN && options.transitions) {
                view.animations.push(
                    new FadeAnimation(element, animation)
                );
            }

            return element;
        }
    });

    var ElementAnimation = Class.extend({
        init: function(element, options) {
            var anim = this;

            anim.options = deepExtend({}, anim.options, options);
            anim.element = element;
        },

        options: {
            duration: INITIAL_ANIMATION_DURATION,
            easing: SWING
        },

        play: function() {
            var anim = this,
                options = anim.options,
                element = anim.element,
                delay = options.delay || 0,
                start = +new Date() + delay,
                duration = options.duration,
                finish = start + duration,
                domElement = doc.getElementById(element.options.id),
                easing = jQuery.easing[options.easing],
                time,
                pos,
                easingPos;

            setTimeout(function() {
                var loop = function() {
                    time = +new Date();
                    pos = time > finish ? 1 : (time - start) / duration;
                    easingPos = easing(pos, time - start, 0, 1, duration);

                    anim.step(easingPos);

                    element.refresh(domElement);

                    if (time < finish) {
                        requestAnimFrame(loop, domElement);
                    }
                };

                loop();
            }, delay);
        },

        setup: function() {
        },

        step: function(pos) {
        }
    });

    var FadeAnimation = ElementAnimation.extend({
        options: {
            duration: 200,
            easing: LINEAR
        },

        setup: function() {
            var anim = this,
                options = anim.element.options;

            anim.targetFillOpacity = options.fillOpacity;
            anim.targetStrokeOpacity = options.strokeOpacity;
            options.fillOpacity = options.strokeOpacity = 0;
        },

        step: function(pos) {
            var anim = this,
                options = anim.element.options;

            options.fillOpacity = pos * anim.targetFillOpacity;
            options.strokeOpacity = pos * anim.targetStrokeOpacity;
        }
    });

    var ExpandAnimation = ElementAnimation.extend({
        options: {
            size: 0,
            easing: LINEAR
        },

        setup: function() {
            var anim = this,
                points = anim.element.points;

            points[1].x = points[2].x = points[0].x;
        },

        step: function(pos) {
            var anim = this,
                options = anim.options,
                size = interpolateValue(0, options.size, pos),
                points = anim.element.points;

            // Expands rectangle to the right
            points[1].x = points[2].x = points[0].x + size;
        }
    });

    var requestAnimFrame =
        window.requestAnimationFrame       ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame    ||
        window.oRequestAnimationFrame      ||
        window.msRequestAnimationFrame     ||
        function(callback, element) {
            setTimeout(callback, ANIMATION_STEP);
        };

    var BarAnimation = ElementAnimation.extend({
        options: {
            easing: SWING
        },

        setup: function() {
            var anim = this,
                element = anim.element,
                points = element.points,
                options = element.options,
                axis = options.normalAngle === 0 ? Y : X,
                stackBase = options.stackBase,
                aboveAxis = options.aboveAxis,
                startPosition,
                endState = anim.endState = {
                    top: points[0].y,
                    right: points[1].x,
                    bottom: points[3].y,
                    left: points[0].x
                };

            if (axis === Y) {
                startPosition = defined(stackBase) ? stackBase :
                    aboveAxis ? endState.bottom : endState.top;
            } else {
                startPosition = defined(stackBase) ? stackBase :
                    aboveAxis ? endState.left : endState.right;
            }

            anim.startPosition = startPosition;

            updateArray(points, axis, startPosition);
        },

        step: function(pos) {
            var anim = this,
                startPosition = anim.startPosition,
                endState = anim.endState,
                element = anim.element,
                points = element.points;

            if (element.options.normalAngle === 0) {
                points[0].y = points[1].y =
                    interpolateValue(startPosition, endState.top, pos);

                points[2].y = points[3].y =
                    interpolateValue(startPosition, endState.bottom, pos);
            } else {
                points[0].x = points[3].x =
                    interpolateValue(startPosition, endState.left, pos);

                points[1].x = points[2].x =
                    interpolateValue(startPosition, endState.right, pos);
            }
        }
    });

    var PieAnimation = ElementAnimation.extend({
        options: {
            easing: "easeOutElastic",
            duration: INITIAL_ANIMATION_DURATION
        },

        setup: function() {
            var anim = this,
                sector = anim.element.circleSector;

            anim.endRadius = sector.r;
            sector.r = 0;
        },

        step: function(pos) {
            var anim = this,
                endRadius = anim.endRadius,
                sector = anim.element.circleSector;

            sector.r = interpolateValue(0, endRadius, pos);
        }
    });

    var Highlight = Class.extend({
        init: function(view, viewElement, options) {
            var highlight = this;
            highlight.options = deepExtend({}, highlight.options, options);

            highlight.view = view;
            highlight.viewElement = viewElement;
        },

        options: {
            fill: WHITE,
            fillOpacity: 0.2,
            stroke: WHITE,
            strokeWidth: 1,
            strokeOpacity: 0.2
        },

        show: function(point) {
            var highlight = this,
                view = highlight.view,
                viewElement = highlight.viewElement,
                outline,
                element;

            highlight.hide();

            if (point.getOutlineElement) {
                outline = point.getOutlineElement(view, highlight.options);

                if (outline) {
                    element = view.renderElement(outline);
                    viewElement.appendChild(element);

                    highlight.element = element;
                    highlight.visible = true;
                }
            }
        },

        hide: function() {
            var highlight = this,
                element = highlight.element;

            if (element) {
                element.parentNode.removeChild(element);

                delete highlight.element;
                highlight.visible = false;
            }
        }
    });

    var Tooltip = Class.extend({
        init: function(chartElement, options) {
            var tooltip = this;

            tooltip.options = deepExtend({}, tooltip.options, options);
            options = tooltip.options;

            tooltip.chartElement = chartElement;

            tooltip.template = Tooltip.template;
            if (!tooltip.template) {
                tooltip.template = Tooltip.template = template(
                    "<div style='display:none; position: absolute; font: #= d.font #;" +
                    "border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px;" +
                    "border: #= d.border.width #px solid;" +
                    "opacity: #= d.opacity #; filter: alpha(opacity=#= d.opacity * 100 #);" +
                    "padding: 2px 6px; white-space: nowrap;'></div>"
                );
            }

            tooltip.element = $(tooltip.template(tooltip.options)).appendTo(chartElement);
        },

        options: {
            background: BLACK,
            color: WHITE,
            border: {
                width: 3
            },
            opacity: 1,
            animation: {
                duration: TOOLTIP_ANIMATION_DURATION
            }
        },

        show: function(point) {
            var tooltip = this;

            tooltip.point = point;
            setTimeout(proxy(tooltip._show, tooltip), TOOLTIP_SHOW_DELAY);
        },

        _show: function() {
            var tooltip = this,
                point = tooltip.point,
                element = tooltip.element,
                options = tooltip.options,
                anchor,
                template,
                content,
                tooltipOptions,
                top,
                left;

            if (!point) {
                return;
            }
            content = point.value.toString();

            tooltipOptions = deepExtend({}, tooltip.options, point.options.tooltip);

            if (tooltipOptions.template) {
                template = baseTemplate(tooltipOptions.template);
                content = template({
                    value: point.value,
                    category: point.category,
                    series: point.series,
                    dataItem: point.dataItem,
                    percentage: point.percentage
                });
            } else if (tooltipOptions.format) {
                content = point.formatPointValue(tooltipOptions.format);
            }

            element.html(content);

            anchor = point.tooltipAnchor(element.outerWidth(), element.outerHeight());
            top = round(anchor.y) + "px";
            left = round(anchor.x) + "px";

            if (!tooltip.visible) {
                tooltip.element.css({ top: top, left: left });
            }

            tooltip.element
                .css({
                   backgroundColor: tooltipOptions.background,
                   borderColor: tooltipOptions.border.color || point.options.color,
                   color: tooltipOptions.color,
                   opacity: tooltipOptions.opacity,
                   borderWidth: tooltipOptions.border.width
                })
                .stop(true, true)
                .show()
                .animate({
                    left: left,
                    top: top
                }, options.animation.duration);

            tooltip.visible = true;
        },

        hide: function() {
            var tooltip = this;

            if (tooltip.visible) {
                tooltip.element.fadeOut();

                tooltip.point = null;
                tooltip.visible = false;
            }
        }
    });

    // Helper functions
    function ceil(value, step) {
        return round(math.ceil(value / step) * step, DEFAULT_PRECISION);
    }

    function floor(value, step) {
        return round(math.floor(value / step) * step, DEFAULT_PRECISION);
    }

    function round(value, precision) {
        var power = math.pow(10, precision || 0);
        return math.round(value * power) / power;
    }

    function measureText(text, style, rotation) {
        var styleHash = getHash(style),
            cacheKey = text + styleHash + rotation,
            cachedResult = measureText.cache[cacheKey];

        if (cachedResult) {
            return cachedResult;
        }

        var measureBox = measureText.measureBox,
            baselineMarker = measureText.baselineMarker.cloneNode(false);

        if (!measureBox) {
            measureBox = measureText.measureBox =
                $("<div style='position: absolute; top: -4000px; left: -4000px;" +
                              "line-height: normal; visibility: hidden;' />")
                .appendTo(doc.body)[0];
        }

        for (var styleKey in style) {
            measureBox.style[styleKey] = style[styleKey];
        }
        measureBox.innerHTML = text;
        measureBox.appendChild(baselineMarker);

        var size = {
                width: measureBox.offsetWidth - BASELINE_MARKER_SIZE,
                height: measureBox.offsetHeight,
                baseline: baselineMarker.offsetTop + BASELINE_MARKER_SIZE
            };

        if (rotation) {
            var width = size.width,
                height = size.height,
                cx = width / 2,
                cy = height / 2,
                r1 = rotatePoint(0, 0, cx, cy, rotation),
                r2 = rotatePoint(width, 0, cx, cy, rotation),
                r3 = rotatePoint(width, height, cx, cy, rotation);
                r4 = rotatePoint(0, height, cx, cy, rotation);

            size.normalWidth = width;
            size.normalHeight = height;
            size.width = math.max(r1.x, r2.x, r3.x, r4.x) - math.min(r1.x, r2.x, r3.x, r4.x);
            size.height = math.max(r1.y, r2.y, r3.y, r4.y) - math.min(r1.y, r2.y, r3.y, r4.y);
        }

        measureText.cache[cacheKey] = size;

        return size;
    }

    measureText.cache = [];
    measureText.baselineMarker =
        $("<div style='display: inline-block; vertical-align: baseline;" +
                  "width: " + BASELINE_MARKER_SIZE + "px; height: " + BASELINE_MARKER_SIZE + "px;" +
                  "zoom: 1; *display: inline; overflow: hidden;' />")[0];

    function getHash(object) {
        var hash = [];
        for (var key in object) {
            hash.push(key + object[key]);
        }

        return hash.sort().join(" ");
    }

    function rotatePoint(x, y, cx, cy, angle) {
        var theta = angle * DEGREE;
        return {
            x: cx + (x - cx) * math.cos(theta) + (y - cy) * math.sin(theta),
            y: cy - (x - cx) * math.sin(theta) + (y - cy) * math.cos(theta)
        }
    }

    function boxDiff(r, s) {
        if (r.x1 == s.x1 && r.y1 == s.y1 && r.x2 == s.x2 && r.y2 == s.y2) {
            return s;
        }

        var a = math.min(r.x1, s.x1);
        var b = math.max(r.x1, s.x1);
        var c = math.min(r.x2, s.x2);
        var d = math.max(r.x2, s.x2);

        var e = math.min(r.y1, s.y1);
        var f = math.max(r.y1, s.y1);
        var g = math.min(r.y2, s.y2);
        var h = math.max(r.y2, s.y2);

        // X = intersection, 0-7 = possible difference areas
        // h +-+-+-+
        // . |5|6|7|
        // g +-+-+-+
        // . |3|X|4|
        // f +-+-+-+
        // . |0|1|2|
        // e +-+-+-+
        // . a b c d

        var result = [];

        // we'll always have rectangles 1, 3, 4 and 6
        result[0] = new Box2D(b, e, c, f);
        result[1] = new Box2D(a, f, b, g);
        result[2] = new Box2D(c, f, d, g);
        result[3] = new Box2D(b, g, c, h);

        // decide which corners
        if( r.x1 == a && r.y1 == e || s.x1 == a && s.y1 == e )
        { // corners 0 and 7
            result[4] = new Box2D(a, e, b, f);
            result[5] = new Box2D(c, g, d, h);
        }
        else
        { // corners 2 and 5
            result[4] = new Box2D(c, e, d, f);
            result[5] = new Box2D(a, g, b, h);
        }

        return $.grep(result, function(box) {
            return box.height() > 0 && box.width() > 0
        })[0];
    }

    function sparseArrayMin(arr) {
        return sparseArrayLimits(arr).min;
    }

    function sparseArrayMax(arr) {
        return sparseArrayLimits(arr).max;
    }

    function sparseArrayLimits(arr) {
        var min = Number.MAX_VALUE,
            max = - Number.MAX_VALUE;
        for (var i = 0, length = arr.length; i < length; i++) {
            var n = arr[i];
            if (defined(n)) {
                min = math.min(min, n);
                max = math.max(max, n);
            }
        }

        return { min: min, max: max };
    }

    function getSpacing(value) {
        var spacing = { top: 0, right: 0, bottom: 0, left: 0 };

        if (typeof(value) === "number") {
            spacing[TOP] = spacing[RIGHT] = spacing[BOTTOM] = spacing[LEFT] = value;
        } else {
            spacing[TOP] = value[TOP] || 0;
            spacing[RIGHT] = value[RIGHT] || 0;
            spacing[BOTTOM] = value[BOTTOM] || 0;
            spacing[LEFT] = value[LEFT] || 0;
        }

        return spacing;
    }

    function inArray(value, array) {
        return $.inArray(value, array) != -1;
    }

    function deepExtend(destination) {
        var i = 1,
            length = arguments.length;

        for (i = 1; i < length; i++) {
            deepExtendOne(destination, arguments[i]);
        }

        return destination;
    }

    function deepExtendOne(destination, source) {
        var property,
            propValue,
            propType,
            destProp;

        for (property in source) {
            propValue = source[property];
            propType = typeof propValue;
            if (propType === OBJECT && propValue !== null && propValue.constructor !== Array) {
                destProp = destination[property];
                if (typeof (destProp) === OBJECT) {
                    destination[property] = destProp || {};
                } else {
                    destination[property] = {};
                }
                deepExtendOne(destination[property], propValue);
            } else if (propType !== UNDEFINED) {
                destination[property] = propValue;
            }
        }

        return destination;
    }

    function intersection(a1, a2, b1, b2) {
        var result,
            ua_t = (b2.x - b1.x) * (a1.y - b1.y) - (b2.y - b1.y) * (a1.x - b1.x),
            u_b = (b2.y - b1.y) * (a2.x - a1.x) - (b2.x - b1.x) * (a2.y - a1.y),
            ua;

        if (u_b != 0) {
            ua = (ua_t / u_b);

            result = new Point2D(
                a1.x + ua * (a2.x - a1.x),
                a1.y + ua * (a2.y - a1.y)
            );
        }

        return result;
    }

    function append(first, second) {
        [].push.apply(first, second);
    }

    function interpolateValue(start, end, progress) {
        return round(start + (end - start) * progress, COORD_PRECISION);
    }

    function applySeriesDefaults(options, themeOptions) {
        var series = options.series,
            i,
            seriesLength = series.length,
            seriesType,
            seriesDefaults = options.seriesDefaults,
            baseSeriesDefaults = deepExtend({}, options.seriesDefaults),
            themeSeriesDefaults = themeOptions ? deepExtend({}, themeOptions.seriesDefaults) : {};

        delete baseSeriesDefaults.bar;
        delete baseSeriesDefaults.column;
        delete baseSeriesDefaults.line;
        delete baseSeriesDefaults.pie;
        delete baseSeriesDefaults.scatter;
        delete baseSeriesDefaults.scatterLine;

        for (i = 0; i < seriesLength; i++) {
            seriesType = series[i].type || options.seriesDefaults.type;

            series[i] = deepExtend(
                {},
                themeSeriesDefaults,
                themeSeriesDefaults[seriesType],
                { tooltip: options.tooltip },
                baseSeriesDefaults,
                seriesDefaults[seriesType],
                series[i]);
        }
    }

    function applySeriesColors(options) {
        var series = options.series,
            i,
            seriesLength = series.length,
            colors = options.seriesColors || [];

        for (i = 0; i < seriesLength; i++) {
            series[i].color = series[i].color || colors[i % colors.length];
        }
    }

    function applyAxisDefaults(options, themeOptions) {
        var themeAxisDefaults = themeOptions ? deepExtend({}, themeOptions.axisDefaults) : {};

        $.each(["category", "value", "x", "y"], function() {
            var axisName = this + "Axis";
            options[axisName] = deepExtend({},
                themeAxisDefaults,
                themeAxisDefaults[axisName],
                options.axisDefaults,
                options[axisName]
            );
        });
    }

    function incrementSlot(slots, index, value) {
        slots[index] = (slots[index] || 0) + value;
    }

    function uniqueId() {
        var id = "k", i;

        for (i = 0; i < 16; i++) {
            id += (math.random() * 16 | 0).toString(16);
        }

        return id;
    }

    function defined(value) {
        return typeof value !== UNDEFINED;
    }

    var Color = function(value) {
        var color = this,
            formats = Color.formats,
            re,
            processor,
            parts,
            i,
            channels;

        if (arguments.length === 1) {
            value = color.resolveColor(value);

            for (i = 0; i < formats.length; i++) {
                re = formats[i].re;
                processor = formats[i].process;
                parts = re.exec(value);

                if (parts) {
                    channels = processor(parts);
                    color.r = channels[0];
                    color.g = channels[1];
                    color.b = channels[2];
                }
            }
        } else {
            color.r = arguments[0];
            color.g = arguments[1];
            color.b = arguments[2];
        }

        color.r = color.normalizeByte(color.r);
        color.g = color.normalizeByte(color.g);
        color.b = color.normalizeByte(color.b);
    };

    Color.prototype = {
        toHex: function() {
            var color = this,
                pad = color.padDigit,
                r = color.r.toString(16),
                g = color.g.toString(16),
                b = color.b.toString(16);

            return "#" + pad(r) + pad(g) + pad(b);
        },

        resolveColor: function(value) {
            value = value || BLACK;

            if (value.charAt(0) == "#") {
                value = value.substr(1, 6);
            }

            value = value.replace(/ /g, "");
            value = value.toLowerCase();
            value = Color.namedColors[value] || value;

            return value;
        },

        normalizeByte: function(value) {
            return (value < 0 || isNaN(value)) ? 0 : ((value > 255) ? 255 : value);
        },

        padDigit: function(value) {
            return (value.length === 1) ? "0" + value : value;
        },

        brightness: function(value) {
            var color = this,
                round = math.round;

            color.r = round(color.normalizeByte(color.r * value));
            color.g = round(color.normalizeByte(color.g * value));
            color.b = round(color.normalizeByte(color.b * value));

            return color;
        }
    };

    Color.formats = [{
            re: /^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/,
            process: function(parts) {
                return [
                    parseInt(parts[1], 10), parseInt(parts[2], 10), parseInt(parts[3], 10)
                ];
            }
        }, {
            re: /^(\w{2})(\w{2})(\w{2})$/,
            process: function(parts) {
                return [
                    parseInt(parts[1], 16), parseInt(parts[2], 16), parseInt(parts[3], 16)
                ];
            }
        }, {
            re: /^(\w{1})(\w{1})(\w{1})$/,
            process: function(parts) {
                return [
                    parseInt(parts[1] + parts[1], 16),
                    parseInt(parts[2] + parts[2], 16),
                    parseInt(parts[3] + parts[3], 16)
                ];
            }
        }
    ];

    Color.namedColors = {
        aqua: "00ffff", azure: "f0ffff", beige: "f5f5dc",
        black: "000000", blue: "0000ff", brown: "a52a2a",
        coral: "ff7f50", cyan: "00ffff", darkblue: "00008b",
        darkcyan: "008b8b", darkgray: "a9a9a9", darkgreen: "006400",
        darkorange: "ff8c00", darkred: "8b0000", dimgray: "696969",
        fuchsia: "ff00ff", gold: "ffd700", goldenrod: "daa520",
        gray: "808080", green: "008000", greenyellow: "adff2f",
        indigo: "4b0082", ivory: "fffff0", khaki: "f0e68c",
        lightblue: "add8e6", lightgrey: "d3d3d3", lightgreen: "90ee90",
        lightpink: "ffb6c1", lightyellow: "ffffe0", lime: "00ff00",
        limegreen: "32cd32", linen: "faf0e6", magenta: "ff00ff",
        maroon: "800000", mediumblue: "0000cd", navy: "000080",
        olive: "808000", orange: "ffa500", orangered: "ff4500",
        orchid: "da70d6", pink: "ffc0cb", plum: "dda0dd",
        purple: "800080", red: "ff0000", royalblue: "4169e1",
        salmon: "fa8072", silver: "c0c0c0", skyblue: "87ceeb",
        slateblue: "6a5acd", slategray: "708090", snow: "fffafa",
        steelblue: "4682b4", tan: "d2b48c", teal: "008080",
        tomato: "ff6347", turquoise: "40e0d0", violet: "ee82ee",
        wheat: "f5deb3", white: "ffffff", whitesmoke: "f5f5f5",
        yellow: "ffff00", yellowgreen: "9acd32"
    };

    Chart.Gradients = {
        glass: {
            type: LINEAR,
            rotation: 0,
            stops: [{
                offset: 0,
                color: WHITE,
                opacity: 0
            }, {
                offset: 0.1,
                color: WHITE,
                opacity: 0
            }, {
                offset: 0.25,
                color: WHITE,
                opacity: 0.3
            }, {
                offset: 0.92,
                color: WHITE,
                opacity: 0
            }, {
                offset: 1,
                color: WHITE,
                opacity: 0
            }]
        },
        sharpBevel: {
            type: RADIAL,
            stops: [{
                offset: 0,
                color: WHITE,
                opacity: 0.55
            }, {
                offset: 0.65,
                color: WHITE,
                opacity: 0
            }, {
                offset: 0.95,
                color: WHITE,
                opacity: 0
            }, {
                offset: 0.95,
                color: WHITE,
                opacity: 0.25
            }]
        },
        roundedBevel: {
            type: RADIAL,
            stops: [{
                offset: 0.33,
                color: WHITE,
                opacity: 0.06
            }, {
                offset: 0.83,
                color: WHITE,
                opacity: 0.2
            }, {
                offset: 0.95,
                color: WHITE,
                opacity: 0
            }]
        }
    };

    function updateArray(arr, prop, value) {
        var i,
            length = arr.length;

        for(i = 0; i < length; i++) {
            arr[i][prop] = value;
        }
    }

    function categoriesCount(series) {
        var seriesCount = series.length,
            categories = 0,
            i;

        for (i = 0; i < seriesCount; i++) {
            categories = math.max(categories, series[i].data.length);
        }

        return categories;
    }

    function sqr(value) {
        return value * value;
    }

    jQuery.extend(jQuery.easing, {
        easeOutElastic: function (n, d, first, diff) {
            var s = 1.70158,
                p = 0,
                a = diff;

            if ( n === 0 ) {
                return first;
            }

            if ( n === 1) {
                return first + diff;
            }

            if (!p) {
                p = 0.5;
            }

            if (a < Math.abs(diff)) {
                a=diff;
                s = p / 4;
            } else {
                s = p / (2 * Math.PI) * Math.asin(diff / a);
            }

            return a * Math.pow(2,-10 * n) *
                   Math.sin((n * 1 - s) * (1.1 * Math.PI) / p) +
                   diff + first;
        }
    });

    // Exports ================================================================

    kendo.ui.plugin(Chart);

    deepExtend(Chart, {
        COORD_PRECISION: COORD_PRECISION,
        CLIP: CLIP,
        DEFAULT_WIDTH: DEFAULT_WIDTH,
        DEFAULT_HEIGHT: DEFAULT_HEIGHT,
        DEFAULT_FONT: DEFAULT_FONT,
        defined: defined,
        template: template,
        rotatePoint: rotatePoint,
        round: round,
        supportsSVG: supportsSVG,
        uniqueId: uniqueId,
        Box2D: Box2D,
        Point2D: Point2D,
        Sector: Sector,
        Text: Text,
        BarLabel: BarLabel,
        ChartElement: ChartElement,
        RootElement: RootElement,
        BoxElement: BoxElement,
        TextBox: TextBox,
        NumericAxis: NumericAxis,
        CategoryAxis: CategoryAxis,
        Bar: Bar,
        BarChart: BarChart,
        ShapeElement: ShapeElement,
        LinePoint: LinePoint,
        LineChart: LineChart,
        ClusterLayout: ClusterLayout,
        StackLayout: StackLayout,
        Title: Title,
        Legend: Legend,
        PlotArea: PlotArea,
        Tooltip: Tooltip,
        Highlight: Highlight,
        PieSegment: PieSegment,
        PieChart: PieChart,
        ViewElement: ViewElement,
        ScatterChart: ScatterChart,
        ScatterLineChart: ScatterLineChart,
        ViewBase: ViewBase,
        deepExtend: deepExtend,
        Color: Color,
        measureText: measureText,
        ExpandAnimation: ExpandAnimation,
        BarAnimation: BarAnimation,
        BarAnimationDecorator: BarAnimationDecorator,
        PieAnimation: PieAnimation,
        PieAnimationDecorator: PieAnimationDecorator,
        FadeAnimation: FadeAnimation,
        FadeAnimationDecorator: FadeAnimationDecorator,
        categoriesCount: categoriesCount
    });

})(jQuery);
(function () {

    // Imports ================================================================
    var $ = jQuery,
        kendo = window.kendo,
        Class = kendo.Class,
        Chart = kendo.ui.Chart,
        BarAnimationDecorator = Chart.BarAnimationDecorator,
        PieAnimationDecorator = Chart.PieAnimationDecorator,
        FadeAnimationDecorator = Chart.FadeAnimationDecorator,
        Box2D = Chart.Box2D,
        Point2D = Chart.Point2D,
        ExpandAnimation = Chart.ExpandAnimation,
        ViewBase = Chart.ViewBase,
        ViewElement = Chart.ViewElement,
        deepExtend = Chart.deepExtend,
        defined = Chart.defined,
        template = Chart.template,
        uniqueId = Chart.uniqueId,
        round = Chart.round,
        doc = document,
        math = Math;

    // Constants ==============================================================
    var CLIP = Chart.CLIP,
        COORD_PRECISION = Chart.COORD_PRECISION,
        DEFAULT_WIDTH = Chart.DEFAULT_WIDTH,
        DEFAULT_HEIGHT = Chart.DEFAULT_HEIGHT,
        DEFAULT_FONT = Chart.DEFAULT_FONT,
        GLOBAL_CLIP = "globalClip",
        NONE = "none",
        RADIAL = "radial",
        SQUARE = "square",
        SVG_NS = "http://www.w3.org/2000/svg",
        SVG_DASH_TYPE = {
            dot: [1.5, 3.5],
            dash: [4, 3.5],
            longdash: [8, 3.5],
            dashdot: [3.5, 3.5, 1.5, 3.5],
            longdashdot: [8, 3.5, 1.5, 3.5],
            longdashdotdot: [8, 3.5, 1.5, 3.5, 1.5, 3.5]
        },
        UNDEFINED = "undefined";

    // View ===================================================================
    var SVGView = ViewBase.extend({
        init: function(options) {
            var view = this;

            ViewBase.fn.init.call(view, options);

            view.decorators.push(
                new SVGOverlayDecorator(view),
                new SVGGradientDecorator(view),
                new BarAnimationDecorator(view),
                new PieAnimationDecorator(view),
                new SVGClipAnimationDecorator(view),
                new FadeAnimationDecorator(view)
            );

            view.template = SVGView.template;
            if (!view.template) {
                view.template = SVGView.template = template(
                    "<svg xmlns='" + SVG_NS + "' version='1.1' " +
                    "width='#= d.options.width #px' height='#= d.options.height #px' " +
                    "style='position: relative;'>" +
                    "#= d.renderDefinitions() #" +
                    "#= d.renderContent() #</svg>"
                );
            }
        },

        options: {
            width: DEFAULT_WIDTH,
            height: DEFAULT_HEIGHT,
            idPrefix: ""
        },

        renderTo: function(container) {
            var view = this,
                viewElement;

            view.setupAnimations();

            renderSVG(container, view.render());
            viewElement = container.firstChild;
            view.alignToScreen(viewElement);

            view.playAnimations();

            return viewElement;
        },

        renderDefinitions: function() {
            var view = this,
                output = ViewBase.fn.renderDefinitions.call(view);

            return output.length > 0 ? "<defs>" + output + "</defs>" : "";
        },

        renderElement: function(element) {
            var container = doc.createElement("div"),
                element;

            renderSVG(container,
                "<svg xmlns='" + SVG_NS + "' version='1.1'>" +
                element.render() +
                "</svg>"
            );

            element = container.firstChild.firstChild;

            return element;
        },

        createGroup: function(options) {
            return this.decorate(
                new SVGGroup(options)
            );
        },

        createText: function(content, options) {
            return this.decorate(
                new SVGText(content, options)
            );
        },

        createRect: function(box, style) {
            return this.decorate(
                new SVGLine(box.points(), true, style)
            );
        },

        createLine: function(x1, y1, x2, y2, options) {
            return this.decorate(
                new SVGLine([new Point2D(x1, y1),
                             new Point2D(x2, y2)], false, options)
            );
        },

        createPolyline: function(points, closed, options) {
            return this.decorate(
                new SVGLine(points, closed, options)
            );
        },

        createCircle: function(center, radius, options) {
            return this.decorate(
                new SVGCircle(center, radius, options)
            );
        },

        createSector: function(sector, options) {
            return this.decorate(
                new SVGSector(sector, options)
            );
        },

        createGradient: function(options) {
            if (options.type === RADIAL) {
                return new SVGRadialGradient(options);
            } else {
                return new SVGLinearGradient(options)
            }
        },

        alignToScreen: function(element) {
            try {
                var ctm = element.getScreenCTM ? element.getScreenCTM() : null;
            } catch (e) { }

            if (ctm) {
                var left = - ctm.e % 1,
                    top = - ctm.f % 1,
                    style = element.style;

                if (left !== 0 || top !== 0) {
                    style.left = left + "px";
                    style.top = top + "px";
                }
            }
        }
    });

    SVGView.fromModel = function(model) {
        var view = new SVGView(model.options);
        [].push.apply(view.children, model.getViewElements(view));

        return view;
    }

    // Primitives =============================================================
    var SVGText = ViewElement.extend({
        init: function(content, options) {
            var text = this;
            ViewElement.fn.init.call(text, options);

            text.content = content;
            text.template = SVGText.template;
            if (!text.template) {
                text.template = SVGText.template = template(
                    "<text #= d.renderAttr(\"id\", d.options.id) # " +
                    "x='#= Math.round(d.options.x) #' " +
                    "y='#= Math.round(d.options.y + d.options.baseline) #' " +
                    "fill-opacity='#= d.options.fillOpacity #' " +
                    "#= d.options.rotation ? d.renderRotation() : '' # " +
                    "style='font: #= d.options.font #' fill='#= d.options.color #'>" +
                    "#= d.content #</text>"
                );
            }
        },

        options: {
            x: 0,
            y: 0,
            baseline: 0,
            font: DEFAULT_FONT,
            size: {
                width: 0,
                height: 0
            },
            fillOpacity: 1
        },

        refresh: function(domElement) {
            var options = this.options;

            $(domElement).attr({
                "fill-opacity": options.fillOpacity
            });
        },

        clone: function() {
            var text = this;
            return new SVGText(text.content, deepExtend({}, text.options));
        },

        renderRotation: function() {
            var text = this,
                options = text.options,
                size = options.size,
                cx = round(options.x + size.normalWidth / 2, COORD_PRECISION),
                cy = round(options.y + size.normalHeight / 2, COORD_PRECISION),
                rcx = round(options.x + size.width / 2, COORD_PRECISION),
                rcy = round(options.y + size.height / 2, COORD_PRECISION),
                offsetX = round(rcx - cx, COORD_PRECISION),
                offsetY = round(rcy - cy, COORD_PRECISION);

            return "transform='translate(" + offsetX + "," + offsetY + ") " +
                   "rotate(" + options.rotation + "," + cx + "," + cy + ")'";
        }
    });

    var SVGPath = ViewElement.extend({
        init: function(options) {
            var path = this;
            ViewElement.fn.init.call(path, options);

            path.template = SVGPath.template;
            if (!path.template) {
                path.template = SVGPath.template = template(
                    "<path #= d.renderAttr(\"id\", d.options.id) #" +
                    "d='#= d.renderPoints() #' " +
                    "#= d.renderAttr(\"stroke\", d.options.stroke) # " +
                    "#= d.renderAttr(\"stroke-width\", d.options.strokeWidth) #" +
                    "#= d.renderDashType() # " +
                    "stroke-linecap='#= d.renderLinecap() #' " +
                    "stroke-linejoin='round' " +
                    "fill-opacity='#= d.options.fillOpacity #' " +
                    "stroke-opacity='#= d.options.strokeOpacity #' " +
                    "fill='#= d.options.fill || \"none\" #'></path>"
                );
            }
        },

        options: {
            fill: "",
            fillOpacity: 1,
            strokeOpacity: 1
        },

        refresh: function(domElement) {
            var options = this.options;

            $(domElement).attr({
                "d": this.renderPoints(),
                "fill-opacity": options.fillOpacity,
                "stroke-opacity": options.strokeOpacity
            });
        },

        clone: function() {
            var path = this;
            return new SVGPath(deepExtend({}, path.options));
        },

        renderPoints: function() {
            // Overriden by inheritors
        },

        renderDashType: function () {
            var path = this,
                options = path.options;

            return renderSVGDash(options.dashType, options.strokeWidth);
        },

        renderLinecap: function() {
            var dashType = this.options.dashType;

            return (dashType && dashType != "solid") ? "butt" : "square";
        }
    });

    var SVGLine = SVGPath.extend({
        init: function(points, closed, options) {
            var line = this;
            SVGPath.fn.init.call(line, options);

            line.points = points;
            line.closed = closed;
        },

        renderPoints: function() {
            var line = this,
                points = line.points,
                i,
                count = points.length,
                first = points[0],
                result = "M" + line._print(first);

            for (i = 1; i < count; i++) {
                result += " " + line._print(points[i]);
            }

            if (line.closed) {
                result += " z";
            }

            return result;
        },

        clone: function() {
            var line = this;
            return new SVGLine(
                deepExtend([], line.points), line.closed,
                deepExtend({}, line.options)
            );
        },

        _print: function(point) {
            var line = this,
                strokeWidth = line.options.strokeWidth,
                shouldAlign = strokeWidth && strokeWidth % 2 !== 0,
                align = shouldAlign ? alignToPixel : math.round;

            return align(point.x) + " " + align(point.y);
        }
    });

    var SVGSector = SVGPath.extend({
        init: function(circleSector, options) {
            var sector = this;
            SVGPath.fn.init.call(sector, options);

            sector.pathTemplate = SVGSector.pathTemplate;
            if (!sector.pathTemplate) {
                sector.pathTemplate = SVGSector.pathTemplate = template(
                    "M #= d.firstPoint.x # #= d.firstPoint.y # " +
                    "A#= d.r # #= d.r # " +
                    "0 #= d.isReflexAngle ? '1' : '0' #,1 " +
                    "#= d.secondPoint.x # #= d.secondPoint.y # " +
                    "L #= d.cx # #= d.cy # z"
                );
            }

            sector.circleSector = circleSector || {};
        },

        options: {
            fill: "",
            fillOpacity: 1,
            strokeOpacity: 1,
            strokeLineCap: SQUARE
        },

        clone: function() {
            var sector = this;
            return new SVGSector(
                deepExtend({}, sector.circleSector),
                deepExtend({}, sector.options)
            );
        },

        renderPoints: function() {
            var sector = this,
                circleSector = sector.circleSector,
                startAngle = circleSector.startAngle,
                endAngle = circleSector.angle + startAngle,
                endAngle = (endAngle - startAngle) == 360 ? endAngle - 0.001 : endAngle,
                isReflexAngle = (endAngle - startAngle) > 180,
                r = math.max(circleSector.r, 0),
                cx = circleSector.c.x,
                cy = circleSector.c.y,
                firstPoint = circleSector.point(startAngle),
                secondPoint = circleSector.point(endAngle);

            return sector.pathTemplate({
                firstPoint: firstPoint,
                secondPoint: secondPoint,
                isReflexAngle: isReflexAngle,
                r: r,
                cx: cx,
                cy: cy
            });
        }
    });

    var SVGCircle = ViewElement.extend({
        init: function(center, radius, options) {
            var circle = this;
            ViewElement.fn.init.call(circle, options);

            circle.center = center;
            circle.radius = radius;

            circle.template = SVGCircle.template;
            if (!circle.template) {
                circle.template = SVGCircle.template = template(
                    "<circle #= d.renderAttr(\"id\", d.options.id) # " +
                    "cx='#= d.center[0] #' cy='#= d.center[1] #' " +
                    "r='#= d.radius #' " +
                    "#= d.renderAttr(\"stroke\", d.options.stroke) # " +
                    "#= d.renderAttr(\"stroke-width\", d.options.strokeWidth) #" +
                    "fill-opacity='#= d.options.fillOpacity #' " +
                    "stroke-opacity='#= d.options.strokeOpacity #'  " +
                    "fill='#= d.options.fill || \"none\" #'></circle>"
                );
            }
        },

        options: {
            fill: "",
            fillOpacity: 1,
            strokeOpacity: 1
        }
    });

    var SVGGroup = ViewElement.extend({
        init: function(options) {
            var group = this;
            ViewElement.fn.init.call(group, options);

            group.template = SVGGroup.template;
            if (!group.template) {
                group.template = SVGGroup.template =
                template("<g#= d.renderAttr(\"id\", d.options.id) #" +
                           "#= d.renderAttr(\"clip-path\", d.options.clipPath) #>" +
                         "#= d.renderContent() #</g>");
            }
        }
    });

    var SVGClipPath = ViewElement.extend({
        init: function(options) {
            var clip = this;
            ViewElement.fn.init.call(clip, options);

            clip.template = SVGClipPath.template;
            if (!clip.template) {
                clip.template = SVGClipPath.template =
                template("<clipPath#= d.renderAttr(\"id\", d.options.id) #>" +
                         "#= d.renderContent() #</clipPath>");
            }
        }
    });

    var SVGLinearGradient = ViewElement.extend({
        init: function(options) {
            var gradient = this;
            ViewElement.fn.init.call(gradient, options);

            gradient.template = SVGLinearGradient.template;
            gradient.stopTemplate = SVGLinearGradient.stopTemplate;
            if (!gradient.template) {
                gradient.template = SVGLinearGradient.template = template(
                    "<linearGradient id='#= d.options.id #' " +
                    "gradientTransform='rotate(#= d.options.rotation #)'> " +
                    "#= d.renderStops() #" +
                    "</linearGradient>"
                );

                gradient.stopTemplate = SVGLinearGradient.stopTemplate = template(
                    "<stop offset='#= Math.round(d.offset * 100) #%' " +
                    "style='stop-color:#= d.color #;stop-opacity:#= d.opacity #' />");
            }
        },

        options: {
            id: "",
            rotation: 0
        },

        renderStops: function() {
            var gradient = this,
                stops = gradient.options.stops,
                stopTemplate = gradient.stopTemplate,
                i,
                length = stops.length,
                currentStop,
                output = '';

            for (i = 0; i < length; i++) {
                currentStop = stops[i];
                output += stopTemplate(currentStop);
            }

            return output;
        }
    });

    var SVGRadialGradient = ViewElement.extend({
        init: function(options) {
            var gradient = this;

            ViewElement.fn.init.call(gradient, options);

            gradient.template = SVGRadialGradient.template;
            gradient.stopTemplate = SVGRadialGradient.stopTemplate;
            if (!gradient.template) {
                gradient.template = SVGRadialGradient.template = template(
                    "<radialGradient id='#= d.options.id #' " +
                    "cx='#= d.options.cx #' cy='#= d.options.cy #' " +
                    "fx='#= d.options.cx #' fy='#= d.options.cy #' " +
                    "r='#= d.options.r #' gradientUnits='userSpaceOnUse'>" +
                    "#= d.renderStops() #" +
                    "</radialGradient>"
                );

                gradient.stopTemplate = SVGRadialGradient.stopTemplate = template(
                    "<stop offset='#= Math.round(d.offset * 100) #%' " +
                    "style='stop-color:#= d.color #;stop-opacity:#= d.opacity #' />");
            }
        },

        options: {
            id: "",
            rotation: 0
        },

        renderStops: function() {
            var gradient = this,
                stops = gradient.options.stops,
                stopTemplate = gradient.stopTemplate,
                length = stops.length,
                currentStop,
                output = '',
                i;

            for (i = 0; i < length; i++) {
                currentStop = stops[i];
                output += stopTemplate(currentStop);
            }

            return output;
        }
    });

    // Decorators =============================================================
    function SVGOverlayDecorator(view) {
        this.view = view;
    }

    SVGOverlayDecorator.prototype = {
        decorate: function(element) {
            var decorator = this,
                view = decorator.view,
                options = element.options,
                id = options.id,
                group,
                overlay;

            if (options.overlay) {
                element.options.id = uniqueId();

                group = view.createGroup();
                overlay = element.clone();

                group.children.push(element, overlay);

                overlay.options.id = id;
                overlay.options.fill = options.overlay;

                return group;
            } else {
                return element;
            }
        }
    }

    function SVGGradientDecorator(view) {
        this.view = view;
    }

    SVGGradientDecorator.prototype = {
        decorate: function(element) {
            var decorator = this,
                options = element.options;

            options.fill = decorator.getPaint(options.fill);

            return element;
        },

        getPaint: function(paint) {
            var decorator = this,
                view = decorator.view,
                definitions = view.definitions,
                overlay,
                overlayId,
                gradient;

            if (paint && defined(paint.gradient)) {
                overlay = view.buildGradient(paint);
                if (overlay) {
                    overlayId = overlay.id;
                    gradient = definitions[overlayId];
                    if (!gradient) {
                        gradient = view.createGradient(overlay);
                        definitions[overlayId] = gradient;
                    }

                    return "url(#" + gradient.options.id + ")";
                } else {
                    return NONE;
                }
            } else {
                return paint;
            }
        }
    };

    var SVGClipAnimationDecorator = Class.extend({
        init: function(view) {
            this.view = view;
        },

        decorate: function(element) {
            var decorator = this,
                view = decorator.view,
                options = view.options,
                animation = element.options.animation,
                definitions = view.definitions,
                clipPath = definitions[GLOBAL_CLIP],
                clipRect;

            if (animation && animation.type === CLIP && options.transitions) {
                if (!clipPath) {
                    clipPath = new SVGClipPath({ id: GLOBAL_CLIP });
                    clipRect = view.createRect(
                        new Box2D(0, 0, options.width, options.height), { id: uniqueId() });
                    clipPath.children.push(clipRect);
                    definitions[GLOBAL_CLIP] = clipPath;

                    view.animations.push(
                        new ExpandAnimation(clipRect, { size: options.width })
                    );
                }

                element.options.clipPath = "url(#" + GLOBAL_CLIP + ")";
            }

            return element;
        }
    });

    // Helpers ================================================================
    function alignToPixel(coord) {
        return math.round(coord) + 0.5;
    }

    function renderSVGDash(dashType, strokeWidth) {
        var result = [],
            dashType = dashType ? dashType.toLowerCase() : null,
            dashTypeArray,
            i;

        if (dashType && dashType != "solid" && strokeWidth) {
            dashTypeArray = SVG_DASH_TYPE[dashType];
            for (i = 0; i < dashTypeArray.length; i++) {
                result.push(dashTypeArray[i] * strokeWidth);
            }

            return "stroke-dasharray='" + result.join(" ") + "' ";
        }

        return "";
    }

    function renderSVG(container, svg) {
        container.innerHTML = svg;
    }

    (function() {
        var testFragment = "<svg xmlns='" + SVG_NS + "'></svg>",
            testContainer = doc.createElement("div"),
            hasParser = typeof DOMParser != UNDEFINED;

        testContainer.innerHTML = testFragment;

        if (hasParser && testContainer.firstChild.namespaceURI != SVG_NS) {
            renderSVG = function(container, svg) {
                var parser = new DOMParser(),
                    chartDoc = parser.parseFromString(svg, "text/xml"),
                    importedDoc = doc.adoptNode(chartDoc.documentElement);

                container.innerHTML = "";
                container.appendChild(importedDoc);
            };
        }
    })();

    // Exports ================================================================
    deepExtend(Chart, {
        SVGView: SVGView,
        SVGText: SVGText,
        SVGPath: SVGPath,
        SVGLine: SVGLine,
        SVGSector: SVGSector,
        SVGCircle: SVGCircle,
        SVGGroup: SVGGroup,
        SVGClipPath: SVGClipPath,
        SVGLinearGradient: SVGLinearGradient,
        SVGRadialGradient: SVGRadialGradient,
        SVGOverlayDecorator: SVGOverlayDecorator,
        SVGGradientDecorator: SVGGradientDecorator,
        SVGClipAnimationDecorator: SVGClipAnimationDecorator
    });

})(jQuery);
(function () {

    // Imports ================================================================
    var $ = jQuery,
        kendo = window.kendo,
        Class = kendo.Class,
        Chart = kendo.ui.Chart,
        Color = Chart.Color,
        Box2D = Chart.Box2D,
        Point2D = Chart.Point2D,
        BarAnimationDecorator = Chart.BarAnimationDecorator,
        PieAnimationDecorator = Chart.PieAnimationDecorator,
        FadeAnimationDecorator = Chart.FadeAnimationDecorator,
        ExpandAnimation = Chart.ExpandAnimation,
        ViewBase = Chart.ViewBase,
        ViewElement = Chart.ViewElement,
        deepExtend = Chart.deepExtend,
        template = Chart.template,
        uniqueId = Chart.uniqueId,
        rotatePoint = Chart.rotatePoint,
        round = Chart.round,
        supportsSVG = Chart.supportsSVG,
        doc = document,
        math = Math;

    // Constants ==============================================================
    var BLACK = "#000",
        CLIP = Chart.CLIP,
        DEFAULT_WIDTH = Chart.DEFAULT_WIDTH,
        DEFAULT_HEIGHT = Chart.DEFAULT_HEIGHT,
        DEFAULT_FONT = Chart.DEFAULT_FONT,
        OBJECT = "object",
        RADIAL = "radial";

    // View ===================================================================
    var VMLView = ViewBase.extend({
        init: function(options) {
            var view = this;
            ViewBase.fn.init.call(view, options);

            view.decorators.push(
                new VMLOverlayDecorator(view),
                new VMLGradientDecorator(view),
                new BarAnimationDecorator(view),
                new PieAnimationDecorator(view),
                new VMLClipAnimationDecorator(view)
            );

            if (!isIE9CompatibilityView()) {
                // Setting opacity on VML elements is broken in
                // IE9 Compatibility View
                view.decorators.push(
                    new FadeAnimationDecorator(view)
                );
            }

            view.template = VMLView.template;
            if (!view.template) {
                view.template = VMLView.template = template(
                    "<div style='width:#= d.options.width #px; " +
                    "height:#= d.options.height #px; " +
                    "position: relative;'>" +
                    "#= d.renderContent() #</div>"
                );
            }
        },

        options: {
            width: DEFAULT_WIDTH,
            height: DEFAULT_HEIGHT
        },

        renderTo: function(container) {
            var view = this;

            if (doc.namespaces) {
                doc.namespaces.add("kvml", "urn:schemas-microsoft-com:vml", "#default#VML");
            }

            view.setupAnimations();
            container.innerHTML = view.render();
            view.playAnimations();

            return container.firstChild;
        },

        renderElement: function(element) {
            var container = doc.createElement("div"),
                element;

            container.style.display = "none";
            doc.body.appendChild(container);
            container.innerHTML = element.render();

            element = container.firstChild;
            doc.body.removeChild(container);

            return element;
        },

        createText: function(content, options) {
            return this.decorate(
                (options && options.rotation) ?
                    new VMLRotatedText(content, options) :
                    new VMLText(content, options)
            );
        },

        createRect: function(box, style) {
            return this.decorate(
                new VMLLine(box.points(), true, style)
            );
        },

        createLine: function(x1, y1, x2, y2, options) {
            return this.decorate(
                new VMLLine([new Point2D(x1, y1),
                    new Point2D(x2, y2)], false, options)
            );
        },

        createPolyline: function(points, closed, options) {
            return this.decorate(
                new VMLLine(points, closed, options)
            );
        },

        createCircle: function(center, radius, options) {
            return this.decorate(
                new VMLCircle(center, radius, options)
            );
        },

        createSector: function(sector, options) {
            return this.decorate(
                new VMLSector(sector, options)
            );
        },

        createGroup: function(options) {
            return this.decorate(
                new VMLGroup(options)
            );
        },

        createGradient: function(options) {
            return new VMLLinearGradient(options);
        }
    });

    VMLView.fromModel = function(model) {
        var view = new VMLView(model.options);
        [].push.apply(view.children, model.getViewElements(view));

        return view;
    }

    // Primitives =============================================================
    var VMLText = ViewElement.extend({
        init: function(content, options) {
            var text = this;
            ViewElement.fn.init.call(text, options);

            text.content = content;
            text.template = VMLText.template;
            if (!text.template) {
                text.template = VMLText.template = template(
                    "<kvml:textbox #= d.renderAttr(\"id\", d.options.id) # " +
                    "style='position: absolute; " +
                    "left: #= d.options.x #px; top: #= d.options.y #px; " +
                    "font: #= d.options.font #; color: #= d.options.color #; " +
                    "visibility: #= d.renderVisibility() #; white-space: nowrap;'>" +
                    "#= d.content #</kvml:textbox>"
                );
            }
        },

        options: {
            x: 0,
            y: 0,
            font: DEFAULT_FONT,
            color: BLACK,
            fillOpacity: 1
        },

        refresh: function(domElement) {
            $(domElement).css("visibility", this.renderVisibility());
        },

        clone: function() {
            var text = this;
            return new VMLText(text.content, deepExtend({}, text.options));
        },

        renderVisibility: function() {
            return this.options.fillOpacity > 0 ? "visible" : "hidden";
        }
    });

    var VMLRotatedText = ViewElement.extend({
        init: function(content, options) {
            var text = this;
            ViewElement.fn.init.call(text, options);

            text.content = content;
            text.template = VMLRotatedText.template;
            if (!text.template) {
                text.template = VMLRotatedText.template = template(
                    "<kvml:shape #= d.renderAttr(\"id\", d.options.id) # " +
                    "style='position: absolute; top: 0px; left: 0px; " +
                    "width: 1px; height: 1px;' stroked='false' coordsize='1,1'>" +
                    "#= d.renderPath() #" +
                    "<kvml:fill color='#= d.options.color #' />" +
                    "<kvml:textpath on='true' style='font: #= d.options.font #;' " +
                    "fitpath='false' string='#= d.content #' /></kvml:shape>"
                );
            }
        },

        options: {
            x: 0,
            y: 0,
            font: DEFAULT_FONT,
            color: BLACK,
            size: {
                width: 0,
                height: 0
            }
        },

        renderPath: function() {
            var text = this,
                options = text.options,
                width = options.size.width,
                height = options.size.height,
                cx = options.x + width / 2,
                cy = options.y + height / 2,
                angle = -options.rotation,
                r1 = rotatePoint(options.x, cy, cx, cy, angle),
                r2 = rotatePoint(options.x + width, cy, cx, cy, angle);

            return "<kvml:path textpathok='true' " +
                   "v='m " + round(r1.x) + "," + round(r1.y) +
                   " l " + round(r2.x) + "," + round(r2.y) +
                   "' />";
        }
    });

    var VMLStroke = ViewElement.extend({
        init: function(options) {
            var stroke = this;
            ViewElement.fn.init.call(stroke, options);

            stroke.template = VMLStroke.template;
            if (!stroke.template) {
                stroke.template = VMLStroke.template = template(
                    "<kvml:stroke on='#= !!d.options.stroke #' " +
                    "#= d.renderAttr(\"color\", d.options.stroke) #" +
                    "#= d.renderAttr(\"weight\", d.options.strokeWidth) #" +
                    "#= d.renderAttr(\"dashstyle\", d.options.dashType) #" +
                    "#= d.renderAttr(\"opacity\", d.options.strokeOpacity) # />"
                );
            }
        }
    });

    var VMLFill = ViewElement.extend({
        init: function(options) {
            var stroke = this;
            ViewElement.fn.init.call(stroke, options);

            stroke.template = VMLFill.template;
            if (!stroke.template) {
                stroke.template = VMLFill.template = template(
                    "<kvml:fill on='#= !!d.options.fill #' " +
                    "#= d.renderAttr(\"color\", d.options.fill) #" +
                    "#= d.renderAttr(\"weight\", d.options.fillWidth) #" +
                    "#= d.renderAttr(\"opacity\", d.options.fillOpacity) # />"
                );
            }
        }
    });

    var VMLPath = ViewElement.extend({
        init: function(options) {
            var path = this;
            ViewElement.fn.init.call(path, options);

            path.template = VMLPath.template;
            if (!path.template) {
                path.template = VMLPath.template = template(
                    "<kvml:shape #= d.renderAttr(\"id\", d.options.id) # " +
                    "style='position:absolute; width:1px; height:1px;' " +
                    "coordorigin='0 0' coordsize='1 1'>" +
                        "<kvml:path v='#= d.renderPoints() # e' />" +
                        "#= d.fill.render() + d.stroke.render() #" +
                    "</kvml:shape>"
                );
            }

            path.stroke = new VMLStroke(path.options);
            path.fill = new VMLFill(path.options);
        },

        options: {
            fill: "",
            fillOpacity: 1,
            strokeOpacity: 1
        },

        render: function() {
            var path = this;
            path.fill.options.fillOpacity = path.options.fillOpacity;
            path.stroke.options.strokeOpacity = path.options.strokeOpacity;

            return ViewElement.fn.render.call(path);
        },

        renderPoints: function() {
            // Overriden by inheritors
        },

        refresh: function(domElement) {
            var path = this,
                options = path.options,
                element = $(domElement),
                parentNode = element[0].parentNode;

            if (parentNode) {
                element.find("path")[0].v = this.renderPoints();
                try {
                    element.find("fill")[0].opacity = options.fillOpacity;
                    element.find("stroke")[0].opacity = options.strokeOpacity;
                } catch(e) {
                    // Random exceptions in IE 8 Compatibility View
                }

                // Force redraw in order to remove artifacts in IE < 7
                parentNode.style.cssText = parentNode.style.cssText;
            }
        }
    });

    var VMLLine = VMLPath.extend({
        init: function(points, closed, options) {
            var line = this;
            VMLPath.fn.init.call(line, options);

            line.points = points;
            line.closed = closed;
        },

        renderPoints: function() {
            var line = this,
                points = line.points,
                i,
                count = points.length,
                result = "m " + line._print(points[0]);

            if (count > 1) {
                result += " l ";

                for (i = 1; i < count; i++) {
                    result += line._print(points[i]);

                    if (i < count - 1) {
                        result += ", ";
                    }
                }
            }

            if (line.closed) {
                result += " x";
            }

            return result;
        },

        clone: function() {
            var line = this;
            return new VMLLine(
                deepExtend([], line.points), line.closed,
                deepExtend({}, line.options)
            );
        },

        _print: function(point) {
            return math.round(point.x) + "," + math.round(point.y);
        }
    });

    var VMLSector = VMLPath.extend({
        init: function(circleSector, options) {
            var sector = this;
            VMLPath.fn.init.call(sector, options);

            sector.pathTemplate = VMLSector.pathTemplate;
            if (!sector.pathTemplate) {
                sector.pathTemplate = VMLSector.pathTemplate = template(
                   "M #= d.cx # #= d.cy # " +
                   "AE #= d.cx # #= d.cy # " +
                   "#= d.r # #= d.r # " +
                   "#= d.sa # #= d.a # X E"
                );
            }

            sector.circleSector = circleSector;
        },

        renderPoints: function() {
            var sector = this,
                circleSector = sector.circleSector,
                r = math.max(round(circleSector.r), 0),
                cx = round(circleSector.c.x),
                cy = round(circleSector.c.y),
                sa = -round((circleSector.startAngle + 180) * 65535),
                a = -round(circleSector.angle * 65536);

            return sector.pathTemplate({ r: r, cx: cx, cy: cy, sa: sa, a: a });
        },

        clone: function() {
            var sector = this;
            return new VMLSector(
                deepExtend({}, sector.circleSector),
                deepExtend({}, sector.options)
            );
        }
    });

    var VMLCircle = ViewElement.extend({
        init: function(center, radius, options) {
            var circle = this;
            ViewElement.fn.init.call(circle, options);

            circle.center = center;
            circle.radius = radius;

            circle.template = VMLCircle.template;
            if (!circle.template) {
                circle.template = VMLCircle.template = template(
                    "<kvml:oval #= d.renderAttr(\"id\", d.options.id) # " +
                            "style='position:absolute; " +
                            "width:#= d.radius * 2 #px; height:#= d.radius * 2 #px; " +
                            "top:#= d.center[1] - d.radius #px; " +
                            "left:#= d.center[0] - d.radius #px;'>" +
                        "#= d.fill.render() + d.stroke.render() #" +
                    "</kvml:oval>"
                );
            }

            circle.stroke = new VMLStroke(circle.options);
            circle.fill = new VMLFill(circle.options);
        },

        options: {
            fill: ""
        }
    });

    var VMLGroup = ViewElement.extend({
        init: function(options) {
            var group = this;
            ViewElement.fn.init.call(group, options);

            group.template = VMLGroup.template;
            if (!group.template) {
                group.template = VMLGroup.template = template(
                    "<div #= d.renderAttr(\"id\", d.options.id) #" +
                    "style='position: absolute; white-space: nowrap;'>" +
                    "#= d.renderContent() #</div>"
                );
            }
        }
    });

    var VMLClipRect = ViewElement.extend({
        init: function(box, options) {
            var clipRect = this;
            ViewElement.fn.init.call(clipRect, options);

            clipRect.template = VMLClipRect.template;
            clipRect.clipTemplate = VMLClipRect.clipTemplate;
            if (!clipRect.template) {
                clipRect.template = VMLClipRect.template = template(
                    "<div #= d.renderAttr(\"id\", d.options.id) #" +
                        "style='position:absolute; " +
                        "width:#= d.box.width() #px; height:#= d.box.height() #px; " +
                        "top:#= d.box.y1 #px; " +
                        "left:#= d.box.x1 #px; " +
                        "clip:#= d._renderClip() #;' >" +
                    "#= d.renderContent() #</div>"
                );

                clipRect.clipTemplate = VMLClipRect.clipTemplate = template(
                    "rect(#= d.points[0].y #px #= d.points[1].x #px " +
                         "#= d.points[2].y #px #= d.points[0].x #px)"
                );
            }

            clipRect.box = box;

            // Points defining the clipping rectangle
            clipRect.points = box.points();
        },

        clone: function() {
            var clipRect = this;
            return new VMLClipRect(
                clipRect.box, deepExtend({}, clipRect.options)
            );
        },

        refresh: function(domElement) {
            $(domElement).css(CLIP, this._renderClip());
        },

        _renderClip: function() {
            return this.clipTemplate(this);
        }
    });

    var VMLLinearGradient = ViewElement.extend({
        init: function(options) {
            var gradient = this;
            ViewElement.fn.init.call(gradient, options);

            gradient.template = VMLLinearGradient.template;
            if (!gradient.template) {
                gradient.template = VMLLinearGradient.template = template(
                    "<kvml:fill type='gradient' angle='#= 270 - d.options.rotation #' " +
                    "colors='#= d.renderColors() #' opacity='#= d.options.opacity #' />"
                );
            }
        },

        options: {
            rotation: 0,
            opacity: 1
        },

        renderColors: function() {
            var gradient = this,
                options = gradient.options,
                stops = options.stops,
                currentStop,
                i,
                length = stops.length,
                output = [],
                round = math.round;

            for (i = 0; i < length; i++) {
                currentStop = stops[i];
                output.push(
                    round(currentStop.offset * 100) + "% " +
                    currentStop.color
                );
            }

            return output.join(",");
        }
    });

    // Decorators =============================================================
    function VMLOverlayDecorator(view) {
        this.view = view;
    }

    VMLOverlayDecorator.prototype = {
        decorate: function(element) {
            var options = element.options,
                view = this.view,
                overlay;

            if (options.overlay) {
                overlay = view.buildGradient(
                    deepExtend({}, options.overlay, {
                        // Make the gradient definition unique for this color
                        _overlayFill: options.fill
                    })
                );
            }

            if (!overlay || overlay.type === RADIAL) {
                return element;
            }

            delete options.overlay;
            options.fill = deepExtend(
                blendGradient(options.fill, overlay),
                { opacity: options.fillOpacity }
            );

            return element;
        }
    };

    function VMLGradientDecorator(view) {
        this.view = view;
    }

    VMLGradientDecorator.prototype = {
        decorate: function(element) {
            var decorator = this,
                view = decorator.view,
                options = element.options,
                fill = options.fill;

            if (fill) {
                if (fill.gradient) {
                    fill = view.buildGradient(fill);
                }

                if (typeof fill === OBJECT) {
                    element.fill = view.createGradient(fill);
                }
            }

            return element;
        }
    };

    var VMLClipAnimationDecorator = Class.extend({
        init: function(view) {
            this.view = view;
        },

        decorate: function(element) {
            var decorator = this,
                view = decorator.view,
                options = view.options,
                animation = element.options.animation,
                clipRect;

            if (animation && animation.type === CLIP && options.transitions) {
                clipRect = new VMLClipRect(
                    new Box2D(0, 0, 0, options.height),
                    { id: uniqueId() }
                );

                view.animations.push(
                    new ExpandAnimation(clipRect, { size: options.width })
                );

                clipRect.children.push(element);

                return clipRect;
            } else {
                return element;
            }
        }
    });

    // Helpers ================================================================
    function isIE9CompatibilityView() {
        return $.browser.msie && !supportsSVG() && typeof window.performance !== "undefined";
    }

    function blendColors(base, overlay, alpha) {
        var baseColor = new Color(base),
            overlayColor = new Color(overlay),
            r = blendChannel(baseColor.r, overlayColor.r, alpha),
            g = blendChannel(baseColor.g, overlayColor.g, alpha),
            b = blendChannel(baseColor.b, overlayColor.b, alpha);

        return new Color(r, g, b).toHex();
    }

    function blendChannel(a, b, alpha) {
        return math.round(alpha * b + (1 - alpha) * a);
    }

    function blendGradient(color, gradient) {
        var srcStops = gradient.stops,
            stopsLength = srcStops.length,
            result = deepExtend({}, gradient),
            i,
            stop,
            resultStop;

        result.stops = [];

        for (i = 0; i < stopsLength; i++) {
            stop = srcStops[i];
            resultStop = result.stops[i] = deepExtend({}, srcStops[i]);
            resultStop.color = blendColors(color, stop.color, stop.opacity);
            resultStop.opacity = 0;
        }

        return result;
    }

    // Exports ================================================================
    deepExtend(Chart, {
        VMLView: VMLView,
        VMLText: VMLText,
        VMLRotatedText: VMLRotatedText,
        VMLStroke: VMLStroke,
        VMLFill: VMLFill,
        VMLPath: VMLPath,
        VMLLine: VMLLine,
        VMLSector: VMLSector,
        VMLCircle: VMLCircle,
        VMLGroup: VMLGroup,
        VMLClipRect: VMLClipRect,
        VMLLinearGradient: VMLLinearGradient,
        VMLOverlayDecorator: VMLOverlayDecorator,
        VMLClipAnimationDecorator: VMLClipAnimationDecorator,
        blendColors: blendColors,
        blendGradient: blendGradient
    });

})(jQuery);
(function () {

    // Imports ================================================================
    var kendo = window.kendo,
        Chart = kendo.ui.Chart,
        deepExtend = Chart.deepExtend;

    // Constants ==============================================================
    var BLACK = "#000",
        SANS = "Arial,Helvetica,sans-serif",
        SANS11 = "11px " + SANS,
        SANS12 = "12px " + SANS,
        SANS16 = "16px " + SANS,
        WHITE = "#fff";

    // Kendo themes ===========================================================
    var baseTheme = {
            title: {
                font: SANS16
            },
            legend: {
                labels: {
                    font: SANS12
                }
            },
            seriesDefaults: {
                labels: {
                    font: SANS11
                }
            },
            categoryAxis: {
                labels: {
                    font: SANS12
                }
            },
            valueAxis: {
                labels: {
                    font: SANS12
                }
            },
            tooltip: {
                font: SANS12
            }
        };

    var themes = { };

    themes.black = deepExtend({}, baseTheme, {
        title: {
            color: WHITE
        },
        legend: {
            labels: {
                color: WHITE
            }
        },
        seriesDefaults: {
            labels: {
                color: WHITE
            },
            pie: {
                highlight: {
                    opacity: 0.6,
                    color: "#3d3d3d",
                    border: {
                        width: 0.5,
                        opacity: 0.9,
                        color: "#000"
                    }
                },
                overlay: {
                    gradient: "sharpBevel"
                }
            },
            line: {
                markers: {
                    background: "#3d3d3d"
                }
            },
            scatter: {
                markers: {
                    background: "#3d3d3d"
                }
            },
            scatterLine: {
                markers: {
                    background: "#3d3d3d"
                }
            }
        },
        chartArea: {
            background: "#3d3d3d"
        },
        seriesColors: ["#0081da", "#3aafff", "#99c900", "#ffeb3d", "#b20753", "#ff4195"],
        categoryAxis: {
            majorGridLines: {
                visible: true
            }
        },
        axisDefaults: {
            line: {
                color: "#8e8e8e"
            },
            labels: {
                color: WHITE
            },
            majorGridLines: {
                color: "#545454"
            },
            minorGridLines: {
                color: "#454545"
            }
        },
        tooltip: {
            background: "#3d3d3d",
            color: WHITE,
            opacity: 0.8
        }
    });

    themes["default"] = deepExtend({}, baseTheme, {
        title: {
            color: "#8e8e8e"
        },
        legend: {
            labels: {
                color: "#232323"
            }
        },
        seriesDefaults: {
            labels: {
                color: BLACK,
                background: WHITE,
                opacity: 0.5
            }
        },
        seriesColors: ["#ff6800", "#a0a700", "#ff8d00", "#678900", "#ffb53c", "#396000"],
        categoryAxis: {
            majorGridLines: {
                visible: true
            }
        },
        axisDefaults: {
            line: {
                color: "#8e8e8e"
            },
            labels: {
                color: "#232323"
            },
            minorGridLines: {
                color: "#f0f0f0"
            },
            majorGridLines: {
                color: "#dfdfdf"
            }
        },
        tooltip: {
            background: WHITE,
            color: BLACK,
            opacity: 0.8
        }
    });

    themes.blueopal = deepExtend({}, baseTheme, {
        title: {
            color: "#293135"
        },
        legend: {
            labels: {
                color: "#293135"
            }
        },
        seriesDefaults: {
            labels: {
                color: BLACK,
                background: WHITE,
                opacity: 0.5
            }
        },
        seriesColors: ["#0069a5", "#0098ee", "#7bd2f6", "#ffb800", "#ff8517", "#e34a00"],
        categoryAxis: {
            majorGridLines: {
                visible: true
            }
        },
        axisDefaults: {
            line: {
                color: "#9aabb2"
            },
            labels: {
                color: "#293135"
            },
            majorGridLines: {
                color: "#c4d0d5"
            },
            minorGridLines: {
                color: "#edf1f2"
            }
        },
        tooltip: {
            background: WHITE,
            color: BLACK,
            opacity: 0.8
        }
    });

    themes.silver = deepExtend({}, baseTheme, {
        title: {
            color: "#4e5968"
        },
        legend: {
            labels: {
                color: "#4e5968"
            }
        },
        seriesDefaults: {
            labels: {
                color: "#293135",
                background: "#eaeaec",
                opacity: 0.5
            },
            pie: {
                connectors: {
                    color: "#A6B1C0"
                }
            }
        },
        chartArea: {
            background: "#eaeaec"
        },
        seriesColors: ["#007bc3", "#76b800", "#ffae00", "#ef4c00", "#a419b7", "#430B62"],
        categoryAxis: {
            majorGridLines: {
                visible: true
            }
        },
        axisDefaults: {
            line: {
                color: "#a6b1c0"
            },
            labels: {
                color: "#4e5968"
            },
            majorGridLines: {
                color: "#dcdcdf"
            },
            minorGridLines: {
                color: "#eeeeef"
            }
        },
        tooltip: {
            background: WHITE,
            color: "#4e5968",
            opacity: 0.8
        }
    });

    themes.metro = deepExtend({}, baseTheme, {
        title: {
            color: "#777777"
        },
        legend: {
            labels: {
                color: "#777777"
            }
        },
        seriesDefaults: {
            labels: {
                color: "#000000"
            }
        },
        seriesColors: ["#25a0da", "#309b46", "#8ebc00", "#ff6900", "#e61e26", "#d8e404", "#16aba9", "#7e51a1", "#313131", "#ed1691"],
        categoryAxis: {
            majorGridLines: {
                visible: true
            }
        },
        axisDefaults: {
            line: {
                color: "#c7c7c7"
            },
            labels: {
                color: "#777777"
            },
            minorGridLines: {
                color: "#c7c7c7"
            },
            majorGridLines: {
                color: "#c7c7c7"
            }
        },
        tooltip: {
            background: WHITE,
            color: BLACK
        }
    });

    // Exports ================================================================
    Chart.themes = themes;
    Chart.prototype.options.theme = "default";

})(jQuery);

