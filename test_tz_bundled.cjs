var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __commonJS = (cb, mod) => function __require() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
};
var __copyProps = (to, from, except, desc) => {
  if (from && typeof from === "object" || typeof from === "function") {
    for (let key of __getOwnPropNames(from))
      if (!__hasOwnProp.call(to, key) && key !== except)
        __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
  }
  return to;
};
var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
  // If the importer is in node compatibility mode or this is not an ESM
  // file that has been converted to a CommonJS file using a Babel-
  // compatible transform (i.e. "__esModule" has not been set), then set
  // "default" to the CommonJS "module.exports" for node compatibility.
  isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
  mod
));
var __publicField = (obj, key, value) => {
  __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
  return value;
};

// node_modules/dayjs/dayjs.min.js
var require_dayjs_min = __commonJS({
  "node_modules/dayjs/dayjs.min.js"(exports2, module2) {
    !function(t, e) {
      "object" == typeof exports2 && "undefined" != typeof module2 ? module2.exports = e() : "function" == typeof define && define.amd ? define(e) : (t = "undefined" != typeof globalThis ? globalThis : t || self).dayjs = e();
    }(exports2, function() {
      "use strict";
      var t = 1e3, e = 6e4, n = 36e5, r = "millisecond", i = "second", s = "minute", u = "hour", a = "day", o = "week", c = "month", f = "quarter", h = "year", d = "date", l2 = "Invalid Date", $ = /^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[Tt\s]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?[.:]?(\d+)?$/, y = /\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g, M = { name: "en", weekdays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"), months: "January_February_March_April_May_June_July_August_September_October_November_December".split("_"), ordinal: function(t2) {
        var e2 = ["th", "st", "nd", "rd"], n2 = t2 % 100;
        return "[" + t2 + (e2[(n2 - 20) % 10] || e2[n2] || e2[0]) + "]";
      } }, m = function(t2, e2, n2) {
        var r2 = String(t2);
        return !r2 || r2.length >= e2 ? t2 : "" + Array(e2 + 1 - r2.length).join(n2) + t2;
      }, v = { s: m, z: function(t2) {
        var e2 = -t2.utcOffset(), n2 = Math.abs(e2), r2 = Math.floor(n2 / 60), i2 = n2 % 60;
        return (e2 <= 0 ? "+" : "-") + m(r2, 2, "0") + ":" + m(i2, 2, "0");
      }, m: function t2(e2, n2) {
        if (e2.date() < n2.date())
          return -t2(n2, e2);
        var r2 = 12 * (n2.year() - e2.year()) + (n2.month() - e2.month()), i2 = e2.clone().add(r2, c), s2 = n2 - i2 < 0, u2 = e2.clone().add(r2 + (s2 ? -1 : 1), c);
        return +(-(r2 + (n2 - i2) / (s2 ? i2 - u2 : u2 - i2)) || 0);
      }, a: function(t2) {
        return t2 < 0 ? Math.ceil(t2) || 0 : Math.floor(t2);
      }, p: function(t2) {
        return { M: c, y: h, w: o, d: a, D: d, h: u, m: s, s: i, ms: r, Q: f }[t2] || String(t2 || "").toLowerCase().replace(/s$/, "");
      }, u: function(t2) {
        return void 0 === t2;
      } }, g = "en", D = {};
      D[g] = M;
      var p = "$isDayjsObject", S = function(t2) {
        return t2 instanceof _ || !(!t2 || !t2[p]);
      }, w = function t2(e2, n2, r2) {
        var i2;
        if (!e2)
          return g;
        if ("string" == typeof e2) {
          var s2 = e2.toLowerCase();
          D[s2] && (i2 = s2), n2 && (D[s2] = n2, i2 = s2);
          var u2 = e2.split("-");
          if (!i2 && u2.length > 1)
            return t2(u2[0]);
        } else {
          var a2 = e2.name;
          D[a2] = e2, i2 = a2;
        }
        return !r2 && i2 && (g = i2), i2 || !r2 && g;
      }, O = function(t2, e2) {
        if (S(t2))
          return t2.clone();
        var n2 = "object" == typeof e2 ? e2 : {};
        return n2.date = t2, n2.args = arguments, new _(n2);
      }, b = v;
      b.l = w, b.i = S, b.w = function(t2, e2) {
        return O(t2, { locale: e2.$L, utc: e2.$u, x: e2.$x, $offset: e2.$offset });
      };
      var _ = function() {
        function M2(t2) {
          this.$L = w(t2.locale, null, true), this.parse(t2), this.$x = this.$x || t2.x || {}, this[p] = true;
        }
        var m2 = M2.prototype;
        return m2.parse = function(t2) {
          this.$d = function(t3) {
            var e2 = t3.date, n2 = t3.utc;
            if (null === e2)
              return /* @__PURE__ */ new Date(NaN);
            if (b.u(e2))
              return /* @__PURE__ */ new Date();
            if (e2 instanceof Date)
              return new Date(e2);
            if ("string" == typeof e2 && !/Z$/i.test(e2)) {
              var r2 = e2.match($);
              if (r2) {
                var i2 = r2[2] - 1 || 0, s2 = (r2[7] || "0").substring(0, 3);
                return n2 ? new Date(Date.UTC(r2[1], i2, r2[3] || 1, r2[4] || 0, r2[5] || 0, r2[6] || 0, s2)) : new Date(r2[1], i2, r2[3] || 1, r2[4] || 0, r2[5] || 0, r2[6] || 0, s2);
              }
            }
            return new Date(e2);
          }(t2), this.init();
        }, m2.init = function() {
          var t2 = this.$d;
          this.$y = t2.getFullYear(), this.$M = t2.getMonth(), this.$D = t2.getDate(), this.$W = t2.getDay(), this.$H = t2.getHours(), this.$m = t2.getMinutes(), this.$s = t2.getSeconds(), this.$ms = t2.getMilliseconds();
        }, m2.$utils = function() {
          return b;
        }, m2.isValid = function() {
          return !(this.$d.toString() === l2);
        }, m2.isSame = function(t2, e2) {
          var n2 = O(t2);
          return this.startOf(e2) <= n2 && n2 <= this.endOf(e2);
        }, m2.isAfter = function(t2, e2) {
          return O(t2) < this.startOf(e2);
        }, m2.isBefore = function(t2, e2) {
          return this.endOf(e2) < O(t2);
        }, m2.$g = function(t2, e2, n2) {
          return b.u(t2) ? this[e2] : this.set(n2, t2);
        }, m2.unix = function() {
          return Math.floor(this.valueOf() / 1e3);
        }, m2.valueOf = function() {
          return this.$d.getTime();
        }, m2.startOf = function(t2, e2) {
          var n2 = this, r2 = !!b.u(e2) || e2, f2 = b.p(t2), l3 = function(t3, e3) {
            var i2 = b.w(n2.$u ? Date.UTC(n2.$y, e3, t3) : new Date(n2.$y, e3, t3), n2);
            return r2 ? i2 : i2.endOf(a);
          }, $2 = function(t3, e3) {
            return b.w(n2.toDate()[t3].apply(n2.toDate("s"), (r2 ? [0, 0, 0, 0] : [23, 59, 59, 999]).slice(e3)), n2);
          }, y2 = this.$W, M3 = this.$M, m3 = this.$D, v2 = "set" + (this.$u ? "UTC" : "");
          switch (f2) {
            case h:
              return r2 ? l3(1, 0) : l3(31, 11);
            case c:
              return r2 ? l3(1, M3) : l3(0, M3 + 1);
            case o:
              var g2 = this.$locale().weekStart || 0, D2 = (y2 < g2 ? y2 + 7 : y2) - g2;
              return l3(r2 ? m3 - D2 : m3 + (6 - D2), M3);
            case a:
            case d:
              return $2(v2 + "Hours", 0);
            case u:
              return $2(v2 + "Minutes", 1);
            case s:
              return $2(v2 + "Seconds", 2);
            case i:
              return $2(v2 + "Milliseconds", 3);
            default:
              return this.clone();
          }
        }, m2.endOf = function(t2) {
          return this.startOf(t2, false);
        }, m2.$set = function(t2, e2) {
          var n2, o2 = b.p(t2), f2 = "set" + (this.$u ? "UTC" : ""), l3 = (n2 = {}, n2[a] = f2 + "Date", n2[d] = f2 + "Date", n2[c] = f2 + "Month", n2[h] = f2 + "FullYear", n2[u] = f2 + "Hours", n2[s] = f2 + "Minutes", n2[i] = f2 + "Seconds", n2[r] = f2 + "Milliseconds", n2)[o2], $2 = o2 === a ? this.$D + (e2 - this.$W) : e2;
          if (o2 === c || o2 === h) {
            var y2 = this.clone().set(d, 1);
            y2.$d[l3]($2), y2.init(), this.$d = y2.set(d, Math.min(this.$D, y2.daysInMonth())).$d;
          } else
            l3 && this.$d[l3]($2);
          return this.init(), this;
        }, m2.set = function(t2, e2) {
          return this.clone().$set(t2, e2);
        }, m2.get = function(t2) {
          return this[b.p(t2)]();
        }, m2.add = function(r2, f2) {
          var d2, l3 = this;
          r2 = Number(r2);
          var $2 = b.p(f2), y2 = function(t2) {
            var e2 = O(l3);
            return b.w(e2.date(e2.date() + Math.round(t2 * r2)), l3);
          };
          if ($2 === c)
            return this.set(c, this.$M + r2);
          if ($2 === h)
            return this.set(h, this.$y + r2);
          if ($2 === a)
            return y2(1);
          if ($2 === o)
            return y2(7);
          var M3 = (d2 = {}, d2[s] = e, d2[u] = n, d2[i] = t, d2)[$2] || 1, m3 = this.$d.getTime() + r2 * M3;
          return b.w(m3, this);
        }, m2.subtract = function(t2, e2) {
          return this.add(-1 * t2, e2);
        }, m2.format = function(t2) {
          var e2 = this, n2 = this.$locale();
          if (!this.isValid())
            return n2.invalidDate || l2;
          var r2 = t2 || "YYYY-MM-DDTHH:mm:ssZ", i2 = b.z(this), s2 = this.$H, u2 = this.$m, a2 = this.$M, o2 = n2.weekdays, c2 = n2.months, f2 = n2.meridiem, h2 = function(t3, n3, i3, s3) {
            return t3 && (t3[n3] || t3(e2, r2)) || i3[n3].slice(0, s3);
          }, d2 = function(t3) {
            return b.s(s2 % 12 || 12, t3, "0");
          }, $2 = f2 || function(t3, e3, n3) {
            var r3 = t3 < 12 ? "AM" : "PM";
            return n3 ? r3.toLowerCase() : r3;
          };
          return r2.replace(y, function(t3, r3) {
            return r3 || function(t4) {
              switch (t4) {
                case "YY":
                  return String(e2.$y).slice(-2);
                case "YYYY":
                  return b.s(e2.$y, 4, "0");
                case "M":
                  return a2 + 1;
                case "MM":
                  return b.s(a2 + 1, 2, "0");
                case "MMM":
                  return h2(n2.monthsShort, a2, c2, 3);
                case "MMMM":
                  return h2(c2, a2);
                case "D":
                  return e2.$D;
                case "DD":
                  return b.s(e2.$D, 2, "0");
                case "d":
                  return String(e2.$W);
                case "dd":
                  return h2(n2.weekdaysMin, e2.$W, o2, 2);
                case "ddd":
                  return h2(n2.weekdaysShort, e2.$W, o2, 3);
                case "dddd":
                  return o2[e2.$W];
                case "H":
                  return String(s2);
                case "HH":
                  return b.s(s2, 2, "0");
                case "h":
                  return d2(1);
                case "hh":
                  return d2(2);
                case "a":
                  return $2(s2, u2, true);
                case "A":
                  return $2(s2, u2, false);
                case "m":
                  return String(u2);
                case "mm":
                  return b.s(u2, 2, "0");
                case "s":
                  return String(e2.$s);
                case "ss":
                  return b.s(e2.$s, 2, "0");
                case "SSS":
                  return b.s(e2.$ms, 3, "0");
                case "Z":
                  return i2;
              }
              return null;
            }(t3) || i2.replace(":", "");
          });
        }, m2.utcOffset = function() {
          return 15 * -Math.round(this.$d.getTimezoneOffset() / 15);
        }, m2.diff = function(r2, d2, l3) {
          var $2, y2 = this, M3 = b.p(d2), m3 = O(r2), v2 = (m3.utcOffset() - this.utcOffset()) * e, g2 = this - m3, D2 = function() {
            return b.m(y2, m3);
          };
          switch (M3) {
            case h:
              $2 = D2() / 12;
              break;
            case c:
              $2 = D2();
              break;
            case f:
              $2 = D2() / 3;
              break;
            case o:
              $2 = (g2 - v2) / 6048e5;
              break;
            case a:
              $2 = (g2 - v2) / 864e5;
              break;
            case u:
              $2 = g2 / n;
              break;
            case s:
              $2 = g2 / e;
              break;
            case i:
              $2 = g2 / t;
              break;
            default:
              $2 = g2;
          }
          return l3 ? $2 : b.a($2);
        }, m2.daysInMonth = function() {
          return this.endOf(c).$D;
        }, m2.$locale = function() {
          return D[this.$L];
        }, m2.locale = function(t2, e2) {
          if (!t2)
            return this.$L;
          var n2 = this.clone(), r2 = w(t2, e2, true);
          return r2 && (n2.$L = r2), n2;
        }, m2.clone = function() {
          return b.w(this.$d, this);
        }, m2.toDate = function() {
          return new Date(this.valueOf());
        }, m2.toJSON = function() {
          return this.isValid() ? this.toISOString() : null;
        }, m2.toISOString = function() {
          return this.$d.toISOString();
        }, m2.toString = function() {
          return this.$d.toUTCString();
        }, M2;
      }(), k = _.prototype;
      return O.prototype = k, [["$ms", r], ["$s", i], ["$m", s], ["$H", u], ["$W", a], ["$M", c], ["$y", h], ["$D", d]].forEach(function(t2) {
        k[t2[1]] = function(e2) {
          return this.$g(e2, t2[0], t2[1]);
        };
      }), O.extend = function(t2, e2) {
        return t2.$i || (t2(e2, _, O), t2.$i = true), O;
      }, O.locale = w, O.isDayjs = S, O.unix = function(t2) {
        return O(1e3 * t2);
      }, O.en = D[g], O.Ls = D, O.p = {}, O;
    });
  }
});

