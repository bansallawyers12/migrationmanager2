/**
 * Maps jQuery .select2() usage to Tom Select (global TomSelect from tom-select.complete.min.js).
 * Keeps select2:* jQuery events and data("select2") checks working for incremental migration.
 */
(function (factory) {
  if (typeof jQuery !== 'undefined' && typeof TomSelect !== 'undefined') {
    factory(jQuery, TomSelect);
  }
})(function ($, TomSelect) {
  'use strict';

  function identityEscapeMarkup(fn) {
    if (typeof fn !== 'function') return false;
    try {
      return fn('<x>') === '<x>';
    } catch (e) {
      return false;
    }
  }

  function jqToHtml(x) {
    if (x == null) return '';
    if (typeof x === 'string') return x;
    if (x.nodeType) return x.outerHTML;
    if (x.jquery) return x[0] ? x[0].outerHTML : '';
    return String(x);
  }

  function s2ResultToOpt(r) {
    if (!r || typeof r !== 'object') return { value: '', text: '' };
    var opt = {};
    for (var k in r) {
      if (Object.prototype.hasOwnProperty.call(r, k)) opt[k] = r[k];
    }
    if (opt.value === undefined && opt.id !== undefined) opt.value = String(opt.id);
    if (opt.value === undefined) opt.value = '';
    if (opt.text === undefined && opt.label !== undefined) opt.text = opt.label;
    if (opt.text === undefined) opt.text = String(opt.value);
    return opt;
  }

  function optionToSelect2Data(raw, valueKey) {
    var d = $.extend(true, {}, raw);
    d.id = raw.id !== undefined ? raw.id : raw.value;
    if (d.text === undefined && raw.label !== undefined) d.text = raw.label;
    if (d.text === undefined) d.text = String(valueKey);
    return d;
  }

  function bindSelect2Events(ts, el, s2opts) {
    ts.on('item_add', function (value) {
      var raw = ts.options[value];
      var data = raw ? optionToSelect2Data(raw, value) : { id: value, text: value };
      $(el).trigger($.Event('select2:select', { params: { data: data } }));
    });

    ts.on('clear', function () {
      $(el).trigger($.Event('select2:clear', { params: {} }));
    });

    ts.on('dropdown_open', function () {
      $(el).trigger($.Event('select2:open', { params: {} }));
    });

    ts.on('dropdown_close', function () {
      $(el).trigger($.Event('select2:close', { params: {} }));
    });
  }

  function applyWidth($el, width) {
    if (!width) return;
    var $w = $el.next('.ts-wrapper');
    if ($w.length) {
      if (width === '100%' || width === 'resolve') $w.css('width', '100%');
      else $w.css('width', width);
    }
  }

  /** Build Tom Select settings from Select2-style options */
  function buildSettings($el, s2opts) {
    var isMulti = !!(s2opts.multiple || $el.prop('multiple'));
    var plugins = [];

    if (isMulti) plugins.push('remove_button');
    if (!isMulti && s2opts.allowClear) plugins.push('clear_button');

    var maxItems = isMulti ? null : 1;
    if (isMulti && s2opts.maximumSelectionLength != null) {
      maxItems = s2opts.maximumSelectionLength;
    }

    var opts = {
      plugins: plugins,
      diacritics: true,
      maxItems: maxItems,
      allowEmptyOption: !!s2opts.allowClear,
      closeAfterSelect: s2opts.closeOnSelect === false ? false : true,
      loadThrottle: s2opts.ajax && s2opts.ajax.delay != null ? s2opts.ajax.delay : 300,
      placeholder: s2opts.placeholder || undefined,
      maxOptions: null,
    };

    if (s2opts.minimumResultsForSearch === Infinity) {
      opts.searchField = [];
    }

    if (s2opts.dropdownParent) {
      var dp = s2opts.dropdownParent;
      opts.dropdownParent = typeof dp === 'string' ? document.querySelector(dp) : dp[0] || dp;
    }

    if (s2opts.dropdownCssClass) {
      opts.dropdownClass = ('ts-dropdown ' + String(s2opts.dropdownCssClass).trim()).trim();
    }

    if (s2opts.ajax) {
      opts.persist = false;
      if (isMulti) opts.hideSelected = true;

      var minLen = s2opts.minimumInputLength;
      if (minLen === undefined) minLen = 0;
      opts.shouldLoad = function (query) {
        return query.length >= minLen;
      };

      var ajax = s2opts.ajax;
      opts.load = function (query, callback) {
        if (query.length < minLen) {
          callback();
          return;
        }
        var payload =
          typeof ajax.data === 'function'
            ? ajax.data({ term: query, page: 1 })
            : ajax.data || {};
        $.ajax({
          url: ajax.url,
          type: ajax.type || 'GET',
          dataType: ajax.dataType || 'json',
          data: payload,
          success: function (resp) {
            var processed = ajax.processResults
              ? ajax.processResults(resp, { term: query, page: 1 })
              : { results: resp };
            var results = processed.results || [];
            callback(results.map(s2ResultToOpt));
          },
          error: function () {
            callback();
          },
        });
      };
    }

    var trustHtml = identityEscapeMarkup(s2opts.escapeMarkup);

    if (s2opts.templateResult || s2opts.templateSelection) {
      opts.render = {};
      if (s2opts.templateResult) {
        opts.render.option = function (data, escape) {
          var d = optionToSelect2Data(data, data.value);
          var out = s2opts.templateResult(d);
          if (out == null) return '';
          if (trustHtml) return jqToHtml(out);
          var html = jqToHtml(out);
          return html || escape(String(d.text || ''));
        };
      }
      opts.render.item = function (data, escape) {
        var d = optionToSelect2Data(data, data.value);
        if (s2opts.templateSelection) {
          var sel = s2opts.templateSelection(d);
          if (sel == null) return '';
          if (typeof sel === 'string') return trustHtml ? sel : escape(sel);
          return jqToHtml(sel);
        }
        return escape(String(d.text || ''));
      };
    }

    if (s2opts.data && Array.isArray(s2opts.data)) {

      opts.options = s2opts.data.map(s2ResultToOpt);
      opts.persist = false;
    }

    return opts;
  }

  if (!$.fn.select2PatchedVal) {
    var _val = $.fn.val;
    $.fn.val = function (value) {
      if (arguments.length && this.length && this[0].tomselect) {
        var ts = this[0].tomselect;
        var v = value;
        if (Array.isArray(v)) {
          ts.setValue(v.map(String), true);
        } else if (v === null || v === undefined || v === '') {
          ts.clear(true);
        } else {
          ts.setValue(String(v), true);
        }
        return this;
      }
      return _val.apply(this, arguments);
    };
    $.fn.select2PatchedVal = true;
  }

  $.fn.select2 = function (options) {
    if (options === 'destroy') {
      return this.each(function () {
        var el = this;
        if (el.tomselect) {
          el.tomselect.destroy();
        }
        $(el).removeData('select2');
        $(el).removeClass('select2-hidden-accessible');
      });
    }

    if (typeof options === 'string') {
      return this;
    }

    var s2opts = options || {};

    return this.each(function () {
      var el = this;
      if (el.tagName !== 'SELECT') return;

      if (el.tomselect) {
        el.tomselect.destroy();
      }

      var opts = buildSettings($(el), s2opts);
      var ts = new TomSelect(el, opts);
      $(el).data('select2', { tomselect: true });
      $(el).addClass('select2-hidden-accessible');
      bindSelect2Events(ts, el, s2opts);
      applyWidth($(el), s2opts.width);
    });
  };
});
