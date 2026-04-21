// Test script to reproduce the Pagume month selection bug

function Ne(t){var a=Math.floor(t/100)-Math.floor(t/400)-4;return(t-1)%4===3?a+1:a}

function toGregorian(t){
  var a=t.constructor===Array?t:[].slice.call(arguments);
  var i=a[0],f=a[1],m=a[2],$=Ne(i),v=i+7,l=[0,30,31,30,31,31,28,31,30,31,30,31,31,30],o=v+1;
  (o%4===0&&o%100!==0||o%400===0)&&(l[6]=29);
  var r=(f-1)*30+m;r<=37&&i<=1575?(r+=28,l[0]=31):r+=$-1;

  // BUG CHECK: operator precedence issue
  // Original code: i-1%4===3&&(r+=1)
  // JS parses: i - (1%4) === 3  → i - 1 === 3 → only true when i=4
  // Intended: (i-1)%4 === 3
  console.log("  [toGregorian] i=" + i + " f=" + f + " m=" + m);
  console.log("    i-1%4 =", i-1%4, "(should be (i-1)%4 =", (i-1)%4, ")");
  i-1%4===3&&(r+=1);

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

// The conversion function used by dayjs calendarSystem
function convertToGregorian(t, a, u) {
    // t=year, a=0-indexed month, u=day
    let i = toGregorian([t, a + 1, u]);
    return { year: i[0], month: i[1] - 1, day: i[2] };
}

function convertFromGregorian(year, month1, day) {
    let f = toEthiopian([year, month1, day]);
    return { year: f[0], month: f[1] - 1, day: f[2] };
}

console.log("=== Test: convertToGregorian for Pagume 5, 2018 ===");
// Pagume = month 13 (1-indexed) = month 12 (0-indexed)
let result = convertToGregorian(2018, 12, 5);
console.log("Pagume 5, 2018 → Gregorian:", result);

console.log("\n=== Test: Round-trip ===");
let ethResult = convertFromGregorian(result.year, result.month + 1, result.day);
console.log("Back to Ethiopic:", ethResult);
console.log("Expected: year=2018, month=12, day=5");

console.log("\n=== Test: convertToGregorian for Miyazya (month 7 0-indexed) day 8, year 2018 ===");
let miyazya = convertToGregorian(2018, 7, 8);
console.log("Miyazya 8, 2018 → Gregorian:", miyazya);

console.log("\n=== Simulating $set for month ===");
console.log("Starting from Miyazya 8, 2018 (month=7, day=8)");
console.log("User selects Pagume (month=12)");

// The $watch handler logic:
let focusedYear = 2018;
let focusedMonth_current = 7;
let focusedDay = 8;
let targetMonth = 12;

let maxDays = targetMonth === 12 ? ((focusedYear + 1) % 4 === 0 ? 6 : 5) : 30;
let clampedDay = Math.min(focusedDay, maxDays);
console.log("maxDays for Pagume:", maxDays, "clampedDay:", clampedDay);

// What $set('day', clampedDay) does: convertToGregorian(year, currentMonth, newDay)
console.log("\nStep 1: .date(clampedDay) → convertToGregorian(" + focusedYear + ", " + focusedMonth_current + ", " + clampedDay + ")");
let dateResult = convertToGregorian(focusedYear, focusedMonth_current, clampedDay);
console.log("  → Gregorian:", dateResult);

// The internal JS Date is now set to dateResult. After init() and toCalendarSystem:
let backToEth = convertFromGregorian(dateResult.year, dateResult.month + 1, dateResult.day);
console.log("  → Back to Ethiopian:", backToEth);

// Step 2: .month(12) → convertToGregorian(year, newMonth, currentDay)
// After step 1, $D should be backToEth.day, $y should be backToEth.year
console.log("\nStep 2: .month(12) → convertToGregorian(" + backToEth.year + ", " + targetMonth + ", " + backToEth.day + ")");
let monthResult = convertToGregorian(backToEth.year, targetMonth, backToEth.day);
console.log("  → Gregorian:", monthResult);

let finalEth = convertFromGregorian(monthResult.year, monthResult.month + 1, monthResult.day);
console.log("  → Final Ethiopian:", finalEth);
console.log("  Expected: year=2018, month=12, day=" + clampedDay);

// Now test with large day values to check overflow
console.log("\n=== OVERFLOW TEST: What if day is NOT clamped? ===");
console.log("convertToGregorian(2018, 12, 8) - Pagume day 8 (doesn't exist!):");
let overflow = convertToGregorian(2018, 12, 8);
console.log("  → Gregorian:", overflow);
let overflowEth = convertFromGregorian(overflow.year, overflow.month + 1, overflow.day);
console.log("  → Ethiopian:", overflowEth);

console.log("\n=== OVERFLOW TEST 2: Pagume day 30 ===");
let overflow2 = convertToGregorian(2018, 12, 30);
console.log("  → Gregorian:", overflow2);
let overflowEth2 = convertFromGregorian(overflow2.year, overflow2.month + 1, overflow2.day);
console.log("  → Ethiopian:", overflowEth2);

// Check: what if the issue is in how dayjs $set chains work
// When setting .month(12) on a date in month 7, before .date(clampedDay) is processed:
console.log("\n=== CRITICAL TEST: What if month is set BEFORE day is clamped? ===");
console.log("convertToGregorian(2018, 12, 8) - setting month=12 with day still at 8");
let directMonthSet = convertToGregorian(2018, 12, 8);
console.log("  → Gregorian:", directMonthSet);
let directMonthEth = convertFromGregorian(directMonthSet.year, directMonthSet.month + 1, directMonthSet.day);
console.log("  → Ethiopian:", directMonthEth);
console.log("  THIS IS THE BUG if day overflows!");