// node_modules/dayjs/plugin/customParseFormat.js
var require_customParseFormat = __commonJS({
  "node_modules/dayjs/plugin/customParseFormat.js"(exports2, module2) {
    !function(e, t) {
      "object" == typeof exports2 && "undefined" != typeof module2 ? module2.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = "undefined" != typeof globalThis ? globalThis : e || self).dayjs_plugin_customParseFormat = t();
    }(exports2, function() {
      "use strict";
      var e = { LTS: "h:mm:ss A", LT: "h:mm A", L: "MM/DD/YYYY", LL: "MMMM D, YYYY", LLL: "MMMM D, YYYY h:mm A", LLLL: "dddd, MMMM D, YYYY h:mm A" }, t = /(\[[^[]*\])|([-_:/.,()\s]+)|(A|a|Q|YYYY|YY?|ww?|MM?M?M?|Do|DD?|hh?|HH?|mm?|ss?|S{1,3}|z|ZZ?)/g, n = /\d/, r = /\d\d/, i = /\d\d?/, o = /\d*[^-_:/,()\s\d]+/, s = {}, a = function(e2) {
        return (e2 = +e2) + (e2 > 68 ? 1900 : 2e3);
      };
      var f = function(e2) {
        return function(t2) {
          this[e2] = +t2;
        };
      }, h = [/[+-]\d\d:?(\d\d)?|Z/, function(e2) {
        (this.zone || (this.zone = {})).offset = function(e3) {
          if (!e3)
            return 0;
          if ("Z" === e3)
            return 0;
          var t2 = e3.match(/([+-]|\d\d)/g), n2 = 60 * t2[1] + (+t2[2] || 0);
          return 0 === n2 ? 0 : "+" === t2[0] ? -n2 : n2;
        }(e2);
      }], u = function(e2) {
        var t2 = s[e2];
        return t2 && (t2.indexOf ? t2 : t2.s.concat(t2.f));
      }, d = function(e2, t2) {
        var n2, r2 = s.meridiem;
        if (r2) {
          for (var i2 = 1; i2 <= 24; i2 += 1)
            if (e2.indexOf(r2(i2, 0, t2)) > -1) {
              n2 = i2 > 12;
              break;
            }
        } else
          n2 = e2 === (t2 ? "pm" : "PM");
        return n2;
      }, c = { A: [o, function(e2) {
        this.afternoon = d(e2, false);
      }], a: [o, function(e2) {
        this.afternoon = d(e2, true);
      }], Q: [n, function(e2) {
        this.month = 3 * (e2 - 1) + 1;
      }], S: [n, function(e2) {
        this.milliseconds = 100 * +e2;
      }], SS: [r, function(e2) {
        this.milliseconds = 10 * +e2;
      }], SSS: [/\d{3}/, function(e2) {
        this.milliseconds = +e2;
      }], s: [i, f("seconds")], ss: [i, f("seconds")], m: [i, f("minutes")], mm: [i, f("minutes")], H: [i, f("hours")], h: [i, f("hours")], HH: [i, f("hours")], hh: [i, f("hours")], D: [i, f("day")], DD: [r, f("day")], Do: [o, function(e2) {
        var t2 = s.ordinal, n2 = e2.match(/\d+/);
        if (this.day = n2[0], t2)
          for (var r2 = 1; r2 <= 31; r2 += 1)
            t2(r2).replace(/\[|\]/g, "") === e2 && (this.day = r2);
      }], w: [i, f("week")], ww: [r, f("week")], M: [i, f("month")], MM: [r, f("month")], MMM: [o, function(e2) {
        var t2 = u("months"), n2 = (u("monthsShort") || t2.map(function(e3) {
          return e3.slice(0, 3);
        })).indexOf(e2) + 1;
        if (n2 < 1)
          throw new Error();
        this.month = n2 % 12 || n2;
      }], MMMM: [o, function(e2) {
        var t2 = u("months").indexOf(e2) + 1;
        if (t2 < 1)
          throw new Error();
        this.month = t2 % 12 || t2;
      }], Y: [/[+-]?\d+/, f("year")], YY: [r, function(e2) {
        this.year = a(e2);
      }], YYYY: [/\d{4}/, f("year")], Z: h, ZZ: h };
      function l2(n2) {
        var r2, i2;
        r2 = n2, i2 = s && s.formats;
        for (var o2 = (n2 = r2.replace(/(\[[^\]]+])|(LTS?|l{1,4}|L{1,4})/g, function(t2, n3, r3) {
          var o3 = r3 && r3.toUpperCase();
          return n3 || i2[r3] || e[r3] || i2[o3].replace(/(\[[^\]]+])|(MMMM|MM|DD|dddd)/g, function(e2, t3, n4) {
            return t3 || n4.slice(1);
          });
        })).match(t), a2 = o2.length, f2 = 0; f2 < a2; f2 += 1) {
          var h2 = o2[f2], u2 = c[h2], d2 = u2 && u2[0], l3 = u2 && u2[1];
          o2[f2] = l3 ? { regex: d2, parser: l3 } : h2.replace(/^\[|\]$/g, "");
        }
        return function(e2) {
          for (var t2 = {}, n3 = 0, r3 = 0; n3 < a2; n3 += 1) {
            var i3 = o2[n3];
            if ("string" == typeof i3)
              r3 += i3.length;
            else {
              var s2 = i3.regex, f3 = i3.parser, h3 = e2.slice(r3), u3 = s2.exec(h3)[0];
              f3.call(t2, u3), e2 = e2.replace(u3, "");
            }
          }
          return function(e3) {
            var t3 = e3.afternoon;
            if (void 0 !== t3) {
              var n4 = e3.hours;
              t3 ? n4 < 12 && (e3.hours += 12) : 12 === n4 && (e3.hours = 0), delete e3.afternoon;
            }
          }(t2), t2;
        };
      }
      return function(e2, t2, n2) {
        n2.p.customParseFormat = true, e2 && e2.parseTwoDigitYear && (a = e2.parseTwoDigitYear);
        var r2 = t2.prototype, i2 = r2.parse;
        r2.parse = function(e3) {
          var t3 = e3.date, r3 = e3.utc, o2 = e3.args;
          this.$u = r3;
          var a2 = o2[1];
          if ("string" == typeof a2) {
            var f2 = true === o2[2], h2 = true === o2[3], u2 = f2 || h2, d2 = o2[2];
            h2 && (d2 = o2[2]), s = this.$locale(), !f2 && d2 && (s = n2.Ls[d2]), this.$d = function(e4, t4, n3, r4) {
              try {
                if (["x", "X"].indexOf(t4) > -1)
                  return new Date(("X" === t4 ? 1e3 : 1) * e4);
                var i3 = l2(t4)(e4), o3 = i3.year, s2 = i3.month, a3 = i3.day, f3 = i3.hours, h3 = i3.minutes, u3 = i3.seconds, d3 = i3.milliseconds, c3 = i3.zone, m2 = i3.week, M2 = /* @__PURE__ */ new Date(), Y = a3 || (o3 || s2 ? 1 : M2.getDate()), p = o3 || M2.getFullYear(), v = 0;
                o3 && !s2 || (v = s2 > 0 ? s2 - 1 : M2.getMonth());
                var D, w = f3 || 0, g = h3 || 0, y = u3 || 0, L = d3 || 0;
                return c3 ? new Date(Date.UTC(p, v, Y, w, g, y, L + 60 * c3.offset * 1e3)) : n3 ? new Date(Date.UTC(p, v, Y, w, g, y, L)) : (D = new Date(p, v, Y, w, g, y, L), m2 && (D = r4(D).week(m2).toDate()), D);
              } catch (e5) {
                return /* @__PURE__ */ new Date("");
              }
            }(t3, a2, r3, n2), this.init(), d2 && true !== d2 && (this.$L = this.locale(d2).$L), u2 && t3 != this.format(a2) && (this.$d = /* @__PURE__ */ new Date("")), s = {};
          } else if (a2 instanceof Array)
            for (var c2 = a2.length, m = 1; m <= c2; m += 1) {
              o2[1] = a2[m - 1];
              var M = n2.apply(this, o2);
              if (M.isValid()) {
                this.$d = M.$d, this.$L = M.$L, this.init();
                break;
              }
              m === c2 && (this.$d = /* @__PURE__ */ new Date(""));
            }
          else
            i2.call(this, e3);
        };
      };
    });
  }
});

