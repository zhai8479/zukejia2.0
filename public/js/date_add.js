/*
 *   功能:实现VBScript的DateAdd功能.
 *   参数:interval,字符串表达式，表示要添加的时间间隔.
 *   参数:number,数值表达式，表示要添加的时间间隔的个数.
 *   参数:date,时间对象.
 *   返回:新的时间对象.
 *   var now = new Date();
 *   var newDate = DateAdd( "d", 5, now);
 *---------------   DateAdd(interval,number,date)   -----------------
 */
function DateAdd(interval, number, date) {
    switch (interval) {
        case "y": {
            date.setFullYear(date.getFullYear() + parseInt(number));
            return date;
            break;
        }
        case "q": {
            date.setMonth(date.getMonth() + parseInt(number) * 3);
            return date;
            break;
        }
        case "M": {
            date.setMonth(date.getMonth() + parseInt(number));
            return date;
            break;
        }
        case "w": {
            date.setDate(date.getDate() + parseInt(number) * 7);
            return date;
            break;
        }
        case "d": {
            date.setDate(date.getDate() + parseInt(number));
            return date;
            break;
        }
        case "h": {
            date.setHours(date.getHours() + parseInt(number));
            return date;
            break;
        }
        case "m": {
            date.setMinutes(date.getMinutes() + parseInt(number));
            return date;
            break;
        }
        case "s": {
            date.setSeconds(date.getSeconds() + parseInt(number));
            return date;
            break;
        }
        default: {
            date.setDate(d.getDate() + parseInt(number));
            return date;
            break;
        }
    }
}

function DateSub(interval, number, date) {
    switch (interval) {
        case "y": {
            date.setFullYear(date.getFullYear() - parseInt(number));
            return date;
            break;
        }
        case "q": {
            date.setMonth(date.getMonth() - parseInt(number) * 3);
            return date;
            break;
        }
        case "M": {
            date.setMonth(date.getMonth() - parseInt(number));
            return date;
            break;
        }
        case "w": {
            date.setDate(date.getDate() - parseInt(number) * 7);
            return date;
            break;
        }
        case "d": {
            date.setDate(date.getDate() - parseInt(number));
            return date;
            break;
        }
        case "h": {
            date.setHours(date.getHours() - parseInt(number));
            return date;
            break;
        }
        case "m": {
            date.setMinutes(date.getMinutes() - parseInt(number));
            return date;
            break;
        }
        case "s": {
            date.setSeconds(date.getSeconds() - parseInt(number));
            return date;
            break;
        }
        default: {
            date.setDate(d.getDate() - parseInt(number));
            return date;
            break;
        }
    }
}