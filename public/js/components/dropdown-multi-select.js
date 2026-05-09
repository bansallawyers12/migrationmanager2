/**
 * Dropdown Multi-Select Component
 *
 * Custom multi-select dropdowns with checkbox list + hidden <select>.
 * Scoped per closest .form-group so multiple widgets / popovers do not cross-wire
 * global #rem_cat / .assignee-item queries.
 */

(function($) {
    'use strict';

    function getAssigneeScope($el) {
        var $fg = $el.closest('.form-group');
        return $fg.length ? $fg : null;
    }

    function updateSelectedValuesFromCheckbox($checkbox) {
        var $scope = getAssigneeScope($checkbox);
        var $searchRoot = $scope && $scope.length ? $scope : $(document);

        var selectedValues = [];
        $searchRoot.find('.checkbox-item:checked').each(function() {
            selectedValues.push($(this).val());
        });

        var $remCat = $searchRoot.find('#rem_cat').filter('select');
        if ($remCat.length) {
            $remCat.val(selectedValues).trigger('change');
        }

        var count = selectedValues.length;
        var $countSpan = $checkbox.closest('.dropdown-multi-select').find('.selected-count').first();
        if (!$countSpan.length && $scope && $scope.length) {
            $countSpan = $scope.find('.selected-count').first();
        }
        if (count > 0) {
            $countSpan.text(' (' + count + ' selected)');
        } else {
            $countSpan.text('');
        }
    }

    $(document).ready(function() {
        initDropdownMultiSelect();
    });

    function initDropdownMultiSelect() {

        $(document).on('click', '.dropdown-menu', function(e) {
            e.stopPropagation();
        });

        $(document).on('change', '.checkbox-item', function() {
            updateSelectedValuesFromCheckbox($(this));
        });

        $(document).on('change', '#select-all', function() {
            var isChecked = $(this).is(':checked');
            var $scope = getAssigneeScope($(this));
            var $items = $scope && $scope.length
                ? $scope.find('.assignee-item:visible .checkbox-item')
                : $('.assignee-item:visible .checkbox-item');
            $items.prop('checked', isChecked).trigger('change');
        });

        $(document).on('input', '.assignee-search-input', function(e) {
            e.stopPropagation();

            var searchTerm = $(this).val().toLowerCase();
            var $scope = getAssigneeScope($(this));
            var $items = $scope && $scope.length ? $scope.find('.assignee-item') : $('.assignee-item');

            if ($items.length === 0) {
                return;
            }

            $items.each(function() {
                var $item = $(this);
                var itemText = $item.text().toLowerCase();

                if (searchTerm === '' || itemText.indexOf(searchTerm) > -1) {
                    $item.show().removeClass('hidden');
                } else {
                    $item.hide().addClass('hidden');
                }
            });
        });

        $(document).on('show.bs.dropdown', function(e) {
            var $toggle = $(e.target).closest('[data-bs-toggle="dropdown"]');
            var $scope = getAssigneeScope($toggle);

            if ($scope && $scope.length) {
                $scope.find('.assignee-search-input').val('');
                $scope.find('.assignee-item').show().removeClass('hidden');
            } else {
                $('.assignee-search-input').val('');
                $('.assignee-item').show().removeClass('hidden');
            }
        });

        $(document).on('shown.bs.dropdown', function(e) {
            var $toggle = $(e.target).closest('[data-bs-toggle="dropdown"]');
            var $scope = getAssigneeScope($toggle);
            setTimeout(function() {
                var $input = $scope && $scope.length
                    ? $scope.find('.assignee-search-input').first()
                    : $('.assignee-search-input').first();
                if ($input.length) {
                    $input.trigger('focus');
                }
            }, 100);
        });
    }

    window.DropdownMultiSelect = {
        init: initDropdownMultiSelect,
        updateValues: function($checkbox) {
            updateSelectedValuesFromCheckbox($checkbox || $('.checkbox-item').first());
        }
    };

})(jQuery);