// node_modules/dayjs/plugin/localeData.js
var require_localeData = __commonJS({
  "node_modules/dayjs/plugin/localeData.js"(exports2, module2) {
    !function(n, e) {
      "object" == typeof exports2 && "undefined" != typeof module2 ? module2.exports = e() : "function" == typeof define && define.amd ? define(e) : (n = "undefined" != typeof globalThis ? globalThis : n || self).dayjs_plugin_localeData = e();
    }(exports2, function() {
      "use strict";
      return function(n, e, t) {
        var r = e.prototype, o = function(n2) {
          return n2 && (n2.indexOf ? n2 : n2.s);
        }, u = function(n2, e2, t2, r2, u2) {
          var i2 = n2.name ? n2 : n2.$locale(), a2 = o(i2[e2]), s2 = o(i2[t2]), f = a2 || s2.map(function(n3) {
            return n3.slice(0, r2);
          });
          if (!u2)
            return f;
          var d = i2.weekStart;
          return f.map(function(n3, e3) {
            return f[(e3 + (d || 0)) % 7];
          });
        }, i = function() {
          return t.Ls[t.locale()];
        }, a = function(n2, e2) {
          return n2.formats[e2] || function(n3) {
            return n3.replace(/(\[[^\]]+])|(MMMM|MM|DD|dddd)/g, function(n4, e3, t2) {
              return e3 || t2.slice(1);
            });
          }(n2.formats[e2.toUpperCase()]);
        }, s = function() {
          var n2 = this;
          return { months: function(e2) {
            return e2 ? e2.format("MMMM") : u(n2, "months");
          }, monthsShort: function(e2) {
            return e2 ? e2.format("MMM") : u(n2, "monthsShort", "months", 3);
          }, firstDayOfWeek: function() {
            return n2.$locale().weekStart || 0;
          }, weekdays: function(e2) {
            return e2 ? e2.format("dddd") : u(n2, "weekdays");
          }, weekdaysMin: function(e2) {
            return e2 ? e2.format("dd") : u(n2, "weekdaysMin", "weekdays", 2);
          }, weekdaysShort: function(e2) {
            return e2 ? e2.format("ddd") : u(n2, "weekdaysShort", "weekdays", 3);
          }, longDateFormat: function(e2) {
            return a(n2.$locale(), e2);
          }, meridiem: this.$locale().meridiem, ordinal: this.$locale().ordinal };
        };
        r.localeData = function() {
          return s.bind(this)();
        }, t.localeData = function() {
          var n2 = i();
          return { firstDayOfWeek: function() {
            return n2.weekStart || 0;
          }, weekdays: function() {
            return t.weekdays();
          }, weekdaysShort: function() {
            return t.weekdaysShort();
          }, weekdaysMin: function() {
            return t.weekdaysMin();
          }, months: function() {
            return t.months();
          }, monthsShort: function() {
            return t.monthsShort();
          }, longDateFormat: function(e2) {
            return a(n2, e2);
          }, meridiem: n2.meridiem, ordinal: n2.ordinal };
        }, t.months = function() {
          return u(i(), "months");
        }, t.monthsShort = function() {
          return u(i(), "monthsShort", "months", 3);
        }, t.weekdays = function(n2) {
          return u(i(), "weekdays", null, null, n2);
        }, t.weekdaysShort = function(n2) {
          return u(i(), "weekdaysShort", "weekdays", 3, n2);
        }, t.weekdaysMin = function(n2) {
          return u(i(), "weekdaysMin", "weekdays", 2, n2);
        };
      };
    });
  }
});

