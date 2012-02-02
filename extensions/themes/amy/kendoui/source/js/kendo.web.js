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
        fx = kendo.fx,
        each = $.each,
        extend = $.extend,
        size = kendo.size,
        browser = $.browser,
        support = kendo.support,
        transitions = support.transitions,
        scaleProperties = { scale: 0, scaleX: 0, scaleY: 0, scale3d: 0 },
        translateProperties = { translate: 0, translateX: 0, translateY: 0, translate3d: 0 },
        matrix3d = [ 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1 ],
        matrix3dRegExp = /matrix3?d?\s*\(.*,\s*([\d\w\.\-]+),\s*([\d\w\.\-]+),\s*([\d\w\.\-]+)/,
        cssParamsRegExp = /^(-?[\d\.\-]+)?[\w\s]*,?\s*(-?[\d\.\-]+)?[\w\s]*/i,
        translateXRegExp = /translatex?$/i,
        transformNon3D = { rotate: "", scale: "", translate: "" },
        transformProps = ["perspective", "rotate", "rotateX", "rotateY", "rotateZ", "rotate3d", "scale", "scaleX", "scaleY", "scaleZ", "scale3d", "skew", "skewX", "skewY", "translate", "translateX", "translateY", "translateZ", "translate3d", "matrix", "matrix3d"],
        cssPrefix = transitions.css,
        round = Math.round,
        BLANK = "",
        PX = "px",
        NONE = "none",
        AUTO = "auto",
        WIDTH = "width",
        SCALE = "scale",
        HEIGHT = "height",
        HIDDEN = "hidden",
        ORIGIN = "origin",
        ABORT_ID = "abortId",
        OVERFLOW = "overflow",
        TRANSLATE = "translate",
        STYLE = "style",
        TRANSITION = cssPrefix + "transition",
        TRANSFORM = cssPrefix + "transform";

    kendo.directions = {
        left: {
            reverse: "right",
            property: "left",
            transition: "translateX",
            vertical: false,
            modifier: -1
        },
        right: {
            reverse: "left",
            property: "left",
            transition: "translateX",
            vertical: false,
            modifier: 1
        },
        down: {
            reverse: "up",
            property: "top",
            transition: "translateY",
            vertical: true,
            modifier: 1
        },
        up: {
            reverse: "down",
            property: "top",
            transition: "translateY",
            vertical: true,
            modifier: -1
        },
        "in": {
            reverse: "out",
            modifier: -1
        },
        out: {
            reverse: "in",
            modifier: 1
        }
    };

    extend($.fn, {
        kendoStop: function(clearQueue, gotoEnd) {
            if (transitions) {
                return kendo.fx.stopQueue(this, clearQueue || false, gotoEnd || false);
            } else {
                return this.stop(clearQueue, gotoEnd);
            }
        }
    });

    kendo.toggleClass = function(element, classes, options, add) {
        if (classes) {
            classes = classes.split(" ");

            if (transitions) {
                options = extend({
                    exclusive: "all",
                    duration: 400,
                    ease: "ease-out"
                }, options);

                element.css(TRANSITION, options.exclusive + " " + options.duration + "ms " + options.ease);
                setTimeout(function() {
                    element.css(TRANSITION, NONE).css(HEIGHT);
                }, options.duration); // TODO: this should fire a kendoAnimate session instead.
            }

            each(classes, function(idx, value) {
                element.toggleClass(value, add);
            });
        }

        return element;
    };

    kendo.parseEffects = function(input, mirror) {
        var effects = {};

        if (typeof input === "string") {
            each(input.split(" "), function() {
                var effect = this.split(":"),
                    direction = effect[1],
                    effectBody = {};

                effect.length > 1 && (effectBody["direction"] = mirror ? kendo.directions[direction].reverse : direction);

                effects[effect[0]] = effectBody;
            });
        } else {
            each(input, function(idx) {
                var direction = this.direction;

                if (direction && mirror)
                    direction = kendo.directions[direction].reverse;

                effects[idx] = this;
            });
        }

        return effects;
    };

    function parseInteger(value) {
        return parseInt(value, 10);
    }

    function parseCSS(element, property) {
        return parseInteger(element.css(property));
    }

    function getComputedStyles(element, properties) {
        var styles = {};

        if (properties) {
            if (document.defaultView && document.defaultView.getComputedStyle) {
                var computedStyle = document.defaultView.getComputedStyle(element, "");

                each(properties, function(idx, value) {
                    styles[value] = computedStyle.getPropertyValue(value);
                });
            } else
                if (element.currentStyle) { // Not really needed
                    var style = element.currentStyle;

                    each(properties, function(idx, value) {
                        styles[value] = style[value.replace(/\-(\w)/g, function (strMatch, g1) { return g1.toUpperCase() })];
                    });
                }
        } else {
            styles = document.defaultView.getComputedStyle(element, "");
        }

        return styles;
    }

    function slideToSlideIn(options) {
      options.effects.slideIn = options.effects.slide;
      delete options.effects.slide;
      return options;
    }

    function parseTransitionEffects(options) {
        var effects = options.effects,
            mirror;

        if (effects === "zoom") {
            effects = "zoomIn fadeIn";
        }
        if (effects === "slide") {
            effects = "slide:left";
        }
        if (effects === "fade") {
            effects = "fadeIn";
        }
        if (effects === "overlay") {
            effects = "slideIn:left";
        }
        if (/^overlay:(.+)$/.test(effects)) {
            effects = "slideIn:" + RegExp.$1;
        }

        mirror = options.reverse && /^(slide:)/.test(effects);

        if (mirror) {
            delete options.reverse;
        }

        options.effects = $.extend(kendo.parseEffects(effects, mirror), {show: true});

        return options;
    }

    if (transitions) {

        function keys(obj) {
            var acc = [];
            for (var propertyName in obj)
                acc.push(propertyName);
            return acc;
        }

        function removeTransitionStyles(element) {
            element.css(TRANSITION, NONE);

            if (!browser.safari) {
                element.css(HEIGHT);
            }
        }

        function activateTask(currentTransition) {
            var element = currentTransition.object;

            if (!currentTransition) return;

            element.css(currentTransition.setup);
            element.css(TRANSITION);

            setTimeout(function() {
                element.data(ABORT_ID, setTimeout(function() {

                    removeTransitionStyles(element);
                    element.dequeue();
                    currentTransition.complete.call(element);

                }, currentTransition.duration));

                element.css(currentTransition.CSS);
            }, 0);
        }

        extend(kendo.fx, {
            transition: function(element, properties, options) {

                options = extend({
                        duration: 200,
                        ease: "ease-out",
                        complete: null,
                        exclusive: "all"
                    },
                    options
                );

                options.duration = $.fx ? $.fx.speeds[options.duration] || options.duration : options.duration;

                var transforms = [],
                    cssValues = {},
                    key;

                for (key in properties)
                    if (transformProps.indexOf(key) != -1)
                        transforms.push(key + "(" + properties[key] + ")");
                    else
                        cssValues[key] = properties[key];

                if (transforms.length)
                    cssValues[TRANSFORM] = transforms.join(" ");

                var currentTask = {
                    keys: keys(cssValues),
                    CSS: cssValues,
                    object: element,
                    setup: {},
                    duration: options.duration,
                    complete: options.complete
                };
                currentTask.setup[TRANSITION] = options.exclusive + " " + options.duration + "ms " + options.ease;

                var oldKeys = element.data("keys") || [];
                $.merge(oldKeys, currentTask.keys);
                element.data("keys", $.unique(oldKeys));

                activateTask(currentTask);
            },

            stopQueue: function(element, clearQueue, gotoEnd) {

                if (element.data(ABORT_ID)) {
                    clearTimeout(element.data(ABORT_ID));
                    element.removeData(ABORT_ID);
                }

                var that = this,
                    taskKeys = element.data("keys"),
                    retainPosition = (gotoEnd === false && taskKeys);

                if (retainPosition) {
                    var cssValues = getComputedStyles(element[0], taskKeys);
                }

                removeTransitionStyles(element);

                if (retainPosition) {
                    element.css(cssValues);
                }

                element.removeData("keys");

                if (that.complete) {
                    that.complete.call(element);
                }

                element.stop(clearQueue);
                return element;
            }

        });
    }

    function animationProperty(element, property) {
        if (transitions) {
            var transform = element.css(TRANSFORM);
            if (transform == "none") return property == "scale" ? 1 : 0;

            var match = transform.match(new RegExp(property + "\\s*\\(([\\d\\w\\.]+)")),
                computed = 0;

            if (match)
                computed = parseInteger(match[1]);
            else {
                match = transform.match(matrix3dRegExp) || [0, 0, 0, 0];

                if (translateXRegExp.test(property)) {
                    computed = parseInteger(match[2]);
                } else if (property.toLowerCase() == "translatey") {
                    computed = parseInteger(match[3]);
                } else if (property.toLowerCase() == "scale") {
                    computed = parseFloat(match[1]);
                }
            }

            return computed;
        } else
            return element.css(property);
    }

    kendo.fx.promise = function(element, options) {
        var promises = [], effects = options.effects;

        if (typeof effects === "string") {
            effects = kendo.parseEffects(options.effects);
        }

        element.data("animating", true);
        element.data("reverse", options.reverse);

        var props = { keep: [], restore: [] }, css = {},
            methods = { setup: [], teardown: [] }, properties = {},

            // create a promise for each effect
            promise = $.Deferred(function(deferred) {
                if (size(effects)) {
                    var opts = extend({}, options, { complete: deferred.resolve });

                    each(effects, function(effectName, settings) {
                        var effect = kendo.fx[effectName];

                        if (effect) {
                            opts = extend(true, opts, settings);

                            each(methods, function (idx) {
                                if (effect[idx])
                                    methods[idx].push(effect[idx]);
                            });

                            each(props, function(idx) {
                                if (effect[idx])
                                    $.merge(props[idx], effect[idx]);
                            });

                            if (effect["css"])
                                css = extend(css, effect.css);
                        }
                    });

                    if (methods.setup.length) {
                        each ($.unique(props.keep), function(idx, value) {
                            if (!element.data(value))
                                element.data(value, element.css(value));
                        });

                        if (options.show) {
                            css = extend(css, { display: element.data("olddisplay") || "block" }); // Add show to the set
                        }

                        if (css.transform) {
                            css[support.transitions.prefix + "Transform"] = css.transform;
                            delete css.transform;
                        }

                        element.css(css);
                        element.css("overflow"); // Nudge Chrome

                        each (methods.setup, function() { properties = extend(properties, this(element, opts)) });

                        if (kendo.fx["animate"]) {
                            options.init();
                            kendo.fx.animate(element, properties, opts);
                        }

                        return;
                    }
                }

                if (options.show) {
                    element.css({ display: element.data("olddisplay") || "block" }).css("display");
                }

                deferred.resolve();
            }).promise();

        promises.push(promise);

        //wait for all effects to complete
        $.when.apply(null, promises).then(function() {
            element
                .removeData("animating")
                .removeData("reverse")
                .dequeue(); // call next animation from the queue

            if (options.hide) {
                element.data("olddisplay", element.css("display")).hide();
            }

            if (size(effects)) {
                var restore = function() {
                    each ($.unique(props.restore), function(idx, value) {
                        element.css(value, element.data(value));
                    });
                };

                if ($.browser.msie) {
                    setTimeout(restore, 0); // Again jQuery callback in IE.
                }
                else {
                    restore();
                }

                each(methods.teardown, function() { this(element, options.reverse); }); // call the internal completion callbacks
            }

            if (options.completeCallback) {
                options.completeCallback(element); // call the external complete callback with the element
            }
        });
    };

    kendo.fx.transitionPromise = function(element, destination, options) {
        kendo.fx.animateTo(element, destination, options);
        return element;
    };

    extend(kendo.fx, {
        animate: function(elements, properties, options) {
            var useTransition = options.transition !== false;
            delete options.transition;

            if (transitions && "transition" in fx && useTransition) {
                fx.transition(elements, properties, options);
            } else {
                each(transformProps, function(idx, value) { // remove transforms to avoid IE and older browsers confusion
                    var params,
                        currentValue = properties ? properties[value]+ " " : null; // We need to match

                    elements.each(function() {
                        if (currentValue) {
                            var element = $(this),
                                single = properties;

                            if (value in scaleProperties && properties[value] !== undefined) {
                                !element.data(SCALE) && element.data(SCALE, {
                                            top: parseCSS(element, "top") || 0,
                                            left: parseCSS(element, "left") || 0,
                                            width: element.width(),
                                            height: element.height()
                                        });

                                var originalScale = element.data(SCALE);

                                params = currentValue.match(cssParamsRegExp);
                                if (params) {
                                    var scaleX = value == SCALE + "Y" ? +null : +params[1],
                                        scaleY = value == SCALE + "Y" ? +params[1] : +params[2] || +params[1];

                                    !isNaN(scaleX) && extend(single, {
                                                left: originalScale.left + originalScale.width * (1-scaleX) / 2,
                                                width: originalScale.width * scaleX
                                    });

                                    !isNaN(scaleY) && extend(single, {
                                                top: originalScale.top + originalScale.height * (1-scaleY) / 2,
                                                height: originalScale.height * scaleY
                                            });
                                }
                            } else
                                if (value in translateProperties && properties[value] !== undefined) {
                                    var position = element.css("position"),
                                        isFixed = (position == "absolute" || position == "fixed");

                                    if (!element.data(TRANSLATE)) {
                                        if (isFixed) {
                                            element.data(TRANSLATE, {
                                                top: parseCSS(element, "top") || 0,
                                                left: parseCSS(element, "left") || 0,
                                                bottom: parseCSS(element, "bottom"),
                                                right: parseCSS(element, "right")
                                            });
                                        } else
                                            element.data(TRANSLATE, {
                                                top: parseCSS(element, "marginTop") || 0,
                                                left: parseCSS(element, "marginLeft") || 0
                                            });
                                    }

                                    var originalPosition = element.data(TRANSLATE);

                                    params = currentValue.match(cssParamsRegExp);
                                    if (params) {

                                        var dX = value == TRANSLATE + "Y" ? +null : +params[1],
                                            dY = value == TRANSLATE + "Y" ? +params[1] : +params[2];

                                        if (isFixed) {
                                            if (!isNaN(originalPosition.right))
                                                !isNaN(dX) && extend(single, { right: originalPosition.right - dX });
                                            else
                                                !isNaN(dX) && extend(single, { left: originalPosition.left + dX });

                                            if (!isNaN(originalPosition.bottom))
                                                !isNaN(dY) && extend(single, { bottom: originalPosition.bottom - dY });
                                            else
                                                !isNaN(dY) && extend(single, { top: originalPosition.top + dY });
                                        } else {
                                            !isNaN(dX) && extend(single, { marginLeft: originalPosition.left + dX });
                                            !isNaN(dY) && extend(single, { marginTop: originalPosition.top + dY });
                                        }
                                    }
                                }

                            value in single && delete single[value];
                            element.animate(single, extend({ queue: false }, options, { show: false, hide: false })); // Stop animate from showing/hiding the element to be able to hide it later on.
                        }
                    });
                });
            }
        },

        animateTo: function(element, destination, options) {
            var direction,
                commonParent = element.parents().filter(destination.parents()).first(),
                originalOverflow = commonParent.css(OVERFLOW);

            options = parseTransitionEffects(options);
            commonParent.css("overflow-x", "hidden");

            $.each(options.effects, function(name, definition) {
                direction = direction || definition.direction;
            });

            function complete() {
                destination[0].style.cssText = "";
                element[0].style.cssText = ""; // Removing the whole style attribute breaks Android.
                commonParent.css(OVERFLOW, originalOverflow);
                options.completeCallback && options.completeCallback();
            }

            options.complete = $.browser.msie ? function() { setTimeout(complete) } : complete;

            if ("slide" in options.effects) {
              element.kendoAnimate(options);
              destination.kendoAnimate(slideToSlideIn(options));
            } else {
              (options.reverse ? element : destination).kendoAnimate(options);
            }
        },

        fadeOut: {
            css: {
                opacity: function() {
                    var element = $(this);
                    return element.data("reverse") && !this.style.opacity ? 0 : undefined;
                }
            },
            setup: function(element, options) {
                return extend({ opacity: options.reverse ? 1 : 0 }, options.properties)
            }
        },
        fadeIn: {
            css: {
                opacity: function() {
                    var element = $(this);
                    return !element.data("reverse") && !this.style.opacity ? 0 : undefined;
                }
            },
            setup: function(element, options) {
                return extend({ opacity: options.reverse ? 0 : 1 }, options.properties)
            }
        },
        zoomIn: {
            css: {
                transform: function() {
                    var element = $(this);
                    return !element.data("reverse") && transitions ? "scale(.01)" : undefined;
                }
            },
            setup: function(element, options) {
                return extend({ scale: options.reverse ? .01 : 1 }, options.properties)
            }
        },
        zoomOut: {
            css: {
                transform: function() {
                    var element = $(this);
                    return element.data("reverse") && transitions ? "scale(.01)" : undefined;
                }
            },
            setup: function(element, options) {
                return extend({ scale: options.reverse ? 1 : .01 }, options.properties)
            }
        },
        slide: {
            setup: function(element, options) {
                var direction = kendo.directions[options.direction],
                    extender = {}, offset, reverse = options.reverse,
                    divisor = options.divisor || 1;

                if (!reverse) {
                    var origin = element.data(ORIGIN);
                    offset = (direction.modifier * (direction.vertical ? element.outerHeight() : element.outerWidth()) / divisor);
                    !origin && origin !== 0  && element.data(ORIGIN, animationProperty(element, direction.transition));
                }

                if (transitions && options.transition !== false) {
                    extender[direction.transition] = reverse ? (element.data(ORIGIN) || 0) : offset + PX;
                } else {
                    extender[direction.property] = reverse ? (element.data(ORIGIN) || 0) : offset + PX;
                }

                return extend(extender, options.properties);
            }
        },
        slideMargin: {
            setup: function(element, options) {
                var origin = element.data(ORIGIN),
                    offset = options.offset, margin,
                    extender = {}, reverse = options.reverse;

                !reverse && !origin && origin !== 0 && element.data(ORIGIN, parseInt(element.css("margin-left"), 10));

                margin = (element.data(ORIGIN) || 0);
                extender["margin-" + options.axis] = !reverse ? margin + offset : margin;
                return extend(extender, options.properties);
            }
        },
        slideTo: {
            setup: function(element, options) {
                var offset = (options.offset+"").split(","),
                    extender = {}, reverse = options.reverse;

                if (transitions && options.transition !== false) {
                    extender["translate"] = !reverse ? offset + PX : 0;
                } else {
                    extender["left"] = !reverse ? offset[0] : 0;
                    extender["top"] = !reverse ? offset[1] : 0;
                }
                element.css("left");

                return extend(extender, options.properties);
            }
        },
        slideIn: {
            setup: function(element, options) {
                var direction = kendo.directions[options.direction],
                    offset = -direction.modifier * (direction.vertical ? element.outerHeight() : element.outerWidth()),
                    extender = {}, reverse = options.reverse;

                if (transitions && options.transition !== false) {
                    element.css(TRANSFORM, direction.transition + "(" + (!reverse ? offset : 0) + "px)");
                    extender[direction.transition] = reverse ? offset + PX : 0;
                } else {
                    !reverse && element.css(direction.property, offset + PX);
                    extender[direction.property] = reverse ? offset + PX : 0;
                }
                element.css(direction.property); // Read a style to force Chrome to apply the change.

                return extend(extender, options.properties);
            }
        },
        expandVertical: {
            keep: [ OVERFLOW ],
            css: { overflow: HIDDEN },
            restore: [ OVERFLOW ],
            setup: function(element, options) {
                var reverse = options.reverse,
                    setHeight = element[0].style.height,
                    oldHeight = element.data(HEIGHT),
                    fixedHeight = parseInteger(oldHeight || setHeight),
                    height = fixedHeight || round(element.css({ height: AUTO }).height());

                element.css(HEIGHT, reverse ? height : 0).css(HEIGHT);
                if (oldHeight === undefined) {
                    element.data(HEIGHT, setHeight);
                }

                return extend({ height: (reverse ? 0 : height) + PX }, options.properties);
            },
            teardown: function(element) {
                var height = element.data(HEIGHT);
                if (height == AUTO || height === BLANK) {
                    setTimeout(function() { element.css(HEIGHT, AUTO).css(HEIGHT); }, 0); // jQuery animate complete callback in IE is called before the last animation step!
                }
            }
        },
        simple: {
            setup: function(element, options) {
                return options.properties;
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
(function ($, undefined) {
    var kendo = window.kendo,
        Observable = kendo.Observable,
        data = kendo.data,
        Model = data.Model,
        CHANGE = "change";

    function bindSelect(select, model) {
        select = $(select);

        var text = select.attr(kendo.attr("text-field")),
            value = select.attr(kendo.attr("value-field")),
            source = select.attr(kendo.attr("source"));

        if (model[source]) {
            source = model[source].call(model);
        } else {
            try {
                source = eval(source);
            } catch(e) {
                return;
            }
        }

        if ($.isArray(source)) {
            select.html(kendo.render(kendo.template('<option value="${'+ value +'}">${' + text + '}</option>'), source));
        }
    }

    var ModelViewBinder = Observable.extend({
        init: function(element, model, options) {
            var that = this;

            that.element = $(element);
            that.options = options || {};

            Observable.fn.init.call(that);

            that.model = model instanceof Model ? model : new (Model.define())(model);

            that.bind([CHANGE], that.options);

            var elements = that.element.find("input,select,textarea");
            if (!elements.length) {
                elements = that.element;
            }

            elements.bind(CHANGE, $.proxy(that._change, that))
                .each(function() {
                    var mapping = that._map(this);
                    if (mapping) {
                        mapping.bindView();
                    }
                });
        },

        bindModel: function() {
            var that = this,
                valid = true;

            that.element.find("input,select,textarea")
                .each(function() {
                    var mapping = that._map(this);
                    if (mapping) {
                        return valid = mapping.bindModel();
                    }
                });

            return valid;
        },

        _change: function(e) {
            var that = this,
                mapping = that._map(e.target);

            if (mapping) {
                mapping.bindModel();
            }
        },

        _map: function(target) {
            var that = this,
                model = that.model,
                options = that.options,
                element = $(target),
                field = element.attr(kendo.attr("field")) || element.attr("name"),
                setting = options[field] || {};

            if (field) {
                return {
                    bindView: function() {
                        var value = model.get(field);

                        if (setting.format) {
                            value = setting.format(value);
                        }

                        if (target.nodeName.toLowerCase() === "select") {
                            bindSelect(target, model);
                        }

                        if (element.is(":checkbox")) {
                            element.attr("checked", value === true);
                        } else {
                            element.val(value);
                        }
                    },
                    bindModel: function() {
                        var value = element.is(":checkbox") ? element.is(":checked") : target.value,
                            values = {};

                        if (setting.parse) {
                           value = setting.parse(value);
                        }

                        values[field] = value;

                        if (!that.trigger(CHANGE, { values: values })) {
                            model.set(field, value);
                            return true;
                        }

                        return false;
                    }
                }
            }
        }
    });

    data.ModelViewBinder = ModelViewBinder;
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
;(function($, undefined) {
    var kendo = window.kendo,
        Widget = kendo.ui.Widget,
        INVALIDMSG = "k-invalid-msg",
        INVALIDINPUT = "k-invalid",
        emailRegExp = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,
        urlRegExp = /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,
        INPUTSELECTOR = ":input:not(:button,[type=submit],[type=reset])",
        NUMBERINPUTSELECTOR = "[type=number],[type=range]",
        BLUR = "blur",
        NAME = "name",
        FORM = "form",
        NOVALIDATE = "novalidate",
        proxy = $.proxy,
        patternMatcher = function(value, pattern) {
            if (typeof pattern === "string") {
                pattern = new RegExp('^(?:' + pattern + ')$');
            }
            return pattern.test(value);
        },
        matcher = function(input, selector, pattern) {
            var value = input.val();

            if (input.filter(selector).length && value !== "") {
                return patternMatcher(value, pattern);
            }
            return true;
        },
        hasAttribute = function(input, name) {
            if (input.length)  {
                return input[0].attributes[name] !== undefined;
            }
            return false;
        };

    /**
     *  @name kendo.ui.Validator.Description
     *
     *  @section
     *  <p>
     *     Validator offers an easy way to do client-side form validation.
     *     Built around the HTML5 form validation attributes it supports variety of built-in validation rules, but also provides a convenient way for setting custom rules handling.
     *  </p>
     *  @exampleTitle <b>Validator</b> initialization to validate input elements inside a container
     *  @example
     *  <div id="myform">
     *   <input type="text" name="firstName" required />
     *   <input type="text" name="lastName" required />
     *   <button id="save" type="button">Save</button>
     *  </div>
     *
     *  <script>
     *   $(document).ready(function(){
     *       var validatable = $("#myform").kendoValidator().data("kendoValidator");
     *       $("#save").click(function() {
     *          if (validatable.validate()) {
     *              save();
     *          }
     *       });
     *   });
     *   </script>
     */
    var Validator = Widget.extend(/** @lends kendo.ui.Validator.prototype */{ /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Object} [rules] Set of validation rules. Those rules will extend the built-in ones.
         * _example
         * $("#myform").kendoValidator({
         *      rules: {
         *          custom: function(input) {
         *              return input.is("[name=firstname]") && input.val() === "Tom"; // Only Tom will be a valid value for FirstName input
         *          }
         *      }
         * });
         * @option {Object} [messages] Set of messages (either strings or functions) which will be shown when given validation rule fails.
         *  By setting already existing key the appropriate built-in message will be overridden.
         * _example
         * $("#myform").kendoValidator({
         *      rules: {
         *          custom: function(input) {
         *             //...
         *          }
         *      },
         *      messages: {
         *          custom: "Please enter valid value for my custom rule",// defines message for the 'custom' validation rule
         *          required: "My custom required message", // overrides the built-in message for required rule
         *          email: function(input) { // overrides the built-in email rule message with a custom function which return the actual message
         *              return getMessage(input);
         *          }
         *      }
         * });
         */
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            that._errorTemplate = kendo.template(that.options.errorTemplate);

            if (that.element.is(FORM)) {
                that.element.attr(NOVALIDATE, NOVALIDATE);
            }

            that._errors = {};
            that._attachEvents();
        },

        options: {
            name: "Validator",
            errorTemplate: '<span class="k-widget k-tooltip k-tooltip-validation">' +
                '<span class="k-icon k-warning"> </span> ${message}</span>',
            messages: {
                required: "{0} is required",
                pattern: "{0} is not valid",
                min: "{0} should be greater than {1}",
                max: "{0} should be smaller than {1}",
                step: "{0} is not valid",
                email: "{0} is not valid email",
                url: "{0} is not valid URL",
                date: "{0} is not valid date"
            },
            rules: {
                required: function(input) {
                    var checkbox = input.filter("[type=checkbox]").length && input.attr("checked") !== "checked";
                    if (hasAttribute(input, "required") && (input.val() === "" || checkbox)) {
                        return false;
                    }
                    return true;
                },
                pattern: function(input) {
                    if (input.filter("[type=text],[type=email],[type=url],[type=tel],[type=search]").filter("[pattern]").length && input.val() !== "") {
                        return patternMatcher(input.val(), input.attr("pattern"));
                    }
                    return true;
                },
                min: function(input) {
                    if (input.filter(NUMBERINPUTSELECTOR + ",[" + kendo.attr("type") + "=number]").filter("[min]").length && input.val() !== "") {
                        var min = parseInt(input.attr("min"), 10) || 0,
                            val = parseInt(input.val(), 10);

                        return min <= val;
                    }
                    return true;
                },
                max: function(input) {
                    if (input.filter(NUMBERINPUTSELECTOR + ",[" + kendo.attr("type") + "=number]").filter("[max]").length && input.val() !== "") {
                        var max = parseInt(input.attr("max"), 10) || 0,
                            val = parseInt(input.val(), 10);

                        return max >= val;
                    }
                    return true;
                },
                step: function(input) {
                    if (input.filter(NUMBERINPUTSELECTOR + ",[" + kendo.attr("type") + "=number]").filter("[step]").length && input.val() !== "") {
                        var min = parseInt(input.attr("min"), 10) || 0,
                            step = parseInt(input.attr("step"), 10) || 0,
                            val = parseInt(input.val(), 10);

                        return (val-min)%step === 0;
                    }
                    return true;
                },
                email: function(input) {
                    return matcher(input, "[type=email],[" + kendo.attr("type") + "=email]", emailRegExp);
                },
                url: function(input) {
                    return matcher(input, "[type=url],[" + kendo.attr("type") + "=url]", urlRegExp);
                },
                date: function(input) {
                    if (input.filter("[type^=date],[" + kendo.attr("type") + "=date]").length && input.val() !== "") {
                        return kendo.parseDate(input.val(), input.attr(kendo.attr("format"))) !== null;
                    }
                    return true;
                }
            }
        },

        _submit: function(e) {
            if (!this.validate()) {
                e.stopPropagation();
                e.stopImmediatePropagation();
                e.preventDefault();
                return false;
            }
            return true;
        },

        _attachEvents: function() {
            var that = this;

            if (that.element.is(FORM)) {
                that.element.submit(proxy(that._submit, that));
            }

            if (!that.element.is(INPUTSELECTOR)) {
                that.element.delegate(INPUTSELECTOR, BLUR, function() {
                    that._validateInput($(this));
                });
            } else {
                that.element.bind(BLUR, function() {
                    that._validateInput(that.element);
                });
            }
        },

        /**
         * Validates the input element(s) against the declared validation rules.
         * @returns {Boolean} If all rules are passed successfully.
         */
        validate: function() {
            var that = this,
                inputs,
                idx,
                invalid = false,
                length;

            that._errors = {};

            if (!that.element.is(INPUTSELECTOR)) {
                inputs = that.element.find(INPUTSELECTOR);

                for (idx = 0, length = inputs.length; idx < length; idx++) {
                    if (!that._validateInput(inputs.eq(idx))) {
                        invalid = true;
                    }
                }
                return !invalid;
            }
            return that._validateInput(that.element);
        },

        _validateInput: function(input) {
            var that = this,
                template = that._errorTemplate,
                customMessages = that.options.messages,
                result = that._checkValidity(input),
                valid = result.valid,
                className = "." + INVALIDMSG,
                fieldName = input.attr(NAME),
                DATAFOR = kendo.attr("for"),
                lbl = that.element.find(className + "[" + DATAFOR +"=" + fieldName + "]").add(input.next(className)).hide(),
                messageText;

            if (!valid) {
                messageText = that._extractMessage(input, result.key);
                that._errors[fieldName] = messageText;

                var messageLabel = $(template({ message: messageText })).addClass(INVALIDMSG).attr(DATAFOR, fieldName || "");
                if (!lbl.replaceWith(messageLabel).length) {
                    messageLabel.insertAfter(input)
                }
                messageLabel.show();
            }

            input.toggleClass(INVALIDINPUT, !valid);

            return valid;
        },

        _extractMessage: function(input, ruleKey) {
            var that = this,
                customMessage = that.options.messages[ruleKey],
                fieldName = input.attr(NAME);

            customMessage = $.isFunction(customMessage) ? customMessage(input) : customMessage;

            return kendo.format(input.attr(kendo.attr(ruleKey + "-msg")) || input.attr("validationMessage") || input.attr("title") || customMessage || "", fieldName, input.attr(ruleKey));
        },

        _checkValidity: function(input) {
            var rules = this.options.rules,
                rule;

            for (rule in rules) {
                if (!rules[rule](input)) {
                    return { valid: false, key: rule };
                }
            }

            return { valid: true };
        },

        /**
         * Get the error messages if any.
         * @returns {Array} Messages for the failed validation rules.
         */
        errors: function() {
            var results = [],
                errors = this._errors,
                error;

            for (error in errors) {
                results.push(errors[error]);
            }
            return results;
        }
    });

    kendo.ui.plugin(Validator);
})(jQuery);
(function ($, undefined) {
    var kendo = window.kendo,
        document = window.document,
        Widget = kendo.ui.Widget,
        proxy = $.proxy,
        extend = $.extend,
        touch = kendo.support.touch,
        getOffset = kendo.getOffset,
        draggables = {},
        dropTargets = {},
        lastDropTarget = { element: [ null ] },
        NAMESPACE = ".kendo-dnd",
        MOUSEENTER = "mouseenter",
        MOUSEUP = touch? "touchend" : "mouseup",
        MOUSEDOWN = touch? "touchstart" : "mousedown",
        MOUSEMOVE = touch? "touchmove" : "mousemove",
        KEYDOWN = "keydown",
        MOUSELEAVE = "mouseleave",
        SELECTSTART = "selectstart",

        DRAGSTART = "dragstart",
        DRAGEND = "dragend",
        DRAG = "drag",
        DRAGENTER = "dragenter",
        DRAGLEAVE = "dragleave",
        DROP = "drop";

    function findTarget(needle, targets) {
        var result = { element: [ null ] };

        $.each(targets, function() {
            var that = this,
                element = that.element[0];

            if (contains(element, needle)) {
                result = that;
                return false;
            }
        });

        return result;
    }

    function contains(parent, child) {
        try {
            return $.contains(parent, child) || parent == child;
        } catch (e) {
            return false;
        }
    }

    function bind(element, filter, eventName, handler) {
        if (filter) {
            element.delegate(filter, eventName, handler);
        } else {
            element.bind(eventName, handler);
        }
    }

    var DropTarget = Widget.extend(/** @lends kendo.ui.DropTarget.prototype */ {
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {String} [group] <"default"> Used to group sets of draggable and drop targets. A draggable with the same group value as a drop target will be accepted by the drop target.
         */
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            that.element.bind(MOUSEENTER, proxy(that._over, that))
                .bind(MOUSEUP, proxy(that._drop, that))
                .bind(MOUSELEAVE, proxy(that._out, that));

            that.bind([
                /**
                 * Fires when draggable moves over the drop target.
                 * @name kendo.ui.DropTarget#dragenter
                 * @event
                 * @param {Event} e
                 * @param {jQueryObject} e.draggable Reference to the draggable that enters the drop target.
                 */
                DRAGENTER,
                /**
                 * Fires when draggable moves out of the drop target.
                 * @name kendo.ui.DropTarget#dragleave
                 * @event
                 * @param {Event} e
                 * @param {jQueryObject} e.draggable Reference to the draggable that leaves the drop target.
                 */
                DRAGLEAVE,
                /**
                 * Fires when draggable is dropped over the drop target.
                 * @name kendo.ui.DropTarget#drop
                 * @event
                 * @param {Event} e
                 * @param {jQueryObject} e.draggable Reference to the draggable that is dropped over the drop target.
                 * @param {jQueryObject} e.draggable.currentTarget The element that the drag and drop operation started from.
                 */
                DROP
            ], that.options);

            var group = that.options.group;

            if (!(group in dropTargets)) {
                dropTargets[group] = [ that ];
            } else {
                dropTargets[group].push( that );
            }
        },

        options: {
            name: "DropTarget",
            group: "default"
        },

        _trigger: function(eventName, e) {
            var that = this,
                draggable = draggables[that.options.group];

            if (draggable) {
                return that.trigger(eventName, extend({}, e, {
                           draggable: draggable
                       }));
            }
        },

        _over: function(e) {
            this._trigger(DRAGENTER, e);
        },

        _out: function(e) {
            this._trigger(DRAGLEAVE, e);
        },

        _drop: function(e) {
            var that = this,
                draggable = draggables[that.options.group];

            if (draggable) {
                draggable.dropped = !that._trigger(DROP, e);
            }
        }
    });

    kendo.ui.plugin(DropTarget);

    /**
     * @name kendo.ui.Draggable.Description
     *
     * @section Enable draggable functionality on any DOM element.
     *
     * @exampleTitle <b>Draggable</b> initialization
     * @example
     * var draggable = $("#draggable").kendoDraggable();
     *
     * @name kendo.ui.DropTarget.Description
     *
     * @section Enable any DOM element to be a target for draggable elements.
     *
     * @exampleTitle <b>DropTarget</b> initialization
     * @example
     * var dropTarget = $("#dropTarget").kendoDropTarget();
     */
    var Draggable = Widget.extend(/** @lends kendo.ui.Draggable.prototype */{
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Integer} [distance] <5> The required distance that the mouse should travel in order to initiate a drag.
         * @option {Selector} [filter] Selects child elements that are draggable if a widget is attached to a container.
         * @option {String} [group] <"default"> Used to group sets of draggable and drop targets. A draggable with the same group value as a drop target will be accepted by the drop target.
         * @option {Function|jQueryObject} [hint] Provides a way for customization of the drag indicator.
         * _example
         *  //hint as a function
         *  $("#draggable").kendoDraggable({
         *      hint: function() {
         *          return $("#draggable").clone();
         *      }
         *  });
         *
         * //hint as jQuery object
         *  $("#draggable").kendoDraggable({
         *      hint: $("#draggableHint");
         *  });
         */
        init: function (element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            bind(that.element, that.options.filter, MOUSEDOWN + NAMESPACE, proxy(that._wait, that));

            that.bind([
                /**
                 * Fires when item drag starts.
                 * @name kendo.ui.Draggable#dragstart
                 * @event
                 * @param {Event} e
                 */
                DRAGSTART,
                 /**
                 * Fires while dragging.
                 * @name kendo.ui.Draggable#drag
                 * @event
                 * @param {Event} e
                 */
                DRAG,
                 /**
                 * Fires when item drag ends.
                 * @name kendo.ui.Draggable#dragend
                 * @event
                 * @param {Event} e
                 */
                DRAGEND
            ], that.options);

            bind(that.element, that.options.filter, DRAGSTART + NAMESPACE, false);
        },

        options: {
            name: "Draggable",
            distance: 5,
            group: "default",
            cursorOffset: {
                left: 10,
                top: touch ? -40 / kendo.support.zoomLevel() : 10
            },
            dropped: false
        },

        _startDrag: function(e) {
            var that = this,
                filter = that.options.filter;

            that._offset = kendo.touchLocation(e);

            if (filter) {
                that.currentTarget = $(e.target).is(filter) ? $(e.target) : $(e.target).closest(filter);
            } else {
                that.currentTarget = $(e.currentTarget);
            }

            $(document).bind(MOUSEMOVE + NAMESPACE, proxy(that._start, that))
                       .bind(MOUSEUP + NAMESPACE, proxy(that._destroy, that));
        },

        _wait: function (e) {
            var that = this;

            e.stopImmediatePropagation();

            that._startDrag(e);

            // Prevent text selection for Gecko and WebKit
            if (!touch) {
                e.preventDefault();
            }
        },

        _start: function(e) {
            var that = this,
                location = kendo.touchLocation(e),
                pageX = location.x,
                pageY = location.y,
                x = that._offset.x - pageX,
                y = that._offset.y - pageY,
                distance = Math.sqrt((x * x) + (y * y)),
                options = that.options,
                cursorOffset = options.cursorOffset,
                hint = options.hint;

            if (distance >= options.distance) {
                if (touch)
                    e.preventDefault();

                if (hint) {
                    that.hint = $.isFunction(hint) ? $(hint(that.currentTarget)) : hint;

                    that.hint.css( {
                        position: "absolute",
                        zIndex: 10010, //the Window's z-index is 10000
                        left: pageX + cursorOffset.left,
                        top: pageY + cursorOffset.top
                    })
                    .appendTo(document.body);
                }

                draggables[options.group] = that;

                $(document).unbind(NAMESPACE)
                           .bind(MOUSEUP + NAMESPACE + " " + KEYDOWN + NAMESPACE, proxy(that._stop, that))
                           .bind(MOUSEMOVE + NAMESPACE, proxy(that._drag, that))
                           .bind(SELECTSTART + NAMESPACE, false);

                that.dropped = false;

                if (that._trigger(DRAGSTART, e)) {
                    that._destroy(e);
                }
            }
        },

        _drag: function(e) {
            var that = this,
                cursorOffset = that.options.cursorOffset,
                location = kendo.touchLocation(e);

            if (touch && kendo.size(dropTargets)) {
                var options = that.options,
                    dropTarget = kendo.eventTarget(e);

                if (dropTarget) {
                    var target = findTarget(dropTarget, dropTargets[options.group]),
                        element = target.element[0],
                        lastTarget = lastDropTarget.element[0],
                        difference = lastTarget != element;

                    if (difference) {
                        if (lastTarget != null)
                            lastDropTarget._trigger(DRAGLEAVE, e);

                        if (contains(element, dropTarget))
                            target._trigger(DRAGENTER, e);

                        lastDropTarget = target;
                    }
                }
            }

            that._trigger(DRAG, e);

            if (that.hint) {
                that.hint.css( {
                    left: location.x + cursorOffset.left,
                    top: location.y + cursorOffset.top
                });
            }
        },

        _stop: function(e) {
            var that = this,
                destroy = proxy(that._destroy, that),
                offset = getOffset(that.currentTarget);

            if (e.type == MOUSEUP || e.keyCode == 27) {
                if (touch && kendo.size(dropTargets)) {
                    var options = that.options,
                        dropTarget = kendo.eventTarget(e);

                    if (dropTarget) {
                        var target = findTarget(dropTarget, dropTargets[options.group]);

                        if (target.element[0]) {
                            lastDropTarget = { element: [ null ] };
                            target._drop(e);
                        }
                    }
                }

                that._trigger(DRAGEND, e);

                if (that.hint && !that.dropped) {
                    that.hint.animate(offset, "fast", destroy);
                } else {
                    destroy();
                }
            }
        },

        _trigger: function(eventName, e) {
            var that = this,
                location = kendo.touchLocation(e);

            return that.trigger(eventName, extend({}, e, {
                currentTarget: that.currentTarget,
                pageX: location.x,
                pageY: location.y
            }));
        },

        _destroy: function(e) {
            var that = this;

            if (that.hint) {
                that.hint.remove();
            }

            delete draggables[that.options.group];

            $(document).unbind(NAMESPACE);
        }
    });

    kendo.ui.plugin(Draggable);

 })(jQuery);
(function ($, undefined) {
    var kendo = window.kendo,
        Widget = kendo.ui.Widget,
        proxy = $.proxy,
        CONTAINER_EMPTY_TEXT = "Drag a column header and drop it here to group by that column",
        indicatorTmpl = kendo.template('<div class="k-group-indicator" data-#=data.ns#field="${data.field}" data-#=data.ns#title="${data.title}" data-#=data.ns#dir="${data.dir || "asc"}">' +
                '<a href="\\#" class="k-link">' +
                    '<span class="k-icon k-arrow-${(data.dir || "asc") == "asc" ? "up" : "down"}-small">(sorted ${(data.dir || "asc") == "asc" ? "ascending": "descending"})</span>' +
                    '${data.title ? data.title: data.field}' +
                '</a>' +
                '<a class="k-button k-button-icon k-button-bare">' +
                    '<span class="k-icon k-group-delete"></span>' +
                '</a>' +
             '</div>',  { useWithBlock:false }),
        hint = function(target) {
            return $('<div class="k-header k-drag-clue" />')
                .html(target.attr(kendo.attr("title")) || target.attr(kendo.attr("field")))
                .prepend('<span class="k-icon k-drag-status k-denied" />');
        },
        dropCue = $('<div class="k-grouping-dropclue"/>');

    function dropCueOffsetTop(element) {
        return $(element).children(".k-grid-toolbar").outerHeight() + 3;
    }

    var Groupable = Widget.extend({
        init: function(element, options) {
            var that = this,
                groupContainer,
                group = kendo.guid(),
                intializePositions = proxy(that._intializePositions, that),
                dropCuePositions = that._dropCuePositions = [];

            Widget.fn.init.call(that, element, options);

            groupContainer = that.groupContainer = $(that.options.groupContainer, that.element)
                .kendoDropTarget({
                    group: group,
                    dragenter: function(e) {
                        e.draggable.hint.find(".k-drag-status").removeClass("k-denied").addClass("k-add");
                        dropCue.css({top: dropCueOffsetTop(that.element), left: 0}).appendTo(groupContainer);
                    },

                    dragleave: function(e) {
                        e.draggable.hint.find(".k-drag-status").removeClass("k-add").addClass("k-denied");
                        dropCue.remove();
                    }
                })
                .kendoDraggable({
                    filter: "div.k-group-indicator",
                    hint: hint,
                    group: group,
                    dragend: function(e) {
                        that._dragEnd(this, e);
                    },
                    dragstart: function(e) {
                        var element = e.currentTarget,
                            marginLeft = parseInt(element.css("marginLeft")),
                            left = element.position().left - marginLeft;

                        intializePositions();
                        dropCue.css({top: dropCueOffsetTop(that.element), left: left}).appendTo(groupContainer);
                        this.hint.find(".k-drag-status").removeClass("k-denied").addClass("k-add");
                    },
                    drag: proxy(that._drag, that)
                })
                .delegate(".k-button", "click", function(e) {
                    e.preventDefault();
                    that._removeIndicator($(this).parent());
                })
                .delegate(".k-link", "click", function(e) {
                    var current = $(this).parent(),
                        newIndicator = that.buildIndicator(current.attr(kendo.attr("field")), current.attr(kendo.attr("title")), current.attr(kendo.attr("dir")) == "asc" ? "desc" : "asc");

                    current.before(newIndicator).remove();
                    that._change();
                    e.preventDefault();
                });

            that.element.kendoDraggable({
                filter: that.options.filter,
                hint: hint,
                group: group,
                dragend: function(e) {
                    that._dragEnd(this, e);
                },
                dragstart: function(e) {
                    var element, marginRight, left,
                        field = e.currentTarget.attr(kendo.attr("field"));

                    if(that.indicator(field)) {
                        e.preventDefault();
                        return;
                    }

                    intializePositions();
                    if(dropCuePositions.length) {
                        element = dropCuePositions[dropCuePositions.length - 1].element;
                        marginRight = parseInt(element.css("marginRight"));
                        left = element.position().left + element.outerWidth() + marginRight;
                    } else {
                        left = 0;
                    }

                    dropCue.css({top: dropCueOffsetTop(that.element), left: left}).appendTo(groupContainer);
                    this.hint.find(".k-drag-status").removeClass("k-denied").addClass("k-add");
                },
                drag: proxy(that._drag, that)
            });

            that.dataSource = that.options.dataSource;

            if(that.dataSource) {
                that.dataSource.bind("change", function() {
                    groupContainer.empty().append(
                        $.map(this.group() || [], function(item) {
                            return that.buildIndicator(item.field, that.element.find(that.options.filter).filter("[" + kendo.attr("field") + "=" + item.field + "]").attr(kendo.attr("title")), item.dir);
                        }).join("")
                    );
                    that._invalidateGroupContainer();
                });
            }
        },
        options: {
            name: "Groupable",
            filter: "th"
        },
        indicator: function(field) {
            var indicators = $(".k-group-indicator", this.groupContainer);
            return $.grep(indicators, function (item)
                {
                    return $(item).attr(kendo.attr("field")) === field;
                })[0];
        },
        buildIndicator: function(field, title, dir) {
            return indicatorTmpl({ field: field, dir: dir, title: title, ns: kendo.ns });
        },
        descriptors: function() {
            var indicators = $(".k-group-indicator", this.groupContainer);
            return $.map(indicators, function(item) {
                item = $(item);

                return {
                    field: item.attr(kendo.attr("field")),
                    dir: item.attr(kendo.attr("dir"))
                };
            });
        },
        _removeIndicator: function(indicator) {
            var that = this;
            indicator.remove();
            that._invalidateGroupContainer();
            that._change();
        },
        _change: function() {
            var that = this;
            if(that.dataSource) {
                that.dataSource.group(that.descriptors());
            }
        },
        _dropCuePosition: function(position) {
            var dropCuePositions = this._dropCuePositions;
            if(!dropCue.is(":visible") || dropCuePositions.length == 0) {
                return;
            }

            var lastCuePosition = dropCuePositions[dropCuePositions.length - 1],
                right = lastCuePosition.right,
                marginLeft = parseInt(lastCuePosition.element.css("marginLeft")),
                marginRight = parseInt(lastCuePosition.element.css("marginRight"));

            if(position >= right) {
                position = {
                    left: lastCuePosition.element.position().left + lastCuePosition.element.outerWidth() + marginRight,
                    element: lastCuePosition.element,
                    before: false
                };
            } else {
                position = $.grep(dropCuePositions, function(item) {
                    return item.left <= position && position <= item.right;
                })[0];

                if(position) {
                    position = {
                        left: position.element.position().left - marginLeft,
                        element: position.element,
                        before: true
                    };
                }
            }

            return position;
        },
        _drag: function(event) {
            var position = this._dropCuePosition(event.pageX);
            if(position) {
                dropCue.css({ left: position.left });
            }
        },
        _canDrop: function(source, target, position) {
            var next = source.next();
            return source[0] !== target[0] && (!next[0] || target[0] !== next[0] || position > next.position().left);
        },
        _dragEnd: function(draggable, event) {
            var that = this,
                field = event.currentTarget.attr(kendo.attr("field")),
                title = event.currentTarget.attr(kendo.attr("title")),
                sourceIndicator = that.indicator(field),
                dropCuePositions = that._dropCuePositions,
                lastCuePosition = dropCuePositions[dropCuePositions.length - 1],
                position;

            if(draggable.dropped) {
                if(lastCuePosition) {
                    position = that._dropCuePosition(dropCue.offset().left + parseInt(lastCuePosition.element.css("marginLeft")) + parseInt(lastCuePosition.element.css("marginRight")));
                    if(that._canDrop($(sourceIndicator), position.element, position.left)) {
                        if(position.before) {
                            position.element.before(sourceIndicator || that.buildIndicator(field, title));
                        } else {
                            position.element.after(sourceIndicator || that.buildIndicator(field, title));
                        }

                        that._change();
                    }
                } else {
                    that.groupContainer.append(that.buildIndicator(field, title));
                    that._change();
                }
            } else {
                if(sourceIndicator) {
                    that._removeIndicator($(sourceIndicator));
                }
            }

            dropCue.remove();
            dropCuePositions = [];
        },
        _intializePositions: function() {
            var that = this,
                indicators = $(".k-group-indicator", that.groupContainer),
                left;
            that._dropCuePositions = $.map(indicators, function(item) {
                item = $(item);
                left = item.offset().left;
                return {
                    left: left,
                    right: left + item.outerWidth(),
                    element: item
                };
            });
        },
        _invalidateGroupContainer: function() {
            var groupContainer = this.groupContainer;
            if(groupContainer.is(":empty")) {
                groupContainer.html(CONTAINER_EMPTY_TEXT);
            }
        }
    });

    kendo.ui.plugin(Groupable);

})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        ui = kendo.ui,
        Widget = ui.Widget,
        proxy = $.proxy,
        isFunction = $.isFunction,
        extend = $.extend,
        HORIZONTAL = "horizontal",
        VERTICAL = "vertical",
        START = "start",
        RESIZE = "resize",
        RESIZEEND = "resizeend";

    var Resizable = Widget.extend({
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            that.orientation = that.options.orientation.toLowerCase() != VERTICAL ? HORIZONTAL : VERTICAL;
            that._positionMouse = that.orientation == HORIZONTAL ? "pageX" : "pageY";
            that._position = that.orientation == HORIZONTAL ? "left" : "top";
            that._sizingDom = that.orientation == HORIZONTAL ? "outerWidth" : "outerHeight";

            that.bind([RESIZE,RESIZEEND,START], that.options);

            new ui.Draggable(element, {
                distance: 0,
                filter: options.handle,
                drag: proxy(that._resize, that),
                dragstart: proxy(that._start, that),
                dragend: proxy(that._stop, that)
            });
        },

        options: {
            name: "Resizable",
            orientation: HORIZONTAL
        },
        _max: function(e) {
            var that = this,
                hintSize = that.hint ? that.hint[that._sizingDom]() : 0,
                size = that.options.max;

            return isFunction(size) ? size(e) : size !== undefined ? (that._initialElementPosition + size) - hintSize : size;
        },
        _min: function(e) {
            var that = this,
                size = that.options.min;

            return isFunction(size) ? size(e) : size !== undefined ? that._initialElementPosition + size : size;
        },
        _start: function(e) {
            var that = this,
                hint = that.options.hint,
                el = $(e.currentTarget);

            that._initialMousePosition = e[that._positionMouse];
            that._initialElementPosition = el.position()[that._position];

            if (hint) {
                that.hint = isFunction(hint) ? $(hint(el)) : hint;

                that.hint.css({
                    position: "absolute"
                })
                .css(that._position, that._initialElementPosition)
                .appendTo(that.element);
            }

            that.trigger(START, e);

            that._maxPosition = that._max(e);
            that._minPosition = that._min(e);

            $(document.body).css("cursor", el.css("cursor"));
        },
        _resize: function(e) {
            var that = this,
                handle = $(e.currentTarget),
                maxPosition = that._maxPosition,
                minPosition = that._minPosition,
                currentPosition = that._initialElementPosition + (e[that._positionMouse] - that._initialMousePosition),
                position;

            position = minPosition !== undefined ? Math.max(minPosition, currentPosition) : currentPosition;
            that.position = position =  maxPosition !== undefined ? Math.min(maxPosition, position) : position;

            if(that.hint) {
                that.hint.toggleClass(that.options.invalidClass || "", position == maxPosition || position == minPosition)
                         .css(that._position, position);
            }

            that.trigger(RESIZE, extend(e, { position: position }));
        },
        _stop: function(e) {
            var that = this;

            if(that.hint) {
                that.hint.remove();
            }

            that.trigger(RESIZEEND, extend(e, { position: that.position }));
            $(document.body).css("cursor", "");
        }
    });

    kendo.ui.plugin(Resizable);

})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        proxy = $.proxy,
        DIR = "dir",
        ASC = "asc",
        SINGLE = "single",
        FIELD = "field",
        DESC = "desc",
        TLINK = ".k-link",
        Widget = kendo.ui.Widget;

    var Sortable = Widget.extend({
        init: function(element, options) {
            var that = this, link;

            Widget.fn.init.call(that, element, options);

            that.dataSource = that.options.dataSource.bind("change", proxy(that.refresh, that));
            link = that.element.find(TLINK);

            if (!link[0]) {
                link = that.element.wrapInner('<a class="k-link" href="#"/>').find(TLINK);
            }

            that.link = link;
            that.element.click(proxy(that._click, that));
        },

        options: {
            name: "Sortable",
            mode: SINGLE,
            allowUnsort: true
        },

        refresh: function() {
            var that = this,
                sort = that.dataSource.sort() || [],
                idx,
                length,
                descriptor,
                dir,
                element = that.element,
                field = element.data(FIELD);

            element.removeData(DIR);

            for (idx = 0, length = sort.length; idx < length; idx++) {
               descriptor = sort[idx];

               if (field == descriptor.field) {
                   element.data(DIR, descriptor.dir);
               }
            }

            dir = element.data(DIR);

            element.find(".k-arrow-up,.k-arrow-down").remove();

            if (dir === ASC) {
                $('<span class="k-icon k-arrow-up" />').appendTo(that.link);
            } else if (dir === DESC) {
                $('<span class="k-icon k-arrow-down" />').appendTo(that.link);
            }
        },

        _click: function(e) {
            var that = this,
                element = that.element,
                field = element.data(FIELD),
                dir = element.data(DIR),
                options = that.options,
                sort = that.dataSource.sort() || [],
                idx,
                length;

            if (dir === ASC) {
                dir = DESC;
            } else if (dir === DESC && options.allowUnsort) {
                dir = undefined;
            } else {
                dir = ASC;
            }

            if (options.mode === SINGLE) {
                sort = [ { field: field, dir: dir } ];
            } else if (options.mode === "multiple") {
                for (idx = 0, length = sort.length; idx < length; idx++) {
                    if (sort[idx].field === field) {
                        sort.splice(idx, 1);
                        break;
                    }
                }
                sort.push({ field: field, dir: dir });
            }

            e.preventDefault();

            that.dataSource.sort(sort);
        }
    });

    kendo.ui.plugin(Sortable);
})(jQuery);
(function ($, undefined) {
    var kendo = window.kendo,
        keys = kendo.keys,
        touch = kendo.support.touch,
        Widget = kendo.ui.Widget,
        proxy = $.proxy,
        MOUSEUP = touch? "touchend" : "mouseup",
        MOUSEDOWN = touch? "touchstart" : "mousedown",
        MOUSEMOVE = touch? "touchmove" : "mousemove",
        SELECTED = "k-state-selected",
        ACTIVE = "k-state-selecting",
        SELECTABLE = "k-selectable",
        SELECTSTART = "selectstart",
        DOCUMENT = $(document),
        CHANGE = "change",
        UNSELECTING = "k-state-unselecting";

    var Selectable = Widget.extend({
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            that._marquee = $("<div class='k-marquee'></div>");
            that._lastActive = null;

            that._moveDelegate = proxy(that._move, that);
            that._upDelegate = proxy(that._up, that);

            that.element.addClass(SELECTABLE);
            that.element.delegate("." + SELECTABLE + " " + that.options.filter, MOUSEDOWN, proxy(that._down, that));
            that.bind([CHANGE], that.options);
        },

        options: {
            name: "Selectable",
            filter: ">*",
            multiple: false
        },
        _collide: function(element, marqueePos) {
            var pos = element.offset(),
                selectee = {
                    left: pos.left,
                    top: pos.top,
                    right: pos.left + element.outerWidth(),
                    bottom: pos.top + element.outerHeight()
                };
            return (!(selectee.left > marqueePos.right
                || selectee.right < marqueePos.left
                || selectee.top > marqueePos.bottom
                || selectee.bottom < marqueePos.top));
        },
        _position: function(event) {
            var pos = this._originalPosition,
                left = pos.x,
                top = pos.y,
                right = event.pageX,
                bottom = event.pageY;
            if (left > right) {
                var tmp = right;
                right = left;
                left = tmp;
            }
            if (top > bottom) {
                var tmp = bottom;
                bottom = top;
                top = tmp;
            }

            return {
                top: top,
                right: right,
                left: left,
                bottom: bottom
            };
        },
        _down: function (event) {
            var that = this,
                selected,
                ctrlKey = event.ctrlKey,
                shiftKey = event.shiftKey,
                single = !that.options.multiple;
            that._downTarget = $(event.currentTarget);
            that._shiftPressed = shiftKey;
            DOCUMENT
                .unbind(MOUSEUP, that._upDelegate) // more cancel friendly
                .bind(MOUSEUP, that._upDelegate);
            that._originalPosition = {
                x: event.pageX,
                y: event.pageY
            };

            if(!single) {
                DOCUMENT
                    .unbind(MOUSEMOVE, that._moveDelegate)
                    .bind(MOUSEMOVE, that._moveDelegate);
            }

            if (!single) {
                $("body").append(that._marquee);
                that._marquee.css({
                    left: event.clientX + 1,
                    top: event.clientY + 1,
                    width: 0,
                    height: 0
                });
            }

            selected = that._downTarget.hasClass(SELECTED);
            if(single || !(ctrlKey || shiftKey)) {
                that.element
                .find(that.options.filter + "." + SELECTED)
                .not(that._downTarget)
                .removeClass(SELECTED);
            }
            if(ctrlKey) {
                that._lastActive = that._downTarget;
            }

            if(selected && (ctrlKey || shiftKey)) {
                that._downTarget.addClass(SELECTED);
                if(!shiftKey) {
                    that._downTarget.addClass(UNSELECTING);
                }
            }
            else {
                if (!(kendo.support.touch && single)) {
                    that._downTarget.addClass(ACTIVE);
                }
            }
        },
        _move: function (event) {
            var that = this,
                pos = that._position(event),
                ctrlKey = event.ctrlKey,
                selectee, collide;

                that._marquee.css({
                    left: pos.left,
                    top: pos.top,
                    width: pos.right - pos.left,
                    height: pos.bottom - pos.top
                });

            DOCUMENT
                .unbind(SELECTSTART, false)
                .bind(SELECTSTART, false);

            that.element.find(that.options.filter).each(function () {
                selectee = $(this);
                collide = that._collide(selectee, pos);

                if (collide) {
                    if(selectee.hasClass(SELECTED)) {
                        if(that._downTarget[0] !== selectee[0] && ctrlKey) {
                            selectee
                                .removeClass(SELECTED)
                                .addClass(UNSELECTING);
                        }
                    } else if (!selectee.hasClass(ACTIVE) && !selectee.hasClass(UNSELECTING)) {
                        selectee.addClass(ACTIVE);
                    }
                }
                else {
                    if (selectee.hasClass(ACTIVE)) {
                        selectee.removeClass(ACTIVE);
                    }
                    else if(ctrlKey && selectee.hasClass(UNSELECTING)) {
                        selectee
                            .removeClass(UNSELECTING)
                            .addClass(SELECTED);
                    }
                }
            });
        },
        _up: function (event) {
            var that = this,
                options = that.options,
                single = !options.multiple;
            DOCUMENT
                .unbind(MOUSEMOVE, that._moveDelegate)
                .unbind(MOUSEUP, that._upDelegate);
            if (!single) {
                that._marquee.remove();
            }

            if (kendo.support.touch && single)
                that._downTarget.addClass(ACTIVE);

            if(!single && that._shiftPressed === true) {
                that.selectRange(that._firstSelectee(), that._downTarget);
            }
            else {
                that.element
                    .find(options.filter + "." + UNSELECTING)
                    .removeClass(UNSELECTING)
                    .removeClass(SELECTED);

                that.value(that.element.find(options.filter + "." + ACTIVE));
            }
            if(!that._shiftPressed) {
                that._lastActive = that._downTarget;
            }
            that._downTarget = null;
            that._shiftPressed = false;
        },
        value: function(val) {
            var that = this,
            selectElement = proxy(that._selectElement, that);
            if(val) {
                val.each(function() {
                    selectElement(this);
                });

                that.trigger(CHANGE, {});
                return;
            }

            return that.element
                    .find(that.options.filter + "." + SELECTED);
        },
        _firstSelectee: function() {
            var that = this, selected;
            if(that._lastActive !== null) {
                return that._lastActive;
            }

            selected = that.value();
            return selected.length > 0 ?
                    selected[0] :
                    that.element.find(that.options.filter);
        },
        _selectElement: function(el) {
            var selecee = $(el),
                isPrevented = this.trigger("select", { element: el });

            selecee.removeClass(ACTIVE);
            if(!isPrevented) {
                selecee.addClass(SELECTED);
            }
        },
        clear: function() {
            var that = this;
            that.element
                .find(that.options.filter + "." + SELECTED)
                .removeClass(SELECTED);
        },
        selectRange: function(start, end) {
            var that = this,
                found = false,
                selectElement = proxy(that._selectElement, that),
                selectee;
            start = $(start)[0];
            end = $(end)[0];
            that.element.find(that.options.filter).each(function () {
                selectee = $(this);
                if(found) {
                    selectElement(this);
                    found = !(this === end);
                }
                else if(this === start) {
                    found = !(start === end);
                    selectElement(this);
                }
                else if(this === end) {
                    var tmp = start;
                    start = end;
                    end = tmp;
                    found = true;
                    selectElement(this);
                }
                else {
                    selectee.removeClass(SELECTED);
                }
            });
            that.trigger(CHANGE, {});
        }
    });

    kendo.ui.plugin(Selectable);

})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        ui = kendo.ui,
        Widget = ui.Widget,
        proxy = $.proxy;

    function button(template, idx, text, numeric) {
        return template( {
            idx: idx,
            text: text,
            ns: kendo.ns,
            numeric: numeric
        });
    }

    var Pager = Widget.extend( {
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            options = that.options;
            that.dataSource = options.dataSource;
            that.linkTemplate = kendo.template(that.options.linkTemplate);
            that.selectTemplate = kendo.template(that.options.selectTemplate);

            that.dataSource.bind("change", proxy(that.refresh, that));
            that.list = $('<ul class="k-pager k-reset k-numeric" />').appendTo(that.element).html(that.selectTemplate({ text: 1 }));
            that.element.delegate("a", "click",  proxy(that._click, that));
        },

        options: {
            name: "Pager",
            selectTemplate: '<li><span class="k-state-active">#=text#</span></li>',
            linkTemplate: '<li><a href="\\#" class="k-link" data-#=ns#page="#=idx#">#=text#</a></li>',
            buttonCount: 10
        },

        refresh: function() {
            var that = this,
                idx,
                end,
                start = 1,
                html = "",
                reminder,
                page = that.page(),
                totalPages = that.totalPages(),
                linkTemplate = that.linkTemplate,
                buttonCount = that.options.buttonCount;

            if (page > buttonCount) {
                reminder = (page % buttonCount);

                start = (reminder == 0) ? (page - buttonCount) + 1 : (page - reminder) + 1;
            }

            end = Math.min((start + buttonCount) - 1, totalPages);

            if(start > 1) {
                html += button(linkTemplate, start - 1, "...", false);
            }

            for(idx = start; idx <= end; idx++) {
                html += button(idx == page ? that.selectTemplate : linkTemplate, idx, idx, true);
            }

            if(end < totalPages) {
                html += button(linkTemplate, idx, "...", false);
            }

            that.list.empty().append(html);
        },

        _click: function(e) {
            var page = $(e.currentTarget).attr(kendo.attr("page"));
            e.preventDefault();

            this.dataSource.page(page);

            this.trigger("change", { index: page });
        },

        totalPages: function() {
            return Math.ceil((this.dataSource.total() || 0) / this.pageSize());
        },

        pageSize: function() {
            return this.dataSource.pageSize() || this.dataSource.total();
        },

        page: function() {
            return this.dataSource.page() || 1;
        }
    });

    ui.plugin(Pager);
})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        ui = kendo.ui,
        touch = kendo.support.touch,
        getOffset = kendo.getOffset,
        OPEN = "open",
        CLOSE = "close",
        CENTER = "center",
        LEFT = "left",
        RIGHT = "right",
        TOP = "top",
        BOTTOM = "bottom",
        ABSOLUTE = "absolute",
        HIDDEN = "hidden",
        BODY = "body",
        LOCATION = "location",
        POSITION = "position",
        VISIBLE = "visible",
        OFFSET = "offset",
        FITTED = "fitted",
        EFFECTS = "effects",
        ACTIVE = "k-state-active",
        ACTIVEBORDER = "k-state-border",
        ACTIVECHILDREN = ".k-picker-wrap, .k-dropdown-wrap, .k-link",
        MOUSEDOWN = touch ? "touchstart" : "mousedown",
        extend = $.extend,
        proxy = $.proxy,
        Widget = ui.Widget;

    function contains(container, target) {
        return container === target || $.contains(container, target);
    }

    var Popup = Widget.extend({
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            options = that.options;

            that.collisions = that.options.collision.split(" ");

            if (that.collisions.length === 1) {
                that.collisions.push(that.collisions[0]);
            }

            that.element.hide()
                .addClass("k-popup k-group k-reset")
                .css({ position : ABSOLUTE })
                .appendTo($(options.appendTo));


            that.wrapper = $();

            if (options.animation === false) {
                options.animation = { open: { show: true, effects: {} }, close: { hide: true, effects: {} } };
            }

            if (!(EFFECTS in options.animation.close)) {
                options.animation.close = extend({ reverse: true }, options.animation.open, options.animation.close);
            }

            extend(options.animation.open, {
                complete: function() {
                    that.wrapper.css({ overflow: VISIBLE }).css("overflow");
                }
            });

            extend(options.animation.close, {
                complete: function() {
                    that.wrapper.hide();

                    var location = that.wrapper.data(LOCATION),
                        anchor = $(options.anchor),
                        direction, dirClass;

                    if (location) {
                        that.wrapper.css(location);
                    }

                    if (options.anchor != BODY) {
                        direction = anchor.hasClass(ACTIVEBORDER + "-down") ? "down" : "up";
                        dirClass = ACTIVEBORDER + "-" + direction;

                        anchor
                            .removeClass(dirClass)
                            .children(ACTIVECHILDREN)
                            .removeClass(ACTIVE)
                            .removeClass(dirClass);

                        that.element.removeClass(ACTIVEBORDER + "-" + kendo.directions[direction].reverse);
                    }

                    that._closing = false;
                }
            });

            that.bind([OPEN, CLOSE], options);

            $(document.documentElement).bind(MOUSEDOWN, proxy(that._mousedown, that));

            $(window).bind("resize scroll", function() {
                that.close();
            });

            if (options.toggleTarget) {
                $(options.toggleTarget).bind(options.toggleEvent, proxy(that.toggle, that));
            }
        },
        options: {
            name: "Popup",
            toggleEvent: "click",
            origin: BOTTOM + " " + LEFT,
            position: TOP + " " + LEFT,
            anchor: BODY,
            appendTo: BODY,
            collision: "flip fit",
            animation: {
                open: {
                    effects: "slideIn:down",
                    transition: !/chrome/i.test(navigator.userAgent),
                    duration: 200,
                    show: true
                },
                close: { // if close animation effects are defined, they will be used instead of open.reverse
                    duration: 100,
                    show: false,
                    hide: true
                }
            }
        },

        open: function() {
            var that = this,
                element = that.element,
                options = that.options,
                direction = "down",
                animation, wrapper,
                anchor = $(options.anchor);

            if (!that.visible()) {

                if (element.data("animating") || that.trigger(OPEN)) {
                    return;
                }

                that.wrapper = wrapper = kendo.wrap(element)
                                        .css({
                                            overflow: HIDDEN,
                                            display: "block",
                                            position: ABSOLUTE
                                        });

                wrapper.css(POSITION);

                if (options.appendTo == BODY) {
                    wrapper.css(TOP, "-10000px");
                }

                animation = extend({}, options.animation.open);

                if (that._update()) {
                    if (typeof animation.effects == "string" && animation.effects.match(direction)) {
                        direction = "up";
                    }

                    animation.effects = kendo.parseEffects(animation.effects, true);
                }

                if (options.anchor != BODY) {
                    var dirClass = ACTIVEBORDER + "-" + direction;

                    element.addClass(ACTIVEBORDER + "-" + kendo.directions[direction].reverse);

                    anchor
                        .addClass(dirClass)
                        .children(ACTIVECHILDREN)
                        .addClass(ACTIVE)
                        .addClass(dirClass);
                }

                element.data(EFFECTS, animation.effects)
                       .kendoStop(true)
                       .kendoAnimate(animation);
            }
        },

        toggle: function() {
            var that = this;

            that[that.visible() ? CLOSE : OPEN]();
        },

        visible: function() {
            return this.element.is(":" + VISIBLE);
        },

        close: function() {
            var that = this,
                options = that.options,
                animation,
                effects;

            if (that.visible()) {

                if (that._closing || that.trigger(CLOSE)) {
                    return;
                }

                animation = extend({}, options.animation.close);
                effects = that.element.data(EFFECTS);

                that.wrapper = kendo.wrap(that.element).css({ overflow: HIDDEN });

                if (effects) {
                    animation.effects = effects;
                }

                that._closing = true;

                that.element.kendoStop(true).kendoAnimate(animation);
            }
        },

        _mousedown: function(e) {
            var that = this,
                container = that.element[0],
                options = that.options,
                anchor = $(options.anchor)[0],
                toggleTarget = options.toggleTarget,
                target = e.target,
                popup = $(target).closest(".k-popup")[0];


            if (popup && popup !== that.element[0] ){
                return;
            }

            if (!contains(container, target) && !contains(anchor, target) && !(toggleTarget && contains($(toggleTarget)[0], target))) {
                that.close();
            }
        },

        _update: function() {
            return this._position($(window));
        },

        _fit: function(position, size, viewPortSize) {
            var output = 0;

            if (position + size > viewPortSize) {
                output = viewPortSize - (position + size);
            }

            if (position < 0) {
                output = position;
            }

            return output;
        },

        _flip: function(offset, size, anchorSize, viewPortSize, origin, position, boxSize) {
            var output = 0;
                boxSize = boxSize || size;

            if (position !== origin && position !== CENTER && origin !== CENTER) {
                if (offset + boxSize > viewPortSize) {
                    output += -(anchorSize + size);
                }

                if (offset + output < 0) {
                    output += anchorSize + size;
                }
            }
            return output;
        },

        _position: function(viewport) {
            var that = this,
                element = that.element,
                wrapper = that.wrapper,
                options = that.options,
                anchor = $(options.anchor),
                origins = options.origin.toLowerCase().split(" "),
                positions = options.position.toLowerCase().split(" "),
                collisions = that.collisions,
                aligned = false,
                zoomLevel = kendo.support.zoomLevel(),
                zIndex = 10002;

            //calculate z-index
            anchor.parents().andSelf().each(function () {
                var zIndex = $(this).css("zIndex");
                if (!isNaN(zIndex)) {
                    zIndex = Number(zIndex) + 1;
                    return false;
                }
            });

            wrapper.css("zIndex", zIndex);

            if (options.appendTo === Popup.fn.options.appendTo) {
                wrapper.css(that._align(origins, positions));
                aligned = true;
            }

            var pos = getOffset(wrapper, POSITION),
                offset = getOffset(wrapper),
                anchorParent = anchor.offsetParent().parent(".k-animation-container"); // If the parent is positioned, get the current positions

            if (anchorParent.length && anchorParent.data(FITTED)) {
                pos = getOffset(wrapper, POSITION);
                offset = getOffset(wrapper);
            }

            offset = {
                top: offset.top - (window.pageYOffset || document.documentElement.scrollTop || 0),
                left: offset.left - (window.pageXOffset || document.documentElement.scrollLeft || 0)
            };

            if (!that.wrapper.data(LOCATION)) { // Needed to reset the popup location after every closure - fixes the resize bugs.
                wrapper.data(LOCATION, extend({}, pos));
            }

            var offsets = extend({}, offset),
                location = extend({}, pos);

            if (collisions[0] === "fit") {
                location.top += that._fit(offsets.top, wrapper.outerHeight(), viewport.height() / zoomLevel);
            }

            if (collisions[1] === "fit") {
                location.left += that._fit(offsets.left, wrapper.outerWidth(), viewport.width() / zoomLevel);
            }

            if (location.left != pos.left || location.top != pos.top) {
                wrapper.data(FITTED, true);
            } else {
                wrapper.removeData(FITTED);
            }

            var flipPos = extend({}, location);

            if (collisions[0] === "flip") {
                location.top += that._flip(offsets.top, element.outerHeight(), anchor.outerHeight(), viewport.height() / zoomLevel, origins[0], positions[0], wrapper.outerHeight())
            }

            if (collisions[1] === "flip") {
                location.left += that._flip(offsets.left, element.outerWidth(), anchor.outerWidth(), viewport.width() / zoomLevel, origins[1], positions[1], wrapper.outerWidth());
            }

            wrapper.css(location);

            return (location.left != flipPos.left || location.top != flipPos.top);
        },

        _align: function(origin, position) {
            var that = this,
                element = that.wrapper,
                anchor = $(that.options.anchor),
                verticalOrigin = origin[0],
                horizontalOrigin = origin[1],
                verticalPosition = position[0],
                horizontalPosition = position[1],
                anchorOffset = getOffset(anchor),
                width = element.outerWidth(),
                height = element.outerHeight(),
                anchorWidth = anchor.outerWidth(),
                anchorHeight = anchor.outerHeight(),
                top = anchorOffset.top,
                left = anchorOffset.left,
                round = Math.round;

            if (verticalOrigin === BOTTOM) {
                top += anchorHeight;
            }

            if (verticalOrigin === CENTER) {
                top += round(anchorHeight / 2);
            }

            if (verticalPosition === BOTTOM) {
                top -= height;
            }

            if (verticalPosition === CENTER) {
                top -= round(height / 2);
            }

            if (horizontalOrigin === RIGHT) {
                left += anchorWidth;
            }

            if (horizontalOrigin === CENTER) {
                left += round(anchorWidth / 2);
            }

            if (horizontalPosition === RIGHT) {
                left -= width;
            }

            if (horizontalPosition === CENTER) {
                left -= round(width / 2);
            }

            return {
                top: top,
                left: left
            };
        }
    });

    ui.plugin(Popup);
})(jQuery);
(function($, undefined) {
    /**
    * @name kendo.ui.List.Description
    *
    * @section Common class for ComboBox, DropDownList and AutoComplete widgets.
    */
    var kendo = window.kendo,
        ui = kendo.ui,
        Widget = ui.Widget,
        keys = kendo.keys,
        ID = "id",
        LI = "li",
        CLICK = kendo.support.touch ? "touchend" : "click",
        CHANGE = "change",
        CHARACTER = "character",
        FOCUSED = "k-state-focused",
        HOVER = "k-state-hover",
        LOADING = "k-loading",
        SELECT = "select",
        proxy = $.proxy;

    function contains(container, target) {
        return container === target || $.contains(container, target);
    }

    var List = Widget.extend(/** @lends kendo.ui.List */{
        /**
         * Creates a List instance.
         * @constructs
         * @extends kendo.ui.Widget
         */
        init: function(element, options) {
            var that = this, id;

            Widget.fn.init.call(that, element, options);

            that._template();

            that.ul = $('<ul class="k-list k-reset"/>')
                        .css({ overflow: "auto" })
                        .mousedown(function() {
                            setTimeout(function() {
                                clearTimeout(that._bluring);
                            }, 0);
                        })
                        .delegate(LI, CLICK, proxy(that._click, that))
                        .delegate(LI, "mouseenter", function() { $(this).addClass(HOVER); })
                        .delegate(LI, "mouseleave", function() { $(this).removeClass(HOVER); });

            that.list = $("<div class='k-list-container'/>").append(that.ul);

            id = that.element.attr(ID);
            if (id) {
                that.list.attr(ID, id + "-list")
            }

            $(document.documentElement).bind("mousedown", proxy(that._mousedown, that));
        },

        current: function(candidate) {
            var that = this;

            if (candidate !== undefined) {
                if (that._current) {
                    that._current.removeClass(FOCUSED);
                }

                if (candidate) {
                    candidate.addClass(FOCUSED);
                    that._scroll(candidate[0]);
                } else {
                    that._selected = candidate;
                }

                that._current = candidate;
            } else {
                return that._current;
            }
        },

        _accessors: function() {
            var that = this,
                element = that.element,
                options = that.options,
                getter = kendo.getter,
                textField = element.attr(kendo.attr("text-field")),
                valueField = element.attr(kendo.attr("value-field"));

            if (textField) {
                options.dataTextField = textField;
            }

            if (valueField) {
                options.dataValueField = valueField;
            }

            that._text = getter(options.dataTextField);
            that._value = getter(options.dataValueField);
        },

        _blur: function() {
            var that = this;

            that._change();
            that.close();
        },

        _change: function() {
            var that = this,
                value = that.value();

            if (value !== that._old) {
                that.trigger(CHANGE);

                // trigger the DOM change event so any subscriber gets notified
                that.element.trigger(CHANGE);

                that._old = value;
            }
        },

        _click: function(e) {
            this._accept($(e.currentTarget));
        },

        _focus: function(li) {
            var that = this;

            that.select(li);
            that._blur();

            if (that._focused[0] !== document.activeElement) {
                that._focused.focus();
            }
        },

        _height: function(length) {
            if (length) {
                var that = this,
                    list = that.list,
                    visible = that.popup.visible(),
                    height = that.options.height;

                list = list.add(list.parent(".k-animation-container")).show()
                           .height(that.ul[0].scrollHeight > height ? height : "auto");

                if (!visible) {
                    list.hide();
                }
            }
        },

        _popup: function() {
            var that = this,
                list = that.list,
                options = that.options,
                wrapper = that.wrapper,
                width;

            that.popup = new ui.Popup(list, {
                anchor: wrapper,
                open: options.open,
                close: options.close,
                animation: options.animation
            });

            width = wrapper.outerWidth() - (list.outerWidth() - list.width());

            list.css({
                fontFamily: wrapper.css("font-family"),
                width: width
            });
        },

        _toggleHover: function(e) {
            if (!kendo.support.touch)
                $(e.currentTarget).toggleClass(HOVER, e.type === "mouseenter");
        },

        _toggle: function(open) {
            var that = this;
            open = open !== undefined? open : !that.popup.visible();

            that[open ? "open" : "close"]();
        },
        _scroll: function (item) {

            if (!item) return;

            var ul = this.ul[0],
                itemOffsetTop = item.offsetTop,
                itemOffsetHeight = item.offsetHeight,
                ulScrollTop = ul.scrollTop,
                ulOffsetHeight = ul.clientHeight,
                bottomDistance = itemOffsetTop + itemOffsetHeight;

            ul.scrollTop = ulScrollTop > itemOffsetTop
                        ? itemOffsetTop
                        : bottomDistance > (ulScrollTop + ulOffsetHeight)
                        ? bottomDistance - ulOffsetHeight
                        : ulScrollTop;
        },

        _template: function() {
            var that = this,
                options = that.options,
                template = options.template,
                dataTextField = options.dataTextField || "";

            if (!template) {
                //unselectable=on is required for IE to prevent the suggestion box from stealing focus from the input
                that.template = kendo.template("<li class='k-item' unselectable='on'>${data" + (dataTextField ? "." : "") + dataTextField + "}</li>", { useWithBlock: false });
            } else {
                template = kendo.template(template);
                that.template = function(data) {
                    return "<li class='k-item' unselectable='on'>" + template(data) + "</li>";
                };
            }
        }
    });

    $.extend(List, {
        caret: function(element) {
            var caret,
                selection = element.ownerDocument.selection;

            if (selection) {
                caret = Math.abs(selection.createRange().moveStart(CHARACTER, -element.value.length));
            } else {
                caret = element.selectionStart;
            }

            return caret;
        },

        selectText: function (element, selectionStart, selectionEnd) {
            if (element.createTextRange) {
                var textRange = element.createTextRange();
                textRange.collapse(true);
                textRange.moveStart(CHARACTER, selectionStart);
                textRange.moveEnd(CHARACTER, selectionEnd - selectionStart);
                textRange.select();
            } else {
                element.setSelectionRange(selectionStart, selectionEnd);
            }
        },
        inArray: function(node, parentNode) {
            var idx = -1;
            if (!node || node.parentNode !== parentNode) {
                return idx;
            }

            idx = 0;
            while (node = node.previousSibling) {
                idx++;
            }

            return idx;
        }
    });

    kendo.ui.List = List;

    /**
    * @name kendo.ui.Select.Description
    *
    * @section Common class for ComboBox and DropDownList widgets.
    */
    ui.Select = List.extend(/** @lends kendo.ui.Select */{
        /**
         * @extends kendo.ui.List
         * @constructs
         */
        init: function(element, options) {
            List.fn.init.call(this, element, options);
        },

        /**
        * Closes the drop-down list.
        * @example
        * dropdownlist.close();
        *
        * @example
        * combobox.close();
        */
        close: function() {
            this.popup.close();
        },

        _accessor: function(value, idx) {
            var element = this.element[0],
                isSelect = element.nodeName == SELECT,
                option;

            if (value === undefined) {
                if (isSelect) {
                    option = element.options[element.selectedIndex];
                    value = option.value || option.text;
                } else {
                    value = element.value;
                }
                return value;
            } else {
                if (isSelect) {
                    element.selectedIndex = idx;
                } else {
                    element.value = value;
                }
            }
        },

        _hideBusy: function () {
            var that = this;
            clearTimeout(that._busy);
            that._arrow.removeClass(LOADING);
        },

        _showBusy: function () {
            var that = this;

            if (that._busy) {
                return;
            }

            that._busy = setTimeout(function () {
                that._arrow.addClass(LOADING);
            }, 100);
        },

        _data: function() {
            return this.dataSource.view();
        },

        _dataSource: function() {
            var that = this,
                selected,
                element = that.element,
                options = that.options,
                dataSource = options.dataSource || {};

            dataSource = $.isArray(dataSource) ? {data: dataSource} : dataSource;

            if(that.element.is(SELECT)) {
                selected = element.children(":selected");
                if (selected[0]) {
                    options.index = selected.index();
                }

                dataSource.select = element;
                dataSource.fields = [{ field: options.dataTextField },
                                     { field: options.dataValueField }];
            }

            that.dataSource = kendo.data.DataSource.create(dataSource)
                                   .bind(CHANGE, proxy(that.refresh, that))
                                   .bind("requestStart", proxy(that._showBusy, that));
        },

        _enable: function() {
            var that = this,
                options = that.options;

            if (that.element.prop("disabled")) {
                options.enable = false;
            }

            that.enable(options.enable);
        },

        _index: function(value) {
            var that = this,
                idx,
                length,
                data = that._data(),
                valueFromData;

            for (idx = 0, length = data.length; idx < length; idx++) {
                valueFromData = that._value(data[idx]);

                if (valueFromData === undefined) {
                    valueFromData = that._text(data[idx]);
                }

                if (valueFromData == value) {
                    return idx;
                }
            }

            return -1;
        },

        _get: function(li) {
            var that = this,
                idx,
                data = that._data(),
                length;

            if (typeof li === "function") {
                for (idx = 0, length = data.length; idx < length; idx++) {
                    if (li(data[idx])) {
                        li = idx;
                        break;
                    }
                }
            }

            idx = -1;

            if (typeof li === "number") {
                if (li < 0) {
                    return $();
                }

                li = $(that.ul[0].childNodes[li]);
            }

            if (li && li.nodeType) {
                li = $(li);
            }

            return li;
        },

        _move: function(e) {
            var that = this,
                key = e.keyCode,
                ul = that.ul[0],
                current = that._current,
                down = key === keys.DOWN,
                pressed;

            if (key === keys.UP || down) {
                if (e.altKey) {
                    that.toggle(down);
                } else if (down) {
                    that.select(current ? current[0].nextSibling : ul.firstChild);
                    e.preventDefault();
                } else {
                    that.select(current ? current[0].previousSibling : ul.lastChild);
                    e.preventDefault();
                }
                pressed = true;
            } else if (key === keys.ENTER || key === keys.TAB) {

                if (that.popup.visible()) {
                    e.preventDefault();
                }

                that._accept(current);
                pressed = true;
            } else if (key === keys.ESC) {
                that.close();
                pressed = true;
            }

            return pressed;
        },

        _options: function(data) {
            var that = this,
                element = that.element,
                selectedIndex = element[0].selectedIndex,
                value = that.value(),
                length = data.length,
                options = "",
                option,
                dataItem,
                dataText,
                dataValue,
                idx;

            for (idx = 0; idx < length; idx++) {
                option = "<option";
                dataItem = data[idx];
                dataText = that._text(dataItem);
                dataValue = that._value(dataItem);

                if (dataValue || dataValue === 0) {
                    option += ' value="' + dataValue + '"';
                }

                option += ">";

                if (dataText !== undefined) {
                    option += dataText;
                }

                option += "</option>";
                options += option;
            }

            element.html(options);
            element[0].selectedIndex = selectedIndex;
        },

        _reset: function() {
            var that = this,
                element = that.element;

            element.closest("form")
                   .bind("reset", function() {
                       setTimeout(function() {
                            that.value(element[0].value);
                       });
                   });
        }
    });

})(jQuery);
(function($, undefined) {
    /**
    * @name kendo.ui.Calendar.Description
    *
    * @section
    *   <p>
    *       The Calendar widget renders a graphical calendar that supports navigation and selection.
    *       It supports custom templates for "month" view, configurable options for min and max date,
    *       start view and the depth of the navigation.
    *   </p>
    *
    *   <h3>Getting Started</h3>
    *
    * @exampleTitle Creating a Calendar from existing DIV element
    * @example
    * <!-- HTML -->
    * <div id="calendar"></div>
    *
    * @exampleTitle Calendar initialization
    * @example
    *   $(document).ready(function(){
    *      $("#calendar").kendoCalendar();
    *   });
    * @section
    *  <p>
    *      When a Calendar is initialized, it will automatically be displayed near the
    *      location of the used HTML element.
    *  </p>
    *  <h3>Configuring Calendar behaviors</h3>
    *  <p>
    *      Calendar provides many configuration options that can be easily set during initialization.
    *      Among the properties that can be controlled:
    *  </p>
    *  <ul>
    *      <li>Selected date</li>
    *      <li>Minimum/Maximum date</li>
    *      <li>Start view</li>
    *      <li>Define the navigation depth (last view to which end user can navigate)</li>
    *      <li>Day template</li>
    *      <li>Footer template</li>
    *  </ul>
    * @exampleTitle Create Calendar with selected date and defined min and max date
    * @example
    *  $("#calendar").kendoCalendar({
    *      value: new Date(),
    *      min: new Date(1950, 0, 1),
    *      max: new Date(2049, 11, 31)
    *  });
    * <p>
    *   Calendar will not navigate to dates less than min and bigger than max date.
    * </p>
    * @section
    * <h3>Define start view and navigation depth</h3>
    * <p>
    *    The first rendered view can be defined with "start" option. Navigation depth
    *    can be controlled with "depth" option. Predefined views are:
    *    <ul>
    *       <li>"month" - shows the days from the month</li>
    *       <li>"year" - shows the months of the year</li>
    *       <li>"decade" - shows the years from the decade</li>
    *       <li>"century" - shows the decades from the century</li>
    *    </ul>
    * </p>
    *
    * @exampleTitle Create Calendar, which allows to select month
    * @example
    *  $("#calendar").kendoCalendar({
    *      start: "year",
    *      depth: "year"
    *  });
    *
    *  @section
    * <h3>Customize day template</h3>
    * <p>
    *   Calendar allows to customize content of the rendered day in the "month" view.
    *
    * @exampleTitle Create Calendar with custom template
    * @example
    *  $("#calendar").kendoCalendar({
    *      month: {
    *         content: '<div class="custom"><#=data.value#></div>'
    *      }
    *  });
    *  @section
    *  <p>
    *     This templates wraps the "value" in a div HTML element. Here is an example of the object
    *     passed to the template function:
    *  </p>
    * @exampleTitle Structure of the data object passed to the template
    * @example
    *  data = {
    *    date: date, // Date object corresponding to the current cell
    *    title: kendo.toString(date, "D"),
    *    value: date.getDate(),
    *    dateString: "2011/0/1" //formatted date using yyyy/MM/dd format and month is zero based
    *  };
    */
    var kendo = window.kendo,
        ui = kendo.ui,
        Widget = ui.Widget,
        parse = kendo.parseDate,
        template = kendo.template,
        transitions = kendo.support.transitions,
        transitionOrigin = transitions ? transitions.css + "transform-origin" : "",
        cellTemplate = template('<td#=data.cssClass#><a class="k-link" href="\\#" data-#=data.ns#value="#=data.dateString#">#=data.value#</a></td>', { useWithBlock: false }),
        emptyCellTemplate = template("<td>&nbsp;</td>", { useWithBlock: false }),
        CLICK = kendo.support.touch ? "touchend" : "click",
        MIN = "min",
        LEFT = "left",
        SLIDE = "slide",
        MONTH = "month",
        CENTURY = "century",
        CHANGE = "change",
        NAVIGATE = "navigate",
        VALUE = "value",
        HOVER = "k-state-hover",
        DISABLED = "k-state-disabled",
        OTHERMONTH = "k-other-month",
        OTHERMONTHCLASS = ' class="' + OTHERMONTH + '"',
        CELLSELECTOR = "td:has(.k-link)",
        MOUSEENTER = "mouseenter",
        MOUSELEAVE = "mouseleave",
        MS_PER_MINUTE = 60000,
        MS_PER_DAY = 86400000,
        PREVARROW = "_prevArrow",
        NEXTARROW = "_nextArrow",
        proxy = $.proxy,
        extend = $.extend,
        DATE = Date,
        views = {
            month: 0,
            year: 1,
            decade: 2,
            century: 3
        };

    var Calendar = Widget.extend(/** @lends kendo.ui.Calendar.prototype */{
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Date} [value] <null> Specifies the selected date.
         * @option {Date} [min] <Date(1900, 0, 1)> Specifies the minimum date, which the calendar can show.
         * @option {Date} [max] <Date(2099, 11, 31)> Specifies the maximum date, which the calendar can show.
         * @option {String} [footer] <> Specifies the content of the footer. If false, the footer will not be rendered.
         * @option {String} [format] <MM/dd/yyyy> Specifies the format, which is used to parse value set with value() method.
         * @option {String} [start] <month> Specifies the start view.
         * @option {String} [depth] Specifies the navigation depth.
         */
        init: function(element, options) {
            var that = this, value;

            Widget.fn.init.call(that, element, options);

            element = that.element;
            options = that.options;

            element.addClass("k-widget k-calendar");

            that._templates();

            that._header();

            if (options.footer) {
                that._footer();
            }

            element
                .delegate(CELLSELECTOR, MOUSEENTER, mouseenter)
                .delegate(CELLSELECTOR, MOUSELEAVE, mouseleave)
                .delegate(CELLSELECTOR, CLICK, proxy(that._click, that));

            that.bind([
                /**
                * Fires when the selected date is changed
                * @name kendo.ui.Calendar#change
                * @event
                * @param {Event} e
                */
                /**
                * Fires when navigate
                * @name kendo.ui.Calendar#navigate
                * @event
                * @param {Event} e
                */
                CHANGE,
                NAVIGATE
            ], options);

            value = options.value;
            validate(options);

            that._index = views[options.start];
            that._current = new DATE(restrictValue(value, options.min, options.max));

            that.value(value);
        },

        options: {
            name: "Calendar",
            value: null,
            min: new DATE(1900, 0, 1),
            max: new DATE(2099, 11, 31),
            footer : '#= kendo.toString(data,"D") #',
            start: MONTH,
            depth: MONTH,
            animation: {
                horizontal: {
                    effects: SLIDE,
                    duration: 500,
                    divisor: 2
                },
                vertical: {
                    effects: "zoomIn",
                    duration: 400
                }
            }
        },

        /**
        * Gets/Sets the min value of the calendar.
        * @param {Date|String} value The min date to set.
        * @returns {Date} The min value of the calendar.
        * @example
        * var calendar = $("#calendar").data("kendoCalendar");
        *
        * // get the min value of the calendar.
        * var min = calendar.min();
        *
        * // set the min value of the calendar.
        * calendar.min(new Date(1900, 0, 1));
        */
        min: function(value) {
            return this._option(MIN, value);
        },

        /**
        * Gets/Sets the max value of the calendar.
        * @param {Date|String} value The max date to set.
        * @returns {Date} The max value of the calendar.
        * @example
        * var calendar = $("#calendar").data("kendoCalendar");
        *
        * // get the max value of the calendar.
        * var max = calendar.max();
        *
        * // set the max value of the calendar.
        * calendar.max(new Date(2100, 0, 1));
        */
        max: function(value) {
            return this._option("max", value);
        },

        /**
        * Navigates to the past
        * @example
        * calendar.navigateToPast();
        */
        navigateToPast: function() {
            this._navigate(PREVARROW, -1);
        },

        /**
        * Navigates to the future
        * @example
        * calendar.navigateToFuture();
        */
        navigateToFuture: function() {
            this._navigate(NEXTARROW, 1);
        },

        /**
        * Navigates to the upper view
        * @example
        * calendar.navigateUp();
        */
        navigateUp: function() {
            var that = this,
                index = that._index;

            if (that._title.hasClass(DISABLED)) {
                return;
            }

            that.navigate(that._current, ++index);
        },

        /**
        * Navigates to the lower view
        * @param {Date} value Desired date
        * @example
        * calendar.navigateDown(value);
        */
        navigateDown: function(value) {
            var that = this,
            index = that._index,
            depth = that.options.depth;

            if (!value) {
                return;
            }

            if (index === views[depth]) {
                if (+that._value != +value) {
                    that.value(value);
                    that.trigger(CHANGE);
                }
                return;
            }

            that.navigate(value, --index);
        },

        /**
        * Navigates to view
        * @param {Date} value Desired date
        * @param {String} view Desired view
        * @example
        * calendar.navigate(value, view);
        */
        navigate: function(value, view) {
            view = isNaN(view) ? views[view] : view;

            var that = this,
                options = that.options,
                min = options.min,
                max = options.max,
                title = that._title,
                from = that._table,
                selectedValue = that._value,
                currentValue = that._current,
                future = value && +value > +currentValue,
                vertical = view !== undefined && view !== that._index,
                to, currentView, compare;

            //do not navigate if the view is still animating
            if (from && from.parent().data("animating")) {
                return;
            }

            if (!value) {
                value = currentValue;
            } else {
                that._current = value = new DATE(restrictValue(value, min, max))
            }

            if (view === undefined) {
                view = that._index;
            } else {
                that._index = view;
            }

            that._view = currentView = calendar.views[view];
            compare = currentView.compare;

            title.toggleClass(DISABLED, view === views[CENTURY])
            that[PREVARROW].toggleClass(DISABLED, compare(value, min) < 1);
            that[NEXTARROW].toggleClass(DISABLED, compare(value, max) > -1);

            if (!from || that._changeView) {
                title.html(currentView.title(value));

                that._table = to = $(currentView.content(extend({
                    min: min,
                    max: max,
                    date: value
                }, that[currentView.name])));

                that._animate({
                    from: from,
                    to: to,
                    vertical: vertical,
                    future: future
                });

                that.trigger(NAVIGATE);
            }

            if (view === views[options.depth] && selectedValue) {
                that._class("k-state-selected", currentView.toDateString(selectedValue));
            }

            that._changeView = true;
        },

        /**
        * Gets/Sets the value of the calendar.
        * @param {Date|String} value The date to set.
        * @returns {Date} The value of the calendar.
        * @example
        * var calendar = $("#calendar").data("kendoCalendar");
        *
        * // get the value of the calendar.
        * var value = calendar.value();
        *
        * // set the value of the calendar.
        * calendar.value(new Date());
        */
        value: function(value) {
            var that = this,
            view = that._view,
            options = that.options,
            min = options.min,
            max = options.max;

            if (value === undefined) {
                return that._value;
            }

            value = parse(value, options.format);

            if (value !== null) {
                value = new DATE(value);

                if (!isInRange(value, min, max)) {
                    value = null;
                }
            }

            that._value = value;
            that._changeView = !value || view && view.compare(value, that._current) !== 0;

            that.navigate(value);
        },

        _animate: function(options) {
            var that = this,
                from = options.from,
                to = options.to;

            if (!from) {
                to.insertAfter(that.element[0].firstChild);
            } else if (!from.is(":visible") || that.options.animation === false) {
                to.insertAfter(from);
                from.remove();
            } else {
                that[options.vertical ? "_vertical" : "_horizontal"](from, to, options.future);
            }
        },

        _horizontal: function(from, to, future) {
            var that = this,
                horizontal = that.options.animation.horizontal,
                effects = horizontal.effects,
                viewWidth = from.outerWidth();

                if (effects && effects.indexOf(SLIDE) != -1) {
                    from.add(to).css({ width: viewWidth });

                    from.wrap("<div/>");

                    from.parent()
                    .css({
                        position: "relative",
                        width: viewWidth * 2,
                        "float": LEFT,
                        left: future ? 0 : -viewWidth
                    });

                    to[future ? "insertAfter" : "insertBefore"](from);

                    extend(horizontal, {
                        effects: SLIDE + ":" + (future ? LEFT : "right"),
                        complete: function() {
                            from.remove();
                            to.unwrap();
                        }
                    });

                    from.parent().kendoStop(true, true).kendoAnimate(horizontal);
                }
        },

        _vertical: function(from, to) {
            var that = this,
                vertical = that.options.animation.vertical,
                effects = vertical.effects,
                viewWidth = from.outerWidth(),
                cell, position;

            if (effects && effects.indexOf("zoomIn") != -1) {
                to.css({
                    position: "absolute",
                    top: from.prev().outerHeight(),
                    left: 0
                }).insertBefore(from);

                if (transitionOrigin) {
                    cell = that._cellByDate(that._view.toDateString(that._current));
                    position = cell.position();
                    position = (position.left + parseInt(cell.width() / 2)) + "px" + " " + (position.top + parseInt(cell.height() / 2) + "px");
                    to.css(transitionOrigin, position);
                }

                from.kendoStop(true, true).kendoAnimate({
                    effects: "fadeOut",
                    duration: 600,
                    complete: function() {
                        from.remove();
                        to.css({
                            position: "static",
                            top: 0,
                            left: 0
                        });
                    }
                });

                to.kendoStop(true, true).kendoAnimate(vertical);
            }
        },

        _click: function(e) {
            var that = this,
                options = that.options,
                currentValue = that._current,
                link = $(e.currentTarget.firstChild),
                value = link.attr(kendo.attr(VALUE)).split("/");

            //Safari cannot create corretly date from "1/1/2090"
            value = new DATE(value[0], value[1], value[2]);

            e.preventDefault();

            if (link.parent().hasClass(OTHERMONTH)) {
                currentValue = value;
            } else {
                that._view.setDate(currentValue, value);
            }

            that.navigateDown(restrictValue(currentValue, options.min, options.max));
        },

        _focus: function(value) {
            var that = this,
                view = that._view;

            if (view.compare(value, that._current) !== 0) {
                that.navigate(value);
            } else {
                that._current = value;
            }

            that._class("k-state-focused", view.toDateString(value));
        },

        _footer: function() {
            var that = this,
                element = that.element,
                today = new DATE();

            if (!element.find(".k-footer")[0]) {
                element.append('<div class="k-footer"><a href="#" class="k-link k-nav-today"></a></div>');
            }

            that._today = element
                        .find(".k-nav-today")
                        .html(template(that.options.footer)(today))
                        .attr("title", kendo.toString(today, "D"))
                        .bind(CLICK, proxy(that._todayClick, that));
        },

        _header: function() {
            var that = this,
            element = that.element,
            links;

            if (!element.find(".k-header")[0]) {
                element.html('<div class="k-header">'
                           + '<a href="#" class="k-link k-nav-prev"><span class="k-icon k-arrow-prev"></span></a>'
                           + '<a href="#" class="k-link k-nav-fast"></a>'
                           + '<a href="#" class="k-link k-nav-next"><span class="k-icon k-arrow-next"></span></a>'
                           + '</div>');
            }

            links = element.find(".k-link")
                           .hover(mouseenter, mouseleave)
                           .click(false);

            that._title = links.eq(1).bind(CLICK, proxy(that.navigateUp, that));
            that[PREVARROW] = links.eq(0).bind(CLICK, proxy(that.navigateToPast, that));
            that[NEXTARROW] = links.eq(2).bind(CLICK, proxy(that.navigateToFuture, that));
        },

        _cellByDate: function(value) {
            return this._table.find("td:not(." + OTHERMONTH + ")")
                       .filter(function() {
                           return $(this.firstChild).attr(kendo.attr(VALUE)) === value;
                       });
        },

        _class: function(className, value) {
            this._table.find("td:not(." + OTHERMONTH + ")")
                .removeClass(className)
                .filter(function() {
                   return $(this.firstChild).attr(kendo.attr(VALUE)) === value;
                })
                .addClass(className);
        },

        _navigate: function(arrow, modifier) {
            var that = this,
                index = that._index + 1,
                currentValue = new DATE(that._current);

            arrow = that[arrow];

            if (!arrow.hasClass(DISABLED)) {
                if (index > 3) {
                    currentValue.setFullYear(currentValue.getFullYear() + 100 * modifier);
                } else {
                    calendar.views[index].setDate(currentValue, modifier);
                }

                that.navigate(currentValue);
            }
        },

        _option: function(option, value) {
            var that = this,
                options = that.options,
                selectedValue = +that._value,
                bigger, navigate;

            if (value === undefined) {
                return options[option];
            }

            value = parse(value, options.format);

            if (!value) {
                return;
            }

            options[option] = new DATE(value);

            navigate = that._view.compare(value, that._current);

            if (option === MIN) {
                bigger = +value > selectedValue;
                navigate = navigate > -1
            } else {
                bigger = selectedValue > +value;
                navigate = navigate < 1;
            }

            if (bigger) {
                that.value(null);
            } else if (navigate) {
                that.navigate();
            }
        },

        _todayClick: function(e) {
            var that = this,
                depth = views[that.options.depth],
                today = new DATE();

            e.preventDefault();

            if (that._view.compare(that._current, today) === 0 && that._index == depth) {
                that._changeView = false;
            }

            that._value = today;
            that.navigate(today, depth);

            that.trigger(CHANGE);
        },

        _templates: function() {
            var that = this,
                month = that.options.month || {},
                content = month.content,
                empty = month.empty;

            that.month = {
                content: template('<td#=data.cssClass#><a class="k-link" href="\\#" ' + kendo.attr("value") + '="#=data.dateString#" title="#=data.title#">' + (content || "#=data.value#") + '</a></td>', { useWithBlock: !!content }),
                empty: template("<td>" + (empty || "&nbsp;") + "</td>", { useWithBlock: !!empty })
            };
        }
    });

    ui.plugin(Calendar);

    var calendar = {
        firstDayOfMonth: function (date) {
            return new DATE(
                date.getFullYear(),
                date.getMonth(),
                1
            );
        },

        firstVisibleDay: function (date) {
            var firstDay = kendo.culture().calendar.firstDay,
            firstVisibleDay = new DATE(date.getFullYear(), date.getMonth(), 0, date.getHours(), date.getMinutes(), date.getSeconds(), date.getMilliseconds());

            while (firstVisibleDay.getDay() != firstDay) {
                calendar.setTime(firstVisibleDay, -1 * MS_PER_DAY)
            }

            return firstVisibleDay;
        },

        setTime: function (date, time) {
            var tzOffsetBefore = date.getTimezoneOffset(),
            resultDATE = new DATE(date.getTime() + time),
            tzOffsetDiff = resultDATE.getTimezoneOffset() - tzOffsetBefore;

            date.setTime(resultDATE.getTime() + tzOffsetDiff * MS_PER_MINUTE);
        },
        views: [{
            name: MONTH,
            title: function(date) {
                return kendo.culture().calendar.months.names[date.getMonth()] + " " + date.getFullYear();
            },
            content: function(options) {
                var that = this,
                idx = 0,
                min = options.min,
                max = options.max,
                date = options.date,
                currentCalendar = kendo.culture().calendar,
                firstDayIdx = currentCalendar.firstDay,
                days = currentCalendar.days,
                names = shiftArray(days.names, firstDayIdx),
                abbr = shiftArray(days.namesAbbr, firstDayIdx),
                short = shiftArray(days.namesShort, firstDayIdx),
                start = calendar.firstVisibleDay(date),
                firstDayOfMonth = that.first(date),
                lastDayOfMonth = that.last(date),
                toDateString = that.toDateString,
                today = new DATE(),
                html = '<table class="k-content" cellspacing="0"><thead><tr>';

                for (; idx < 7; idx++) {
                    html += '<th abbr="' + abbr[idx] + '" scope="col" title="' + names[idx] + '">' + short[idx] + '</th>';
                }

                today = +new DATE(today.getFullYear(), today.getMonth(), today.getDate());

                return view({
                    cells: 42,
                    perRow: 7,
                    html: html += "</tr></thead><tbody><tr>",
                    start: new DATE(start.getFullYear(), start.getMonth(), start.getDate()),
                    min: new DATE(min.getFullYear(), min.getMonth(), min.getDate()),
                    max: new DATE(max.getFullYear(), max.getMonth(), max.getDate()),
                    content: options.content,
                    empty: options.empty,
                    setter: that.setDate,
                    build: function(date, idx) {
                        var cssClass = [],
                        day = date.getDay();

                        if (date < firstDayOfMonth || date > lastDayOfMonth) {
                            cssClass.push(OTHERMONTH);
                        }

                        if (+date === today) {
                            cssClass.push("k-today");
                        }

                        if (day === 0 || day === 6) {
                            cssClass.push("k-weekend");
                        }

                        return {
                            date: date,
                            ns: kendo.ns,
                            title: kendo.toString(date, "D"),
                            value: date.getDate(),
                            dateString: toDateString(date),
                            cssClass: cssClass[0] ? ' class="' + cssClass.join(" ") + '"' : ""
                        };
                    }
                });
            },
            first: function(date) {
                return calendar.firstDayOfMonth(date);
            },
            last: function(date) {
                return new DATE(date.getFullYear(), date.getMonth() + 1, 0);
            },
            compare: function(date1, date2) {
                var result,
                month1 = date1.getMonth(),
                year1 = date1.getFullYear(),
                month2 = date2.getMonth(),
                year2 = date2.getFullYear();

                if (year1 > year2) {
                    result = 1;
                } else if (year1 < year2) {
                    result = -1;
                } else {
                    result = month1 == month2 ? 0 : month1 > month2 ? 1 : -1;
                }

                return result;
            },
            setDate: function(date, value) {
                if (value instanceof DATE) {
                    date.setFullYear(value.getFullYear(), value.getMonth(), value.getDate());
                } else {
                    calendar.setTime(date, value * MS_PER_DAY);
                }
            },
            toDateString: function(date) {
                return date.getFullYear() + "/" + date.getMonth() + "/" + date.getDate();
            }
        },
        {
            name: "year",
            title: function(date) {
                return date.getFullYear();
            },
            content: function(options) {
                var namesAbbr = kendo.culture().calendar.months.namesAbbr,
                toDateString = this.toDateString,
                min = options.min,
                max = options.max;

                return view({
                    min: new DATE(min.getFullYear(), min.getMonth(), 1),
                    max: new DATE(max.getFullYear(), max.getMonth(), 1),
                    start: new DATE(options.date.getFullYear(), 0, 1),
                    setter: this.setDate,
                    build: function(date) {
                        return {
                            value: namesAbbr[date.getMonth()],
                            ns: kendo.ns,
                            dateString: toDateString(date),
                            cssClass: ""
                        };
                    }
                });
            },
            first: function(date) {
                return new DATE(date.getFullYear(), 0, date.getDate());
            },
            last: function(date) {
                return new DATE(date.getFullYear(), 11, date.getDate());
            },
            compare: function(date1, date2){
                return compare(date1, date2);
            },
            setDate: function(date, value) {
                if (value instanceof DATE) {
                    date.setFullYear(value.getFullYear(),
                    value.getMonth(),
                    date.getDate());
                } else {
                    var month = date.getMonth() + value;

                    date.setMonth(month);

                    if (month > 11) {
                        month -= 12;
                    }

                    if (date.getMonth() != month) {
                        date.setDate(0);
                    }
                }
            },
            toDateString: function(date) {
                return date.getFullYear() + "/" + date.getMonth() + "/1";
            }
        },
        {
            name: "decade",
            title: function(date) {
                var start = date.getFullYear();

                start = start - start % 10;

                return start + "-" + (start + 9);
            },
            content: function(options) {
                var year = options.date.getFullYear(),
                toDateString = this.toDateString;

                return view({
                    start: new DATE(year - year % 10 - 1, 0, 1),
                    min: new DATE(options.min.getFullYear(), 0, 1),
                    max: new DATE(options.max.getFullYear(), 0, 1),
                    setter: this.setDate,
                    build: function(date, idx) {
                        return {
                            value: date.getFullYear(),
                            ns: kendo.ns,
                            dateString: toDateString(date),
                            cssClass: idx == 0 || idx == 11 ? OTHERMONTHCLASS : ""
                        };
                    }
                });
            },
            first: function(date) {
                var year = date.getFullYear();
                return new DATE(year - year % 10, date.getMonth(), date.getDate());
            },
            last: function(date) {
                var year = date.getFullYear();
                return new DATE(year - year % 10 + 9, date.getMonth(), date.getDate());
            },
            compare: function(date1, date2) {
                return compare(date1, date2, 10);
            },
            setDate: function(date, value) {
                setDate(date, value, 1);
            },
            toDateString: function(date) {
                return date.getFullYear() + "/0/1";
            }
        },
        {
            name: CENTURY,
            title: function(date) {
                var start = date.getFullYear();

                start = start - start % 100;

                return start + "-" + (start + 99);
            },
            content: function(options) {
                var year = options.date.getFullYear(),
                minYear = options.min.getFullYear(),
                maxYear = options.max.getFullYear(),
                toDateString = this.toDateString;

                minYear = minYear - minYear % 10;
                maxYear = maxYear - maxYear % 10;

                if (maxYear - minYear < 10) {
                    maxYear = minYear + 9;
                }

                return view({
                    start: new DATE(year - year % 100 - 10, 0, 1),
                    min: new DATE(minYear, 0, 1),
                    max: new DATE(maxYear, 0, 1),
                    setter: this.setDate,
                    build: function(date, idx) {
                        var year = date.getFullYear();
                        return {
                            value: year + " - " + (year + 9),
                            ns: kendo.ns,
                            dateString: toDateString(date),
                            cssClass: idx == 0 || idx == 11 ? OTHERMONTHCLASS : ""
                        };
                    }
                });
            },
            first: function(date) {
                var year = date.getFullYear();
                return new DATE(year - year % 100, date.getMonth(), date.getDate());
            },
            last: function(date) {
                var year = date.getFullYear();
                return new DATE(year - year % 100 + 99, date.getMonth(), date.getDate());
            },
            compare: function(date1, date2) {
                return compare(date1, date2, 100);
            },
            setDate: function(date, value) {
                setDate(date, value, 10);
            },
            toDateString: function(date) {
                var year = date.getFullYear();
                return (year - year % 10) + "/0/1";
            }
        }]
    }

    function view(options) {
        var idx = 0,
            data,
            view = options.view,
            min = options.min,
            max = options.max,
            start = options.start,
            setter = options.setter,
            build = options.build,
            length = options.cells || 12,
            cellsPerRow = options.perRow || 4,
            toDateString = options.toDateString,
            content = options.content || cellTemplate,
            empty = options.empty || emptyCellTemplate,
            html = options.html || '<table class="k-content k-meta-view" cellspacing="0"><tbody><tr>';

        for(; idx < length; idx++) {
            if (idx > 0 && idx % cellsPerRow == 0) {
                html += "</tr><tr>";
            }

            data = build(start, idx);

            html += isInRange(start, min, max) ? content(data) : empty(data);

            setter(start, 1);
        }

        return html + "</tr></tbody></table>";
    }

    function compare(date1, date2, modifier) {
        var year1 = date1.getFullYear(),
            start  = date2.getFullYear(),
            end = start,
            result = 0;

        if (modifier) {
            start = start - start % modifier;
            end = start - start % modifier + modifier - 1;
        }

        if (year1 > end) {
            result = 1;
        } else if (year1 < start) {
            result = -1;
        }

        return result;
    }

    function restrictValue (value, min, max) {
        var today = new DATE();

        today = new DATE(today.getFullYear(), today.getMonth(), today.getDate());

        if (value) {
            today = new DATE(value);
        }

        if (min > today) {
            today = new DATE(min);
        } else if (max < today) {
            today = new DATE(max);
        }
        return today;
    }

    function isInRange(date, min, max) {
        return +date >= +min && +date <= +max;
    }

    function shiftArray(array, idx) {
        return array.slice(idx).concat(array.slice(0, idx));
    }

    function setDate(date, value, multiplier) {
        value = value instanceof DATE ? value.getFullYear() : date.getFullYear() + multiplier * value;
        date.setFullYear(value);
    }

    function mouseenter() {
        $(this).addClass(HOVER);
    }

    function mouseleave() {
        $(this).removeClass(HOVER);
    }

    function validate(options) {
        var start = views[options.start],
            depth = views[options.depth],
            format = options.format || kendo.culture().calendar.patterns.d;

        if (isNaN(start)) {
            start = 0;
            options.start = MONTH;
        }

        if (depth === undefined || depth > start) {
            options.depth = MONTH;
        }

        if (format.slice(0,3) === "{0:") {
            format = format.slice(3, format.length - 1);
        }

        options.format = format;
    }

    calendar.restrictValue = restrictValue;
    calendar.isInRange = isInRange;
    calendar.validate = validate;
    calendar.viewsEnum = views;

    kendo.calendar = calendar;
})(jQuery);
(function($, undefined) {
    /**
    * @name kendo.ui.DatePicker.Description
    *
    * @section
    *   <p>
    *       The DatePicker widget allows the end user to select a date from a calendar or by direct input.
    *       It supports custom templates for "month" view, configurable options for min and max date,
    *       start view and the depth of the navigation.
    *   </p>
    *
    *   <h3>Getting Started</h3>
    *
    * @exampleTitle Creating a DatePicker from existing INPUT element
    * @example
    * <!-- HTML -->
    * <input id="datepicker"/>
    *
    * @exampleTitle DatePicker initialization
    * @example
    *   $(document).ready(function(){
    *      $("#datepicker").kendoDatePicker();
    *   });
    * @section
    *  <p>
    *      When a DatePicker is initialized, it will automatically be displayed near the
    *      location of the used HTML element.
    *  </p>
    *  <h3>Configuring DatePicker behaviors</h3>
    *  <p>
    *      DatePicker provides configuration options that can be easily set during initialization.
    *      Among the properties that can be controlled:
    *  </p>
    *  <ul>
    *      <li>Selected date</li>
    *      <li>Minimum/Maximum date</li>
    *      <li>Define format</li>
    *      <li>Start view</li>
    *      <li>Define the navigation depth (last view to which end user can navigate)</li>
    *  </ul>
    * @exampleTitle Create DatePicker with selected date and defined min and max date
    * @example
    *  $("#datepicker").kendoDatePicker({
    *      value: new Date(),
    *      min: new Date(1950, 0, 1),
    *      max: new Date(2049, 11, 31)
    *  });
    *  @section
    * <p>
    *   DatePicker will set the value only if the entered date is valid and if it is in the defined range
    * </p>
    * @section
    * <h3>Define start view and navigation depth</h3>
    * <p>
    *    The first rendered view can be defined with "start" option. Navigation depth
    *    can be controlled with "depth" option. Predefined views are:
    *    <ul>
    *       <li>"month" - shows the days from the month</li>
    *       <li>"year" - shows the months of the year</li>
    *       <li>"decade" - shows the years from the decade</li>
    *       <li>"century" - shows the decades from the century</li>
    *    </ul>
    * </p>
    *
    * @exampleTitle Create Month picker
    * @example
    *  $("#datepicker").kendoDatePicker({
    *      start: "year",
    *      depth: "year"
    *  });
    *
    */

    var kendo = window.kendo,
    ui = kendo.ui,
    touch = kendo.support.touch,
    Widget = ui.Widget,
    parse = kendo.parseDate,
    keys = kendo.keys,
    template = kendo.template,
    DIV = "<div />",
    SPAN = "<span />",
    CLICK = (touch ? "touchend" : "click"),
    OPEN = "open",
    CLOSE = "close",
    CHANGE = "change",
    NAVIGATE = "navigate",
    DATEVIEW = "dateView",
    DISABLED = "disabled",
    DEFAULT = "k-state-default",
    FOCUSED = "k-state-focused",
    SELECTED = "k-state-selected",
    STATEDISABLED = "k-state-disabled",
    HOVER = "k-state-hover",
    HOVEREVENTS = "mouseenter mouseleave",
    MOUSEDOWN = (touch ? "touchstart" : "mousedown"),
    MIN = "min",
    MAX = "max",
    MONTH = "month",
    FIRST = "first",
    calendar = kendo.calendar,
    views = calendar.viewsEnum,
    isInRange = calendar.isInRange,
    restrictValue = calendar.restrictValue,
    proxy = $.proxy,
    DATE = Date,
    sharedCalendar;

    var DateView = function(options) {
        var that = this,
            body = document.body;

        if (!sharedCalendar) {
            sharedCalendar = new ui.Calendar($(DIV).hide().appendTo(body));
        }

        that.calendar = sharedCalendar;
        that.options = options = options || {};
        that.popup = new ui.Popup($(DIV).addClass("k-calendar-container").appendTo(body), options);

        that._templates();

        that.value(options.value);
    };

    DateView.prototype = {
        _calendar: function() {
            var that = this,
                popup = that.popup,
                options = that.options,
                calendar = that.calendar,
                element = calendar.element;

            if (element.data(DATEVIEW) !== that) {

                element.appendTo(popup.element)
                       .data(DATEVIEW, that)
                       .bind(CLICK, proxy(that._click, that))
                       .unbind(MOUSEDOWN)
                       .bind(MOUSEDOWN, options.clearBlurTimeout)
                       .show();

                calendar.unbind(CHANGE)
                        .unbind(NAVIGATE)
                        .bind(NAVIGATE, proxy(that._navigate, that))
                        .bind(CHANGE, options);

                calendar.month = that.month;
                calendar.options.depth = options.depth;

                calendar._today.html(that.footer(new DATE()));

                calendar.min(options.min);
                calendar.max(options.max);

                calendar.navigate(that._value, options.start);
                that.value(that._value);
            }
        },

        open: function() {
            var that = this;

            that._calendar();
            setTimeout( function () { that.popup.open(); });
        },

        close: function() {
            this.popup.close();
        },

        min: function(value) {
            this._option(MIN, value);
        },

        max: function(value) {
            this._option(MAX, value);
        },

        toggle: function() {
            var that = this;

            that[that.popup.visible() ? CLOSE : OPEN]();
        },

        move: function(e) {
            var that = this,
                options = that.options,
                min = options.min,
                max = options.max,
                currentValue = new DATE(that._current),
                calendar = that.calendar,
                index = calendar._index,
                view = calendar._view,
                key = e.keyCode,
                dateString, value, prevent, method;

            if (key == keys.ESC) {
                that.close();
                return;
            }

            if (e.altKey) {
                if (key == keys.DOWN) {
                    that.open();
                    prevent = true;
                } else if (key == keys.UP) {
                    that.close();
                    prevent = true;
                }
            }

            if (!that.popup.visible()) {
                return;
            }

            if (e.ctrlKey) {
                if (key == keys.RIGHT) {
                    calendar.navigateToFuture();
                    prevent = true;
                } else if (key == keys.LEFT) {
                    calendar.navigateToPast();
                    prevent = true;
                } else if (key == keys.UP) {
                    calendar.navigateUp();
                    prevent = true;
                } else if (key == keys.DOWN) {
                    that._navigateDown();
                    prevent = true;
                }
            } else {
                if (key == keys.RIGHT) {
                    value = 1;
                    prevent = true;
                } else if (key == keys.LEFT) {
                    value = -1;
                    prevent = true;
                } else if (key == keys.UP) {
                    value = index === 0 ? -7 : -4;
                    prevent = true;
                } else if (key == keys.DOWN) {
                    value = index === 0 ? 7 : 4;
                    prevent = true;
                } else if (key == keys.ENTER) {
                    that._navigateDown();
                    prevent = true;
                } else if (key == keys.HOME || key == keys.END) {
                    method = key == keys.HOME ? FIRST : "last";
                    currentValue = view[method](currentValue);
                    prevent = true;
                } else if (key == keys.PAGEUP) {
                    prevent = true;
                    calendar.navigateToPast();
                } else if (key == keys.PAGEDOWN) {
                    prevent = true;
                    calendar.navigateToFuture();
                }

                if (value || method) {
                    if (!method) {
                        view.setDate(currentValue, value);
                    }

                    that._current = currentValue = restrictValue(currentValue, options.min, options.max);
                    calendar._focus(currentValue);
                }
            }

            if (prevent) {
                e.preventDefault();
            }
        },

        value: function(value) {
            var that = this,
                calendar = that.calendar,
                options = that.options;

            that._value = value;
            that._current = new DATE(restrictValue(value, options.min, options.max));

            if (calendar.element.data(DATEVIEW) === that) {
                calendar._focus(that._current);
                calendar.value(value);
            }
        },

        _click: function(e) {
            if (e.currentTarget.className.indexOf(SELECTED) !== -1) {
                this.close();
            }
        },

        _navigate: function() {
            var that = this,
                calendar = that.calendar;

            that._current = new DATE(calendar._current);
            calendar._focus(calendar._current);
        },

        _navigateDown: function() {
            var that = this,
                calendar = that.calendar,
                currentValue = calendar._current,
                cell = calendar._table.find("." + FOCUSED),
                value = cell.children(":" + FIRST).attr(kendo.attr("value")).split("/");

            //Safari cannot create corretly date from "1/1/2090"
            value = new DATE(value[0], value[1], value[2]);

            if (!cell[0] || cell.hasClass(SELECTED)) {
                that.close();
                return;
            }

            calendar._view.setDate(currentValue, value);
            calendar.navigateDown(currentValue);
        },

        _option: function(option, value) {
            var that = this,
                options = that.options,
                calendar = that.calendar;

            options[option] = value;

            if (calendar.element.data(DATEVIEW) === that) {
                calendar[option](value);
            }
        },

        _templates: function() {
            var that = this,
                options = that.options,
                month = options.month || {},
                content = month.content,
                empty = month.empty;

            that.month = {
                content: template('<td#=data.cssClass#><a class="k-link" href="\\#" ' + kendo.attr("value") + '="#=data.dateString#" title="#=data.title#">' + (content || "#=data.value#") + '</a></td>', { useWithBlock: !!content }),
                empty: template("<td>" + (empty || "&nbsp;") + "</td>", { useWithBlock: !!empty })
            };

            that.footer = template(options.footer || '#= kendo.toString(data,"D") #', { useWithBlock: false });
        }
    };

    kendo.DateView = DateView;

    var DatePicker = Widget.extend(/** @lends kendo.ui.DatePicker.prototype */{
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Date} [value] <null> Specifies the selected date.
         * @option {Date} [min] <Date(1900, 0, 1)> Specifies the minimum date, which the calendar can show.
         * @option {Date} [max] <Date(2099, 11, 31)> Specifies the maximum date, which the calendar can show.
         * @option {String} [format] <MM/dd/yyyy> Specifies the format, which is used to parse value set with value() method.
         * @option {String} [start] <month> Specifies the start view.
         * @option {String} [depth] Specifies the navigation depth.
         */
        init: function(element, options) {
            var that = this,
                dateView, enable;

            Widget.fn.init.call(that, element, options);
            element = that.element;
            options = that.options;

            calendar.validate(options);

            that._wrapper();

            that.dateView = dateView = new DateView($.extend({}, options, {
                anchor: that.wrapper,
                change: function() {
                    // calendar is the current scope
                    that._change(this.value());
                    that.close();
                },
                clearBlurTimeout: proxy(that._clearBlurTimeout, that)
            }));

            that._icon();

            element
                .addClass("k-input")
                .bind({
                    keydown: proxy(that._keydown, that),
                    focus: function(e) {
                        clearTimeout(that._bluring);
                        that._inputWrapper.addClass(FOCUSED);
                    },
                    blur: proxy(that._blur, that)
                })
                .closest("form")
                .bind("reset", function() {
                    that.value(element[0].defaultValue);
                });


            /**
            * Fires when the selected date is changed
            * @name kendo.ui.DatePicker#change
            * @event
            * @param {Event} e
            */
            /**
            * Fires when the calendar is opened
            * @name kendo.ui.DatePicker#open
            * @event
            * @param {Event} e
            */
            /**
            * Fires when the calendar is closed
            * @name kendo.ui.DatePicker#close
            * @event
            * @param {Event} e
            */
            that.bind(CHANGE, options);

            that.enable(!element.is('[disabled]'));
            that.value(options.value || that.element.val());
        },

        options: {
            name: "DatePicker",
            value: null,
            min: new Date(1900, 0, 1),
            max: new Date(2099, 11, 31),
            start: MONTH,
            depth: MONTH
        },

        /**
        * Enable/Disable the datepicker widget.
        * @param {Boolean} enable The argument, which defines whether to enable/disable the datepicker.
        * @example
        * var datepicker = $("#datepicker").data("kendoDatePicker");
        *
        * // disables the datepicker
        * datepicker.enable(false);
        *
        * // enables the datepicker
        * datepicker.enable(true);
        */
        enable: function(enable) {
            var that = this,
                icon = that._icon,
                wrapper = that._inputWrapper,
                element = that.element;

            icon.unbind(CLICK)
                .unbind(MOUSEDOWN);

            if (enable === false) {
                wrapper
                    .removeClass(DEFAULT)
                    .addClass(STATEDISABLED)
                    .unbind(HOVEREVENTS);

                element.attr(DISABLED, DISABLED);
            } else {
                wrapper
                    .addClass(DEFAULT)
                    .removeClass(STATEDISABLED)
                    .bind(HOVEREVENTS, that._toggleHover);

                element
                    .removeAttr(DISABLED);

                icon.bind(CLICK, proxy(that._click, that))
                    .bind(MOUSEDOWN, proxy(that._clearBlurTimeout, that))
            }
        },

        /**
        * Opens the calendar.
        * @name kendo.ui.DatePicker#open
        * @function
        * @example
        * datepicker.open();
        */
        open: function() {
            this.dateView.open();
        },

        /**
        * Closes the calendar.
        * @name kendo.ui.DatePicker#close
        * @function
        * @example
        * datepicker.close();
        */
        close: function() {
            this.dateView.close();
        },

        /**
        * Gets/Sets the min value of the datepicker.
        * @param {Date|String} value The min date to set.
        * @returns {Date} The min value of the datepicker.
        * @example
        * var datepicker = $("#datepicker").data("kendoDatePicker");
        *
        * // get the min value of the datepicker.
        * var min = datepicker.min();
        *
        * // set the min value of the datepicker.
        * datepicker.min(new Date(1900, 0, 1));
        */
        min: function(value) {
            return this._option(MIN, value);
        },

        /**
        * Gets/Sets the max value of the datepicker.
        * @param {Date|String} value The max date to set.
        * @returns {Date} The max value of the datepicker.
        * @example
        * var datepicker = $("#datepicker").data("kendoDatePicker");
        *
        * // get the max value of the datepicker.
        * var max = datepicker.max();
        *
        * // set the max value of the datepicker.
        * datepicker.max(new Date(1900, 0, 1));
        */
        max: function(value) {
            return this._option(MAX, value);
        },

        /**
        * Gets/Sets the value of the datepicker.
        * @param {Date|String} value The value to set.
        * @returns {Date} The value of the datepicker.
        * @example
        * var datepicker = $("#datepicker").data("kendoDatePicker");
        *
        * // get the value of the datepicker.
        * var value = datepicker.value();
        *
        * // set the value of the datepicker.
        * datepicker.value("10/10/2000"); //parse "10/10/2000" date and selects it in the calendar.
        */
        value: function(value) {
            var that = this;

            if (value === undefined) {
                return that._value;
            }

            that._old = that._update(value);
        },

        _toggleHover: function(e) {
            if (!touch) {
                $(e.currentTarget).toggleClass(HOVER, e.type === "mouseenter");
            }
        },

        _blur: function() {
            var that = this;

            that._bluring = setTimeout(function() {
                that._change(that.element.val());
                if (!touch) {
                    that.close();
                }
                that._inputWrapper.removeClass(FOCUSED);
            }, 100);
        },

        _clearBlurTimeout: function() {
            var that = this;

            setTimeout(function() {
                clearTimeout(that._bluring);
                that.element.focus();
            });
        },

        _click: function() {
            this.dateView.toggle();
        },

        _change: function(value) {
            var that = this;

            value = that._update(value);

            if (+that._old != +value) {
                that._old = value;
                that.trigger(CHANGE);

                // trigger the DOM change event so any subscriber gets notified
                that.element.trigger(CHANGE);
            }
        },

        _keydown: function(e) {
            var that = this,
                dateView = that.dateView;

            if (!dateView.popup.visible() && e.keyCode == keys.ENTER) {
                that._change(that.element.val());
            } else {
                dateView.move(e);
            }
        },

        _icon: function() {
            var that = this,
                element = that.element,
                icon;

            icon = element.next("span.k-select");

            if (!icon[0]) {
                icon = $('<span class="k-select"><span class="k-icon k-icon-calendar">select</span></span>').insertAfter(element);
            }

            that._icon = icon;
        },

        _option: function(option, value) {
            var that = this,
                options = that.options;

            if (value === undefined) {
                return options[option];
            }

            value = parse(value, options.format);

            if (!value) {
                return;
            }

            options[option] = new DATE(value);
            that.dateView[option](value);
        },

        _update: function(value) {
            var that = this,
                options = that.options,
                format = options.format,
                date = parse(value, format);

            if (!isInRange(date, options.min, options.max)) {
                date = null;
            }

            that._value = date;
            that.dateView.value(date);
            that.element.val(date ? kendo.toString(date, format) : value);

            return date;
        },

        _wrapper: function() {
            var that = this,
                element = that.element,
                wrapper;

            wrapper = element.parents(".k-datepicker");

            if (!wrapper[0]) {
                wrapper = element.wrap(SPAN).parent().addClass("k-picker-wrap k-state-default");
                wrapper = wrapper.wrap(SPAN).parent();
            }

            wrapper[0].style.cssText = element[0].style.cssText;
            element.css({
                width: "100%",
                height: "auto"
            });

            that.wrapper = wrapper.addClass("k-widget k-datepicker k-header");
            that._inputWrapper = $(wrapper[0].firstChild);
        }
    });

    ui.plugin(DatePicker);

})(jQuery);
(function ($, undefined) {
    /**
    * @name kendo.ui.AutoComplete.Description
    *
    * @section The AutoComplete widget provides suggestions depending on the typed text. It also allows multiple value entries.
    * The suggestions shown by the AutoComplete widget can come from a local Array or from a remote data service.
    *
    * <h3>Getting Started</h3>
    * @exampleTitle Create a simple HTML input element
    * @example
    * <input id="autocomplete" />
    *
    * @exampleTitle Initialize Kendo AutoComplete using a jQuery selector
    * @example
    * var autocomplete = $("#autocomplete").kendoAutoComplete(["Item1", "Item2"]);
    *
    * @section <h3>AutoComplete Suggestions</h3>
    * There are two primary ways to provide the AutoComplete suggestions:
    *   <ol>
    *       <li>From a local, statically defined JavaScript Array</li>
    *       <li>From a remote data service</li>
    *   </ol>
    * Locally defined values are best for small, fixed sets of suggestions. Remote suggestions should be used for larger data sets. When used with the <a href="../datasource/index.html" title="Kendo DataSource">Kendo DataSource</a>,
    * filtering large remote data services can be pushed to the server, too, maximizing client-side performance.
    * <h3>Local Suggestions</h3>
    * To configure and provide AutoComplete suggestions locally, you can either pass an Array directly to the AutoComplete constructor,
    * or you can set the AutoComplete dataSource property to an Array defined elsewhere in your JavaScript code.
    * @exampleTitle Directly initialize suggestions in constructor
    * @example
    * $("#autocomplete").kendoAutoComplete(["Item1", "Item2", "Item3"]);
    *
    * @exampleTitle Using dataSource property to bind to local Array
    * @example
    * var data = ["Item1", "Item2", "Item3"];
    * $("#autocomplete").kendoAutoComplete({
    *    dataSource: data
    * });
    * @section <h3>Remote Suggestions</h3>
    * The easiest way to bind to remote AutoComplete suggestions is to use the <a href="../datasource/index.html" title="Kendo DataSource">Kendo DataSource</a> component. The Kendo DataSource is an abstraction for local and
    * remote data, and it can be used to serve data from a variety of data services, such as XML, JSON, and JSONP.
    *
    * @exampleTitle Using Kendo DataSource to bind to remote suggestions with OData
    * @example
    * $("#autocomplete").kendoAutoComplete({
    *    minLength: 3,
    *    dataTextField: "Name", //JSON property name to use
    *    dataSource: new kendo.data.DataSource({
    *        type: "odata", //Specifies data protocol
    *        pageSize: 10, //Limits result set
    *        transport: {
    *            read: "http://odata.netflix.com/Catalog/Titles"
    *        }
    *    })
    * });
    *
    * @exampleTitle Using Kendo DataSource to bind to JSONP suggestions
    * @example
    * $(document).ready(function(){
    *    $("#txtAc").kendoAutoComplete({
    *        minLength:6,
    *        dataTextField:"title",
    *        filter: "contains",
    *        dataSource: new kendo.data.DataSource({
    *            transport:{
    *                read:{
    *                    url: "http://api.geonames.org/wikipediaSearchJSON",
    *                    data:{
    *                        q:function(){
    *                            var ac = $("#txtAc").data("kendoAutoComplete");
    *                            return ac.value();
    *                        },
    *                        maxRows:10,
    *                        username:"demo"
    *                    }
    *                }
    *            },
    *            schema:{
    *                data:"geonames"
    *            }
    *        }),
    *        change:function(){
    *            this.dataSource.read();
    *        }
    *    });
    * });
    *
    * @section <h3>Accessing an Existing AutoComplete</h3>
    * To access an existing Kendo UI AutoComplete widget instance, use the jQuery data API. Once a reference to the AutoComplete is established,
    * you can use the Kendo UI API to control the widget.
    *
    * @exampleTitle Accessing Existing AutoComplete widget instance
    * @example
    * var autocomplete = $("#autocomplete").data("kendoAutoComplete");
    *
    *
    */
    var kendo = window.kendo,
        touch = kendo.support.touch,
        ui = kendo.ui,
        DataSource = kendo.data.DataSource,
        List = ui.List,
        CHANGE = "change",
        DEFAULT = "k-state-default",
        DISABLED = "disabled",
        FOCUSED = "k-state-focused",
        SELECTED = "k-state-selected",
        STATEDISABLED = "k-state-disabled",
        HOVER = "k-state-hover",
        HOVEREVENTS = "mouseenter mouseleave",
        caretPosition = List.caret,
        selectText = List.selectText,
        proxy = $.proxy;

    function indexOfWordAtCaret(caret, text, separator) {
        return text.substring(0, caret).split(separator).length - 1;
    }

    function wordAtCaret(caret, text, separator) {
        return text.split(separator)[indexOfWordAtCaret(caret, text, separator)];
    }

    function replaceWordAtCaret(caret, text, word, separator) {
        var words = text.split(separator);

        words.splice(indexOfWordAtCaret(caret, text, separator), 1, word);

        if (words[words.length - 1] !== "") {
            words.push("");
        }

        return words.join(separator);
    }

    function moveCaretAtEnd(element) {
        var length = element.value.length;

        selectText(element, length, length);
    }

    var AutoComplete = List.extend/** @lends kendo.ui.AutoComplete.prototype */({
        /**
        * @constructs
        * @extends kendo.ui.List
        * @param {DomElement} element DOM element
        * @param {Object} options Configuration options.
        * @option {kendo.data.DataSource | Object} [dataSource] Instance of DataSource or the data that the AutoComplete will be bound to.
        * @option {Boolean} [enable] <true> Controls whether the AutoComplete should be initially enabled.
        * @option {Boolean} [suggest] <false> Controls whether the AutoComplete should automatically auto-type the rest of text.
        * @option {Number} [delay] <200> Specifies the delay in ms after which the AutoComplete will start filtering dataSource.
        * @option {Number} [minLength] <1> Specifies the minimum characters that should be typed before the AutoComplete activates
        * @option {String} [dataTextField] <null> Sets the field of the data item that provides the text content of the list items.
        * @option {String} [filter] <"startswith"> Defines the type of filtration.
        * @option {Number} [height] <200> Define the height of the drop-down list in pixels.
        */
        init: function (element, options) {
            var that = this;

            options = $.isArray(options) ? { dataSource: options} : options;

            List.fn.init.call(that, element, options);

            element = that.element;

            that._wrapper();

            that._accessors();

            that.dataSource = DataSource.create(that.options.dataSource).bind(CHANGE, proxy(that.refresh, that));

            that.bind([
            /**
            * Fires when the drop-down list is opened
            * @name kendo.ui.AutoComplete#open
            * @event
            * @param {Event} e
            */
            /**
            * Fires when the drop-down list is closed
            * @name kendo.ui.AutoComplete#close
            * @event
            * @param {Event} e
            */
            /**
            * Fires when the value has been changed.
            * @name kendo.ui.AutoComplete#change
            * @event
            * @param {Event} e
            */
                CHANGE
            ], that.options);

            element[0].type = "text";

            element
                .attr("autocomplete", "off")
                .addClass("k-input")
                .bind({
                    keydown: proxy(that._keydown, that),
                    paste: proxy(that._search, that),
                    focus: function () {
                        that._old = that.value();
                        that.wrapper.addClass(FOCUSED);
                    },
                    blur: function () {
                        that._bluring = setTimeout(function () {
                            that._blur();
                            that.wrapper.removeClass(FOCUSED);
                        }, 100);
                    }
                });

            that.enable(!element.is('[disabled]'));

            that._popup();
        },

        options: {
            name: "AutoComplete",
            suggest: false,
            minLength: 1,
            delay: 200,
            height: 200,
            filter: "startswith"
        },

        /**
        * Enable/Disable the autocomplete widget.
        * @param {Boolean} enable The argument, which defines whether to enable/disable the autocomplete.
        * @example
        * var autocomplete = $("autocomplete").data("kendoAutoComplete");
        *
        * // disables the autocomplete
        * autocomplete.enable(false);
        *
        * // enables the autocomplete
        * autocomplete.enable(true);
        */
        enable: function(enable) {
            var that = this,
                element = that.element,
                wrapper = that.wrapper;

            if (enable === false) {
                wrapper
                    .removeClass(DEFAULT)
                    .addClass(STATEDISABLED)
                    .unbind(HOVEREVENTS);

                element.attr(DISABLED, DISABLED);
            } else {
                wrapper
                    .removeClass(STATEDISABLED)
                    .addClass(DEFAULT)
                    .bind(HOVEREVENTS, that._toggleHover);

                element
                    .removeAttr(DISABLED);
            }
        },

        /**
        * Closes the drop-down list.
        * @example
        * autocomplete.close();
        */
        close: function () {
            var that = this;
            that._current = null;
            that.popup.close();
        },

        refresh: function () {
            var that = this,
            ul = that.ul[0],
            data = that.dataSource.view(),
            length = data.length;

            ul.innerHTML = kendo.render(that.template, data);

            that._height(length);

            if (length && that.options.highlightFirst) {
                that.current($(ul.firstChild));
            }

            if (that._open) {
                that._open = false;
                that.popup[length ? "open" : "close"]();
            }
        },

        /**
        * Selects drop-down list item and sets the text of the autocomplete.
        * @param {jQueryObject} li The LI element.
        * @example
        * var autocomplete = $("#autocomplete").data("kendoAutoComplete");
        *
        * // selects by jQuery object
        * autocomplete.select(autocomplete.ul.children().eq(0));
        */
        select: function (li) {
            var that = this,
                separator = that.options.separator,
                data = that.dataSource.view(),
                text,
                idx;

            li = $(li);

            if (li[0] && !li.hasClass(SELECTED)) {
                idx = List.inArray(li[0], that.ul[0]);

                if (idx > -1) {
                    data = data[idx];
                    text = that._text(data);

                    if (separator) {
                        text = replaceWordAtCaret(caretPosition(that.element[0]), that.value(), text, separator);
                    }

                    that.value(text);
                    that.current(li.addClass(SELECTED));
                }
            }
        },

        /**
        * Filters dataSource using the provided parameter and rebinds drop-down list.
        * @param {string} word The filter value.
        * @example
        * var autocomplete = $("#autocomplete").data("kendoAutoComplete");
        *
        * // Searches for item which has "Inception" in the name.
        * autocomplete.search("Inception");
        */
        search: function (word) {
            var that = this,
            word = word || that.value(),
            options = that.options,
            separator = options.separator,
            length,
            caret,
            index;

            that._current = null;

            clearTimeout(that._typing);

            if (separator) {
                word = wordAtCaret(caretPosition(that.element[0]), word, separator);
            }

            length = word.length;

            if (!length) {
                that.popup.close();
            } else if (length >= that.options.minLength) {
                that._open = true;
                that.dataSource.filter({ field: options.dataTextField, operator: options.filter, value: word });
            }
        },

        suggest: function (word) {
            var that = this,
                element = that.element[0],
                separator = that.options.separator,
                value = that.value(),
                selectionEnd,
                textRange,
                caret = caretPosition(element);


            if (typeof word !== "string") {
                word = word ? word.text() : "";
            }

            if (caret <= 0) {
                caret = value.toLowerCase().indexOf(word.toLowerCase()) + 1;
            }

            if (!word) {
                word = value.substring(0, caret);

                if (separator) {
                    word = word.split(separator).pop();
                }
            }

            if (separator) {
                word = replaceWordAtCaret(caret, value, word, separator);
            }

            if (word !== value) {
                that.value(word);

                selectionEnd = word.length;

                if (separator) {
                    selectionEnd = caret + word.substring(caret).indexOf(separator);
                }

                selectText(element, caret, selectionEnd);
            }
        },

        /**
        * Gets/Sets the value of the autocomplete.
        * @param {String} value The value to set.
        * @returns {String} The value of the autocomplete.
        * @example
        * var autocomplete = $("#autocomplete").data("kendoAutoComplete");
        *
        * // get the text of the autocomplete.
        * var value = autocomplete.value();
        */
        value: function (value) {
            var that = this,
                element = that.element[0];

            if (value !== undefined) {
                element.value = value;
            } else {
                return element.value;
            }
        },

        _accept: function (li) {
            var that = this;

            if (kendo.support.touch) {
                setTimeout(function () { that._focus(li) }, 0);
            } else {
                that._focus(li);
            }

            moveCaretAtEnd(that.element[0]);
        },

        _move: function (li) {
            var that = this;

            li = li[0] ? li : null;

            that.current(li);

            if (that.options.suggest) {
                that.suggest(li);
            }
        },

        _keydown: function (e) {
            var that = this,
                ul = that.ul[0],
                key = e.keyCode,
                keys = kendo.keys,
                current = that._current,
                visible = that.popup.visible();

            if (key === keys.DOWN) {
                if (visible) {
                    that._move(current ? current.next() : $(ul.firstChild));
                }
                e.preventDefault();
            } else if (key === keys.UP) {
                if (visible) {
                    that._move(current ? current.prev() : $(ul.lastChild));
                }
                e.preventDefault();
            } else if (key === keys.ENTER || key === keys.TAB) {

                if (that.popup.visible()) {
                    e.preventDefault();
                }

                that._accept(current);
            } else if (key === keys.ESC) {
                that.close();
            } else {
                that._search();
            }
        },

        _search: function () {
            var that = this;
            clearTimeout(that._typing);

            that._typing = setTimeout(function () {
                if (that._old !== that.value()) {
                    that._old = that.value();
                    that.search();
                }
            }, that.options.delay);
        },

        _toggleHover: function(e) {
            if (!touch) {
                $(e.currentTarget).toggleClass(HOVER, e.type === "mouseenter");
            }
        },

        _wrapper: function () {
            var that = this,
                element = that.element,
                DOMelement = element[0],
                TABINDEX = "tabIndex",
                wrapper;

            wrapper = element.parent();

            if (!wrapper.is("span.k-widget")) {
                wrapper = element.wrap("<span />").parent();
            }

            wrapper[0].style.cssText = DOMelement.style.cssText;
            element.css({
                width: "100%",
                height: "auto"
            });

            that._focused = that.element;
            that.wrapper = wrapper
                              .addClass("k-widget k-autocomplete k-header")
                              .addClass(DOMelement.className);
        }
    });

    ui.plugin(AutoComplete);
})(jQuery);
(function($, undefined) {
    /**
    * @name kendo.ui.DropDownList.Description
    *
    * @section
    *   <p>
    *       The DropDownList widget displays a list of values and allows the selection of a single value from the list.
    *       It is a richer version of the standard HTML select, providing support for local and remote data binding, item templates,
    *       and configurable options for controlling the list behavior.
    *   </p>
    *   If you want to allow user input, use the <a href="../combobox/index.html" title="Kendo UI ComboBox">Kendo UI ComboBox</a>.
    *
    *   <h3>Getting Started</h3>
    *   There are two basic ways to create a DropDownList:
    *   <ol>
    *       <li>From a basic HTML input element, using data binding to define the list items</li>
    *       <li>From a HTML select element, using HTML to define the list items</li>
    *   </ol>
    *   Regardless of the initialization technique, the resulting Kendo UI DropDownList will look and function identically.
    *
    * @exampleTitle Creating a dropdownlist from existing input HTML element
    * @example
    * <!-- HTML -->
    * <input id="dropdownlist" />
    *
    * @exampleTitle DropDownList initialization
    * @example
    *   $(document).ready(function(){
    *      $("#dropdownlist").kendoDropDownList([{text: "Item1", value: "1"}, {text: "Item2", value: "2"}]);
    *   });
    *
    * @exampleTitle Creating a dropdownlist from existing select HTML element
    * @example
    * <!-- HTML -->
    * <select id="dropdownlist">
    *     <option>Item 1</option>
    *     <option>Item 2</option>
    *     <option>Item 3</option>
    * </select>
    *
    * @exampleTitle DropDownList initialization
    * @example
    *   $(document).ready(function(){
    *       $("#dropdownlist").kendoDropDownList();
    *   });
    *
    * @section
    *   <h3>Binding to Data</h3>
    *   <p>
    *       The DropDownList can be bound to both local JavaScript Arrays and remote data via the
    *       Kendo DataSource component. Local JavaScript Arrays are appropriate for limited value
    *       options, while remote data binding is better for larger data sets. With remote binding,
    *       options will be loaded on-demand, similar to AutoComplete.
    *   </p>
    * @exampleTitle Binding to a remote OData service
    * @example
    *   $(document).ready(function() {
    *       $("#titles").kendoDropDownList({
    *           index: 0,
    *           dataTextField: "Name",
    *           dataValueField: "Id",
    *           filter: "contains",
    *           dataSource: {
    *               type: "odata",
    *               severFiltering: true,
    *               serverPaging: true,
    *               pageSize: 20,
    *               transport: {
    *                   read: "http://odata.netflix.com/Catalog/Titles"
    *               }
    *           }
    *       });
    *   });
    *
    * @section
    *   <h3>Customizing Item Templates</h3>
    *   <p>
    *       DropDownList leverages Kendo UI high-performance Templates to give you complete control
    *       over item rendering. For a complete overview of Kendo UI Template capabilities and syntax,
    *       please review the <a href="../templates/index.html" title="Kendo UI Template">Kendo UI Template</a> demos and documentation.
    *   </p>
    * @exampleTitle Basic item template customization
    * @example
    *   <!-- HTML -->
    *   <input id="titles"/>
    *
    *   <!-- Template -->
    *   <script id="scriptTemplate" type="text/x-kendo-template">
    *       # if (data.BoxArt.SmallUrl) { #
    *           <img src="${ data.BoxArt.SmallUrl }" alt="${ data.Name }" />Title:${ data.Name }, Year: ${ data.Name }
    *       # } else { #
    *           <img alt="${ data.Name }" />Title:${ data.Name }, Year: ${ data.Name }
    *       # } #
    *   </script>
    *
    *   <!-- DropDownList initialization -->
    *   <script type="text/javascript">
    *       $(document).ready(function() {
    *           $("#titles").kendoDropDownList({
    *               autoBind: false,
    *               dataTextField: "Name",
    *               dataValueField: "Id",
    *               template: $("#scriptTemplate").html(),
    *               dataSource: {
    *                   type: "odata",
    *                   severFiltering: true,
    *                   serverPaging: true,
    *                   pageSize: 20,
    *                   transport: {
    *                       read: "http://odata.netflix.com/Catalog/Titles"
    *                   }
    *               }
    *           });
    *       });
    *   </script>
    */
    var kendo = window.kendo,
        ui = kendo.ui,
        Select = ui.Select,
        ATTRIBUTE = "disabled",
        CHANGE = "change",
        SELECT = "select",
        FOCUSED = "k-state-focused",
        DEFAULT = "k-state-default",
        DISABLED = "k-state-disabled",
        SELECTED = "k-state-selected",
        HOVER = "k-state-hover",
        HOVEREVENTS = "mouseenter mouseleave",
        INPUTWRAPPER = ".k-dropdown-wrap",
        proxy = $.proxy;

    var DropDownList = Select.extend( /** @lends kendo.ui.DropDownList.prototype */ {
        /**
         * @constructs
         * @extends kendo.ui.Select
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {kendo.data.DataSource|Object} [dataSource] Instance of DataSource or the data that the DropDownList will be bound to.
         * @option {Boolean} [enable] <true> Controls whether the DropDownList should be initially enabled.
         * @option {Number} [index] <0> Defines the initial selected item.
         * @option {Boolean} [autoBind] <true> Controls whether to bind the widget on initialization.
         * @option {Number} [delay] <500> Specifies the delay in ms before the search text typed by the end user is cleared.
         * @option {String} [dataTextField] <"text"> Sets the field of the data item that provides the text content of the list items.
         * @option {String} [dataValueField] <"value"> Sets the field of the data item that provides the value content of the list items.
         * @option {Number} [height] <200> Define the height of the drop-down list in pixels.
         * @option {String} [optionLabel] Define the text of the default empty item.
         */
        init: function(element, options) {
            var that = this,

            options = $.isArray(options) ? { dataSource: options } : options;

            Select.fn.init.call(that, element, options);

            options = that.options;
            element = that.element.focus(function() {
                that.wrapper.focus();
            });

            that._reset();

            that._word = "";

            that._wrapper();

            that._span();

            that._popup();

            that._accessors();

            that._dataSource();

            that._enable();

            that.bind([
                /**
                * Fires when the drop-down list is opened
                * @name kendo.ui.DropDownList#open
                * @event
                * @param {Event} e
                */
                /**
                * Fires when the drop-down list is closed
                * @name kendo.ui.DropDownList#close
                * @event
                * @param {Event} e
                */
                /**
                * Fires when the value has been changed.
                * @name kendo.ui.DropDownList#change
                * @event
                * @param {Event} e
                */
                CHANGE
            ], options);

            if (options.autoBind) {
                that.dataSource.fetch();
            } else if (element.is(SELECT)) {
                that.text(element.children(":selected").text());
            }
        },

        options: {
            name: "DropDownList",
            enable: true,
            index: 0,
            autoBind: true,
            delay: 500,
            dataTextField: "text",
            dataValueField: "value",
            height: 200
        },

        /**
        * Closes the drop-down list.
        * @name kendo.ui.DropDownList#close
        * @function
        * @example
        * dropdownlist.close();
        */

        /**
        * Enables/disables the dropdownlist widget
        * @param {Boolean} enable Desired state
        */
        enable: function(enable) {
            var that = this,
                element = that.element,
                wrapper = that.wrapper,
                dropDownWrapper = that._inputWrapper;

            if (enable === false) {
                element.attr(ATTRIBUTE, ATTRIBUTE);

                wrapper.unbind();

                dropDownWrapper
                    .removeClass(DEFAULT)
                    .addClass(DISABLED)
                    .unbind(HOVEREVENTS)

            } else {
                element.removeAttr(ATTRIBUTE, ATTRIBUTE);

                dropDownWrapper
                    .addClass(DEFAULT)
                    .removeClass(DISABLED)
                    .bind(HOVEREVENTS, that._toggleHover);

                wrapper
                    .bind({
                        keydown: proxy(that._keydown, that),
                        keypress: proxy(that._keypress, that),
                        focusin: function() {
                            that._inputWrapper.addClass(FOCUSED);
                            clearTimeout(that._bluring);
                        },
                        click: function() {
                            that.toggle();
                        },
                        focusout: function(e) {
                            that._bluring = setTimeout(function() {
                                that._blur();
                                that._inputWrapper.removeClass(FOCUSED);
                            }, 100);
                        }
                    });
            }
        },

        /**
        * Opens the drop-down list.
        * @example
        * dropdownlist.open();
        */
        open: function() {
            var that = this,
                current = that._current;

            if (!that.ul[0].firstChild) {
                that._open = true;
                that.dataSource.fetch();
            } else {
                that.popup.open();
                if (current) {
                    that._scroll(current[0]);
                }
            }
        },

        /**
        * Toggles the drop-down list between opened and closed state.
        * @param {Boolean} toggle Defines the whether to open/close the drop-down list.
        * @example
        * var dropdownlist = $("#dropdownlist").data("kendoDropDownList");
        *
        * // toggles the open state of the drop-down list.
        * dropdownlist.toggle();
        */
        toggle: function(toggle) {
            this._toggle(toggle);
        },

        refresh: function() {
            var that = this,
                value = that.value(),
                options = that.options,
                data = that._data(),
                length = data.length;

            that.ul[0].innerHTML = kendo.render(that.template, data);
            that._height(length);

            if (that.element.is(SELECT)) {
                that._options(data);
            }

            if (value) {
                that.value(value);
            } else {
                that.select(options.index);
            }

            that._old = that.value();

            if (that._open) {
                that.toggle(length);
            }

            that._hideBusy();
        },

        /**
        * Selects item, which starts with the provided parameter.
        * @param {string} word The search value.
        * @example
        * var dropdownlist = $("#dropdownlist").data("kendoDropDownList");
        *
        * // Selects item which starts with "In".
        * autocomplete.search("In");
        */
        search: function(word) {
            if(word){
                var that = this;
                word = word.toLowerCase();

                that.select(function(dataItem) {
                    var text = that._text(dataItem);
                    if (text !== undefined) {
                        return (text + "").toLowerCase().indexOf(word) === 0;
                    }
                });
            }
        },

        /**
        * Selects drop-down list item and sets the value and the text of the dropdownlist.
        * @param {jQueryObject | Number | Function} li LI element or index of the item or predicate function, which defines the item that should be selected.
        * @example
        * var dropdownlist = $("#dropdownlist").data("kendoDropDownList");
        *
        * // selects by jQuery object
        * dropdownlist.select(dropdownlist.ul.children().eq(0));
        *
        * // selects by index
        * dropdownlist.select(1);
        *
        * // selects item if its text is equal to "test" using predicate function
        * dropdownlist.select(function(dataItem) {
        *     return dataItem.text === "test";
        * });
        */
        select: function(li) {
            var that = this,
                element = that.element[0],
                current = that._current,
                data = that._data(),
                value,
                text,
                idx;

            li = that._get(li);

            if (li && li[0] && !li.hasClass(SELECTED)) {
                if (current) {
                    current.removeClass(SELECTED);
                }

                idx = ui.List.inArray(li[0], that.ul[0]);
                if (idx > -1) {
                    data = data[idx];
                    text = that._text(data);
                    value = that._value(data);

                    that.text(text);
                    that._accessor(value != undefined ? value : text, idx);
                    that.current(li.addClass(SELECTED));
                }
            }
        },

        /**
        * Gets/Sets the text of the dropdownlist.
        * @param {String} text The text to set.
        * @returns {String} The text of the dropdownlist.
        * @example
        * var dropdownlist = $("#dropdownlist").data("kendoDropDownList");
        *
        * // get the text of the dropdownlist.
        * var text = dropdownlist.text();
        */
        text: function (text) {
            var span = this.span;

            if (text !== undefined) {
                span.text(text);
            } else {
                return span.text();
            }
        },

        /**
        * Gets/Sets the value of the dropdownlist. The value will not be set if there is no item with such value. If value is undefined, text of the data item is used.
        * @param {String} value The value to set.
        * @returns {String} The value of the dropdownlist.
        * @example
        * var dropdownlist = $("#dropdownlist").data("kendoDropDownList");
        *
        * // get the value of the dropdownlist.
        * var value = dropdownlist.value();
        *
        * // set the value of the dropdownlist.
        * dropdownlist.value("1"); //looks for item which has value "1"
        */
        value: function(value) {
            var that = this,
                idx,
                element = that.element;

            if (value !== undefined) {
                idx = that._index(value);

                that.select(idx > -1 ? idx : 0);
                that._old = that._accessor();
            } else {
                return that._accessor();
            }
        },

        _accept: function(li) {
            this._focus(li);
        },

        _data: function() {
            var that = this,
                options = that.options,
                optionLabel = options.optionLabel,
                textField = options.dataTextField,
                valueField = options.dataValueField,
                data = that.dataSource.view(),
                length = data.length,
                first = optionLabel,
                idx = 0;

            if (optionLabel && length) {
                if (textField) {
                    first = {};
                    first[textField] = optionLabel;

                    if (valueField) {
                        first[valueField] = "";
                    }
                }

                first = [first];

                for (; idx < length; idx++) {
                    first.push(data[idx]);
                }
                data = first;
            }

            return data;
        },

        _keydown: function(e) {
            var that = this,
                key = e.keyCode,
                keys = kendo.keys,
                ul = that.ul[0];

            that._move(e);

            if (key === keys.HOME) {
                e.preventDefault();
                that.select(ul.firstChild);
            } else if (key === keys.END) {
                e.preventDefault();
                that.select(ul.lastChild);
            }
        },

        _keypress: function(e) {
            var that = this;

            setTimeout(function() {
                that._word += String.fromCharCode(e.keyCode || e.charCode);
                that._search();
            }, 0);
        },

        _search: function() {
            var that = this;
            clearTimeout(that._typing);

            that._typing = setTimeout(function() {
                that._word = "";
            }, that.options.delay);

            that.search(that._word);
        },

        _span: function() {
            var that = this,
                wrapper = that.wrapper,
                SELECTOR = ".k-input",
                span;

            span = wrapper.find(SELECTOR);

            if (!span[0]) {
                wrapper.append('<span class="k-dropdown-wrap k-state-default"><span class="k-input">&nbsp;</span><span class="k-select"><span class="k-icon k-arrow-down">select</span></span></span>')
                       .append(that.element);

                span = wrapper.find(SELECTOR);
            }

            that.span = span;
            that._arrow = wrapper.find(".k-icon");
            that._inputWrapper = $(wrapper[0].firstChild)
        },

        _wrapper: function() {
            var that = this,
                element = that.element,
                DOMelement = element[0],
                TABINDEX = "tabIndex",
                wrapper;

            wrapper = element.parent();

            if (!wrapper.is("span.k-widget")) {
                wrapper = element.wrap("<span />").parent();
            }

            if (!wrapper.attr(TABINDEX)) {
                wrapper.attr(TABINDEX, 0);
            }

            wrapper[0].style.cssText = DOMelement.style.cssText;
            element.hide();

            that._focused = that.wrapper = wrapper
                              .addClass("k-widget k-dropdown k-header")
                              .addClass(DOMelement.className);
        }
    });

    ui.plugin(DropDownList);
})(jQuery);
(function($, undefined) {
    /**
    * @name kendo.ui.ComboBox.Description
    *
    * @section
    *   <p>
    *       The ComboBox widget allows the selection from pre-defined values or entering a new value.
    *       It is a richer version of the standard HTML select, providing support for local and remote data binding, item templates,
    *       and configurable options for controlling the list behavior.
    *   </p>
    *   If you do not want to allow user input, use the <a href="../dropdownlist/index.html" title="Kendo UI DropDownList">Kendo UI DropDownList</a>.
    *
    *   <h3>Getting Started</h3>
    *   There are two basic ways to create a ComboBox:
    *   <ol>
    *       <li>From a basic HTML input element, using data binding to define the list items</li>
    *       <li>From a HTML select element, using HTML to define the list items</li>
    *   </ol>
    *   Regardless of the initialization technique, the resulting Kendo UI ComboBox will look and function identically.
    *
    * @exampleTitle Creating a combobox from existing input HTML element
    * @example
    * <!-- HTML -->
    * <input id="combobox" />
    *
    * @exampleTitle ComboBox initialization
    * @example
    *   $(document).ready(function(){
    *      $("#combobox").kendoComboBox([{text: "Item1", value: "1"}, {text: "Item2", value: "2"}]);
    *   });
    *
    * @exampleTitle Creating a combobox from existing select HTML element
    * @example
    * <!-- HTML -->
    * <select id="combobox">
    *     <option>Item 1</option>
    *     <option>Item 2</option>
    *     <option>Item 3</option>
    * </select>
    *
    * @exampleTitle ComboBox initialization
    * @example
    *   $(document).ready(function(){
    *       $("#combobox").kendoComboBox();
    *   });
    *
    * @section
    *   <h3>Binding to Data</h3>
    *   <p>
    *       The ComboBox can be bound to both local JavaScript Arrays and remote data via the
    *       Kendo DataSource component. Local JavaScript Arrays are appropriate for limited value
    *       options, while remote data binding is better for larger data sets. With remote binding,
    *       options will be loaded on-demand, similar to AutoComplete.
    *   </p>
    * @exampleTitle Binding to a remote OData service
    * @example
    *   $(document).ready(function() {
    *       $("#titles").kendoComboBox({
    *           index: 0,
    *           dataTextField: "Name",
    *           dataValueField: "Id",
    *           filter: "contains",
    *           dataSource: {
    *               type: "odata",
    *               severFiltering: true,
    *               serverPaging: true,
    *               pageSize: 20,
    *               transport: {
    *                   read: "http://odata.netflix.com/Catalog/Titles"
    *               }
    *           }
    *       });
    *   });
    *
    * @section
    *   <h3>Customizing Item Templates</h3>
    *   <p>
    *       ComboBox leverages Kendo UI high-performance Templates to give you complete control
    *       over item rendering. For a complete overview of Kendo UI Template capabilities and syntax,
    *       please review the <a href="../templates/index.html" title="Kendo UI Template">Kendo UI Template</a> demos and documentation.
    *   </p>
    * @exampleTitle Basic item template customization
    * @example
    *   <!-- HTML -->
    *   <input id="titles"/>
    *
    *   <!-- Template -->
    *   <script id="scriptTemplate" type="text/x-kendo-template">
    *       # if (data.BoxArt.SmallUrl) { #
    *           <img src="${ data.BoxArt.SmallUrl }" alt="${ data.Name }" />Title:${ data.Name }, Year: ${ data.Name }
    *       # } else { #
    *           <img alt="${ data.Name }" />Title:${ data.Name }, Year: ${ data.Name }
    *       # } #
    *   </script>
    *
    *   <!-- ComboBox initialization -->
    *   <script type="text/javascript">
    *       $(document).ready(function() {
    *           $("#titles").kendoComboBox({
    *               autoBind: false,
    *               dataTextField: "Name",
    *               dataValueField: "Id",
    *               template: $("#scriptTemplate").html(),
    *               dataSource: {
    *                   type: "odata",
    *                   severFiltering: true,
    *                   serverPaging: true,
    *                   pageSize: 20,
    *                   transport: {
    *                       read: "http://odata.netflix.com/Catalog/Titles"
    *                   }
    *               }
    *           });
    *       });
    *   </script>
    */
    var kendo = window.kendo,
        ui = kendo.ui,
        List = ui.List,
        Select = ui.Select,
        CLICK = "click",
        ATTRIBUTE = "disabled",
        CHANGE = "change",
        DEFAULT = "k-state-default",
        DISABLED = "k-state-disabled",
        FOCUSED = "k-state-focused",
        SELECT = "select",
        STATE_SELECTED = "k-state-selected",
        STATE_FILTER = "filter",
        STATE_ACCEPT = "accept",
        HOVER = "k-state-hover",
        HOVEREVENTS = "mouseenter mouseleave",
        NULL = null,
        proxy = $.proxy;

    var ComboBox = Select.extend(/** @lends kendo.ui.ComboBox.prototype */{
        /**
        * @constructs
        * @extends kendo.ui.Select
        * @param {DomElement} element DOM element
        * @param {Object} options Configuration options.
        * @option {kendo.data.DataSource|Object} [dataSource] Instance of DataSource or the data that the ComboBox will be bound to.
        * @option {Boolean} [enable] <true> Controls whether the ComboBox should be initially enabled.
        * @option {Number} [index] <-1> Defines the initial selected item.
        * @option {Boolean} [autoBind] <true> Controls whether to bind the widget on initialization.
        * @option {Boolean} [highlightFirst] <true> Controls whether the first item will be automatically highlighted.
        * @option {Boolean} [suggest] <false> Controls whether the ComboBox should automatically auto-type the rest of text.
        * @option {Number} [delay] <200> Specifies the delay in ms after which the ComboBox will start filtering dataSource.
        * @option {Number} [minLength] <1> Specifies the minimum characters that should be typed before the ComboBox activates
        * @option {String} [dataTextField] <"text"> Sets the field of the data item that provides the text content of the list items.
        * @option {String} [dataValueField] <"value"> Sets the field of the data item that provides the value content of the list items.
        * @option {String} [filter] <"none"> Defines the type of filtration. If "none" the ComboBox will not filter the items.
        * @option {Number} [height] <200> Define the height of the drop-down list in pixels.
        */
        init: function(element, options) {
            var that = this;

            options = $.isArray(options) ? { dataSource: options } : options;

            Select.fn.init.call(that, element, options);

            options = that.options;
            element = that.element.focus(function() {
                        that.input.focus();
                      });

            that._reset();

            that._wrapper();

            that._input();

            that._popup();

            that._accessors();

            that._dataSource();

            that._enable();

            that.bind([
                /**
                * Fires when the drop-down list is opened
                * @name kendo.ui.ComboBox#open
                * @event
                * @param {Event} e
                */
                /**
                * Fires when the drop-down list is closed
                * @name kendo.ui.ComboBox#close
                * @event
                * @param {Event} e
                */
                /**
                * Fires when the value has been changed.
                * @name kendo.ui.ComboBox#change
                * @event
                * @param {Event} e
                */
                CHANGE
            ], options);

            that.input.bind({
                keydown: proxy(that._keydown, that),
                focus: function() {
                    that._inputWrapper.addClass(FOCUSED);
                },
                blur: function() {
                    that._bluring = setTimeout(function() {
                        clearTimeout(that._typing);

                        that.text(that.text());
                        that._blur();

                        that._inputWrapper.removeClass(FOCUSED);
                    }, 100);
                }
            });

            that._old = that.value();

            if (options.autoBind) {
                that._select();
            } else if (element.is(SELECT)) {
                that.input.val(element.children(":selected").text());
            }
        },

        options: {
            name: "ComboBox",
            enable: true,
            index: -1,
            autoBind: true,
            delay: 200,
            dataTextField: "text",
            dataValueField: "value",
            minLength: 0,
            height: 200,
            highlightFirst: true,
            filter: "none",
            suggest: false
        },

        current: function(li) {
            var that = this,
                current = that._current;

            if (li === undefined) {
                return current;
            }

            that._selected = NULL;

            if (current) {
                current.removeClass(STATE_SELECTED);
            }

            Select.fn.current.call(that, li);
        },

        /**
        * Closes the drop-down list.
        * @name kendo.ui.ComboBox#close
        * @function
        * @example
        * combobox.close();
        */

        /**
        * Enables/disables the combobox widget
        * @param {Boolean} enable Desired state
        */
        enable: function(enable) {
            var that = this,
                input = that.input,
                element = that.element,
                wrapper = that._inputWrapper,
                arrow = that._arrow.parent();

            if (enable === false) {
                wrapper
                    .removeClass(DEFAULT)
                    .addClass(DISABLED)
                    .unbind(HOVEREVENTS);

                input.add(element).attr(ATTRIBUTE, ATTRIBUTE);
                arrow.unbind(CLICK);
            } else {
                wrapper
                    .removeClass(DISABLED)
                    .addClass(DEFAULT)
                    .bind(HOVEREVENTS, that._toggleHover);

                input.add(element).removeAttr(ATTRIBUTE);
                arrow.bind(CLICK, function() { that.toggle() });
            }
        },

        /**
        * Opens the drop-down list.
        * @example
        * combobox.open();
        */
        open: function() {
            var that = this,
                selected = that._selected;

            if (that.popup.visible()) {
                return;
            }

            if (!that.ul[0].firstChild || that._state === STATE_ACCEPT) {
                that._open = true;
                that._state = "";
                that._select();
            } else {
                that.popup.open();
                if (selected) {
                    that._scroll(selected[0]);
                }
            }
        },

        refresh: function() {
            var that = this,
                ul = that.ul,
                options = that.options,
                suggest = options.suggest,
                height = options.height,
                data = that._data(),
                length = data.length;

            ul[0].innerHTML = kendo.render(that.template, data);
            that._height(length);

            if (that.element.is(SELECT)) {
                that._options(data);
            }

            if (length) {
                if (suggest || options.highlightFirst) {
                    that.current($(that.ul[0].firstChild));
                }

                if (suggest) {
                    that.suggest(that._current);
                }
            }

            if (that._open) {
                that._open = false;
                that.toggle(!!length);
            }

            that._hideBusy();
        },

        /**
        * Selects drop-down list item and sets the value and the text of the combobox.
        * @param {jQueryObject | Number | Function} li LI element or index of the item or predicate function, which defines the item that should be selected.
        * @example
        * var combobox = $("#combobox").data("kendoComboBox");
        *
        * // selects by jQuery object
        * combobox.select(combobox.ul.children().eq(0));
        *
        * // selects by index
        * combobox.select(1);
        *
        * // selects item if its text is equal to "test" using predicate function
        * combobox.select(function(dataItem) {
        *     return dataItem.text === "test";
        * });
        */
        select: function(li) {
            var that = this,
                text,
                value,
                idx = that._highlight(li),
                data = that._data();

            if (idx !== -1) {
                that._selected = that._current.addClass(STATE_SELECTED);

                data = data[idx];
                text = that._text(data);
                value = that._value(data);

                that._prev = that.input[0].value = text;
                that._accessor(value != undefined ? value : text, idx);
            }
        },

        /**
        * Filters dataSource using the provided parameter and rebinds drop-down list.
        * @param {string} word The filter value.
        * @example
        * var combobox = $("#combobox").data("kendoComboBox");
        *
        * // Searches for item which has "In" in the name.
        * combobox.search("In");
        */
        search: function(word) {
            var that = this,
                word = word || that.text(),
                length = word.length,
                options = that.options,
                filter = options.filter;

            clearTimeout(that._typing);

            if (length >= options.minLength) {
                if (filter === "none") {
                    that._filter(word);
                } else {
                    that._open = true;
                    that._state = STATE_FILTER,
                    that.dataSource.filter( {
                        field: options.dataTextField,
                        operator: filter,
                        value: word
                    });
                }
            }
        },

        suggest: function(word) {
            var that = this,
                element = that.input[0],
                value = that.text(),
                caret = List.caret(element);


            if (typeof word !== "string") {
                word = word ? word.text() : "";
            }

            if (caret <= 0) {
                caret = value.toLowerCase().indexOf(word.toLowerCase()) + 1;
            }

            if (!word) {
                word = value.substring(0, caret);
            }

            if (word !== value) {
                that.text(word);
                List.selectText(element, caret, word.length);
            }
        },

        /**
        * Gets/Sets the text of the ComboBox.
        * @param {String} text The text to set.
        * @returns {String} The text of the combobox.
        * @example
        * var combobox = $("#combobox").data("kendoComboBox");
        *
        * // get the text of the combobox.
        * var text = combobox.text();
        */
        text: function (text) {
            var that = this,
                input = that.input[0];

            if (text !== undefined) {
                that.select(function(dataItem) {
                    return that._text(dataItem) === text;
                });

                if (!that._selected) {
                    that._custom(text);
                }

                input.value = text;
            } else {
                return input.value;
            }
        },

        /**
        * Toggles the drop-down list between opened and closed state.
        * @param {Boolean} toggle Defines the whether to open/close the drop-down list.
        * @example
        * var combobox = $("#combobox").data("kendoComboBox");
        *
        * // toggles the open state of the drop-down list.
        * combobox.toggle();
        */
        toggle: function(toggle) {
            var that = this;
            clearTimeout(that._bluring);
            that.input[0].focus();
            setTimeout( function () { that._toggle(toggle); }); // Fixes an annoying flickering issue in iOS.
        },

        /**
        * Gets/Sets the value of the combobox. If the value is undefined, text of the data item will be used.
        * @param {String} value The value to set.
        * @returns {String} The value of the combobox.
        * @example
        * var combobox = $("#combobox").data("kendoComboBox");
        *
        * // get the value of the combobox.
        * var value = combobox.value();
        *
        * // set the value of the combobox.
        * combobox.value("1"); //looks for item which has value "1"
        */
        value: function(value) {
            var that = this,
                idx,
                element = that.element;

            if (value !== undefined) {
                idx = that._index(value);

                if (idx > -1) {
                    that.select(idx);
                } else {
                    that.current(NULL);
                    that._custom(value);
                    that.text(value);
                }

                that._old = that._accessor();
            } else {
                return that._accessor();
            }
        },

        _accept: function(li) {
            var that = this;

            if (li && that.popup.visible()) {

                if (that._state === STATE_FILTER) {
                    that._state = STATE_ACCEPT;
                }

                setTimeout( function () { that._focus(li); });
            } else {
                that.text(that.text());
                that._change();
            }
        },

        _custom: function(value) {
            var that = this,
                element = that.element,
                custom = that._option;

            if (element.is(SELECT)) {
                if (!custom) {
                    custom = that._option = $("<option/>");
                    element.append(custom);
                }
                custom.text(value);
                custom[0].selected = true;
            } else {
                element.val(value);
            }
        },

        _filter: function(word) {
            var that = this,
                options = that.options,
                word = word.toLowerCase(),
                dataSource = that.dataSource,
                predicate = function (dataItem) {
                    var text = that._text(dataItem);
                    if (text !== undefined) {
                        text = text + "";
                        if (text !== "" && word === "") {
                            return false;
                        }

                        return text.toLowerCase().indexOf(word) === 0;
                    }
                };

            if (!that.ul[0].firstChild) {
                dataSource.one(CHANGE, function () { that.search(word); }).fetch();
                return;
            }

            if (that._highlight(predicate) !== -1) {
                if (options.suggest && that._current) {
                    that.suggest(that._current);
                }
                that.open();
            }

            that._hideBusy();
        },

        _highlight: function(li) {
            var that = this, idx;

            if (li == undefined) {
                return -1;
            }

            li = that._get(li);
            idx = List.inArray(li[0], that.ul[0]);

            if (idx == -1) {
                if (that.options.highlightFirst && !that.text()) {
                    li = $(that.ul[0].firstChild);
                } else {
                    li = NULL;
                }
            }

            that.current(li);

            return idx;
        },

        _input: function() {
            var that = this,
                element = that.element[0],
                wrapper = that.wrapper,
                SELECTOR = ".k-input",
                input;

            input = wrapper.find(SELECTOR);

            if (!input[0]) {
                wrapper.append('<span class="k-dropdown-wrap k-state-default"><input class="k-input" type="text" autocomplete="off"/><span class="k-select"><span class="k-icon k-arrow-down">select</span></span></span>')
                       .append(that.element);

                input = wrapper.find(SELECTOR);
            }

            input[0].style.cssText = element.style.cssText;
            input.addClass(element.className)
                 .val(element.value)
                 .css({
                    width: "100%",
                    height: "auto"
                 })
                 .show();

            that._focused = that.input = input;
            that._arrow = wrapper.find(".k-icon");
            that._inputWrapper = $(wrapper[0].firstChild)
        },

        _keydown: function(e) {
            var that = this;

            if (kendo.keys.TAB === e.keyCode) {
                that.text(that.input.val());

                if (that._state === STATE_FILTER && that._selected) {
                    that._state = STATE_ACCEPT;
                }

            } else if (!that._move(e)) {
               that._search();
            }
        },

        _search: function() {
            var that = this;
            clearTimeout(that._typing);

            that._typing = setTimeout(function() {
                var value = that.text();
                if (that._prev !== value) {
                    that._prev = value;
                    that.search(value);
                }
            }, that.options.delay);
        },

        _select: function() {
            var that = this;

            that.dataSource.one(CHANGE, function() {
                var value = that.value();
                if (value) {
                    that.value(value);
                } else {
                    that.select(that.options.index);
                }

                that._old = that.value();
            }).query();
        },

        _wrapper: function() {
            var that = this,
                element = that.element,
                wrapper;

            wrapper = element.parent();

            if (!wrapper.is("span.k-widget")) {
                wrapper = element.hide().wrap("<span />").parent();
            }

            wrapper[0].style.cssText = element[0].style.cssText;
            that.wrapper = wrapper.addClass("k-widget k-combobox k-header").show();
        }
    });

    ui.plugin(ComboBox);
})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        ui = kendo.ui,
        DataSource = kendo.data.DataSource,
        Groupable = ui.Groupable,
        tbodySupportsInnerHtml = kendo.support.tbodyInnerHtml,
        Widget = ui.Widget,
        keys = kendo.keys,
        isPlainObject = $.isPlainObject,
        extend = $.extend,
        map = $.map,
        isArray = $.isArray,
        proxy = $.proxy,
        isFunction = $.isFunction,
        math = Math,
        REQUESTSTART = "requestStart",
        ERROR = "error",
        ROW_SELECTOR = "tbody>tr:not(.k-grouping-row,.k-detail-row):visible",
        DATA_CELL = ":not(.k-group-cell,.k-hierarchy-cell):visible",
        CELL_SELECTOR =  ROW_SELECTOR + ">td" + DATA_CELL,
        FIRST_CELL_SELECTOR = CELL_SELECTOR + ":first",
        EDIT = "edit",
        SAVE = "save",
        REMOVE = "remove",
        DETAILINIT = "detailInit",
        CHANGE = "change",
        SAVECHANGES = "saveChanges",
        MODELCHANGE = "modelChange",
        DATABOUND = "dataBound",
        DETAILEXPAND = "detailExpand",
        DETAILCOLLAPSE = "detailCollapse",
        FOCUSED = "k-state-focused",
        FOCUSABLE = "k-focusable",
        SELECTED = "k-state-selected",
        CLICK = "click",
        HEIGHT = "height",
        TABINDEX = "tabIndex",
        FUNCTION = "function",
        STRING = "string",
        DELETECONFIRM = "Are you sure you want to delete this record?",
        formatRegExp = /\}/ig,
        templateHashRegExp = /#/ig,
        COMMANDBUTTONTEMP = '<a class="k-button k-button-icontext #=className#" #=attr# href="\\#"><span class="k-icon #=imageClass#"></span>#=text#</a>';

    var VirtualScrollable =  Widget.extend({
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);
            that.dataSource = options.dataSource;
            that.dataSource.bind(CHANGE, proxy(that.refresh, that));
            that.wrap();
        },

        options: {
            name: "VirtualScrollable",
            itemHeight: $.noop
        },

        wrap: function() {
            var that = this,
                // workaround for IE issue where scroll is not raised if container is same width as the scrollbar
                scrollbar = kendo.support.scrollbar() + 1,
                element = that.element;

            element.css( {
                width: "auto",
                paddingRight: scrollbar,
                overflow: "hidden"
            });
            that.content = element.children().first();
            that.wrapper = that.content.wrap('<div class="k-virtual-scrollable-wrap"/>')
                                .parent()
                                .bind("DOMMouseScroll", proxy(that._wheelScroll, that))
                                .bind("mousewheel", proxy(that._wheelScroll, that));

            that.verticalScrollbar = $('<div class="k-scrollbar k-scrollbar-vertical" />')
                                        .css({
                                            width: scrollbar
                                        }).appendTo(element)
                                        .bind("scroll", proxy(that._scroll, that));
        },

        _wheelScroll: function(e) {
            var that = this,
                scrollTop = that.verticalScrollbar.scrollTop(),
                originalEvent = e.originalEvent,
                delta;

            e.preventDefault();

            if (originalEvent.wheelDelta) {
                delta = originalEvent.wheelDelta;
            } else if (originalEvent.detail) {
                delta = -originalEvent.detail;
            } else if ($.browser.opera) {
                delta = -originalEvent.wheelDelta;
            }
            that.verticalScrollbar.scrollTop(scrollTop + (-delta));
        },

        _scroll: function(e) {
            var that = this,
                scrollTop = e.currentTarget.scrollTop,
                dataSource = that.dataSource,
                rowHeight = that.itemHeight,
                skip = dataSource.skip() || 0,
                start = that._rangeStart || skip,
                height = that.element.innerHeight(),
                isScrollingUp = !!(that._scrollbarTop && that._scrollbarTop > scrollTop),
                firstItemIndex = math.max(math.floor(scrollTop / rowHeight), 0),
                lastItemIndex = math.max(firstItemIndex + math.floor(height / rowHeight), 0);

            that._scrollTop = scrollTop - (start * rowHeight);
            that._scrollbarTop = scrollTop;

            if (!that._fetch(firstItemIndex, lastItemIndex, isScrollingUp)) {
                that.wrapper[0].scrollTop = that._scrollTop;
            }
        },

        _fetch: function(firstItemIndex, lastItemIndex, scrollingUp) {
            var that = this,
                dataSource = that.dataSource,
                itemHeight = that.itemHeight,
                take = dataSource.take(),
                rangeStart = that._rangeStart || dataSource.skip() || 0,
                currentSkip = math.floor(firstItemIndex / take) * take,
                fetching = false,
                prefetchAt = 0.33;

            if (firstItemIndex < rangeStart) {

                fetching = true;
                rangeStart = math.max(0, lastItemIndex - take);
                that._scrollTop = (firstItemIndex - rangeStart) * itemHeight;
                that._page(rangeStart, take);

            } else if (lastItemIndex >= rangeStart + take && !scrollingUp) {

                fetching = true;
                rangeStart = firstItemIndex;
                that._scrollTop = itemHeight;
                that._page(rangeStart, take);

            } else if (!that._fetching) {

                if (firstItemIndex < (currentSkip + take) - take * prefetchAt && firstItemIndex > take) {
                    dataSource.prefetch(currentSkip - take, take);
                }
                if (lastItemIndex > currentSkip + take * prefetchAt) {
                    dataSource.prefetch(currentSkip + take, take);
                }

            }
            return fetching;
        },

        _page: function(skip, take) {
            var that = this,
                dataSource = that.dataSource;

            clearTimeout(that._timeout);
            that._fetching = true;
            that._rangeStart = skip;

            if (dataSource.inRange(skip, take)) {
                dataSource.range(skip, take);
            } else {
                kendo.ui.progress(that.wrapper, true);
                that._timeout = setTimeout(function() {
                    dataSource.range(skip, take);
                }, 100);
            }
        },

        refresh: function() {
            var that = this,
                html = "",
                maxHeight = 250000,
                dataSource = that.dataSource,
                rangeStart = that._rangeStart,
                scrollbar = kendo.support.scrollbar(),
                wrapperElement = that.wrapper[0],
                totalHeight,
                idx,
                itemHeight;

            kendo.ui.progress(that.wrapper, false);
            clearTimeout(that._timeout);

            itemHeight = that.itemHeight = that.options.itemHeight() || 0;

            var addScrollBarHeight = (wrapperElement.scrollWidth > wrapperElement.offsetWidth) ? scrollbar : 0;

            totalHeight = dataSource.total() * itemHeight + addScrollBarHeight;

            for (idx = 0; idx < math.floor(totalHeight / maxHeight); idx++) {
                html += '<div style="width:1px;height:' + maxHeight + 'px"></div>';
            }

            if (totalHeight % maxHeight) {
                html += '<div style="width:1px;height:' + (totalHeight % maxHeight) + 'px"></div>';
            }

            that.verticalScrollbar.html(html);
            wrapperElement.scrollTop = that._scrollTop;

            if (rangeStart && !that._fetching) { // we are rebound from outside local range should be reset
                that._rangeStart = dataSource.skip();
            }
            that._fetching = false;
        }
    });

    function groupCells(count) {
        return new Array(count + 1).join('<td class="k-group-cell"></td>');
    }

    var defaultCommands = {
        create: {
            text: "Add new record",
            imageClass: "k-add",
            className: "k-grid-add"
        },
        cancel: {
            text: "Cancel changes",
            imageClass: "k-cancel",
            className: "k-grid-cancel-changes"
        },
        save: {
            text: "Save changes",
            imageClass: "k-update",
            className: "k-grid-save-changes"
        },
        destroy: {
            text: "Delete",
            imageClass: "k-delete",
            className: "k-grid-delete"
        }
    }

    /**
     *  @name kendo.ui.Grid.Description
     *
     *  @section
     *  <p>
     *      The Grid widget displays tabular data and offers rich support interacting with data,
     *      including paging, sorting, grouping, and selection. Grid is a powerful widget with
     *      many configuration options. It can be bound to local JSON data or to remote data
     *      using the Kendo DataSource component.
     *  </p>
     *  <h3>Getting Started</h3>
     *  There are two primary ways to create a Kendo Grid:
     *
     *  <ol>
     *      <li>From an existing HTML table element, defining columns, rows, and data in HTML</li>
     *      <li>From an HTML div element, defining columns and rows with configuration, and binding to data</li>
     *  </ol>
     *
     *  @exampleTitle Creating a <b>Grid</b> from existing HTML Table element
     *  @example
     *  <!-- Define the HTML table, with rows, columns, and data -->
     *  <table id="grid">
     *   <thead>
     *       <tr>
     *           <th data-field="title">Title<th>
     *           <th datao-field="year">Year<th>
     *       </tr>
     *   </thead>
     *   <tbody>
     *       <tr>
     *           <td>Star Wars: A New Hope<td>
     *           <td>1977<td>
     *       </tr>
     *       <tr>
     *           <td>Star Wars: The Empire Strikes Back<td>
     *           <td>1980<td>
     *       </tr>
     *   </tbody>
     *  </table>
     *
     *  @exampleTitle Initialize the Kendo Grid
     *  @example
     *   $(document).ready(function(){
     *       $("#grid").kendoGrid();
     *   });
     *
     *  @exampleTitle Creating a <b>Grid</b> from existing HTML Div element
     *  @example
     *  <!-- Define the HTML div that will hold the Grid -->
     *  <div id="grid">
     *  </div>
     *
     *  @exampleTitle Initialize the Kendo Grid and configure columns & data binding
     *  @example
     *    $(document).ready(function(){
     *       $("#grid").kendoGrid({modelSet.get(1)
     *           columns:[
     *               {
     *                   field: "FirstName",
     *                   title: "First Name"
     *               },
     *               {
     *                   field: "LastName",
     *                   title: "Last Name"
     *           }],
     *           dataSource: {
     *               data: [
     *                   {
     *                       FirstName: "Joe",
     *                       LastName: "Smith"
     *                   },
     *                   {
     *                       FirstName: "Jane",
     *                       LastName: "Smith"
     *               }]
     *           }
     *       });
     *   });
     *
     *  @section <h3>Configuring Grid Behavior</h3>
     *  Kendo Grid supports paging, sorting, grouping, and scrolling. Configuring any of
     *  these Grid behaviors is done using simple boolean configuration options. For
     *  example, the follow snippet shows how to enable all of these behaviors.
     *
     *  @exampleTitle Enabling Grid paging, sorting, grouping, and scrolling
     *  @example
     *    $(document).ready(function(){
     *       $("#grid").kendoGrid({
     *          groupable: true,
     *          scrollable: true,
     *          sortable: true,
     *          pageable: true
     *       });
     *   });
     *  @section
     *  By default, paging, grouping, and sorting are <strong>disabled</strong>. Scrolling is enabled by default.
     *
     *  <h3>Performance with Virtual Scrolling</h3>
     *  When binding to large data sets or when using large page sizes, reducing active in-memory
     *  DOM objects is important for performance. Kendo Grid provides built-in UI virtualization
     *  for highly optimized binding to large data sets. Enabling UI virtualization is done via simple configuration.
     *
     *  @exampleTitle Enabling Grid UI virtualization
     *  @example
     *    $(document).ready(function(){
     *       $("#grid").kendoGrid({
     *          scrollable: {
     *              virtual: true
     *          }
     *       });
     *   });
     */
    var Grid = Widget.extend(/** @lends kendo.ui.Grid.prototype */ {
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {kendo.data.DataSource|Object} [dataSource] Instance of DataSource or Object with DataSource configuration.
         * _example
         * var sharedDataSource = new kendo.data.DataSource({
         *      data: [{title: "Star Wars: A New Hope", year: 1977}, {title: "Star Wars: The Empire Strikes Back", year: 1980}],
         *      pageSize: 1
         * });
         *
         * $("#grid").kendoGrid({
         *      dataSource: sharedDataSource
         *  });
         *
         *  //or
         *
         *  $("#grid").kendoGrid({
         *      dataSource: {
         *          data: [{title: "Star Wars: A New Hope", year: 1977}, {title: "Star Wars: The Empire Strikes Back", year: 1980}],
         *          pageSize: 1
         *      }
         *  });
         * @option {Function} [detailTemplate] Template to be used for rendering the detail rows in the grid.
         * @option {Object} [sortable] Defines whether grid columns are sortable.
         * @option {String} [sortable.mode] <"single"> Defines sorting mode. Possible values:
         *    <dl>
         *         <dt>
         *              "single"
         *         </dt>
         *         <dd>
         *             Defines that only once column can be sorted at a time.
         *         </dd>
         *         <dt>
         *              "multiple"
         *         </dt>
         *         <dd>
         *              Defines that multiple columns can be sorted at a time.
         *         </dd>
         *    </dl>
         *
         * @option {Boolean} [sortable.allowUnsort] <false>  Defines whether column can have unsorted state.
         * @option {Array} [columns] A collection of column objects or collection of strings that represents the name of the fields.
         * @option {String} [columns.field] The field that will displayed in the column.
         * @option {String} [columns.title] The title that will displayed in the column header.
         * @option {String} [columns.format] The format that will be applied on the column cells.
         * _example
         *  $(".k-grid").kendoGrid({
         *      dataSource: {
         *          data: createRandomData(50),
         *          pageSize: 10
         *      },
         *      columns: [
         *          {
         *              field: "BirthDate",
         *              title: "Birth Date",
         *              format: "{0:dd/MMMM/yyyy}"
         *         }
         *      ]
         *   });
         * @option {Boolean} [columns.filterable] <true> Specifies whether given column is filterable.
         * @option {Boolean} [columns.sortable] <true> Specifies whether given column is sortable.
         * @option {Function} [columns.editor] The editor will be used when column is edited.
         * @option {Object} [columns.editor.container] The container in which the editor must be added.
         * @option {Object} [columns.editor.options] Additional options.
         * @option {String} [columns.editor.options.field] The field for the editor.
         * @option {Object} [columns.editor.options.model] The model for the editor.
         * @option {String} [columns.width] The width of the column.
         * @option {String} [columns.command] Definition of command column. The supported built-in commands are: "create", "cancel", "save", "destroy".
         * @option {String} [columns.template] The template for column's cells.
         * _example
         *  $(".k-grid").kendoGrid({
         *      dataSource: {
         *          data: createRandomData(50),
         *          pageSize: 10
         *      },
         *      columns: [
         *          {
         *              field: "Name"
         *          },
         *          {
         *              field: "BirthDate",
         *              title: "Birth Date",
         *              template: '#= kendo.toString(BirthDate,"dd MMMM yyyy") #'
         *         }
         *      ]
         *   });
         * @option {Boolean} [pageable] <false> Indicates whether paging is enabled/disabled.
         * @option {Boolean} [groupable] <false> Indicates whether grouping is enabled/disabled.
         * @option {Boolean} [navigatable] <false> Indicates whether keyboard navigation is enabled/disabled.
         * @option {String} [selectable] <undefined> Indicates whether selection is enabled/disabled. Possible values:
         *    <dl>
         *         <dt>
         *              "row"
         *         </dt>
         *         <dd>
         *              Single row selection.
         *         </dd>
         *         <dt>
         *              "cell"
         *         </dt>
         *         <dd>
         *              Single cell selection.
         *         </dd>
         *         <dt>
         *              "multiple, row"
         *         </dt>
         *         <dd>
         *              Multiple row selection.
         *         </dd>
         *         <dt>
         *              "multiple, cell"
         *         </dt>
         *         <dd>
         *              Multiple cell selection.
         *         </dd>
         *    </dl>
         * @option {Boolean} [autoBind] <false> Indicates whether the grid will call query on DataSource initially.
         * @option {Boolean|Object} [scrollable] <true> Enable/disable grid scrolling. Possible values:
         *    <dl>
         *         <dt>
         *              true
         *         </dt>
         *         <dd>
         *              Enables grid vertical scrolling
         *         </dd>
         *         <dt>
         *              false
         *         </dt>
         *         <dd>
         *              Disables grid vertical scrolling
         *         </dd>
         *         <dt>
         *              { virtual: false }
         *         </dt>
         *         <dd>
         *              Enables grid vertical scrolling without data virtualization. Same as first option.
         *         </dd>
         *         <dt>
         *              { virtual: true }
         *         </dt>
         *         <dd>
         *              Enables grid vertical scrolling with data virtualization.
         *         </dd>
         *    </dl>
         * _example
         *  $("#grid").kendoGrid({
         *      scrollable: {
         *          virtual: true //false
         *      }
         *  });
         * @option {Function} [rowTemplate] Template to be used for rendering the rows in the grid.
         * _example
         *  //template
         *  &lt;script id="rowTemplate" type="text/x-kendo-tmpl"&gt;
         *      &lt;tr&gt;
         *          &lt;td&gt;
         *              &lt;img src="${ BoxArt.SmallUrl }" alt="${ Name }" /&gt;
         *          &lt;/td&gt;
         *          &lt;td&gt;
         *              ${ Name }
         *          &lt;/td&gt;
         *          &lt;td&gt;
         *              ${ AverageRating }
         *          &lt;/td&gt;
         *      &lt;/tr&gt;
         *  &lt;/script&gt;
         *
         *  //grid intialization
         *  &lt;script&gt;PO details informaiton
         *      $("#grid").kendoGrid({
         *          dataSource: dataSource,
         *          rowTemplate: kendo.template($("#rowTemplate").html()),
         *          height: 200
         *      });
         *  &lt;/script&gt;
         */
        init: function(element, options) {
            var that = this;

            options = isArray(options) ? { dataSource: options } : options;

            Widget.fn.init.call(that, element, options);

            that._element();

            that._columns(that.options.columns);

            that._dataSource();

            that._tbody();

            that._pageable();

            that._groupable();

            that._toolbar();

            that.bind([
                /**
                 * Fires when the grid selection has changed.
                 * @name kendo.ui.Grid#change
                 * @event
                 * @param {Event} e
                 */
                CHANGE,
                /**
                 * Fires when the grid has received data from the data source.
                 * @name kendo.ui.Grid#dataBound
                 * @event
                 * @param {Event} e
                 */
                DATABOUND,
                /**
                 * Fires when the grid detail row is expanded.
                 * @name kendo.ui.Grid#detailExpand
                 * @event
                 * @param {Event} e
                 * @param {Object} e.masterRow The jQuery element representing master row.
                 * @param {Object} e.detailRow The jQuery element representing detail row.
                 */
                DETAILEXPAND,
                /**
                 * Fires when the grid detail row is collapsed.
                 * @name kendo.ui.Grid#detailCollapse
                 * @event
                 * @param {Event} e
                 * @param {Object} e.masterRow The jQuery element representing master row.
                 * @param {Object} e.detailRow The jQuery element representing detail row.
                 */
                DETAILCOLLAPSE,
                /**
                 * Fires when the grid detail is initialized.
                 * @name kendo.ui.Grid#detailInit
                 * @event
                 * @param {Event} e
                 * @param {Object} e.masterRow The jQuery element representing master row.
                 * @param {Object} e.detailRow The jQuery element representing detail row.
                 * @param {Object} e.detailCell The jQuery element representing detail cell.
                 * @param {Object} e.data The data for the master row.
                 */
                DETAILINIT,
                /**
                 * Fires when the grid enters edit mode.
                 * @name kendo.ui.Grid#edit
                 * @event
                 * @param {Event} e
                 * @param {Object} e.container The jQuery element to be edited.
                 * @param {Object} e.model The model to be edited.
                 */
                EDIT,
                /**
                 * Fires before the grid item is changed.
                 * @name kendo.ui.Grid#save
                 * @event
                 * @param {Event} e
                 * @param {Object} e.values The values entered by the user.
                 * @param {Object} e.container The jQuery element which is in edit mode.
                 * @param {Object} e.model The edited model.
                 */
                SAVE,
                /**
                 * Fires before the grid item is removed.
                 * @name kendo.ui.Grid#remove
                 * @event
                 * @param {Event} e
                 * @param {Object} e.row The row element to be deleted.
                 * @param {Object} e.model The model which to be deleted.
                 */
                REMOVE,
                /**
                 * Fires before the grid calls DataSource sync.
                 * @name kendo.ui.Grid#saveChanges
                 * @event
                 * @param {Event} e
                 */
                SAVECHANGES
            ], that.options);

            that._thead();

            that._templates();

            that._navigatable();

            that._selectable();

            that._details();

            that._editable();

            if (that.options.autoBind) {
                that.dataSource.fetch();
            }
        },

        options: {
            name: "Grid",
            columns: [],
            autoBind: true,
            scrollable: true,
            groupable: false,
            dataSource: {}
        },

        _element: function() {
            var that = this,
                table = that.element;

            if (!table.is("table")) {
                table = $("<table />").appendTo(that.element);
            }

            that.table = table.attr("cellspacing", 0);

            that._wrapper();
        },

        /**
         * Returns the index of the cell in the grid item skipping group and hierarchy cells.
         * @param {Selector|DOMElement} cell Target cell.
         */
        cellIndex: function(td) {
            return $(td).parent().find('td:not(.k-group-cell,.k-hierarchy-cell)').index(td);
        },

        _modelForContainer: function(container) {
            var id = (container.is("tr") ? container : container.closest("tr")).attr(kendo.attr("id"));

            return this.dataSource.get(id);
        },

        _editable: function() {
            var that = this,
                cell,
                model,
                column,
                editable = that.options.editable,
                handler = function () {
                    var target = document.activeElement,
                        cell = that._editContainer;

                    if (cell && !$.contains(cell[0], target) && cell[0] !== target && !$(target).closest(".k-animation-container").length) {
                        if (that.editable.end()) {
                            that.closeCell();
                        }
                    }
                };

            if (editable) {

                if (editable.update !== false) {
                    that.wrapper.delegate("tr:not(.k-grouping-row) > td:not(.k-hierarchy-cell,.k-detail-cell,.k-group-cell,.k-edit-cell,:has(a.k-grid-delete))", CLICK, function(e) {
                        var td = $(this)

                        if (td.closest("tbody")[0] !== that.tbody[0] || $(e.target).is(":input")) {
                            return;
                        }

                        if (that.editable) {
                            if (that.editable.end()) {
                                that.closeCell();
                                that.editCell(td);
                            }
                        } else {
                            that.editCell(td);
                        }

                    });

                    that.wrapper.bind("focusin", function(e) {
                        clearTimeout(that.timer);
                        that.timer = null;
                    });
                    that.wrapper.bind("focusout", function(e) {
                        that.timer = setTimeout(handler, 1);
                    });
                }

                if (editable.destroy !== false) {
                    that.wrapper.delegate("tbody>tr:not(.k-detail-row,.k-grouping-row):visible a.k-grid-delete", "click", function(e) {
                        e.preventDefault();
                        that.removeRow($(this).closest("tr"));
                    });
               }
            }
        },

        /**
         * Puts the specified table cell in edit mode. It requires a jQuery object representing the cell. The editCell method triggers edit event.
         * @param {Selector} cell Cell to be edited.
         * @example
         * // edit first table cell
         * grid.editCell(grid.tbody.find(">tr>td:first"));
         */
        editCell: function(cell) {
            var that = this,
                column = that.columns[that.cellIndex(cell)],
                model = that._modelForContainer(cell);

            if (model.editable(column.field) && !cell.has("a.k-grid-delete").length) {
                that._editContainer = cell;

                that.editable = cell.addClass("k-edit-cell")
                    .kendoEditable({
                        fields: { field: column.field, format: column.format },
                        model: model,
                        change: function(e) {
                            if (that.trigger(SAVE, { values: e.values, container: cell, model: model } )) {
                                e.preventDefault();
                            }
                        }
                    }).data("kendoEditable");

                cell.parent().addClass("k-grid-edit-row");

                that.trigger(EDIT, { container: cell, model: model });
            }
        },

        _distroyEditable: function() {
            var that = this;

            if (that.editable) {
                that.editable.distroy();
                delete that.editable;
                that._editContainer = null;
            }
        },

        /**
         * Closes current edited cell.
         * @example
         * grid.closeCell();
         */
        closeCell: function() {
            var that = this,
                cell = that._editContainer.removeClass("k-edit-cell"),
                id = cell.closest("tr").attr(kendo.attr("id")),
                column = that.columns[that.cellIndex(cell)],
                model = that.dataSource.get(id);

            cell.parent().removeClass("k-grid-edit-row");

            that._displayCell(cell, column, model.data);

            if (column.field in (model.changes() || {})) {
                $('<span class="k-dirty"/>').prependTo(cell);
            }
            that._distroyEditable();
        },

        _displayCell: function(cell, column, dataItem) {
            var that = this,
                state = { storage: {}, count: 0 },
                settings = extend({}, kendo.Template, that.options.templateSettings),
                tmpl = kendo.template(that._cellTmpl(column, state), settings);

            if (state.count > 0) {
                tmpl = proxy(tmpl, state.storage);
            }

            cell.empty().html(tmpl(dataItem));
        },

        /**
         * Removes the specified row from the grid. The removeRow method triggers remove event.
         * @param {Selector|DOMElement} row Row to be removed.
         * @example
         * // remove first table row
         * grid.removeRow(grid.tbody.find(">tr:first"));
         *
         */
        removeRow: function(row) {
            var that = this,
                model;

            if (!that._confirmation()) {
                return;
            }

            row = $(row).hide();
            model = that._modelForContainer(row);

            if (model && !that.trigger(REMOVE, { row: row, model: model })) {
                that.dataSource.remove(model);
            }
        },

        _showMessage: function(text) {
            return confirm(text);
        },

        _confirmation: function() {
            var that = this;
                confirmation = that.options.editable === true ? DELETECONFIRM : that.options.editable.confirmation;

            return confirmation !== false ? that._showMessage(confirmation) : true;
        },

        /**
         * Cancels any pending changes during. Deleted rows are restored. Inserted rows are removed. Updated rows are restored to their original values.
         * @example
         * grid.cancelChanges();
         */
        cancelChanges: function() {
            this.dataSource.cancelChanges();
        },

        /**
         * Calls DataSource sync to submit any pending changes if state is valid. The saveChanges method triggers saveChanges event.
         * @example
         * grid.saveChanges();
         */
        saveChanges: function() {
            var that = this;

            if (((that.editable && that.editable.end()) || !that.editable) && !that.trigger(SAVECHANGES)) {
                that.dataSource.sync();
            }
        },

        /**
         * Adds a new empty table row in edit mode. The addRow method triggers edit event.
         * @example
         * grid.addRow();
         */
        addRow: function() {
            var that = this,
                dataSource = that.dataSource;

            if ((that.editable && that.editable.end()) || !that.editable) {
                var index = dataSource.indexOf((dataSource.view() || [])[0]) || 0,
                    model = dataSource.insert(index, {}),
                    id = model.id(),
                    cell = that.table.find("tr[" + kendo.attr("id") + "=" + id + "] > td:not(.k-group-cell,.k-hierarchy-cell)").first();

                if (cell.length) {
                    that.editCell(cell);
                }
            }
        },

        _toolbar: function() {
            var that = this,
                wrapper = that.wrapper,
                toolbar = that.options.toolbar,
                template;

            if (toolbar) {
                toolbar = isFunction(toolbar) ? toolbar({}) : (typeof toolbar === STRING ? toolbar : that._toolbarTmpl(toolbar).replace(templateHashRegExp, "\\#"));

                template = proxy(kendo.template(toolbar), that)

                $('<div class="k-toolbar k-grid-toolbar" />')
                    .html(template({}))
                    .prependTo(wrapper)
                    .delegate(".k-grid-add", CLICK, function(e) { e.preventDefault(); that.addRow(); })
                    .delegate(".k-grid-cancel-changes", CLICK, function(e) { e.preventDefault(); that.cancelChanges(); })
                    .delegate(".k-grid-save-changes", CLICK, function(e) { e.preventDefault(); that.saveChanges(); });
            }
        },

        _toolbarTmpl: function(commands) {
            var that = this,
                idx,
                length,
                html = "",
                options,
                commandName,
                template,
                command;

            if (isArray(commands)) {
                for (idx = 0, length = commands.length; idx < length; idx++) {
                    html += that._createButton(commands[idx]);
                }
            }
            return html;
        },

        _createButton: function(command) {
            var that = this,
                template = command.template || COMMANDBUTTONTEMP,
                commandName = typeof command === STRING ? command : command.name,
                options = { className: "", text: commandName, imageClass: "", attr: "" };

            if (isPlainObject(command)) {
                options = extend(true, options, defaultCommands[commandName], command);
            } else {
                options = extend(true, options, defaultCommands[commandName]);
            }

            return kendo.template(template)(options);
        },

        _groupable: function() {
            var that = this,
                wrapper = that.wrapper,
                groupable = that.options.groupable;

            if (groupable) {
                if(!wrapper.has("div.k-grouping-header")[0]) {
                    $("<div />").addClass("k-grouping-header").html("&nbsp;").prependTo(wrapper);
                }

                that.groupable = new Groupable(wrapper, {
                    filter: "th:not(.k-group-cell)[" + kendo.attr("field") + "]",
                    groupContainer: "div.k-grouping-header",
                    dataSource: that.dataSource
                });
            }

            that.table.delegate(".k-grouping-row .k-collapse, .k-grouping-row .k-expand", CLICK, function(e) {
                var element = $(this),
                    group = element.closest("tr");

                if(element.hasClass('k-collapse')) {
                    that.collapseGroup(group);
                } else {
                    that.expandGroup(group);
                }
                e.preventDefault();
            });
        },

        _selectable: function() {
            var that = this,
                multi,
                cell,
                selectable = that.options.selectable;

            if (selectable) {
                multi = typeof selectable === STRING && selectable.toLowerCase().indexOf("multiple") > -1;
                cell = typeof selectable === STRING && selectable.toLowerCase().indexOf("cell") > -1;

                that.selectable = new kendo.ui.Selectable(that.table, {
                    filter: cell ? CELL_SELECTOR : ROW_SELECTOR,
                    multiple: multi,
                    change: function() {
                        that.trigger(CHANGE);
                    }
                });

                if (that.options.navigatable) {
                    that.wrapper.keydown(function(e) {
                        var current = that.current();
                        if (e.keyCode === keys.SPACEBAR && !current.hasClass("k-edit-cell")) {
                            e.preventDefault();
                            current = cell ? current : current.parent();

                            if(multi) {
                                if(!e.ctrlKey) {
                                    that.selectable.clear();
                                } else {
                                    if(current.hasClass(SELECTED)) {
                                        current.removeClass(SELECTED);
                                        current = null;
                                    }
                                }
                            } else {
                                that.selectable.clear();
                            }

                            that.selectable.value(current);
                        }
                    });
                }
            }
        },

        /**
         * Clears currently selected items.
         */
        clearSelection: function() {
            var that = this;
            that.selectable.clear();
            that.trigger(CHANGE);
        },

        /**
         * Selects the specified Grid rows/cells. If called without arguments - returns the selected rows/cells.
         * @param {Selector|Array} items Items to select.
         * @example
         * // selects first grid item
         * grid.select(grid.tbody.find(">tr:first"));
         */
        select: function(items) {
            var that = this,
                selectable = that.selectable;

            items = $(items);
            if(items.length) {
                if(!selectable.options.multiple) {
                    selectable.clear();
                    items = items.first();
                }
                selectable.value(items);
                return;
            }

            return selectable.value();
        },

        current: function(element) {
            var that = this,
                current = that._current;

            if (element !== undefined && element.length) {
                if (!current || current[0] !== element[0]) {
                    element.addClass(FOCUSED);
                    if (current) {
                        current.removeClass(FOCUSED);
                    }
                    that._current = element;
                    that._scrollTo(element.parent()[0]);
                }
            }

            return that._current;
        },

        _scrollTo: function(element) {
            if(!element || !this.options.scrollable) {
                return;
            }

            var elementOffsetTop = element.offsetTop,
                container = this.content[0],
                elementOffsetHeight = element.offsetHeight,
                containerScrollTop = container.scrollTop,
                containerOffsetHeight = container.clientHeight,
                bottomDistance = elementOffsetTop + elementOffsetHeight;

            container.scrollTop = containerScrollTop > elementOffsetTop
                                    ? elementOffsetTop
                                    : bottomDistance > (containerScrollTop + containerOffsetHeight)
                                    ? bottomDistance - containerOffsetHeight
                                    : containerScrollTop;
        },

        _navigatable: function() {
            var that = this,
                wrapper = that.wrapper,
                table = that.table.addClass(FOCUSABLE),
                currentProxy = proxy(that.current, that),
                selector = "." + FOCUSABLE + " " + CELL_SELECTOR,
                browser = $.browser,
                clickCallback = function(e) {
                    currentProxy($(e.currentTarget));
                    if(e.type == CLICK && !$(e.target).is(":button,a,:input,a>.k-icon,textarea")) {
                        wrapper.focus();
                    }
                };

            if (that.options.navigatable) {
                wrapper.bind({
                    focus: function() {
                        var current = that._current;
                        if(current && current.is(":visible")) {
                            current.addClass(FOCUSED);
                        } else {
                            currentProxy(that.table.find(FIRST_CELL_SELECTOR));
                        }
                    },
                    focusout: function() {
                        if (that._current) {
                            that._current.removeClass(FOCUSED);
                        }
                    },
                    keydown: function(e) {
                        var key = e.keyCode,
                            current = that.current(),
                            shiftKey = e.shiftKey,
                            dataSource = that.dataSource,
                            pageable = that.options.pageable,
                            canHandle = !$(e.target).is(":button,a,:input,a>.t-icon"),
                            handled = false;

                        if (canHandle && keys.UP === key) {
                            currentProxy(current ? current.parent().prevAll(ROW_SELECTOR).last().children(":eq(" + current.index() + "),:eq(0)").last() : table.find(FIRST_CELL_SELECTOR));
                            handled = true;
                        } else if (canHandle && keys.DOWN === key) {
                            currentProxy(current ? current.parent().nextAll(ROW_SELECTOR).first().children(":eq(" + current.index() + "),:eq(0)").last() : table.find(FIRST_CELL_SELECTOR));
                            handled = true;
                        } else if (canHandle && keys.LEFT === key) {
                            currentProxy(current ? current.prevAll(DATA_CELL + ":first") : table.find(FIRST_CELL_SELECTOR));
                            handled = true;
                        } else if (canHandle && keys.RIGHT === key) {
                            currentProxy(current ? current.nextAll(":visible:first") : table.find(FIRST_CELL_SELECTOR));
                            handled = true;
                        } else if (canHandle && pageable && keys.PAGEDOWN == key) {
                            that._current = null;
                            dataSource.page(dataSource.page() + 1);
                            handled = true;
                        } else if (canHandle && pageable && keys.PAGEUP == key) {
                            that._current = null;
                            dataSource.page(dataSource.page() - 1);
                            handled = true;
                        } else if (that.options.editable) {
                            if (keys.ENTER == key || keys.F12 == key) {
                                that._handleEditing(current);
                                handled = true;
                            } else if (keys.TAB == key) {
                                var cell = shiftKey ? current.prevAll(DATA_CELL + ":first") : current.nextAll(":visible:first");
                                if (!cell.length) {
                                    cell = current.parent()[shiftKey ? "prevAll" : "nextAll"]("tr:not(.k-grouping-row,.k-detail-row):visible")
                                        .children(DATA_CELL + (shiftKey ? ":last" : ":first"));
                                }

                                if (cell.length) {
                                    that._handleEditing(current, cell);
                                    handled = true;
                                }
                            } else if (keys.ESC == key && current.hasClass("k-edit-cell")) {
                                that.closeCell();
                                if (browser.msie && parseInt(browser.version) < 9) {
                                    document.body.focus();
                                }
                                wrapper.focus();
                                handled = true;
                            }
                        }

                        if(handled) {
                            e.preventDefault();
                        }
                    }
                });

                wrapper.delegate(selector, browser.msie ? CLICK : "mousedown", clickCallback);
            }
        },

        _handleEditing: function(current, next) {
            var that = this,
                isEdited = current.hasClass("k-edit-cell");

            if (that.editable) {
                if ($.contains(that._editContainer[0], document.activeElement)) {
                    document.activeElement.blur();
                }

                if (that.editable.end()) {
                    that.closeCell();
                } else {
                    that.current(that._editContainer);
                    that._editContainer.find(":input:visible:first").focus();
                    return;
                }
            }

            if (next) {
                that.current(next);
            }

            that.wrapper.focus();
            if ((!isEdited && !next) || next) {
                that.editCell(that.current());
            }
        },

        _wrapper: function() {
            var that = this,
                table = that.table,
                height = that.options.height,
                wrapper = that.element;

            if (!wrapper.is("div")) {
               wrapper = wrapper.wrap("<div/>").parent();
            }

            that.wrapper = wrapper.addClass("k-grid k-widget")
                                  .attr(TABINDEX, math.max(table.attr(TABINDEX) || 0, 0));

            table.removeAttr(TABINDEX);

            if (height) {
                that.wrapper.css(HEIGHT, height);
                table.css(HEIGHT, "auto");
            }
        },

        _tbody: function() {
            var that = this,
                table = that.table,
                tbody;

            tbody = table.find(">tbody");

            if (!tbody.length) {
                tbody = $("<tbody/>").appendTo(table);
            }

            that.tbody = tbody;
        },

        _scrollable: function() {
            var that = this,
                header,
                table,
                options = that.options,
                height = that.wrapper.innerHeight(),
                scrollable = options.scrollable,
                scrollbar = kendo.support.scrollbar();

            if (scrollable) {
                header = that.wrapper.children().filter(".k-grid-header");

                if (!header[0]) {
                    header = $('<div class="k-grid-header" />').insertBefore(that.table);
                }

                // workaround for IE issue where scroll is not raised if container is same width as the scrollbar
                header.css("padding-right", scrollable.virtual ? scrollbar + 1 : scrollbar);
                table = $('<table cellspacing="0" />');
                table.append(that.thead);
                header.empty().append($('<div class="k-grid-header-wrap" />').append(table));

                that.content = that.table.parent();

                if (that.content.is(".k-virtual-scrollable-wrap")) {
                    that.content = that.content.parent();
                }

                if (!that.content.is(".k-grid-content, .k-virtual-scrollable-wrap")) {
                    that.content = that.table.wrap('<div class="k-grid-content" />').parent();

                    if (scrollable !== true && scrollable.virtual) {
                        new VirtualScrollable(that.content, {
                            dataSource: that.dataSource,
                            itemHeight: proxy(that._averageRowHeight, that)
                        });
                    }
                }

                height -= header.outerHeight();

                if (that.pager) {
                    height -= that.pager.element.outerHeight();
                }

                if(options.groupable) {
                    height -= $(".k-grouping-header").outerHeight();
                }

                if(options.toolbar) {
                    height -= $(".k-grid-toolbar").outerHeight();
                }
                that.content.height(height);

                var scrollables = header.find(">.k-grid-header-wrap"); // add footer when implemented

                if (scrollable.virtual) {
                    that.content.find(">.k-virtual-scrollable-wrap").bind('scroll', function () {
                        scrollables.scrollLeft(this.scrollLeft);
                    });
                } else {
                    that.content.bind('scroll', function () {
                        scrollables.scrollLeft(this.scrollLeft);
                    });
                }
            }
        },

        _averageRowHeight: function() {
            var that = this,
                rowHeight = that._rowHeight;

            if (!that._rowHeight) {
                that._rowHeight = rowHeight = that.table.outerHeight() / that.table[0].rows.length;
                that._sum = rowHeight;
                that._measures = 1;

                totalHeight = math.round(that.dataSource.total() * rowHeight);
            }

            var currentRowHeight = that.table.outerHeight() / that.table[0].rows.length;

            if (rowHeight !== currentRowHeight) {
                that._measures ++;
                that._sum += currentRowHeight;
                that._rowHeight = that._sum / that._measures;
            }
            return rowHeight;
        },

        _dataSource: function() {
            var that = this,
                options = that.options,
                pageable,
                dataSource = options.dataSource;

            dataSource = isArray(dataSource) ? { data: dataSource } : dataSource;

            if (isPlainObject(dataSource)) {
                extend(dataSource, { table: that.table, fields: that.columns });

                pageable = options.pageable;

                if (isPlainObject(pageable) && pageable.pageSize !== undefined) {
                    dataSource.pageSize = pageable.pageSize;
                }
            }

            that.dataSource = DataSource.create(dataSource)
                                .bind(CHANGE, proxy(that.refresh, that))
                                .bind(REQUESTSTART, proxy(that._requestStart, that))
                                .bind(ERROR, proxy(that._error, that))
                                .bind(MODELCHANGE, proxy(that._modelChange, that));
        },

        _error: function() {
            this._progress(false);
        },

        _requestStart: function() {
            this._progress(true);
        },

        _modelChange: function(model) {
            var that = this,
                row = that.tbody.find("tr[" + kendo.attr("id") + "=" + model.id() +"]"),
                changes = model.changes() || {},
                cell,
                column,
                isAlt = row.hasClass("k-alt");

            if (row.has(".k-edit-cell")) {
                row.find(">td:not(.k-group-cell,.k-hierarchy-cell,.k-edit-cell)").each(function() {
                    cell = $(this);
                    column = that.columns[that.cellIndex(cell)];

                    if (column.field in changes) {
                        that._displayCell(cell, column, model.data);
                        $('<span class="k-dirty"/>').prependTo(cell);
                    }
                });
            } else {
                row.replaceWith($((isAlt ? that.altRowTemplate : that.rowTemplate)(model.data)));
            }
        },

        _pageable: function() {
            var that = this,
                wrapper,
                pageable = that.options.pageable;

            if (pageable) {
                wrapper = that.wrapper.children("div.k-grid-pager");

                if (!wrapper.length) {
                    wrapper = $('<div class="k-grid-pager"/>').appendTo(that.wrapper);
                }

                if (typeof pageable === "object" && pageable instanceof kendo.ui.Pager) {
                    that.pager = pageable;
                } else {
                    that.pager = new kendo.ui.Pager(wrapper, extend({}, pageable, { dataSource: that.dataSource }));
                }
            }
        },

        _filterable: function() {
            var that = this,
                columns = that.columns,
                filterable = that.options.filterable;

            if (filterable) {
                that.thead
                    .find("th:not(.k-hierarchy-cell)")
                    .each(function(index) {
                        if (columns[index].filterable !== false) {
                            $(this).kendoFilterMenu(extend(true, {}, filterable, columns[index].filterable, { dataSource: that.dataSource }));
                        }
                    })
            }
        },

        _sortable: function() {
            var that = this,
                columns = that.columns,
                column,
                sortable = that.options.sortable;

            if (sortable) {
                that.thead
                    .find("th:not(.k-hierarchy-cell)")
                    .each(function(index) {
                        column = columns[index];
                        if (column.sortable !== false && !column.command) {
                            $(this).kendoSortable(extend({}, sortable, { dataSource: that.dataSource }));
                        }
                    })
            }
        },

        _columns: function(columns) {
            var that = this,
                table = that.table,
                encoded,
                cols = table.find("col"),
                dataSource = that.options.dataSource;

            // using HTML5 data attributes as a configuration option e.g. <th data-field="foo">Foo</foo>
            columns = columns.length ? columns : map(table.find("th"), function(th, idx) {
                var th = $(th),
                    sortable = th.attr(kendo.attr("sortable"))
                    filterable = th.attr(kendo.attr("filterable"))
                    field = th.attr(kendo.attr("field"));

                if (!field) {
                   field = th.text().replace(/\s|[^A-z0-9]/g, "");
                }

                return {
                    field: field,
                    sortable: sortable !== "false",
                    filterable: filterable !== "false",
                    template: th.attr(kendo.attr("template")),
                    width: cols.eq(idx).css("width")
                };
            });

            encoded = !(that.table.find("tbody tr").length > 0 && (!dataSource || !dataSource.transport));

            that.columns = map(columns, function(column) {
                column = typeof column === STRING ? { field: column } : column;
                return extend({ encoded: encoded }, column);
            });
        },

        _tmpl: function(rowTemplate, alt) {
            var that = this,
                settings = extend({}, kendo.Template, that.options.templateSettings),
                paramName = settings.paramName,
                idx,
                length = that.columns.length,
                template,
                model = that.dataSource.options.schema.model,
                state = { storage: {}, count: 0 },
                id,
                column,
                type,
                hasDetails = that._hasDetails(),
                className = [],
                groups = that.dataSource.group().length;

            if (!rowTemplate) {
                rowTemplate = "<tr";

                if (alt) {
                    className.push("k-alt");
                }

                if (hasDetails) {
                    className.push("k-master-row");
                }

                if (className.length) {
                    rowTemplate += ' class="' + className.join(" ") + '"';
                }

                if (model) {
                    id = model.id;
                    if (id) {
                        // render the id as data-id attribute
                        type = typeof id;

                        rowTemplate += ' ' + kendo.attr("id") + '="#=';
                        state.storage["tmpl" + state.count] = type === FUNCTION ? id : that.dataSource.reader.model.id;
                        rowTemplate += 'this.tmpl' + state.count + "(" + paramName + ")";
                        state.count++;

                        rowTemplate += '#"';
                    }
                }

                rowTemplate += ">";

                if (groups > 0) {
                    rowTemplate += groupCells(groups);
                }

                if (hasDetails) {
                    rowTemplate += '<td class="k-hierarchy-cell"><a class="k-icon k-plus" href="\\#"></a></td>';
                }

                for (idx = 0; idx < length; idx++) {
                    column = that.columns[idx];
                    template = column.template;
                    type = typeof template;

                    rowTemplate += "<td>";
                    rowTemplate += that._cellTmpl(column, state);

                    rowTemplate += "</td>";
                }

                rowTemplate += "</tr>";
            }

            rowTemplate = kendo.template(rowTemplate, settings);

            if (state.count > 0) {
                return proxy(rowTemplate, state.storage);
            }

            return rowTemplate;
        },

        _cellTmpl: function(column, state) {
            var that = this,
                settings = extend({}, kendo.Template, that.options.templateSettings),
                template = column.template,
                paramName = settings.paramName,
                html = "",
                format = column.format,
                type = typeof template;

            if (column.command) {
                return that._createButton(column.command).replace(templateHashRegExp, "\\#");
            }

            if (type === FUNCTION) {
                state.storage["tmpl" + state.count] = template;
                html += "#=this.tmpl" + state.count + "(" + paramName + ")#";
                state.count ++;
            } else if (type === STRING) {
                html += template;
            } else {
                html += column.encoded ? "${" : "#=";

                if (format) {
                    html += 'kendo.format(\"' + format.replace(formatRegExp,"\\}") + '\",';
                }

                if (!settings.useWithBlock) {
                    html += paramName + ".";
                }

                html += column.field;

                if (format) {
                    html += ")";
                }

                html += column.encoded ? "}" : "#";
            }
            return html;
        },

        _templates: function() {
            var that = this,
                options = that.options;

            that.rowTemplate = that._tmpl(options.rowTemplate);
            that.altRowTemplate = that._tmpl(options.altRowTemplate || options.rowTemplate, true);

            if (that._hasDetails()) {
                that.detailTemplate = that._detailTmpl(options.detailTemplate || "");
            }
        },

        _detailTmpl: function(template) {
            var that = this,
                html = "",
                settings = extend({}, kendo.Template, that.options.templateSettings),
                paramName = settings.paramName,
                templateFunctionStorage = {},
                templateFunctionCount = 0,
                groups = that.dataSource.group().length,
                columns = that.columns.length,
                type = typeof template;

                html += '<tr class="k-detail-row">';
                if (groups > 0) {
                    html += groupCells(groups);
                }
                html += '<td class="k-hierarchy-cell"></td><td class="k-detail-cell"' + (columns ? ' colspan="' + columns + '"' : '') + ">";

            if (type === FUNCTION) {
                templateFunctionStorage["tmpl" + templateFunctionCount] = template;
                html += "#=this.tmpl" + templateFunctionCount + "(" + paramName + ")#";
                templateFunctionCount ++;
            } else {
                html += template;
            }

            html += "</td></tr>";

            html = kendo.template(html, settings);

            if (templateFunctionCount > 0) {
                return proxy(html, templateFunctionStorage);
            }

            return html;
        },

        _hasDetails: function() {
            var that = this;

            return that.options.detailTemplate !== undefined  || (that._events[DETAILINIT] || []).length;
        },

        _details: function() {
            var that = this;

            that.table.delegate(".k-hierarchy-cell .k-plus, .k-hierarchy-cell .k-minus", CLICK, function(e) {
                var button = $(this),
                    expanding = button.hasClass("k-plus"),
                    masterRow = button.closest("tr.k-master-row"),
                    detailRow,
                    detailTemplate = that.detailTemplate,
                    data,
                    hasDetails = that._hasDetails();

                button.toggleClass("k-plus", !expanding)
                    .toggleClass("k-minus", expanding);

                if(hasDetails && !masterRow.next().hasClass("k-detail-row")) {
                    data = that.dataItem(masterRow),
                    $(detailTemplate(data)).insertAfter(masterRow);

                    that.trigger(DETAILINIT, { masterRow: masterRow, detailRow: masterRow.next(), data: data, detailCell: masterRow.next().find(".k-detail-cell") });
                }

                detailRow = masterRow.next();

                that.trigger(expanding ? DETAILEXPAND : DETAILCOLLAPSE, { masterRow: masterRow, detailRow: detailRow});
                detailRow.toggle(expanding);

                e.preventDefault();
                return false;
            });
        },

        /**
         * Returns the data item to which a given table row (tr DOM element) is bound.
         * @param {Selector|DOMElement} tr Target row.
         * @example
         * // returns the data item for first row
         * grid.dataItem(grid.tbody.find(">tr:first"));
         */
        dataItem: function(tr) {
            return this._data[this.tbody.find('> tr:not(.k-grouping-row,.k-detail-row)').index($(tr))]
        },

        /**
         * Expands specified master row.
         * @param {Selector|DOMElement} row Target master row to expand.
         * @example
         * // expands first master row
         * grid.expandRow(grid.tbody.find(">tr.k-master-row:first"));
         */
        expandRow: function(tr) {
            $(tr).find('> td .k-plus, > td .k-expand').click();
        },

        /**
         * Collapses specified master row.
         * @param {Selector|DOMElement} row Target master row to collapse.
         * @example
         * // collapses first master row
         * grid.collapseRow(grid.tbody.find(">tr.k-master-row:first"));
         */
        collapseRow: function(tr) {
            $(tr).find('> td .k-minus, > td .k-plus').click();
        },

        _thead: function() {
            var that = this,
                columns = that.columns,
                idx,
                length,
                html = "",
                thead = that.table.find("thead"),
                tr,
                th;

            if (!thead.length) {
                thead = $("<thead/>").insertBefore(that.tbody);
            }

            tr = that.table.find("tr").filter(":has(th)");

            if (!tr.length) {
                tr = thead.children().first();
                if(!tr.length) {
                    tr = $("<tr/>");
                }
            }

            if (!tr.children().length) {
                if (that._hasDetails() && columns.length) {
                    html += '<th class="k-hierarchy-cell">&nbsp;</th>';
                }

                for (idx = 0, length = columns.length; idx < length; idx++) {
                    th = columns[idx];

                    if (!th.command) {
                        html += "<th " + kendo.attr("field") + "='" + th.field + "' ";
                        if (th.title) {
                            html += kendo.attr("title") + "='" + th.title + "'";
                        }
                        html += ">" + (th.title || th.field || "") + "</th>";
                    } else {
                        html += "<th>" + (th.title || "") + "</th>";
                    }
                }

                tr.html(html);
            }

            tr.find("th").addClass("k-header");

            if(!that.options.scrollable) {
                thead.addClass("k-grid-header");
            }

            tr.appendTo(thead);

            that.thead = thead;

            that._sortable();

            that._filterable();

            that._scrollable();

            that._updateCols();
        },

        _updateCols: function() {
            var that = this,
                table = that.thead.parent().add(that.table),
                colgroup = table.find("colgroup"),
                width,
                cols = map(that.columns, function(column) {
                    width = column.width;
                    if (width && parseInt(width) != 0) {
                        return kendo.format('<col style="width:{0}"/>', typeof width === STRING? width : width + "px");
                    }

                    return "<col />";
                }),
                groups = that.dataSource.group().length;

            if (that._hasDetails()) {
                cols.splice(0, 0, '<col class="k-hierarchy-col" />');
            }

            if (colgroup.length) {
                colgroup.remove();
            }

            colgroup = $("<colgroup/>").append($(new Array(groups + 1).join('<col class="k-group-col">') + cols.join("")));

            table.prepend(colgroup);
        },

        _autoColumns: function(schema) {
            if (schema) {
                var that = this,
                    field;

                for (field in schema) {
                    that.columns.push({
                        field: field
                    });
                }

                that._thead();

                that._templates();
            }
        },

        _rowsHtml: function(data) {
            var that = this,
                html = "",
                idx,
                length,
                rowTemplate = that.rowTemplate,
                altRowTemplate = that.altRowTemplate;

            for (idx = 0, length = data.length; idx < length; idx++) {
                if (idx % 2) {
                    html += altRowTemplate(data[idx]);
                } else {
                    html += rowTemplate(data[idx]);
                }

                that._data.push(data[idx]);
            }

            return html;
        },

        _groupRowHtml: function(group, colspan, level) {
            var that = this,
                html = "",
                idx,
                length,
                field = group.field,
                column = $.grep(that.columns, function(column) { return column.field == field; })[0] || { },
                value = column.format ? kendo.format(column.format, group.value) : group.value,
                groupItems = group.items;

            html +=  '<tr class="k-grouping-row">' + groupCells(level) +
                      '<td colspan="' + colspan + '">' +
                        '<p class="k-reset">' +
                         '<a class="k-icon k-collapse" href="#"></a>' +
                         (column.title || field) + ': ' + value +'</p></td></tr>';

            if(group.hasSubgroups) {
                for(idx = 0, length = groupItems.length; idx < length; idx++) {
                    html += that._groupRowHtml(groupItems[idx], colspan - 1, level + 1);
                }
            } else {
                html += that._rowsHtml(groupItems);
            }

            return html;
        },

        /**
         * Collapses specified group.
         * @param {Selector|DOMElement} group Target group item to collapse.
         * @example
         * // collapses first group item
         * grid.collapseGroup(grid.tbody.find(">tr.k-grouping-row:first"));
         */
        collapseGroup: function(group) {
            group = $(group).find(".k-icon").addClass("k-expand").removeClass("k-collapse").end();
            var level = group.find(".k-group-cell").length;

            group.nextUntil(function() {
                return $(".k-group-cell", this).length <= level;
            }).hide();
        },

        /**
         * Expands specified group.
         * @param {Selector|DOMElement} group Target group item to expand.
         * @example
         * // expands first group item
         * grid.expandGroup(grid.tbody.find(">tr.k-grouping-row:first"));
         */
        expandGroup: function(group) {
            group = $(group).find(".k-icon").addClass("k-collapse").removeClass("k-expand").end();
            var that = this,
                level = group.find(".k-group-cell").length;

            group.nextAll("tr").each(function () {
                var tr = $(this);
                var offset = tr.find(".k-group-cell").length;
                if (offset <= level)
                    return false;

                if (offset == level + 1) {
                    tr.show();

                    if (tr.hasClass("k-grouping-row") && tr.find(".k-icon").hasClass("k-collapse"))
                        that.expandGroup(tr);
                }
            });
        },

        _updateHeader: function(groups) {
            var that = this,
                cells = that.thead.find("th.k-group-cell"),
                length = cells.length;

            if(groups > length) {
                $(new Array(groups - length + 1).join('<th class="k-group-cell k-header">&nbsp;</th>')).prependTo(that.thead.find("tr"));
            } else if(groups < length) {
                length = length - groups;
                $($.grep(cells, function(item, index) { return length > index } )).remove();
            }
        },

        _firstDataItem: function(data, grouped) {
            if(data && grouped) {
                if(data.hasSubgroups) {
                    data = this._firstDataItem(data.items[0], grouped);
                } else {
                    data = data.items[0];
                }
            }
            return data;
        },

        _progress: function(toggle) {
            var that = this,
                element = that.element.is("table") ? that.element.parent() : (that.content && that.content.length ? that.content : that.element);

            kendo.ui.progress(element, toggle);
        },

        /**
         * Reloads the data and repaints the grid.
         * @example
         * var grid = $("#grid").data("kendoGrid");
         *
         * // refreshes the grid
         * grid.refresh();
         */
        refresh: function() {
            var that = this,
                length,
                idx,
                html = "",
                data = that.dataSource.view(),
                tbody,
                placeholder,
                groups = (that.dataSource.group() || []).length,
                colspan = groups + that.columns.length;

            that._distroyEditable();

            that._progress(false);

            that._data = [];

            if (!that.columns.length) {
                that._autoColumns(that._firstDataItem(data[0], groups));
                colspan = groups + that.columns.length;
            }

            that._group = groups > 0 || that._group;

            if(that._group) {
                that._templates();
                that._updateCols();
                that._updateHeader(groups);
                that._group = groups > 0;
            }

            if(groups > 0) {

                if (that.detailTemplate) {
                    colspan++;
                }

                for (idx = 0, length = data.length; idx < length; idx++) {
                    html += that._groupRowHtml(data[idx], colspan, 0);
                }
            } else {
                html += that._rowsHtml(data);
            }

            if (tbodySupportsInnerHtml) {
                that.tbody[0].innerHTML = html;
            } else {
                placeholder = document.createElement("div");
                placeholder.innerHTML = "<table><tbody>" + html + "</tbody></table>";
                tbody = placeholder.firstChild.firstChild;
                that.table[0].replaceChild(tbody, that.tbody[0]);
                that.tbody = $(tbody);
            }
            that.trigger(DATABOUND);
       }
   });

   ui.plugin(Grid);
   ui.plugin(VirtualScrollable);
})(jQuery);
(function($, undefined) {
    /**
    * @name kendo.ui.NumericTextBox.Description
    *
    * @section
    * <p>
    *    The NumericTextBox widget can convert an INPUT element into a numeric, percentage or currency textbox.
    *    The type is defined depending on the specified format. The widget renders spin buttons and with their help you can
    *    increment/decrement the value with a predefined step. The NumericTextBox widget accepts only numeric entries.
    *    The widget uses <em>kendo.culture.current</em> culture in order to determine number precision and other culture
    *    specific properties.
    * </p>
    *
    * <h3>Getting Started</h3>
    *
    * @exampleTitle Creating a NumericTextBox from existing INPUT element
    * @example
    * <!-- HTML -->
    * <input id="textbox" />
    *
    * @exampleTitle NumericTextBox initialization
    * @example
    *   $(document).ready(function(){
    *      $("#textbox").kendoNumericTextBox();
    *   });
    * @section
    *  <p>
    *      When a NumericTextBox is initialized, it will automatically wraps the input element with SPAN
    *      element and will render spin buttons.
    *  </p>
    *  <h3>Configuring NumericTextBox behaviors</h3>
    *  <p>
    *      NumericTextBox provides configuration options that can be easily set during initialization.
    *      Among the properties that can be controlled:
    *  </p>
    *  <ul>
    *      <li>Value of the NumericTextBox</li>
    *      <li>Min/Max values</li>
    *      <li>Increment step</li>
    *      <li>Precision of the number</li>
    *      <li>Number format. Any valid number format is allowed.</li>
    *  </ul>
    *  <p>
    *      To see a full list of available properties and values, review the Slider Configuration API documentation tab.
    *  </p>
    * @exampleTitle Customizing NumericTextBox defaults
    * @example
    *  $("#textbox").kendoNumericTextBox({
    *      value: 10,
    *      min: -10,
    *      max: 100,
    *      step: 0.75,
    *      format: "n",
    *      decimals: 3
    *  });
    * @section
    * @exampleTitle Create Currency NumericTextBox widget
    * @example
    *  $("#textbox").kendoNumericTextBox({
    *      format: "c2" //Define currency type and 2 digits precision
    *  });
    * @section
    * @exampleTitle Create Percentage NumericTextBox widget
    * @example
    *  $("#textbox").kendoNumericTextBox({
    *      format: "p",
    *      value: 0.15 // 15 %
    *  });
    */

    var kendo = window.kendo,
        keys = kendo.keys,
        ui = kendo.ui,
        Widget = ui.Widget,
        parse = kendo.parseFloat,
        touch = kendo.support.touch,
        CHANGE = "change",
        DISABLED = "disabled",
        INPUT = "k-input",
        TOUCHEND = "touchend",
        MOUSEDOWN = touch ? "touchstart" : "mousedown",
        MOUSEUP = touch ? "touchmove " + TOUCHEND : "mouseup mouseleave",
        HIDE = "k-hide-text",
        DEFAULT = "k-state-default",
        FOCUSED = "k-state-focused",
        HOVER = "k-state-hover",
        HOVEREVENTS = "mouseenter mouseleave",
        POINT = ".",
        SELECTED = "k-state-selected",
        STATEDISABLED = "k-state-disabled",
        NULL = null,
        proxy = $.proxy,
        decimals = {
            190 : ".",
            188 : ","
        };

    var NumericTextBox = Widget.extend(/** @lends kendo.ui.NumericTextBox.prototype */{
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options
         * @option {Number} [value] <null> Specifies the value of the NumericTextBox widget.
         * @option {Number} [min] <null> Specifies the smallest value, which user can enter.
         * @option {Number} [max] <null> Specifies the biggest value, which user can enter.
         * @option {Number} [decimals] <null> Specifies the number precision. If not set precision defined by current culture is used.
         * @option {String} [format] <n> Specifies the format of the number. Any valid number format is allowed.
         * @option {String} [placeholder] <Enter value> Specifies the text displayed when the input is empty.
         * @option {String} [upArrowText] <Increase value> Specifies the title of the up arrow.
         * @option {String} [downArrowText] <Decrease value> Specifies the title of the down arrow.
         */
         init: function(element, options) {
             var that = this,
             isStep = options && options[step] !== undefined,
             min, max, step, value, format;

             Widget.fn.init.call(that, element, options);

             options = that.options;
             element = that.element.addClass(INPUT)
                           .bind({
                               keydown: proxy(that._keydown, that),
                               paste: proxy(that._paste, that),
                               blur: proxy(that._focusout, that)
                           });

             element.closest("form")
                    .bind("reset", function() {
                        setTimeout(function() {
                            that.value(element[0].value);
                        });
                    });

             that._wrapper();
             that._arrows();
             that._input();

             /**
             * Fires when the value is changed
             * @name kendo.ui.NumericTextBox#change
             * @event
             * @param {Event} e
             */
             that.bind(CHANGE, options);

             that._text.focus(proxy(that._click, that));

             min = parse(element.attr("min"));
             max = parse(element.attr("max"));
             step = parse(element.attr("step"));

             if (options.min === NULL && min !== NULL) {
                 options.min = min;
             }

             if (options.max === NULL && max !== NULL) {
                 options.max = max;
             }

             if (!isStep && step !== NULL) {
                 options.step = step;
             }

             format = options.format;
             if (format.slice(0,3) === "{0:") {
                 options.format = format.slice(3, format.length - 1);
             }

             value = options.value;
             that.value(value !== NULL ? value : element.val());

             that.enable(!element.is('[disabled]'));
         },

        options: {
            name: "NumericTextBox",
            min: NULL,
            max: NULL,
            value: NULL,
            step: 1,
            format: "n",
            upArrowText: "Increase value",
            downArrowText: "Decrease value"
        },

        /**
        * Enable/Disable the numerictextbox widget.
        * @param {Boolean} enable The argument, which defines whether to enable/disable tha numerictextbox.
        * @example
        * var textbox = $("#textbox").data("kendoNumericTextBox");
        *
        * // disables the numerictextbox
        * numerictextbox.enable(false);
        *
        * // enables the numerictextbox
        * numerictextbox.enable(true);
        */
        enable: function(enable) {
            var that = this,
                text = that._text,
                element = that.element;
                wrapper = that._inputWrapper,
                upArrow = that._upArrow,
                downArrow = that._downArrow;

            upArrow.unbind(MOUSEDOWN);
            downArrow.unbind(MOUSEDOWN);

            that._toggleText(true);

            if (enable === false) {
                wrapper
                    .removeClass(DEFAULT)
                    .addClass(STATEDISABLED)
                    .unbind(HOVEREVENTS);

                text.add(element).attr(DISABLED, DISABLED);
            } else {
                wrapper
                    .addClass(DEFAULT)
                    .removeClass(STATEDISABLED)
                    .bind(HOVEREVENTS, that._toggleHover);

                text.add(element).removeAttr(DISABLED);

                upArrow.bind(MOUSEDOWN, function(e) {
                    e.preventDefault();
                    that._spin(1);
                    that._upArrow.addClass(SELECTED);
                });

                downArrow.bind(MOUSEDOWN, function(e) {
                    e.preventDefault();
                    that._spin(-1);
                    that._downArrow.addClass(SELECTED);
                });
            }
        },

        /**
        * Gets/Sets the value of the numerictextbox.
        * @param {Number|String} value The value to set.
        * @returns {Number} The value of the numerictextbox.
        * @example
        * var numerictextbox = $("#textbox").data("kendoNumericTextBox");
        *
        * // get the value of the numerictextbox.
        * var value = numerictextbox.value();
        *
        * // set the value of the numerictextbox.
        * numerictextbox.value("10.20");
        */
        value: function(value) {
            var that = this;

            if (value === undefined) {
                return that._value;
            }

            that._update(value);
            that._old = that._value;
        },

        _adjust: function(value) {
            var that = this,
            options = that.options,
            min = options.min,
            max = options.max;

            if (min !== NULL && value < min) {
                value = min;
            } else if (max !== NULL && value > max) {
                value = max;
            }

            return value;
        },

        _arrows: function() {
            var that = this,
            arrows,
            options = that.options,
            element = that.element;

            arrows = element.siblings(".k-icon");

            if (!arrows[0]) {
                arrows = $(buttonHtml("up", options.upArrowText) + buttonHtml("down", options.downArrowText))
                        .insertAfter(element);

                arrows.wrapAll('<span class="k-select"/>');
            }

            arrows.bind(MOUSEUP, function(e) {
                if (!touch || kendo.eventTarget(e) != e.currentTarget || e.type === TOUCHEND) {
                    clearTimeout( that._spinning );
                }
                arrows.removeClass(SELECTED);
            });

            that._upArrow = arrows.eq(0);
            that._downArrow = arrows.eq(1);
        },

        _blur: function() {
            var that = this;

            that._toggleText(true);
            that._change(that.element.val());
        },

        _click: function(e) {
            var that = this;

            clearTimeout(that._focusing);
            that._focusing = setTimeout(function() {
                var input = e.target,
                    idx = caret(input),
                    value = input.value.substring(0, idx),
                    format = that._format(that.options.format),
                    group = format[","],
                    groupRegExp = new RegExp("\\" + group, "g"),
                    extractRegExp = new RegExp("([\\d\\" + group + "]+)(\\" + format[POINT] + ")?(\\d+)?"),
                    result = extractRegExp.exec(value),
                    caretPosition = 0;

                if (result) {
                    caretPosition = result[0].replace(groupRegExp, "").length;

                    if (value.indexOf("(") != -1 && that._value < 0) {
                        caretPosition++;
                    }
                }

                that._focusin();

                caret(that.element[0], caretPosition);
            });
        },

        _change: function(value) {
            var that = this;

            that._update(value);
            value = that._value;

            if (that._old != value) {
                that._old = value;
                that.trigger(CHANGE);

                // trigger the DOM change event so any subscriber gets notified
                that.element.trigger(CHANGE);
            }
        },

        _focusin: function() {
            var that = this;
            clearTimeout(that._bluring);
            that._toggleText(false);
            that.element.focus();
            that._inputWrapper.addClass(FOCUSED);
        },

        _focusout: function() {
            var that = this;
            that._bluring = setTimeout(function() {
                that._inputWrapper.removeClass(FOCUSED);
                that._blur();
            }, 100);
        },

        _format: function(format) {
            var that = this,
                options = that.options,
                numberFormat = kendo.culture().numberFormat;


            if (format.indexOf("c") > -1) {
                numberFormat = numberFormat.currency;
            } else if (format.indexOf("p") > -1) {
                numberFormat = numberFormat.percent;
            }

            return numberFormat;
        },

        _input: function() {
            var that = this,
                CLASSNAME = "k-formatted-value",
                element = that.element.show()[0],
                wrapper = that.wrapper,
                text;


            text = wrapper.find(POINT + CLASSNAME);

            if (!text[0]) {
                text = $("<input />").insertBefore(element).addClass(CLASSNAME);
            }

            element.type = "text";
            text[0].type = "text";

            text[0].style.cssText = element.style.cssText;
            that._text = text.attr("readonly", true).addClass(element.className);
        },

        _keydown: function(e) {
            var that = this,
                key = e.keyCode;

            if (key == keys.DOWN) {
                that._step(-1);
            } else if (key == keys.UP) {
                that._step(1);
            } else if (key == keys.ENTER) {
                that._change(that.element.val());
            }

            if (that._prevent(key) && !e.ctrlKey) {
                e.preventDefault();
            }
        },

        _paste: function(e) {
            var that = this,
                element = e.target,
                value = element.value;
            setTimeout(function() {
                if (parse(element.value) === NULL) {
                    that._update(value);
                }
            });
        },

        _prevent: function(key) {
            var that = this,
                prevent = true,
                min = that.options.min,
                element = that.element[0],
                value = element.value,
                separator = that._format(that.options.format)[POINT],
                idx = caret(element),
                end;

            if ((key > 16 && key < 21)
             || (key > 32 && key < 37)
             || (key > 47 && key < 58)
             || (key > 95 && key < 106)
             || key == 45 /* INSERT */
             || key == 46 /* DELETE */
             || key == keys.LEFT
             || key == keys.RIGHT
             || key == keys.TAB
             || key == keys.BACKSPACE
             || key == keys.ENTER) {
                prevent = false;
            } else if (decimals[key] === separator && value.indexOf(separator) == -1) {
                prevent = false;
            } else if ((min === NULL || min < 0) && value.indexOf("-") == -1 && (key == 189 || key == 109) && idx == 0) { //sign
                prevent = false;
            } else if (key == 110 && value.indexOf(separator) == -1) {
                end = value.substring(idx);

                element.value = value.substring(0, idx) + separator + end;
            }

            return prevent;
        },

        _spin: function(step, timeout) {
            var that = this;

            timeout = timeout || 500;

            clearTimeout( that._spinning );
            that._spinning = setTimeout(function() {
                that._spin(step, 50);
            }, timeout );

            that._step(step);
        },

        _step: function(step) {
            var that = this,
                element = that.element,
                value = parse(element.val()) || 0;

            if (document.activeElement != element[0]) {
                that._focusin();
            }

            value += that.options.step * parse(step);

            that._update(that._adjust(value));
        },

        _toggleHover: function(e) {
            if (!touch) {
                $(e.currentTarget).toggleClass(HOVER, e.type === "mouseenter");
            }
        },

        _toggleText: function(toggle) {
            var that = this;

            toggle = !!toggle;
            that._text.toggle(toggle);
            that.element.toggle(!toggle);
        },

        _update: function(value) {
            var that = this,
                options = that.options,
                format = options.format,
                decimals = options.decimals,
                numberFormat = that._format(format),
                isNotNull;

            if (decimals === undefined) {
                decimals = numberFormat.decimals;
            }

            value = parse(value);

            isNotNull = value !== NULL;

            if (isNotNull) {
                value = parseFloat(value.toFixed(decimals));
            }

            that._value = value = that._adjust(value);
            that._text.val(isNotNull ? kendo.toString(value, format) : options.placeholder);
            that.element.val(isNotNull ? value.toString().replace(POINT, numberFormat[POINT]) : "");
        },

        _wrapper: function() {
            var that = this,
                element = that.element,
                wrapper;

            wrapper = element.parent();

            if (!wrapper.is("span.k-widget")) {
                wrapper = element.hide().wrap('<span class="k-numeric-wrap k-state-default" />').parent();
                wrapper = wrapper.wrap("<span/>").parent();
            }

            wrapper[0].style.cssText = element[0].style.cssText;
            that.wrapper = wrapper.addClass("k-widget k-numerictextbox").show();
            that._inputWrapper = $(wrapper[0].firstChild);
        }
    });

    function buttonHtml(className, text) {
        return '<span unselectable="on" class="k-link"><span class="k-icon k-arrow-' + className + '" title="' + text + '">' + text + '</span></span>'
    }

    function caret(element, position) {
        var range,
            isPosition = position !== undefined;

        if (document.selection) {
            element.focus();
            var range = document.selection.createRange();
            if (isPosition) {
                range.move("character", position);
                range.select();
            } else {
                var rangeElement = element.createTextRange(),
                    rangeDuplicated = rangeElement.duplicate();
                    rangeElement.moveToBookmark(range.getBookmark());
                    rangeDuplicated.setEndPoint('EndToStart', rangeElement);

                position = rangeDuplicated.text.length;

            }
        } else if (element.selectionStart !== undefined) {
            if (isPosition) {
                element.focus();
                element.setSelectionRange(position, position);
            } else {
                position = element.selectionStart;
            }
        }

        return position;
    }

    ui.plugin(NumericTextBox);
})(jQuery);
(function ($, undefined) {
    /**
     * @name kendo.ui.Menu.Description
     *
     * @section
     *  <p>
     *      The Menu widget displays hierarchical data as a multi-level menu. Menus provide
     *      rich styling for unordered lists of items, and can be used for both navigation and
     *      executing JavaScript commands. Items can be defined and initialized from HTML, or
     *      the rich Menu API can be used to add and remove items.
     *  </p>
     *
     *  <h3>Getting Started</h3>
     * @exampleTitle Create a simple HTML hierarchical list of items
     * @example
     * <ul id="menu">
     *     <li>Item 1
     *         <ul>
     *             <li>Item 1.1</li>
     *             <li>Item 1.2</li>
     *         </ul>
     *     </li>
     *     <li>Item 2</li>
     * </ul>
     *
     * @exampleTitle Initialize Kendo Menu using jQuery selector
     * @example
     * var menu = $("#menu").kendoMenu();
     *
     * @section
     *  <h3>Customizing Menu Animations</h3>
     *  <p>
     *      By default, the Menu uses a slide animation to expand and reveal sub-items as the
     *      mouse hovers. Animations can be easily customized using configuration properties, changing
     *      the animation style and delay. Menu items can also be configured to open on click instead of on hover.
     *  </p>
     *
     * @exampleTitle Changing Menu animation and open behavior
     * @example
     * $("#menu").kendoMenu({
     *      animation: {
     *        open : {effects: fadeIn},
     *        hoverDelay: 500
     *      },
     *      openOnClick: true
     *  });
     *
     *  @section
     *   <h3>Dynamically configuring Menu items</h3>
     *   <p>
     *          The Menu API provides several methods for dynamically adding or removing Items.
     *          To add items, provide the new item as a JSON object along with a reference item that
     *          will be used to determine the placement in the hierarchy.
     *  </p>
     *  <br/>
     *  <p>
     *          A reference item is simply a target Menu Item HTML element that already exists in
     *          the Menu. Any valid jQuery selector can be used to obtain a reference to the target
     *          item. For examples, see the <a href="../menu/api.html" title="Menu API demos">Menu API demos</a>.
     *          Removing an item only requires a reference to the target element that should be removed.
     *  </p>
     * @exampleTitle Dynamically add a new root Menu item
     * @example
     *  var menu = $("#menu").kendoMenu().data("kendoMenu");
     *
     *  menu.insertAfter(
     *      { text: "New Menu Item" },
     *      menu.element.children("li:last")
     *  );
     *
     */
    var kendo = window.kendo,
        ui = kendo.ui,
        touch = kendo.support.touch,
        extend = $.extend,
        proxy = $.proxy,
        each = $.each,
        template = kendo.template,
        Widget = ui.Widget,
        excludedNodesRegExp = /^(ul|a|div)$/i,
        IMG = "img",
        OPEN = "open",
        MENU = "k-menu",
        LINK = "k-link",
        LAST = "k-last",
        CLOSE = "close",
        CLICK = "click",
        TIMER = "timer",
        FIRST = "k-first",
        IMAGE = "k-image",
        EMPTY = ":empty",
        SELECT = "select",
        ZINDEX = "zIndex",
        MOUSEENTER = "mouseenter",
        MOUSELEAVE = "mouseleave",
        KENDOPOPUP = "kendoPopup",
        SLIDEINRIGHT = "slideIn:right",
        DEFAULTSTATE = "k-state-default",
        DISABLEDSTATE = "k-state-disabled",
        disabledSelector = ".k-item.k-state-disabled",
        itemSelector = ".k-item:not(.k-state-disabled)",
        linkSelector = ".k-item:not(.k-state-disabled) > .k-link",

        templates = {
            group: template(
                "<ul class='#= groupCssClass(group) #'#= groupAttributes(group) #>" +
                    "#= renderItems(data) #" +
                "</ul>"
            ),
            itemWrapper: template(
                "<#= tag(item) # class='#= textClass(item) #'#= textAttributes(item) #>" +
                    "#= image(item) ##= sprite(item) ##= text(item) #" +
                    "#= arrow(data) #" +
                "</#= tag(item) #>"
            ),
            item: template(
                "<li class='#= wrapperCssClass(group, item) #'>" +
                    "#= itemWrapper(data) #" +
                    "# if (item.items) { #" +
                    "#= subGroup({ items: item.items, menu: menu, group: { expanded: item.expanded } }) #" +
                    "# } #" +
                "</li>"
            ),
            image: template("<img class='k-image' alt='' src='#= imageUrl #' />"),
            arrow: template("<span class='#= arrowClass(item, group) #'></span>"),
            sprite: template("<span class='k-sprite #= spriteCssClass #'></span>"),
            empty: template("")
        },

        rendering = {
            /** @ignore */
            wrapperCssClass: function (group, item) {
                var result = "k-item",
                    index = item.index;

                if (item.enabled === false) {
                    result += " k-state-disabled";
                } else {
                    result += " k-state-default";
                }

                if (group.firstLevel && index == 0) {
                    result += " k-first"
                }

                if (index == group.length-1) {
                    result += " k-last";
                }

                return result;
            },
            /** @ignore */
            textClass: function(item) {
                return LINK;
            },
            /** @ignore */
            textAttributes: function(item) {
                return item.url ? " href='" + item.url + "'" : "";
            },
            /** @ignore */
            arrowClass: function(item, group) {
                var result = "k-icon";

                if (group.horizontal) {
                    result += " k-arrow-down";
                } else {
                    result += " k-arrow-right";
                }

                return result;
            },
            /** @ignore */
            text: function(item) {
                return item.encoded === false ? item.text : kendo.htmlEncode(item.text);
            },
            /** @ignore */
            tag: function(item) {
                return item.url ? "a" : "span";
            },
            /** @ignore */
            groupAttributes: function(group) {
                return group.expanded !== true ? " style='display:none'" : "";
            },
            /** @ignore */
            groupCssClass: function(group) {
                return "k-group";
            }
        };

    function getEffectOptions(item) {
        var parent = item.parent();
        return {
            effects: parent.hasClass(MENU) ? parent.hasClass(MENU + "-vertical") ? SLIDEINRIGHT : "slideIn:down" : SLIDEINRIGHT
        };
    }

    function contains(parent, child) {
        try {
            return $.contains(parent, child);
        } catch (e) {
            return false;
        }
    }

    function updateItemClasses (item) {
        item = $(item);

        item
            .children(IMG)
            .addClass(IMAGE);
        item
            .children("a")
            .addClass(LINK)
            .children(IMG)
            .addClass(IMAGE);
        item
            .filter(":not([disabled])")
            .addClass(DEFAULTSTATE);
        item
            .filter(".k-separator:empty")
            .append("&nbsp;");
        item
            .filter("li[disabled]")
            .addClass(DISABLEDSTATE)
            .removeAttr("disabled");
        item
            .children("a:focus")
            .parent()
            .addClass("k-state-active");

        if (!item.children("." + LINK).length) {
            item
                .contents()      // exclude groups, real links, templates and empty text nodes
                .filter(function() { return (!this.nodeName.match(excludedNodesRegExp) && !(this.nodeType == 3 && !$.trim(this.nodeValue))); })
                .wrapAll("<span class='" + LINK + "'/>");
        }

        updateArrow(item);
        updateFirstLast(item);
    }

    function updateArrow (item) {
        item = $(item);

        item.find(".k-icon").remove();

        item.filter(":has(.k-group)")
            .children(".k-link:not(:has([class*=k-arrow]))")
            .each(function () {
                var item = $(this),
                    parent = item.parent().parent();

                item.append("<span class='k-icon " + (parent.hasClass(MENU + "-horizontal") ? "k-arrow-down" : "k-arrow-next") + "'/>");
            });
    }

    function updateFirstLast (item) {
        item = $(item);

        item.filter(".k-first:not(:first-child)").removeClass(FIRST);
        item.filter(".k-last:not(:last-child)").removeClass(LAST);
        item.filter(":first-child").addClass(FIRST);
        item.filter(":last-child").addClass(LAST);
    }

    var Menu = Widget.extend({/** @lends kendo.ui.Menu.prototype */
        /**
         * Creates a Menu instance.
         * @constructs
         * @extends kendo.ui.Widget
         * @class Menu UI widget
         * @param {Selector} element DOM element
         * @param {Object} options Configuration options.
         * @option {Object} [animation] A collection of <b>Animation</b> objects, used to change default animations. A value of false will disable all animations in the widget.
         * @option {Animation} [animation.open] The animation that will be used when opening sub menus.
         * @option {Animation} [animation.close] The animation that will be used when closing sub menus.
         * @option {String} [orientation] <"horizontal"> Root menu orientation.
         * @option {Boolean} [openOnClick] <false> Specifies that the root sub menus will be opened on item click.
         * @option {Number} [hoverDelay] <100> Specifies the delay in ms before the menu is opened/closed - used to avoid accidental closure on leaving.
         */
        init: function(element, options) {
            element = $(element);
            var that = this;

            Widget.fn.init.call(that, element, options);

            options = that.options;

            if (that.element.is(EMPTY)) {
                that.element.append($(Menu.renderGroup({
                    items: options.dataSource,
                    group: {
                        firstLevel: true,
                        horizontal: that.element.hasClass(MENU + "-horizontal"),
                        expanded: true
                    },
                    menu: {}
                })).children());
            }

            that._updateClasses();

            if (options.animation === false) {
                options.animation = { open: { show: true, effects: {} }, close: { hide:true, effects: {} } };
            }

            that.nextItemZIndex = 100;

            element.delegate(disabledSelector, CLICK, false);

            element.delegate(itemSelector, MOUSEENTER, proxy(that._mouseenter, that))
                   .delegate(itemSelector, MOUSELEAVE, proxy(that._mouseleave, that))
                   .delegate(itemSelector, CLICK, proxy(that._click , that));

            element.delegate(linkSelector, MOUSEENTER + " " + MOUSELEAVE, that._toggleHover);

            $(document).click(proxy( that._documentClick, that ));
            that.clicked = false;

            that.bind([
                /**
                 * Fires before a sub menu gets opened.
                 * @name kendo.ui.Menu#open
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The opened item
                 */
                OPEN,
                /**
                 * Fires after a sub menu gets closed.
                 * @name kendo.ui.Menu#close
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The closed item
                 */
                CLOSE,
                /**
                 * Fires when a menu item gets selected.
                 * @name kendo.ui.Menu#select
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The selected item
                 */
                SELECT
            ], that.options);
        },
        options: {
            name: "Menu",
            animation: {
                open: {
                    duration: 200,
                    show: true
                },
                close: { // if close animation effects are defined, they will be used instead of open.reverse
                    duration: 100,
                    show: false,
                    hide: true
                }
            },
            orientation: "horizontal",
            openOnClick: false,
            hoverDelay: 100
        },

        /**
         * Enables/disables a Menu item
         * @param {Selector} element Target element
         * @param {Boolean} enable Desired state
         */
        enable: function (element, enable) {
            this._toggleDisabled(element, enable !== false);
        },

        /**
         * Disables a Menu item
         * @param {Selector} element Target element
         */
        disable: function (element) {
            this._toggleDisabled(element, false);
        },

        /**
         * Appends a Menu item in the specified referenceItem's sub menu
         * @param {Selector} item Target item, specified as a JSON object. Can also handle an array of such objects.
         * @param {Item} referenceItem A reference item to append the new item in
         * @example
         * menu.append(
         *     [{
         *         text: "Item 1"
         *     },
         *     {
         *         text: "Item 2"
         *     }],
         *     referenceItem
         * );
         */
        append: function (item, referenceItem) {
            referenceItem = $(referenceItem);

            var inserted = this._insert(item, referenceItem, referenceItem.length ? referenceItem.find("> .k-group, .k-animation-container > .k-group") : null);

            each(inserted.items, function () {
                inserted.group.append(this);
                updateFirstLast(this);
            });

            updateArrow(referenceItem);
            updateFirstLast(inserted.group.find(".k-first, .k-last"));
        },

        /**
         * Inserts a Menu item before the specified referenceItem
         * @param {Selector} item Target item, specified as a JSON object. Can also handle an array of such objects.
         * @param {Selector} referenceItem A reference item to insert the new item before
         * @example
         * menu.insertBefore(
         *     [{
         *         text: "Item 1"
         *     },
         *     {
         *         text: "Item 2"
         *     }],
         *     referenceItem
         * );
         */
        insertBefore: function (item, referenceItem) {
            referenceItem = $(referenceItem);

            var inserted = this._insert(item, referenceItem, referenceItem.parent());

            each(inserted.items, function () {
                referenceItem.before(this);
                updateFirstLast(this);
            });

            updateFirstLast(referenceItem);
        },

        /**
         * Inserts a Menu item after the specified referenceItem
         * @param {Selector} item Target item, specified as a JSON object. Can also handle an array of such objects.
         * @param {Selector} referenceItem A reference item to insert the new item after
         * @example
         * menu.insertAfter(
         *     [{
         *         text: "Item 1"
         *     },
         *     {
         *         text: "Item 2"
         *     }],
         *     referenceItem
         * );
         */
        insertAfter: function (item, referenceItem) {
            referenceItem = $(referenceItem);

            var inserted = this._insert(item, referenceItem, referenceItem.parent());

            each(inserted.items, function () {
                referenceItem.after(this);
                updateFirstLast(this);
            });

            updateFirstLast(referenceItem);
        },

        _insert: function (item, referenceItem, parent) {
            var that = this;

            if (!referenceItem || !referenceItem.length) {
                parent = that.element;
            }

            var plain = $.isPlainObject(item),
                items,
                groupData = {
                    firstLevel: parent.hasClass(MENU),
                    horizontal: parent.hasClass(MENU + "-horizontal"),
                    expanded: true,
                    length: parent.children().length
                };

            if (referenceItem && !parent.length) {
                parent = $(Menu.renderGroup({ group: groupData })).appendTo(referenceItem);
            }

            if (plain || $.isArray(item)) { // is JSON
                items = $.map(plain ? [ item ] : item, function (value, idx) {
                            return $(Menu.renderItem({
                                group: groupData,
                                item: extend(value, { index: idx })
                            }));
                        });
            } else {
                items = $(item);

                updateItemClasses(items);
            }

            return { items: items, group: parent };
        },

        /**
         * Removes the specified Menu item/s from the Menu
         * @param {Selector} element Target item selector.
         * @example
         * menu.remove("#Item1");
         */
        remove: function (element) {
            element = $(element);

            var that = this,
                parent = element.parentsUntil(that.element, ".k-item"),
                group = element.parent("ul");

            element.remove();

            if (group && !group.children(".k-item").length) {
                var container = group.parent(".k-animation-container");
                container.length ? container.remove() : group.remove();
            }

            if (parent.length) {
                parent = parent.eq(0);

                updateArrow(parent);
                updateFirstLast(parent);
            }
        },

        /**
         * Opens the sub menu of the specified Menu item/s
         * @param {Selector} element Target item selector.
         * @example
         * menu.open("#Item1");
         */
        open: function (element) {
            var that = this;

            $(element).each(function () {
                var li = $(this);

                clearTimeout(li.data(TIMER));

                li.data(TIMER, setTimeout(function () {
                    var ul = li.find(".k-group:first:hidden"), popup;

                    if (ul[0]) {
                        li.data(ZINDEX, li.css(ZINDEX));
                        li.css(ZINDEX, that.nextItemZIndex ++);

                        popup = ul.data(KENDOPOPUP);
                        var parentHorizontal = li.parent().hasClass(MENU + "-horizontal");

                        if (!popup) {
                            popup = ul.kendoPopup({
                                origin: parentHorizontal ? "bottom left" : "top right",
                                position: "top left",
                                collision: parentHorizontal ? "fit" : "fit flip",
                                anchor: li,
                                appendTo: li,
                                animation: {
                                    open: extend( getEffectOptions(li), that.options.animation.open),
                                    close: that.options.animation.close
                                }
                            }).data(KENDOPOPUP);
                        }

                        popup.open();
                    }

                }, that.options.hoverDelay));
            });
        },

        /**
         * Closes the sub menu of the specified Menu item/s
         * @param {Selector} element Target item selector.
         * @example
         * menu.close("#Item1");
         */
        close: function (element) {
            var that = this;

            $(element).each(function () {
                var li = $(this);

                clearTimeout(li.data(TIMER));

                li.data(TIMER, setTimeout(function () {
                    var ul = li.find(".k-group:first:visible"), popup;
                    if (ul[0]) {
                        li.css(ZINDEX, li.data(ZINDEX));
                        li.removeData(ZINDEX);

                        popup = ul.data(KENDOPOPUP);
                        popup.close();
                    }
                }, that.options.hoverDelay));
            });
        },

        _toggleDisabled: function (element, enable) {
            $(element).each(function () {
                $(this)
                    .toggleClass(DEFAULTSTATE, enable)
                    .toggleClass(DISABLEDSTATE, !enable);
            });
        },

        _toggleHover: function(e) {
            var target = $(e.currentTarget);

            if (!target.parents("li." + DISABLEDSTATE).length) {
                target.toggleClass("k-state-hover", e.type == MOUSEENTER);
            }
        },

        _updateClasses: function() {
            var that = this;

            that.element.addClass("k-widget k-reset k-header " + MENU).addClass(MENU + "-" + that.options.orientation);

            var items = that.element
                            .find("ul")
                            .addClass("k-group")
                            .end()
                            .find("li")
                            .addClass("k-item");

            items.each(function () {
                updateItemClasses(this);
            });
        },

        _mouseenter: function (e) {
            var that = this,
                element = $(e.currentTarget),
                hasChildren = (element.children(".k-animation-container").length || element.children(".k-group").length);

            if (!that.options.openOnClick || that.clicked) {
                if (!contains(e.currentTarget, e.relatedTarget) && hasChildren) {
                    if (that.trigger(OPEN, { item: element[0] }) === false) {
                        that.open(element);
                    }
                }
            }

            if (that.options.openOnClick && that.clicked) {
                that.trigger(CLOSE, { item: element[0] });

                element.siblings().each(proxy(function (_, sibling) {
                    that.close(sibling);
                }, that));
            }
        },

        _mouseleave: function (e) {
            var that = this,
                element = $(e.currentTarget),
                hasChildren = (element.children(".k-animation-container").length || element.children(".k-group").length);

            if (!that.options.openOnClick && !contains(e.currentTarget, e.relatedTarget) && hasChildren) {
                if (that.trigger(CLOSE, { item: element[0] }) === false) {
                    that.close(element);
                }
            }
        },

        _click: function (e) {
            var that = this, openHandle;

            var element = $(e.currentTarget);

            if (element.hasClass(DISABLEDSTATE)) {
                e.preventDefault();
                return;
            }

            if (!e.handled) // We shouldn't stop propagation.
                that.trigger(SELECT, { item: element[0] });

            e.handled = true;

            if (!element.parent().hasClass(MENU) || (!that.options.openOnClick && !touch)) {
                return;
            }

            e.preventDefault();

            that.clicked = true;
            openHandle = element.children(".k-animation-container, .k-group").is(":visible") ? CLOSE : OPEN;

            that.trigger(openHandle, { item: element[0] });
            that[openHandle](element);
        },

        _documentClick: function (e) {
            var that = this;

            if (contains(that.element[0], e.target)) {
                return;
            }

            if (that.clicked) {
                that.clicked = false;
                that.close(that.element.find(".k-item>.k-animation-container:visible").parent());
            }
        }
    });

    // client-side rendering
    extend(Menu, {
        renderItem: function (options) {
            options = extend({ menu: {}, group: {} }, options);

            var empty = templates.empty,
                item = options.item,
                menu = options.menu;

            return templates.item(extend(options, {
                image: item.imageUrl ? templates.image : empty,
                sprite: item.spriteCssClass ? templates.sprite : empty,
                itemWrapper: templates.itemWrapper,
                arrow: item.items ? templates.arrow : empty,
                subGroup: Menu.renderGroup
            }, rendering));
        },

        renderGroup: function (options) {
            return templates.group(extend({
                renderItems: function(options) {
                    var html = "",
                        i = 0,
                        items = options.items,
                        len = items ? items.length : 0,
                        group = extend({ length: len }, options.group);

                    for (; i < len; i++) {
                        html += Menu.renderItem(extend(options, {
                            group: group,
                            item: extend({ index: i }, items[i])
                        }));
                    }

                    return html;
                }
            }, options, rendering));
        }
    });

    kendo.ui.plugin(Menu);

})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        ui = kendo.ui,
        Widget = ui.Widget,
        extend = $.extend,
        isFunction = $.isFunction,
        isPlainObject = $.isPlainObject,
        inArray = $.inArray,
        Binder = kendo.data.ModelViewBinder,
        Validator = ui.Validator,
        CHANGE = "change";

    var specialRules = ["url", "email", "number", "date", "boolean"];

    function createAttributes(options) {
        var field = options.model.fields[options.field],
            type = field.type,
            validation = field.validation,
            ruleName,
            DATATYPE = kendo.attr("type"),
            rule,
            attr = {
                name: options.field
            };

        for (ruleName in validation) {
            rule = validation[ruleName];

            if (inArray(ruleName, specialRules) >= 0) {
                attr[DATATYPE] = ruleName;
            } else if (!isFunction(rule)) {
                attr[ruleName] = isPlainObject(rule) ? rule.value || ruleName : rule;
            }

            attr[kendo.attr(ruleName + "-msg")] = rule.message;
        }

        if (inArray(type, specialRules) >= 0) {
            attr[DATATYPE] = type;
        }

        return attr;
    }

    var editors = {
        "number": function(container, options) {
            var attr = createAttributes(options);
            $('<input type="text"/>').attr(attr).appendTo(container).kendoNumericTextBox({ format: options.format });
            $('<span ' + kendo.attr("for") + '="' + options.field + '" class="k-invalid-msg"/>').hide().appendTo(container);
        },
        "date": function(container, options) {
            var attr = createAttributes(options);
            attr[kendo.attr("format")] = options.format;

            $('<input type="text"/>').attr(attr).appendTo(container).kendoDatePicker({ format: options.format });
            $('<span ' + kendo.attr("for") + '="' + options.field + '" class="k-invalid-msg"/>').hide().appendTo(container);
        },
        "string": function(container, options) {
            var attr = createAttributes(options);
            $('<input type="text" class="k-input k-textbox"/>').attr(attr).appendTo(container);
        },
        "boolean": function(container, options) {
            var attr = createAttributes(options);
            $('<input type="checkbox" />').attr(attr).appendTo(container);
        }
    };

    var Editable = Widget.extend({
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            that.bind([CHANGE], that.options);

            that.refresh();
        },

        options: {
            name: "Editable",
            editors: editors
        },

        editor: function(field, modelField) {
            var that = this,
                editors = that.options.editors,
                isObject = isPlainObject(field),
                fieldName = isObject ? field.field : field,
                model = that.options.model || {},
                fieldType = modelField && modelField.type ? modelField.type : "string",
                editor = isObject && field.editor ? field.editor : editors[fieldType];

            editor = editor ? editor : editors["string"];

            if (modelField) {
                editor(that.element, extend(true, {}, isObject ? field : { field: fieldName }, { model: model }));
            }
        },

        _binderChange: function(e) {
            var that = this;
            if (!that.validatable.validate() || that.trigger(CHANGE, { values: e.values })) {
                e.preventDefault();
            }
        },

        end: function() {
            return this.binder.bindModel();
        },

        distroy: function() {
            this.element.removeData("kendoValidator")
                .removeData("kendoEditable");
        },

        refresh: function() {
            var that = this,
                idx,
                length,
                fields = that.options.fields || [],
                container = that.element.empty(),
                model = that.options.model || {},
                rules = {},
                settings = {};

            if (!$.isArray(fields)) {
                fields = [fields];
            }

            for (idx = 0, length = fields.length; idx < length; idx++) {
                var field = fields[idx],
                    isObject = isPlainObject(field),
                    fieldName = isObject ? field.field : field,
                    modelField = (model.fields || {})[fieldName],
                    type = modelField ? modelField.type : null,
                    validation = modelField ? (modelField.validation || {}) : {};

                for (var rule in validation) {
                    if (isFunction(validation[rule])) {
                        rules[rule] = validation[rule];
                    }
                }

                if (isObject && field.format && type == "date") {
                    settings[fieldName] = {
                        format: function(value) { return kendo.format(field.format, value); },
                        parse: function(value) { return kendo.parseDate(value, field.format); }
                    };
                }

                that.editor(field, modelField);
            }

            settings[CHANGE] = $.proxy(that._binderChange, that);
            that.binder = new Binder(container, that.options.model, settings);

            that.validatable = container.kendoValidator({
                errorTemplate: '<div class="k-widget k-tooltip k-tooltip-validation" style="margin:0.5em"><span class="k-icon k-warning"> </span>' +
                                '${message}<div class="k-callout k-callout-n"></div></div>', rules: rules }).data("kendoValidator");

            container.find(":input:visible:first").focus();
        }
   });

   ui.plugin(Editable);
})(jQuery);
(function($, undefined) {
    var kendo = window.kendo,
        ui = kendo.ui,
        DROPDOWNLIST = "kendoDropDownList",
        NUMERICTEXTBOX = "kendoNumericTextBox",
        DATEPICKER = "kendoDatePicker",
        proxy = $.proxy,
        POPUP = "kendoPopup",
        EQ = "Is equal to",
        NEQ = "Is not equal to",
        Widget = ui.Widget;

    var booleanTemplate =
            '<div>' +
                '<input type="hidden" name="filters[0].field" value="#=field#"/>' +
                '<input type="hidden" name="filters[0].operator" value="eq"/>' +
                '<div class="k-filter-help-text">#=messages.info#</div>'+
                '<label>#=messages.isTrue#'+
                    '<input type="radio" name="filters[0].value" value="true"/>' +
                '</label>' +
                '<label>#=messages.isFalse#'+
                    '<input type="radio" name="filters[0].value" value="false"/>' +
                '</label>' +
                '<button type="submit" class="k-button">#=messages.filter#</button>'+
                '<button type="reset" class="k-button">#=messages.clear#</button>'+
            '</div>';

    var defaultTemplate =
            '<div>' +
                '<input type="hidden" name="filters[0].field" value="#=field#"/>' +
                '<input type="hidden" name="filters[1].field" value="#=field#"/>' +
                '<div class="k-filter-help-text">#=messages.info#</div>'+
                '<select name="filters[0].operator">'+
                    '#for(var op in operators){#'+
                        '<option value="#=op#">#=operators[op]#</option>'+
                    '#}#'+
                '</select>'+
                '<input name="filters[0].value" class="k-widget k-input k-autocomplete" type="text" data-#=ns#type="#=type#"/>'+
                '#if(extra){#'+
                    '<select name="logic" class="k-filter-and">'+
                        '<option value="and">And</option>'+
                        '<option value="or">Or</option>'+
                    '</select>'+
                    '<select name="filters[1].operator">'+
                        '#for(var op in operators){#'+
                            '<option value="#=op#">#=operators[op]#</option>'+
                        '#}#'+
                    '</select>'+
                    '<input name="filters[1].value" class="k-widget k-input k-autocomplete" type="text" data-#=ns#type="#=type#"/>'+
                '#}#'+
                '<button type="submit" class="k-button">#=messages.filter#</button>'+
                '<button type="reset" class="k-button">#=messages.clear#</button>'+
            '</div>';

    function removeFiltersForField(expression, field) {
        if (expression.filters) {
            expression.filters = $.grep(expression.filters, function(filter) {
                removeFiltersForField(filter, field);
                return filter.field != field;
            });
        }
    }

    function value(dom, value) {
        var widget = dom.data(DROPDOWNLIST) || dom.data(NUMERICTEXTBOX) || dom.data(DATEPICKER);

        if (widget) {
            widget.value(value);
        } else if (dom.is(":radio")) {
            dom.filter("[value=" + value + "]").attr("checked", "checked");
        } else {
            dom.val(value);
        }
    }

    function toObject(array) {
        var result = {},
            idx,
            length,
            name,
            members,
            member,
            value,
            interimResult,
            previousMember,
            parentResult;

        for (idx = 0, length = array.length; idx < length; idx++) {
            members = array[idx].name.split(/[\.\[\]]+/);

            members = $.grep(members, function(value){ return value });

            value = array[idx].value;

            interimResult = result;

            parentResult = result;

            for (member = 0; member < members.length - 1; member++) {
                name = members[member];

                if (!isNaN(name)) {
                    previousMember = members[member-1];

                    if (!$.isArray(parentResult[previousMember])) {
                        interimResult = parentResult[previousMember] = [];
                    }
                }

                parentResult = interimResult;

                interimResult = interimResult[name] = interimResult[name] || {};
            }

            interimResult[members[member]] = value;
        }

        return result;
    }

    var FilterMenu = Widget.extend({
        init: function(element, options) {
            var that = this,
                type = "string",
                link,
                field,
                getter,
                operators;

            Widget.fn.init.call(that, element, options);

            operators = options.operators || {};
            element = that.element;
            options = that.options;

            link = element.addClass("k-filterable").find("k-grid-filter");

            if (!link[0]) {
                link = element.prepend('<a class="k-grid-filter" href="#"><span class="k-icon k-filter"/></a>').find(".k-grid-filter");
            }

            link.click(proxy(that._click, that));

            that.dataSource = options.dataSource.bind("change", proxy(that.refresh, that));

            that.field = element.attr(kendo.attr("field"));

            that.model = that.dataSource.reader.model;

            that._parse = function(value) {
                 return value + "";
            }

            if (that.model && that.model.fields) {
                field = that.model.fields[that.field];

                if (field) {
                    type = field.type;
                    that._parse = proxy(field.parse, field);
                }
            }

            operators = operators[type] || options.operators[type];

            that.form = $('<form class="k-filter-menu k-group"/>');
            that.form.html(kendo.template(type === "boolean" ? booleanTemplate : defaultTemplate)({
                field: that.field,
                ns: kendo.ns,
                messages: options.messages,
                extra: options.extra,
                operators: operators,
                type: type
            }));

            that.popup = that.form[POPUP]({
                anchor: link,
                open: proxy(that._open, that)
            }).data(POPUP);

            that.link = link;

            that.form
                .bind({
                    submit: proxy(that._submit, that),
                    reset: proxy(that._reset, that)
                })
                .find("select")
                [DROPDOWNLIST]()
                .end()
                .find("[" + kendo.attr("type") + "=number]")
                [NUMERICTEXTBOX]()
                .end()
                .find("[" + kendo.attr("type") + "=date]")
                [DATEPICKER]();

            that.refresh();
        },

        refresh: function() {
            var that = this,
                form = that.form,
                expression = that.dataSource.filter() || { filters: [], logic: "and" },
                filters = expression.filters,
                filter,
                idx,
                length,
                current = 0;

            for (idx = 0, length = filters.length; idx < length; idx++) {
                filter = filters[idx];
                if (filter.field == that.field) {
                    value(form.find("[name='filters[" + current + "].value']"), that._parse(filter.value));
                    value(form.find("[name='filters[" + current + "].operator']"), filter.operator);
                    current++;
                }
            }

            if (current > 0) {
                value(form.find("[name=logic]"), expression.logic);
                that.link.addClass("k-state-active");
            } else {
                that.link.removeClass("k-state-active");
            }
        },

        _merge: function(expression) {
            var that = this,
                logic = expression.logic || "and",
                filters = expression.filters,
                filter,
                result = that.dataSource.filter() || { filters:[], logic: "and" },
                idx,
                length;

            removeFiltersForField(result, that.field);

            filters = $.grep(filters, function(filter) {
                return filter.value != "";
            });

            for (idx = 0, length = filters.length; idx < length; idx++) {
                filter = filters[idx];
                filter.value = that._parse(filter.value);
            }

            if (filters.length) {
                if (result.filters.length) {
                    expression.filters = filters;

                    if (result.logic !== "and") {
                        result.filters = [ { logic: result.logic, filters: result.filters }];
                        result.logic = "and";
                    }

                    if (filters.length > 1) {
                        result.filters.push(expression);
                    } else {
                        result.filters.push(filters[0]);
                    }
                } else {
                    result.filters = filters;
                    result.logic = logic;
                }
            }

            return result;
        },

        filter: function(expression) {
            expression = this._merge(expression);

            if (expression.filters.length) {
                this.dataSource.filter(expression);
            }
        },

        clear: function() {
            var that = this,
                expression = that.dataSource.filter() || { filters:[] };

            expression.filters = $.grep(expression.filters, function(filter) {
                return filter.field != that.field;
            });

            if (!expression.filters.length) {
                expression = null;
            }

            that.dataSource.filter(expression);
        },

        _submit: function(e) {
            var that = this;

            e.preventDefault();

            that.filter(toObject(that.form.serializeArray()));

            that.popup.close();
        },

        _reset: function(e) {
            this.clear();
            this.popup.close();
        },

        _click: function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.popup.toggle();
        },

        _open: function() {
            $(".k-filter-menu").not(this.form).each(function() {
                $(this).data(POPUP).close();
            });
        },

        options: {
            name: "FilterMenu",
            extra: true,
            type: "string",
            operators: {
                string: {
                    eq: EQ,
                    neq: NEQ,
                    startswith: "Starts with",
                    contains: "Contains",
                    endswith: "Ends with"
                },
                number: {
                    eq: EQ,
                    neq: NEQ,
                    gte: "Is greater than or equal to",
                    gt: "Is greater than",
                    lte: "Is less than or equal to",
                    lt: "Is less than"
                },
                date: {
                    eq: EQ,
                    neq: NEQ,
                    gte: "Is after or equal to",
                    gt: "Is after",
                    lte: "Is before or equal to",
                    lt: "Is before"
                }
            },
            messages: {
                info: "Show rows with value that:",
                isTrue: "is true",
                isFalse: "is false",
                filter: "Filter",
                clear: "Clear"
            }
        }
    });

    ui.plugin(FilterMenu);
})(jQuery);
(function ($, undefined) {
    /**
     * @name kendo.ui.PanelBar.Description
     *
     * @section
     *  <p>
     *      The PanelBar widget displays hierarchical data as a multi-level expandable panel
     *      bar. PanelBar structure can be defined statically in HTML or configured dynamically
     *      with the PanelBar API. Content for PanelBar items can also be easily loaded with
     *      Ajax simply by specifying the content URL.
     *  </p>
     *  <h3>Getting Started</h3>
     *
     *
     * @exampleTitle Create a simple HTML hierarchical list of items
     * @example
     *  <ul id="panelbar">
     *      <li>Item 1
     *          <ul>
     *              <li>Sub Item 1</li>
     *              <li>Sub Item 2</li>
     *          </ul>
     *      <li>
     *      <li>Item 2</li>
     *      <li>Item with Content
     *          <div>This is some PanelBar Item content</div>
     *      </li>
     *  </ul>
     *
     *
     * @exampleTitle Initialize Kendo PanelBar using jQuery selector
     * @example
     * var panelBar = $("#panelBar").kendoPanelBar();
     *
     * @section
     *  <p>
     *      Items in a PanelBar can optionally define in-line HTML content. To add content,
     *      simply place the HTML inside of a div. Text content outside of the div will be used as
     *      the Item's PanelBar text.
     *  </p>
     *  <h3>Loading Content with Ajax</h3>
     *  <p>
     *      While any valid technique for loading Ajax content can be used, PanelBar provides
     *      built-in support for asynchronously loading content from URLs. These URLs should return
     *      HTML fragments that can be loaded in the PanelBar item content area.
     *  </p>
     *  <br/>
     *  <p>
     *      When PanelBar loads content with Ajax, it is cached so that subsequent
     *      expand/collapse actions do not re-trigger the Ajax request.
     *  </p>
     *
     * @exampleTitle Loading PanelBar content asynchronously
     * @example
     *  <!-- HTML structure -->
     *  <ul id="panelbar">
     *      <li>Item 1
     *          <ul>
     *              <li>Sub Item 1</li>
     *          </ul>
     *      </li>
     *      <li>Item 2</li>
     *      <li>
     *          Item with Dynamic Content
     *          <div></div>
     *      </li>
     *  </ul>
     *
     * @exampleTitle
     * @example
     *  //JavaScript initialization and configuration
     *  $(document).ready(function(){
     *      $("#panelbar").kendoPanelBar({
     *          contentUrls:[
     *            null,
     *            null,
     *            "html-content-snippet.html"
     *          ]
     *      });
     *  });
     *
     * @section
     *  <h3>Customizing PanelBar Animations</h3>
     *  <p>
     *      By default, the PanelBar uses a slide animation to expand and reveal sub-items as
     *      the mouse hovers. Animations can be easily customized using configuration properties,
     *      changing the open and close animation style. A PanelBar can also be configured to
     *      only allow one panel to remain open at a time.
     *  </p>
     * @exampleTitle Changing PanelBar animation and expandMode behavior
     * @example
     *  $("#panelbar").kendoPanelBar({
     *      animation: {
     *          open : {effects: fadeIn}
     *      },
     *      expandMode: "single"
     *  });
     * @section
     *  <h3>Dynamically configuring PanelBar items</h3>
     *  <p>
     *      The PanelBar API provides several methods for dynamically adding or removing
     *      Items. To add items, provide the new item as a JSON object along with a reference
     *      item that will be used to determine the placement in the hierarchy.
     *  </p>
     *  <br/>
     *  <p>
     *      A reference item is simply a target PanelBar Item HTML element that already exists
     *      in the PanelBar. Any valid jQuery selector can be used to obtain a reference to the
     *      target item. For examples, see the PanelBar API demos.
     *  </p>
     *  </br>
     *  <p>
     *      Removing an item only requires a reference to the target element that should be removed.
     *  </p>
     *
     * @exampleTitle Dynamically add a new root PanelBar item
     * @example
     *  var pb = $("#panelbar").kendoPanelBar().data("kendoPanelBar");
     *
     *  pb.insertAfter(
     *      { text: "New PanelBar Item" },
     *      pb.element.children("li:last")
     *  );
     *
     */
    var kendo = window.kendo,
        ui = kendo.ui,
        extend = $.extend,
        each = $.each,
        template = kendo.template,
        Widget = ui.Widget,
        excludedNodesRegExp = /^(ul|a|div)$/i,
        IMG = "img",
        HREF = "href",
        LAST = "k-last",
        LINK = "k-link",
        ERROR = "error",
        CLICK = "click",
        ITEM = ".k-item",
        IMAGE = "k-image",
        FIRST = "k-first",
        EXPAND = "expand",
        SELECT = "select",
        CONTENT = "k-content",
        COLLAPSE = "collapse",
        CONTENTURL = "contentUrl",
        MOUSEENTER = "mouseenter",
        MOUSELEAVE = "mouseleave",
        CONTENTLOAD = "contentLoad",
        ACTIVECLASS = ".k-state-active",
        GROUPS = "> .k-group",
        CONTENTS = "> .k-content",
        SELECTEDCLASS = ".k-state-selected",
        DISABLEDCLASS = ".k-state-disabled",
        HIGHLIGHTEDCLASS = ".k-state-highlighted",
        clickableItems = ITEM + ":not(.k-state-disabled) .k-link",
        disabledItems = ITEM + ".k-state-disabled .k-link",
        defaultState = "k-state-default",
        VISIBLE = ":visible",
        EMPTY = ":empty",
        SINGLE = "single",
        animating = false,

        templates = {
            content: template(
                "<div class='k-content'#= contentAttributes(data) #>#= content(item) #</div>"
            ),
            group: template(
                "<ul class='#= groupCssClass(group) #'#= groupAttributes(group) #>" +
                    "#= renderItems(data) #" +
                "</ul>"
            ),
            itemWrapper: template(
                "<#= tag(item) # class='#= textClass(item, group) #'#= contentUrl(item) ##= textAttributes(item) #>" +
                    "#= image(item) ##= sprite(item) ##= text(item) #" +
                    "#= arrow(data) #" +
                "</#= tag(item) #>"
            ),
            item: template(
                "<li class='#= wrapperCssClass(group, item) #'>" +
                    "#= itemWrapper(data) #" +
                    "# if (item.items) { #" +
                    "#= subGroup({ items: item.items, panelBar: panelBar, group: { expanded: item.expanded } }) #" +
                    "# } #" +
                "</li>"
            ),
            image: template("<img class='k-image' alt='' src='#= imageUrl #' />"),
            arrow: template("<span class='#= arrowClass(item, group) #'></span>"),
            sprite: template("<span class='k-sprite #= spriteCssClass #'></span>"),
            empty: template("")
        },

        rendering = {
            wrapperCssClass: function (group, item) {
                var result = "k-item",
                    index = item.index;

                if (item.enabled === false) {
                    result += " k-state-disabled";
                } else {
                    result += " k-state-default";
                }

                if (index == 0) {
                    result += " k-first"
                }

                if (index == group.length-1) {
                    result += " k-last";
                }

                return result;
            },
            textClass: function(item, group) {
                var result = LINK;

                if (group.firstLevel) {
                    result += " k-header";
                }

                return result;
            },
            textAttributes: function(item) {
                return item.url ? " href='" + item.url + "'" : "";
            },
            arrowClass: function(item, group) {
                var result = "k-icon";

                if (group.horizontal) {
                    result += " k-arrow-down";
                } else {
                    result += " k-arrow-right";
                }

                return result;
            },
            text: function(item) {
                return item.encoded === false ? item.text : kendo.htmlEncode(item.text);
            },
            tag: function(item) {
                return item.url ? "a" : "span";
            },
            groupAttributes: function(group) {
                return group.expanded !== true ? " style='display:none'" : "";
            },
            groupCssClass: function(group) {
                return "k-group";
            },
            contentAttributes: function(content) {
                return content.active !== true ? " style='display:none'" : "";
            },
            content: function(item) {
                return item.content ? item.content : item.contentUrl ? "" : "&nbsp;";
            },
            contentUrl: function(item) {
                return item.contentUrl ? kendo.attr("content-url") + '="' + item.contentUrl + '"' : "";
            }
        };

    function updateItemClasses (item, menuElement) {
        item = $(item).addClass("k-item");

        item
            .children(IMG)
            .addClass(IMAGE);
        item
            .children("a")
            .addClass(LINK)
            .children(IMG)
            .addClass(IMAGE);
        item
            .filter(":not([disabled]):not([class*=k-state])")
            .addClass("k-state-default");
        item
            .filter("li[disabled]")
            .addClass("k-state-disabled")
            .removeAttr("disabled");
        item
            .filter(":not([class*=k-state])")
            .children("a:focus")
            .parent()
            .addClass(ACTIVECLASS.substr(1));
        item
            .find(">div")
            .addClass(CONTENT)
            .css({ display: "none" });

        item.each(function() {
            var item = $(this);

            if (!item.children("." + LINK).length) {
                item
                    .contents()      // exclude groups, real links, templates and empty text nodes
                    .filter(function() { return (!this.nodeName.match(excludedNodesRegExp) && !(this.nodeType == 3 && !$.trim(this.nodeValue))); })
                    .wrapAll("<span class='" + LINK + "'/>");
            }
        });

        menuElement
            .find(" > li > ." + LINK)
            .addClass("k-header");
    }

    function updateArrow (items) {
        items = $(items);

        items.children(".k-link").children(".k-icon").remove();

        items
            .filter(":has(.k-group),:has(.k-content)")
            .children(".k-link:not(:has([class*=k-arrow]))")
            .each(function () {
                var item = $(this),
                    parent = item.parent();

                item.append("<span class='k-icon " + (parent.hasClass(ACTIVECLASS.substr(1)) ? "k-arrow-up k-panelbar-collapse" : "k-arrow-down k-panelbar-expand") + "'/>");
            });
    }

    function updateFirstLast (items) {
        items = $(items);

        items.filter(".k-first:not(:first-child)").removeClass(FIRST);
        items.filter(".k-last:not(:last-child)").removeClass(LAST);
        items.filter(":first-child").addClass(FIRST);
        items.filter(":last-child").addClass(LAST);
    }

    var PanelBar = Widget.extend({/** @lends kendo.ui.PanelBar.prototype */
        /**
         * Creates a PanelBar instance.
         * @constructs
         * @extends kendo.ui.Widget
         * @class PanelBar UI widget
         * @param {Selector} element DOM element
         * @param {Object} options Configuration options.
         * @option {Object} [animation] A collection of <b>Animation</b> objects, used to change default animations. A value of false will disable all animations in the widget.
         * @option {Animation} [animation.open] The animation that will be used when expanding items.
         * @option {Animation} [animation.close] The animation that will be used when collapsing items.
         * @option {String} [expandMode] <multiple> Specifies if PanelBar should collapse the already expanded item when expanding next item
         */
        init: function(element, options) {
            element = $(element);

            var that = this,
                content;

            Widget.fn.init.call(that, element, options);

            options = that.options;

            if (that.element.is(EMPTY)) {
                that.element.append($(PanelBar.renderGroup({
                    items: options.dataSource,
                    group: {
                        firstLevel: true,
                        expanded: true
                    },
                    panelBar: {}
                })).children());
            }

            that._updateClasses();

            if (options.animation === false) {
                options.animation = { open: { show: true, effects: {} }, close: { hide:true, effects: {} } };
            }

            element
                .delegate(clickableItems, CLICK, $.proxy(that._click, that))
                .delegate(clickableItems, MOUSEENTER + " " + MOUSELEAVE, that._toggleHover)
                .delegate(disabledItems, CLICK, false);

            that.bind([
                /**
                 * Fires before an item is expanded.
                 * @name kendo.ui.PanelBar#expand
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The expanding item
                 */
                EXPAND,
                /**
                 * Fires before an item is collapsed.
                 * @name kendo.ui.PanelBar#collapse
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The collapsing item
                 */
                COLLAPSE,
                /**
                 * Fires before an item is selected.
                 * @name kendo.ui.PanelBar#select
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The selected item
                 */
                SELECT,
                /**
                 * Fires when ajax request results in an error.
                 * @name kendo.ui.PanelBar#error
                 * @event
                 * @param {Event} e
                 * @param {jqXHR} e.xhr The jqXHR object used to load the content
                 * @param {String} e.status The returned status.
                 */
                ERROR,
                /**
                 * Fires when content is fetched from an ajax request.
                 * @name kendo.ui.PanelBar#contentLoad
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The selected item
                 * @param {Element} e.item The loaded content element
                 */
                CONTENTLOAD
            ], that.options);

            if (that.options.contentUrls) {
                element.find("> .k-item")
                    .each(function(index, item) {
                        $(item).find("." + LINK).data(CONTENTURL, that.options.contentUrls[index]);
                    });
            }

            content = element.find("li" + ACTIVECLASS + " > ." + CONTENT);

            if (content.length > 0) {
                that.expand(content.parent(), false);
            }
        },
        options: {
            name: "PanelBar",
            animation: {
                open: {
                    effects: "expandVertical",
                    duration: 200,
                    show: true
                },
                close: { // if close animation effects are defined, they will be used instead of open.reverse
                    duration: 200,
                    show: false,
                    hide: true
                }
            },
            expandMode: "multiple"
        },

        /**
         * Expands the specified PanelBar item/s
         * @param {Selector} element Target item selector.
         * @param {Boolean} useAnimation Use this parameter to temporary disable the animation.
         * @example
         * panelBar.expand("#Item1");
         */
        expand: function (element, useAnimation) {
            var that = this,
                animBackup = {};
            useAnimation = useAnimation !== false;

            $(element).each(function (index, item) {
                item = $(item);
                var groups = item.find(GROUPS).add(item.find(CONTENTS));

                if (!item.hasClass(DISABLEDCLASS) && groups.length > 0) {

                    if (that.options.expandMode == SINGLE && that._collapseAllExpanded(item)) {
                        return;
                    }

                    element.find(HIGHLIGHTEDCLASS).removeClass(HIGHLIGHTEDCLASS.substr(1));
                    item.addClass(HIGHLIGHTEDCLASS.substr(1));

                    if (!useAnimation) {
                        animBackup = that.options.animation;
                        that.options.animation = { open: { show: true, effects: {} }, close: { hide:true, effects: {} } };
                    }

                    that._toggleItem(item, false, null);

                    if (!useAnimation) {
                        that.options.animation = animBackup;
                    }
                }
            });
        },

        /**
         * Collapses the specified PanelBar item/s
         * @param {Selector} element Target item selector.
         * @param {Boolean} useAnimation Use this parameter to temporary disable the animation.
         * @example
         * panelBar.collapse("#Item1");
         */
        collapse: function (element, useAnimation) {
            var that = this,
                animBackup = {};
            useAnimation = useAnimation !== false;

            $(element).each(function (index, item) {
                item = $(item);
                var groups = item.find(GROUPS).add(item.find(CONTENTS));

                if (!item.hasClass(DISABLEDCLASS) && groups.is(VISIBLE)) {
                    item.removeClass(HIGHLIGHTEDCLASS.substr(1));

                    if (!useAnimation) {
                        animBackup = that.options.animation;
                        that.options.animation = { open: { show: true, effects: {} }, close: { hide:true, effects: {} } };
                    }

                    that._toggleItem(item, true, null);

                    if (!useAnimation) {
                        that.options.animation = animBackup;
                    }
                }

            });
        },

        toggle: function (element, enable) {
            $(element)
                .toggleClass(defaultState, enable)
                .toggleClass(DISABLEDCLASS.substr(1), !enable);
        },

        /**
         * Selects the specified PanelBar item/s. If called without arguments - returns the selected item.
         * @param {Selector} element Target item selector.
         * @example
         * panelBar.select("#Item1");
         */
        select: function (element) {
            var that = this;

            if (arguments.length === 0) {
                return that.element.find(".k-item > " + SELECTEDCLASS).parent();
            }

            $(element).each(function (index, item) {
                item = $(item);
                var link = item.children("." + LINK);

                if (item.is(DISABLEDCLASS)) {
                    return;
                }

                $(SELECTEDCLASS, that.element).removeClass(SELECTEDCLASS.substr(1));
                $(HIGHLIGHTEDCLASS, that.element).removeClass(HIGHLIGHTEDCLASS.substr(1));

                link.addClass(SELECTEDCLASS.substr(1));
                link.parentsUntil(that.element, ITEM).filter(":has(.k-header)").addClass(HIGHLIGHTEDCLASS.substr(1));
            });
        },

        /**
         * Enables/disables a PanelBar item
         * @param {Selector} element Target element
         * @param {Boolean} enable Desired state
         */
        enable: function (element, state) {
            this.toggle(element, state !== false);
        },

        /**
         * Disables a PanelBar item
         * @param {Selector} element Target element
         */
        disable: function (element) {
            this.toggle(element, false);
        },

        /**
         * Appends a PanelBar item in the specified referenceItem
         * @param {Selector} item Target item, specified as a JSON object. You can pass item text, content or contentUrl here. Can handle an HTML string or array of such strings or JSON.
         * @param {Item} referenceItem A reference item to append the new item in
         * @example
         * panelBar.append(
         *     [{
         *         text: "Item 1",
         *         content: "text"
         *     },
         *     {
         *         text: "Item 2",
         *         contentUrl: "partialContent.html"
         *     }],
         *     referenceItem
         * );
         */
        append: function (item, referenceItem) {
            referenceItem = $(referenceItem);

            var inserted = this._insert(item, referenceItem, referenceItem.length ? referenceItem.find("> .k-group") : null);

            each(inserted.items, function (idx) {
                inserted.group.append(this);

                var contents = inserted.contents[idx];
                if (contents)
                    $(this).append(contents);

                updateFirstLast(this);
            });

            updateArrow(referenceItem);
            updateFirstLast(inserted.group.find(".k-first, .k-last"));
            inserted.group.height("auto");
        },

        /**
         * Inserts a PanelBar item before the specified referenceItem
         * @param {Selector} item Target item, specified as a JSON object. You can pass item text, content or contentUrl here. Can handle an HTML string or array of such strings or JSON.
         * @param {Item} referenceItem A reference item to insert the new item before
         * @example
         * panelBar.insertBefore(
         *     [{
         *         text: "Item 1",
         *         content: "text"
         *     },
         *     {
         *         text: "Item 2",
         *         contentUrl: "partialContent.html"
         *     }],
         *     referenceItem
         * );
         */
        insertBefore: function (item, referenceItem) {
            referenceItem = $(referenceItem);

            var inserted = this._insert(item, referenceItem, referenceItem.parent());

            each(inserted.items, function (idx) {
                referenceItem.before(this);

                var contents = inserted.contents[idx];
                if (contents)
                    $(this).append(contents);

                updateFirstLast(this);
            });

            updateFirstLast(referenceItem);
            inserted.group.height("auto");
        },

        /**
         * Inserts a PanelBar item after the specified referenceItem
         * @param {Selector} item Target item, specified as a JSON object. You can pass item text, content or contentUrl here. Can handle an HTML string or array of such strings or JSON.
         * @param {Item} referenceItem A reference item to insert the new item after
         * @example
         * panelBar.insertAfter(
         *     [{
         *         text: "Item 1",
         *         content: "text"
         *     },
         *     {
         *         text: "Item 2",
         *         contentUrl: "partialContent.html"
         *     }],
         *     referenceItem
         * );
         */
        insertAfter: function (item, referenceItem) {
            referenceItem = $(referenceItem);

            var inserted = this._insert(item, referenceItem, referenceItem.parent());

            each(inserted.items, function (idx) {
                referenceItem.after(this);

                var contents = inserted.contents[idx];
                if (contents)
                    $(this).append(contents);

                updateFirstLast(this);
            });

            updateFirstLast(referenceItem);
            inserted.group.height("auto");
        },

        /**
         * Removes the specified PanelBar item/s
         * @param {Selector} element Target item selector.
         * @example
         * panelBar.remove("#Item1");
         */
        remove: function (element) {
            element = $(element);

            var that = this,
                parent = element.parentsUntil(that.element, ITEM),
                group = element.parent("ul");

            element.remove();

            if (group && !group.children(ITEM).length) {
                group.remove();
            }

            if (parent.length) {
                parent = parent.eq(0);

                updateArrow(parent);
                updateFirstLast(parent);
            }
        },

        _insert: function (item, referenceItem, parent) {
            var that = this, contents = [];

            if (!referenceItem || !referenceItem.length) {
                parent = that.element;
            }

            var plain = $.isPlainObject(item),
                items,
                groupData = {
                    firstLevel: parent.hasClass("k-panelbar"),
                    expanded: parent.parent().hasClass("k-state-active"),
                    length: parent.children().length
                };

            if (referenceItem && !parent.length) {
                parent = $(PanelBar.renderGroup({ group: groupData })).appendTo(referenceItem);
            }

            if (plain || $.isArray(item)) { // is JSON
                items = $.map(plain ? [ item ] : item, function (value, idx) {
                            if (typeof value === "string") {
                                return $(value);
                            } else {
                                return $(PanelBar.renderItem({
                                    group: groupData,
                                    item: extend(value, { index: idx })
                                }));
                            }
                        });
                contents = $.map(plain ? [ item ] : item, function (value, idx) {
                            if (value.content || value.contentUrl) {
                                return $(PanelBar.renderContent({
                                    item: extend(value, { index: idx })
                                }));
                            } else {
                                return false;
                            }
                        });
            } else {
                items = $(item);

                updateItemClasses(items, that.element);
            }

            return { items: items, group: parent, contents: contents };
        },

        _toggleHover: function(e) {
            var target = $(e.currentTarget);

            if (!target.parents("li" + DISABLEDCLASS).length) {
                target.toggleClass("k-state-hover", e.type == MOUSEENTER);
            }
        },

        _updateClasses: function() {
            var that = this;

            that.element.addClass("k-widget k-reset k-header k-panelbar");

            var items = that.element
                            .find("ul")
                            .addClass("k-group")
                            .end()
                            .find("li:not(" + ACTIVECLASS + ") > ul")
                            .css({ display: "none" })
                            .end()
                            .find("li");

            items.each(function () {
                updateItemClasses(this, that.element);
            });

            updateArrow(items);
            updateFirstLast(items);
        },

        _click: function (e) {
            var that = this,
                target = $(e.currentTarget),
                element = that.element;

            if (target.parents("li" + DISABLEDCLASS).length) {
                return;
            }

            if (target.closest(".k-widget")[0] != element[0]) {
                return;
            }

            var link = target.closest("." + LINK),
                item = link.closest(ITEM);

            $(SELECTEDCLASS, element).removeClass(SELECTEDCLASS.substr(1));
            $(HIGHLIGHTEDCLASS, element).removeClass(HIGHLIGHTEDCLASS.substr(1));

            link.addClass(SELECTEDCLASS.substr(1));
            link.parentsUntil(that.element, ITEM).filter(":has(.k-header)").addClass(HIGHLIGHTEDCLASS.substr(1));

            var contents = item.find(GROUPS).add(item.find(CONTENTS)),
                href = link.attr(HREF),
                isAnchor = link.data(CONTENTURL) || (href && (href.charAt(href.length - 1) == "#" || href.indexOf("#" + that.element[0].id + "-") != -1));

            if (contents.data("animating")) {
                return;
            }

            if (that._triggerEvent(SELECT, item)) {
                e.preventDefault();
            }

            if (isAnchor || contents.length) {
                e.preventDefault();
            } else {
                return;
            }

            if (that.options.expandMode == SINGLE) {
                if (that._collapseAllExpanded(item)) {
                    return;
                }
            }

            if (contents.length) {
                var visibility = contents.is(VISIBLE);

                if (!that._triggerEvent(!visibility ? EXPAND : COLLAPSE, item)) {
                    that._toggleItem(item, visibility, e);
                }
            }
        },

        _toggleItem: function (element, isVisible, e) {
            var that = this,
                childGroup = element.find("> .k-group");

            if (childGroup.length) {

                this._toggleGroup(childGroup, isVisible);

                if (e) {
                    e.preventDefault();
                }
            } else {

                var content = element.find("> ."  + CONTENT);

                if (content.length) {
                    if (e) {
                        e.preventDefault();
                    }

                    if (!content.is(EMPTY)) {
                        that._toggleGroup(content, isVisible);
                    } else {
                        that._ajaxRequest(element, content, isVisible);
                    }
                }
            }
        },

        _toggleGroup: function (element, visibility) {
            var that = this,
                hasCloseAnimation = "effects" in that.options.animation.close,
                closeAnimation = extend({}, that.options.animation.open);

            if (element.is(VISIBLE) != visibility) {
                return;
            }

            visibility && element.css("height", element.height()); // Set initial height on visible items (due to a Chrome bug/feature).
            element.css("height");

            element
                .parent()
                .toggleClass(defaultState, visibility)
                .toggleClass(ACTIVECLASS.substr(1), !visibility)
                .find("> .k-link > .k-icon")
                    .toggleClass("k-arrow-up", !visibility)
                    .toggleClass("k-panelbar-collapse", !visibility)
                    .toggleClass("k-arrow-down", visibility)
                    .toggleClass("k-panelbar-expand", visibility);

            element
                .kendoStop(true, true)
                .kendoAnimate(extend( hasCloseAnimation && visibility ?
                                          that.options.animation.close :
                                          !hasCloseAnimation && visibility ?
                                               extend(closeAnimation, { show: false, hide: true }) :
                                               that.options.animation.open, {
                                                   reverse: !hasCloseAnimation && visibility
                                               }));
        },

        _collapseAllExpanded: function (item) {
            var that = this;

            if (item.find("> ." + LINK).hasClass("k-header")) {
                var groups = item.find(GROUPS).add(item.find(CONTENTS));
                if (groups.is(VISIBLE) || groups.length == 0) {
                    return true;
                } else {
                    var children = $(that.element).children();
                    children.find(GROUPS).add(children.find(CONTENTS))
                            .filter(function () { return $(this).is(VISIBLE) })
                            .each(function (index, content) {
                                that._toggleGroup($(content), true);
                            });
                }
            }
        },

        _ajaxRequest: function (element, contentElement, isVisible) {

            var that = this,
                statusIcon = element.find(".k-panelbar-collapse, .k-panelbar-expand"),
                link = element.find("." + LINK),
                loadingIconTimeout = setTimeout(function () {
                    statusIcon.addClass("k-loading");
                }, 100),
                data = {};

            $.ajax({
                type: "GET",
                cache: false,
                url: link.data(CONTENTURL) || link.attr(HREF),
                dataType: "html",
                data: data,

                error: function (xhr, status) {
                    if (that.trigger(ERROR, { xhr: xhr, status: status })) {
                        this.complete();
                    }
                },

                complete: function () {
                    clearTimeout(loadingIconTimeout);
                    statusIcon.removeClass("k-loading");
                },

                success: function (data, textStatus) {
                    contentElement.html(data);
                    that._toggleGroup(contentElement, isVisible);

                    that.trigger(CONTENTLOAD, { item: element[0], contentElement: contentElement[0] });
                }
            });
        },

        _triggerEvent: function (eventName, element) {
            var that = this;

            that.trigger(eventName, { item: element[0] });
        }
    });

    // client-side rendering
    extend(PanelBar, {
        renderItem: function (options) {
            options = extend({ panelBar: {}, group: {} }, options);

            var empty = templates.empty,
                item = options.item,
                panelBar = options.panelBar;

            return templates.item(extend(options, {
                image: item.imageUrl ? templates.image : empty,
                sprite: item.spriteCssClass ? templates.sprite : empty,
                itemWrapper: templates.itemWrapper,
                arrow: item.items ? templates.arrow : empty,
                subGroup: PanelBar.renderGroup
            }, rendering));
        },

        renderGroup: function (options) {
            return templates.group(extend({
                renderItems: function(options) {
                    var html = "",
                        i = 0,
                        items = options.items,
                        len = items ? items.length : 0,
                        group = extend({ length: len }, options.group);

                    for (; i < len; i++) {
                        html += PanelBar.renderItem(extend(options, {
                            group: group,
                            item: extend({ index: i }, items[i])
                        }));
                    }

                    return html;
                }
            }, options, rendering));
        },

        renderContent: function (options) {
            return templates.content(extend(options, rendering));
        }
    });

    kendo.ui.plugin(PanelBar);

})(jQuery);
(function ($, undefined) {
    /**
     * @name kendo.ui.TabStrip.Description
     *
     * @section
     *  <p>
     *      The TabStrip widget displays a collection of tabs with associated tab content.
     *      TabStrips are composed of an HTML unordered list of items, which represent the tabs,
     *      and a collection of HTML divs, which define the tab content.
     *  </p>
     *  <h3>Getting Started</h3>
     *
     * @exampleTitle In a HTML div, create an HTML unordered list for tabs, HTML divs for content
     * @example
     *  <div id="tabstrip">
     *      <ul>
     *          <li>First Tab</li>
     *          <li>Second Tab</li>
     *      </ul>
     *      <div>First Tab Content</div>
     *      <div>Second Tab Content</div>
     *  </div>
     *
     * @exampleTitle Initialize the TabStrip using a jQuery selector to target the outer div
     * @example
     * var tabStrip = $("#tabstrip").kendoTabStrip();
     * @section
     *  <p>
     *      Tabs do not have to have content. If a tab should have no content, it is safe to omit the HTML div.
     *  </p>
     *  <h3>Loading TabStrip content with Ajax</h3>
     *  <p>
     *      While any valid technique for loading Ajax content can be used, TabStrip provides
     *      built-in support for asynchronously loading content from URLs. These URLs should
     *      return HTML fragments that can be loaded in a TabStrip content area.
     *  </p>
     * @exampleTitle Loading Tab content asynchronously
     * @example
     *  <!-- Define the TabStrip HTML -->
     *  <div id="tabstrip">
     *      <ul>
     *          <li>First Tab</li>
     *          <li>Second Tab</li>
     *      </ul>
     *      <div> </div>
     *      <div> </div>
     *  </div>
     * @exampleTitle
     * @example
     *  //Initialize TabStrip and configure one tab with async content loading
     *  $(document).ready(function(){
     *      $("#tabstrip").kendoTabStrip({
     *        contentUrls: [null, "html-content-snippet.html"]
     *      });
     *  });
     *
     * @section
     *  <h3>Dynamically configure TabStrip tabs</h3>
     *  <p>
     *      The TabStrip API provides several methods for dynamically adding or removing Tabs. To add tabs,
     *      provide the new item as a JSON object along with a reference item that will be used to determine
     *      the placement in the TabStrip.
     *  <p>
     *  <br/>
     *  <p>
     *      A reference item is simply a target Tab HTML element that already exists in the TabStrip. Any valid
     *      jQuery selector can be used to obtain a reference to the target item. For examples, see the <a href="../tabstrip/api.html" title="TabStrip  API demos">TabStrip  API demos</a>.
     *  </p>
     *  <br/>
     *  <p>
     *      Removing an item only requires a reference to the target element that should be removed.
     *  </p>
     * @exampleTitle Dynamically add a new Tab
     * @example
     *  var tabstrip = $("#tabstrip").kendoTabStrip().data("kendoTabStrip");
     *
     *  tabstrip.insertAfter(
     *      { text: "New Tab" },
     *      tabstrip.tabGroup.children("li:last")
     *  );
     * @section
     *  <h3>Selecting a Tab on Initial Load</h3>
     *  <p>
     *      A common desire with TabStrips is to select a tab and display its associated content on initial load. There are two ways to accomplish this with TabStrip:
     *  </p>
     *  <ol>
     *      <li>Manually add the "k-state-active" class to the Tab that should be selected</li>
     *      <li>Use the TabStrip API to target and select a Tab</li>
     *  </ol>
     *  <p>
     *      Both approaches produce the same end result. The first approach requires no additional JavaScript, but does require a small amount of HTML configuration.
     *  </p>
     *
     * @exampleTitle Selecting a default tab manually using HTML
     * @example
     *  <div id="tabstrip">
     *      <ul>
     *          <li class="k-state-active">First Tab</li>
     *          <li>Second Tab</li>
     *      </ul>
     *      <div> </div>
     *      <div> </div>
     *  </div>
     * @exampleTitle
     * @example
     *  //Initialize the TabStrip
     *  $(document).ready(function(){
     *      $("#tabstrip").kendoTabStrip();
     *  });
     * @exampleTitle Selecting a default tab using the TabStrip API
     * @example
     *  <div id="tabstrip">
     *      <ul>
     *          <li>First Tab</li>
     *          <li>Second Tab</li>
     *      </ul>
     *      <div> </div>
     *      <div> </div>
     *  </div>
     *
     * @exampleTitle
     * @example
     *  //Initialize the TabStrip and select first tab
     *  $(document).ready(function(){
     *      var tabstrip = $("#tabstrip").kendoTabStrip().data("kendoTabStrip");
     *      tabstrip.select(tabstrip.tabGroup.children("li:first"));
     *  });
     */
    var kendo = window.kendo,
        ui = kendo.ui,
        map = $.map,
        each = $.each,
        trim = $.trim,
        extend = $.extend,
        template = kendo.template,
        Widget = ui.Widget,
        excludedNodesRegExp = /^(a|div)$/i,
        IMG = "img",
        HREF = "href",
        LINK = "k-link",
        LAST = "k-last",
        CLICK = "click",
        ERROR = "error",
        EMPTY = ":empty",
        IMAGE = "k-image",
        FIRST = "k-first",
        SELECT = "select",
        CONTENT = "k-content",
        CONTENTURL = "contentUrl",
        MOUSEENTER = "mouseenter",
        MOUSELEAVE = "mouseleave",
        CONTENTLOAD = "contentLoad",
        CLICKABLEITEMS = ".k-tabstrip-items > .k-item:not(.k-state-disabled)",
        HOVERABLEITEMS = ".k-tabstrip-items > .k-item:not(.k-state-disabled):not(.k-state-active)",
        DISABLEDLINKS = ".k-tabstrip-items > .k-state-disabled .k-link",
        DISABLEDSTATE = "k-state-disabled",
        DEFAULTSTATE = "k-state-default",
        ACTIVESTATE = "k-state-active",
        HOVERSTATE = "k-state-hover",
        TABONTOP = "k-tab-on-top",

        templates = {
            content: template(
                "<div class='k-content'#= contentAttributes(data) #>#= content(item) #</div>"
            ),
            itemWrapper: template(
                "<#= tag(item) # class='k-link'#= contentUrl(item) ##= textAttributes(item) #>" +
                    "#= image(item) ##= sprite(item) ##= text(item) #" +
                "</#= tag(item) #>"
            ),
            item: template(
                "<li class='#= wrapperCssClass(group, item) #'>" +
                    "#= itemWrapper(data) #" +
                "</li>"
            ),
            image: template("<img class='k-image' alt='' src='#= imageUrl #' />"),
            sprite: template("<span class='k-sprite #= spriteCssClass #'></span>"),
            empty: template("")
        },

        rendering = {
            wrapperCssClass: function (group, item) {
                var result = "k-item",
                    index = item.index;

                if (item.enabled === false) {
                    result += " k-state-disabled";
                } else {
                    result += " k-state-default";
                }

                if (index == 0) {
                    result += " k-first"
                }

                if (index == group.length-1) {
                    result += " k-last";
                }

                return result;
            },
            textAttributes: function(item) {
                return item.url ? " href='" + item.url + "'" : "";
            },
            text: function(item) {
                return item.encoded === false ? item.text : kendo.htmlEncode(item.text);
            },
            tag: function(item) {
                return item.url ? "a" : "span";
            },
            contentAttributes: function(content) {
                return content.active !== true ? " style='display:none'" : "";
            },
            content: function(item) {
                return item.content ? item.content : item.contentUrl ? "" : "&nbsp;";
            },
            contentUrl: function(item) {
                return item.contentUrl ? kendo.attr("content-url") + '="' + item.contentUrl + '"' : "";
            }
        };

    function updateTabClasses (tabs) {
        tabs.children(IMG)
            .addClass(IMAGE);

        tabs.children("a")
            .addClass(LINK)
            .children(IMG)
            .addClass(IMAGE);

        tabs.filter(":not([disabled]):not([class*=k-state-disabled])")
            .addClass(DEFAULTSTATE);

        tabs.filter("li[disabled]")
            .addClass(DISABLEDSTATE)
            .removeAttr("disabled");

        tabs.filter(":not([class*=k-state])")
            .children("a:focus")
            .parent()
            .addClass(ACTIVESTATE + " " + TABONTOP);

        tabs.each(function() {
            var item = $(this);

            if (!item.children("." + LINK).length) {
                item
                    .contents()      // exclude groups, real links, templates and empty text nodes
                    .filter(function() { return (!this.nodeName.match(excludedNodesRegExp) && !(this.nodeType == 3 && !trim(this.nodeValue))); })
                    .wrapAll("<a class='" + LINK + "'/>");
            }
        });

    }

    function updateFirstLast (tabGroup) {
        var tabs = tabGroup.children(".k-item");

        tabs.filter(".k-first:not(:first-child)").removeClass(FIRST);
        tabs.filter(".k-last:not(:last-child)").removeClass(LAST);
        tabs.filter(":first-child").addClass(FIRST);
        tabs.filter(":last-child").addClass(LAST);
    }

    var TabStrip = Widget.extend({/** @lends kendo.ui.TabStrip.prototype */
        /**
         * Creates a TabStrip instance.
         * @constructs
         * @extends kendo.ui.Widget
         * @class TabStrip UI widget
         * @param {Selector} element DOM element
         * @param {Object} options Configuration options.
         * @option {Object} [animation] A collection of <b>Animation</b> objects, used to change default animations. A value of false will disable all animations in the widget.
         * @option {Animation} [animation.open] The animation that will be used when opening content.
         * @option {Animation} [animation.close] The animation that will be used when closing content.
         */

        init: function(element, options) {
            element = $(element);

            if (element.is("ul")) {
                element = element.wrapAll("<div />").parent();
            }

            var that = this;

            if (options && ("animation" in options) && !options.animation) {
                options.animation = { open: { effects: {} }, close: { effects: {} } }; // No animation
            }

            Widget.fn.init.call(that, element, options);

            options = that.options;

            element
                .delegate(CLICKABLEITEMS, CLICK, $.proxy(that._click, that))
                .delegate(HOVERABLEITEMS, MOUSEENTER + " " + MOUSELEAVE, that._toggleHover)
                .delegate(DISABLEDLINKS, CLICK, false);

            that.bind([
                /**
                 * Fires before a tab is selected.
                 * @name kendo.ui.TabStrip#select
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The selected item
                 */
                SELECT,
                /**
                 * Fires when ajax request results in an error.
                 * @name kendo.ui.TabStrip#error
                 * @event
                 * @param {Event} e
                 * @param {jqXHR} e.xhr The jqXHR object used to load the content
                 * @param {String} e.status The returned status.
                 */
                ERROR,
                /**
                 * Fires when content is fetched from an ajax request.
                 * @name kendo.ui.TabStrip#contentLoad
                 * @event
                 * @param {Event} e
                 * @param {Element} e.item The selected item
                 * @param {Element} e.item The loaded content element
                 */
                CONTENTLOAD
            ], that.options);

            that._updateClasses();

            if (that.tabGroup.is(EMPTY)) {
                options.dataSource && that.append(options.dataSource);
            }

            if (that.options.contentUrls) {
                element.find(".k-tabstrip-items > .k-item")
                    .each(function(index, item) {
                        $(item).find(">." + LINK).data(CONTENTURL, that.options.contentUrls[index]);
                    });
            }

            var selectedItems = element.find("li." + ACTIVESTATE),
                content = $(that.contentElement(selectedItems.parent().children().index(selectedItems)));

            if (content.length > 0 && content[0].childNodes.length == 0) {
                that.activateTab(selectedItems.eq(0));
            }
        },
        options: {
            name: "TabStrip",
            animation: {
                open: {
                    effects: "expandVertical fadeIn",
                    duration: 200,
                    show: true
                },
                close: { // if close animation effects are defined, they will be used instead of open.reverse
                    duration: 200,
                    show: false,
                    hide: true
                }
            },
            collapsible: false
        },

        /**
         * Selects the specified TabStrip tab/s. If called without arguments - returns the selected tab.
         * @param {Selector} element Target item selector.
         * @example
         * tabStrip.select("#Item1");
         */
        select: function (element) {
            var that = this;

            if (arguments.length == 0) {
                return that.element.find("li." + ACTIVESTATE);
            }

            $(element).each(function (index, item) {
                item = $(item);
                if (!item.hasClass(ACTIVESTATE)) {
                    that.activateTab(item);
                }
            });
        },

        /**
         * Enables/disables a TabStrip tab
         * @param {Selector} element Target element
         * @param {Boolean} enable Desired state
         */
        enable: function (element, state) {
            this._toggleDisabled(element, state !== false);
        },

        /**
         * Disables a TabStrip tab
         * @param {Selector} element Target element
         */
        disable: function (element) {
            this._toggleDisabled(element, false);
        },


        /**
         * Reloads a TabStrip tab from ajax request
         * @param {Selector} element Target element
         */
        reload: function (element) {
            var that = this;

            $(element).each(function () {
                var item = $(this),
                    contentUrl = item.find("." + LINK).data(CONTENTURL);

                if (contentUrl) {
                    that.ajaxRequest(item, $(that.contentElement(item.index())), null, contentUrl);
                }
            });
        },

        /**
         * Appends a TabStrip item to the end of the tab list.
         * @param {Selector} tab Target tab, specified as a JSON object. You can pass tab text, content or contentUrl here. Can handle an HTML string or array of such strings or JSON.
         * @example
         * tabStrip.append(
         *     [{
         *         text: "Item 1",
         *         content: "text"
         *     },
         *     {
         *         text: "Item 2",
         *         contentUrl: "partialContent.html"
         *     }]
         * );
         */
        append: function (tab) {
            var that = this,
                inserted = that._create(tab);

            each(inserted.tabs, function (idx) {
                that.tabGroup.append(this);
                that.element.append(inserted.contents[idx]);
            });

            updateFirstLast(that.tabGroup);
            that._updateContentElements();
        },

        /**
         * Inserts a TabStrip item before the specified referenceItem
         * @param {Selector} item Target tab, specified as a JSON object. You can pass tab text, content or contentUrl here. Can handle an HTML string or array of such strings or JSON.
         * @param {Item} referenceTab A reference tab to insert the new item before
         * @example
         * tabStrip.insertBefore(
         *     [{
         *         text: "Item 1",
         *         content: "text"
         *     },
         *     {
         *         text: "Item 2",
         *         contentUrl: "partialContent.html"
         *     }],
         *     referenceItem
         * );
         */
        insertBefore: function (tab, referenceTab) {
            var that = this,
                inserted = this._create(tab),
                referenceContent = $(that.contentElement(referenceTab.index()));

            each(inserted.tabs, function (idx) {
                referenceTab.before(this);
                referenceContent.before(inserted.contents[idx]);
            });

            updateFirstLast(that.tabGroup);
            that._updateContentElements();
        },

        /**
         * Inserts a TabStrip tab after the specified referenceTab
         * @param {Selector} item Target tab, specified as a JSON object. You can pass tab text, content or contentUrl here. Can handle an HTML string or array of such strings or JSON.
         * @param {Item} referenceTab A reference tab to insert the new item after
         * @example
         * tabStrip.insertAfter(
         *     [{
         *         text: "Item 1",
         *         content: "text"
         *     },
         *     {
         *         text: "Item 2",
         *         contentUrl: "partialContent.html"
         *     }],
         *     referenceItem
         * );
         */
        insertAfter: function (tab, referenceTab) {
            var that = this,
                inserted = this._create(tab),
                referenceContent = $(that.contentElement(referenceTab.index()));

            each(inserted.tabs, function (idx) {
                referenceTab.after(this);
                referenceContent.after(inserted.contents[idx]);
            });

            updateFirstLast(that.tabGroup);
            that._updateContentElements();
        },

        /**
         * Removes the specified TabStrip item/s
         * @param {Selector} element Target item selector.
         * @example
         * tabStrip.remove("#Item1");
         */
        remove: function (element) {
            element = $(element);

            var that = this,
                content = $(that.contentElement(element.index()));

            content.remove();
            element.remove();

            that._updateContentElements();
        },

        _create: function (tab) {
            var plain = $.isPlainObject(tab),
                that = this, tabs, contents;

            if (plain || $.isArray(tab)) { // is JSON
                tabs = map(plain ? [ tab ] : tab, function (value, idx) {
                            return $(TabStrip.renderItem({
                                group: that.tabGroup,
                                item: extend(value, { index: idx })
                            }));
                        });
                contents = map(plain ? [ tab ] : tab, function (value, idx) {
                            return $(TabStrip.renderContent({
                                item: extend(value, { index: idx })
                            }));
                        });
            } else {
                tabs = $(tab);
                contents = $("<div class='" + CONTENT + "'/>");

                updateTabClasses(tabs);
            }

            return { tabs: tabs, contents: contents };
        },

        _toggleDisabled: function(element, enable) {
            $(element).each(function () {
                $(this)
                    .toggleClass(DEFAULTSTATE, enable)
                    .toggleClass(DISABLEDSTATE, !enable);
            });
        },

        _updateClasses: function() {
            var that = this,
                tabs, activeItem, activeTab;

            that.element.addClass("k-widget k-header k-tabstrip");

            that.tabGroup = that.element.children("ul").addClass("k-tabstrip-items k-reset");

            if (!that.tabGroup.length)
                that.tabGroup = $("<ul class='k-tabstrip-items k-reset'/>").appendTo(that.element);

            tabs = that.tabGroup.find("li").addClass("k-item");

            if (tabs.length) {
                activeItem = tabs.filter("." + ACTIVESTATE).index();
                activeTab = activeItem >= 0 ? activeItem : undefined;

                that.tabGroup // Remove empty text nodes
                    .contents()
                    .filter(function () { return (this.nodeType == 3 && !trim(this.nodeValue)); })
                    .remove();
            }

            tabs.eq(activeItem).addClass(TABONTOP);

            that.contentElements = that.element.children("div");

            that.contentElements
                .addClass(CONTENT)
                .eq(activeTab)
                .addClass(ACTIVESTATE)
                .css({ display: "block" });

            if (tabs.length) {
                updateTabClasses(tabs);

                updateFirstLast(that.tabGroup);
                that._updateContentElements();
            }
        },

        _updateContentElements: function() {
            var that = this,
                tabStripID = that.element.attr("id");

            that.contentElements = that.element.children("div");

            that.tabGroup.find(".k-item").each(function(idx) {
                var currentContent = that.contentElements.eq(idx),
                    id = tabStripID + "-" + (idx+1),
                    href = $(this).children("." + LINK).attr(HREF);

                if (!currentContent.length) {
                    $("<div id='"+ id +"' class='" + CONTENT + "'/>").appendTo(that.element);
                } else {
                    currentContent.attr("id", id);
                }
            });

            that.contentElements = that.element.children("div"); // refresh the contents
        },

        _toggleHover: function(e) {
            $(e.currentTarget).toggleClass(HOVERSTATE, e.type == MOUSEENTER);
        },

        _click: function (e) {
            var that = this,
                item = $(e.currentTarget),
                link = item.find("." + LINK),
                href = link.attr(HREF),
                collapse = that.options.collapsible,
                content = $(that.contentElement(item.index()));

            if (item.is("." + DISABLEDSTATE + (!collapse ? ",." + ACTIVESTATE : ""))) {
                e.preventDefault();
                return;
            }

            if ($("." + CONTENT, this.element).filter(function() { return $(this).data("animating"); }).length) {
                return;
            }

            if (that.trigger(SELECT, { item: item[0], contentElement: content[0] })) {
                e.preventDefault();
            } else {
                var isAnchor = link.data(CONTENTURL) || (href && (href.charAt(href.length - 1) == "#" || href.indexOf("#" + that.element[0].id + "-") != -1));

                if (!href || isAnchor) {
                    e.preventDefault();
                } else {
                    return;
                }

                if (collapse && item.is("." + ACTIVESTATE)) {
                    that.deactivateTab(item);
                    e.preventDefault();

                    return;
                }

                if (that.activateTab(item)) {
                    e.preventDefault();
                }

            }
        },

        deactivateTab: function (item) {
            var that = this,
                closeAnimation = that.options.animation.close,
                openAnimation = that.options.animation.open;

            closeAnimation = closeAnimation && "effects" in closeAnimation ? closeAnimation :
                                   extend( extend({ reverse: true }, openAnimation), { show: false, hide: true });

            if (kendo.size(openAnimation.effects)) {
                item.kendoAddClass(DEFAULTSTATE, { duration: openAnimation.duration });
                item.kendoRemoveClass(ACTIVESTATE, { duration: openAnimation.duration });
            } else {
                item.addClass(DEFAULTSTATE);
                item.removeClass(ACTIVESTATE);
            }

            that.contentElements
                    .filter("." + ACTIVESTATE)
                    .kendoStop(true, true)
                    .kendoAnimate( closeAnimation )
                    .removeClass(ACTIVESTATE);
        },

        activateTab: function (item) {
            var that = this,
                openAnimation = that.options.animation.open,
                closeAnimation = that.options.animation.close,
                neighbours = item.parent().children(),
                oldTab = neighbours.filter("." + ACTIVESTATE),
                itemIndex = neighbours.index(item);

            closeAnimation = closeAnimation && "effects" in closeAnimation ? closeAnimation : extend( extend({ reverse: true }, openAnimation), { show: false, hide: true });

            // deactivate previously active tab
            if (kendo.size(openAnimation.effects)) {
                oldTab.kendoRemoveClass(ACTIVESTATE, { duration: closeAnimation.duration });
                item.kendoRemoveClass(HOVERSTATE, { duration: closeAnimation.duration });
            } else {
                oldTab.removeClass(ACTIVESTATE);
                item.removeClass(HOVERSTATE);
            }

            // handle content elements
            var contentElements = that.contentElements;

            if (contentElements.length == 0) {
                return false;
            }

            var visibleContentElements = contentElements.filter("." + ACTIVESTATE);

            // find associated content element
            var content = $(that.contentElement(itemIndex));

            if (content.length == 0) {
                visibleContentElements
                    .removeClass( ACTIVESTATE )
                    .kendoStop(true, true)
                    .kendoAnimate( closeAnimation );
                return false;
            }

            var isAjaxContent = (item.children("." + LINK).data(CONTENTURL) || false) && content.is(EMPTY),
                showContentElement = function () {
                    oldTab.removeClass(TABONTOP);
                    item.addClass(TABONTOP) // change these directly to bring the tab on top.
                        .css("z-index");

                    if (kendo.size(openAnimation.effects)) {
                        oldTab.kendoAddClass(DEFAULTSTATE, { duration: openAnimation.duration });
                        item.kendoAddClass(ACTIVESTATE, { duration: openAnimation.duration });
                    } else {
                        oldTab.addClass(DEFAULTSTATE);
                        item.addClass(ACTIVESTATE);
                    }

                    content
                        .addClass(ACTIVESTATE)
                        .kendoStop(true, true)
                        .kendoAnimate( openAnimation );
                },
                showContent = function() {
                    if (!isAjaxContent) {
                        showContentElement();
                    } else
                        that.ajaxRequest(item, content, function () {
                            showContentElement();
                        });
                };

            visibleContentElements
                    .removeClass(ACTIVESTATE);

            if (visibleContentElements.length) {
                visibleContentElements
                    .kendoStop(true, true)
                    .kendoAnimate(extend( {
                        complete: showContent
                   }, closeAnimation ));
            } else {
                showContent();
            }

            return true;
        },

        contentElement: function (itemIndex) {
            if (isNaN(itemIndex - 0)) return;

            var contentElements = this.contentElements,
                idTest = new RegExp("-" + (itemIndex + 1) + "$");

            for (var i = 0, len = contentElements.length; i < len; i++) {
                if (idTest.test(contentElements[i].id)) {
                    return contentElements[i];
                }
            }
        },

        ajaxRequest: function (element, content, complete, url) {
            if (element.find(".k-loading").length)
                return;

            var that = this,
                link = element.find("." + LINK),
                data = {},
                statusIcon = null,
                loadingIconTimeout = setTimeout(function () {
                    statusIcon = $("<span class='k-icon k-loading'/>").prependTo(link)
                }, 100);

            $.ajax({
                type: "GET",
                cache: false,
                url: url || link.data(CONTENTURL) || link.attr(HREF),
                dataType: "html",
                data: data,

                error: function (xhr, status) {
                    if (that.trigger("error", { xhr: xhr, status: status })) {
                        this.complete();
                    }
                },

                complete: function () {
                    clearTimeout(loadingIconTimeout);
                    if (statusIcon !== null) {
                        statusIcon.remove();
                    }
                },

                success: function (data, textStatus) {
                    content.html(data);

                    if (complete) {
                        complete.call(that, content);
                    }

                    that.trigger(CONTENTLOAD, { item: element[0], contentElement: content[0] });
                }
            });
        }
    });

    // client-side rendering
    extend(TabStrip, {
        renderItem: function (options) {
            options = extend({ tabStrip: {}, group: {} }, options);

            var empty = templates.empty,
                item = options.item,
                tabStrip = options.tabStrip;

            return templates.item(extend(options, {
                image: item.imageUrl ? templates.image : empty,
                sprite: item.spriteCssClass ? templates.sprite : empty,
                itemWrapper: templates.itemWrapper
            }, rendering));
        },

        renderContent: function (options) {
            return templates.content(extend(options, rendering));
        }
    });

    kendo.ui.plugin(TabStrip);

})(jQuery);
(function($, undefined) {
    /**
    * @name kendo.ui.TimePicker.Description
    *
    * @section
    *   <p>
    *       The TimePicker widget allows the end user to select a value from a list of predefined values or to type a new value.
    *       It supports configurable options for the format, min and max time and the interval between predefined values in the list.
    *   </p>
    *
    *   <h3>Getting Started</h3>
    *
    * @exampleTitle Creating a TimePicker from existing INPUT element
    * @example
    * <!-- HTML -->
    * <input id="timepicker"/>
    *
    * @exampleTitle TimePicker initialization
    * @example
    *   $(document).ready(function(){
    *      $("#timepicker").kendoTimePicker();
    *   });
    * @section
    *  <p>
    *      When a TimePicker is initialized, it will automatically be displayed near the
    *      location of the used HTML element.
    *  </p>
    *  <h3>Configuring TimePicker behaviors</h3>
    *  <p>
    *      TimePicker provides configuration options that can be easily set during initialization.
    *      Among the properties that can be controlled:
    *  </p>
    *  <ul>
    *      <li>Selected time</li>
    *      <li>Minimum/Maximum time</li>
    *      <li>Define format</li>
    *      <li>Define interval between predefined values in the list</li>
    *  </ul>
    * @exampleTitle Create TimePicker with selected time and defined min and max time
    * @example
    *  $("#timepicker").kendoTimePicker({
    *      value: new Date(2000, 10, 10, 10, 0, 0),
    *      min: new Date(1950, 0, 1, 8, 0, 0),
    *      max: new Date(2049, 11, 31, 18, 0, 0)
    *  });
    *  @section
    * <p>
    *   TimePicker will set the value only if the entered time is valid and if it is in the defined range
    * </p>
    * @section
    *
    * @exampleTitle Define time format
    * @example
    *  $("#timepicker").kendoTimePicker({
    *      format: "hh:mm:ss tt"
    *  });
    *
    * @exampleTitle Define the interval between values in the list
    * @example
    *  $("#timepicker").kendoTimePicker({
    *      interval: 15 //in minutes
    *  });
    *
    */

    var kendo = window.kendo,
        touch = kendo.support.touch,
        keys = kendo.keys,
        ui = kendo.ui,
        Widget = ui.Widget,
        keys = kendo.keys,
        CHANGE = "change",
        CLICK = (touch ? "touchend" : "click"),
        DEFAULT = "k-state-default",
        DISABLED = "disabled",
        LI = "li",
        DIV = "<div/>",
        SPAN = "<span/>",
        FOCUSED = "k-state-focused",
        HOVER = "k-state-hover",
        HOVEREVENTS = "mouseenter mouseleave",
        MOUSEDOWN = (touch ? "touchstart" : "mousedown"),
        MS_PER_MINUTE = 60000,
        MS_PER_DAY = 86400000,
        SELECTED = "k-state-selected",
        STATEDISABLED = "k-state-disabled",
        proxy = $.proxy,
        DATE = Date,
        TODAY = new DATE();

    TODAY = new DATE(TODAY.getFullYear(), TODAY.getMonth(), TODAY.getDate(), 0, 0, 0);

    var TimeView = function(options) {
        var that = this, list;

        that.options = options;

        that.ul = $('<ul class="k-list k-reset"/>')
                    .css({ overflow: "auto"})
                    .bind(MOUSEDOWN, options.clearBlurTimeout)
                    .delegate(LI, CLICK, proxy(that._click, that))
                    .delegate(LI, "mouseenter", function() { $(this).addClass(HOVER); })
                    .delegate(LI, "mouseleave", function() { $(this).removeClass(HOVER); });

        that.list = $("<div class='k-list-container'/>").append(that.ul);

        that._popup();

        that.template = kendo.template('<li class="k-item" unselectable="on">#=data#</li>', { useWithBlock: false });
    }

    TimeView.prototype = {
        current: function(candidate) {
            var that = this;

            if (candidate !== undefined) {
                if (that._current) {
                    that._current.removeClass(SELECTED);
                }

                if (candidate) {
                    candidate = $(candidate);
                    candidate.addClass(SELECTED);
                    that.scroll(candidate[0]);
                }

                that._current = candidate;
            } else {
                return that._current;
            }
        },

        close: function() {
            this.popup.close();
        },

        open: function() {
            var that = this;

            if (!that.ul[0].firstChild) {
                that.refresh();
            }

            that.popup.open();
            if (that._current) {
                that.scroll(that._current[0]);
            }
        },

        refresh: function() {
            var that = this,
                options = that.options,
                format = options.format,
                min = options.min,
                max = options.max,
                msMin = getMilliseconds(min),
                msMax = getMilliseconds(max),
                msInterval = options.interval * MS_PER_MINUTE,
                toString = kendo.toString,
                template = that.template,
                start = new DATE(min),
                length = MS_PER_DAY / msInterval,
                idx = 0, length,
                html = "";

            if (msMin != msMax) {
                if (msMin > msMax) {
                    msMax += MS_PER_DAY;
                }
                length = (msMax - msMin) / msInterval + 1;
            }

            for (; idx < length; idx++) {
                if (idx) {
                    setTime(start, msInterval);
                }

                if (msMax && getMilliseconds(start) > msMax) {
                    start = new DATE(max);
                }

                html += template(toString(start, format));
            }

            that.ul[0].innerHTML = html;

            that._height(length);

            that.select(that._value);
        },

        scroll: function(item) {
            if (!item) return;

            var ul = this.ul[0],
                itemOffsetTop = item.offsetTop,
                itemOffsetHeight = item.offsetHeight,
                ulScrollTop = ul.scrollTop,
                ulOffsetHeight = ul.clientHeight,
                bottomDistance = itemOffsetTop + itemOffsetHeight;

            ul.scrollTop = ulScrollTop > itemOffsetTop
                        ? itemOffsetTop
                        : bottomDistance > (ulScrollTop + ulOffsetHeight)
                        ? bottomDistance - ulOffsetHeight
                        : ulScrollTop;
        },

        select: function(li) {
            var that = this,
                current = that._current;

            if (typeof li === "string") {
                if (!current || current.text() !== li) {
                    li = $.grep(that.ul[0].childNodes, function(node) {
                        return (node.textContent || node.innerText) == li;
                    });

                    li = li[0] ? li : null;
                } else {
                    li = current;
                }
            }

            that.current(li);
        },

        toggle: function() {
            var that = this;

            if (that.popup.visible()) {
                that.close();
            } else {
                that.open();
            }
        },

        value: function(value) {
            var that = this;

            that._value = value;
            if (that.ul[0].firstChild) {
                that.select(value);
            }
        },

        _click: function(e) {
            var that = this,
                li = $(e.currentTarget);

            that.select(li);
            that.options.change(li.text(), true);
            that.close();
        },

        _height: function(length) {
            if (length) {
                var that = this,
                    list = that.list,
                    parent = list.parent(".k-animation-container"),
                    height = that.options.height;

                list.add(parent)
                    .show()
                    .height(that.ul[0].scrollHeight > height ? height : "auto")
                    .hide();
            }
        },

        _popup: function() {
            var that = this,
                list = that.list,
                options = that.options,
                anchor = options.anchor,
                width;

            that.popup = new ui.Popup(list, {
                anchor: anchor,
                open: options.open,
                close: options.close,
                animation: options.animation
            });

            width = anchor.outerWidth() - (list.outerWidth() - list.width());

            list.css({
                fontFamily: anchor.css("font-family"),
                width: width
            });
        },

        move: function(e) {
            var that = this,
                key = e.keyCode,
                ul = that.ul[0],
                current = that._current,
                down = key === keys.DOWN;

            if (key === keys.UP || down) {
                if (e.altKey) {
                    that.toggle(down);
                    return;
                } else if (down) {
                    current = current ? current[0].nextSibling : ul.firstChild;
                } else {
                    current = current ? current[0].previousSibling : ul.lastChild;
                }

                if (current) {
                    that.select(current);
                }

                that.options.change(that._current.text());
                e.preventDefault();

            } else if (key === keys.ENTER || key === keys.TAB || key === keys.ESC) {
                e.preventDefault();
                that.close();
            }
        }
    };

    function setTime(date, time) {
        var tzOffsetBefore = date.getTimezoneOffset(),
        resultDATE = new DATE(date.getTime() + time),
        tzOffsetDiff = resultDATE.getTimezoneOffset() - tzOffsetBefore;

        date.setTime(resultDATE.getTime() + tzOffsetDiff * MS_PER_MINUTE);
    }

    function getMilliseconds(date) {
        return date.getHours() * 60 * MS_PER_MINUTE + date.getMinutes() * MS_PER_MINUTE + date.getSeconds() * 1000 + date.getMilliseconds();
    }

    function isInRange(value, min, max) {
        var msMin = getMilliseconds(min),
            msMax = getMilliseconds(max),
            msValue;

        if (!value || msMin == msMax) {
            return true;
        }

        msValue = getMilliseconds(value);

        if (msMin > msValue) {
            msValue += MS_PER_DAY;
        }

        if (msMax < msMin) {
            msMax += MS_PER_DAY;
        }

        return msValue >= msMin && msValue <= msMax
    }

    kendo.TimeView = TimeView;

    var TimePicker = Widget.extend(/** @lends kendo.ui.TimePicker.prototype */{
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Date} [value] <null> Specifies the selected time.
         * @option {Date} [min] <00:00> Specifies the start value in the popup list.
         * @option {Date} [max] <00:00> Specifies the end value in the popup list.
         * @option {String} [format] <h:mm tt> Specifies the format, which is used to parse value set with value() method.
         * @option {Number} [interval] <30> Specifies the interval, between values in the popup list, in minutes.
         */
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            element = that.element;
            options = that.options;

            options.format = options.format || kendo.culture().calendar.patterns.t;

            that._wrapper();

            that.timeView = new TimeView($.extend({}, options, {
                anchor: that.wrapper,
                format: options.format,
                change: function(value, trigger) {
                    if (trigger) {
                        that._change(value);
                    } else {
                        element.val(value);
                    }
                },
                clearBlurTimeout: proxy(that._clearBlurTimeout, that)
            }));

            that._icon();

            element.addClass("k-input")
                .bind({
                    keydown: proxy(that._keydown, that),
                    focus: function(e) {
                        clearTimeout(that._bluring);
                        that._inputWrapper.addClass(FOCUSED);
                    },
                    blur: proxy(that._blur, that)
                })
                .closest("form")
                .bind("reset", function() {
                    that.value(element[0].defaultValue);
                });

            /**
            * Fires when the value is changed
            * @name kendo.ui.TimePicker#change
            * @event
            * @param {Event} e
            */
            /**
            * Fires when the popup is opened
            * @name kendo.ui.TimePicker#open
            * @event
            * @param {Event} e
            */
            /**
            * Fires when the popup is closed
            * @name kendo.ui.TimePicker#close
            * @event
            * @param {Event} e
            */
            that.bind(CHANGE, options);

            that.enable(!element.is('[disabled]'));
            that.value(options.value || element.val());
        },

        options: {
            name: "TimePicker",
            min: TODAY,
            max: TODAY,
            value: null,
            interval: 30,
            height: 200
        },

        /**
        * Enable/Disable the timepicker widget.
        * @param {Boolean} enable The argument, which defines whether to enable/disable the timepicker.
        * @example
        * var timepicker = $("timepicker").data("kendoTimePicker");
        *
        * // disables the timepicker
        * timepicker.enable(false);
        *
        * // enables the timepicker
        * timepicker.enable(true);
        */
        enable: function(enable) {
            var that = this,
                arrow = that._arrow,
                element = that.element,
                wrapper = that._inputWrapper;

            arrow.unbind(CLICK)
                 .unbind(MOUSEDOWN);

            if (enable === false) {
                wrapper
                    .removeClass(DEFAULT)
                    .addClass(STATEDISABLED)
                    .unbind(HOVEREVENTS);

                element.attr(DISABLED, DISABLED);
            } else {
                wrapper
                    .removeClass(STATEDISABLED)
                    .addClass(DEFAULT)
                    .bind(HOVEREVENTS, that._toggleHover);

                element
                    .removeAttr(DISABLED);

                arrow.bind(CLICK, proxy(that._click, that))
                     .bind(MOUSEDOWN, proxy(that._clearBlurTimeout, that))
            }
        },

        /**
        * Closes the popup.
        * @name kendo.ui.TimePicker#close
        * @function
        * @example
        * timepicker.close();
        */
        close: function() {
            this.timeView.close();
        },

        /**
        * Opens the popup.
        * @name kendo.ui.TimePicker#open
        * @function
        * @example
        * timepicker.open();
        */
        open: function() {
            this.timeView.open();
        },

        /**
        * Gets/Sets the min value of the timepicker.
        * @param {Date|String} value The min time to set.
        * @returns {Date} The min value of the timepicker.
        * @example
        * var timepicker = $("#timepicker").data("kendoTimePicker");
        *
        * // get the min value of the timepicker.
        * var min = timepicker.min();
        *
        * // set the min value of the timepicker.
        * timepicker.min(new Date(1900, 0, 1, 10, 0, 0));
        */
        min: function (value) {
            return this._option("min", value);
        },

        /**
        * Gets/Sets the max value of the timepicker.
        * @param {Date|String} value The max time to set.
        * @returns {Date} The max value of the timepicker.
        * @example
        * var timepicker = $("#timepicker").data("kendoTimePicker");
        *
        * // get the max value of the timepicker.
        * var max = timepicker.max();
        *
        * // set the max value of the timepicker.
        * timepicker.max(new Date(1900, 0, 1, 18, 0, 0));
        */
        max: function (value) {
            return this._option("max", value);
        },

        /**
        * Gets/Sets the value of the timepicker.
        * @param {Date|String} value The value to set.
        * @returns {Date} The value of the timepicker.
        * @example
        * var timepicker = $("#timepicker").data("kendoTimePicker");
        *
        * // get the value of the timepicker.
        * var value = timepicker.value();
        *
        * // set the value of the timepicker.
        * timepicker.value("10:00 AM"); //parse "10:00 AM" time and selects it in the popup.
        */
        value: function(value) {
            var that = this;

            if (value === undefined) {
                return that._value;
            }

            that._old = that._update(value);
        },

        _blur: function() {
            var that = this;

            that._bluring = setTimeout(function() {
                that._change(that.element.val());
                if (!touch) {
                    that.close();
                }
                that._inputWrapper.removeClass(FOCUSED);
            }, 100);
        },

        _clearBlurTimeout: function() {
            var that = this;
            setTimeout(function() {
                clearTimeout(that._bluring);
                that.element.focus();
            });
        },

        _click: function() {
            this.timeView.toggle();
        },

        _change: function(value) {
            var that = this;

            value = that._update(value);

            if (+that._old != +value) {
                that._old = value;
                that.trigger(CHANGE);

                // trigger the DOM change event so any subscriber gets notified
                that.element.trigger(CHANGE);
            }
        },

        _icon: function() {
            var that = this,
                element = that.element,
                arrow;

            arrow = element.next("span.k-select");

            if (!arrow[0]) {
                arrow = $('<span class="k-select"><span class="k-icon k-icon-clock">select</span></span>').insertAfter(element);
            }

            that._arrow = arrow;
        },

        _keydown: function(e) {
            var that = this,
                key = e.keyCode,
                enter = key == keys.ENTER,
                timeView = that.timeView;

            if (timeView.popup.visible() || e.altKey || enter) {
                timeView.move(e);
            }

            if (enter) {
                that._change(that.element.val());
            }
        },

        _option: function(option, value) {
            var that = this,
                options = that.options;

            if (value === undefined) {
                return options[option];
            }

            value = that._parse(value);

            if (!value) {
                return;
            }

            value = new DATE(value);

            options[option] = value;
            that.timeView.options[option] = value;
            that.timeView.refresh();
        },

        _parse: function(value) {
            var that = this,
                current = that._value || TODAY;

            if (value instanceof DATE) {
                return value;
            }

            value = kendo.parseDate(value, that.options.format);

            if (value) {
                value = new DATE(current.getFullYear(),
                                 current.getMonth(),
                                 current.getDate(),
                                 value.getHours(),
                                 value.getMinutes(),
                                 value.getSeconds(),
                                 value.getMilliseconds());
            }

            return value;
        },

        _toggleHover: function(e) {
            if (!touch) {
                $(e.currentTarget).toggleClass(HOVER, e.type === "mouseenter");
            }
        },

        _update: function(value) {
            var that = this,
                current = that._value,
                options = that.options,
                date = that._parse(value),
                text = kendo.toString(date, options.format);

            if (!isInRange(date, options.min, options.max)) {
                date = null;
            }

            that._value = date;
            that.element.val(date ? text : value);
            that.timeView.value(text);

            return date;
        },

        _wrapper: function() {
            var that = this,
                element = that.element,
                wrapper;

            wrapper = element.parents(".k-timepicker");

            if (!wrapper[0]) {
                wrapper = element.wrap(SPAN).parent().addClass("k-picker-wrap k-state-default");
                wrapper = wrapper.wrap(SPAN).parent();
            }

            wrapper[0].style.cssText = element[0].style.cssText;
            element.css({
                width: "100%",
                height: "auto"
            });

            that.wrapper = wrapper.addClass("k-widget k-timepicker k-header");
            that._inputWrapper = $(wrapper[0].firstChild);
        }
    });

    ui.plugin(TimePicker);

})(jQuery);
(function($, undefined){
    /**
     * @name kendo.ui.TreeView.Description
     *
     * @section
     * <p>The TreeView widget displays hierarchical data in a traditional tree structure,
     * with support for interactive drag-and-drop reordering operations.
     * A TreeView can be defined statically using HTML lists,
     * or it can be dynamically bound to hierarchical data.</p>
     *
     * <h3>Getting Started</h3>
     *
     * <p>There are two primary ways to create a TreeView:</p>
     *
     * <ol>
     *     <li>Define a hierarchical list with static HTML</li>
     *     <li>Use dynamic data binding</li>
     * </ol>
     *
     * <p>Static HTML definition is appropriate for small hierarchies and for data that does not change frequently.
     * Data binding should be used for larger data sets and for data that changes frequently.</p>
     *
     * <h3>Creating a treeview from HTML</h3>
     * @exampleTitle Create a hierarchical HTML list
     * @example
     * <ul id="treeview">
     *     <li>Item 1
     *         <ul>
     *             <li>Item 1.1</li>
     *             <li>Item 1.2</li>
     *         </ul>
     *     </li>
     *     <li>Item 2</li>
     * </ul>
     *
     * @exampleTitle Initialize the TreeView using a jQuery selector
     * @example var treeview = $("#treeview").kendoTreeView();
     *
     * @section <h3>Creating a TreeView with data binding (local data source)</h3>
     *
     * @exampleTitle Create a hierarchical HTML list
     * @example
     * <div id="treeview"></div>
     *
     * @exampleTitle Initialize and bind the TreeView
     * @example
     * $("#treeview").kendoTreeView({
     *     dataSource: [
     *         { text: "Item 1", items: [
     *             { text: "Item 1.1" },
     *             { text: "Item 1.2" }
     *         ]},
     *         { text: "Item 2" }
     *     ]
     * });
     *
     * @section <h3>Configuring TreeView behavior</h3>
     * <p> A number of TreeView behaviors can be easily controlled by simple configuration properties,
     * such as animation behaviors and drag-and-drop behaviors.
     * Refer to the demo Configuration tab for more API details.</p>
     *
     * @exampleTitle Enabling TreeView node drag-and-drop
     * @example
     * $("#treeview").kendoTreeView({
     *     dragAndDrop: true
     * });
     *
     * @section When drag-and-drop is enabled, TreeView nodes can be dragged and dropped between all levels,
     * with useful tooltips helping indicate where the node will be dropped.
     */
    var kendo = window.kendo,
        ui = kendo.ui,
        extend = $.extend,
        template = kendo.template,
        Widget = ui.Widget,
        proxy = $.proxy,
        SELECT = "select",
        EXPAND = "expand",
        COLLAPSE = "collapse",
        DRAGSTART = "dragstart",
        DRAG = "drag",
        NODEDRAGCANCELLED = "nodeDragCancelled",
        DROP = "drop",
        DRAGEND = "dragend",
        CLICK = "click",
        VISIBILITY = "visibility",
        TSTATEHOVER = "k-state-hover",
        TTREEVIEW = "k-treeview",
        TITEM = "k-item",
        VISIBLE = ":visible",
        NODE = ".k-item",
        SUBGROUP = ">.k-group,>.k-animation-container>.k-group",
        NODECONTENTS = SUBGROUP + ",>.k-content,>.k-animation-container>.k-content",
        templates, rendering, TreeView;

    function updateNodeHtml(node) {
        var wrapper = node.find(">div"),
            subGroup = node.find(">ul"),
            toggleButton = wrapper.find(">.k-icon"),
            innerWrapper = wrapper.find(">.k-in");

        if (!wrapper.length) {
            wrapper = $("<div />").prependTo(node);
        }

        if (!toggleButton.length && subGroup.length) {
            toggleButton = $("<span class='k-icon' />").prependTo(wrapper);
        } else if (!subGroup.length || !subGroup.children().length) {
            toggleButton.remove();
            subGroup.remove();
        }

        if (!innerWrapper.length) {
            innerWrapper = $("<span class='k-in' />").appendTo(wrapper)[0];

            // move all non-group content in the k-in container
            currentNode = wrapper[0].nextSibling;
            innerWrapper = wrapper.find(".k-in")[0];

            while (currentNode && currentNode.nodeName.toLowerCase() != "ul") {
                tmp = currentNode;
                currentNode = currentNode.nextSibling;
                innerWrapper.appendChild(tmp);
            }
        }
    }

    function updateNodeClasses(node, groupData, nodeData) {
        var wrapper = node.find(">div"),
            subGroup = node.find(">ul")

        if (!nodeData) {
            nodeData = {
                expanded: !(subGroup.css("display") == "none"),
                index: node.index(),
                enabled: !wrapper.find(">.k-in").hasClass("k-state-disabled")
            };
        }

        if (!groupData) {
            groupData = {
                firstLevel: node.parent().parent().hasClass(TTREEVIEW),
                length: node.parent().children().length
            };
        }

        // li
        node.removeClass("k-first k-last")
            .addClass(rendering.wrapperCssClass(groupData, nodeData));

        // div
        wrapper.removeClass("k-top k-mid k-bot")
               .addClass(rendering.cssClass(groupData, nodeData));

        // toggle button
        if (subGroup.length) {
            wrapper.find(">.k-icon").removeClass("k-plus k-minus k-plus-disabled k-minus-disabled")
                .addClass(rendering.toggleButtonClass(nodeData));

            subGroup.addClass("k-group");
        }
    }


    templates = {
        dragClue: template("<div class='k-header k-drag-clue'><span class='k-icon k-drag-status'></span>#= text #</div>"),
        group: template(
            "<ul class='#= groupCssClass(group) #'#= groupAttributes(group) #>" +
                "#= renderItems(data) #" +
            "</ul>"
        ),
        itemWrapper: template(
            "<div class='#= cssClass(group, item) #'>" +
                "#= toggleButton(data) #" +
                "<#= tag(item) # class='#= textClass(item) #'#= textAttributes(item) #>" +
                    "#= image(item) ##= sprite(item) ##= text(item) #" +
                "</#= tag(item) #>" +
            "</div>"
        ),
        item: template(
            "<li class='#= wrapperCssClass(group, item) #'>" +
                "#= itemWrapper(data) #" +
                "# if (item.items) { #" +
                "#= subGroup({ items: item.items, treeview: treeview, group: { expanded: item.expanded } }) #" +
                "# } #" +
            "</li>"
        ),
        image: template("<img class='k-image' alt='' src='#= imageUrl #' />"),
        toggleButton: template("<span class='#= toggleButtonClass(item) #'></span>"),
        sprite: template("<span class='k-sprite #= spriteCssClass #'></span>"),
        empty: template("")
    };

    TreeView = Widget.extend(/** @lends kendo.ui.TreeView.prototype */ {
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Array} [dataSource] The data that the TreeView will be bound to.
         * @option {Object} [animation] A collection of {Animation} objects, used to change default animations. A value of false will disable all animations in the widget.
         * @option {Boolean} [dragAndDrop] <false> Controls whether the treeview nodes can be dragged and rearranged.
         * @option {Animation} [animation.expand] The animation that will be used when expanding items.
         * @option {Animation} [animation.collapse] The animation that will be used when collapsing items.
         */
        init: function (element, options) {
            var that = this,
                clickableItems = ".k-in:not(.k-state-selected,.k-state-disabled)",
                dataInit;

            options = $.isArray(options) ? (dataInit = true, { dataSource: options }) : options;

            Widget.prototype.init.call(that, element, options);

            element = that.element;
            options = that.options;

            if (options.animation === false) {
                options.animation = {
                    expand: { show: true, effects: {} },
                    collapse: { hide: true, effects: {} }
                };
            }

            // render treeview if it's not already rendered
            if (!element.hasClass(TTREEVIEW)) {
                that._wrapper();

                if (!that.root.length) { // treeview initialized from empty element
                    that.root = that.wrapper.html(TreeView.renderGroup({
                        items: options.dataSource,
                        group: {
                            firstLevel: true,
                            expanded: true
                        },
                        treeview: {}
                    })).children("ul");
                } else {
                    that._group(that.wrapper);
                }
            } else {
                // otherwise just initialize properties
                that.wrapper = element;
                that.root = element.children("ul").eq(0);
            }

            that.wrapper
                .delegate(".k-in.k-state-selected", "mouseenter", function(e) { e.preventDefault(); })
                .delegate(clickableItems, "mouseenter", function () { $(this).addClass(TSTATEHOVER); })
                .delegate(clickableItems, "mouseleave", function () { $(this).removeClass(TSTATEHOVER); })
                .delegate(clickableItems, CLICK, proxy(that._nodeClick, that))
                .delegate("div:not(.k-state-disabled) .k-in", "dblclick", proxy(that._toggleButtonClick, that))
                .delegate(".k-plus,.k-minus", CLICK, proxy(that._toggleButtonClick, that));

            if (options.dragAndDrop) {
                that.bind([
                        /**
                         * Fires before the dragging of a node starts.
                         * @name kendo.ui.TreeView#dragstart
                         * @event
                         * @param {Event} e
                         * @param {Node} e.sourceNode The node that will be dragged.
                         */
                        DRAGSTART,
                        /**
                         * Fires while a node is being dragged.
                         * @name kendo.ui.TreeView#drag
                         * @event
                         * @param {Event} e
                         * @param {Node} e.sourceNode The node that is being dragged.
                         * @param {DomElement} e.dropTarget The element that the node is placed over.
                         * @param {Integer} e.pageX The x coordinate of the mouse.
                         * @param {Integer} e.pageY The y coordinate of the mouse.
                         * @param {String} e.statusClass The status that the drag clue shows.
                         * @param {Function} e.setStatusClass Allows a custom drag clue status to be set.
                         */
                        DRAG,
                        /**
                         * Fires when a node is being dropped.
                         * @name kendo.ui.TreeView#drop
                         * @event
                         * @param {Event} e
                         * @param {Node} e.sourceNode The node that is being dropped.
                         * @param {Node} e.destinationNode The node that the sourceNode is being dropped upon.
                         * @param {Boolean} e.valid Whether this drop operation is permitted
                         * @param {Function} e.setValid Allows the drop to be prevented.
                         * @param {DomElement} e.dropTarget The element that the node is placed over.
                         * @param {String} e.dropPosition Shows where the new sourceLocation would be.
                         */
                        DROP,
                        /**
                         * Fires after a node is has been dropped.
                         * @name kendo.ui.TreeView#dragend
                         * @event
                         * @param {Event} e
                         * @param {Node} e.sourceNode The node that is being dropped.
                         * @param {Node} e.destinationNode The node that the sourceNode is being dropped upon.
                         * @param {String} e.dropPosition Shows where the new sourceLocation would be.
                         */
                        DRAGEND
                    ], options);

                that.dragging = new TreeViewDragAndDrop(that);
            }

            that.bind([
                /**
                 * Fires before a subgroup gets expanded.
                 * @name kendo.ui.TreeView#expand
                 * @event
                 * @param {Event} e
                 * @param {Node} e.node The expanded node
                 */
                EXPAND,
                /**
                 * Fires before a subgroup gets collapsed.
                 * @name kendo.ui.TreeView#collapse
                 * @event
                 * @param {Event} e
                 * @param {Node} e.node The collapsed node
                 */
                COLLAPSE,
                /**
                 * Fires when a node gets selected.
                 * @name kendo.ui.TreeView#select
                 * @event
                 * @param {Event} e
                 * @param {Node} e.node The selected node
                 */
                SELECT
            ], options);
        },

        options: {
            name: "TreeView",
            dataSource: {},
            animation: {
                expand: {
                    effects: "expandVertical",
                    duration: 200,
                    show: true
                },
                collapse: {
                    duration: 100,
                    show: false,
                    hide: true
                }
            }
        },

        _trigger: function (eventName, node) {
            return this.trigger(eventName, {
                node: node.closest(NODE)[0]
            });
        },

        _toggleButtonClick: function (e) {
            this.toggle($(e.target).closest(NODE));
        },

        _nodeClick: function (e) {
            var that = this,
                node = $(e.target),
                contents = node.closest(NODE).find(NODECONTENTS),
                href = node.attr("href"),
                shouldNavigate;

            if (href) {
                shouldNavigate = href == "#" || href.indexOf("#" + this.element.id + "-") >= 0;
            } else {
                shouldNavigate = contents.length && !contents.children().length;
            }

            if (shouldNavigate) {
                e.preventDefault();
            }

            if (!node.hasClass(".k-state-selected") && !that._trigger("select", node)) {
                that.select(node);
            }
        },

        _wrapper: function() {
            var that = this,
                element = that.element,
                wrapper, root,
                wrapperClasses = "k-widget k-treeview k-reset";

            if (element.is("div")) {
                wrapper = element.addClass(wrapperClasses);
                root = wrapper.children("ul").eq(0);
            } else { // element is ul
                wrapper = element.wrap('<div class="' + wrapperClasses + '" />').parent();
                root = element;
            }

            that.wrapper = wrapper;
            that.root = root;
        },

        _group: function(item) {
            var that = this,
                firstLevel = item.hasClass(TTREEVIEW),
                group = {
                    firstLevel: firstLevel,
                    expanded: firstLevel || item.attr(kendo.attr("expanded")) === "true"
                },
                groupElement = item.find("> ul");

            groupElement
                .addClass(rendering.groupCssClass(group))
                .css("display", group.expanded ? "" : "none");

            that._nodes(groupElement, group);
        },

        _nodes: function(groupElement, groupData) {
            var that = this,
                nodes = groupElement.find("> li"),
                nodeData;

            groupData = extend({ length: nodes.length }, groupData);

            nodes.each(function(i, node) {
                node = $(node);

                nodeData = { index: i, expanded: node.attr(kendo.attr("expanded")) === "true" };

                updateNodeHtml(node);

                updateNodeClasses(node, groupData, nodeData);

                // iterate over child nodes
                that._group(node);
            });
        },

        _processNodes: function(nodes, callback) {
            var that = this;
            that.element.find(nodes).each(function(index, item) {
                callback.call(that, index, $(item).closest(NODE));
            });
        },

        /**
         * Expands nodes.
         * @param {Selector} nodes The nodes that are to be expanded.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // expands the node with id="firstItem"
         * treeview.expand(document.getElementById("firstItem"));
         *
         * // expands all nodes
         * treeview.expand(".k-item");
         */
        expand: function (nodes) {
            this._processNodes(nodes, function (index, item) {
                var contents = item.find(NODECONTENTS);

                if (contents.length > 0 && !contents.is(VISIBLE)) {
                    this.toggle(item);
                }
            });
        },

        /**
         * Collapses nodes.
         * @param {Selector} nodes The nodes that are to be collapsed.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // collapse the node with id="firstItem"
         * treeview.collapse(document.getElementById("firstItem"));
         *
         * // collapse all nodes
         * treeview.collapse(".k-item");
         */
        collapse: function (nodes) {
            this._processNodes(nodes, function (index, item) {
                var contents = item.find(NODECONTENTS);

                if (contents.length > 0 && contents.is(VISIBLE)) {
                    this.toggle(item);
                }
            });
        },

        /**
         * Enables or disables nodes.
         * @param {Selector} nodes The nodes that are to be enabled/disabled.
         * @param {Boolean} [enable=true] Whether the nodes should be enabled or disabled.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // disable the node with id="firstItem"
         * treeview.enable(document.getElementById("firstItem"), false);
         *
         * // enable all nodes
         * treeview.enable(".k-item");
         */
        enable: function (nodes, enable) {
            enable = arguments.length == 2 ? !!enable : true;

            this._processNodes(nodes, function (index, item) {
                var isCollapsed = !item.find(NODECONTENTS).is(VISIBLE);

                if (!enable) {
                    this.collapse(item);
                    isCollapsed = true;
                }

                item.find(">div")
                        .find(">.k-in")
                            .toggleClass("k-state-default", enable)
                            .toggleClass("k-state-disabled", !enable)
                        .end()
                        .find(">.k-icon")
                            .toggleClass("k-plus", isCollapsed && enable)
                            .toggleClass("k-plus-disabled", isCollapsed && !enable)
                            .toggleClass("k-minus", !isCollapsed && enable)
                            .toggleClass("k-minus-disabled", !isCollapsed && !enable);
            });
        },

        /**
         * Gets/sets the selected node.
         * @param {Selector} [node] The node that should be selected.
         * @returns {Node} The currently selected node
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // select the node with id="firstItem"
         * treeview.select(document.getElementById("firstItem"));
         *
         * // get the currently selected node
         * var selectedNode = treeview.select();
         */
        select: function (node) {
            var element = this.element;

            if (arguments.length == 0) {
                return element.find(".k-state-selected").closest(NODE);
            }

            node = $(node).closest(NODE);

            if (node.length) {
                element.find(".k-in").removeClass("k-state-hover k-state-selected");

                node.find(".k-in:first").addClass("k-state-selected");
            }
        },

        /**
         * Toggles a node between expanded and collapsed state.
         * @param {jQueryObject} node The node that should be toggled.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // toggle the node with id="firstItem"
         * treeview.toggle(document.getElementById("firstItem"));
         */
        toggle: function (node) {
            node = $(node);

            if (node.find(".k-minus,.k-plus").length == 0) {
                return;
            }

            if (node.find("> div > .k-state-disabled").length) {
                return;
            }

            var that = this,
                contents = node.find(NODECONTENTS),
                isExpanding = !contents.is(VISIBLE),
                animationSettings = that.options.animation || {},
                animation = animationSettings.expand,
                collapse = animationSettings.collapse,
                hasCollapseAnimation = collapse && 'effects' in collapse;

            if (contents.data("animating"))
                return;

            if (!isExpanding) {
                animation = hasCollapseAnimation ? collapse
                                    : extend({ reverse: true }, animation, { show: false, hide: true });
            }

            if (contents.children().length > 0) {
                if (!that._trigger(isExpanding ? "expand" : "collapse", node)) {
                    node.find("> div > .k-icon")
                        .toggleClass("k-minus", isExpanding)
                        .toggleClass("k-plus", !isExpanding);

                    if (!isExpanding) {
                        contents.css("height", contents.height()).css("height");
                    }

                    contents.kendoStop(true, true).kendoAnimate(extend(animation, {
                        complete: function() {
                            if (isExpanding) {
                                contents.css("height", "");
                            }
                        }
                    }));
                }
            }
        },

        /**
         * Get the text of a node.
         * @param {Selector} node The node that you need the text for.
         * @returns {String} The text of the node.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // get the text of the node with id="firstItem"
         * var nodeText = treeview.text(document.getElementById("firstItem"));
         */
        text: function (node) {
            return $(node).closest(NODE).find(">div>.k-in").text();
        },

        _insertNode: function(nodeData, index, parentNode, group, insertCallback) {
            var that = this,
                updatedGroupLength = group.children().length + 1,
                fromNodeData = $.isPlainObject(nodeData),
                groupData = {
                    firstLevel: parentNode.hasClass(TTREEVIEW),
                    expanded: true,
                    length: updatedGroupLength
                }, node;

            if (fromNodeData) {
                node = $(TreeView.renderItem({
                    group: groupData,
                    item: extend(nodeData, { index: index })
                }));
            } else {
                node = $(nodeData);

                if (group.children()[index - 1] == node[0]) {
                    return node;
                }

                if (node.closest(".k-treeview")[0] == that.wrapper[0]) {
                    that.remove(node);
                }
            }

            if (!group.length) {
                group = $(TreeView.renderGroup({
                    group: groupData
                })).appendTo(parentNode);
            }

            insertCallback(node, group);

            if (parentNode.hasClass("k-item")) {
                updateNodeHtml(parentNode);
                updateNodeClasses(parentNode);
            }

            if (!fromNodeData) {
                updateNodeClasses(node);
            }

            updateNodeClasses(node.prev());
            updateNodeClasses(node.next());

            return node;
        },

        /**
         * Inserts a node after another node.
         * @param {NodeData} nodeData JSON that specifies the node data, or a reference to a node in the TreeView.
         * @param {Node} referenceNode The node that will be before the newly appended node.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // inserts a new node with the text "new node" after the node with id="firstItem"
         * treeview.insertAfter({ text: "new node" }, document.getElementById("firstItem"));
         *
         * // moves the node with id="secondNode" after the node with id="firstItem"
         * treeview.insertAfter(document.getElementById("secondNode"), document.getElementById("firstItem"));
         */
        insertAfter: function (nodeData, referenceNode) {
            var group = referenceNode.parent();

            return this._insertNode(nodeData, referenceNode.index() + 1, group.parent(), group, function(item, group) {
                item.insertAfter(referenceNode);
            });
        },

        /**
         * Inserts a node before another node.
         * @param {NodeData} nodeData JSON that specifies the node data, or a reference to a node in the TreeView.
         * @param {Node} referenceNode The node that will be after the newly appended node.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // inserts a new node with the text "new node" before the node with id="firstItem"
         * treeview.insertBefore({ text: "new node" }, document.getElementById("firstItem"));
         *
         * // moves the node with id="secondNode" before the node with id="firstItem"
         * treeview.insertBefore(document.getElementById("secondNode"), document.getElementById("firstItem"));
         */
        insertBefore: function (nodeData, referenceNode) {
            var group = referenceNode.parent();

            return this._insertNode(nodeData, referenceNode.index(), group.parent(), group, function(item, group) {
                item.insertBefore(referenceNode);
            });
        },

        /**
         * Appends a node to a treeview group.
         * @param {NodeData} nodeData JSON that specifies the node data, or a reference to a node in the TreeView.
         * @param {Node} [parentNode] The node that will contain the newly appended node. If not specified, the new node will be appended to the root group of the treeview.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // appends a new node with the text "new node" to the node with id="firstItem"
         * treeview.append({ text: "new node" }, document.getElementById("firstItem"));
         *
         * // moves the node with id="secondNode" as a last child of the node with id="firstItem"
         * treeview.append(document.getElementById("secondNode"), document.getElementById("firstItem"));
         */
        append: function (nodeData, parentNode) {
            parentNode = parentNode || this.element;

            var group = parentNode.find(SUBGROUP);

            return this._insertNode(nodeData, group.children().length, parentNode, group, function(item, group) {
                item.appendTo(group);
            });
        },

        /**
         * Removes a node
         * @param {Selector} node The node that is to be removed.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // remove the node with id="firstItem"
         * treeview.remove(document.getElementById("firstItem"));
         */
        remove: function (node) {
            node = $(node);

            var that = this,
                parentNode = node.parent().parent(),
                prevSibling = node.prev(),
                nextSibling = node.next();

            node.remove();

            if (parentNode.hasClass("k-item")) {
                updateNodeHtml(parentNode);
                updateNodeClasses(parentNode);
            }

            updateNodeClasses(prevSibling);
            updateNodeClasses(nextSibling);
        },

        /**
         * Searches the treeview for a node that has specific text.
         * @param {String} text The text that is being searched for.
         * @returns {jQueryObject} All nodes that have the text.
         * @example
         * var treeview = $("#treeview").data("kendoTreeView");
         *
         * // searches the treeview for the item that has the text "foo"
         * var foundNode = treeview.findByText("foo");
         */
        findByText: function(text) {
            return $(this.element).find(".k-in").filter(function(i, element) {
                return $(element).text() == text;
            }).closest(NODE);
        }
    });

    function TreeViewDragAndDrop(treeview) {
        var that = this;

        that.treeview = treeview;

        that._draggable = new ui.Draggable(treeview.element, {
           filter: "div:not(.k-state-disabled) .k-in",
           hint: function(node) {
               return templates.dragClue({ text: node.text() });
           },
           dragstart: proxy(that.dragstart, that),
           drag: proxy(that.drag, that),
           dragend: proxy(that.dragend, that)
        });
    }

    TreeViewDragAndDrop.prototype = /** @ignore */{
        _hintStatus: function(newStatus) {
            var statusElement = this._draggable.hint.find(".k-drag-status")[0];

            if (newStatus) {
                statusElement.className = "k-icon k-drag-status " + newStatus;
            } else {
                return $.trim(statusElement.className.replace(/k-(icon|drag-status)/g, ""));
            }
        },

        dragstart: function (e) {
            var that = this,
                treeview = that.treeview,
                sourceNode = that.sourceNode = e.currentTarget.closest(NODE);

            if (treeview.trigger(DRAGSTART, { sourceNode: sourceNode[0] })) {
                return false;
            }

            that.dropHint = $("<div class='k-drop-hint' />")
                .css(VISIBILITY, "hidden")
                .appendTo(treeview.element);
        },

        drag: function (e) {
            var that = this,
                treeview = that.treeview,
                sourceNode = that.sourceNode,
                dropTarget = that.dropTarget = $(kendo.eventTarget(e)),
                statusClass,
                hoveredItem, hoveredItemPos, itemHeight, itemTop, itemContent, delta,
                insertOnTop, insertOnBottom, addChild;

            if (!dropTarget.closest(".k-treeview").length) {
                // dragging node outside of treeview
                statusClass = "k-denied";
            } else if ($.contains(sourceNode[0], dropTarget[0])) {
                // dragging node within itself
                statusClass = "k-denied";
            } else {
                // moving or reordering node
                statusClass = "k-insert-middle";

                that.dropHint.css(VISIBILITY, "visible");

                hoveredItem = dropTarget.closest(".k-top,.k-mid,.k-bot");

                if (hoveredItem.length > 0) {
                    itemHeight = hoveredItem.outerHeight();
                    itemTop = hoveredItem.offset().top;
                    itemContent = dropTarget.closest(".k-in");
                    delta = itemHeight / (itemContent.length > 0 ? 4 : 2);

                    insertOnTop = e.pageY < (itemTop + delta);
                    insertOnBottom = (itemTop + itemHeight - delta) < e.pageY;
                    addChild = itemContent.length > 0 && !insertOnTop && !insertOnBottom;

                    itemContent.toggleClass(TSTATEHOVER, addChild);
                    that.dropHint.css(VISIBILITY, addChild ? "hidden" : "visible");

                    if (addChild) {
                        statusClass = "k-add";
                    } else {
                        hoveredItemPos = hoveredItem.position();
                        hoveredItemPos.top += insertOnTop ? 0 : itemHeight;

                        that.dropHint
                            .css(hoveredItemPos)
                            [insertOnTop ? "prependTo" : "appendTo"](dropTarget.closest(NODE).find("> div:first"));

                        if (insertOnTop && hoveredItem.hasClass("k-top")) {
                            statusClass = "k-insert-top";
                        }

                        if (insertOnBottom && hoveredItem.hasClass("k-bot")) {
                            statusClass = "k-insert-bottom";
                        }
                    }
                }
            }

            treeview.trigger(DRAG, {
                sourceNode: sourceNode[0],
                dropTarget: dropTarget[0],
                pageY: e.pageY,
                pageX: e.pageX,
                statusClass: statusClass.substring(2),
                setStatusClass: function (value) { statusClass = value }
            });

            if (statusClass.indexOf("k-insert") != 0) {
                that.dropHint.css(VISIBILITY, "hidden");
            }

            that._hintStatus(statusClass);
        },

        dragend: function (e) {
            var that = this,
                treeview = that.treeview,
                dropPosition = "over",
                sourceNode = that.sourceNode,
                destinationNode,
                valid, dropPrevented;

            if (e.keyCode == kendo.keys.ESC){
                that.dropHint.remove();
                //treeview.trigger("nodeDragCancelled", { item: sourceNode[0] });
            } else {
                if (that.dropHint.css(VISIBILITY) == "visible") {
                    dropPosition = that.dropHint.prevAll(".k-in").length > 0 ? "after" : "before";
                    destinationNode = that.dropHint.closest(NODE);
                } else if (that.dropTarget) {
                    destinationNode = that.dropTarget.closest(NODE);
                }

                valid = that._hintStatus() != "k-denied";

                dropPrevented = treeview.trigger(DROP, {
                    sourceNode: sourceNode[0],
                    destinationNode: destinationNode[0],
                    valid: valid,
                    setValid: function(newValid) { valid = newValid; },
                    dropTarget: e.target,
                    dropPosition: dropPosition
                });

                that.dropHint.remove();

                if (!valid || dropPrevented) {
                    that._draggable.dropped = valid;
                    return;
                }

                that._draggable.dropped = true;

                // perform reorder / move
                if (dropPosition == "over") {
                    treeview.append(sourceNode, destinationNode);
                    treeview.expand(destinationNode);
                } else if (dropPosition == "before") {
                    treeview.insertBefore(sourceNode, destinationNode);
                } else if (dropPosition == "after") {
                    treeview.insertAfter(sourceNode, destinationNode);
                }

                treeview.trigger(DRAGEND, {
                    sourceNode: sourceNode[0],
                    destinationNode: destinationNode[0],
                    dropPosition: dropPosition
                });
            }
        }
    };

    // client-side rendering

    extend(TreeView, {
        renderItem: function (options) {
            options = extend({ treeview: {}, group: {} }, options);

            var empty = templates.empty,
                item = options.item,
                treeview = options.treeview;

            return templates.item(extend(options, {
                image: item.imageUrl ? templates.image : empty,
                sprite: item.spriteCssClass ? templates.sprite : empty,
                itemWrapper: templates.itemWrapper,
                toggleButton: item.items ? templates.toggleButton : empty,
                subGroup: TreeView.renderGroup
            }, rendering));
        },

        renderGroup: function (options) {
            return templates.group(extend({
                renderItems: function(options) {
                    var html = "",
                        i = 0,
                        items = options.items,
                        len = items ? items.length : 0,
                        group = extend({ length: len }, options.group);

                    for (; i < len; i++) {
                        html += TreeView.renderItem(extend(options, {
                            group: group,
                            item: extend({ index: i }, items[i])
                        }));
                    }

                    return html;
                }
            }, options, rendering));
        }
    });

    rendering = /** @ignore */{
        wrapperCssClass: function (group, item) {
            var result = "k-item",
                index = item.index;

            if (group.firstLevel && index == 0) {
                result += " k-first"
            }

            if (index == group.length-1) {
                result += " k-last";
            }

            return result;
        },
        cssClass: function(group, item) {
            var result = "",
                index = item.index,
                groupLength = group.length - 1;

            if (group.firstLevel && index == 0) {
                result += "k-top ";
            }

            if (index == 0 && index != groupLength) {
                result += "k-top";
            } else if (index == groupLength) {
                result += "k-bot";
            } else {
                result += "k-mid";
            }

            return result;
        },
        textClass: function(item) {
            var result = "k-in";

            if (item.enabled === false) {
                result += " k-state-disabled";
            }

            if (item.selected === true) {
                result += " k-state-selected";
            }

            return result;
        },
        textAttributes: function(item) {
            return item.url ? " href='" + item.url + "'" : "";
        },
        toggleButtonClass: function(item) {
            var result = "k-icon";

            if (item.expanded !== true) {
                result += " k-plus";
            } else {
                result += " k-minus";
            }

            if (item.enabled === false) {
                result += "-disabled";
            }

            return result;
        },
        text: function(item) {
            return item.encoded === false ? item.text : kendo.htmlEncode(item.text);
        },
        tag: function(item) {
            return item.url ? "a" : "span";
        },
        groupAttributes: function(group) {
            return group.expanded !== true ? " style='display:none'" : "";
        },
        groupCssClass: function(group) {
            var cssClass = "k-group";

            if (group.firstLevel) {
                cssClass += " k-treeview-lines";
            }

            return cssClass;
        }
    };

    ui.plugin(TreeView);
})(jQuery);
(function ($, undefined) {
    /**
     * @name kendo.ui.Slider.Description
     * @section
     *  <p>
     *      The Slider widget provides a rich input for selecting values or ranges of values.
     *      Unlike the plain HTML5 range input, the Slider presents a consistent experience across
     *      all browsers and has a rich API and event model.
     *  </p>
     *  <h3>Getting Started</h3>
     *  There are two basic types of Sliders:
     *  <ol>
     *      <li><strong>Slider</strong>, which presents one thumb and two opposing buttons for selecting a single value</li>
     *      <li><strong>RangeSlider</strong>, which present two thumbs for defining a range of values</li>
     *  </ol>
     *  <h4>Slider</h4>
     * @exampleTitle Create simple HTML input element
     * @example
     *  <input id="slider" />
     * @exampleTitle Initialize the Slider using a jQuery selector
     * @example
     *  $("#slider").kendoSlider();
     *
     * @section
     *  <h4>RangeSlider</h4>
     * @exampleTitle Create two simple HTML input elements in a div
     * @example
     *  <div id="rangeSlider">
     *      <input />
     *      <input />
     *  </div>
     *
     * @exampleTitle Initialize the RangeSlider using a jQuery selector targeting the div
     * @example
     *  $("#rangeSlider").kendoRangeSlider();
     *
     * @section
     *  <p>
     *      The RangeSlider requires two inputs to capture both ends of the value range. This
     *      benefits scenarios where JavaScript is disabled, in which case users will be presented
     *      with two inputs, still allowing them to input a valid range.
     *  </p>
     *
     *  <h3>Customizing Slider Behavior</h3>
     *  Many facets of the Slider and RangeSlider behavior can be configured via simple properties, including:
     *  <ul>
     *      <li>Min/Max values</li>
     *      <li>Orientation (horizontal or vertical)</li>
     *      <li>Small/Large step</li>
     *      <li>Tooltip format/placement</li>
     *  </ul>
     *  <p>
     *      To see a full list of available properties and values, review the Slider Configuration API documentation tab.
     *  </p>
     * @exampleTitle Customizing Slider default settings
     * @example
     *  $("#slider").kendoSlider({
     *      min:10,
     *      max:50,
     *      orientation: "vertical",
     *      smallStep: 1,
     *      largeStep: 10
     *  });
     *
     */
    var kendo = window.kendo,
        Widget = kendo.ui.Widget,
        Draggable = kendo.ui.Draggable,
        keys = kendo.keys,
        extend = $.extend,
        parse = kendo.parseFloat,
        proxy = $.proxy,
        math = Math,
        touch = kendo.support.touch,
        CHANGE = "change",
        SLIDE = "slide",
        MOUSE_DOWN = touch ? "touchstart" : "mousedown",
        MOUSE_UP = touch ? "touchend" : "mouseup",
        MOVE_SELECTION = "moveSelection",
        KEY_DOWN = "keydown",
        MOUSE_OVER = "mouseover",
        DRAG_HANDLE = ".k-draghandle",
        TRACK_SELECTOR = ".k-slider-track",
        TICK_SELECTOR = ".k-tick",
        STATE_SELECTED = "k-state-selected",
        STATE_DEFAULT = "k-state-default",
        STATE_DISABLED = "k-state-disabled",
        PRECISION = 3,
        DISABLED = "disabled";

    var SliderBase = Widget.extend({
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            options = that.options;

            that._distance = options.max - options.min;
            that._isHorizontal = options.orientation == "horizontal";
            that._position = that._isHorizontal ? "left" : "bottom";
            that._size = that._isHorizontal ? "width" : "height";
            that._outerSize = that._isHorizontal ? "outerWidth" : "outerHeight";

            options.tooltip.format = options.tooltip.enabled ? options.tooltip.format || "{0}" : "{0}";

            that._createHtml();
            that.wrapper = that.element.closest(".k-slider");
            that._trackDiv = that.wrapper.find(TRACK_SELECTOR);

            that._setTrackDivWidth();

            that._maxSelection = that._trackDiv[that._size]();

            var sizeBetweenTicks = that._maxSelection / ((options.max - options.min) / options.smallStep);
            var pixelWidths = that._calculateItemsWidth(math.floor(that._distance / options.smallStep));

            if (options.tickPlacement != "none" && sizeBetweenTicks >= 2) {
                that._trackDiv.before(createSliderItems(options, that._distance));
                that._setItemsWidth(pixelWidths);
                that._setItemsTitle();
                that._setItemsLargeTick();
            }

            that._calculateSteps(pixelWidths);

            that[options.enabled ? "enable" : "disable"]();

            that._keyMap = {
                37: step(-options.smallStep), // left arrow
                40: step(-options.smallStep), // down arrow
                39: step(+options.smallStep), // right arrow
                38: step(+options.smallStep), // up arrow
                35: setValue(options.max), // end
                36: setValue(options.min), // home
                33: step(+options.largeStep), // page up
                34: step(-options.largeStep)  // page down
            };

            that.bind([
                /**
                 * Fires when the slider value changes as a result of selecting a new value with the drag handle, buttons or keyboard.
                 * @name kendo.ui.Slider#change
                 * @event
                 * @param {Event} e
                 * @param {Number} e.value Represents the updated value of the slider.
                 */

                /**
                 * Fires when the rangeSlider value changes as a result of selecting a new value with one of the drag handles or the keyboard.
                 * @name kendo.ui.RangeSlider#change
                 * @event
                 * @param {Event} e
                 * @param {Number} e.values Represents the updated array of values of the first and second drag handle.
                 */
                CHANGE,

                /**
                 * Fires when the user drags the drag handle to a new position.
                 * @name kendo.ui.Slider#slide
                 * @event
                 * @param {Event} e
                 * @param {Number} e.value Represents the value from the current position of the drag handle.
                 */

                /**
                 * Fires when the user drags the drag handle to a new position.
                 * @name kendo.ui.RangeSlider#slide
                 * @event
                 * @param {Event} e
                 * @param {Number} e.values Represents an array of values of the current positions of the first and second drag handle.
                 */
                SLIDE], options);
        },

        options: {
            enabled: true,
            min: 0,
            max: 10,
            smallStep: 1,
            largeStep: 5,
            orientation: "horizontal",
            tickPlacement: "both",
            tooltip: { enabled: true, format: "{0}" }
        },

        _setTrackDivWidth: function() {
            var that = this,
                trackDivPosition = parseFloat(that._trackDiv.css(that._position), 10) * 2;

            that._trackDiv[that._size]((that.wrapper[that._size]() - 2) - trackDivPosition);
        },

        _setItemsWidth: function(pixelWidths) {
            var that = this,
                options = that.options,
                first = 0,
                last = pixelWidths.length - 1,
                items = that.wrapper.find(TICK_SELECTOR),
                i,
                paddingTop = 0,
                bordersWidth = 2,
                selection = 0;

            for (i = 0; i < items.length - 2; i++) {
                $(items[i + 1])[that._size](pixelWidths[i]);
            }

            if (that._isHorizontal) {
                $(items[first]).addClass("k-first")[that._size](pixelWidths[last - 1]);
                $(items[last]).addClass("k-last")[that._size](pixelWidths[last]);
            } else {
                $(items[last]).addClass("k-first")[that._size](pixelWidths[last]);
                $(items[first]).addClass("k-last")[that._size](pixelWidths[last - 1]);
            }

            if (that._distance % options.smallStep != 0 && !that._isHorizontal) {
                for (i = 0; i < pixelWidths.length; i++) {
                    selection += pixelWidths[i];
                }

                paddingTop = that._maxSelection - selection;
                paddingTop += parseFloat(that._trackDiv.css(that._position), 10) + bordersWidth;

                that.wrapper.find(".k-slider-items").css("padding-top", paddingTop);
            }
        },

        _setItemsTitle: function() {
            var that = this,
                options = that.options,
                items = that.wrapper.find(TICK_SELECTOR),
                titleNumber = options.min,
                i = that._isHorizontal ? 0 : items.length - 1,
                limit = that._isHorizontal ? items.length : -1,
                increment = that._isHorizontal ? 1 : -1;

            for (; i - limit != 0 ; i += increment) {
                $(items[i]).attr("title", kendo.format(options.tooltip.format, round(titleNumber)));
                titleNumber += options.smallStep;
            }
        },

        _setItemsLargeTick: function() {
            var that = this,
                options = that.options,
                i,
                items = that.wrapper.find(TICK_SELECTOR),
                item = {},
                step = round(options.largeStep / options.smallStep);

            if ((1000 * options.largeStep) % (1000 * options.smallStep) == 0) {
                if (that._isHorizontal) {
                    for (i = 0; i < items.length; i = round(i + step)) {
                        item = $(items[i]);

                        item.addClass("k-tick-large")
                            .html("<span class='k-label'>" + item.attr("title") + "</span>");
                    }
                } else {
                    for (i = items.length - 1; i >= 0; i = round(i - step)) {
                        item = $(items[i]);

                        item.addClass("k-tick-large")
                            .html("<span class='k-label'>" + item.attr("title") + "</span>");

                        if (i != 0 && i != items.length - 1) {
                            item.css("line-height", item[that._size]() + "px");
                        }
                    }
                }
            }
        },

        _calculateItemsWidth: function(itemsCount) {
            var that = this,
                options = that.options,
                trackDivSize = parseFloat(that._trackDiv.css(that._size)) + 1,
                pixelStep = trackDivSize / that._distance,
                itemWidth,
                pixelWidths,
                i;

            if ((that._distance / options.smallStep) - math.floor(that._distance / options.smallStep) > 0) {
                trackDivSize -= ((that._distance % options.smallStep) * pixelStep);
            }

            itemWidth = trackDivSize / itemsCount;
            pixelWidths = [];

            for (i = 0; i < itemsCount - 1; i++) {
                pixelWidths[i] = itemWidth;
            }

            pixelWidths[itemsCount - 1] = pixelWidths[itemsCount] = itemWidth / 2;
            return that._roundWidths(pixelWidths);
        },

        _roundWidths: function(pixelWidthsArray) {
            var balance = 0;

            for (i = 0; i < pixelWidthsArray.length; i++) {
                balance += (pixelWidthsArray[i] - math.floor(pixelWidthsArray[i]));
                pixelWidthsArray[i] = math.floor(pixelWidthsArray[i]);
            }

            balance = math.round(balance);

            return this._addAdditionalSize(balance, pixelWidthsArray);
        },

        _addAdditionalSize: function(additionalSize, pixelWidthsArray) {
            if (additionalSize == 0) {
                return pixelWidthsArray;
            }

            //set step size
            var step = parseFloat(pixelWidthsArray.length - 1) / parseFloat(additionalSize == 1 ? additionalSize : additionalSize - 1),
                i;

            for (i = 0; i < additionalSize; i++) {
                pixelWidthsArray[parseInt(math.round(step * i))] += 1;
            }

            return pixelWidthsArray;
        },

        _calculateSteps: function(pixelWidths) {
            var that = this,
                options = that.options,
                val = options.min,
                selection = 0,
                itemsCount = pixelWidths.length,
                i = 1,
                lastItem;

            pixelWidths.splice(0, 0, pixelWidths[itemsCount - 2] * 2);
            pixelWidths.splice(itemsCount -1, 1, pixelWidths.pop() * 2);

            that._pixelSteps = [selection];
            that._values = [val];

            if (itemsCount == 0) {
                return;
            }

            while (i < itemsCount) {
                selection += (pixelWidths[i - 1] + pixelWidths[i]) / 2;
                that._pixelSteps[i] = selection;
                that._values[i] = val += options.smallStep;

                i++;
            }

            lastItem = options.max % options.smallStep == 0 ? itemsCount - 1 : itemsCount;

            that._pixelSteps[lastItem] = that._maxSelection;
            that._values[lastItem] = options.max;
        },

        _getValueFromPosition: function(mousePosition, dragableArea) {
            var that = this,
                options = that.options,
                step = math.max(options.smallStep * (that._maxSelection / that._distance), 0),
                position = 0,
                halfStep = (step / 2),
                val = 0,
                i;

            if (that._isHorizontal) {
                position = mousePosition - dragableArea.startPoint;
            } else {
                position = dragableArea.startPoint - mousePosition;
            }

            if (that._maxSelection - ((parseInt(that._maxSelection % step) - 3) / 2) < position) {
                return options.max;
            }

            for (i = 0; i < that._pixelSteps.length; i++) {
                if (math.abs(that._pixelSteps[i] - position) - 1 <= halfStep) {
                    return round(that._values[i]);
                }
            }
        },

        _getDragableArea: function() {
            var that = this,
                offsetLeft = that._trackDiv.offset().left,
                offsetTop = that._trackDiv.offset().top;

            return {
                startPoint: that._isHorizontal ? offsetLeft : offsetTop + that._maxSelection,
                endPoint: that._isHorizontal ? offsetLeft + that._maxSelection : offsetTop
            };
        },

        _createHtml: function() {
            var that = this,
                element = that.element,
                options = that.options,
                inputs = element.find("input");

            if (inputs.length == 2) {
                inputs.eq(0).val(options.selectionStart);
                inputs.eq(1).val(options.selectionEnd);
            } else {
                element.val(options.value);
            }

            element.wrap(createWrapper(options, element, that._isHorizontal)).hide();

            if (options.showButtons) {
                element.before(createButton(options, "increase", that._isHorizontal))
                       .before(createButton(options, "decrease", that._isHorizontal));
            }

            element.before(createTrack(element));
        }
    });

    function createWrapper (options, element, isHorizontal) {
        var orientationCssClass = isHorizontal ? " k-slider-horizontal" : " k-slider-vertical",
            style = options.style ? options.style : element.attr("style"),
            cssClasses = element.attr("class") ? (" " + element.attr("class")) : "",
            tickPlacementCssClass = "";

        if (options.tickPlacement == "bottomRight") {
            tickPlacementCssClass = " k-slider-bottomright";
        } else if (options.tickPlacement == "topLeft") {
            tickPlacementCssClass = " k-slider-topleft";
        }

        style = style ? " style='" + style + "'" : "";

        return "<div class='k-widget k-slider" + orientationCssClass + cssClasses + "'" + style + ">" +
               "<div class='k-slider-wrap" + (options.showButtons ? " k-slider-buttons" : "") + tickPlacementCssClass +
               "'></div></div>";
    }

    function createButton (options, type, isHorizontal) {
        var buttonCssClass = "";

        if (type == "increase") {
            buttonCssClass = isHorizontal ? "k-arrow-next" : "k-arrow-up";
        } else {
            buttonCssClass = isHorizontal ? "k-arrow-prev" : "k-arrow-down";
        }

        return "<a class='k-button k-button-" + type + "'><span class='k-icon " + buttonCssClass +
               "' title='" + options[type + "ButtonTitle"] + "'>" + options[type + "ButtonTitle"] + "</span></a>";
    }

    function createSliderItems (options, distance) {
        var result = "<ul class='k-reset k-slider-items'>",
            count = math.floor(round(distance / options.smallStep)) + 1;

        for(i = 0; i < count; i++) {
            result += "<li class='k-tick'>&nbsp;</li>";
        }

        result += "</ul>";

        return result;
    }

    function createTrack (element) {
        var dragHandleCount = element.is("input") ? 1 : 2;

        return "<div class='k-slider-track'><div class='k-slider-selection'><!-- --></div>" +
               "<a href='javascript:void(0)' class='k-draghandle' title='Drag'>Drag</a>" +
               (dragHandleCount > 1 ? "<a href='javascript:void(0)' class='k-draghandle' title='Drag'>Drag</a>" : "") +
               "</div>";
    }

    function step(step) {
        return function (value) {
            return value + step;
        }
    }

    function setValue(value) {
        return function () {
            return value;
        }
    }

    function formatValue(value) {
        return (value + "").replace(".", kendo.cultures.current.numberFormat["."]);
    }

    function round(value) {
        value = parseFloat(value, 10);
        var power = math.pow(10, PRECISION || 0);
        return math.round(value * power) / power;
    }

    function parseAttr(element, name) {
        return parse(element.getAttribute(name)) || undefined;
    }

    var Slider = SliderBase.extend(/** @lends kendo.ui.Slider.prototype */{
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Boolean} [enabled] <true> Can be used to enable/disable the slider.
         * @option {Number} [min] <0> The minimum value of the slider.
         * @option {Number} [max] <10> The maximum value of the slider.
         * @option {Boolean} [showButtons] <true> Can be used to show or hide the slider increase and decrease buttons. The buttons are used to increase or decrease the value. They are not available in the RangeSlider.
         * @option {Object} [tooltip] Confituration of the slider tooltip.
         * @option {Boolean} [tooltip.enabled] <true> Can be used to enable/disable the tooltip.
         * @option {String} [tooltip.format] <"{0}"> Can be used to formatting of the text of the tooltip. Note that the applied format will also influence the appearance of the slider tick labels.
         * @option {Number} [value] <0> The value of the slider.
         * @option {String} [orientation] <"horizontal"> The orientation of the slider. Available options are "horizontal" and "vertical".
         * @option {String} [tickPlacement] <"both"> the location of the tick marks in the widget. Available options are:
         *     <dl>
         *         <dt>
         *              "topLeft"
         *         </dt>
         *         <dd>
         *              Tick marks are located on the top of the horizontal widget or on the left of the vertical widget.
         *         </dd>
         *         <dt>
         *              "bottomRight"
         *         </dt>
         *         <dd>
         *              Tick marks are located on the bottom of the horizontal widget or on the right side of the vertical widget.
         *         </dd>
         *         <dt>
         *              "both"
         *         </dt>
         *         <dd>
         *              Tick marks are located on both sides of the widget.
         *         </dd>
         *     </dl>
         * @option {Number} [smallStep] <1> The small step of the slider. The Value will be changed with SmallStep when the end user:
         *     <ul>
         *         <li>
         *             clicks on the Slider buttons
         *         </li>
         *         <li>
         *             presses the arrow keys (the drag handle must be focused)
         *         </li>
         *         <li>
         *             drag the drag handle
         *         </li>
         *     </ul>
         * @option {Number} [largeStep] <5> The delta with which the value will change when the user presses the Page Up or Page Down key (the drag handle must be focused). Note that the allied largeStep will also set large tick for every large step.
         * @option {String} [increaseButtonTitle] <"Increase"> The title of the increase button of the slider.
         * @option {String} [decreaseButtonTitle] <"Decrease"> The title of the decrease button of the slider.
         */
        init: function(element, options) {
            var that = this,
                dragHandle;

            element.type = "text";

            options = extend({}, {
                value: parseAttr(element, "value"),
                min: parseAttr(element, "min"),
                max: parseAttr(element, "max"),
                smallStep: parseAttr(element, "step")
            }, options);

            SliderBase.fn.init.call(that, element, options);
            options = that.options;

            that._setValueInRange(options.value);
            dragHandle = that.wrapper.find(DRAG_HANDLE);

            new Slider.Selection(dragHandle, that, options);
            that._drag = new Slider.Drag(dragHandle, "", that, options);
        },

        options: {
            name: "Slider",
            value: 0,
            showButtons: true,
            increaseButtonTitle: "Increase",
            decreaseButtonTitle: "Decrease"
        },

        /**
         * Enables the slider.
         * @example
         * var slider = $("#slider").data("kendoSlider");
         *
         * // enables the slider
         * slider.enable();
         */
        enable: function () {
            var that = this,
                options = that.options,
                clickHandler,
                move;

            that.wrapper
                .removeAttr(DISABLED)
                .removeClass(STATE_DISABLED)
                .addClass(STATE_DEFAULT);

            clickHandler = function (e) {
                if ($(e.target).hasClass("k-draghandle")) {
                    $(e.target).addClass(STATE_SELECTED);
                    return;
                }

                var location = kendo.touchLocation(e),
                    mousePosition = that._isHorizontal ? location.x : location.y,
                    dragableArea = that._getDragableArea();

                that._update(that._getValueFromPosition(mousePosition, dragableArea));

                that._drag.dragstart(e);
            };

            that.wrapper
                .find(TICK_SELECTOR).bind(MOUSE_DOWN, clickHandler)
                .end()
                .find(TRACK_SELECTOR).bind(MOUSE_DOWN, clickHandler);

            that.wrapper.find(DRAG_HANDLE).bind(MOUSE_UP, function (e) {
                $(e.target).removeClass(STATE_SELECTED);
            });

            move = proxy(function (e, sign) {
                var index = math.ceil(options.value / options.smallStep) - math.abs(options.min);

                if (index >= that._values.length - 1 || index <= 0) {
                    that._setValueInRange(options.value + (sign * options.smallStep));
                } else {
                    that._setValueInRange(that._values[index + (sign * 1)]);
                }
            }, that);

            if (options.showButtons) {
                var mouseDownHandler = proxy(function(e, sign) {
                    if (e.which == 1 || (touch && e.which == 0)) {
                        move(e, sign);

                        this.timeout = setTimeout(proxy(function () {
                            this.timer = setInterval(function () {
                                move(e, sign)
                            }, 60);
                        }, this), 200);
                    }
                }, that);

                that.wrapper.find(".k-button")
                    .bind(MOUSE_UP, proxy(function (e) {
                        this._clearTimer();
                    }, that))
                    .bind(MOUSE_OVER, function (e) {
                        $(e.currentTarget).addClass("k-state-hover");
                    })
                    .bind("mouseout", proxy(function (e) {
                        $(e.currentTarget).removeClass("k-state-hover");
                        this._clearTimer();
                    }, that))
                    .eq(0)
                    .bind(MOUSE_DOWN, proxy(function (e) {
                        mouseDownHandler(e, 1);
                    }, that))
                    .click(false)
                    .end()
                    .eq(1)
                    .bind(MOUSE_DOWN, proxy(function (e) {
                        mouseDownHandler(e, -1);
                    }, that))
                    .click(false);
            }

            that.wrapper
                .find(DRAG_HANDLE).bind(KEY_DOWN, proxy(this._keydown, that));

            options.enabled = true;
        },

        /**
         * Disables the slider.
         * @example
         * var slider = $("#slider").data("kendoSlider");
         *
         * // disables the slider
         * slider.disable();
         */
        disable: function () {
            var that = this;

            that.wrapper
                .attr(DISABLED, DISABLED)
                .removeClass(STATE_DEFAULT)
                .addClass(STATE_DISABLED);

            that.wrapper
                .find(".k-button")
                .unbind(MOUSE_DOWN)
                .bind(MOUSE_DOWN, false)
                .unbind(MOUSE_UP)
                .bind(MOUSE_UP, false)
                .unbind("mouseleave")
                .bind("mouseleave", false)
                .unbind(MOUSE_OVER)
                .bind(MOUSE_OVER, false);

            that.wrapper
                .find(TICK_SELECTOR).unbind(MOUSE_DOWN)
                .end()
                .find(TRACK_SELECTOR).unbind(MOUSE_DOWN);

            that.wrapper
                .find(DRAG_HANDLE)
                .unbind(MOUSE_UP)
                .unbind(KEY_DOWN)
                .bind(KEY_DOWN, false);

            that.options.enabled = false;
        },

        _update: function (val) {
            var that = this,
                change = that.value() != val;

            that.value(val);

            if (change) {
                that.trigger(CHANGE, { value: that.options.value });
            }
        },

        /**
         * The value method gets or sets the value of the slider.
         * The value method accepts a {String} or a {Number} as parameters, and returns a {Nubmer}.
         * @example
         * var slider = $("#slider").data("kendoSlider");
         *
         * // Get or sets the value of the slider
         * slider.value();
         */
        value: function (val) {
            var that = this,
                options = that.options;

            val = round(val);
            if (isNaN(val)) {
                return options.value;
            }

            if (val >= options.min && val <= options.max) {
                if (options.value != val) {
                    that.element.attr("value", formatValue(val));
                    options.value = val;
                    that.refresh();
                }
            }
        },

        refresh: function () {
            this.trigger(MOVE_SELECTION, { value: this.options.value });
        },

        _clearTimer: function (e) {
            clearTimeout(this.timeout);
            clearInterval(this.timer);
        },

        _keydown: function (e) {
            var that = this;

            if (e.keyCode in that._keyMap) {
                that._setValueInRange(that._keyMap[e.keyCode](that.options.value));
                e.preventDefault();
            }
        },

        _setValueInRange: function (val) {
            var that = this,
                options = that.options;

            val = round(val);
            if (isNaN(val)) {
                that._update(options.min);
                return;
            }

            val = math.max(math.min(val, options.max), options.min);
            that._update(val);
        }
    });

    Slider.Selection = function (dragHandle, that, options) {
        function moveSelection (val) {
            var selectionValue = val - options.min,
                index = math.ceil(selectionValue / options.smallStep),
                selection = parseInt(that._pixelSteps[index]),
                selectionDiv = that._trackDiv.find(".k-slider-selection"),
                halfDragHanndle = parseInt(dragHandle[that._outerSize]() / 2, 10);

            selectionDiv[that._size](selection);
            dragHandle.css(that._position, selection - halfDragHanndle);
        }

        moveSelection(options.value);

        that.bind([CHANGE, SLIDE, MOVE_SELECTION], function (e) {
            moveSelection(parseFloat(e.value, 10));
        });
    };

    Slider.Drag = function (dragHandle, type, owner, options) {
        var that = this;
        that.owner = owner;
        that.options = options;
        that.dragHandle = dragHandle;
        that.dragHandleSize = dragHandle[owner._outerSize]();
        that.type = type;

        that.draggable = new Draggable(dragHandle, {
            dragstart: proxy(that._dragstart, that),
            drag: proxy(that.drag, that),
            dragend: proxy(that.dragend, that)
        });

        dragHandle.click(false);
    };

    Slider.Drag.prototype = {
        dragstart: function (e) {
            this.draggable._startDrag(e);
        },

        _dragstart: function (e) {
            var that = this,
                owner = that.owner,
                options = that.options;

            if (!options.enabled) {
                e.preventDefault();
                return false;
            }

            owner.element.unbind(MOUSE_OVER);
            that.dragHandle.addClass(STATE_SELECTED);

            that.dragableArea = owner._getDragableArea();
            that.step = math.max(options.smallStep * (owner._maxSelection / owner._distance), 0);

            if (that.type) {
                that.selectionStart = options.selectionStart;
                that.selectionEnd = options.selectionEnd;
                owner._setZIndex(that.type);
            } else {
                that.oldVal = that.val = options.value;
            }

            if (options.tooltip.enabled) {
                that.tooltipDiv = $("<div class='k-widget k-tooltip'><!-- --></div>").appendTo(document.body);
                var html = "";

                if (that.type) {
                    var formattedSelectionStart = kendo.format(options.tooltip.format, that.selectionStart),
                        formattedSelectionEnd = kendo.format(options.tooltip.format, that.selectionEnd);

                    html = formattedSelectionStart + " - " + formattedSelectionEnd;
                } else {
                    that.tooltipInnerDiv = "<div class='k-callout k-callout-" + (owner._isHorizontal ? "s" : "e") + "'><!-- --></div>";
                    html = kendo.format(options.tooltip.format, that.val) + that.tooltipInnerDiv;
                }

                that.tooltipDiv.html(html);

                that.moveTooltip();
            }
        },

        drag: function (e) {
            var that = this,
                owner = that.owner,
                options = that.options,
                location = kendo.touchLocation(e),
                startPoint = that.dragableArea.startPoint,
                endPoint = that.dragableArea.endPoint;

            if (owner._isHorizontal) {
                that.val = that.constrainValue(location.x, startPoint, endPoint, location.x >= endPoint);
            } else {
                that.val = that.constrainValue(location.y, endPoint, startPoint, location.y <= endPoint);
            }

            if (that.oldVal != that.val) {
                that.oldVal = that.val;

                if (that.type) {
                    if (that.type == "firstHandle") {
                        if (that.val < that.selectionEnd) {
                            that.selectionStart = that.val;
                        } else {
                            that.selectionStart = that.selectionEnd = that.val;
                        }
                    } else {
                        if (that.val > that.selectionStart) {
                            that.selectionEnd = that.val;
                        } else {
                            that.selectionStart = that.selectionEnd = that.val;
                        }
                    }

                    owner.trigger(SLIDE, { values: [that.selectionStart, that.selectionEnd] });

                    if (options.tooltip.enabled) {
                        var formattedSelectionStart = kendo.format(options.tooltip.format, that.selectionStart),
                            formattedSelectionEnd = kendo.format(options.tooltip.format, that.selectionEnd);

                        that.tooltipDiv.html(formattedSelectionStart + " - " + formattedSelectionEnd );
                    }
                } else {
                    owner.trigger(SLIDE, { value: that.val });

                    if (options.tooltip.enabled) {
                        that.tooltipDiv.html(kendo.format(options.tooltip.format, that.val) + that.tooltipInnerDiv);
                    }
                }

                if (options.tooltip.enabled) {
                    that.moveTooltip();
                }
            }
        },

        dragend: function (e) {
            var that = this,
                owner = that.owner;

            if (e.keyCode == kendo.keys.ESC) {
                owner.refresh();
            } else {
                if (that.type) {
                    owner._update(that.selectionStart, that.selectionEnd);
                } else {
                    owner._update(that.val);
                }
            }

            if (owner.options.tooltip.enabled) {
                that.tooltipDiv.remove();
            }

            that.dragHandle.removeClass(STATE_SELECTED);
            owner.element.bind(MOUSE_OVER);

            return false;
        },

        moveTooltip: function () {
            var that = this,
                owner = that.owner,
                positionTop = 0,
                positionLeft = 0,
                dragHandleOffset = that.dragHandle.offset(),
                margin = 4,
                callout = that.tooltipDiv.find(".k-callout"),
                padding;

            if (that.type) {
                var dragHandles = owner.wrapper.find(DRAG_HANDLE),
                    firstDragHandleOffset = dragHandles.eq(0).offset(),
                    secondDragHandleOffset = dragHandles.eq(1).offset();

                if (owner._isHorizontal) {
                    positionTop = secondDragHandleOffset.top;
                    positionLeft = firstDragHandleOffset.left + ((secondDragHandleOffset.left - firstDragHandleOffset.left) / 2);
                } else {
                    positionTop = firstDragHandleOffset.top + ((secondDragHandleOffset.top - firstDragHandleOffset.top) / 2);
                    positionLeft = secondDragHandleOffset.left;
                }
            } else {
                positionTop = dragHandleOffset.top;
                positionLeft = dragHandleOffset.left;
            }
            if (owner._isHorizontal) {
                positionLeft -= parseInt((that.tooltipDiv.outerWidth() - that.dragHandle[owner._outerSize]()) / 2);
                positionTop -= that.tooltipDiv.outerHeight() + callout.height() + margin;
            } else {
                positionTop -= parseInt((that.tooltipDiv.outerHeight() - that.dragHandle[owner._outerSize]()) / 2);
                positionLeft -= that.tooltipDiv.outerWidth() + callout.width() + margin;
            }

            that.tooltipDiv.css({ top: positionTop, left: positionLeft });
        },

        constrainValue: function (position, min, max, maxOverflow) {
            var that = this,
                val = 0;

            if (min < position && position < max) {
                val = that.owner._getValueFromPosition(position, that.dragableArea);
            } else
                if (maxOverflow) {
                    val = that.options.max;
                } else {
                    val = that.options.min;
                }

            return val;
        }

    };

    kendo.ui.plugin(Slider);

    //
    // RangeSlider
    //

    var RangeSlider = SliderBase.extend(/** @lends kendo.ui.RangeSlider.prototype */{
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Boolean} [enabled] <true> Can be used to enable/disable the rangeSlider.
         * @option {Number} [min] <0> The minimum value of the rangeSlider.
         * @option {Number} [max] <10> The maximum value of the rangeSlider.
         * @option {Object} [tooltip] Confituration of the Rangelider tooltip.
         * @option {Boolean} [tooltip.enabled] <true> Can be used to enable/disable the tooltip.
         * @option {String} [tooltip.format] <"{0}"> Can be used to formatting of the text of the tooltip. Note that the applied format will also influence the appearance of the rangeSlider tick labels.
         * @option {Number} [selectionStart] <0> The selection start value of the rangeSlider.
         * @option {Number} [selectionEnd] <10> The selection end value of the rangeSlider.
         * @option {String} [orientation] <"horizontal"> The orientation of the rangeSlider. Available options are "horizontal" and "vertical".
         * @option {String} [tickPlacement] <"both"> the location of the tick marks in the widget. Available options are:
         *     <dl>
         *         <dt>
         *              "topLeft"
         *         </dt>
         *         <dd>
         *              Tick marks are located on the top of the horizontal widget or on the left of the vertical widget.
         *         </dd>
         *         <dt>
         *              "bottomRight"
         *         </dt>
         *         <dd>
         *              Tick marks are located on the bottom of the horizontal widget or on the right side of the vertical widget.
         *         </dd>
         *         <dt>
         *              "both"
         *         </dt>
         *         <dd>
         *              Tick marks are located on both sides of the widget.
         *         </dd>
         *     </dl>
         * @option {Number} [smallStep] <1> The small step of the rangeSlider. The Value will be changed with SmallStep when the end user:
         *     <ul>
         *         <li>
         *             clicks on the Slider buttons
         *         </li>
         *         <li>
         *             presses the arrow keys (the drag handle must be focused)
         *         </li>
         *         <li>
         *             drag the drag handle
         *         </li>
         *     </ul>
         * @option {Number} [largeStep] <5> The delta with which the value will change when the user presses the Page Up or Page Down key (the drag handle must be focused). Note that the allied largeStep will also set large ticks for every large step.
         */
        init: function(element, options) {
            var that = this,
                inputs = $(element).find("input"),
                firstInput = inputs.eq(0)[0],
                secondInput = inputs.eq(1)[0];

            firstInput.type = "text";
            secondInput.type = "text";

            options = extend({}, {
                selectionStart: parseAttr(firstInput, "value"),
                min: parseAttr(firstInput, "min"),
                max: parseAttr(firstInput, "max"),
                smallStep: parseAttr(firstInput, "step")
            }, {
                selectionEnd: parseAttr(secondInput, "value"),
                min: parseAttr(secondInput, "min"),
                max: parseAttr(secondInput, "max"),
                smallStep: parseAttr(secondInput, "step")
            }, options);

            SliderBase.fn.init.call(that, element, options);
            options = that.options;
            that._setValueInRange(options.selectionStart, options.selectionEnd);

            var dragHandles = that.wrapper.find(DRAG_HANDLE);

            new RangeSlider.Selection(dragHandles, that, options);
            that._firstHandleDrag = new Slider.Drag(dragHandles.eq(0), "firstHandle", that, options);
            that._lastHandleDrag = new Slider.Drag(dragHandles.eq(1), "lastHandle" , that, options);
        },

        options: {
            name: "RangeSlider",
            selectionStart: 0,
            selectionEnd: 10
        },

        /**
         * Enables the rangeSlider.
         * @example
         * var rangeSlider = $("#rangeSlider").data("kendoRangeSlider");
         *
         * // enables the rangeSlider
         * rangeSlider.enable();
         */
        enable: function () {
            var that = this,
                options = that.options,
                clickHandler;

            that.wrapper
                .removeAttr(DISABLED)
                .removeClass(STATE_DISABLED)
                .addClass(STATE_DEFAULT);

            clickHandler = function (e) {
                if ($(e.target).hasClass("k-draghandle")) {
                    $(e.target).addClass(STATE_SELECTED);
                    return;
                }

                var location = kendo.touchLocation(e),
                    mousePosition = that._isHorizontal ? location.x : location.y,
                    dragableArea = that._getDragableArea(),
                    val = that._getValueFromPosition(mousePosition, dragableArea);

                if (val < options.selectionStart) {
                    that._setValueInRange(val, options.selectionEnd);
                    that._firstHandleDrag.dragstart(e);
                } else if (val > that.selectionEnd) {
                    that._setValueInRange(options.selectionStart, val);
                    that._lastHandleDrag.dragstart(e);
                } else {
                    if (val - options.selectionStart <= options.selectionEnd - val) {
                        that._setValueInRange(val, options.selectionEnd);
                        that._firstHandleDrag.dragstart(e);
                    } else {
                        that._setValueInRange(options.selectionStart, val);
                        that._lastHandleDrag.dragstart(e);
                    }
                }
            };

            that.wrapper
                .find(TICK_SELECTOR).bind(MOUSE_DOWN, clickHandler)
                .end()
                .find(TRACK_SELECTOR).bind(MOUSE_DOWN, clickHandler);

            that.wrapper.find(DRAG_HANDLE).bind(MOUSE_UP, function (e) {
                $(e.target).removeClass(STATE_SELECTED);
            });

            that.wrapper.find(DRAG_HANDLE)
                .eq(0).bind(KEY_DOWN,
                    proxy(function(e) {
                        this._keydown(e, "firstHandle");
                    }, that)
                )
                .end()
                .eq(1).bind(KEY_DOWN,
                    proxy(function(e) {
                        this._keydown(e, "lastHandle");
                    }, that)
                );

            that.options.enabled = true;
        },

        /**
         * Disables the rangeSlider.
         * @example
         * var rangeSlider = $("#rangeSlider").data("kendoRangeSlider");
         *
         * // disables the rangeSlider
         * rangeSlider.disable();
         */
        disable: function () {
            var that = this,
                options = that.options;

            that.wrapper
                .attr(DISABLED, DISABLED)
                .removeClass(STATE_DEFAULT)
                .addClass(STATE_DISABLED);

            that.wrapper
                .find(TICK_SELECTOR).unbind(MOUSE_DOWN)
                .end()
                .find(TRACK_SELECTOR).unbind(MOUSE_DOWN);

            that.wrapper
                .find(DRAG_HANDLE)
                .unbind(MOUSE_UP)
                .unbind(KEY_DOWN)
                .bind(KEY_DOWN, false);

            that.options.enabled = false;
        },

        _keydown: function (e, handle) {
            var that = this,
                selectionStartValue = that.options.selectionStart,
                selectionEndValue = that.options.selectionEnd;

            if (e.keyCode in that._keyMap) {
                if (handle == "firstHandle") {
                    selectionStartValue = that._keyMap[e.keyCode](selectionStartValue);

                    if (selectionStartValue > selectionEndValue) {
                        selectionEndValue = selectionStartValue;
                    }
                } else {
                    selectionEndValue = that._keyMap[e.keyCode](selectionEndValue);

                    if (selectionStartValue > selectionEndValue) {
                        selectionStartValue = selectionEndValue;
                    }
                }

                that._setValueInRange(selectionStartValue, selectionEndValue);
                e.preventDefault();
            }
        },

        _update: function (selectionStart, selectionEnd) {
            var that = this,
                values = that.values();

            var change = values[0] != selectionStart || values[1] != selectionEnd;

            that.values(selectionStart, selectionEnd);

            if (change) {
                that.trigger(CHANGE, { values: [selectionStart, selectionEnd] });
            }
        },

        /**
         * The values method gets or sets the selection start and end of the RangeSlider. The values method accepts {String}, {Number} or {Array} object as parameters, and returns a {Array} object with start and end selection values.
         * @example
         * var rangeSider = $("#rangeSlider").data("kendoRangeSlider");
         *
         * // Get or sets the selection start and end of the rangeSlider
         * rangeSlider.values();
         */
        values: function () {
            var that = this,
                options = that.options,
                selectionStart = 0,
                selectionEnd = 0;

            if (arguments.length == 0) {
                return [options.selectionStart, options.selectionEnd];
            } else if (arguments.length == 1 && $.isArray(arguments[0])) {
                selectionStart = arguments[0][0];
                selectionEnd = arguments[0][1];
            } else {
                selectionStart = round(arguments[0]);
                selectionEnd = round(arguments[1]);
            }

            if (selectionStart >= options.min && selectionStart <= options.max
            && selectionEnd >= options.min && selectionEnd <= options.max && selectionStart <= selectionEnd) {
                if (options.selectionStart != selectionStart || options.selectionEnd != selectionEnd) {
                    that.element.find("input")
                                .eq(0).attr("value", formatValue(selectionStart))
                                .end()
                                .eq(1).attr("value", formatValue(selectionEnd));

                    options.selectionStart = selectionStart;
                    options.selectionEnd = selectionEnd;
                    that.refresh();
                }
            }
        },

        refresh: function() {
            var that = this,
                options = that.options;

            that.trigger(MOVE_SELECTION, { values: [options.selectionStart, options.selectionEnd] });

            if (options.selectionStart == options.max && options.selectionEnd == options.max) {
                that._setZIndex("firstHandle");
            }
        },

        _setValueInRange: function (selectionStart, selectionEnd) {
            var options = this.options;

            selectionStart = math.max(math.min(selectionStart, options.max), options.min);

            selectionEnd = math.max(math.min(selectionEnd, options.max), options.min);

            if (selectionStart == options.max && selectionEnd == options.max) {
                this._setZIndex("firstHandle");
            }

            this._update(math.min(selectionStart, selectionEnd), math.max(selectionStart, selectionEnd));
        },

        _setZIndex: function (type) {
            this.wrapper.find(DRAG_HANDLE).each(function (index) {
                $(this).css("z-index", type == "firstHandle" ? 1 - index : index);
            });
        }
    });

    RangeSlider.Selection = function (dragHandles, that, options) {
        function moveSelection(values) {
            var selectionStartValue = values[0] - options.min,
                selectionEndValue = values[1] - options.min,
                selectionStartIndex = math.ceil(selectionStartValue / options.smallStep),
                selectionEndIndex = math.ceil(selectionEndValue / options.smallStep),
                selectionStart = that._pixelSteps[selectionStartIndex],
                selectionEnd = that._pixelSteps[selectionEndIndex],
                halfHandle = parseInt(dragHandles.eq(0)[that._outerSize]() / 2, 10);

            dragHandles.eq(0).css(that._position, selectionStart - halfHandle)
                       .end()
                       .eq(1).css(that._position, selectionEnd - halfHandle);

            makeSelection(selectionStart, selectionEnd);
        }

        function makeSelection(selectionStart, selectionEnd) {
            var selection = 0,
                selectionPosition = 0,
                selectionDiv = that._trackDiv.find(".k-slider-selection");

            selection = math.abs(selectionStart - selectionEnd);
            selectionPosition = selectionStart < selectionEnd ? selectionStart : selectionEnd;

            selectionDiv[that._size](selection);
            selectionDiv.css(that._position, selectionPosition - 1);
        }

        moveSelection(that.values());

        that.bind([ CHANGE, SLIDE, MOVE_SELECTION ], function (e) {
            moveSelection(e.values);
        });
    };

    kendo.ui.plugin(RangeSlider);

})(jQuery);
(function($, undefined) {
    /**
    * @name kendo.ui.Splitter.Description
    *
    * @section
    *   <p>
    *       The Splitter widget provides an easy way to create a dynamic layout of resizable and
    *       collapsible panes. The widget converts the children of an HTML element in to the interactive
    *       layout, adding resize and collapse handles based on configuration. Splitters can be mixed
    *       in both vertical and horizontal orientations to build complex layouts.
    *   </p>
    *   <h3>Getting Started</h3>
    *
    * @exampleTitle Create a root HTML div element with children that will become panes
    * @example
    * <div id="splitter">
    *    <div>
    *        Area 1
    *    </div>
    *    <div>
    *        Area 2
    *    </div>
    * </div>
    *
    * @exampleTitle Initialize the Splitter using a jQuery selector
    * @example
    *   $("#splitter").kendoSplitter();
    * @section
    *   <p>
    *       When the Splitter is initialized, a vertical split bar will be placed between the two
    *       HTML divs. This bar can be moved by a user left and right to adjust the size on the panes.
    *   </p>
    *   <h3>Configuring Splitter Behavior</h3>
    *   <p>
    *       Splitter provides many configuration options that can be easily set during initialization.
    *       Among the properties that can be controlled:
    *   </p>
    *   <ul>
    *       <li>Min/Max pane size</li>
    *       <li>Resizable and Collapsible pane behaviors</li>
    *       <li>Orientation of the splitter</li>
    *   </ul>
    *   <p>
    *       Pane properties are set for each individual pane in a Splitter,
    *       whereas Splitter properties apply to the entire widget.
    *   </p>
    * @exampleTitle Setting Splitter and Pane properties
    * @example
    *   $("#splitter").kendoSplitter({
    *       panes: [{
    *           min: "100px",
    *           max: "300px",
    *           collapsible: true
    *       },
    *       {
    *           collapsible: true
    *       }],
    *       orientation: "vertical"
    *   });
    * @section
    *   <h3>Nested Splitter Layouts</h3>
    *   <p>
    *       To achieve complex layouts, it may be necessary to nest Splitters in different orientations.
    *       Splitter fully supports nested configurations. All that is required is proper HTML
    *       configuration and multiple Kendo Splitter initializations.
    *   </p>
    * @exampleTitle Creating nested Splitter layout
    * @example
    *   <!-- Define nested HTML layout with divs -->
    *   <div id="horizontalSplitter">
    *       <div><p>Left Side Pane Content</p></div>
    *       <div>
    *           <div id="verticalSplitter">
    *               <div><p>Right Side, Top Pane Content</p></div>
    *               <div><p>Right Side, Bottom Pane Content</p></div>
    *           </div>
    *       </div>
    *   </div>
    * @exampleTitle
    * @example
    *   // Initialize both Splitters with the proper orientation
    *   $(document).ready(function() {
    *       $("horizontalSplitter").kendoSplitter();
    *       $("verticalSplitter").kendoSplitter({ orientation: "vertical" });
    *   });
    *
    * @section
    *   <h3>Loading Content with Ajax</h3>
    *   <p>
    *       While any valid technique for loading Ajax content can be used, Splitter provides built-in
    *       support for asynchronously loading content from URLs. These URLs should return HTML fragments
    *       that can be loaded in a Splitter pane. If you want to load a whole page in an IFRAME,
    *       you can do so by specifying the complete URL (e.g. http://kendoui.com/)
    *       Ajax content loading must be configured for each Pane that should use it.
    *   </p>
    * @exampleTitle Loading Splitter content asynchronously
    * @example
    *   <!-- Define the Splitter HTML -->
    *   <div id="splitter">
    *       <div>Area 1 with Static Content</div>
    *       <div></div>
    *       <div></div>
    *   </div>
    * @exampleTitle
    * @example
    *   // Initialize the Splitter and configure async loading for one pane, and an iframe for a thrid pane
    *   $(document).ready(function() {
    *       $("#splitter").kendoSplitter({
    *           panes: [
    *               {},
    *               { contentUrl: "html-content-snippet.html" },
    *               { contentUrl: "http://kendoui.com" }
    *           ]
    *       });
    *   });
    */
    var kendo = window.kendo,
        ui = kendo.ui,
        extend = $.extend,
        proxy = $.proxy,
        Widget = ui.Widget,
        pxUnitsRegex = /^\d+(\.\d+)?px$/i,
        percentageUnitsRegex = /^\d+(\.\d+)?%$/i,
        EXPAND = "expand",
        COLLAPSE = "collapse",
        CONTENTLOAD = "contentLoad",
        RESIZE = "resize",
        LAYOUTCHANGE = "layoutChange",
        HORIZONTAL = "horizontal",
        VERTICAL = "vertical",
        MOUSEENTER = "mouseenter",
        CLICK = "click",
        PANE = "pane",
        MOUSELEAVE = "mouseleave";

    function isPercentageSize(size) {
        return percentageUnitsRegex.test(size);
    }

    function isPixelSize(size) {
        return pxUnitsRegex.test(size);
    }

    function isFluid(size) {
        return !isPercentageSize(size) && !isPixelSize(size);
    }

    function panePropertyAccessor(propertyName, triggersResize) {
        return function(pane, value) {
            var paneConfig = $(pane).data(PANE);

            if (arguments.length == 1) {
                return paneConfig[propertyName];
            }

            paneConfig[propertyName] = value;

            if (triggersResize) {
                var splitter = this.element.data("kendoSplitter");
                splitter.trigger(RESIZE);
            }
        };
    }

    var Splitter = Widget.extend(/** @lends kendo.ui.Splitter.prototype */ {
        /**
         * Creates a Splitter instance.
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {String} [orientation] <horizontal> Specifies the orientation of the splitter.
         *    <dl>
         *         <dt>
         *              "horizontal"
         *         </dt>
         *         <dd>
         *              Define horizontal orientation of the splitter.
         *         </dd>
         *         <dt>
         *              "vertical"
         *         </dt>
         *         <dd>
         *              Define vertical orientation of the splitter.
         *         </dd>
         *    </dl>
         * @option {Array} [panes] Array of pane definitions.
         * _example
         *  $("#splitter").kendoSplitter({
         *      // definitions for the first three panes
         *      panes: [
         *          {
         *              size: "200px",
         *              min: "100px",
         *              max: "300px"
         *          },
         *          {
         *              size: "20%",
         *              resizable: false
         *         },
         *         {
         *              collapsed: true,
         *              collapsible: true
         *         }
         *      ]
         *   });
         * @option {String} [panes.size] Specifies the size of the pane.
         * <p>
         * The size can be defined in pixes or in percents.
         * </p>
         * <p>
         * The size cannot be more than panes.max and less then panes.min.
         * </p>
         * @option {String} [panes.min] Specifies the minimum size of the pane.
         * <p>
         * Resized pane cannot be smaller then the defined minimum size.
         * </p>
         * @option {String} [panes.max] Specifies the maximum size of the pane.
         * <p>
         * Resized pane cannot be bigger then the defined maximum size.
         * </p>
         * @option {Boolean} [panes.collapsed] <false> Specifies whether the pane is initially collapsed.
         * @option {Boolean} [panes.collapsible] <false> Specifies whether the pane can be collapsed by the user.
         * @option {Boolean} [panes.scrollable] <true> Specifies whether the pane shows a scrollbar when its content overflows.
         * @option {Boolean} [panes.resizable] <true> Specifies whether the pane can be resized by the user.
         * @option {Boolean} [panes.contentUrl] <true> Specifies URL from which to load the pane content.
         */
        init: function(element, options) {
            var that = this,
                panesConfig,
                splitbarSelector,
                expandCollapseSelector = ".k-splitbar .k-icon:not(.k-resize-handle)",
                triggerResize = function() {
                    that.trigger(RESIZE);
                };

            Widget.fn.init.call(that, element, options);

            that.orientation = that.options.orientation.toLowerCase() != VERTICAL ? HORIZONTAL : VERTICAL;
            splitbarSelector = ".k-splitbar-draggable-" + that.orientation;

            that.bind([
                /**
                 * Fires before a pane is expanded.
                 * @name kendo.ui.Splitter#expand
                 * @event
                 * @param {Event} e
                 * @param {Element} e.pane The expanding pane
                 */
                EXPAND,
                /**
                 * Fires before a pane is collapsed.
                 * @name kendo.ui.Splitter#collapse
                 * @event
                 * @param {Event} e
                 * @param {Element} e.pane The collapsing pane
                 */
                COLLAPSE,
                /**
                 * Fires when a request for the pane contents has finished
                 * @name kendo.ui.Splitter#contentLoad
                 * @event
                 * @param {Event} e
                 * @param {Element} e.pane The pane whose content has been loaded.
                 */
                CONTENTLOAD,
                /**
                 * Fires when a pane is resized
                 * @name kendo.ui.Splitter#resize
                 * @event
                 * @param {Event} e
                 * @param {Element} e.pane The pane which is resized
                 */
                RESIZE,
                /**
                 * Fires when the splitter layout has changed
                 * @name kendo.ui.Splitter#layoutChange
                 * @event
                 */
                LAYOUTCHANGE
            ], that.options);

            that.bind(RESIZE, proxy(that._resize, that));

            that._initPanes();

            that.element
                .delegate(splitbarSelector, MOUSEENTER, function() { $(this).addClass("k-splitbar-" + that.orientation + "-hover"); })
                .delegate(splitbarSelector, MOUSELEAVE, function() { $(this).removeClass("k-splitbar-" + that.orientation + "-hover"); })
                .delegate(splitbarSelector, "mousedown", function() { that.element.find("> .k-pane > .k-content-frame").after("<div class='k-overlay' />"); })
                .delegate(expandCollapseSelector, MOUSEENTER, function() { $(this).addClass("k-state-hover")})
                .delegate(expandCollapseSelector, MOUSELEAVE, function() { $(this).removeClass('k-state-hover')})
                .delegate(".k-splitbar .k-collapse-next, .k-splitbar .k-collapse-prev", CLICK, that._arrowClick(COLLAPSE))
                .delegate(".k-splitbar .k-expand-next, .k-splitbar .k-expand-prev", CLICK, that._arrowClick(EXPAND))
                .delegate(".k-splitbar", "dblclick", proxy(that._dbclick, that))
                .parent().closest(".k-splitter").each(function() {
                    $(this).data("kendoSplitter").bind(RESIZE, triggerResize);
                });

            $(window).resize(triggerResize);

            that.resizing = new PaneResizing(that);
        },

        options: {
            name: "Splitter",
            orientation: HORIZONTAL
        },

        _initPanes: function() {
            var that = this,
                panesConfig = that.options.panes || [];

            that.element
                .addClass("k-widget").addClass("k-splitter")
                .children()
                    .addClass("k-pane")
                    .each(function (index, pane) {
                        var config = panesConfig && panesConfig[index];

                        pane = $(pane);

                        pane.data(PANE, config ? config : {})
                            .toggleClass("k-scrollable", config ? config.scrollable !== false : true);
                        that.ajaxRequest(pane);
                    })
                .end();
            that.trigger(RESIZE);
        },

        /**
        * Loads the pane content from the specified URL.
        * @param {Selector|DomElement|jQueryObject} pane The pane whose content should be loaded.
        * @param {String} url The URL which returns the pane content.
        * @param {Object|String} data Data to be sent to the server.
        * @example
        * splitter.ajaxRequest("#Pane1", "/customer/profile", { id: 42 });
        */
        ajaxRequest: function(pane, url, data) {
            pane = $(pane);

            var that = this,
                paneConfig = pane.data(PANE);

            url = url || paneConfig.contentUrl;

            if (url) {
                pane.append("<span class='k-icon k-loading k-pane-loading' />");

                if (kendo.isLocalUrl(url)) {
                    $.ajax({
                        url: url,
                        data: data || {},
                        type: "GET",
                        dataType: "html",
                        success: function (data) {
                            pane.html(data);

                            that.trigger(CONTENTLOAD, { pane: pane[0] });
                        }
                    });
                } else {
                    pane.removeClass("k-scrollable")
                        .html("<iframe src='" + url + "' frameborder='0' class='k-content-frame'>" +
                                "This page requires frames in order to show content" +
                            + "</iframe>");
                }
            }
        },
        _triggerAction: function(type, pane) {
            if (!this.trigger(type, { pane: pane[0] })) {
                this[type](pane[0]);
            }
        },
        _dbclick: function(e) {
            var that = this,
                target = $(e.target),
                arrow;

            if (target.closest(".k-splitter")[0] != that.element[0]) {
                return;
            }

            arrow = target.children(".k-icon:not(.k-resize-handle)");

            if (arrow.length !== 1) {
                return;
            }

            if (arrow.is(".k-collapse-prev")) {
                that._triggerAction(COLLAPSE, target.prev());
            } else if (arrow.is(".k-collapse-next")) {
                that._triggerAction(COLLAPSE, target.next());
            } else if (arrow.is(".k-expand-prev")) {
                that._triggerAction(EXPAND, target.prev());
            } else if (arrow.is(".k-expand-next")) {
                that._triggerAction(EXPAND, target.next());
            }
        },
        _arrowClick: function (arrowType) {
            var that = this;

            return function(e) {
                var target = $(e.target),
                    pane;

                if (target.closest(".k-splitter")[0] != that.element[0])
                    return;

                if (target.is(".k-" + arrowType + "-prev")) {
                    pane = target.parent().prev();
                } else {
                    pane = target.parent().next();
                }
                that._triggerAction(arrowType, pane);
            };
        },
        _updateSplitBar: function(splitBar, previousPane, nextPane) {
            var catIconIf = function(iconType, condition) {
                   return condition ? "<div class='k-icon " + iconType + "' />" : "";
                },
                orientation = this.orientation,
                draggable = (previousPane.resizable !== false) && (nextPane.resizable !== false),
                prevCollapsible = previousPane.collapsible,
                prevCollapsed = previousPane.collapsed,
                nextCollapsible = nextPane.collapsible,
                nextCollapsed = nextPane.collapsed;

            splitBar.addClass("k-splitbar k-state-default k-splitbar-" + orientation)
                .removeClass("k-splitbar-" + orientation + "-hover")
                .toggleClass("k-splitbar-draggable-" + orientation,
                    draggable && !prevCollapsed && !nextCollapsed)
                .toggleClass("k-splitbar-static-" + orientation,
                    !draggable && !prevCollapsible && !nextCollapsible)
                .html(
                    catIconIf("k-collapse-prev", prevCollapsible && !prevCollapsed && !nextCollapsed) +
                    catIconIf("k-expand-prev", prevCollapsible && prevCollapsed && !nextCollapsed) +
                    catIconIf("k-resize-handle", draggable) +
                    catIconIf("k-collapse-next", nextCollapsible && !nextCollapsed && !prevCollapsed) +
                    catIconIf("k-expand-next", nextCollapsible && nextCollapsed && !prevCollapsed)
                );
        },
        _updateSplitBars: function() {
            var that = this;

            this.element.children(".k-splitbar").each(function() {
                var splitbar = $(this),
                    previousPane = splitbar.prev(".k-pane").data(PANE),
                    nextPane = splitbar.next(".k-pane").data(PANE);

                if (!nextPane) {
                    return;
                }

                that._updateSplitBar(splitbar, previousPane, nextPane);
            });
        },
        _resize: function() {
            var that = this,
                element = that.element,
                panes = element.children(":not(.k-splitbar)"),
                isHorizontal = that.orientation == HORIZONTAL,
                splitBars = element.children(".k-splitbar"),
                splitBarsCount = splitBars.length,
                sizingProperty = isHorizontal ? "width" : "height",
                totalSize = element[sizingProperty]();

            if (splitBarsCount === 0) {
                splitBarsCount = panes.length - 1;
                panes.slice(0, splitBarsCount).after("<div class='k-splitbar' />");
                that._updateSplitBars();
                splitBars = element.children(".k-splitbar");
            } else {
                that._updateSplitBars();
            }

            // discard splitbar sizes from total size
            splitBars.each(function() {
                totalSize -= this[isHorizontal ? "offsetWidth" : "offsetHeight"];
            });

            var sizedPanesWidth = 0,
                sizedPanesCount = 0,
                freeSizedPanes = $();

            panes.css({ position: "absolute", top: 0 })
                [sizingProperty](function() {
                    var config = $(this).data(PANE) || {}, size;

                    if (config.collapsed) {
                        size = 0;
                    } else if (isFluid(config.size)) {
                        freeSizedPanes = freeSizedPanes.add(this);
                        return;
                    } else { // sized in px/%, not collapsed
                        size = parseInt(config.size, 10);

                        if (isPercentageSize(config.size)) {
                            size = Math.floor(size * totalSize / 100);
                        }
                    }

                    sizedPanesCount++;
                    sizedPanesWidth += size;

                    return size;
                });

            totalSize -= sizedPanesWidth;

            var freeSizePanesCount = freeSizedPanes.length,
                freeSizePaneWidth = Math.floor(totalSize / freeSizePanesCount);

            freeSizedPanes
                .slice(0, freeSizePanesCount - 1)
                    .css(sizingProperty, freeSizePaneWidth)
                .end()
                .eq(freeSizePanesCount - 1)
                    .css(sizingProperty, totalSize - (freeSizePanesCount - 1) * freeSizePaneWidth);

            // arrange panes
            var sum = 0,
                alternateSizingProperty = isHorizontal ? "height" : "width",
                positioningProperty = isHorizontal ? "left" : "top",
                sizingDomProperty = isHorizontal ? "offsetWidth" : "offsetHeight";

            element.children()
                .css(alternateSizingProperty, element[alternateSizingProperty]())
                .each(function (i, child) {
                    child.style[positioningProperty] = Math.floor(sum) + "px";
                    sum += child[sizingDomProperty];
                });

            that.trigger(LAYOUTCHANGE);
        },
        toggle: function(pane, expand) {
            var pane = $(pane),
                paneConfig = pane.data(PANE);

            if (arguments.length == 1) {
                expand = paneConfig.collapsed === undefined ? false : paneConfig.collapsed;
            }

            paneConfig.collapsed = !expand;

            this.trigger(RESIZE);
        },
        /**
        * Collapses the specified Pane item
        * @param {Selector|DomElement|jQueryObject} pane The pane, which will be collapsed.
        * @example
        * splitter.collapse("#Item1"); // id of the first pane
        */
        collapse: function(pane) {
            this.toggle(pane, false);
        },
        /**
        * Expands the specified Pane item
        * @param {Selector|DomElement|jQueryObject} pane The pane, which will be expanded.
        * @example
        * splitter.expand("#Item1"); // id of the first pane
        */
        expand: function(pane) {
            this.toggle(pane, true);
        },
        /**
        * Set the size of the pane.
        * @name kendo.ui.Splitter#size
        * @function
        * @param {Selector|DomElement|jQueryObject} pane The pane
        * @param {String} value The new size of the pane.
        * @example
        * splitter.size("#Item1", "200px");
        */
        size: panePropertyAccessor("size", true),
        /**
        * Set the minimum size of the pane.
        * @name kendo.ui.Splitter#min
        * @function
        * @param {Selector|DomElement|jQueryObject} pane The pane
        * @param {String} value The minimum size value.
        * @example
        * splitter.min("#Item1", "100px");
        */
        min: panePropertyAccessor("min"),
        /**
        * Set the maximum size of the pane.
        * @name kendo.ui.Splitter#max
        * @function
        * @param {Selector|DomElement|jQueryObject} pane The pane
        * @param {String} value The maximum size value.
        * @example
        * splitter.max("#Item1", "300px");
        */
        max: panePropertyAccessor("max")
    });

    ui.plugin(Splitter);

    var verticalDefaults = {
            sizingProperty: "height",
            sizingDomProperty: "offsetHeight",
            alternateSizingProperty: "width",
            positioningProperty: "top",
            mousePositioningProperty: "pageY"
        };

    var horizontalDefaults = {
            sizingProperty: "width",
            sizingDomProperty: "offsetWidth",
            alternateSizingProperty: "height",
            positioningProperty: "left",
            mousePositioningProperty: "pageX"
        };

    function PaneResizing(splitter) {
        var that = this,
            orientation = splitter.orientation;

        that.owner = splitter;
        that._element = splitter.element;
        that.orientation = orientation;

        extend(that, orientation === HORIZONTAL ? horizontalDefaults : verticalDefaults);

        that._resizable = new kendo.ui.Resizable(splitter.element, {
            orientation: orientation,
            handle: ".k-splitbar-draggable-" + orientation,
            hint: proxy(that._createHint, that),
            start: proxy(that._start, that),
            max: proxy(that._max, that),
            min: proxy(that._min, that),
            invalidClass:"k-restricted-size-" + orientation,
            resizeend: proxy(that._stop, that)
        });
    }

    PaneResizing.prototype = {
        _createHint: function(handle) {
            var that = this;
            return $("<div class='k-ghost-splitbar k-ghost-splitbar-" + that.orientation + " k-state-default' />")
                        .css(that.alternateSizingProperty, handle[that.alternateSizingProperty]())
        },
        _start: function(e) {
            var that = this,
                splitBar = $(e.currentTarget),
                previousPane = splitBar.prev(),
                nextPane = splitBar.next(),
                previousPaneConfig = previousPane.data(PANE),
                nextPaneConfig = nextPane.data(PANE),
                prevBoundary = parseInt(previousPane[0].style[that.positioningProperty]),
                nextBoundary = parseInt(nextPane[0].style[that.positioningProperty]) + nextPane[0][that.sizingDomProperty] - splitBar[0][that.sizingDomProperty],
                totalSize = that._element.css(that.sizingProperty),
                toPx = function (value) {
                    var val = parseInt(value, 10);
                    return (isPixelSize(value) ? val : (totalSize * val) / 100) || 0;
                },
                prevMinSize = toPx(previousPaneConfig.min),
                prevMaxSize = toPx(previousPaneConfig.max) || nextBoundary - prevBoundary,
                nextMinSize = toPx(nextPaneConfig.min),
                nextMaxSize = toPx(nextPaneConfig.max) || nextBoundary - prevBoundary;

            that.previousPane = previousPane;
            that.nextPane = nextPane;
            that._maxPosition = Math.min(nextBoundary - nextMinSize, prevBoundary + prevMaxSize);
            that._minPosition = Math.max(prevBoundary + prevMinSize, nextBoundary - nextMaxSize);
        },
        _max: function(e) {
              return this._maxPosition;
        },
        _min: function(e) {
            return this._minPosition;
        },
        _stop: function(e) {
            var that = this,
                splitBar = $(e.currentTarget);

            splitBar.siblings(".k-pane").find("> .k-content-frame + .k-overlay").remove();

            if (e.keyCode !== kendo.keys.ESC) {
                var ghostPosition = e.position,
                    previousPane = splitBar.prev(),
                    nextPane = splitBar.next(),
                    previousPaneConfig = previousPane.data(PANE),
                    nextPaneConfig = nextPane.data(PANE),
                    previousPaneNewSize = ghostPosition - parseInt(previousPane[0].style[that.positioningProperty]),
                    nextPaneNewSize = parseInt(nextPane[0].style[that.positioningProperty]) + nextPane[0][that.sizingDomProperty] - ghostPosition - splitBar[0][that.sizingDomProperty],
                    fluidPanesCount = that._element.children(".k-pane").filter(function() { return isFluid($(this).data(PANE).size); }).length;

                if (!isFluid(previousPaneConfig.size) || fluidPanesCount > 1) {
                    if (isFluid(previousPaneConfig.size)) {
                        fluidPanesCount--;
                    }

                    previousPaneConfig.size = previousPaneNewSize + "px";
                }

                if (!isFluid(nextPaneConfig.size) || fluidPanesCount > 1) {
                    nextPaneConfig.size = nextPaneNewSize + "px";
                }

                that.owner.trigger(RESIZE);
            }

            return false;
        }
    }

})(jQuery);
(function($, undefined) {
    /**
     * @name kendo.ui.Upload.Description
     *
     * @section
     * <p>
     * The Upload widget uses progressive enhancement to deliver the best possible
     * uploading experience to users without requiring any extra developer effort.
     * Upload is packed with features, including:
     * </p>
     *
     * <ul>
     *    <li>Asynchronous and synchronous (on form submit) file upload</li>
     *    <li>Multiple file selection</li>
     *    <li>Removing uploaded files</li>
     *    <li>Progress tracking *</li>
     *    <li>File Drag-and-Drop *</li>
     *    <li>Cancelling upload in progress *</li>
     * </ul>
     * <p>
     * * These features are automatically enabled if supported by the browser.
     * </p>
     * <p>
     * Upload is a standards-based widget. No plug-ins required.
     * </p>
     *
     * <h3>
     * Getting Started
     * </h3>
     * <p>
     * There are two primary ways to configure Upload:
     * </p>
     * <ol>
     *     <li>For synchronous upload using an HTML form and input</li>
     *     <li>For asynchronous upload using a simple HTML input</li>
     * </ol>
     * <p>
     * The async upload is implemented using the new HTML5 File API,
     * but it will gracefully degrade and continue to function
     * in legacy browsers (using a hidden IFRAME). If placed inside a form,
     * queued and partially uploaded files will be sent synchronously if
     * the async upload is submitted with the form.
     * </p>
     *
     * <h3>
     * Configuring for synchronous upload
     * </h3>
     * @exampleTitle 1. Create a simple HTML form and input element of type "file"
     * @example
     * <!-- Kendo will automatically set the proper FORM enctype attribute -->
     * <form method="post" action="handler.php">
     *     <div>
     *         <input name="files" id="files" type="file" />
     *     </div>
     * </form>
     *
     * @exampleTitle 2. Initialize Upload with a jQuery selector
     * @example
     *    $(document).ready(function() {
     *        $("#files").kendoUpload();
     *    });
     *
     * @section
     * <p>
     * It’s important to note that some type of server-side handler is needed
     * to process and save the uploaded files. There are different server-side
     * techniques for handling file uploads depending on the technology you use.
     * Please consult the documentation for your server technology
     * to understand how to implement a basic file handler.
     * </p>
     *
     * <h3>
     * Configure for async upload
     * </h3>
     * @exampleTitle
     * 1. Create a simple HTML input of type "file" (no HTML form is required*)
     * @example
     * <input name="files[]" id="files" type="file" />
     *
     * @exampleTitle
     * 2. Initialize Upload and configure async upload end-points
     *
     * @example
     * $("#files").kendoUpload({
     *     async: {
     *         saveUrl: "saveHandler.php",
     *         removeUrl: "removeHandler.php",
     *         removeField: "fileNames[]",
     *         autoUpload: true
     *     }
     * });
     *
     * @section
     * <p>
     * Like synchronous uploads, the async upload requires a server-side handler
     * to process and save (or remove) the uploaded files. The handlers need to
     * accept POST requests. The save action will POST the file upload to the handler
     * (similar to synchronous uploads). The remove action will POST only the name of
     * the file that should be removed on the server.
     * </p>
     * <p>
     * Both handlers should return either:
     * </p>
     * <ul>
     *     <li>
     *         An empty response to signify success.
     *     </li>
     *     <li>
     *         Response containing JSON string with "text/plain" content encoding.
     *         The deserialized object can be accessed in the <strong>success</strong> event handler.
     *     </li>
     *     <li>
     *         Any other response to signify error.
     *     </li>
     * </ul>
     *
     * <h3>
     * Configuring Upload behavior
     * </h3>
     * <p>
     * Upload enables most behaviors by default, providing the richest experience possible
     * depending on browser capabilities. Behaviors can be easily configured, though,
     * using simple configuration properties. Refer to the Upload demo Configuration
     * tab for more information on available properties.
     * </p>
     * @exampleTitle
     * Disable Upload default behaviors
     * @example
     * $("#upload").kendoUpload({
     *     multiple: false,
     *     showFileList: false
     * });
     */
    var kendo = window.kendo,
        Widget = kendo.ui.Widget,
        rFileExtension = /\.([^\.]+)$/,
        SELECT = "select",
        UPLOAD = "upload",
        SUCCESS = "success",
        ERROR = "error",
        COMPLETE = "complete",
        CANCEL = "cancel",
        LOAD = "load",
        REMOVE = "remove";

    var Upload = Widget.extend(/** @lends kendo.ui.Upload.prototype */{
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Boolean} [enabled] <true>
         * Can be used to disable the upload. A disabled upload can be enabled by calling enable().
         * @option {Boolean} [multiple] <true>
         * Enables or disables multiple file selection.
         * If set to false, users will be able to select only one file at a time.
         * Note: This option does not limit the total number of uploaded files
         * in asynchronous configuration.
         * @option {Boolean} [showFileList] <true>
         * Controls whether to show the list of uploaded files.
         * Hiding the list can be useful when you want to fully customize the UI.
         * Use the client-side events to build your own UI.
         * @option {Object} [async]
         * Configures the upload for asynchronous operation.
         * <dl>
         *     <dt>
         *         saveUrl: (String)
         *     </dt>
         *     <dd>
         *         The URL of the handler that will receive the submitted files.
         *         The handler must accept POST requests containing one or more
         *         fields with the same name as the original input name.
         *     </dd>
         *     <dt>
         *         saveField: (String)
         *     </dt>
         *     <dd>
         *         The name of the form field submitted to the Save URL.
         *         The default value is the input name.
         *     </dd>
         *     <dt>
         *         removeUrl: (String)
         *     </dt>
         *     <dd>
         *         The URL of the handler responsible for removing uploaded files (if any).
         *         The handler must accept POST requests containing one or more
         *         "fileNames" fields specifying the files to be deleted.
         *     </dd>
         *     <dt>
         *         removeVerb: (String)
         *     </dt>
         *     <dd>
         *         The HTTP verb to be used by the remove action.
         *         The default value is "DELETE".
         *     </dd>
         *     <dt>
         *         removeField: (String)
         *     </dt>
         *     <dd>
         *         The name of the form field submitted to the Remove URL.
         *         The default value is fileNames.
         *     </dd>
         *     <dt>
         *         autoUpload: (Boolean)
         *     </dt>
         *     <dd>
         *         The selected files will be uploaded immediately by default.
         *         You can change this behavior by setting autoUpload to false.
         *     </dd>
         * </dl>
         * <p>
         * The save and remove handlers should return either:
         * </p>
         * <ul>
         *     <li>
         *         An empty response to signify success.
         *     </li>
         *     <li>
         *         Response containing JSON string with "text/plain" content encoding.
         *         The deserialized object can be accessed in the <strong>success</strong> event handler.
         *     </li>
         *     <li>
         *         Any other response to signify error.
         *     </li>
         * </ul>
         * <p>
         *     <strong>Fallback to synchronous upload</strong>
         * </p>
         * The Upload has a fallback mechanism when it is placed inside a form
         * and is configured for asynchronous operation.
         * Any files that were not fully uploaded will be sent as part of the form
         * when the user submits it. This ensures that no files will be lost,
         * even if you do not take any special measures to block the submit button during upload.
         * <p><em>
         * You have to handle the uploaded files both in the save handler and in the form submit action.
         * </em></p>
         */
        init: function(element, options) {
            var that = this;

            Widget.fn.init.call(that, element, options);

            that.name = element.name;
            that.multiple = that.options.multiple;
            that.localization = that.options.localization;

            var activeInput = that.element;
            that.wrapper = activeInput.closest(".k-upload");
            if (that.wrapper.length == 0) {
                that.wrapper = that._wrapInput(activeInput);
            }

            that._activeInput(activeInput);
            that.toggle(that.options.enabled);

            activeInput.closest("form").bind({
                "submit": $.proxy(that._onParentFormSubmit, that),
                "reset": $.proxy(that._onParentFormReset, that)
            });

            if (that.options.async.saveUrl != undefined) {
                that._module = that._supportsFormData() ?
                new formDataUploadModule(that) :
                new iframeUploadModule(that);
            } else {
                that._module = new syncUploadModule(that);
            }

            if (that._supportsDrop()) {
                that._setupDropZone();
            }

            that.wrapper
            .delegate(".k-upload-action", "click", $.proxy(that._onFileAction, that))
            .delegate(".k-upload-selected", "click", $.proxy(that._onUploadSelected, that))
            .delegate(".k-file", "t:progress", $.proxy(that._onFileProgress, that))
            .delegate(".k-file", "t:upload-success", $.proxy(that._onUploadSuccess, that))
            .delegate(".k-file", "t:upload-error", $.proxy(that._onUploadError, that));

            that.bind([
                /**
                 * Fires when one or more files are selected.
                 * Cancelling the event will prevent the selection.
                 * @name kendo.ui.Upload#select
                 * @event
                 * @param {Event} e
                 * @param {Array} e.files
                 * List of the selected files. Each file has:
                 * <ul>
                 *     <li>name</li>
                 *     <li>
                 *         extension - the file extension
                 *         inlcuding the leading dot - ".jpg", ".png", etc.
                 *      </li>
                 *     <li>size - the file size in bytes (null if not available)</li>
                 * </ul>
                 */
                SELECT,

                /**
                 * Fires when one or more files are about to be uploaded.
                 * Cancelling the event will prevent the upload.
                 * @name kendo.ui.Upload#upload
                 * @event
                 * @param {Event} e
                 * @param {Array} e.files
                 * List of the files that will be uploaded. Each file has:
                 * <ul>
                 *     <li>name</li>
                 *     <li>
                 *         extension - the file extension
                 *         inlcuding the leading dot - ".jpg", ".png", etc.
                 *      </li>
                 *     <li>size - the file size in bytes (null if not available)</li>
                 * </ul>
                 * @param {Object} data - undefined by default,
                 * but can be set to a custom object to pass information to the save handler.
                 */
                UPLOAD,

                /**
                 * Fires when an upload / remove operation has been completed successfully.
                 * @name kendo.ui.Upload#success
                 * @event
                 * @param {Event} e
                 * @param {Array} e.files
                 * List of the files that were uploaded or removed . Each file has:
                 * <ul>
                 *     <li>name</li>
                 *     <li>
                 *         extension - the file extension
                 *         inlcuding the leading dot - ".jpg", ".png", etc.
                 *      </li>
                 *     <li>size - the file size in bytes (null if not available)</li>
                 * </ul>
                 * @param {String} e.operation - "upload" or "remove".
                 * @param {String} e.response - the response object returned by the server.
                 * @param {Object} e.XMLHttpRequest
                 * This is either the original XHR used for the operation or a stub containing:
                 * <ul>
                 *     <li>responseText</li>
                 *     <li>status</li>
                 *     <li>statusText</li>
                 * </ul>
                 * Verify that this is an actual XHR before accessing any other fields.
                 */
                SUCCESS,

                /**
                 * Fires when an upload / remove operation has failed.
                 * @name kendo.ui.Upload#error
                 * @event
                 * @param {Event} e
                 * @param {Array} e.files
                 * List of the files that were uploaded or removed . Each file has:
                 * <ul>
                 *     <li>name</li>
                 *     <li>
                 *         extension - the file extension
                 *         inlcuding the leading dot - ".jpg", ".png", etc.
                 *      </li>
                 *     <li>size - the file size in bytes (null if not available)</li>
                 * </ul>
                 * @param {String} e.operation - "upload" or "remove".
                 * @param {Object} e.XMLHttpRequest
                 * This is either the original XHR used for the operation or a stub containing:
                 * <ul>
                 *     <li>responseText</li>
                 *     <li>status</li>
                 *     <li>statusText</li>
                 * </ul>
                 * Verify that this is an actual XHR before accessing any other fields.
                 */
                ERROR,

                /**
                 * Fires when all active uploads have completed either successfully or with errors.
                 * @name kendo.ui.Upload#complete
                 * @event
                 * @param {Event} e
                 */
                COMPLETE,

                /**
                 * Fires when the upload has been cancelled while in progress.
                 * @name kendo.ui.Upload#cancel
                 * @event
                 * @param {Event} e
                 * @param {Array} e.files
                 * List of the files that were uploaded or removed . Each file has:
                 * <ul>
                 *     <li>name</li>
                 *     <li>
                 *         extension - the file extension
                 *         inlcuding the leading dot - ".jpg", ".png", etc.
                 *      </li>
                 *     <li>size - the file size in bytes (null if not available)</li>
                 * </ul>
                 */
                CANCEL,

                /**
                 * Fires when an uploaded file is about to be removed.
                 * Cancelling the event will prevent the remove.
                 * @name kendo.ui.Upload#remove
                 * @event
                 * @param {Event} e
                 * @param {Array} e.files
                 * List of the files that were uploaded or removed . Each file has:
                 * <ul>
                 *     <li>name</li>
                 *     <li>
                 *         extension - the file extension
                 *         inlcuding the leading dot - ".jpg", ".png", etc.
                 *      </li>
                 *     <li>size - the file size in bytes (null if not available)</li>
                 * </ul>
                 * @param {Object} e.data - undefined by default,
                 * but can be set to a custom object to pass information to the save handler.
                 */
                REMOVE], that.options);
        },

        options: {
            name: "Upload",
            enabled: true,
            multiple: true,
            showFileList: true,
            async: {
                removeVerb: "POST",
                autoUpload: true
            },
            localization: {
                "select": "Select...",
                "cancel": "Cancel",
                "retry": "Retry",
                "remove": "Remove",
                "uploadSelectedFiles": "Upload files",
                "dropFilesHere": "drop files here to upload",
                "statusUploading": "uploading",
                "statusUploaded": "uploaded",
                "statusFailed": "failed"
            }
        },

        /**
         * Enables the upload.
         * @example
         * var upload = $("#upload").data("kendoUpload");
         *
         * // enables the upload
         * upload.enable();
         */
        enable: function() {
            this.toggle(true);
        },

        /**
         * Disables the upload.
         * @example
         * var upload = $("#upload").data("kendoUpload");
         *
         * // disables the upload
         * upload.enable();
         */
        disable: function() {
            this.toggle(false);
        },

        /**
         * Toggles the upload enabled state.
         * @param {Boolean} enable (Optional) The new enabled state.
         * @example
         * var upload = $("#upload").data("kendoUpload");
         *
         * // toggles the upload enabled state
         * upload.toggle();
         */
        toggle: function(enable) {
            enable = typeof enable === "undefined" ? enable : !enable;
            this.wrapper.toggleClass("k-state-disabled", enable);
        },

        _addInput: function(input) {
            var that = this;

            input
                .insertAfter(that.element)
                .data("kendoUpload", that);

            $(that.element)
                .hide()
                .removeAttr("id");

            that._activeInput(input);
        },

        _activeInput: function(input) {
            var that = this,
                wrapper = that.wrapper;

            that.element = input;

            input
                .attr("multiple", that._supportsMultiple() ? that.multiple : false)
                .attr("autocomplete", "off")
                .click(function(e) {
                    if (wrapper.hasClass("k-state-disabled")) {
                        e.preventDefault();
                    }
                })
                .change($.proxy(that._onInputChange, that));
        },

        _onInputChange: function(e) {
            var input = $(e.target),
                prevented = this.trigger(SELECT, { files: inputFiles(input) });

            if (!prevented) {
                input.trigger("t:select");
            }
        },

        _onDrop: function (e) {
            var dt = e.originalEvent.dataTransfer,
                that = this,
                droppedFiles = dt.files;

            stopEvent(e);

            if (droppedFiles.length > 0) {
                var prevented = that.trigger(SELECT, { files: droppedFiles });
                if (!prevented) {
                    $(".k-dropzone", that.wrapper).trigger("t:select", [ droppedFiles ]);
                }
            }
        },

        _enqueueFile: function(name, data) {
            var that = this,
                existingFileEntries,
                fileEntry,
                fileList =  $(".k-upload-files", that.wrapper);

            if (fileList.length == 0) {
                fileList = $("<ul class='k-upload-files k-reset'></ul>").appendTo(that.wrapper);
                if (!that.options.showFileList) {
                    fileList.hide();
                }
            }

            existingFileEntries = $(".k-file", fileList);
            fileEntry =
                $("<li class='k-file'><span class='k-icon'></span><span class='k-filename' title='" + name + "'>" + name + "</span></li>")
                .appendTo(fileList)
                .data(data);

            if (!that.multiple) {
                existingFileEntries.trigger("t:remove");
            }

            return fileEntry;
        },

        _removeFileEntry: function(fileEntry) {
            var fileList = fileEntry.closest(".k-upload-files");
            if ($(".k-file", fileList).length == 1) {
                fileList.remove();
                this._hideUploadButton();
            } else {
                fileEntry.remove();
            }
        },

        _fileAction: function(fileElement, actionKey) {
            var classDictionary = { remove: "k-delete", cancel: "k-cancel", retry: "k-retry" };
            if (!classDictionary.hasOwnProperty(actionKey)) {
                return;
            }

            this._clearFileAction(fileElement);

            fileElement.append(
                this._renderAction(classDictionary[actionKey], this.localization[actionKey])
                .addClass("k-upload-action")
            );
        },

        _fileState: function(fileEntry, stateKey) {
            var localization = this.localization,
                states = {
                    uploading: {
                        cssClass: "k-loading",
                        text : localization.statusUploading
                    },
                    uploaded: {
                        cssClass: "k-success",
                        text : localization.statusUploaded
                    },
                    failed: {
                        cssClass: "k-fail",
                        text : localization.statusFailed
                    }
                },
                currentState = states[stateKey];

            if (currentState) {
                var icon = fileEntry.children(".k-icon").text(currentState.text);
                icon[0].className = "k-icon " + currentState.cssClass;
            }
        },

        _renderAction: function (actionClass, actionText) {
            if (actionClass != "") {
                return $(
                "<button type='button' class='k-button k-button-icontext'>" +
                    "<span class='k-icon " + actionClass + "'></span>" +
                    actionText +
                "</button>"
                )
            }
            else {
                return $(
                "<button type='button' class='k-button'>" +
                    actionText +
                "</button>"
                )
            }
        },

        _clearFileAction: function(fileElement) {
            fileElement
                .find(".k-upload-action").remove();
        },

        _onFileAction: function(e) {
            var that = this;

            if (!that.wrapper.hasClass("k-state-disabled")) {
                var button = $(e.target).closest(".k-upload-action"),
                    icon = button.find(".k-icon"),
                    fileEntry = button.closest(".k-file"),
                    eventArgs = { files: fileEntry.data("fileNames") };

                if (icon.hasClass("k-delete")) {
                    if (!that.trigger(REMOVE, eventArgs)) {
                        fileEntry.trigger("t:remove", eventArgs.data);
                    }
                } else if (icon.hasClass("k-cancel")) {
                    that.trigger(CANCEL, eventArgs);
                    fileEntry.trigger("t:cancel");
                } else if (icon.hasClass("k-retry")) {
                    fileEntry.trigger("t:retry");
                }
            }

            return false;
        },

        _onUploadSelected: function() {
            this.wrapper.trigger("t:saveSelected");
            return false;
        },

        _onFileProgress: function(e, percentComplete) {
            var progressBar = $(".k-progress-status", e.target);
            if (progressBar.length == 0) {
                progressBar =
                    $("<span class='k-progress'><span class='k-progress-status' style='width: 0;'></span></span>")
                        .appendTo($(".k-filename", e.target))
                        .find(".k-progress-status");
            }

            progressBar.width(percentComplete + "%");
        },

        _onUploadSuccess: function(e, response, xhr) {
            var fileEntry = getFileEntry(e);

            this._fileState(fileEntry, "uploaded");

            this.trigger(SUCCESS, {
                files: fileEntry.data("fileNames"),
                response: response,
                operation: "upload",
                XMLHttpRequest: xhr
            });

            if (this._supportsRemove()) {
                this._fileAction(fileEntry, REMOVE);
            } else {
                this._clearFileAction(fileEntry);
            }

            this._checkAllComplete();
        },

        _onUploadError: function(e, xhr) {
            var fileEntry = getFileEntry(e);

            this._fileState(fileEntry, "failed");
            this._fileAction(fileEntry, "retry");

            var prevented = this.trigger(ERROR, {
                operation: "upload",
                files: fileEntry.data("fileNames"),
                XMLHttpRequest: xhr
            });

            logToConsole("Server response: " + xhr.responseText);

            if (!prevented) {
                this._alert("Error! Upload failed. Unexpected server response - see console.");
            }

            this._checkAllComplete();
        },

        _showUploadButton: function() {
            var uploadButton = $(".k-upload-selected", this.wrapper);
            if (uploadButton.length == 0) {
                uploadButton =
                    this._renderAction("", this.localization["uploadSelectedFiles"])
                    .addClass("k-upload-selected");
            }

            this.wrapper.append(uploadButton);
        },

        _hideUploadButton: function() {
            $(".k-upload-selected", this.wrapper).remove();
        },

        _onParentFormSubmit: function() {
            var upload = this,
                element = upload.element;
            element.trigger("t:abort");

            if (!element.value) {
                var input = $(element);

                // Prevent submitting an empty input
                input.attr("disabled", "disabled");

                window.setTimeout(function() {
                    // Restore the input so the Upload remains functional
                    // in case the user cancels the form submit
                    input.removeAttr("disabled");
                }, 0);
            }
        },

        _onParentFormReset: function() {
            $(".k-file", this.wrapper).trigger("t:remove");
        },

        _supportsFormData: function() {
            return typeof(FormData) != "undefined";
        },

        _supportsMultiple: function() {
            return !$.browser.opera;
        },

        _supportsDrop: function() {
            var userAgent = this._userAgent().toLowerCase(),
                isChrome = /chrome/.test(userAgent),
                isSafari = !isChrome && /safari/.test(userAgent),
                isWindowsSafari = isSafari && /windows/.test(userAgent);

            return !isWindowsSafari && this._supportsFormData() && (this.options.async.saveUrl != undefined);
        },

        _userAgent: function() {
            return navigator.userAgent;
        },

        _setupDropZone: function() {
            $(".k-upload-button", this.wrapper)
                .wrap("<div class='k-dropzone'></div>");

            var dropZone = $(".k-dropzone", this.wrapper)
                .append($("<em>" + this.localization["dropFilesHere"] + "</em>"))
                .bind({
                    "dragenter": stopEvent,
                    "dragover": function(e) { e.preventDefault(); },
                    "drop" : $.proxy(this._onDrop, this)
                });

            bindDragEventWrappers(dropZone,
                function() { dropZone.addClass("k-dropzone-hovered"); },
                function() { dropZone.removeClass("k-dropzone-hovered"); });

            bindDragEventWrappers($(document),
                function() { dropZone.addClass("k-dropzone-active"); },
                function() { dropZone.removeClass("k-dropzone-active"); });
        },

        _supportsRemove: function() {
            return this.options.async.removeUrl != undefined;
        },

        _submitRemove: function(fileNames, data, onSuccess, onError) {
            var upload = this,
                removeField = upload.options.async.removeField || "fileNames",
                params = $.extend(data, getAntiForgeryTokens());

            params[removeField] = fileNames;

            $.ajax({
                  type: this.options.async.removeVerb,
                  dataType: "json",
                  url: this.options.async.removeUrl,
                  traditional: true,
                  data: params,
                  success: onSuccess,
                  error: onError
            });
        },

        _alert: function(message) {
            alert(message);
        },

        _wrapInput: function(input) {
            input.wrap("<div class='k-widget k-upload'><div class='k-button k-upload-button'></div></div>");
            input.closest(".k-button")
                .append("<span>" + this.localization.select + "</span>");

            return input.closest(".k-upload");
        },

        _checkAllComplete: function() {
            if ($(".k-file .k-icon.k-loading", this.wrapper).length == 0) {
                this.trigger(COMPLETE);
            }
        }
    });

    // Synchronous upload module
    var syncUploadModule = function(upload) {
        this.name = "syncUploadModule";
        this.element = upload.wrapper;
        this.upload = upload;
        this.element
            .bind("t:select", $.proxy(this.onSelect, this))
            .bind("t:remove", $.proxy(this.onRemove, this))
            .closest("form")
                .attr("enctype", "multipart/form-data")
                .attr("encoding", "multipart/form-data");
    };

    syncUploadModule.prototype = /** @ignore */  {
        onSelect: function(e) {
            var upload = this.upload;
            var sourceInput = $(e.target);
            upload._addInput(sourceInput.clone().val(""));
            var file = upload._enqueueFile(getFileName(sourceInput), { relatedInput : sourceInput });
            upload._fileAction(file, REMOVE);
        },

        onRemove: function(e) {
            var fileEntry = getFileEntry(e);
            fileEntry.data("relatedInput").remove();

            this.upload._removeFileEntry(fileEntry);
        }
    };

    // Iframe upload module
    var iframeUploadModule = function(upload) {
        this.name = "iframeUploadModule";
        this.element = upload.wrapper;
        this.upload = upload;
        this.iframes = [];
        this.element
            .bind("t:select", $.proxy(this.onSelect, this))
            .bind("t:cancel", $.proxy(this.onCancel, this))
            .bind("t:retry", $.proxy(this.onRetry, this))
            .bind("t:remove", $.proxy(this.onRemove, this))
            .bind("t:saveSelected", $.proxy(this.onSaveSelected, this))
            .bind("t:abort", $.proxy(this.onAbort, this));
    };

    Upload._frameId = 0;

    iframeUploadModule.prototype = /** @ignore */ {
        onSelect: function(e) {
            var upload = this.upload,
                sourceInput = $(e.target);

            var fileEntry = this.prepareUpload(sourceInput);

            if (upload.options.async.autoUpload) {
                this.performUpload(fileEntry);
            } else {
                if (upload._supportsRemove()) {
                    this.upload._fileAction(fileEntry, REMOVE);
                }

                upload._showUploadButton();
            }
        },

        prepareUpload: function(sourceInput) {
            var upload = this.upload;
            var activeInput = $(upload.element);
            var name = upload.options.async.saveField || sourceInput.attr("name");
            upload._addInput(sourceInput.clone().val(""));

            sourceInput.attr("name", name);

            var iframe = this.createFrame(upload.name + "_" + Upload._frameId++);
            this.registerFrame(iframe);

            var form = this.createForm(upload.options.async.saveUrl, iframe.attr("name"))
                .append(activeInput);

            var fileEntry = upload._enqueueFile(
                getFileName(sourceInput),
                { "frame": iframe, "relatedInput": activeInput, "fileNames": inputFiles(sourceInput) });

            iframe
                .data({ "form": form, "file": fileEntry });

            return fileEntry;
        },

        performUpload: function(fileEntry) {
            var e = { files: fileEntry.data("fileNames") },
                iframe = fileEntry.data("frame"),
                upload = this.upload;

            if (!upload.trigger(UPLOAD, e)) {
                upload._hideUploadButton();

                iframe.appendTo(document.body);

                var form = iframe.data("form")
                    .appendTo(document.body);

                e.data = $.extend({ }, e.data, getAntiForgeryTokens());
                for (var key in e.data) {
                    var dataInput = form.find("input[name='" + key + "']");
                    if (dataInput.length == 0) {
                        dataInput = $("<input>", { type: "hidden", name: key })
                            .appendTo(form);
                    }
                    dataInput.val(e.data[key]);
                }

                upload._fileAction(fileEntry, CANCEL);
                upload._fileState(fileEntry, "uploading");

                iframe
                    .one("load", $.proxy(this.onIframeLoad, this));

                form[0].submit();
            } else {
                upload._removeFileEntry(iframe.data("file"));
                this.cleanupFrame(iframe);
                this.unregisterFrame(iframe);
            }
        },

        onSaveSelected: function(e) {
            var module = this;

            $(".k-file", this.element).each(function() {
                var fileEntry = $(this),
                    started = isFileUploadStarted(fileEntry);

                if (!started) {
                    module.performUpload(fileEntry);
                }
            });
        },

        onIframeLoad: function(e) {
            var iframe = $(e.target);

            try {
                var responseText = iframe.contents().text();
            } catch (e) {
                responseText = "Error trying to get server response: " + e;
            }

            this.processResponse(iframe, responseText);
        },

        processResponse: function(iframe, responseText) {
            var fileEntry = iframe.data("file"),
                module = this,
                fakeXHR = {
                    responseText: responseText
                };

            tryParseJSON(responseText,
                function(jsonResult) {
                    $.extend(fakeXHR, { statusText: "OK", status: "200" });
                    fileEntry.trigger("t:upload-success", [ jsonResult, fakeXHR ]);
                    module.cleanupFrame(iframe);
                    module.unregisterFrame(iframe);
                },
                function() {
                    $.extend(fakeXHR, { statusText: "error", status: "500" });
                    fileEntry.trigger("t:upload-error", [ fakeXHR ]);
                }
            );
        },

        onCancel: function(e) {
            var iframe = $(e.target).data("frame");

            this.stopFrameSubmit(iframe);
            this.cleanupFrame(iframe);
            this.unregisterFrame(iframe);
            this.upload._removeFileEntry(iframe.data("file"));
        },

        onRetry: function(e) {
            var fileEntry = getFileEntry(e);
            this.performUpload(fileEntry);
        },

        onRemove: function(e, data) {
            var fileEntry = getFileEntry(e);

            var iframe = fileEntry.data("frame");
            if (iframe)
            {
                this.unregisterFrame(iframe);
                this.upload._removeFileEntry(fileEntry);
                this.cleanupFrame(iframe);
            } else {
                removeUploadedFile(fileEntry, this.upload, data);
            }
        },

        onAbort: function() {
            var element = this.element,
                module = this;

            $.each(this.iframes, function() {
                $("input", this.data("form")).appendTo(element);
                module.stopFrameSubmit(this[0]);
                this.data("form").remove();
                this.remove();
            });

            this.iframes = [];
        },

        createFrame: function(id) {
            return $(
                "<iframe" +
                " name='" + id + "'" +
                " id='" + id + "'" +
                " style='display:none;' />"
            );
        },

        createForm: function(action, target) {
            return $(
                "<form enctype='multipart/form-data' method='POST'" +
                " action='" + action + "'" +
                " target='" + target + "'" +
                "/>");
        },

        stopFrameSubmit: function(frame) {
            if (typeof(frame.stop) != "undefined") {
                frame.stop();
            } else if (frame.document) {
                frame.document.execCommand("Stop");
                frame.contentWindow.location.href = frame.contentWindow.location.href;
            }
        },

        registerFrame: function(frame) {
            this.iframes.push(frame);
        },

        unregisterFrame: function(frame) {
            this.iframes = $.grep(this.iframes, function(value) {
                return value.attr("name") != frame.attr("name");
            });
        },

        cleanupFrame: function(frame) {
            var form = frame.data("form");

            frame.data("file").data("frame", null);

            setTimeout(function () {
                form.remove();
                frame.remove();
            }, 1);
        }
    };

    // FormData upload module
    var formDataUploadModule = function(upload) {
        this.name = "formDataUploadModule";
        this.element = upload.wrapper;
        this.upload = upload;
        this.element
            .bind("t:select", $.proxy(this.onSelect, this))
            .bind("t:cancel", $.proxy(this.onCancel, this))
            .bind("t:remove", $.proxy(this.onRemove, this))
            .bind("t:retry", $.proxy(this.onRetry, this))
            .bind("t:saveSelected", $.proxy(this.onSaveSelected, this))
            .bind("t:abort", $.proxy(this.onAbort, this));
    };

    formDataUploadModule.prototype = /** @ignore */ {
        onSelect: function(e, rawFiles) {
            var upload = this.upload,
                module = this,
                sourceElement = $(e.target),
                files = rawFiles ? getAllFileInfo(rawFiles) : this.inputFiles(sourceElement),
                fileEntries = this.prepareUpload(sourceElement, files);

            $.each(fileEntries, function() {
                if (upload.options.async.autoUpload) {
                    module.performUpload(this);
                } else {
                    if (upload._supportsRemove()) {
                        upload._fileAction(this, REMOVE);
                    }
                    upload._showUploadButton();
                }
            });
        },

        prepareUpload: function(sourceElement, files) {
            var fileEntries = this.enqueueFiles(files);

            if (sourceElement.is("input")) {
                $.each(fileEntries, function() {
                    $(this).data("relatedInput", sourceElement);
                });
                sourceElement.data("relatedFileEntries", fileEntries);
                this.upload._addInput(sourceElement.clone().val(""));
            }

            return fileEntries;
        },

        enqueueFiles: function(arrFileInfo) {
            var upload = this.upload
                fileEntries = [];

            for (var i = 0; i < arrFileInfo.length; i++) {
                var currentFile = arrFileInfo[i],
                    name = currentFile.name;

                var fileEntry = upload._enqueueFile(name, { "fileNames": [ currentFile ] });
                fileEntry.data("formData", this.createFormData(arrFileInfo[i]));

                fileEntries.push(fileEntry);
            }

            return fileEntries;
        },

        inputFiles: function(sourceInput) {
            return inputFiles(sourceInput);
        },

        performUpload: function(fileEntry) {
            var upload = this.upload,
                formData = fileEntry.data("formData"),
                e = { files: fileEntry.data("fileNames") };

            if (!upload.trigger(UPLOAD, e)) {
                upload._fileAction(fileEntry, CANCEL);
                upload._hideUploadButton();

                e.data = $.extend({ }, e.data, getAntiForgeryTokens());
                for (var key in e.data) {
                    formData.append(key, e.data[key]);
                }

                upload._fileState(fileEntry, "uploading");

                this.postFormData(this.upload.options.async.saveUrl, formData, fileEntry);
            } else {
                this.removeFileEntry(fileEntry);
            }
        },

        onSaveSelected: function(e) {
            var module = this;

            $(".k-file", this.element).each(function() {
                var fileEntry = $(this),
                    started = isFileUploadStarted(fileEntry);

                if (!started) {
                    module.performUpload(fileEntry);
                }
            });
        },

        onCancel: function(e) {
            var fileEntry = getFileEntry(e);
            this.stopUploadRequest(fileEntry);
            this.removeFileEntry(fileEntry);
        },

        onRetry: function(e) {
            var fileEntry = getFileEntry(e);
            this.performUpload(fileEntry);
        },

        onRemove: function(e, data) {
            var fileEntry = getFileEntry(e);

            if (fileEntry.children(".k-icon").is(".k-success")) {
                removeUploadedFile(fileEntry, this.upload, data);
            } else {
                this.removeFileEntry(fileEntry);
            }
        },

        postFormData: function(url, data, fileEntry) {
            var xhr = new XMLHttpRequest(),
                module = this;

            fileEntry.data("request", xhr);

            xhr.addEventListener("load", function(e) {
                module.onRequestSuccess.call(module, e, fileEntry);
            }, false);

            xhr.addEventListener(ERROR, function(e) {
                module.onRequestError.call(module, e, fileEntry);
            }, false);

            xhr.upload.addEventListener("progress", function(e) {
                module.onRequestProgress.call(module, e, fileEntry);
            }, false);

            xhr.open("POST", url);
            xhr.send(data);
        },

        createFormData: function(fileInfo) {
            var formData = new FormData(),
            upload = this.upload;

            formData.append(upload.options.async.saveField || upload.name, fileInfo.rawFile);

            return formData;
        },

        onRequestSuccess: function(e, fileEntry) {
            var xhr = e.target,
                module = this;
            tryParseJSON(xhr.responseText,
                function(jsonResult) {
                    fileEntry.trigger("t:upload-success", [ jsonResult, xhr ]);
                    fileEntry.trigger("t:progress", [ 100 ]);
                    module.cleanupFileEntry(fileEntry);
                },
                function() {
                    fileEntry.trigger("t:upload-error", [ xhr ]);
                }
            );
        },

        onRequestError: function(e, fileEntry) {
            var xhr = e.target;
            fileEntry.trigger("t:upload-error", [ xhr ]);
        },

        cleanupFileEntry: function(fileEntry) {
            var relatedInput = fileEntry.data("relatedInput"),
                uploadComplete = true;

            if (relatedInput) {
                $.each(relatedInput.data("relatedFileEntries"), function() {
                    // Exclude removed file entries and self
                    if (this.parent().length > 0 && this[0] != fileEntry[0]) {
                        uploadComplete = uploadComplete && this.children(".k-icon").is(".k-success");
                    }
                });

                if (uploadComplete) {
                    relatedInput.remove();
                }
            }

            fileEntry.data("formData", null);
        },

        removeFileEntry: function(fileEntry) {
            this.cleanupFileEntry(fileEntry);
            this.upload._removeFileEntry(fileEntry);
        },

        onRequestProgress: function(e, fileEntry) {
            var percentComplete = Math.round(e.loaded * 100 / e.total);
            fileEntry.trigger("t:progress", [ percentComplete ]);
        },

        stopUploadRequest: function(fileEntry) {
            fileEntry.data("request").abort();
        }
    };

    // Helper functions
    function getFileName(input) {
        return $.map(inputFiles(input), function (file) {
            return file.name;
        }).join(", ");
    }

    function inputFiles($input) {
        var input = $input[0];
        if (input.files) {
            return getAllFileInfo(input.files);
        } else {
            return [{
                name: stripPath(input.value),
                extension: getFileExtension(input.value),
                size: null
            }];
        }
    }

    function getAllFileInfo(rawFiles) {
        return $.map(rawFiles, function (file) {
            return getFileInfo(file);
        });
    }

    function getFileInfo(rawFile) {
        // Older Firefox versions (before 3.6) use fileName and fileSize
        var fileName = rawFile.name || rawFile.fileName;
        return {
            name: fileName,
            extension: getFileExtension(fileName),
            size: rawFile.size || rawFile.fileSize,
            rawFile: rawFile
        };
    }

    function getFileExtension(fileName) {
        var matches = fileName.match(rFileExtension);
        return matches ? matches[0] : "";
    }

    function stripPath(name) {
        var slashIndex = name.lastIndexOf("\\");
        return (slashIndex != -1) ? name.substr(slashIndex + 1) : name;
    }

    function removeUploadedFile(fileEntry, upload, data) {
        if (!upload._supportsRemove()) {
            return;
        }

        var files = fileEntry.data("fileNames");
        var fileNames = $.map(files, function(file) { return file.name });

        upload._submitRemove(fileNames, data,
            function onSuccess(data, textStatus, xhr) {
                upload._removeFileEntry(fileEntry);

                upload.trigger(SUCCESS, {
                    operation: "remove",
                    files: files,
                    response: data,
                    XMLHttpRequest: xhr });
            },

            function onError(xhr, textStatus, textStatus) {
                var prevented = upload.trigger(ERROR, {
                    operation: "remove",
                    files: files,
                    XMLHttpRequest: xhr });

                logToConsole("Server response: " + xhr.responseText);

                if (!prevented) {
                    upload._alert("Error! Remove operation failed. Unexpected response - see console.");
                }
            }
        );
    }

    function tryParseJSON(input, onSuccess, onError) {
        try {
            var json = $.parseJSON(input);
            onSuccess(json);
        } catch (e) {
            onError();
        }
    }

    function stopEvent(e) {
        e.stopPropagation(); e.preventDefault();
    }

    function bindDragEventWrappers(element, onDragEnter, onDragLeave) {
        var hideInterval, lastDrag;

        element
            .bind("dragenter", function(e) {
                onDragEnter();
                lastDrag = new Date();

                if (!hideInterval) {
                    hideInterval = setInterval(function() {
                        var sinceLastDrag = new Date() - lastDrag;
                        if (sinceLastDrag > 100) {
                            onDragLeave();

                            clearInterval(hideInterval);
                            hideInterval = null;
                        }
                    }, 100);
                }
            })
            .bind("dragover", function(e) {
                lastDrag = new Date();
            });
    }

    function isFileUploadStarted(fileEntry) {
        return fileEntry.children(".k-icon").is(".k-loading, .k-success, .k-fail");
    }

    function logToConsole(message) {
        if (typeof(console) != "undefined" && console.log) {
            console.log(message);
        }
    }

    function getFileEntry(e) {
        return $(e.target).closest(".k-file");
    }

    function getAntiForgeryTokens() {
        var tokens = { };
        $("input[name^='__RequestVerificationToken']").each(function() {
            tokens[this.name] = this.value;
        });

        return tokens;
    }
    kendo.ui.plugin(Upload);
})(jQuery);
(function ($, undefined) {
    /**
     * @name kendo.ui.Window.Description
     *
     * @section
     *  <p>
     *      The Window widget displays content in a modal or non-modal HTML window. By default, Windows can be moved,
     *      resized, and closed by users. Window content can also be defined with either static HTML or loaded dynamically with Ajax.
     *  </p>
     *  <p>
     *      A Window can be initialized from virtually any HTML element. During initialization, the targeted content will
     *      automatically be wrapped in the Window’s HTML div element.
     *  </p>
     *  <h3>Getting Started</h3>
     * @exampleTitle Create a simple HTML element with the Window content
     * @example
     *  <p id="window">
     *      Kendo window content
     *  </p>
     * @exampleTitle Initialize Window using a jQuery selector
     * @example
     * $("#window").kendoWindow();
     * @section
     *  <p>
     *      When a Window is initialized, it will automatically be displayed open near the
     *      location of the HTML element that was used to initialize the content.
     *  </p>
     *  <h3>Configuring Window behaviors</h3>
     *  <p>
     *      Window provides many configuration options that can be easily set during initialization.
     *      Among the properties that can be controlled:
     *  </p>
     *  <ul>
     *      <li>Minimum height/width</li>
     *      <li>Available user Window actions (close/refresh/maximize)</li>
     *      <li>Window title</li>
     *      <li>Draggable and Resizable behaviors</li>
     *  </ul>
     * @exampleTitle Create modal Window with all user actions enabled
     * @example
     *  $("#window").kendoWindow({
     *      draggable: false,
     *      resizable: false,
     *      width: "500px",
     *      height: "300px",
     *      title: "Modal Window",
     *      modal: true,
     *      actions: ["Refresh", "Maximize", "Close"]
     *  });
     * @section
     *  <p>
     *      The order of the values in the actions array determines the order in which the action buttons
     *      will be rendered in the Window title bar. The maximize action serves both as a button for expanding
     *      the Window to fill the screen and as a button to restore the Window to the previous size.
     *  </p>
     *  <h3>Positioning and Opening the Window</h3>
     *  <p>
     *      In some scenarios, it is preferable to center a Window rather than open it near the HTML element
     *      used to define the content. It’s also common to open a Window as the result of an action rather
     *      than on initial page load. The Window API provides methods for handling this and many more advanced
     *      Window scenarios. Please see the Window demo Methods tab for more details.
     *  </p>
     * @exampleTitle Centering a Window and opening on button click
     * @example
     *  <!-- Create Window HTML and a button to open Window -->
     *  <p id="window">
     *      Centered Kendo UI Window content
     *  </p>
     *  <button id="btnOpen">Open Window</button>
     * @exampleTitle
     * @example
     *  // Initialize Window, center, and configure button click action
     *  $(document).ready(function(){
     *      var window = $("#window").kendoWindow({
     *              title: "Centered Window",
     *              width: "200px",
     *              height: "200px",
     *              visible: false
     *          }).data("kendoWindow");
     *  });
     *
     *  $("#btnOpen").click(function(){
     *      var window = $("#window").data("kendoWindow");
     *      window.center();
     *      window.open();
     *  });
     * @section
     *  <h3>Loading Window content with Ajax</h3>
     *  <p>
     *      While any valid technique for loading Ajax content can be used, Window provides
     *      built-in support for asynchronously loading content from a URL. This URL should
     *      return a HTML fragment that can be loaded in a Window content area.
     *  </p>
     * @exampleTitle Load Window content asynchronously
     * @example
     *  <!-- Define a basic HTML element for the Window -->
     *  <div id="window"></div>
     * @exampleTitle
     * @example
     *  // Initialize window and configure content loading
     *  $(document).ready(function(){
     *      $("#window").kendoWindow({
     *        title: "Async Window Content",
     *        content: "html-content-snippet.html"
     *      });
     *  });
     */
    var kendo = window.kendo,
        Widget = kendo.ui.Widget,
        Draggable = kendo.ui.Draggable,
        fx = kendo.fx,
        isPlainObject = $.isPlainObject,
        proxy = $.proxy,
        each = $.each,
        template = kendo.template,
        body,
        templates,
        // classNames
        KWINDOW = ".k-window",
        KWINDOWTITLEBAR = ".k-window-titlebar",
        KWINDOWCONTENT = ".k-window-content",
        KOVERLAY = ".k-overlay",
        LOADING = "k-loading",
        KHOVERSTATE = "k-state-hover",
        // constants
        VISIBLE = ":visible",
        CURSOR = "cursor",
        // events
        OPEN = "open",
        ACTIVATE = "activate",
        DEACTIVATE = "deactivate",
        CLOSE = "close",
        REFRESH = "refresh",
        RESIZE = "resize",
        DRAGEND = "dragend",
        ERROR = "error",
        OVERFLOW = "overflow",
        isLocalUrl = kendo.isLocalUrl;

    function windowObject(element) {
        return element.children(KWINDOWCONTENT).data("kendoWindow");
    }

    function openedModalWindows() {
        return $(KWINDOW).filter(function() {
            var wnd = $(this);
            return wnd.is(VISIBLE) && windowObject(wnd).options.modal;
        });
    }


    var Window = Widget.extend(/** @lends kendo.ui.Window.prototype */ {
        /**
         * @constructs
         * @extends kendo.ui.Widget
         * @param {DomElement} element DOM element
         * @param {Object} options Configuration options.
         * @option {Boolean} [modal] <false> Specifies whether the window should block interaction with other page elements.
         * @option {Boolean} [visible] <true> Specifies whether the window will be initially visible.
         * @option {Boolean} [draggable] <true> Specifies whether the users may move the window.
         * @option {Boolean} [resizable] <true> Specifies whether the users may to resize the window.
         * @option {Integer} [minWidth] <50> The minimum width that may be achieved by resizing the window.
         * @option {Integer} [minHeight] <50> The minimum height that may be achieved by resizing the window.
         * @option {Object|String} [content] Specifies a URL or request options that the window should load its content from. For remote URLs, a container iframe element is automatically created.
         * @option {Array<String>} [actions] <"Close"> The buttons for interacting with the window. Predefined array values are "Close", "Refresh", "Minimize", "Maximize".
         * @option {String} [title] The text in the window title bar.
         * @option {Object} [animation] A collection of {Animation} objects, used to change default animations. A value of false will disable all animations in the widget.
         * @option {Animation} [animation.open] The animation that will be used when the window opens.
         * @option {Animation} [animation.close] The animation that will be used when the window closes.
         */
        init: function(element, options) {
            var that = this,
                wrapper,
                windowActions = ".k-window-titlebar .k-window-action",
                titleBar, offset,
                isVisible = false;

            body = document.body;

            Widget.fn.init.call(that, element, options);
            options = that.options;
            element = that.element;

            if (options.animation === false) {
                options.animation = { open: { show: true, effects: {} }, close: { hide:true, effects: {} } };
            }

            if (!element.parent().is("body")) {
                if (element.is(VISIBLE)) {
                    offset = element.offset();
                    isVisible = true;
                } else {
                    var visibility = element.css("visibility"),
                        display = element.css("display");

                    element.css({ visibility: "hidden", display: "" });
                    offset = element.offset();

                    element.css({ visibility: visibility, display: display });
                }
            }

            wrapper = that.wrapper = element.closest(KWINDOW);

            if (!element.is(".k-content") || !wrapper[0]) {
                element.addClass("k-window-content k-content");
                createWindow(element, options);
                wrapper = that.wrapper = element.closest(KWINDOW);

                titleBar = that.wrapper.find(KWINDOWTITLEBAR);
                titleBar.css("margin-top", -titleBar.outerHeight());

                wrapper.css("padding-top", titleBar.outerHeight());

                if (options.width) {
                    wrapper.width(options.width);
                }

                if (options.height) {
                    wrapper.height(options.height);
                }

                $.each(["minWidth","minHeight","maxWidth","maxHeight"], function(_, prop) {
                    var value = options[prop];
                    if (value && value != Infinity) {
                        wrapper.css(prop, value);
                    }
                });

                if (!options.visible) {
                    wrapper.hide();
                }
            }

            if (offset) {
                if (isVisible) {
                    wrapper.css({ top: offset.top, left: offset.left });
                } else {
                   wrapper
                    .css({
                        top: offset.top,
                        left: offset.left,
                        visibility: "visible",
                        display: "none"
                    });
                }
            }

            wrapper.toggleClass("k-rtl", that.wrapper.closest(".k-rtl").length)
                   .appendTo(body);

            that.toFront();

            if (options.modal) {
                that._overlay(wrapper.is(VISIBLE)).css({ opacity: 0.5 });
            }

            wrapper
                .bind("mousedown", proxy(that.toFront, that))
                .delegate(windowActions, "mouseenter", function () { $(this).addClass(KHOVERSTATE); })
                .delegate(windowActions, "mouseleave", function () { $(this).removeClass(KHOVERSTATE); })
                .delegate(windowActions, "click", proxy(that._windowActionHandler, that));

            if (options.resizable) {
                wrapper.delegate(KWINDOWTITLEBAR, "dblclick", proxy(that.toggleMaximization, that));

                each("n e s w se sw ne nw".split(" "), function(index, handler) {
                    wrapper.append(templates.resizeHandle(handler));
                });

                that.resizing = new WindowResizing(that);
            }

            if (options.draggable) {
                that.dragging = new WindowDragging(that);
            }

            that.bind([
                /**
                 * Fires when the window is opened (i.e. the open() method is called).
                 * @name kendo.ui.Window#open
                 * @event
                 * @param {Event} e
                 * @cancellable
                 */
                OPEN,
                /**
                 * Fires when the window has finished its opening animation
                 * @name kendo.ui.Window#activate
                 * @event
                 * @param {Event} e
                 */
                ACTIVATE,
                /**
                 * Fires when the window has finished its closing animation
                 * @name kendo.ui.Window#deactivate
                 * @event
                 * @param {Event} e
                 */
                DEACTIVATE,
                /**
                 * Fires when the window is being closed (by the user or through the close() method)
                 * @name kendo.ui.Window#close
                 * @event
                 * @param {Event} e
                 * @cancellable
                 */
                CLOSE,
                /**
                 * Fires when the window contents have been refreshed through AJAX.
                 * @name kendo.ui.Window#refresh
                 * @event
                 * @param {Event} e
                 */
                REFRESH,
                /**
                 * Fires when the window has been resized by the user.
                 * @name kendo.ui.Window#resize
                 * @event
                 * @param {Event} e
                 */
                RESIZE,
                /**
                 * Fires when the window has been moved by the user.
                 * @name kendo.ui.Window#dragend
                 * @event
                 * @param {Event} e
                 */
                DRAGEND,
                /**
                 * Fires when an AJAX request for content fails.
                 * @name kendo.ui.Window#error
                 * @event
                 * @param {Event} e
                 */
                ERROR
            ], options);

            $(window).resize(proxy(that._onDocumentResize, that));

            if (!$.isPlainObject(options.content)) {
                options.content = { url: options.content };
            }

            if (isLocalUrl(options.content.url)) {
                that._ajaxRequest(options.content);
            }

            if (wrapper.is(VISIBLE)) {
                that.trigger(OPEN);
                that.trigger(ACTIVATE);
            }
        },

        options: {
            name: "Window",
            animation: {
                open: {
                    effects: { zoomIn: {}, fadeIn: {} },
                    duration: 350,
                    show: true
                },
                close: {
                    effects: { zoomOut: { properties: { scale: 0.7 } }, fadeOut: {} },
                    duration: 350,
                    hide: true
                }
            },
            title: "",
            actions: ["Close"],
            modal: false,
            resizable: true,
            draggable: true,
            minWidth: 50,
            minHeight: 50,
            maxWidth: Infinity,
            maxHeight: Infinity,
            visible: true
        },

        _overlay: function (visible) {
            var overlay = $("body > .k-overlay"),
                doc = $(document),
                wrapper = this.wrapper[0];

            if (overlay.length == 0) {
                overlay = $("<div class='k-overlay' />")
                    .toggle(visible)
                    .insertBefore(wrapper);
            } else {
                overlay.insertBefore(wrapper).toggle(visible);
            }

            return overlay;
        },

        _windowActionHandler: function (e) {
            var target = $(e.target).closest(".k-window-action").find(".k-icon"),
                that = this;

            each({
                "k-close": that.close,
                "k-maximize": that.maximize,
                "k-restore": that.restore,
                "k-refresh": that.refresh
            }, function (commandName, handler) {
                if (target.hasClass(commandName)) {
                    e.preventDefault();
                    handler.call(that);
                    return false;
                }
            });
        },

        /**
         * Centers the window within the viewport.
         * @example
         * var wnd = $("#window").data("kendoWindow");
         *
         * wnd.center();
         */
        center: function () {
            var wrapper = this.wrapper,
                documentWindow = $(window);

            wrapper.css({
                left: documentWindow.scrollLeft() + Math.max(0, (documentWindow.width() - wrapper.width()) / 2),
                top: documentWindow.scrollTop() + Math.max(0, (documentWindow.height() - wrapper.height()) / 2)
            });

            return this;
        },

        /**
         * Sets/gets the window title.
         * @param {String} title The new window title
         * @example
         * var wnd = $("#window").data("kendoWindow");
         *
         * // get the title
         * var title = wnd.title();
         *
         * // set the title
         * wnd.title("New title");
         */
        title: function (text) {
            var title = $(".k-window-titlebar > .k-window-title", this.wrapper);

            if (!text) {
                return title.text();
            }

            title.text(text);
            return this;
        },

        /**
         * Sets/gets the window content.
         * @param {String} content The new window content
         * @example
         * var wnd = $("#window").data("kendoWindow");
         *
         * // get the content
         * var content = wnd.content();
         *
         * // set the content
         * wnd.content("&lt;p&gt;New content&lt;/p&gt;");
         */
        content: function (html) {
            var content = this.wrapper.children(KWINDOWCONTENT);

            if (!html) {
                return content.html();
            }

            content.html(html);
            return this;
        },

        /**
         * Opens the window
         * @example
         * var wnd = $("#window").data("kendoWindow");
         *
         * wnd.open();
         */
        open: function () {
            var that = this,
                wrapper = that.wrapper,
                showOptions = that.options.animation.open,
                contentElement = wrapper.children(KWINDOWCONTENT),
                initialOverflow = contentElement.css(OVERFLOW);

            if (!that.trigger(OPEN)) {
                if (that.options.modal) {
                    var overlay = that._overlay(false);

                    if (showOptions.duration) {
                        overlay.kendoStop().kendoAnimate({
                            effects: { fadeOut: { properties: { opacity: 0.5 } } },
                            duration: showOptions.duration,
                            show: true
                        });
                    } else {
                        overlay.css("opacity", 0.5).show();
                    }
                }

                if (!wrapper.is(VISIBLE)) {
                    contentElement.css(OVERFLOW, "hidden");
                    wrapper.show().kendoStop().kendoAnimate({
                        effects: showOptions.effects,
                        duration: showOptions.duration,
                        complete: function() {
                            that.trigger(ACTIVATE);
                            contentElement.css(OVERFLOW, initialOverflow);
                        }
                    });
                }

                that.toFront();
            }

            if (that.options.isMaximized) {
               $("html, body").css(OVERFLOW, "hidden");
            }

            return that;
        },

        /**
         * Closes the window
         * @example
         * var wnd = $("#window").data("kendoWindow");
         *
         * wnd.close();
         */
        close: function () {
            var that = this,
                wrapper = that.wrapper,
                options = that.options,
                hideOptions = options.animation.close,
                modalWindows,
                shouldHideOverlay, overlay;

            if (wrapper.is(VISIBLE) && !that.trigger(CLOSE)) {
                modalWindows = openedModalWindows();

                shouldHideOverlay = options.modal && modalWindows.length == 1;

                overlay = options.modal ? that._overlay(true) : $(undefined);

                if (shouldHideOverlay) {
                    if (hideOptions.duration) {
                        overlay.kendoStop().kendoAnimate({
                             effects: { fadeOut: { properties: { opacity: 0 } } },
                             duration: hideOptions.duration,
                             hide: true
                         });
                    } else {
                        overlay.hide();
                    }
                } else if (modalWindows.length) {
                    windowObject(modalWindows.eq(modalWindows.length - 2))._overlay(true);
                }

                wrapper.kendoStop().kendoAnimate({
                    effects: hideOptions.effects,
                    duration: hideOptions.duration,
                    complete: function() {
                        wrapper.hide();
                        that.trigger(DEACTIVATE);
                    }
                });
            }

            if (that.options.isMaximized) {
                $("html, body").css(OVERFLOW, "");
            }

            return that;
        },

        /**
         * Brings the window on top of other windows.
         */
        toFront: function () {
            var that = this,
                wrapper = that.wrapper,
                currentWindow = wrapper[0],
                zIndex = +wrapper.css("zIndex");

            $(KWINDOW).each(function(i, element) {
                var windowObject = $(element),
                    zIndexNew = windowObject.css("zIndex"),
                    contentElement = windowObject.find(".k-window-content");

                if (!isNaN(zIndexNew)) {
                    zIndex = Math.max(+zIndexNew, zIndex);
                }

                // Add overlay to windows with iframes and lower z-index to prevent
                // trapping of events when resizing / dragging
                if (element != currentWindow && contentElement.find("> .k-content-frame").length > 0) {
                    contentElement.append(templates.overlay);
                }
            });

            wrapper.css("zIndex", zIndex + 2)
            that.element.find("> .k-overlay").remove();

            return that;
        },

        /**
         * Toggles the window between a maximized and restored state.
         */
        toggleMaximization: function () {
            return this[this.options.isMaximized ? "restore" : "maximize"]();
        },

        /**
         * Restores a maximized window to its previous size.
         */
        restore: function () {
            var that = this,
                options = that.options,
                restorationSettings = that.restorationSettings;

            if (!options.isMaximized) {
                return;
            }

            that.wrapper
                .css({
                    position: "absolute",
                    left: restorationSettings.left,
                    top: restorationSettings.top,
                    width: restorationSettings.width,
                    height: restorationSettings.height
                })
                .find(".k-resize-handle").show().end()
                .find(".k-window-titlebar .k-restore").addClass("k-maximize").removeClass("k-restore");

            $("html, body").css(OVERFLOW, "");

            options.isMaximized = false;

            that.trigger(RESIZE);

            return that;
        },

        /**
         * Maximizes a window so that it fills the entire screen.
         */
        maximize: function () {
            var that = this;

            if (that.options.isMaximized) {
                return;
            }

            var wrapper = that.wrapper;

            that.restorationSettings = {
                left: wrapper.position().left,
                top: wrapper.position().top,
                width: wrapper.width(),
                height: wrapper.height()
            };

            wrapper
                .css({ left: 0, top: 0, position: "fixed" })
                .find(".k-resize-handle").hide().end()
                .find(".k-window-titlebar .k-maximize").addClass("k-restore").removeClass("k-maximize");

            $("html, body").css(OVERFLOW, "hidden");

            that.options.isMaximized = true;

            that._onDocumentResize();

            return that;
        },

        _onDocumentResize: function () {
            var that = this,
                wrapper = that.wrapper,
                wnd = $(window);

            if (!that.options.isMaximized) {
                return;
            }

            wrapper.css({
                    width: wnd.width(),
                    height: wnd.height()
                });

            that.trigger(RESIZE);
        },

        /**
         * Refreshes the window content from a remote url.
         * @param {Object|String} options Options for requesting data from the server. If omitted, the window uses the <code>content</code> property that was supplied when the window was created. Any options specified here are passed to the jQuery.ajax call.
         * @param {String} options.url The server URL that will be requested.
         * @param {Object} options.data A JSON object containing the data that will be passed to the server.
         * @param {String} options.type The request method ("GET", "POST").
         * @example
         * var windowObject = $("#window").data("kendoWindow");
         * windowObject.refresh("/feedbackForm");
         * windowObject.refresh({
         *     url: "/feedbackForm",
         *     data: { userId: 42 }
         * });
         */
        refresh: function (options) {
            if (!$.isPlainObject(options)) {
                options = { url: options };
            }

            var that = this,
                url = options.url = options.url || that.options.content.url;

            if (isLocalUrl(url)) {
                that._ajaxRequest(options);
            }

            return that;
        },

        _ajaxRequest: function (options) {
            var that = this,
                refreshIcon = that.wrapper.find(".k-window-titlebar .k-refresh"),
                loadingIconTimeout = setTimeout(function () {
                    refreshIcon.addClass(LOADING);
                }, 100);

            $.ajax($.extend({
                type: "GET",
                dataType: "html",
                cache: false,
                error: proxy(function (xhr, status) {
                    that.trigger(ERROR);
                }, that),
                complete: function () {
                    clearTimeout(loadingIconTimeout);
                    refreshIcon.removeClass(LOADING);
                },
                success: proxy(function (data, textStatus) {
                    that.wrapper.children(KWINDOWCONTENT).html(data);

                    that.trigger(REFRESH);
                }, that)
            }, that.options.content, options));
        },

        /**
         * Destroys the window and its modal overlay, if necessary. Useful for removing modal windows.
         */
        destroy: function () {
            var that = this,
                modalWindows,
                shouldHideOverlay;

            that.wrapper.remove();

            modalWindows = openedModalWindows();

            shouldHideOverlay = that.options.modal && !modalWindows.length;

            if (shouldHideOverlay) {
                that._overlay(false).remove();
            } else if (modalWindows.length > 0) {
                windowObject(modalWindows.eq(modalWindows.length - 2))._overlay(true);
            }
        }
    });

    templates = {
        wrapper: template("<div class='k-widget k-window' />"),
        titlebar: template(
            "<div class='k-window-titlebar k-header'>&nbsp;" +
                "<span class='k-window-title'>#= title #</span>" +
                "<div class='k-window-actions k-header'>" +
                "# for (var i = 0; i < actions.length; i++) { #" +
                    "<a href='\\#' class='k-window-action k-link'>" +
                        "<span class='k-icon k-#= actions[i].toLowerCase() #'>#= actions[i] #</span>" +
                    "</a>" +
                "# } #" +
                "</div>" +
            "</div>"
        ),
        overlay: "<div class='k-overlay' />",
        iframe: template(
            "<iframe src='#= content #' title='#= title #' frameborder='0'" +
                " class='k-content-frame'>" +
                    "This page requires frames in order to show content" +
            "</iframe>"
        ),
        resizeHandle: template("<div class='k-resize-handle k-resize-#= data #'></div>")
    };

    function createWindow(element, options) {
        var contentHtml = $(element);

        if (typeof (options.scrollable) != "undefined" && options.scrollable === false) {
            contentHtml.attr("style", "overflow:hidden;");
        }

        if (options.content && !isLocalUrl(options.content)) {
            contentHtml.html(templates.iframe(options));
        }

        $(templates.wrapper(options))
            .append(templates.titlebar(options))
            .append(contentHtml)
            .appendTo(body);
    }

    function WindowResizing(wnd) {
        var that = this;

        that.owner = wnd;
        that._draggable = new Draggable(wnd.wrapper, {
            filter: ".k-resize-handle",
            group: wnd.wrapper.id + "-resizing",
            dragstart: proxy(that.dragstart, that),
            drag: proxy(that.drag, that),
            dragend: proxy(that.dragend, that)
        });
    }

    WindowResizing.prototype = /** @ignore */ {
        dragstart: function (e) {
            var wnd = this.owner,
                wrapper = wnd.wrapper;

            wnd.elementPadding = parseInt(wnd.wrapper.css("padding-top"));
            wnd.initialCursorPosition = wrapper.offset();

            wnd.resizeDirection = e.currentTarget.prop("className").replace("k-resize-handle k-resize-", "").split("");

            wnd.initialSize = {
                width: wnd.wrapper.width(),
                height: wnd.wrapper.height()
            };

            wrapper
                .append(templates.overlay)
                .find(".k-resize-handle").not(e.currentTarget).hide();

            $(body).css(CURSOR, e.currentTarget.css(CURSOR));
        },
        drag: function (e) {
            var wnd = this.owner,
                wrapper = wnd.wrapper,
                options = wnd.options,
                constrain = function(value, low, high) {
                    return Math.max(Math.min(value, high), low);
                },
                resizeHandlers = {
                    "e": function () {
                        var newWidth = e.pageX - wnd.initialCursorPosition.left;

                        wrapper.width(constrain(newWidth, options.minWidth, options.maxWidth));
                    },
                    "s": function () {
                        var newHeight = e.pageY - wnd.initialCursorPosition.top - wnd.elementPadding;

                        wrapper.height(constrain(newHeight, options.minHeight, options.maxHeight));
                    },
                    "w": function () {
                        var windowRight = wnd.initialCursorPosition.left + wnd.initialSize.width,
                            newWidth = constrain(windowRight - e.pageX, options.minWidth, options.maxWidth);

                        wrapper.css({
                            left: windowRight - newWidth,
                            width: newWidth
                        })
                    },
                    "n": function () {
                        var windowBottom = wnd.initialCursorPosition.top + wnd.initialSize.height,
                            newHeight = constrain(windowBottom - e.pageY, options.minHeight, options.maxHeight);

                        wrapper.css({
                            top: windowBottom - newHeight,
                            height: newHeight
                        });
                    }
                };

            each(wnd.resizeDirection, function () {
                resizeHandlers[this]();
            });

            wnd.trigger(RESIZE);
        },
        dragend: function (e) {
            var wnd = this.owner,
                wrapper = wnd.wrapper;

            wrapper
                .find(KOVERLAY).remove().end()
                .find(".k-resize-handle").not(e.currentTarget).show();

            $(body).css(CURSOR, "");

            if (e.keyCode == 27) {
                wrapper.css(wnd.initialCursorPosition)
                    .css(wnd.initialSize);
            }

            return false;
        }
    };

    function WindowDragging(wnd) {
        var that = this;

        that.owner = wnd;
        that._draggable = new Draggable(wnd.wrapper, {
            filter: KWINDOWTITLEBAR,
            group: wnd.wrapper.id + "-moving",
            dragstart: proxy(that.dragstart, that),
            drag: proxy(that.drag, that),
            dragend: proxy(that.dragend, that)
        });
    }

    WindowDragging.prototype = /** @ignore */{
        dragstart: function (e) {
            var wnd = this.owner,
                $element = $(wnd.element);

            wnd.initialWindowPosition = wnd.wrapper.position();

            wnd.startPosition = {
                left: e.pageX - wnd.initialWindowPosition.left,
                top: e.pageY - wnd.initialWindowPosition.top
            };

            var actionsElement = $element.find(".k-window-actions");
            if (actionsElement.length > 0) {
                wnd.minLeftPosition = actionsElement.outerWidth() + parseInt(actionsElement.css("right"), 10) - $element.outerWidth();
            } else {
                wnd.minLeftPosition =  20 - $element.outerWidth(); // at least 20px remain visible
            }

            wnd.wrapper
                .append(templates.overlay)
                .find(".k-resize-handle").hide();

            $(body).css(CURSOR, e.currentTarget.css(CURSOR));
        },
        drag: function (e) {
            var wnd = this.owner,
                coordinates = {
                    left: Math.max(e.pageX - wnd.startPosition.left, wnd.minLeftPosition),
                    top: Math.max(e.pageY - wnd.startPosition.top, 0)
                };

            $(wnd.wrapper).css(coordinates);
        },
        dragend: function (e) {
            var wnd = this.owner;

            wnd.wrapper
                .find(".k-resize-handle").show().end()
                .find(KOVERLAY).remove();

            $(body).css(CURSOR, "");

            if (e.keyCode == 27) {
                e.currentTarget.closest(KWINDOW).css(wnd.initialWindowPosition);
            } else {
                wnd.trigger(DRAGEND);
            }

            return false;
        }
    };

    kendo.ui.plugin(Window);

})(jQuery);
