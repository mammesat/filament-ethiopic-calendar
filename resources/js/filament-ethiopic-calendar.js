import dayjs from "dayjs";
import customParseFormat from "dayjs/plugin/customParseFormat.js";
import localeData from "dayjs/plugin/localeData.js";
import timezone from "dayjs/plugin/timezone.js";
import utc from "dayjs/plugin/utc.js";
import calendarSystems from "@calidy/dayjs-calendarsystems";
import 'dayjs/locale/am.js';
import 'dayjs/locale/en.js';

dayjs.extend(customParseFormat);
dayjs.extend(localeData);
dayjs.extend(timezone);
dayjs.extend(utc);
dayjs.extend(calendarSystems);

// The base ethiopic integration provided by calendarSystems has a defect in Pagume handling.
// We override it here natively so we don't have to rewrite the complex math.
import GregoryCalendarSystem from "@calidy/dayjs-calendarsystems/calendarSystems/GregoryCalendarSystem.js";
import CalendarSystemBase from "@calidy/dayjs-calendarsystems/calendarSystems/CalendarSystemBase.js";
import { generateMonthNames } from "@calidy/dayjs-calendarsystems/calendarUtils/IntlUtils.js";
import { jd_to_gregorian, gregorian_to_jd } from "@calidy/dayjs-calendarsystems/calendarUtils/fourmilabCalendar.js";

function Ne(t){var a=Math.floor(t/100)-Math.floor(t/400)-4;return(t-1)%4===3?a+1:a}
function toGregorian(t){
  var a=t.constructor===Array?t:[].slice.call(arguments);
  var i=a[0],f=a[1],m=a[2],$=Ne(i),v=i+7,l=[0,30,31,30,31,31,28,31,30,31,30,31,31,30],o=v+1;
  (o%4===0&&o%100!==0||o%400===0)&&(l[6]=29);
  var r=(f-1)*30+m;r<=37&&i<=1575?(r+=28,l[0]=31):r+=$-1,i-1%4===3&&(r+=1);
  for(var s=0,e=undefined,n=0;n<l.length;n++)if(r<=l[n]){s=n,e=r;break}else s=n,r-=l[n];
  s>4&&(v+=1);var c=[8,9,10,11,12,1,2,3,4,5,6,7,8,9];return l=c[s],[v,l,e]
}

function toEthiopian(t){
  var a=t.constructor===Array?t:[].slice.call(arguments);
  var i=a[0],f=a[1],m=a[2];
  var $=[0,31,28,31,30,31,30,31,31,30,31,30,31],v=[0,30,30,30,30,30,30,30,30,30,5,30,30,30,30];
  (i%4===0&&i%100!==0||i%400===0)&&($[2]=29);var l=i-8;l%4===3&&(v[10]=6);
  for(var o=Ne(i-8),r=0,s=1;s<f;s++)r+=$[s];r+=m;var e=l%4===0?26:25;
  i<1582||r<=277&&i===1582?(v[1]=0,v[2]=e):(e=o-3,v[1]=e);
  for(var n=1,c=undefined;n<v.length;n++)if(r<=v[n]){c=n===1||v[n]===0?r+(30-e):r;break}else r-=v[n];
  n>10&&(l+=1);var D=[0,4,5,6,7,8,9,10,11,12,13,1,2,3,4],S=D[n];return[l,S,c]
}

class EthiopicCalendarSystem extends CalendarSystemBase {
  constructor(t = "en") {
    super(t);
    this.firstDayOfWeek = 0;
    this.locale = t;
    this.intlCalendar = "ethiopic";
    this.firstMonthNameEnglish = "Meskerem";
    this.monthNamesLocalized = generateMonthNames(t, "ethiopic", this.firstMonthNameEnglish);
  }
  convertFromJulian(t) {
    let a = jd_to_gregorian(t);
    return toEthiopian([a[0], a[1] + 1, a[2]]);
  }
  convertToJulian(t, a, u) {
    let i = toGregorian([t, a + 1, u]);
    return gregorian_to_jd(i[0], i[1] - 1, i[2]);
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
    return a >= 12 ? (this.isLeapYear(t) ? 6 : 5) : 30;
  }
  isLeapYear(year = null) {
      if (year === null) year = this.$y;
      return (year + 1) % 4 === 0;
  }
  monthNames(locale="en", cal="ethiopic", first="Meskerem") {
    return generateMonthNames(locale, cal, first);
  }
}

