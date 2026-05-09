/**
 * Build <option> HTML for phone dial codes from window.countriesData (id, name, sortname, phonecode).
 * Depends on window.phonePopularIsoCodes (array of ISO2 uppercase) and optional window.phoneDefaultDialCode.
 */
(function (global) {
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function normalizeDial(raw) {
        if (raw === undefined || raw === null) return '';
        var s = String(raw).trim().replace(/[^\d+]/g, '');
        if (!s) return '';
        if (s.charAt(0) !== '+') s = '+' + s.replace(/^\+/, '');
        return s;
    }

    function dialFromCountry(row) {
        if (!row || row.phonecode === undefined || row.phonecode === null) return '';
        var pc = String(row.phonecode).trim();
        return pc === '' ? '' : '+' + pc.replace(/^\+/, '');
    }

    global.buildPhoneDialCodeOptionsHtml = function (selectedRaw, options) {
        options = options || {};
        var showPlaceholder = options.showPlaceholder === true;
        var placeholderLabel = options.placeholderLabel || 'Select';

        var rows = Array.isArray(global.countriesData) ? global.countriesData.slice() : [];
        rows = rows.filter(function (r) {
            return r && r.phonecode !== undefined && r.phonecode !== null && String(r.phonecode).trim() !== '';
        });

        var popularIso = Array.isArray(global.phonePopularIsoCodes)
            ? global.phonePopularIsoCodes.map(function (x) { return String(x || '').toUpperCase(); })
            : ['AU', 'IN', 'PK', 'NP', 'GB', 'CA', 'US'];

        var selected = normalizeDial(selectedRaw);
        var defaultDial = normalizeDial(global.phoneDefaultDialCode) || '+61';
        if (!selected) selected = defaultDial;

        if (!rows.length) {
            var fallbackHtml = '';
            if (showPlaceholder) {
                fallbackHtml += '<option value="">' + escapeHtml(placeholderLabel) + '</option>';
            }
            fallbackHtml += '<option value="' + escapeHtml(defaultDial) + '" selected>' + escapeHtml(defaultDial + ' (default)') + '</option>';
            return fallbackHtml;
        }

        var preferred = [];
        popularIso.forEach(function (iso) {
            rows.forEach(function (r) {
                if (String(r.sortname || '').toUpperCase() === iso) preferred.push(r);
            });
        });

        var prefIds = {};
        preferred.forEach(function (r) { prefIds[r.id] = true; });

        var rest = rows.filter(function (r) { return !prefIds[r.id]; });
        rest.sort(function (a, b) {
            return String(a.name || '').localeCompare(String(b.name || ''));
        });

        var listed = {};

        function optHtml(dial, label, sel) {
            if (!dial) return '';
            listed[dial] = true;
            var isSel = sel && dial === sel ? ' selected' : '';
            return '<option value="' + escapeHtml(dial) + '"' + isSel + '>' + escapeHtml(label) + '</option>';
        }

        var html = '';

        if (showPlaceholder) {
            html += '<option value="">' + escapeHtml(placeholderLabel) + '</option>';
        }

        if (preferred.length) {
            html += '<optgroup label="Popular">';
            preferred.forEach(function (r) {
                var d = dialFromCountry(r);
                var lab = d + ' (' + (r.name || '') + ')';
                html += optHtml(d, lab, selected);
            });
            html += '</optgroup>';
        }

        html += '<optgroup label="All countries">';
        rest.forEach(function (r) {
            var d = dialFromCountry(r);
            var lab = d + ' (' + (r.name || '') + ')';
            html += optHtml(d, lab, selected);
        });
        html += '</optgroup>';

        if (selected && !listed[selected]) {
            html += '<option value="' + escapeHtml(selected) + '" selected>' + escapeHtml(selected + ' (current)') + '</option>';
        }

        return html;
    };
})(window);
