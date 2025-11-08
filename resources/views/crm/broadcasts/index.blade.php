@extends('layouts.crm_client_detail')
@section('title', 'Broadcast Notifications')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="mb-0">Broadcast Notifications</h1>
                <p class="mb-0 broadcast-subtitle">Send announcements and monitor read receipts in real time.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" id="broadcast-refresh-history">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh History
                </button>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Compose Broadcast</h4>
                        </div>
                        <div class="card-body">
                            <div id="broadcast-compose-feedback" class="alert d-none" role="alert"></div>
                            <form id="broadcast-compose-form" novalidate>
                                @csrf
                                <div class="form-group">
                                    <label for="broadcast-title">Title <span class="text-muted">(optional)</span></label>
                                    <input type="text" id="broadcast-title" name="title" class="form-control" maxlength="255" placeholder="System Maintenance">
                                </div>

                                <div class="form-group">
                                    <label for="broadcast-message">Message</label>
                                    <textarea id="broadcast-message" name="message" class="form-control" rows="5" maxlength="1000" placeholder="Enter the announcement you want everyone to see." required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="broadcast-scope">Audience</label>
                                    <select id="broadcast-scope" name="scope" class="form-control">
                                        <option value="all" selected>All users</option>
                                        <option value="specific">Specific team members</option>
                                        <option value="team" disabled>Teams (coming soon)</option>
                                    </select>
                                </div>

                                <div class="form-group d-none" id="broadcast-recipient-group">
                                    <label for="broadcast-recipient-select">Select recipients</label>
                                    <select id="broadcast-recipient-select" class="form-control" name="recipient_ids[]" multiple="multiple" data-placeholder="Search team members"></select>
                                    <small class="form-text text-muted">Start typing to search for staff members. Portal user targeting will be added soon.</small>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <span class="text-muted small">
                                        Broadcasts send instantly and appear in the sticky banner for recipients.
                                    </span>
                                    <button type="submit" class="btn btn-primary" id="broadcast-submit-btn">
                                        <span class="submit-text">Send Broadcast</span>
                                        <span class="submit-spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">History</h4>
                            <span class="badge badge-primary" id="broadcast-history-count">0 sent</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="broadcast-history-table">
                                    <thead>
                                        <tr>
                                            <th>Sent</th>
                                            <th>Message</th>
                                            <th class="text-center">Read</th>
                                            <th class="text-center">Unread</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="broadcast-history-body">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-bullhorn mb-2" style="font-size: 28px;"></i>
                                                <div>No broadcasts yet. Send your first announcement!</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-muted small">
                            History is grouped by broadcast batch. Totals update automatically as recipients read notifications.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Currently Active Users</h4>
                            <small class="text-muted">Presence is calculated from active sessions within the last 5 minutes.</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-success mr-3" id="active-users-count">0 online</span>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="active-users-refresh">
                                <i class="fas fa-redo mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="active-users-table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Team</th>
                                        <th>Last Activity</th>
                                        <th>Last Login</th>
                                    </tr>
                                </thead>
                                <tbody id="active-users-body">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-users mb-2" style="font-size: 28px;"></i>
                                            <div>No active users detected yet.</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        Refreshing manually will recalculate active sessions in real time. Offline users are not shown to keep the list focused.
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="broadcastDetailModal" tabindex="-1" role="dialog" aria-labelledby="broadcastDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="broadcastDetailModalLabel">Broadcast Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong id="broadcast-detail-title" class="d-block"></strong>
                    <span id="broadcast-detail-message" class="d-block"></span>
                    <small class="text-muted" id="broadcast-detail-meta"></small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Recipient</th>
                                <th>Status</th>
                                <th>Read at</th>
                            </tr>
                        </thead>
                        <tbody id="broadcast-detail-body">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Loading recipients…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const composeForm = document.getElementById('broadcast-compose-form');
        const messageInput = document.getElementById('broadcast-message');
        const titleInput = document.getElementById('broadcast-title');
        const scopeSelect = document.getElementById('broadcast-scope');
        const recipientGroup = document.getElementById('broadcast-recipient-group');
        const recipientSelect = $('#broadcast-recipient-select');

        const feedbackEl = document.getElementById('broadcast-compose-feedback');
        const submitBtn = document.getElementById('broadcast-submit-btn');
        const submitText = submitBtn.querySelector('.submit-text');
        const submitSpinner = submitBtn.querySelector('.submit-spinner');

        const historyBody = document.getElementById('broadcast-history-body');
        const historyCount = document.getElementById('broadcast-history-count');
        const refreshBtn = document.getElementById('broadcast-refresh-history');

        const detailModal = $('#broadcastDetailModal');
        const detailTitle = document.getElementById('broadcast-detail-title');
        const detailMessage = document.getElementById('broadcast-detail-message');
        const detailMeta = document.getElementById('broadcast-detail-meta');
        const detailBody = document.getElementById('broadcast-detail-body');

        const activeUsersBody = document.getElementById('active-users-body');
        const activeUsersCount = document.getElementById('active-users-count');
        const activeUsersRefresh = document.getElementById('active-users-refresh');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function toggleRecipientsVisibility() {
            if (scopeSelect.value === 'specific') {
                recipientGroup.classList.remove('d-none');
            } else {
                recipientGroup.classList.add('d-none');
                recipientSelect.val(null).trigger('change');
            }
        }

        function showFeedback(type, message) {
            feedbackEl.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
            feedbackEl.classList.add(`alert-${type}`);
            feedbackEl.textContent = message;
        }

        function hideFeedback() {
            feedbackEl.classList.add('d-none');
            feedbackEl.textContent = '';
        }

        function setSubmitting(isSubmitting) {
            submitBtn.disabled = isSubmitting;
            submitText.classList.toggle('d-none', isSubmitting);
            submitSpinner.classList.toggle('d-none', !isSubmitting);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            if (Number.isNaN(date.getTime())) {
                return '';
            }
            return new Intl.DateTimeFormat(undefined, {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
            }).format(date);
        }

        function renderHistoryTable(items) {
            historyBody.innerHTML = '';

            if (!items.length) {
                historyBody.innerHTML = `<tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-bullhorn mb-2" style="font-size: 28px;"></i>
                        <div>No broadcasts yet. Send your first announcement!</div>
                    </td>
                </tr>`;
                historyCount.textContent = '0 sent';
                return;
            }

            historyCount.textContent = `${items.length} sent`;

            items.forEach((item) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatDate(item.sent_at)}</td>
                    <td>
                        ${item.title ? `<strong>${item.title}</strong><br>` : ''}
                        <span class="text-muted">${item.message}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-success">${item.read_count}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-warning">${item.unread_count}</span>
                    </td>
                    <td class="text-right">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-action="view-broadcast" data-batch="${item.batch_uuid}">
                            View details
                        </button>
                    </td>
                `;
                historyBody.appendChild(row);
            });
        }

        function renderActiveUsers(users) {
            activeUsersBody.innerHTML = '';

            if (!users.length) {
                activeUsersBody.innerHTML = `<tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-users mb-2" style="font-size: 28px;"></i>
                        <div>No active users detected in the last few minutes.</div>
                    </td>
                </tr>`;
                activeUsersCount.textContent = '0 online';
                activeUsersCount.className = 'badge badge-secondary';
                return;
            }

            activeUsersCount.textContent = `${users.length} online`;
            activeUsersCount.className = 'badge badge-success';

            users.forEach((user) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <strong>${user.name}</strong><br>
                        <span class="text-muted small">#${user.id}</span>
                    </td>
                    <td>${user.role ?? '—'}</td>
                    <td>${user.team ?? '—'}</td>
                    <td>${user.last_activity ? formatDate(user.last_activity) : '—'}</td>
                    <td>${user.last_login ? formatDate(user.last_login) : '—'}</td>
                `;
                activeUsersBody.appendChild(row);
            });
        }

        function loadHistory() {
            historyBody.classList.add('loading');
            fetch('/notifications/broadcasts/history', {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'include',
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Unable to load broadcast history.');
                    }
                    return response.json();
                })
                .then((payload) => {
                    renderHistoryTable(payload.data || []);
                })
                .catch((error) => {
                    console.error(error);
                    showFeedback('danger', 'Failed to load broadcast history. Please try again.');
                })
                .finally(() => {
                    historyBody.classList.remove('loading');
                });
        }

        function loadBroadcastDetails(batchUuid) {
            detailBody.innerHTML = `<tr>
                <td colspan="3" class="text-center text-muted py-3">Loading recipients…</td>
            </tr>`;

            fetch(`/notifications/broadcasts/${batchUuid}/details`, {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'include',
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Unable to load broadcast details.');
                    }
                    return response.json();
                })
                .then((payload) => {
                    const data = payload.data;
                    detailTitle.textContent = data.title || '';
                    detailTitle.classList.toggle('d-none', !data.title);
                    detailMessage.textContent = data.message || '';
                    detailMeta.textContent = `${data.sender_name || 'You'} • ${formatDate(data.sent_at)}`;

                    if (!Array.isArray(data.recipients) || !data.recipients.length) {
                        detailBody.innerHTML = `<tr>
                            <td colspan="3" class="text-center text-muted py-3">No recipients found.</td>
                        </tr>`;
                        return;
                    }

                    detailBody.innerHTML = '';
                    data.recipients.forEach((recipient) => {
                        const row = document.createElement('tr');
                        const statusBadge = recipient.read
                            ? '<span class="badge badge-success">Read</span>'
                            : '<span class="badge badge-secondary">Unread</span>';
                        row.innerHTML = `
                            <td>${recipient.receiver_name || `User #${recipient.receiver_id}`}</td>
                            <td>${statusBadge}</td>
                            <td>${recipient.read_at ? formatDate(recipient.read_at) : '-'}</td>
                        `;
                        detailBody.appendChild(row);
                    });
                })
                .catch((error) => {
                    console.error(error);
                    detailBody.innerHTML = `<tr>
                        <td colspan="3" class="text-center text-danger py-3">Failed to load recipients.</td>
                    </tr>`;
                });
        }

        function loadActiveUsers() {
            fetch('/dashboard/active-users', {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'include',
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Unable to load active users.');
                    }
                    return response.json();
                })
                .then((payload) => {
                    renderActiveUsers(payload.data || []);
                })
                .catch((error) => {
                    console.error(error);
                    activeUsersBody.innerHTML = `<tr>
                        <td colspan="5" class="text-center text-danger py-3">Failed to load active users.</td>
                    </tr>`;
                    activeUsersCount.textContent = 'Unavailable';
                    activeUsersCount.className = 'badge badge-warning';
                });
        }

        composeForm.addEventListener('submit', (event) => {
            event.preventDefault();
            hideFeedback();

            if (!messageInput.value.trim()) {
                showFeedback('warning', 'Please enter a message before sending your broadcast.');
                messageInput.focus();
                return;
            }

            if (scopeSelect.value === 'specific' && recipientSelect.val().length === 0) {
                showFeedback('warning', 'Select at least one recipient or switch back to All users.');
                return;
            }

            setSubmitting(true);

            fetch('/notifications/broadcasts/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'include',
                body: JSON.stringify({
                    title: titleInput.value || null,
                    message: messageInput.value,
                    scope: scopeSelect.value,
                    recipient_ids: scopeSelect.value === 'specific' ? recipientSelect.val() : [],
                }),
            })
                .then(async (response) => {
                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload.message || 'Unable to send broadcast.');
                    }
                    return payload;
                })
                .then((payload) => {
                    showFeedback('success', 'Broadcast sent successfully.');
                    composeForm.reset();
                    recipientSelect.val(null).trigger('change');
                    toggleRecipientsVisibility();
                    loadHistory();
                })
                .catch((error) => {
                    console.error(error);
                    showFeedback('danger', error.message || 'Failed to send broadcast.');
                })
                .finally(() => {
                    setSubmitting(false);
                });
        });

        historyBody.addEventListener('click', (event) => {
            const button = event.target.closest('[data-action="view-broadcast"]');
            if (!button) {
                return;
            }
            const batchUuid = button.getAttribute('data-batch');
            if (!batchUuid) {
                return;
            }
            loadBroadcastDetails(batchUuid);
            detailModal.modal('show');
        });

        refreshBtn.addEventListener('click', (event) => {
            event.preventDefault();
            loadHistory();
        });

        activeUsersRefresh.addEventListener('click', (event) => {
            event.preventDefault();
            loadActiveUsers();
        });

        scopeSelect.addEventListener('change', toggleRecipientsVisibility);

        recipientSelect.select2({
            width: '100%',
            placeholder: recipientSelect.data('placeholder') || 'Select recipients',
            minimumInputLength: 1,
            ajax: {
                url: '/getassigneeajax',
                dataType: 'json',
                delay: 250,
                data(params) {
                    return {
                        likevalue: params.term || '',
                    };
                },
                processResults(data) {
                    return {
                        results: (data || []).map((item) => ({
                            id: item.id,
                            text: item.assignee || item.agent_id || `User #${item.id}`,
                        })),
                    };
                },
                cache: true,
            },
        });

        toggleRecipientsVisibility();
        loadHistory();
        loadActiveUsers();
    })();
</script>
@endpush

@push('styles')
<style>
    .broadcast-subtitle {
        color: #4a5568;
    }
</style>
@endpush

