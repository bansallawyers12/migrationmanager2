/**
 * Invoices module - List invoices, Quick Receipt helpers, create invoice modal
 * Extracted from detail-main.js - Phase 3f refactoring.
 * Requires: jQuery, ClientDetailConfig
 */
(function($) {
    'use strict';
    if (!$) return;

    function listOfInvoice() {
        var client_id = window.ClientDetailConfig.clientId;
        var selectedMatter = $('.general_matter_checkbox_client_detail').is(':checked') ?
            $('.general_matter_checkbox_client_detail').val() : $('#sel_matter_id_client_detail').val();
        $.ajax({
            type: 'post',
            url: window.ClientDetailConfig.urls.listOfInvoice,
            sync: true,
            dataType: 'json',
            data: { client_id: client_id, selectedMatter: selectedMatter },
            success: function(response) {
                try {
                    var obj = response;
                    if (typeof response === 'string') {
                        obj = $.parseJSON(response);
                    }
                    if (!obj || typeof obj !== 'object') {
                        throw new Error('Invalid response structure');
                    }
                    $('#office_receipt_form .invoice_no_cls').html(obj.record_get || '<option value="">No invoices found</option>');
                    $('#client_receipt_form .invoice_no_cls').html(obj.record_get || '<option value="">No invoices found</option>');
                } catch(e) {
                    console.error('Failed to parse JSON response from listOfInvoice:', e);
                    console.error('Response received:', response);
                    $('#office_receipt_form .invoice_no_cls').html('<option value="">Error loading invoices</option>');
                    $('#client_receipt_form .invoice_no_cls').html('<option value="">Error loading invoices</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error in listOfInvoice:', status, error);
                $('#office_receipt_form .invoice_no_cls').html('<option value="">Failed to load invoices</option>');
                $('#client_receipt_form .invoice_no_cls').html('<option value="">Failed to load invoices</option>');
            }
        });
    }

    function loadInvoicesForQuickReceipt(matterId, preSelectInvoice) {
        var token = (window.ClientDetailConfig && window.ClientDetailConfig.csrfToken) || $('meta[name="csrf-token"]').attr('content');
        return $.ajax({
            type: 'POST',
            url: window.ClientDetailConfig.urls.getInvoicesByMatter,
            data: {
                client_matter_id: matterId,
                client_id: window.ClientDetailConfig.clientId,
                _token: token
            }
        }).done(function(response) {
            var $dropdown = $('#office_receipt_form .productitem_office tr.clonedrow_office').first().find('select.invoice_no_cls');
            if (!$dropdown.length) return;
            $dropdown.empty();
            $dropdown.append('<option value="">Select Invoice (Optional)</option>');
            if (response && Array.isArray(response.invoices) && response.invoices.length > 0) {
                response.invoices.forEach(function(invoice) {
                    var selected = invoice.trans_no === preSelectInvoice ? 'selected' : '';
                    $dropdown.append(
                        '<option value="' + invoice.trans_no + '" ' + selected + '>' +
                        invoice.trans_no + ' - $' + parseFloat(invoice.balance_amount || 0).toFixed(2) +
                        ' (' + (invoice.status || '') + ')</option>'
                    );
                });
            }
        }).fail(function(xhr) {
            console.error('Failed to load invoices for Quick Receipt:', xhr);
            $('#office_receipt_form .productitem_office tr.clonedrow_office').first().find('select.invoice_no_cls')
                .html('<option value="">Error loading invoices</option>');
        });
    }

    function populateQuickReceiptOfficeForm(invoiceData) {
        var $modal = $('#createreceiptmodal');
        if (!$modal.length || !$modal.data('quick-receipt-mode')) return;
        $('#client_matter_id_office').val(invoiceData.matterId);
        var today = new Date();
        var dateStr = ('0' + today.getDate()).slice(-2) + '/' + ('0' + (today.getMonth() + 1)).slice(-2) + '/' + today.getFullYear();
        var $firstRow = $('#office_receipt_form .productitem_office tr.clonedrow_office').first();
        if (!$firstRow.length) return;
        $firstRow.find('input[name="trans_date[]"]').val(dateStr);
        $firstRow.find('input[name="entry_date[]"]').val(dateStr);
        $firstRow.find('input[name="deposit_amount[]"]').val(parseFloat(invoiceData.balance || 0).toFixed(2));
        $firstRow.find('input[name="description[]"]').val('Payment for ' + invoiceData.invoiceNo + ' - ' + (invoiceData.description || ''));
        loadInvoicesForQuickReceipt(invoiceData.matterId, invoiceData.invoiceNo)
            .always(function() {
                var $modalRef = $('#createreceiptmodal');
                if ($modalRef.data('quick-receipt-mode')) {
                    $firstRow.find('select[name="payment_method[]"]').focus();
                    $modalRef.removeData('quick-receipt-mode');
                    $modalRef.removeData('quick-receipt-invoice-data');
                }
            });
    }

    function applyInvoiceDiscountUrl() {
        if (window.ClientDetailConfig && window.ClientDetailConfig.urls && window.ClientDetailConfig.urls.applyInvoiceDiscount) {
            return window.ClientDetailConfig.urls.applyInvoiceDiscount;
        }
        return '/clients/apply-invoice-discount';
    }

    function csrfToken() {
        return (window.ClientDetailConfig && window.ClientDetailConfig.csrfToken) || $('meta[name="csrf-token"]').attr('content') || '';
    }

    function formatMoney(value) {
        var amount = parseFloat(value);
        if (isNaN(amount)) {
            return '0.00';
        }
        return amount.toFixed(2);
    }

    function ensureDiscountModalOnBody() {
        var $modal = $('#applyInvoiceDiscountModal');
        if ($modal.length && !$modal.parent().is('body')) {
            $modal.appendTo('body');
        }
        return $modal;
    }

    $(document).on('click', '.apply-invoice-discount', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $link = $(this);
        var invoiceNo = $link.data('invoice-no') || '';
        var outstanding = parseFloat($link.data('invoice-balance')) || 0;
        var clientId = $link.data('client-id') || (window.ClientDetailConfig && window.ClientDetailConfig.clientId) || '';
        var $modal = ensureDiscountModalOnBody();
        if (!$modal.length) {
            alert('Discount form is not available. Please refresh the page.');
            return;
        }
        $('#apply_discount_client_id').val(clientId);
        $('#apply_discount_invoice_no').val(invoiceNo);
        $('#apply_discount_invoice_label').text(invoiceNo);
        $('#apply_discount_outstanding').text(formatMoney(outstanding));
        $('#apply_discount_amount').val('').data('max', outstanding);
        $('#apply_discount_description').val('Discount for ' + invoiceNo);
        $modal.modal('show');
        setTimeout(function() {
            $('#apply_discount_amount').trigger('focus');
        }, 200);
    });

    $(document).on('input', '#apply_discount_amount', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/(\.\d{2}).*/g, '$1');
    });

    $(document).on('submit', '#apply-invoice-discount-form', function(e) {
        e.preventDefault();
        var $submit = $('#apply_discount_submit_btn');
        var amount = parseFloat($('#apply_discount_amount').val()) || 0;
        var outstanding = parseFloat($('#apply_discount_amount').data('max'));
        if (amount <= 0) {
            alert('Enter a discount amount greater than 0.');
            return;
        }
        if (!isNaN(outstanding) && amount > outstanding) {
            alert('Discount cannot exceed the invoice outstanding balance of $' + formatMoney(outstanding) + '.');
            return;
        }

        $submit.prop('disabled', true);
        if ($('.popuploader').length) {
            $('.popuploader').show();
        }

        $.ajax({
            type: 'POST',
            url: applyInvoiceDiscountUrl(),
            dataType: 'json',
            data: {
                _token: csrfToken(),
                client_id: $('#apply_discount_client_id').val(),
                invoice_no: $('#apply_discount_invoice_no').val(),
                amount: amount.toFixed(2),
                description: $('#apply_discount_description').val()
            }
        }).done(function(obj) {
            if (!obj || !(obj.status === true || obj.status === 1)) {
                alert((obj && obj.message) ? obj.message : 'Could not apply discount. Please try again.');
                return;
            }
            $('#applyInvoiceDiscountModal').modal('hide');
            localStorage.setItem('activeTab', 'accounts');
            var okMsg = obj.message || 'Discount applied successfully';
            if (typeof toastr !== 'undefined' && typeof toastr.success === 'function') {
                toastr.success(okMsg);
            }
            var clientId = $('#apply_discount_client_id').val();
            if (typeof getallactivities === 'function' && clientId) {
                getallactivities(clientId);
            }
            if (typeof mmSoftRefreshAccountAfterOfficeReceiptCreate === 'function') {
                mmSoftRefreshAccountAfterOfficeReceiptCreate()
                    .catch(function() {
                        location.reload();
                    });
            } else {
                location.reload();
            }
        }).fail(function(xhr) {
            var msg = 'Could not apply discount. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                if (firstKey && xhr.responseJSON.errors[firstKey][0]) {
                    msg = xhr.responseJSON.errors[firstKey][0];
                }
            }
            alert(msg);
        }).always(function() {
            $submit.prop('disabled', false);
            if ($('.popuploader').length) {
                $('.popuploader').hide();
            }
        });
    });

    window.listOfInvoice = listOfInvoice;
    window.loadInvoicesForQuickReceipt = loadInvoicesForQuickReceipt;
    window.populateQuickReceiptOfficeForm = populateQuickReceiptOfficeForm;

    // createapplicationnewinvoice handler REMOVED - Create Invoice from Schedule flow unused
    // (payment schedule list removed; no /create-invoice route)

})(typeof jQuery !== 'undefined' ? jQuery : null);
