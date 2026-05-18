/**
 * Maps the v17+ vanilla API (window.intlTelInput) onto the legacy jQuery style
 * used in CRM layouts: $(".telephone").intlTelInput();
 */
(function ($) {
    'use strict';
    if (!$ || typeof window.intlTelInput !== 'function') {
        return;
    }

    function utilsUrl() {
        var base =
            typeof window.site_url === 'string' && window.site_url.length
                ? window.site_url.replace(/\/$/, '')
                : '';
        return base + '/vendor/intl-tel-input/js/utils.js';
    }

    function defaultOptions() {
        return {
            loadUtilsOnInit: utilsUrl(),
        };
    }

    $.fn.intlTelInput = function (options) {
        return this.each(function () {
            if (this.nodeName !== 'INPUT' && this.nodeName !== 'TEXTAREA') {
                return;
            }
            if (window.intlTelInput.getInstance(this)) {
                return;
            }
            var opts = $.extend(true, {}, defaultOptions(), options || {});
            if (this.readOnly && opts.allowDropdown !== true) {
                opts.allowDropdown = false;
            }
            window.intlTelInput(this, opts);
        });
    };
})(window.jQuery);
