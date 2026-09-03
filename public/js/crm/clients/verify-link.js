/**
 * Isolated Verify Link sender — does not touch appointment / email / SMS handlers.
 */
(function () {
    'use strict';

    function toast(type, message) {
        if (typeof iziToast !== 'undefined') {
            iziToast[type]({ message: message, position: 'topRight' });
            return;
        }
        window.alert(message);
    }

    function sendVerificationSms($btn) {
        var config = window.ClientDetailConfig || {};
        var url = (config.urls && config.urls.sendVerifyLink) || '';
        var clientId = config.clientId;
        if (!url || !clientId) {
            toast('error', 'Unable to send verification link.');
            return;
        }

        $btn.data('busy', true);
        window.jQuery.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: config.csrfToken,
                client_id: clientId
            },
            success: function (res) {
                toast('success', (res && res.message) || 'Verification link sent.');
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not send verification link.';
                toast('error', msg);
            },
            complete: function () {
                $btn.data('busy', false);
            }
        });
    }

    function confirmThenSend($btn) {
        if (typeof window.Swal !== 'undefined') {
            window.Swal.fire({
                title: 'Send verification SMS?',
                text: 'This will send a verification link by SMS to this client\'s primary phone.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    sendVerificationSms($btn);
                }
            });
            return;
        }

        if (window.confirm('Send a verification link by SMS to this client\'s primary phone?')) {
            sendVerificationSms($btn);
        }
    }

    function bind() {
        if (typeof window.jQuery === 'undefined') {
            return;
        }

        window.jQuery(document).on('click', '.send-verify-link', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var $btn = window.jQuery(this);
            if ($btn.data('busy')) {
                return;
            }
            confirmThenSend($btn);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bind);
    } else {
        bind();
    }
})();