// node_modules/dayjs/plugin/timezone.js
var require_timezone = __commonJS({
  "node_modules/dayjs/plugin/timezone.js"(exports2, module2) {
    !function(t, e) {
      "object" == typeof exports2 && "undefined" != typeof module2 ? module2.exports = e() : "function" == typeof define && define.amd ? define(e) : (t = "undefined" != typeof globalThis ? globalThis : t || self).dayjs_plugin_timezone = e();
    }(exports2, function() {
      "use strict";
      var t = { year: 0, month: 1, day: 2, hour: 3, minute: 4, second: 5 }, e = {};
      return function(n, i, o) {
        var r, a = function(t2, n2, i2) {
          void 0 === i2 && (i2 = {});
          var o2 = new Date(t2), r2 = function(t3, n3) {
            void 0 === n3 && (n3 = {});
            var i3 = n3.timeZoneName || "short", o3 = t3 + "|" + i3, r3 = e[o3];
            return r3 || (r3 = new Intl.DateTimeFormat("en-US", { hour12: false, timeZone: t3, year: "numeric", month: "2-digit", day: "2-digit", hour: "2-digit", minute: "2-digit", second: "2-digit", timeZoneName: i3 }), e[o3] = r3), r3;
          }(n2, i2);
          return r2.formatToParts(o2);
        }, u = function(e2, n2) {
          for (var i2 = a(e2, n2), r2 = [], u2 = 0; u2 < i2.length; u2 += 1) {
            var f2 = i2[u2], s2 = f2.type, m = f2.value, c = t[s2];
            c >= 0 && (r2[c] = parseInt(m, 10));
          }
          var d = r2[3], l2 = 24 === d ? 0 : d, h = r2[0] + "-" + r2[1] + "-" + r2[2] + " " + l2 + ":" + r2[4] + ":" + r2[5] + ":000", v = +e2;
          return (o.utc(h).valueOf() - (v -= v % 1e3)) / 6e4;
        }, f = i.prototype;
        f.tz = function(t2, e2) {
          void 0 === t2 && (t2 = r);
          var n2, i2 = this.utcOffset(), a2 = this.toDate(), u2 = a2.toLocaleString("en-US", { timeZone: t2 }), f2 = Math.round((a2 - new Date(u2)) / 1e3 / 60), s2 = 15 * -Math.round(a2.getTimezoneOffset() / 15) - f2;
          if (!Number(s2))
            n2 = this.utcOffset(0, e2);
          else if (n2 = o(u2, { locale: this.$L }).$set("millisecond", this.$ms).utcOffset(s2, true), e2) {
            var m = n2.utcOffset();
            n2 = n2.add(i2 - m, "minute");
          }
          return n2.$x.$timezone = t2, n2;
        }, f.offsetName = function(t2) {
          var e2 = this.$x.$timezone || o.tz.guess(), n2 = a(this.valueOf(), e2, { timeZoneName: t2 }).find(function(t3) {
            return "timezonename" === t3.type.toLowerCase();
          });
          return n2 && n2.value;
        };
        var s = f.startOf;
        f.startOf = function(t2, e2) {
          if (!this.$x || !this.$x.$timezone)
            return s.call(this, t2, e2);
          var n2 = o(this.format("YYYY-MM-DD HH:mm:ss:SSS"), { locale: this.$L });
          return s.call(n2, t2, e2).tz(this.$x.$timezone, true);
        }, o.tz = function(t2, e2, n2) {
          var i2 = n2 && e2, a2 = n2 || e2 || r, f2 = u(+o(), a2);
          if ("string" != typeof t2)
            return o(t2).tz(a2);
          var s2 = function(t3, e3, n3) {
            var i3 = t3 - 60 * e3 * 1e3, o2 = u(i3, n3);
            if (e3 === o2)
              return [i3, e3];
            var r2 = u(i3 -= 60 * (o2 - e3) * 1e3, n3);
            return o2 === r2 ? [i3, o2] : [t3 - 60 * Math.min(o2, r2) * 1e3, Math.max(o2, r2)];
          }(o.utc(t2, i2).valueOf(), f2, a2), m = s2[0], c = s2[1], d = o(m).utcOffset(c);
          return d.$x.$timezone = a2, d;
        }, o.tz.guess = function() {
          return Intl.DateTimeFormat().resolvedOptions().timeZone;
        }, o.tz.setDefault = function(t2) {
          r = t2;
        };
      };
    });
  }
});

// node_modules/dayjs/plugin/utc.js
var require_utc = __commonJS({
  "node_modules/dayjs/plugin/utc.js"(exports2, module2) {
    !function(t, i) {
      "object" == typeof exports2 && "undefined" != typeof module2 ? module2.exports = i() : "function" == typeof define && define.amd ? define(i) : (t = "undefined" != typeof globalThis ? globalThis : t || self).dayjs_plugin_utc = i();
    }(exports2, function() {
      "use strict";
      var t = "minute", i = /[+-]\d\d(?::?\d\d)?/g, e = /([+-]|\d\d)/g;
      return function(s, f, n) {
        var u = f.prototype;
        n.utc = function(t2) {
          var i2 = { date: t2, utc: true, args: arguments };
          return new f(i2);
        }, u.utc = function(i2) {
          var e2 = n(this.toDate(), { locale: this.$L, utc: true });
          return i2 ? e2.add(this.utcOffset(), t) : e2;
        }, u.local = function() {
          return n(this.toDate(), { locale: this.$L, utc: false });
        };
        var r = u.parse;
        u.parse = function(t2) {
          t2.utc && (this.$u = true), this.$utils().u(t2.$offset) || (this.$offset = t2.$offset), r.call(this, t2);
        };
        var o = u.init;
        u.init = function() {
          if (this.$u) {
            var t2 = this.$d;
            this.$y = t2.getUTCFullYear(), this.$M = t2.getUTCMonth(), this.$D = t2.getUTCDate(), this.$W = t2.getUTCDay(), this.$H = t2.getUTCHours(), this.$m = t2.getUTCMinutes(), this.$s = t2.getUTCSeconds(), this.$ms = t2.getUTCMilliseconds();
          } else
            o.call(this);
        };
        var a = u.utcOffset;
        u.utcOffset = function(s2, f2) {
          var n2 = this.$utils().u;
          if (n2(s2))
            return this.$u ? 0 : n2(this.$offset) ? a.call(this) : this.$offset;
          if ("string" == typeof s2 && (s2 = function(t2) {
            void 0 === t2 && (t2 = "");
            var s3 = t2.match(i);
            if (!s3)
              return null;
            var f3 = ("" + s3[0]).match(e) || ["-", 0, 0], n3 = f3[0], u3 = 60 * +f3[1] + +f3[2];
            return 0 === u3 ? 0 : "+" === n3 ? u3 : -u3;
          }(s2), null === s2))
            return this;
          var u2 = Math.abs(s2) <= 16 ? 60 * s2 : s2;
          if (0 === u2)
            return this.utc(f2);
          var r2 = this.clone();
          if (f2)
            return r2.$offset = u2, r2.$u = false, r2;
          var o2 = this.$u ? this.toDate().getTimezoneOffset() : -1 * this.utcOffset();
          return (r2 = this.local().add(u2 + o2, t)).$offset = u2, r2.$x.$localOffset = o2, r2;
        };
        var h = u.format;
        u.format = function(t2) {
          var i2 = t2 || (this.$u ? "YYYY-MM-DDTHH:mm:ss[Z]" : "");
          return h.call(this, i2);
        }, u.valueOf = function() {
          var t2 = this.$utils().u(this.$offset) ? 0 : this.$offset + (this.$x.$localOffset || this.$d.getTimezoneOffset());
          return this.$d.valueOf() - 6e4 * t2;
        }, u.isUTC = function() {
          return !!this.$u;
        }, u.toISOString = function() {
          return this.toDate().toISOString();
        }, u.toString = function() {
          return this.toDate().toUTCString();
        };
        var l2 = u.toDate;
        u.toDate = function(t2) {
          return "s" === t2 && this.$offset ? n(this.format("YYYY-MM-DD HH:mm:ss:SSS")).toDate() : l2.call(this);
        };
        var c = u.diff;
        u.diff = function(t2, i2, e2) {
          if (t2 && this.$u === t2.$u)
            return c.call(this, t2, i2, e2);
          var s2 = this.local(), f2 = n(t2).local();
          return c.call(s2, f2, i2, e2);
        };
      };
    });
  }
});

