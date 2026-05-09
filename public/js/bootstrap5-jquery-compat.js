/**
 * Bootstrap 5.3.7 — jQuery compatibility shim
 *
 * Canonical version matches compiled CSS (see public/css/app.css header; layout uses
 * public/css/app.min.css) and the bootstrap.bundle script (currently 5.3.7 CDN) in
 * resources/views/layouts/crm_client_detail_dashboard.blade.php.
 *
 * Provides $.fn.modal, $.fn.collapse, $.fn.dropdown, $.fn.alert for legacy code that
 * expects Bootstrap 4's jQuery API. Delegates to Bootstrap 5's native API.
 *
 * Note: Popover/Tooltip still use the legacy jQuery plugin from app.min.js where
 * present; this file does not register $.fn.popover. New code should prefer
 * bootstrap.Popover / bootstrap.Tooltip when possible.
 */
(function() {
    if (typeof bootstrap === 'undefined' || typeof jQuery === 'undefined') return;

    var $ = jQuery;

    // Modal: $('#modal').modal('show'|'hide'|'toggle')
    if (!$.fn.modal) {
        $.fn.modal = function(action) {
            return this.each(function() {
                var el = this;
                if (typeof el === 'string') el = document.querySelector(el);
                if (!el || !el.id) return;
                try {
                    var instance = bootstrap.Modal.getOrCreateInstance(el);
                    if (action === 'show') instance.show();
                    else if (action === 'hide') instance.hide();
                    else if (action === 'toggle') instance.toggle();
                } catch (e) { console.warn('Bootstrap modal:', e); }
            });
        };
    }

    // Collapse: $(target).collapse('toggle'|'show'|'hide')
    if (!$.fn.collapse) {
        $.fn.collapse = function(action) {
            return this.each(function() {
                try {
                    var instance = bootstrap.Collapse.getOrCreateInstance(this);
                    if (action === 'toggle') instance.toggle();
                    else if (action === 'show') instance.show();
                    else if (action === 'hide') instance.hide();
                } catch (e) { console.warn('Bootstrap collapse:', e); }
            });
        };
    }

    // Dropdown: $('.dropdown-toggle').dropdown()
    if (!$.fn.dropdown) {
        $.fn.dropdown = function() {
            return this.each(function() {
                try {
                    bootstrap.Dropdown.getOrCreateInstance(this).toggle();
                } catch (e) { console.warn('Bootstrap dropdown:', e); }
            });
        };
    }

    // Alert: $('.alert').alert('close')
    if (!$.fn.alert) {
        $.fn.alert = function(action) {
            if (action === 'close') {
                return this.each(function() {
                    try {
                        var alert = bootstrap.Alert.getOrCreateInstance(this);
                        if (alert) alert.close();
                    } catch (e) { /* no-op */ }
                });
            }
            return this;
        };
    }
})();
