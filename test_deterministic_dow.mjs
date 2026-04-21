import dayjs from "dayjs";
import customParseFormat from "dayjs/plugin/customParseFormat.js";
import localeData from "dayjs/plugin/localeData.js";
import timezone from "dayjs/plugin/timezone.js";
import utc from "dayjs/plugin/utc.js";

dayjs.extend(customParseFormat);
dayjs.extend(localeData);
dayjs.extend(timezone);
dayjs.extend(utc);

function Ne(t){var a=Math.floor(t/100)-Math.floor(t/400)-4;return(t-1)%4===3?a+1:a}
function toGregorian(t){
  var a=t.constructor===Array?t:[].slice.call(arguments);
  var i=a[0],f=a[1],m=a[2],$=Ne(i),v=i+7,l=[0,30,31,30,31,31,28,31,30,31,30,31,31,30],o=v+1;
  (o%4===0&&o%100!==0||o%400===0)&&(l[6]=29);
  var r=(f-1)*30+m;r<=37&&i<=1575?(r+=28,l[0]=31):r+=$-1,i-1%4===3&&(r+=1);
  for(var s=0,e=undefined,n=0;n<l.length;n++)if(r<=l[n]){s=n,e=r;break}else s=n,r-=l[n];
  s>4&&(v+=1);var c=[8,9,10,11,12,1,2,3,4,5,6,7,8,9];return l=c[s],[v,l,e]
}
function gregorian_to_jd(year, month, day) {
    return (1461 * (year + 4800 + (month - 14) / 12)) / 4 +
        (367 * (month - 2 - 12 * ((month - 14) / 12))) / 12 -
        (3 * ((year + 4900 + (month - 14) / 12) / 100)) / 4 +
        day - 32075;
}

let date = dayjs("2026-04-09");
console.log("Dayjs dow for 2026-04-09: " + date.day() + " (0=Sun, 4=Thu)");

// Let's implement deterministic DOW:
function getDeterministicDow(ethYear, ethMonthIndex, ethDay) {
    let greg = toGregorian([ethYear, ethMonthIndex + 1, ethDay]);
    // Day of week from Gregorian Y/M/D without JS Date objects.
    let gy = greg[0];
    let gm = greg[1];
    let gd = greg[2];
    
    // Zeller's congruence
    let q = gd;
    let m = gm < 3 ? gm + 12 : gm;
    let Y = gm < 3 ? gy - 1 : gy;
    let K = Y % 100;
    let J = Math.floor(Y / 100);
    
    let h = (q + Math.floor((13 * (m + 1)) / 5) + K + Math.floor(K / 4) + Math.floor(J / 4) - 2 * J) % 7;
    let dow = (h + 6) % 7; // Convert Zeller (0=Sat, 1=Sun, 2=Mon...) to JS format (0=Sun, 1=Mon, ..., 6=Sat)
    
    return dow;
}

let dow = getDeterministicDow(2018, 7, 1);
console.log("Deterministic DOW for Miyazya 1, 2018 (should be 4/Thu):", dow);

let dow2 = getDeterministicDow(2018, 12, 1);
console.log("Deterministic DOW for Pagume 1, 2018 (should be 0/Sun):", dow2);