// node_modules/@calidy/dayjs-calendarsystems/dayjs-calendarsystems.cjs.min.js
var require_dayjs_calendarsystems_cjs_min = __commonJS({
  "node_modules/@calidy/dayjs-calendarsystems/dayjs-calendarsystems.cjs.min.js"(exports2, module2) {
    "use strict";
    var t = {};
    var e = {};
    function r(e2, r2 = "persian") {
      const n2 = `${e2}-${r2}`;
      if (!t[n2]) {
        const s2 = [];
        for (let t2 = 0; t2 < 12; t2++)
          if ("amazigh" === r2)
            s2.push(o(t2, e2));
          else {
            const n3 = new Date(2023, t2, 1), o2 = new Intl.DateTimeFormat(`${e2}-u-ca-${r2}`, { month: "long" });
            s2.push(o2.format(n3));
          }
        t[n2] = s2;
      }
      return t[n2];
    }
    var n = { tzm: ["\u2D62\u2D3B\u2D4F\u2D4F\u2D30\u2D62\u2D3B\u2D54", "\u2D3C\u2D53\u2D54\u2D30\u2D54", "\u2D4E\u2D3B\u2D56\u2D54\u2D3B\u2D59", "\u2D62\u2D3B\u2D31\u2D54\u2D49\u2D54", "\u2D4E\u2D30\u2D62\u2D62\u2D53", "\u2D62\u2D53\u2D4F\u2D62\u2D53", "\u2D62\u2D53\u2D4D\u2D62\u2D53\u2D63", "\u2D56\u2D53\u2D5B\u2D5C", "\u2D59\u2D53\u2D5C\u2D3B\u2D4F\u2D31\u2D49\u2D54", "\u2D3D\u2D5C\u2D53\u2D31\u2D54", "\u2D4F\u2D53\u2D4F\u2D3B\u2D4E\u2D31\u2D49\u2D54", "\u2D37\u2D53\u2D4A\u2D3B\u2D4E\u2D31\u2D49\u2D54"], ar: ["\u064A\u0646\u0627\u064A\u0631", "\u0641\u0628\u0631\u0627\u064A\u0631", "\u0645\u0627\u0631\u0633", "\u0623\u0628\u0631\u064A\u0644", "\u0645\u0627\u064A\u0648", "\u064A\u0648\u0646\u064A\u0648", "\u064A\u0648\u0644\u064A\u0648", "\u0623\u063A\u0633\u0637\u0633", "\u0633\u0628\u062A\u0645\u0628\u0631", "\u0623\u0643\u062A\u0648\u0628\u0631", "\u0646\u0648\u0641\u0645\u0628\u0631", "\u062F\u064A\u0633\u0645\u0628\u0631"], default: ["Yennayer", "Furar", "Meghres", "Yebrir", "Mayyu", "Yunyu", "Yulyu", "Ghuct", "Cutenber", "Ktuber", "Nunember", "Dujember"] };
    function o(t2, e2) {
      return n[e2] ? n[e2][t2] : n.default[t2];
    }
    function s(t2, n2 = "persian", o2 = "Farvardin") {
      return function(t3, n3, o3 = "persian") {
        const s2 = `${t3}-${n3}-${o3}`;
        if (!e[s2]) {
          let i2 = r(t3, o3);
          i2 = [...i2.slice(n3), ...i2.slice(0, n3)], e[s2] = i2;
        }
        return e[s2];
      }(t2, r("en", n2).indexOf(o2), n2);
    }
    var i = class {
      constructor(t2 = "en") {
        this.locale = t2, this.intlCalendar = "gregory", this.firstMonthNameEnglish = "January", this.monthNamesLocalized = s(t2, "gregory", "January");
      }
      convertFromGregorian(t2) {
        throw new Error("Method convertFromGregorian must be implemented by subclass");
      }
      convertToGregorian(t2) {
        throw new Error("Method convertToGregorian must be implemented by subclass");
      }
      convertFromJulian(t2) {
        throw new Error("Method convertToJulian must be implemented by subclass");
      }
      convertToJulian(t2) {
        throw new Error("Method convertToJulian must be implemented by subclass");
      }
      isLeapYear(t2) {
        throw new Error("Method isLeapYear must be implemented by subclass");
      }
      monthNames(t2, e2, r2) {
        throw new Error("Method monthNames must be implemented by subclass");
      }
      getLocalizedMonthName(t2) {
        const e2 = this.monthNames();
        if (t2 < 0 || t2 >= e2.length)
          throw new Error("Invalid month index.");
        return this.monthNamesLocalized[t2];
      }
      localeOverride(t2) {
        return { gregorianMonths: this.monthNames(t2, "gregory", "January"), months: this.monthNames(t2), monthsShort: this.monthNames(t2).map((t3) => t3.substring(0, 3)) };
      }
      validateDate(t2) {
        if (null == t2)
          t2 = /* @__PURE__ */ new Date();
        else if ("string" == typeof t2)
          t2 = new Date(t2);
        else if ("number" == typeof t2)
          t2 = new Date(t2);
        else if (t2 instanceof Date)
          ;
        else if (void 0 !== t2.$y && void 0 !== t2.$M && void 0 !== t2.$D)
          t2 = new Date(t2.$y, t2.$M, t2.$D, t2.$H, t2.$m, t2.$s, t2.$ms);
        else {
          if (void 0 === t2.year || void 0 === t2.month || void 0 === t2.day)
            throw new Error("Invalid date");
          t2 = new Date(t2.year, t2.month, t2.day);
        }
        return t2;
      }
    };
    __publicField(i, "typeName", "CalendarSystemBase");
    var a = class extends i {
      constructor(t2 = "en") {
        super(), this.firstDayOfWeek = 6, this.locale = t2, this.intlCalendar = "gregory", this.firstMonthNameEnglish = "January", this.monthNamesLocalized = s(t2, "gregory", "January");
      }
      convertToJulian(t2, e2, r2) {
        return CalendarUtils.gregorian_to_jd(t2, e2 + 1, r2);
      }
      convertFromGregorian(t2) {
        return t2 = this.validateDate(t2);
      }
      convertToGregorian(t2, e2, r2) {
        return { year: t2, month: e2, day: r2 };
      }
      isLeapYear(t2 = null) {
        return null === t2 && (t2 = this.$y), t2 % 4 == 0 && !(t2 % 100 == 0 && t2 % 400 != 0);
      }
      monthNames(t2 = "en", e2 = "gregory", r2 = "January") {
        return s(t2, e2, r2);
      }
    };
    var h = {};
    module2.exports = (t2, e2, r2) => {
      let n2 = "gregory";
      const o2 = e2.prototype.$utils(), s2 = function(t3, e3) {
        let n3 = {};
        e3 && (n3 = Object.keys(e3).reduce((t4, r3) => ("$L" === r3 || "locale" === r3 ? t4.locale = e3[r3] : "$u" === r3 || "utc" === r3 ? t4.utc = e3[r3] : "$offset" === r3 ? t4.$offset = e3[r3] : "$x" === r3 ? t4.x = e3[r3] : "$d" === r3 || e3[r3], t4), {}));
        const o3 = r2(t3, n3);
        if (e3 && "$C" in e3 && "gregory" !== e3.$C) {
          return o3.toCalendarSystem(e3.$C);
        }
        return o3;
      };
      r2.registerCalendarSystem = (t3, r3) => {
        if ("CalendarSystemBase" !== r3.constructor.typeName)
          throw new Error("Calendar system must extend CalendarSystemBase");
        if (h[t3] = r3, "function" == typeof r3.daysInMonth) {
          const t4 = e2.prototype.daysInMonth;
          e2.prototype.daysInMonth = function() {
            if (this.$C && "gregory" !== this.$C) {
              const t5 = h[this.$C];
              if (t5 && "function" == typeof t5.daysInMonth)
                return t5.daysInMonth(this.$y, this.$M);
            }
            return t4.call(this);
          };
        }
        if ("function" == typeof r3.startOf) {
          const t4 = e2.prototype.startOf;
          e2.prototype.startOf = function(e3) {
            if (this.$C && "gregory" !== this.$C) {
              const t5 = h[this.$C];
              if (t5 && "function" == typeof t5.startOf)
                return t5.startOf(this.$y, this.$M, this.$D, e3);
            }
            return t4.call(this, e3);
          };
        }
        if ("function" == typeof r3.endOf) {
          const t4 = e2.prototype.endOf;
          e2.prototype.endOf = function(e3) {
            if (this.$C && "gregory" !== this.$C) {
              const t5 = h[this.$C];
              if (t5 && "function" == typeof t5.endOf)
                return t5.endOf(this.$y, this.$M, this.$D, e3);
            }
            return t4.call(this, e3);
          };
        }
        if ("function" == typeof r3.isLeapYear) {
          const t4 = e2.prototype.isLeapYear;
          e2.prototype.isLeapYear = function() {
            if (this.$C && "gregory" !== this.$C) {
              const t5 = h[this.$C];
              if (t5 && "function" == typeof t5.isLeapYear)
                return t5.isLeapYear(this.$y);
            }
            return t4.call(this);
          };
        }
      }, r2.getRegisteredCalendarSystem = (t3) => {
        if (!h[t3])
          throw new Error(`Calendar system '${t3}' is not registered.`);
        return h[t3];
      };
      const i2 = e2.prototype.init;
      e2.prototype.init = function(t3) {
        i2.bind(this)(t3), this.$C && "gregory" !== this.$C && this.toCalendarSystem(this.$C);
      }, e2.prototype.clone = function() {
        return s2(this.$d, this);
      }, e2.prototype.startOf = function(t3, e3) {
        const r3 = !!o2.u(e3) || e3, n3 = o2.p(t3), i3 = (t4, e4, n4 = this.$y) => {
          if ("$C" in this && "gregory" !== this.$C) {
            let r4;
            try {
              r4 = h[this.$C].convertToGregorian(n4, e4, t4), n4 = r4.year, e4 = r4.month, t4 = r4.day;
            } catch (t5) {
              console.log("Error calling convertToGregorian", t5);
            }
          }
          const o3 = s2(this.$u ? Date.UTC(n4, e4, t4) : new Date(n4, e4, t4), this);
          return r3 ? o3 : o3.endOf("day");
        }, a2 = (t4, e4) => s2(this.toDate()[t4].apply(this.$C && "gregory" !== this.$C ? this.$d : this.toDate("s"), (r3 ? [0, 0, 0, 0] : [23, 59, 59, 999]).slice(e4)), this), { $W: c2, $M: y2, $D: u2 } = this, $2 = "set" + (this.$u ? "UTC" : "");
        var l3 = 12;
        switch ("hebrew" == this.$C && (l3 = this.isLeapYear() ? 13 : 12), "ethiopic" == this.$C && (l3 = 13), n3) {
          case "year":
            return r3 ? i3(1, 0) : i3(0, 0, this.$y + 1);
          case "month":
            return r3 ? i3(1, this.$M) : i3(0, (this.$M + 1) % l3, this.$y + parseInt((this.$M + 1) / l3, 10));
          case "week": {
            const t4 = this.$locale().weekStart || 0, e4 = (c2 < t4 ? c2 + 7 : c2) - t4;
            return i3(r3 ? u2 - e4 : u2 + (6 - e4), y2);
          }
          case "day":
          case "date":
            return a2(`${$2}Hours`, 0);
          case "hour":
            return a2(`${$2}Minutes`, 1);
          case "minute":
            return a2(`${$2}Seconds`, 2);
          case "second":
            return a2(`${$2}Milliseconds`, 3);
          default:
            return this.clone();
        }
      };
      const c = e2.prototype.$set;
      e2.prototype.$set = function(t3, e3) {
        if (!("$C" in this) || "gregory" === this.$C)
          return c.call(this, t3, e3);
        const { $d: r3, $u: n3, $C: o3, $y: s3, $M: i3, $D: a2, $H: y2, $m: u2, $s: $2, $ms: l3 } = this, d2 = n3 ? "UTC" : "", m2 = (t4, e4) => r3[`set${d2}${t4}`](e4), f = (t4, e4, r4 = s3, n4 = y2, i4 = u2, a3 = $2, c2 = l3) => {
          const { year: d3, month: f2, day: p2 } = h[o3].convertToGregorian(r4, e4, t4, n4, i4, a3, c2);
          return m2("FullYear", d3), m2("Month", f2), m2("Date", p2), m2("Hours", n4), m2("Minutes", i4), m2("Seconds", a3), m2("Milliseconds", c2), this;
        }, p = { date: () => f(e3, i3), day: () => f(e3, i3), month: () => f(a2, e3), year: () => f(a2, i3, e3), hour: () => f(a2, i3, s3, e3), minute: () => f(a2, i3, s3, y2, e3), second: () => f(a2, i3, s3, y2, u2, e3), millisecond: () => f(a2, i3, s3, y2, u2, $2, e3) };
        return t3 in p ? (p[t3](), this.init(), "gregory" !== o3 ? this.toCalendarSystem(o3) : this) : c.call(this, t3, e3);
      };
      const y = e2.prototype.add;
      e2.prototype.add = function(t3, e3) {
        t3 = Number(t3);
        const n3 = o2.p(e3);
        if ("$C" in this && "gregory" === this.$C || !("$C" in this))
          return y.bind(this)(t3, e3);
        const i3 = (e4) => {
          const n4 = h[this.$C].convertToGregorian(this.$y, this.$M, this.$D, this.$H, this.$m, this.$s, this.$ms), o3 = r2(n4.year + "-" + (n4.month + 1) + "-" + n4.day);
          return s2(o3.date(o3.date() + Math.round(e4 * t3)), this);
        };
        var a2 = 12;
        if ("hebrew" == this.$C && (a2 = this.isLeapYear() ? 13 : 12), "ethiopic" == this.$C && (a2 = 13), "month" === n3) {
          const e4 = this.$M + t3, r3 = e4 < 0 ? -Math.ceil(-e4 / a2) : parseInt(e4 / a2, 10), n4 = this.$D, o3 = this.set("day", 1).add(r3, "year").set("month", e4 - r3 * a2);
          return o3.set("day", Math.min(o3.daysInMonth(), n4));
        }
        if ("year" === n3)
          return this.set("year", this.$y + t3);
        if ("day" === n3)
          return i3(1);
        if ("week" === n3)
          return i3(7);
        const c2 = { minute: 6e4, hour: 36e5, second: 1e3 }[n3] || 1, u2 = this.$d.getTime() + t3 * c2;
        return s2(u2, this);
      };
      const u = e2.prototype.date;
      e2.prototype.date = function(t3) {
        return "$C" in this && "gregory" !== this.$C ? this.$g(t3, "$D", "day") : u.bind(this)(t3);
      }, r2.toCalendarSystem = function(t3) {
        if (!h[t3])
          throw new Error(`Calendar system '${t3}' is not registered.`);
        return r2.$C = t3, r2;
      }, e2.prototype.toCalendarSystem = function(t3 = n2) {
        if (!h[t3])
          throw new Error(`Calendar system '${t3}' is not registered.`);
        const e3 = this.clone();
        if (e3.$C !== t3) {
          if ("gregory" === t3) {
            const t4 = h[e3.$C || "gregory"].convertToGregorian(e3.$y, e3.$M, e3.$D, e3.$H, e3.$m, e3.$s, e3.$ms);
            e3.$G_y = t4.year, e3.$G_M = t4.month, e3.$G_D = t4.day, e3.$C_y = t4.year, e3.$C_M = t4.month, e3.$C_D = t4.day, e3.$y = t4.year, e3.$M = t4.month, e3.$D = t4.day;
          } else {
            const r3 = h[t3].convertFromGregorian(this.toDate());
            e3.$G_y = e3.get("year"), e3.$G_M = e3.get("month"), e3.$G_D = e3.get("date"), e3.$C_y = r3.year, e3.$C_M = r3.month, e3.$C_D = r3.day, e3.$y = r3.year, e3.$M = r3.month, e3.$D = r3.day;
          }
          e3.$C = t3;
        }
        return e3.$calendarLocale = h[t3].localeOverride(e3.$L), e3;
      };
      const $ = e2.prototype.$locale;
      function l2(t3, e3, n3) {
        return "string" != typeof t3 || !["ar", "ar-dz", "ar-iq", "ar-kw", "ar-ly", "ar-ma", "ar-sa", "ar-tn", "fa", "he"].includes(t3) && "amazigh" != n3 ? e3 : r2.updateLocale(t3, h[n3].localeOverride(t3));
      }
      e2.prototype.$locale = function() {
        const t3 = $.call(this);
        return this.$calendarLocale ? { ...t3, ...this.$calendarLocale } : t3;
      };
      const d = e2.prototype.locale;
      e2.prototype.locale = function(t3, e3) {
        e3 = l2(t3, e3, this.$C || "gregory");
        const r3 = d.bind(this)(t3, e3);
        return this.$C && "gregory" !== this.$C && h[this.$C] && (r3.$calendarLocale = h[this.$C].localeOverride(r3.$L)), r3;
      };
      const m = r2.locale;
      r2.locale = function(t3, e3, r3) {
        return e3 = l2(t3, e3, n2), m.bind(this)(t3, e3, r3);
      }, r2.toCalendarSystem.setDefault = function(t3) {
        n2 = t3;
      }, r2.updateLocale = function(t3, e3) {
        const n3 = r2.Ls[t3];
        if (!n3)
          return;
        return (e3 ? Object.keys(e3) : []).forEach((t4) => {
          n3[t4] = e3[t4];
        }), n3;
      }, r2.fromCalendarSystem = (t3, e3, n3, o3, s3 = 0, i3 = 0, a2 = 0, c2 = 0) => {
        if (!h[t3])
          throw new Error(`Calendar system '${t3}' is not registered.`);
        const y2 = h[t3].convertToGregorian(e3, n3, o3, s3, i3, a2, c2);
        return r2(y2.year + "-" + y2.month + "-" + y2.day);
      }, r2.registerCalendarSystem("gregory", new a());
    };
  }
});

