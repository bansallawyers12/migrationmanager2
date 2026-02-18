/**
 * Accounts module - Client Funds Ledger balance, render, edit
 * Extracted from detail-main.js - Phase 3e refactoring.
 * Requires: jQuery, ClientDetailConfig, Flatpickr (optional)
 */
(function($) {
    'use strict';
    if (!$) return;

    function clientLedgerBalanceAmount(selectedMatter) {
        var client_id = window.ClientDetailConfig.clientId;
        $.ajax({
            type: 'post',
            url: window.ClientDetailConfig.urls.clientLedgerBalance,
            sync: true,
            data: { client_id: client_id, selectedMatter: selectedMatter },
            success: function(response) {
                var obj = $.parseJSON(response);
                $('#client_ledger_balance_amount').val(obj.record_get);
            }
        });
    }

    function renderClientFundsLedger(entries) {
        var trRows = "";
        $.each(entries, function(index, entry) {
            var typeIconMap = {
                'Deposit': 'fa-arrow-down',
                'Fee Transfer': 'fa-arrow-right-from-bracket',
                'Disbursement': 'fa-arrow-right-from-bracket',
                'Refund': 'fa-arrow-right-from-bracket'
            };
            var typeIcon = typeIconMap[entry.client_fund_ledger_type] || 'fa-money-bill';
            var typeClass = entry.client_fund_ledger_type === 'Deposit' ? 'text-success' : 'text-primary';
            var depositAmount = entry.deposit_amount ? '$' + parseFloat(entry.deposit_amount).toFixed(2) : '$0.00';
            var withdrawAmount = entry.withdraw_amount ? '$' + parseFloat(entry.withdraw_amount).toFixed(2) : '$0.00';
            var balanceAmount = entry.balance_amount ? '$' + parseFloat(entry.balance_amount).toFixed(2) : '$0.00';
            var editIcon = entry.client_fund_ledger_type !== 'Fee Transfer' ?
                '<a href="#" class="edit-ledger-entry" data-id="' + entry.id + '" data-trans-date="' + entry.trans_date + '" data-entry-date="' + entry.entry_date + '" data-type="' + entry.client_fund_ledger_type + '" data-description="' + (entry.description || '') + '" data-deposit="' + (entry.deposit_amount || '') + '" data-withdraw="' + (entry.withdraw_amount || '') + '"><i class="fas fa-pencil-alt"></i></a>' : '';
            trRows += '<tr data-id="' + entry.id + '">' +
                '<td>' + entry.trans_date + ' ' + editIcon + '</td>' +
                '<td class="type-cell"><i class="fas ' + typeIcon + ' type-icon ' + typeClass + '"></i>' +
                '<span>' + entry.client_fund_ledger_type + (entry.invoice_no ? '<br/>(' + entry.invoice_no + ')' : '') + '</span></td>' +
                '<td class="description">' + (entry.description || '') + '</td>' +
                '<td><a href="#" title="View Receipt ' + (entry.trans_no || '') + '">' + (entry.trans_no || '') + '</a></td>' +
                '<td class="currency text-success">' + depositAmount + '</td>' +
                '<td class="currency text-danger">' + withdrawAmount + '</td>' +
                '<td class="currency">' + balanceAmount + '</td></tr>';
        });
        $('.client-funds-ledger-list').html(trRows);
    }

    function handleEditLedgerEntry(element) {
        var id = $(element).data('id');
        var transDate = $(element).data('trans-date');
        var entryDate = $(element).data('entry-date');
        var type = $(element).data('type');
        var description = $(element).data('description');
        var deposit = $(element).data('deposit');
        var withdraw = $(element).data('withdraw');
        $('#editLedgerModal input[name="id"]').val(id);
        $('#editLedgerModal input[name="trans_date"]').val(transDate);
        $('#editLedgerModal input[name="entry_date"]').val(entryDate);
        $('#editLedgerModal input[name="client_fund_ledger_type"]').val(type).prop('readonly', true);
        $('#editLedgerModal input[name="description"]').val(description);
        if (parseFloat(deposit) === 0) {
            $('#editLedgerModal input[name="deposit_amount"]').val(deposit).prop('readonly', true);
        } else {
            $('#editLedgerModal input[name="deposit_amount"]').val(deposit).prop('readonly', false);
        }
        if (parseFloat(withdraw) === 0) {
            $('#editLedgerModal input[name="withdraw_amount"]').val(withdraw).prop('readonly', true);
        } else {
            $('#editLedgerModal input[name="withdraw_amount"]').val(withdraw).prop('readonly', false);
        }
        if (typeof flatpickr !== 'undefined') {
            var transDateEl = $('#editLedgerModal input[name="trans_date"]')[0];
            var entryDateEl = $('#editLedgerModal input[name="entry_date"]')[0];
            if (transDateEl && !$(transDateEl).data('flatpickr')) {
                flatpickr(transDateEl, {
                    dateFormat: 'd/m/Y',
                    allowInput: true,
                    clickOpens: true,
                    defaultDate: $(transDateEl).val() || null,
                    locale: { firstDayOfWeek: 1 },
                    onChange: function(selectedDates, dateStr) {
                        $(transDateEl).val(dateStr);
                    }
                });
            }
            if (entryDateEl && !$(entryDateEl).data('flatpickr')) {
                flatpickr(entryDateEl, {
                    dateFormat: 'd/m/Y',
                    allowInput: true,
                    clickOpens: true,
                    defaultDate: $(entryDateEl).val() || null,
                    locale: { firstDayOfWeek: 1 },
                    onChange: function(selectedDates, dateStr) {
                        $(entryDateEl).val(dateStr);
                    }
                });
            }
        }
        $('#editLedgerModal').modal('show');
    }

    function attachEditLedgerHandlers() {
        $('.dropdown-menu .edit-ledger-entry').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            handleEditLedgerEntry(this);
        });
    }

    window.clientLedgerBalanceAmount = clientLedgerBalanceAmount;
    window.renderClientFundsLedger = renderClientFundsLedger;

    $(document).ready(function() {
        setTimeout(function() {
            attachEditLedgerHandlers();
        }, 500);

        $(document).on('shown.bs.dropdown', function() {
            attachEditLedgerHandlers();
        });

        $(document).on('click', '.edit-ledger-entry', function(e) {
            if ($(this).closest('.dropdown-menu').length > 0) {
                return;
            }
            e.preventDefault();
            handleEditLedgerEntry(this);
        });

        $('#updateLedgerEntryBtn').on('click', function() {
            var form = $('#editLedgerForm')[0];
            var formData = new FormData(form);
            $.ajax({
                type: 'POST',
                url: window.ClientDetailConfig.urls.updateClientFundsLedger,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        $('#editLedgerModal').modal('hide');
                        localStorage.setItem('activeTab', 'accounts');
                        location.reload();
                        $('.custom-error-msg').html('<span class="alert alert-success">' + response.message + '</span>');
                        if (response.updatedEntries) {
                            renderClientFundsLedger(response.updatedEntries);
                        }
                        if (response.currentFundsHeld !== undefined) {
                            $('.current-funds-held').text('$ ' + parseFloat(response.currentFundsHeld).toFixed(2));
                        }
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');
                    }
                },
                error: function(xhr, status, error) {
                    $('.custom-error-msg').html('<span class="alert alert-danger">An error occurred. Please try again.</span>');
                    console.error('AJAX error:', status, error);
                }
            });
        });
    });

})(typeof jQuery !== 'undefined' ? jQuery : null);