dayjs.registerCalendarSystem("ethiopic", new EthiopicCalendarSystem());
window.dayjs = dayjs;

export default function filamentEthiopicCalendarComponent({
  displayFormat: h,
  firstDayOfWeek: t,
  isAutofocused: a,
  locale: u,
  shouldCloseOnDateSelection: i,
  state: f,
  months: m,
  dayLabel: $,
  dayShortLabel: v
}) {
  let l = dayjs.tz.guess();
  return {
    daysInFocusedMonth: [],
    displayText: "",
    emptyDaysInFocusedMonth: [],
    buildEthiopicDate: function (year, month, day) {
      // Construct an Ethiopic date by going through Gregorian directly,
      // bypassing the calendarSystems plugin's .month()/.year()/.date()
      // setters which can overflow for Pagume (5-6 day month).
      let greg = toGregorian([year, month + 1, day]);
      console.log('[buildEthiopicDate] ET('+year+','+month+','+day+') → Greg('+greg[0]+','+greg[1]+','+greg[2]+')');
      let result = dayjs(new Date(greg[0], greg[1] - 1, greg[2]))
          .tz(l, true)
          .toCalendarSystem("ethiopic");
      console.log('[buildEthiopicDate] Result: Y='+result.year()+' M='+result.month()+' D='+result.date());
      return result;
    },
    focusedDate: null,
    focusedMonth: null,
    focusedYear: null,
    hour: null,
    isClearingState: !1,
    minute: null,
    second: null,
    state: f,
    dayLabels: [],
    months: m,
    dayLabel: $,
    dayShortLabel: v,
    init: function () {
      dayjs.locale(u || "en");
      this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic");
      let o = this.getSelectedDate() ?? dayjs().tz(l).toCalendarSystem("ethiopic").hour(0).minute(0).second(0);
      (this.getMaxDate() !== null && o.isAfter(this.getMaxDate()) || this.getMinDate() !== null && o.isBefore(this.getMinDate())) && (o = null);
      this.hour = o?.hour() ?? 0;
      this.minute = o?.minute() ?? 0;
      this.second = o?.second() ?? 0;
      this.setDisplayText();
      this.setMonths();
      this.setDayLabels();
      a && this.$nextTick(() => this.togglePanelVisibility(this.$refs.button));
      
      this.$watch("focusedMonth", () => {
        if (!this.focusedDate) return;
        this.focusedMonth = +this.focusedMonth;
        if (this.focusedDate.month() !== this.focusedMonth) {
          // Clamp day to prevent overflow for month 13 (Pagume)
          let targetYear = this.focusedDate.year();
          let maxDays = this.focusedMonth === 12 ? ((targetYear + 1) % 4 === 0 ? 6 : 5) : 30;
          let currentDay = this.focusedDate.date();
          let clampedDay = Math.min(currentDay, maxDays);
          
          this.focusedDate = this.buildEthiopicDate(targetYear, this.focusedMonth, clampedDay);
        }
      });
      
      this.$watch("focusedYear", () => {
        if (!this.focusedDate) return;
        if (this.focusedYear?.length > 4 && (this.focusedYear = String(this.focusedYear).substring(0, 4)), !this.focusedYear || String(this.focusedYear).length !== 4) return;
        let r = +this.focusedYear;
        Number.isInteger(r) || (r = dayjs().tz(l).toCalendarSystem("ethiopic").year(), this.focusedYear = r);
        if (this.focusedDate.year() !== r) {
            // Clamp day on year change in case transitioning out of a leap year Pagume 6
            let maxDays = +this.focusedMonth === 12 ? ((r + 1) % 4 === 0 ? 6 : 5) : 30;
            let currentDay = this.focusedDate.date();
            let clampedDay = Math.min(currentDay, maxDays);
            
            this.focusedDate = this.buildEthiopicDate(r, this.focusedDate.month(), clampedDay);
        }
      });
      
      this.$watch("focusedDate", () => {
        let r = this.focusedDate.month(), s = this.focusedDate.year();
        // Use numeric coercion (+) to prevent type-mismatch cascade:
        // focusedMonth/focusedYear may be strings from HTML inputs,
        // while .month()/.year() return numbers.
        +this.focusedMonth !== r && (this.focusedMonth = r);
        +this.focusedYear !== s && (this.focusedYear = s);
        this.setupDaysGrid();
      });
      
      this.$watch("hour", () => {
        let r = +this.hour;
        if (Number.isInteger(r) ? r > 23 ? this.hour = 0 : r < 0 ? this.hour = 23 : this.hour = r : this.hour = 0, this.isClearingState) return;
        let s = this.getSelectedDate() ?? this.focusedDate;
        this.setState(s.hour(this.hour ?? 0));
      });
      
      this.$watch("minute", () => {
        let r = +this.minute;
        if (Number.isInteger(r) ? r > 59 ? this.minute = 0 : r < 0 ? this.minute = 59 : this.minute = r : this.minute = 0, this.isClearingState) return;
        let s = this.getSelectedDate() ?? this.focusedDate;
        this.setState(s.minute(this.minute ?? 0));
      });
      
      this.$watch("second", () => {
        let r = +this.second;
        if (Number.isInteger(r) ? r > 59 ? this.second = 0 : r < 0 ? this.second = 59 : this.second = r : this.second = 0, this.isClearingState) return;
        let s = this.getSelectedDate() ?? this.focusedDate;
        this.setState(s.second(this.second ?? 0));
      });
      
      this.$watch("state", () => {
        if (this.state === void 0) return;
        let r = this.getSelectedDate();
        if (r === null) {
          this.clearState();
          return;
        }
        this.getMaxDate() !== null && r?.isAfter(this.getMaxDate()) && (r = null);
        this.getMinDate() !== null && r?.isBefore(this.getMinDate()) && (r = null);
        let s = r?.hour() ?? 0;
        this.hour !== s && (this.hour = s);
        let e = r?.minute() ?? 0;
        this.minute !== e && (this.minute = e);
        let n = r?.second() ?? 0;
        this.second !== n && (this.second = n);
        this.setDisplayText();
      });
    },
    clearState: function () {
      this.isClearingState = !0;
      this.setState(null);
      this.hour = 0;
      this.minute = 0;
      this.second = 0;
      this.$nextTick(() => this.isClearingState = !1);
    },
    dateIsDisabled: function (o) {
      return !!(this.$refs?.disabledDates && JSON.parse(this.$refs.disabledDates.value ?? "[]").some(r => (r = dayjs(r), r.isValid() ? r.isSame(o.toCalendarSystem("gregory"), "day") : !1)) || this.getMaxDate() && o.isAfter(this.getMaxDate()) || this.getMinDate() && o.isBefore(this.getMinDate()));
    },
    dayIsDisabled: function (o) {
      return this.focusedDate ?? (this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic")), this.dateIsDisabled(this.focusedDate.date(o));
    },
    dayIsSelected: function (o) {
      let r = this.getSelectedDate();
      return r === null ? !1 : (this.focusedDate ?? (this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic")), r.date() === o && r.month() === this.focusedDate.month() && r.year() === this.focusedDate.year());
    },
    dayIsToday: function (o) {
      let r = dayjs().tz(l).toCalendarSystem("ethiopic");
      return this.focusedDate ?? (this.focusedDate = r), r.date() === o && r.month() === this.focusedDate.month() && r.year() === this.focusedDate.year();
    },
    focusPreviousDay: function () {
      this.focusedDate ?? (this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic"));
      this.focusedDate = this.focusedDate.subtract(1, "day");
    },
    focusPreviousWeek: function () {
      this.focusedDate ?? (this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic"));
      this.focusedDate = this.focusedDate.subtract(1, "week");
    },
    focusNextDay: function () {
      this.focusedDate ?? (this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic"));
      this.focusedDate = this.focusedDate.add(1, "day");
    },
    focusNextWeek: function () {
      this.focusedDate ?? (this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic"));
      this.focusedDate = this.focusedDate.add(1, "week");
    },
    getDayLabels: function () {
      let o = this.$el.dataset.weekdaysShort, r = [];
      return o === "short" ? typeof this.dayShortLabel != "object" ? r = dayjs.weekdaysShort() : r = Object.values(this.dayShortLabel) : typeof this.dayLabel != "object" ? r = dayjs.weekdays() : r = Object.values(this.dayLabel), t === 0 ? r : [...r.slice(t), ...r.slice(0, t)];
    },
    getMaxDate: function () {
      let o = dayjs(this.$refs.maxDate?.value);
      return o.isValid() ? o.toCalendarSystem("ethiopic") : null;
    },
    getMinDate: function () {
      let o = dayjs(this.$refs.minDate?.value);
      return o.isValid() ? o.toCalendarSystem("ethiopic") : null;
    },
    getSelectedDate: function () {
      if (this.state === void 0 || this.state === null) return null;
      let o = dayjs(this.state).toCalendarSystem("ethiopic");
      return o.isValid() ? o : null;
    },
    togglePanelVisibility: function () {
      this.isOpen() || (this.focusedDate = this.getSelectedDate() ?? this.getMinDate() ?? dayjs().tz(l).toCalendarSystem("ethiopic"), this.setupDaysGrid());
      this.$refs.panel.toggle(this.$refs.button);
    },
    selectDate: function (o = null) {
      o && this.setFocusedDay(o);
      this.focusedDate ?? (this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic"));
      this.setState(this.focusedDate);
      i && this.togglePanelVisibility();
    },
    setDisplayText: function () {
      this.displayText = this.getSelectedDate() ? this.getSelectedDate().format(h) : "";
    },
    setMonths: function () {
      (typeof this.months != "object" || !Array.isArray(this.months) || this.months === null) && (this.months = dayjs.months());
    },
    setDayLabels: function () {
      this.dayLabels = this.getDayLabels();
    },
    setupDaysGrid: function () {
      this.focusedDate ?? (this.focusedDate = dayjs().tz(l).toCalendarSystem("ethiopic"));
      
      // Calculate empty leading days using proper modulo arithmetic.
      // Use deterministic Zeller's congruence to completely bypass timezone shift bugs
      // in dayjs().day() when cross-referencing Gregorian and Ethiopian dates.
      let greg = toGregorian([this.focusedDate.year(), this.focusedDate.month() + 1, 1]);
      let gy = greg[0];
      let gm = greg[1]; // toGregorian returns 1-indexed month
      let gd = greg[2];
      
      let q = gd;
      let m = gm < 3 ? gm + 12 : gm;
      let Y = gm < 3 ? gy - 1 : gy;
      let K = Y % 100;
      let J = Math.floor(Y / 100);
      let h = (q + Math.floor((13 * (m + 1)) / 5) + K + Math.floor(K / 4) + Math.floor(J / 4) - 2 * J) % 7;
      
      let firstDay = (h + 6) % 7; // Zeller format to JS format: 0-6 (Sun-Sat)
      
      // t is firstDayOfWeek (0 for Sun, 1 for Mon).
      let emptyDays = (firstDay - t + 7) % 7;
      
      this.emptyDaysInFocusedMonth = Array.from({ length: emptyDays }, (o, r) => r + 1);
      this.daysInFocusedMonth = Array.from({ length: this.focusedDate.daysInMonth() }, (o, r) => r + 1);
    },
    setFocusedDay: function (o) {
      let r = o - this.focusedDate.date();
      this.focusedDate = this.focusedDate.add(r, "day");
    },
    setState: function (o) {
      if (o === null) {
        this.state = null;
        this.setDisplayText();
        return;
      }
      this.dateIsDisabled(o) || (this.state = o.hour(this.hour ?? 0).minute(this.minute ?? 0).second(this.second ?? 0).toCalendarSystem("gregory").format("YYYY-MM-DD HH:mm:ss"), this.setDisplayText());
    },
    isOpen: function () {
      return this.$refs.panel?.style.display === "block";
    }
  };
}

window.filamentEthiopicCalendarComponent = filamentEthiopicCalendarComponent;