// node_modules/@calidy/dayjs-calendarsystems/calendarSystems/CalendarSystemBase.js
var require_CalendarSystemBase = __commonJS({
  "node_modules/@calidy/dayjs-calendarsystems/calendarSystems/CalendarSystemBase.js"(exports2, module2) {
    !function(e, t) {
      "object" == typeof exports2 && "undefined" != typeof module2 ? module2.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = "undefined" != typeof globalThis ? globalThis : e || self).dayjs_calendarsystems_CalendarSystemBase = t();
    }(exports2, function() {
      "use strict";
      var _a;
      const e = {}, t = {};
      function n(t2, n2 = "persian") {
        const r2 = `${t2}-${n2}`;
        if (!e[r2]) {
          const s2 = [];
          for (let e2 = 0; e2 < 12; e2++)
            if ("amazigh" === n2)
              s2.push(o(e2, t2));
            else {
              const r3 = new Date(2023, e2, 1), o2 = new Intl.DateTimeFormat(`${t2}-u-ca-${n2}`, { month: "long" });
              s2.push(o2.format(r3));
            }
          e[r2] = s2;
        }
        return e[r2];
      }
      const r = { tzm: ["\u2D62\u2D3B\u2D4F\u2D4F\u2D30\u2D62\u2D3B\u2D54", "\u2D3C\u2D53\u2D54\u2D30\u2D54", "\u2D4E\u2D3B\u2D56\u2D54\u2D3B\u2D59", "\u2D62\u2D3B\u2D31\u2D54\u2D49\u2D54", "\u2D4E\u2D30\u2D62\u2D62\u2D53", "\u2D62\u2D53\u2D4F\u2D62\u2D53", "\u2D62\u2D53\u2D4D\u2D62\u2D53\u2D63", "\u2D56\u2D53\u2D5B\u2D5C", "\u2D59\u2D53\u2D5C\u2D3B\u2D4F\u2D31\u2D49\u2D54", "\u2D3D\u2D5C\u2D53\u2D31\u2D54", "\u2D4F\u2D53\u2D4F\u2D3B\u2D4E\u2D31\u2D49\u2D54", "\u2D37\u2D53\u2D4A\u2D3B\u2D4E\u2D31\u2D49\u2D54"], ar: ["\u064A\u0646\u0627\u064A\u0631", "\u0641\u0628\u0631\u0627\u064A\u0631", "\u0645\u0627\u0631\u0633", "\u0623\u0628\u0631\u064A\u0644", "\u0645\u0627\u064A\u0648", "\u064A\u0648\u0646\u064A\u0648", "\u064A\u0648\u0644\u064A\u0648", "\u0623\u063A\u0633\u0637\u0633", "\u0633\u0628\u062A\u0645\u0628\u0631", "\u0623\u0643\u062A\u0648\u0628\u0631", "\u0646\u0648\u0641\u0645\u0628\u0631", "\u062F\u064A\u0633\u0645\u0628\u0631"], default: ["Yennayer", "Furar", "Meghres", "Yebrir", "Mayyu", "Yunyu", "Yulyu", "Ghuct", "Cutenber", "Ktuber", "Nunember", "Dujember"] };
      function o(e2, t2) {
        return r[t2] ? r[t2][e2] : r.default[e2];
      }
      function s(e2, r2 = "persian", o2 = "Farvardin") {
        return function(e3, r3, o3 = "persian") {
          const s2 = `${e3}-${r3}-${o3}`;
          if (!t[s2]) {
            let a = n(e3, o3);
            a = [...a.slice(r3), ...a.slice(0, r3)], t[s2] = a;
          }
          return t[s2];
        }(e2, n("en", r2).indexOf(o2), r2);
      }
      return _a = class {
        constructor(e2 = "en") {
          this.locale = e2, this.intlCalendar = "gregory", this.firstMonthNameEnglish = "January", this.monthNamesLocalized = s(e2, "gregory", "January");
        }
        convertFromGregorian(e2) {
          throw new Error("Method convertFromGregorian must be implemented by subclass");
        }
        convertToGregorian(e2) {
          throw new Error("Method convertToGregorian must be implemented by subclass");
        }
        convertFromJulian(e2) {
          throw new Error("Method convertToJulian must be implemented by subclass");
        }
        convertToJulian(e2) {
          throw new Error("Method convertToJulian must be implemented by subclass");
        }
        isLeapYear(e2) {
          throw new Error("Method isLeapYear must be implemented by subclass");
        }
        monthNames(e2, t2, n2) {
          throw new Error("Method monthNames must be implemented by subclass");
        }
        getLocalizedMonthName(e2) {
          const t2 = this.monthNames();
          if (e2 < 0 || e2 >= t2.length)
            throw new Error("Invalid month index.");
          return this.monthNamesLocalized[e2];
        }
        localeOverride(e2) {
          return { gregorianMonths: this.monthNames(e2, "gregory", "January"), months: this.monthNames(e2), monthsShort: this.monthNames(e2).map((e3) => e3.substring(0, 3)) };
        }
        validateDate(e2) {
          if (null == e2)
            e2 = /* @__PURE__ */ new Date();
          else if ("string" == typeof e2)
            e2 = new Date(e2);
          else if ("number" == typeof e2)
            e2 = new Date(e2);
          else if (e2 instanceof Date)
            ;
          else if (void 0 !== e2.$y && void 0 !== e2.$M && void 0 !== e2.$D)
            e2 = new Date(e2.$y, e2.$M, e2.$D, e2.$H, e2.$m, e2.$s, e2.$ms);
          else {
            if (void 0 === e2.year || void 0 === e2.month || void 0 === e2.day)
              throw new Error("Invalid date");
            e2 = new Date(e2.year, e2.month, e2.day);
          }
          return e2;
        }
      }, __publicField(_a, "typeName", "CalendarSystemBase"), _a;
    });
  }
});

