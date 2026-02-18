/**
 * Send to Client - Invoice and Receipt email functionality
 * Extracted from detail-main.js - Phase 3 refactoring.
 * Requires: jQuery, ClientDetailConfig, Swal (optional)
 */
(function() {
    'use strict';

    /**
     * Send Invoice to Client
     */
    function handleSendInvoiceToClient($btn) {
        var invoiceId = $btn.data('invoice-id');
        var invoiceNo = $btn.data('invoice-no');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Send Invoice to Client?',
                text: 'This will send invoice #' + invoiceNo + ' to the client\'s email address.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendInvoiceToClientAjax(invoiceId, invoiceNo, $btn);
                }
            });
        } else {
            if (confirm('Are you sure you want to send invoice #' + invoiceNo + ' to the client\'s email?')) {
                sendInvoiceToClientAjax(invoiceId, invoiceNo, $btn);
            }
        }
    }

    function sendInvoiceToClientAjax(invoiceId, invoiceNo, $btn) {
        var originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        $btn.prop('disabled', true);

        $.ajax({
            url: window.ClientDetailConfig.urls.sendInvoiceToClient + '/' + invoiceId,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 3000
                        });
                    } else {
                        alert(response.message);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error sending invoice to client:', xhr);
                var errorMsg = 'Failed to send invoice. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                } else {
                    alert('Error: ' + errorMsg);
                }
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
            }
        });
    }

    /**
     * Send Client Fund Receipt to Client
     */
    function handleSendClientFundReceiptToClient($btn) {
        var receiptId = $btn.data('receipt-id');
        var receiptNo = $btn.data('receipt-no');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Send Receipt to Client?',
                text: 'This will send receipt #' + receiptNo + ' to the client\'s email address.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendClientFundReceiptToClientAjax(receiptId, receiptNo, $btn);
                }
            });
        } else {
            if (confirm('Are you sure you want to send receipt #' + receiptNo + ' to the client\'s email?')) {
                sendClientFundReceiptToClientAjax(receiptId, receiptNo, $btn);
            }
        }
    }

    function sendClientFundReceiptToClientAjax(receiptId, receiptNo, $btn) {
        var originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        $btn.prop('disabled', true);

        $.ajax({
            url: window.ClientDetailConfig.urls.sendClientFundReceiptToClient + '/' + receiptId,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 3000
                        });
                    } else {
                        alert(response.message);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error sending client fund receipt to client:', xhr);
                var errorMsg = 'Failed to send receipt. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                } else {
                    alert('Error: ' + errorMsg);
                }
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
            }
        });
    }

    /**
     * Send Office Receipt to Client
     */
    function handleSendOfficeReceiptToClient($btn) {
        var receiptId = $btn.data('receipt-id');
        var receiptNo = $btn.data('receipt-no');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Send Receipt to Client?',
                text: 'This will send office receipt #' + receiptNo + ' to the client\'s email address.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendOfficeReceiptToClientAjax(receiptId, receiptNo, $btn);
                }
            });
        } else {
            if (confirm('Are you sure you want to send office receipt #' + receiptNo + ' to the client\'s email?')) {
                sendOfficeReceiptToClientAjax(receiptId, receiptNo, $btn);
            }
        }
    }

    function sendOfficeReceiptToClientAjax(receiptId, receiptNo, $btn) {
        var originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        $btn.prop('disabled', true);

        $.ajax({
            url: window.ClientDetailConfig.urls.sendOfficeReceiptToClient + '/' + receiptId,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 3000
                        });
                    } else {
                        alert(response.message);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error sending office receipt to client:', xhr);
                var errorMsg = 'Failed to send receipt. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                } else {
                    alert('Error: ' + errorMsg);
                }
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
            }
        });
    }

    function attachSendToClientHandlers() {
        $('.dropdown-menu .send-invoice-to-client').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            handleSendInvoiceToClient($(this));
        });

        $('.dropdown-menu .send-client-fund-receipt-to-client').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            handleSendClientFundReceiptToClient($(this));
        });

        $('.dropdown-menu .send-office-receipt-to-client').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            handleSendOfficeReceiptToClient($(this));
        });
    }

    setTimeout(function() {
        attachSendToClientHandlers();
    }, 500);

    $(document).on('shown.bs.dropdown', function() {
        attachSendToClientHandlers();
    });

    $(document).on('click', '.send-invoice-to-client', function() {
        if ($(this).closest('.dropdown-menu').length > 0) {
            return;
        }
        handleSendInvoiceToClient($(this));
    });

    $(document).on('click', '.send-client-fund-receipt-to-client', function() {
        if ($(this).closest('.dropdown-menu').length > 0) {
            return;
        }
        handleSendClientFundReceiptToClient($(this));
    });

    $(document).on('click', '.send-office-receipt-to-client', function() {
        if ($(this).closest('.dropdown-menu').length > 0) {
            return;
        }
        handleSendOfficeReceiptToClient($(this));
    });

})();
