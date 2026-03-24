/**
 * Human-readable local datetime for CRM UI (ISO / Laravel JSON → "24 Mar 2026, 8:32 am").
 */
(function (global) {
    'use strict';

    function formatDisplayDateTime(iso) {
        if (iso == null || iso === '') return '';
        var d = new Date(iso);
        if (isNaN(d.getTime())) return String(iso);
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var day = d.getDate();
        var mon = months[d.getMonth()];
        var y = d.getFullYear();
        var h24 = d.getHours();
        var m = d.getMinutes();
        var ap = h24 >= 12 ? 'pm' : 'am';
        var h12 = h24 % 12;
        if (h12 === 0) h12 = 12;
        var mm = m < 10 ? '0' + m : String(m);
        return day + ' ' + mon + ' ' + y + ', ' + h12 + ':' + mm + ' ' + ap;
    }

    global.formatDisplayDateTime = formatDisplayDateTime;
    global.formatGrantWhen = formatDisplayDateTime;
}(typeof window !== 'undefined' ? window : this));