// node_modules/@calidy/dayjs-calendarsystems/calendarUtils/IntlUtils.js
var require_IntlUtils = __commonJS({
  "node_modules/@calidy/dayjs-calendarsystems/calendarUtils/IntlUtils.js"(exports2, module2) {
    !function(e, n) {
      "object" == typeof exports2 && "undefined" != typeof module2 ? n(exports2) : "function" == typeof define && define.amd ? define(["exports"], n) : n((e = "undefined" != typeof globalThis ? globalThis : e || self).dayjs_calendarsystems_calendarutils_IntlUtils = {});
    }(exports2, function(e) {
      "use strict";
      const n = {}, t = {}, r = {};
      function o(e2, n2 = "persian") {
        const r2 = `${e2}-${n2}`;
        if (!t[r2]) {
          const o2 = [];
          for (let t2 = 0; t2 < 12; t2++)
            if ("amazigh" === n2)
              o2.push(i(t2, e2));
            else {
              const r3 = new Date(2023, t2, 1), a2 = new Intl.DateTimeFormat(`${e2}-u-ca-${n2}`, { month: "long" });
              o2.push(a2.format(r3));
            }
          t[r2] = o2;
        }
        return t[r2];
      }
      const a = { tzm: ["\u2D62\u2D3B\u2D4F\u2D4F\u2D30\u2D62\u2D3B\u2D54", "\u2D3C\u2D53\u2D54\u2D30\u2D54", "\u2D4E\u2D3B\u2D56\u2D54\u2D3B\u2D59", "\u2D62\u2D3B\u2D31\u2D54\u2D49\u2D54", "\u2D4E\u2D30\u2D62\u2D62\u2D53", "\u2D62\u2D53\u2D4F\u2D62\u2D53", "\u2D62\u2D53\u2D4D\u2D62\u2D53\u2D63", "\u2D56\u2D53\u2D5B\u2D5C", "\u2D59\u2D53\u2D5C\u2D3B\u2D4F\u2D31\u2D49\u2D54", "\u2D3D\u2D5C\u2D53\u2D31\u2D54", "\u2D4F\u2D53\u2D4F\u2D3B\u2D4E\u2D31\u2D49\u2D54", "\u2D37\u2D53\u2D4A\u2D3B\u2D4E\u2D31\u2D49\u2D54"], ar: ["\u064A\u0646\u0627\u064A\u0631", "\u0641\u0628\u0631\u0627\u064A\u0631", "\u0645\u0627\u0631\u0633", "\u0623\u0628\u0631\u064A\u0644", "\u0645\u0627\u064A\u0648", "\u064A\u0648\u0646\u064A\u0648", "\u064A\u0648\u0644\u064A\u0648", "\u0623\u063A\u0633\u0637\u0633", "\u0633\u0628\u062A\u0645\u0628\u0631", "\u0623\u0643\u062A\u0648\u0628\u0631", "\u0646\u0648\u0641\u0645\u0628\u0631", "\u062F\u064A\u0633\u0645\u0628\u0631"], default: ["Yennayer", "Furar", "Meghres", "Yebrir", "Mayyu", "Yunyu", "Yulyu", "Ghuct", "Cutenber", "Ktuber", "Nunember", "Dujember"] };
      function i(e2, n2) {
        return a[n2] ? a[n2][e2] : a.default[e2];
      }
      e.generateMonthNames = function(e2, n2 = "persian", t2 = "Farvardin") {
        return function(e3, n3, t3 = "persian") {
          const a2 = `${e3}-${n3}-${t3}`;
          if (!r[a2]) {
            let i2 = o(e3, t3);
            i2 = [...i2.slice(n3), ...i2.slice(0, n3)], r[a2] = i2;
          }
          return r[a2];
        }(e2, o("en", n2).indexOf(t2), n2);
      }, e.getLocalizedMonthName = function(e2, t2 = "en") {
        return n[t2] || (n[t2] = new Intl.DateTimeFormat(t2, { month: "long" })), n[t2].format(new Date(2023, e2));
      };
    });
  }
});

