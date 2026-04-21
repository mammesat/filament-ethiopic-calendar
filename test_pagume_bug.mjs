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
    this.monthNamesLocalized=generateMonthNames(t,"ethiopic",this.firstMonthNameEnglish);
  }
  monthNames(locale="en", cal="ethiopic", first="Meskerem") {
    return generateMonthNames(locale, cal, first);
  }
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

// ========== DEEP INVESTIGATION ==========

console.log("=== Understanding the init / month-set interaction ===\n");

// The issue: when focusedDate is opened from the panel for the FIRST time,
// focusedDate gets created, and BOTH focusedMonth and focusedYear watchers
// fire because they're being set for the first time.

// Simulate the full init + watcher sequence for a date that hasn't been selected yet
console.log("--- Simulating init() sequence ---");
let now = dayjs().toCalendarSystem("ethiopic");
console.log("Today (Ethiopian):", `Y=${now.year()} M=${now.month()} D=${now.date()}`);

// When togglePanelVisibility runs, it sets focusedDate
// Then focusedDate watcher fires, setting focusedMonth and focusedYear
// Then focusedMonth watcher fires (because focusedMonth changed from null)

// Scenario: focusedMonth was null, now set to current month
// Then user selects 12 from dropdown
// BUT - what if focusedMonth watcher fires with the INITIAL value first?

console.log("\n--- Testing: What happens with Meskerem (M=0) starting date ---");
// M0 (Meskerem) was the failing case in TEST 2
let meskerem = dayjs.fromCalendarSystem("ethiopic", 2018, 0, 19).toCalendarSystem("ethiopic");
console.log("Meskerem:", `Y=${meskerem.year()} M=${meskerem.month()} D=${meskerem.date()}`);

// Go to Pagume
let cd = Math.min(meskerem.date(), 5);
console.log("clampedDay:", cd);

let step1 = meskerem.date(cd);
console.log("After .date(5):", `Y=${step1.year()} M=${step1.month()} D=${step1.date()}`);
let step2 = step1.month(12);
console.log("After .month(12):", `Y=${step2.year()} M=${step2.month()} D=${step2.date()}`);

// Debug: check what convertToGregorian returns for Meskerem edge cases
console.log("\n--- convertToGregorian debugging ---");
// Month is 0-indexed in our code. convertToGregorian adds 1 before calling toGregorian.
// So convertToGregorian(2018, 0, 5) → toGregorian([2018, 1, 5]) → Meskerem 5
// convertToGregorian(2018, 12, 5) → toGregorian([2018, 13, 5]) → Pagume 5

for (let m = 0; m <= 12; m++) {
  let greg = toGregorian([2018, m+1, 5]);
  let eth = toEthiopian(greg);
  console.log(`  Eth[2018,${m+1},5] → Greg${JSON.stringify(greg)} → Eth${JSON.stringify(eth)}`);
}

// Now test what happens with watcher cascade
console.log("\n=== Simulating EXACT watcher cascade ===");
console.log("Scenario: Panel opens on today's date, user clicks Pagume");

// State when panel opens
let focusedDate = dayjs().toCalendarSystem("ethiopic");
let focusedMonth = null;
let focusedYear = null;

console.log("Panel opens. focusedDate:", `Y=${focusedDate.year()} M=${focusedDate.month()} D=${focusedDate.date()}`);

// focusedDate watcher fires first (assigning focusedDate triggered it)
let r1 = focusedDate.month();
let s1 = focusedDate.year();
console.log("focusedDate watcher: month="+r1+", year="+s1);
focusedMonth = r1;  // e.g., 7 for Miyazya
focusedYear = s1;   // e.g., 2018
console.log("Set focusedMonth=" + focusedMonth + ", focusedYear=" + focusedYear);

// focusedMonth watcher fires (focusedMonth changed from null to 7)
console.log("\nfocusedMonth watcher fires (was null, now " + focusedMonth + "):");
focusedMonth = +focusedMonth;
if (focusedDate.month() !== focusedMonth) {
  console.log("  MISMATCH - would recalculate! month()=" + focusedDate.month() + " vs " + focusedMonth);
} else {
  console.log("  OK - months match, no recalculation needed");
}

// NOW: user selects Pagume (12) from dropdown
console.log("\n--- User selects Pagume (12) from dropdown ---");
focusedMonth = "12";  // HTML select gives string!

// focusedMonth watcher fires
console.log("focusedMonth watcher fires (value=" + JSON.stringify(focusedMonth) + "):");
focusedMonth = +focusedMonth;
console.log("  Coerced to number:", focusedMonth);

if (focusedDate.month() !== focusedMonth) {
  let targetYear2 = focusedDate.year();
  let maxDays2 = focusedMonth === 12 ? ((targetYear2 + 1) % 4 === 0 ? 6 : 5) : 30;
  let currentDay2 = focusedDate.date();
  let clampedDay2 = Math.min(currentDay2, maxDays2);
  console.log(`  targetYear=${targetYear2}, maxDays=${maxDays2}, currentDay=${currentDay2}, clampedDay=${clampedDay2}`);
  
  focusedDate = focusedDate.date(clampedDay2).month(focusedMonth);
  console.log("  New focusedDate:", `Y=${focusedDate.year()} M=${focusedDate.month()} D=${focusedDate.date()}`);
  
  // focusedDate watcher fires
  let r2 = focusedDate.month();
  let s2 = focusedDate.year();
  console.log("  focusedDate watcher: month=" + r2 + ", year=" + s2);
  
  if (focusedMonth !== r2) {
    console.log("  !!! MONTH MISMATCH: focusedMonth=" + focusedMonth + " but date.month()=" + r2);
    focusedMonth = r2;
    console.log("  → Setting focusedMonth to " + focusedMonth + " (triggers focusedMonth watcher AGAIN!)");
    
    // This would trigger focusedMonth watcher AGAIN with the wrong month!
    focusedMonth = +focusedMonth;
    if (focusedDate.month() !== focusedMonth) {
      let targetYear3 = focusedDate.year();
      let maxDays3 = focusedMonth === 12 ? ((targetYear3 + 1) % 4 === 0 ? 6 : 5) : 30;
      let currentDay3 = focusedDate.date();
      let clampedDay3 = Math.min(currentDay3, maxDays3);
      console.log("  2nd focusedMonth trigger: recalculating...");
      focusedDate = focusedDate.date(clampedDay3).month(focusedMonth);
      console.log("  2nd new focusedDate:", `Y=${focusedDate.year()} M=${focusedDate.month()} D=${focusedDate.date()}`);
    }
  }
  
  if (focusedYear !== s2) {
    console.log("  !!! YEAR MISMATCH: focusedYear=" + focusedYear + " but date.year()=" + s2);
    focusedYear = s2;
    console.log("  → Setting focusedYear to " + focusedYear + " (triggers focusedYear watcher!)");
  }
}

console.log("\n=== FINAL STATE ===");
console.log("focusedDate:", `Y=${focusedDate.year()} M=${focusedDate.month()} D=${focusedDate.date()}`);
console.log("focusedMonth:", focusedMonth);
console.log("focusedYear:", focusedYear);
