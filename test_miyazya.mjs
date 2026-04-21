import dayjs from "dayjs";
import customParseFormat from "dayjs/plugin/customParseFormat.js";
import localeData from "dayjs/plugin/localeData.js";
import timezone from "dayjs/plugin/timezone.js";
import utc from "dayjs/plugin/utc.js";
import calendarSystems from "@calidy/dayjs-calendarsystems";
import CalendarSystemBase from "@calidy/dayjs-calendarsystems/calendarSystems/CalendarSystemBase.js";
import { generateMonthNames } from "@calidy/dayjs-calendarsystems/calendarUtils/IntlUtils.js";

dayjs.extend(customParseFormat);
dayjs.extend(localeData);
dayjs.extend(timezone);
dayjs.extend(utc);
dayjs.extend(calendarSystems);

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
  constructor(t="en") {
    super(t); this.firstDayOfWeek=0; this.locale=t;
    this.intlCalendar="ethiopic"; this.firstMonthNameEnglish="Meskerem";
  }
  monthNames(locale="en", cal="ethiopic", first="Meskerem") { return []; }
  convertFromGregorian(t) {
    let a=t.getFullYear(),u=t.getMonth()+1,i=t.getDate(),f=toEthiopian([a,u,i]);
    return {year:f[0],month:f[1]-1,day:f[2]};
  }
  convertToGregorian(t,a,u) {
    let i=toGregorian([t,a+1,u]);
    return {year:i[0],month:i[1]-1,day:i[2]};
  }
  daysInMonth(t=null,a=null) {
    a===null&&(a=this.$M);
    return a>=12?(this.isLeapYear(t)?6:5):30;
  }
  isLeapYear(year=null) { if(year===null) year=this.$y; return (year+1)%4===0; }
}

dayjs.registerCalendarSystem("ethiopic", new EthiopicCalendarSystem());

function buildEthiopicDate(year, month, day) {
  let greg = toGregorian([year, month + 1, day]);
  return dayjs(new Date(greg[0], greg[1] - 1, greg[2]))
      .toCalendarSystem("ethiopic");
}

function evaluateEmptyDays(year, month, t) {
  let date = buildEthiopicDate(year, month, 1);
  let firstDay = date.date(1).day(); // 0-6 (Sun-Sat)
  let emptyProper = (firstDay - t + 7) % 7;
  
  console.log(`t=${t} Y=${year} M=${month} (Miyazya): Day 1 is ${firstDay}. ` +
    `Proper empty: ${emptyProper}`);
}

evaluateEmptyDays(2018, 7, 1); // Miyazya is month 8 (index 7)
