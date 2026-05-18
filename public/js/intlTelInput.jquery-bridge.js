/**
 * Maps intl-tel-input v24+ (window.intlTelInput) onto $(".telephone").intlTelInput()
 * used in CRM layouts.
 *
 * Utils URL: set window.intlTelInputUtilsUrl in the layout (via asset()); otherwise
 * derived from window.site_url. Empty loadUtilsOnInit skips async utils (no formatting).
 *
 * Defaults suit dial-code-only inputs (country_code[]) matched with a separate national
 * number field — see PhoneHelper::formatForStorage.
 */
(function ($) {
    'use strict';
    if (!$ || typeof window.intlTelInput !== 'function') {
        return;
    }

    function utilsLoadUrl() {
        if (
            typeof window.intlTelInputUtilsUrl === 'string' &&
            window.intlTelInputUtilsUrl.length > 0
        ) {
            return window.intlTelInputUtilsUrl;
        }
        var base =
            typeof window.site_url === 'string' && window.site_url.length
                ? window.site_url.replace(/\/$/, '')
                : '';
        return base + '/vendor/intl-tel-input/js/utils.js';
    }

    /** Options tuned for CRM .telephone = dial code (+NN) beside .tel_input. */
    function defaultOptions() {
        var utilsUrlResolved = utilsLoadUrl();
        var opts = {
            nationalMode: false,
            formatOnDisplay: false,
            formatAsYouType: false,
            autoPlaceholder: 'off',
            countrySearch: true,
        };
        if (utilsUrlResolved) {
            opts.loadUtilsOnInit = utilsUrlResolved;
        }
        return opts;
    }

    $.fn.intlTelInput = function (options) {
        if (typeof options === 'string') {
            var method = options;
            var args = Array.prototype.slice.call(arguments, 1);
            return this.each(function () {
                if (this.nodeName !== 'INPUT' && this.nodeName !== 'TEXTAREA') {
                    return;
                }
                var iti =
                    typeof window.intlTelInput.getInstance === 'function'
                        ? window.intlTelInput.getInstance(this)
                        : null;
                if (iti && typeof iti[method] === 'function') {
                    iti[method].apply(iti, args);
                }
            });
        }

        return this.each(function () {
            if (this.nodeName !== 'INPUT' && this.nodeName !== 'TEXTAREA') {
                return;
            }
            var existing =
                typeof window.intlTelInput.getInstance === 'function'
                    ? window.intlTelInput.getInstance(this)
                    : null;
            if (existing) {
                return;
            }
            var opts = $.extend(true, {}, defaultOptions(), options || {});
            if (opts.allowDropdown !== true && (this.readOnly || this.disabled)) {
                opts.allowDropdown = false;
            }
            window.intlTelInput(this, opts);
        });
    };
})(window.jQuery);
