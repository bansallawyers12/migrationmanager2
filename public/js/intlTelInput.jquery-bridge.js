/**
 * Maps intl-tel-input v28+ (window.intlTelInput) onto $(".telephone").intlTelInput()
 * used in CRM layouts.
 *
 * Layouts load vendor/intlTelInput.min.js — the WithUtils build from copy-intl-tel-input,
 * so formatting/validation helpers are available without extra scripts or import().
 *
 * Defaults suit dial-code-only inputs (country_code[]) beside national tel_input —
 * see PhoneHelper::formatForStorage. separateDialCode stays false so +NN stays in the field.
 */
(function ($) {
    'use strict';
    if (!$ || typeof window.intlTelInput !== 'function') {
        return;
    }

    /** Options tuned for CRM .telephone = dial code (+NN) beside .tel_input. */
    function defaultOptions() {
        return {
            nationalMode: false,
            formatOnDisplay: false,
            formatAsYouType: false,
            autoPlaceholder: 'off',
            countrySearch: true,
            separateDialCode: false,
            strictMode: false,
        };
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