// test_tz.mjs
var import_dayjs = __toESM(require_dayjs_min(), 1);
var import_customParseFormat = __toESM(require_customParseFormat(), 1);
var import_localeData = __toESM(require_localeData(), 1);
var import_timezone = __toESM(require_timezone(), 1);
var import_utc = __toESM(require_utc(), 1);
var import_dayjs_calendarsystems = __toESM(require_dayjs_calendarsystems_cjs_min(), 1);
var import_CalendarSystemBase = __toESM(require_CalendarSystemBase(), 1);
var import_IntlUtils = __toESM(require_IntlUtils(), 1);
import_dayjs.default.extend(import_customParseFormat.default);
import_dayjs.default.extend(import_localeData.default);
import_dayjs.default.extend(import_timezone.default);
import_dayjs.default.extend(import_utc.default);
import_dayjs.default.extend(import_dayjs_calendarsystems.default);
function Ne(t) {
  var a = Math.floor(t / 100) - Math.floor(t / 400) - 4;
  return (t - 1) % 4 === 3 ? a + 1 : a;
}
function toGregorian(t) {
  var a = t.constructor === Array ? t : [].slice.call(arguments);
  var i = a[0], f = a[1], m = a[2], $ = Ne(i), v = i + 7, l2 = [0, 30, 31, 30, 31, 31, 28, 31, 30, 31, 30, 31, 31, 30], o = v + 1;
  (o % 4 === 0 && o % 100 !== 0 || o % 400 === 0) && (l2[6] = 29);
  var r = (f - 1) * 30 + m;
  r <= 37 && i <= 1575 ? (r += 28, l2[0] = 31) : r += $ - 1, i - 1 % 4 === 3 && (r += 1);
  for (var s = 0, e = void 0, n = 0; n < l2.length; n++)
    if (r <= l2[n]) {
      s = n, e = r;
      break;
    } else
      s = n, r -= l2[n];
  s > 4 && (v += 1);
  var c = [8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9];
  return l2 = c[s], [v, l2, e];
}
function toEthiopian(t) {
  var a = t.constructor === Array ? t : [].slice.call(arguments);
  var i = a[0], f = a[1], m = a[2];
  var $ = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31], v = [0, 30, 30, 30, 30, 30, 30, 30, 30, 30, 5, 30, 30, 30, 30];
  (i % 4 === 0 && i % 100 !== 0 || i % 400 === 0) && ($[2] = 29);
  var l2 = i - 8;
  l2 % 4 === 3 && (v[10] = 6);
  for (var o = Ne(i - 8), r = 0, s = 1; s < f; s++)
    r += $[s];
  r += m;
  var e = l2 % 4 === 0 ? 26 : 25;
  i < 1582 || r <= 277 && i === 1582 ? (v[1] = 0, v[2] = e) : (e = o - 3, v[1] = e);
  for (var n = 1, c = void 0; n < v.length; n++)
    if (r <= v[n]) {
      c = n === 1 || v[n] === 0 ? r + (30 - e) : r;
      break;
    } else
      r -= v[n];
  n > 10 && (l2 += 1);
  var D = [0, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 1, 2, 3, 4], S = D[n];
  return [l2, S, c];
}
var EthiopicCalendarSystem = class extends import_CalendarSystemBase.default {
  constructor(t = "en") {
    super(t);
    this.firstDayOfWeek = 0;
    this.locale = t;
    this.intlCalendar = "ethiopic";
    this.firstMonthNameEnglish = "Meskerem";
  }
  monthNames(locale = "en", cal = "ethiopic", first = "Meskerem") {
    return [];
  }
  convertFromGregorian(t) {
    let a = t.getFullYear(), u = t.getMonth() + 1, i = t.getDate(), f = toEthiopian([a, u, i]);
    return { year: f[0], month: f[1] - 1, day: f[2] };
  }
  convertToGregorian(t, a, u) {
    let i = toGregorian([t, a + 1, u]);
    return { year: i[0], month: i[1] - 1, day: i[2] };
  }
  daysInMonth(t = null, a = null) {
    a === null && (a = this.$M);
    return a >= 12 ? this.isLeapYear(t) ? 6 : 5 : 30;
  }
  isLeapYear(year = null) {
    if (year === null)
      year = this.$y;
    return (year + 1) % 4 === 0;
  }
};
import_dayjs.default.registerCalendarSystem("ethiopic", new EthiopicCalendarSystem());
var l = import_dayjs.default.tz.guess();
function buildEthiopicDate(year, month, day) {
  let greg = toGregorian([year, month + 1, day]);
  return (0, import_dayjs.default)(new Date(greg[0], greg[1] - 1, greg[2])).tz(l, true).toCalendarSystem("ethiopic");
}
var date = buildEthiopicDate(2018, 7, 13);
var firstDay = date.date(1).day();
console.log(`t=1 Y=2018 M=7 (Miyazya): firstDay=${firstDay}`);
var dateNow = (0, import_dayjs.default)().tz(l).toCalendarSystem("ethiopic");
console.log(`With dayjs().tz(l): Y=${dateNow.year()} M=${dateNow.month()} firstDay=${dateNow.date(1).day()}`);
/*! Bundled license information:

@calidy/dayjs-calendarsystems/dayjs-calendarsystems.cjs.min.js:
  (**
   * @fileoverview This file contains utility functions for working with the Intl API.
   * @license see LICENSE file included in the project
   * @author Calidy.com, Amir Moradi (https://calidy.com/)
   * @description see README.md file included in the project
   *
   *)
  (**
   * Calendar System Base Class.
   * @file CalendarSystemBase.js
   * @project dayjs-calendarsystems
   * @license see LICENSE file included in the project
   * @author Calidy.com, Amir Moradi (https://calidy.com/)
   * @description see README.md file included in the project
   *
   *)
  (**
   * Gregorian Calendar System
   *
   * @file GregoryCalendarSystem.js
   * @project dayjs-calendarsystems
   * @license see LICENSE file included in the project
   * @author Calidy.com, Amir Moradi (https://calidy.com/)
   * @description see README.md file included in the project
   *
   *)
  (**
   * Day.js calendar systems plugin
   * @license see LICENSE file included in the project
   * @author Calidy.com, Amir Moradi (https://calidy.com/)
   * @description see README.md file included in the project
   *
   * This plugin allows Day.js to work with different calendar systems.
   *)

@calidy/dayjs-calendarsystems/calendarSystems/CalendarSystemBase.js:
  (**
   * @fileoverview This file contains utility functions for working with the Intl API.
   * @license see LICENSE file included in the project
   * @author Calidy.com, Amir Moradi (https://calidy.com/)
   * @description see README.md file included in the project
   *
   *)
  (**
   * Calendar System Base Class.
   * @file CalendarSystemBase.js
   * @project dayjs-calendarsystems
   * @license see LICENSE file included in the project
   * @author Calidy.com, Amir Moradi (https://calidy.com/)
   * @description see README.md file included in the project
   *
   *)

@calidy/dayjs-calendarsystems/calendarUtils/IntlUtils.js:
  (**
   * @fileoverview This file contains utility functions for working with the Intl API.
   * @license see LICENSE file included in the project
   * @author Calidy.com, Amir Moradi (https://calidy.com/)
   * @description see README.md file included in the project
   *
   *)
*/
