/**
 * Client Detail — Account tab lazy fragment loader.
 * Stub on first paint; load once on click; re-run invoice/ledger init after inject.
 * Create-receipt modal (#createreceiptmodal) stays on the detail page.
 */
(function() {
    'use strict';

    var accountTabLoadPromise = null;

    function accountTabFragmentUrl(tabEl) {
        var el = tabEl || document.getElementById('account-tab');
        if (el && el.getAttribute('data-account-url')) {
            return el.getAttribute('data-account-url');
        }
        var urls = (window.ClientDetailConfig && window.ClientDetailConfig.urls) || {};
        if (urls.accountTab) {
            return urls.accountTab;
        }
        var encodeId = window.ClientDetailConfig && window.ClientDetailConfig.encodeId;
        if (!encodeId) {
            return '';
        }
        var matterRef = (window.ClientDetailConfig.matterId || window.ClientDetailConfig.matterRefNo || '');
        var url = '/clients/detail-account-tab/' + encodeURIComponent(encodeId);
        if (matterRef) {
            url += '/' + encodeURIComponent(matterRef);
        }
        return url;
    }

    function importTabFragment(node) {
        if (!node) {
            return null;
        }
        try {
            return document.importNode(node, true);
        } catch (err) {
            return node;
        }
    }

    function showAccountLazyError(message) {
        var tab = document.getElementById('account-tab');
        if (!tab) {
            return;
        }
        var placeholder = tab.querySelector('[data-account-lazy-placeholder]');
        if (placeholder) {
            placeholder.textContent = message || 'Failed to load account. Please refresh the page.';
            return;
        }
        var container = tab.querySelector('.card') || tab;
        container.innerHTML = '<div class="workflow-v2-empty" data-account-lazy-placeholder style="padding: 24px; color: #6c757d;">' +
            (message || 'Failed to load account. Please refresh the page.') +
            '</div>';
    }

    function activateInjectedScripts(root) {
        return new Promise(function(resolve) {
            if (!root || !root.querySelectorAll) {
                resolve();
                return;
            }
            var scripts = Array.prototype.slice.call(root.querySelectorAll('script'));
            var i = 0;

            function next() {
                if (i >= scripts.length) {
                    resolve();
                    return;
                }
                var oldScript = scripts[i++];
                var scriptType = (oldScript.getAttribute('type') || '').toLowerCase();
                if (scriptType && scriptType !== 'text/javascript' && scriptType !== 'application/javascript') {
                    next();
                    return;
                }
                var s = document.createElement('script');
                Array.prototype.slice.call(oldScript.attributes || []).forEach(function(attr) {
                    if (attr.name === 'src' || attr.name === 'type') {
                        return;
                    }
                    s.setAttribute(attr.name, attr.value);
                });
                if (oldScript.src) {
                    s.async = false;
                    s.onload = s.onerror = next;
                    s.src = oldScript.src;
                    oldScript.parentNode.replaceChild(s, oldScript);
                    return;
                }
                var code = oldScript.textContent || '';
                if (document.readyState !== 'loading') {
                    code = '(function(){\n' +
                        'var __docAdd = Document.prototype.addEventListener;\n' +
                        'Document.prototype.addEventListener = function(type, listener, options){\n' +
                        '  if (String(type).toLowerCase() === "domcontentloaded") {\n' +
                        '    try { listener.call(this); } catch (e) { console.error(e); }\n' +
                        '    return;\n' +
                        '  }\n' +
                        '  return __docAdd.call(this, type, listener, options);\n' +
                        '};\n' +
                        'try {\n' + code + '\n} finally {\n' +
                        '  Document.prototype.addEventListener = __docAdd;\n' +
                        '}\n' +
                        '})();';
                }
                s.textContent = code;
                oldScript.parentNode.replaceChild(s, oldScript);
                next();
            }

            next();
        });
    }

    function selectedMatterId() {
        if (typeof $ === 'undefined') {
            return '';
        }
        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
            return $('.general_matter_checkbox_client_detail').val();
        }
        return $('#sel_matter_id_client_detail').val() || '';
    }

    function initAccountTabAfterInject(tabEl) {
        if (typeof refreshLucideIcons === 'function' && tabEl) {
            refreshLucideIcons(tabEl);
        }
        if (typeof window.listOfInvoice === 'function') {
            window.listOfInvoice();
        }
        if (typeof window.clientLedgerBalanceAmount === 'function') {
            window.clientLedgerBalanceAmount(selectedMatterId());
        }
    }

    function needsFragmentLoad(tabEl, force) {
        if (!tabEl) {
            return false;
        }
        if (force === true) {
            return true;
        }
        return tabEl.getAttribute('data-account-lazy') === '1'
            || !!tabEl.querySelector('[data-account-lazy-placeholder]');
    }

    function ensureAccountTabLoaded(force) {
        var currentTab = document.getElementById('account-tab');
        if (!currentTab) {
            return Promise.resolve(null);
        }

        if (!needsFragmentLoad(currentTab, force)) {
            initAccountTabAfterInject(currentTab);
            return Promise.resolve(currentTab);
        }

        if (accountTabLoadPromise) {
            return accountTabLoadPromise;
        }

        var url = accountTabFragmentUrl(currentTab);
        if (!url) {
            showAccountLazyError('Account URL is missing. Please refresh the page.');
            return Promise.reject(new Error('Account fragment URL missing'));
        }

        currentTab.setAttribute('data-account-loading', '1');
        var wasActive = currentTab.classList.contains('active');

        accountTabLoadPromise = fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            credentials: 'same-origin'
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Failed to load Account tab fragment (' + response.status + ')');
            }
            return response.text();
        })
        .then(function(html) {
            currentTab = document.getElementById('account-tab');
            if (!currentTab || !currentTab.parentNode) {
                throw new Error('Account tab element no longer in document');
            }
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var parsedTab = doc.querySelector('#account-tab');
            if (!parsedTab) {
                throw new Error('Account tab fragment not found in response');
            }
            var newTab = importTabFragment(parsedTab);
            if (!newTab) {
                throw new Error('Failed to import Account tab fragment');
            }
            newTab.removeAttribute('data-account-lazy');
            newTab.removeAttribute('data-account-loading');
            if (!newTab.getAttribute('data-account-url') && url) {
                newTab.setAttribute('data-account-url', url);
            }
            if (wasActive || currentTab.classList.contains('active')) {
                newTab.classList.add('active');
            }
            currentTab.replaceWith(newTab);
            return activateInjectedScripts(newTab).then(function() {
                initAccountTabAfterInject(newTab);
                return newTab;
            });
        })
        .catch(function(err) {
            var tab = document.getElementById('account-tab');
            if (tab) {
                tab.removeAttribute('data-account-loading');
            }
            showAccountLazyError('Failed to load account. Please refresh the page.');
            throw err;
        })
        .finally(function() {
            accountTabLoadPromise = null;
        });

        return accountTabLoadPromise;
    }

    function bootAccountTabIfNeeded() {
        var tab = document.getElementById('account-tab');
        if (!tab) {
            return;
        }
        var activeNav = document.querySelector('.client-nav-button.active');
        var activeTab = (activeNav && activeNav.getAttribute('data-tab'))
            || (window.ClientDetailConfig && window.ClientDetailConfig.activeTab)
            || '';
        var path = window.location.pathname || '';
        if (activeTab !== 'account' && !/\/account\/?$/.test(path)) {
            return;
        }
        ensureAccountTabLoaded().catch(function(err) {
            console.error('[Account] Boot load failed', err);
        });
    }

    window.ensureAccountTabLoaded = ensureAccountTabLoaded;
    window.initAccountTabAfterInject = initAccountTabAfterInject;

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || typeof target.closest !== 'function') {
            return;
        }
        var button = target.closest('#account-tab .dibp-receipts-toggle');
        if (!button) {
            return;
        }
        event.preventDefault();
        var tab = document.getElementById('account-tab');
        if (!tab) {
            return;
        }
        var layout = tab.querySelector('.account-layout');
        var panel = tab.querySelector('#dibp-receipts-panel');
        if (!layout || !panel) {
            return;
        }
        var showReceipts = panel.hasAttribute('hidden');
        if (showReceipts) {
            layout.setAttribute('hidden', '');
            panel.removeAttribute('hidden');
            button.setAttribute('aria-pressed', 'true');
            button.classList.add('active');
        } else {
            panel.setAttribute('hidden', '');
            layout.removeAttribute('hidden');
            button.setAttribute('aria-pressed', 'false');
            button.classList.remove('active');
            dibpReceiptsResetBulkUi();
        }
    });

    function dibpReceiptsEscapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function dibpReceiptsCsrfHeaders() {
        var csrf = document.querySelector('meta[name="csrf-token"]');
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (csrf && csrf.getAttribute('content')) {
            headers['X-CSRF-TOKEN'] = csrf.getAttribute('content');
        }
        return headers;
    }

    function dibpReceiptsPanel() {
        return document.getElementById('dibp-receipts-panel');
    }

    function dibpReceiptsHideContextMenus() {
        ['dibp-receipts-file-context-menu', 'dibp-receipts-checklist-context-menu'].forEach(function (id) {
            var menu = document.getElementById(id);
            if (menu) {
                menu.hidden = true;
            }
        });
    }

    function dibpReceiptsPositionMenu(menu, event) {
        menu.hidden = false;
        menu.style.visibility = 'hidden';
        var menuWidth = menu.offsetWidth;
        var menuHeight = menu.offsetHeight;
        var left = event.clientX + 2;
        var top = event.clientY + 2;
        if (left + menuWidth > window.innerWidth - 5) {
            left = event.clientX - menuWidth - 2;
        }
        if (top + menuHeight > window.innerHeight - 5) {
            top = event.clientY - menuHeight - 2;
        }
        menu.style.left = Math.max(5, left) + 'px';
        menu.style.top = Math.max(5, top) + 'px';
        menu.style.visibility = 'visible';
    }

    function dibpReceiptsPreviewFile(fileType, fileUrl) {
        var container = document.getElementById('dibp-receipts-preview');
        if (!container) {
            return;
        }
        var type = String(fileType || '').toLowerCase();
        var url = String(fileUrl || '');
        if (!url) {
            container.innerHTML = '<p>Preview not available for this file.</p>';
            return;
        }
        var safeUrl = dibpReceiptsEscapeHtml(url);
        if (/(jpg|jpeg|png|gif)$/.test(type)) {
            container.innerHTML = '<div class="dibp-receipts-preview-content"><img src="' + safeUrl + '" alt="Document Preview"></div>';
            return;
        }
        if (type === 'pdf') {
            container.innerHTML = '<iframe src="' + safeUrl + '" title="Document Preview"></iframe>';
            return;
        }
        if (/^(docx?|xlsx?|pptx?)$/.test(type)) {
            var officeUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(url);
            container.innerHTML = '<iframe src="' + dibpReceiptsEscapeHtml(officeUrl) + '" title="Document Preview"></iframe>';
            return;
        }
        container.innerHTML = '<p>Preview not available for this file type.</p><a href="' + safeUrl + '" target="_blank" rel="noopener">Open File</a>';
    }

    function dibpReceiptsChecklistCellHtml(doc) {
        return '<div class="dibp-receipts-checklist" data-id="' + dibpReceiptsEscapeHtml(doc.id) + '" data-checklist="' + dibpReceiptsEscapeHtml(doc.checklist || '') + '">' +
            dibpReceiptsEscapeHtml(doc.checklist || '') +
            '</div>';
    }

    function dibpReceiptsFileCellHtml(doc) {
        var displayName = doc.display_name || doc.file_name || '';
        return '<div class="dibp-receipts-file" data-id="' + dibpReceiptsEscapeHtml(doc.id) + '" data-name="' + dibpReceiptsEscapeHtml(doc.file_name || '') + '" data-filetype="' + dibpReceiptsEscapeHtml(doc.filetype || '') + '" data-fileurl="' + dibpReceiptsEscapeHtml(doc.myfile || '') + '" data-filename="' + dibpReceiptsEscapeHtml(doc.myfile_key || '') + '" data-hubdoc-sent="' + (doc.hubdoc_sent ? '1' : '0') + '" data-hubdoc-sent-at="' + dibpReceiptsEscapeHtml(doc.hubdoc_sent_at_formatted || '') + '">' +
            '<a href="javascript:void(0);" class="dibp-receipts-preview-link"><span class="dibp-receipts-file-label">' + dibpReceiptsEscapeHtml(displayName) + '</span></a>' +
            '</div>';
    }

    function dibpReceiptsUploadCellHtml(doc, clientId, uploadAction) {
        if (doc.file_name) {
            return dibpReceiptsFileCellHtml(doc);
        }
        var formId = 'dibp-receipts-upload-form-' + doc.id;
        var icon = (typeof window.crmI === 'function') ? window.crmI('fas fa-cloud-upload-alt') : '';
        return '<form class="dibp-receipts-upload-form" id="' + formId + '" action="' + dibpReceiptsEscapeHtml(uploadAction) + '" method="post" enctype="multipart/form-data">' +
            '<input type="hidden" name="clientid" value="' + dibpReceiptsEscapeHtml(clientId) + '">' +
            '<input type="hidden" name="fileid" value="' + dibpReceiptsEscapeHtml(doc.id) + '">' +
            '<div class="document-drag-drop-zone dibp-receipts-drag-zone" data-fileid="' + dibpReceiptsEscapeHtml(doc.id) + '" data-formid="' + formId + '">' +
            '<div class="drag-zone-inner">' + icon +
            '<span class="drag-zone-text">Drag file here or <strong>click to browse</strong></span>' +
            '</div></div>' +
            '<input class="dibp-receipts-upload d-none" type="file" name="document_upload" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">' +
            '</form>';
    }

    function dibpReceiptsUploadInputFromZone(zone) {
        if (!zone || typeof zone.closest !== 'function') {
            return null;
        }
        var form = zone.closest('.dibp-receipts-upload-form');
        return form ? form.querySelector('.dibp-receipts-upload') : null;
    }

    function dibpReceiptsAssignFileAndUpload(zone, file) {
        var input = dibpReceiptsUploadInputFromZone(zone);
        if (!input || !file) {
            return;
        }
        try {
            var transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
        } catch (err) {
            window.alert('Unable to attach the file. Please click to browse.');
            return;
        }
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function appendDibpReceiptChecklistRow(doc) {
        var list = document.getElementById('dibp-receipts-list');
        var panel = dibpReceiptsPanel();
        if (!list || !doc || !doc.id) {
            return;
        }
        var emptyRow = list.querySelector('.dibp-receipts-empty-row');
        if (emptyRow) {
            emptyRow.remove();
        }
        var clientId = panel ? (panel.getAttribute('data-clientid') || '') : '';
        var uploadAction = panel ? (panel.getAttribute('data-upload-url') || '') : '';
        var row = document.createElement('tr');
        row.className = 'dibp-receipts-row';
        row.id = 'dibp-receipts-row-' + doc.id;
        row.innerHTML = '<td>' + dibpReceiptsChecklistCellHtml(doc) + '</td><td>' + dibpReceiptsUploadCellHtml(doc, clientId, uploadAction) + '</td><td></td>';
        list.appendChild(row);
    }

    function replaceDibpReceiptFileCell(doc) {
        var row = document.getElementById('dibp-receipts-row-' + doc.id);
        if (!row || !row.cells || !row.cells[1]) {
            return;
        }
        row.cells[1].innerHTML = dibpReceiptsFileCellHtml(doc);
    }

    var dibpReceiptsBulkFiles = [];

    function dibpReceiptsBulkDropzone() {
        return document.getElementById('dibp-receipts-bulk-dropzone-container');
    }

    function dibpReceiptsBulkToggleButton() {
        return document.getElementById('dibp-receipts-bulk-toggle');
    }

    function dibpReceiptsBulkMappingModal() {
        return document.getElementById('dibp-receipts-bulk-mapping-modal');
    }

    function dibpReceiptsSetBulkDropzoneOpen(open) {
        var container = dibpReceiptsBulkDropzone();
        var button = dibpReceiptsBulkToggleButton();
        if (container) {
            if (open) {
                container.removeAttribute('hidden');
            } else {
                container.setAttribute('hidden', '');
            }
        }
        if (button) {
            button.setAttribute('aria-pressed', open ? 'true' : 'false');
            var label = button.querySelector('.dibp-receipts-bulk-toggle-label');
            if (label) {
                label.textContent = open ? 'Close' : 'Bulk Upload';
            }
        }
    }

    function dibpReceiptsResetBulkUi() {
        dibpReceiptsBulkFiles = [];
        dibpReceiptsSetBulkDropzoneOpen(false);
        var list = document.getElementById('dibp-receipts-bulk-file-list');
        var count = document.getElementById('dibp-receipts-bulk-file-count');
        var input = document.getElementById('dibp-receipts-bulk-file-input');
        var progress = document.getElementById('dibp-receipts-bulk-progress');
        var bar = document.getElementById('dibp-receipts-bulk-progress-bar');
        var confirmBtn = document.getElementById('dibp-receipts-bulk-confirm');
        if (list) {
            list.setAttribute('hidden', '');
        }
        if (count) {
            count.textContent = '0';
        }
        if (input) {
            input.value = '';
        }
        if (progress) {
            progress.setAttribute('hidden', '');
        }
        if (bar) {
            bar.style.width = '0%';
            bar.textContent = '0%';
        }
        if (confirmBtn) {
            confirmBtn.disabled = false;
        }
        dibpReceiptsHideBulkMapping();
    }

    function dibpReceiptsHideBulkMapping() {
        var modal = dibpReceiptsBulkMappingModal();
        if (modal) {
            modal.setAttribute('hidden', '');
        }
    }

    function dibpReceiptsShowBulkMapping() {
        var modal = dibpReceiptsBulkMappingModal();
        if (modal) {
            modal.removeAttribute('hidden');
        }
    }

    function dibpReceiptsFormatFileSize(bytes) {
        if (!bytes) {
            return '0 Bytes';
        }
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    function dibpReceiptsChecklistNameFromFile(fileName) {
        var name = String(fileName || '').replace(/\.[^/.]+$/, '');
        name = name.replace(/^[^_]+_/, '');
        name = name.replace(/_\d{10,}$/, '');
        name = name.replace(/_/g, ' ');
        name = name.replace(/\b\w/g, function (letter) {
            return letter.toUpperCase();
        });
        return name || 'Document';
    }

    function dibpReceiptsExistingChecklistNames() {
        var names = [];
        var seen = {};
        document.querySelectorAll('#dibp-receipts-list .dibp-receipts-checklist').forEach(function (el) {
            var name = (el.getAttribute('data-checklist') || el.textContent || '').trim();
            if (name && !seen[name]) {
                seen[name] = true;
                names.push(name);
            }
        });
        document.querySelectorAll('#dibp-receipts-checklist-name option').forEach(function (option) {
            var name = (option.value || '').trim();
            if (name && !seen[name]) {
                seen[name] = true;
                names.push(name);
            }
        });
        return names;
    }

    function dibpReceiptsAutoMatchChecklist(fileName, names) {
        var base = String(fileName || '').replace(/\.[^/.]+$/, '').toLowerCase();
        var match = '';
        names.forEach(function (name) {
            var lower = String(name).toLowerCase();
            if (base === lower || base.indexOf(lower) !== -1 || lower.indexOf(base) !== -1) {
                match = name;
            }
        });
        return match;
    }

    function dibpReceiptsAddBulkFiles(fileList) {
        var allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        var skipped = [];
        Array.prototype.forEach.call(fileList || [], function (file) {
            var ext = String(file.name || '').split('.').pop().toLowerCase();
            if (allowed.indexOf(ext) === -1) {
                skipped.push(file.name + ' (unsupported type)');
                return;
            }
            if (file.size > 20 * 1024 * 1024) {
                skipped.push(file.name + ' (over 20MB)');
                return;
            }
            if (typeof mmIsAllowedDocumentFilename === 'function' ? !mmIsAllowedDocumentFilename(file.name) : !/^[a-zA-Z0-9_\-\.\s\$\(\),&+']+$/.test(file.name)) {
                skipped.push(file.name + ' (invalid characters in name)');
                return;
            }
            var exists = dibpReceiptsBulkFiles.some(function (existing) {
                return existing.name === file.name && existing.size === file.size;
            });
            if (!exists) {
                dibpReceiptsBulkFiles.push(file);
            }
        });
        if (skipped.length) {
            window.alert('The following files were skipped:\n' + skipped.join('\n'));
        }
        if (!dibpReceiptsBulkFiles.length) {
            window.alert('No valid files selected. Please select PDF, JPG, PNG, DOC, or DOCX files under 20MB.');
            return;
        }
        var list = document.getElementById('dibp-receipts-bulk-file-list');
        var count = document.getElementById('dibp-receipts-bulk-file-count');
        if (list) {
            list.removeAttribute('hidden');
        }
        if (count) {
            count.textContent = String(dibpReceiptsBulkFiles.length);
        }
        dibpReceiptsRenderBulkMapping();
    }

    function dibpReceiptsRenderBulkMapping() {
        var table = document.getElementById('dibp-receipts-bulk-mapping-table');
        if (!table) {
            return;
        }
        var names = dibpReceiptsExistingChecklistNames();
        var html = '<table class="table table-bordered" style="width: 100%;"><thead><tr><th>File Name</th><th>Checklist Assignment</th><th>Status</th><th></th></tr></thead><tbody>';
        dibpReceiptsBulkFiles.forEach(function (file, index) {
            var selected = dibpReceiptsAutoMatchChecklist(file.name, names);
            var status = selected ? 'Auto-matched' : 'Manual selection';
            html += '<tr class="dibp-receipts-bulk-file-item" data-file-index="' + index + '" data-file-name="' + dibpReceiptsEscapeHtml(file.name) + '">';
            html += '<td><div class="dibp-receipts-bulk-file-name">' + dibpReceiptsEscapeHtml(file.name) + '</div><div class="dibp-receipts-bulk-file-size">' + dibpReceiptsEscapeHtml(dibpReceiptsFormatFileSize(file.size)) + '</div></td>';
            html += '<td><select class="form-control dibp-receipts-bulk-checklist-select" data-file-index="' + index + '">';
            html += '<option value="">-- Select Checklist --</option>';
            html += '<option value="__NEW__">+ Create New Checklist</option>';
            names.forEach(function (name) {
                html += '<option value="' + dibpReceiptsEscapeHtml(name) + '"' + (selected === name ? ' selected' : '') + '>' + dibpReceiptsEscapeHtml(name) + '</option>';
            });
            html += '</select><input type="text" class="form-control dibp-receipts-bulk-new-input" data-file-index="' + index + '" placeholder="Enter new checklist name" hidden></td>';
            html += '<td><span class="dibp-receipts-bulk-status">' + status + '</span></td>';
            html += '<td><button type="button" class="btn btn-sm btn-danger dibp-receipts-bulk-remove-file" data-file-index="' + index + '">Remove</button></td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
        table.innerHTML = html;
        dibpReceiptsShowBulkMapping();
    }

    function dibpReceiptsCollectBulkMappings() {
        var autoCreate = document.getElementById('dibp-receipts-bulk-auto-create');
        var allowAuto = !!(autoCreate && autoCreate.checked);
        var mappings = [];
        var unmapped = [];
        dibpReceiptsBulkFiles.forEach(function (file, index) {
            var select = document.querySelector('#dibp-receipts-bulk-mapping-table .dibp-receipts-bulk-checklist-select[data-file-index="' + index + '"]');
            var mapping = null;
            if (select) {
                var value = select.value;
                if (value === '__NEW__') {
                    var input = document.querySelector('#dibp-receipts-bulk-mapping-table .dibp-receipts-bulk-new-input[data-file-index="' + index + '"]');
                    var typed = input ? String(input.value || '').trim() : '';
                    if (typed) {
                        mapping = { type: 'new', name: typed };
                    } else if (allowAuto) {
                        mapping = { type: 'new', name: dibpReceiptsChecklistNameFromFile(file.name) };
                    }
                } else if (value) {
                    mapping = { type: 'existing', name: value };
                } else if (allowAuto) {
                    mapping = { type: 'new', name: dibpReceiptsChecklistNameFromFile(file.name) };
                }
            }
            if (!mapping || !mapping.name) {
                unmapped.push(file.name);
            }
            mappings.push(mapping);
        });
        return { mappings: mappings, unmapped: unmapped, allowAuto: allowAuto };
    }

    function dibpReceiptsApplyBulkDocuments(documents) {
        (documents || []).forEach(function (doc) {
            if (!doc || !doc.id) {
                return;
            }
            var row = document.getElementById('dibp-receipts-row-' + doc.id);
            if (row) {
                var checklistEl = row.querySelector('.dibp-receipts-checklist');
                if (checklistEl && doc.checklist) {
                    checklistEl.setAttribute('data-checklist', doc.checklist);
                    checklistEl.textContent = doc.checklist;
                }
                replaceDibpReceiptFileCell(doc);
                return;
            }
            appendDibpReceiptChecklistRow(doc);
        });
    }

    function dibpReceiptsSubmitBulkUpload() {
        var collected = dibpReceiptsCollectBulkMappings();
        if (collected.unmapped.length && !collected.allowAuto) {
            window.alert('Please map all files to checklists or enable "Auto-create checklist for unmatched files"');
            return;
        }
        collected.mappings.forEach(function (mapping, index) {
            if (!mapping || !mapping.name) {
                collected.mappings[index] = {
                    type: 'new',
                    name: dibpReceiptsChecklistNameFromFile(dibpReceiptsBulkFiles[index].name)
                };
            }
        });

        var panel = dibpReceiptsPanel();
        var url = panel ? (panel.getAttribute('data-bulk-url') || '') : '';
        var clientId = panel ? (panel.getAttribute('data-clientid') || '') : '';
        if (!url) {
            window.alert('Unable to upload files.');
            return;
        }
        var matterInput = document.getElementById('dibp-receipts-client-matter-id');
        var matterId = matterInput ? matterInput.value : selectedMatterId();
        var body = new FormData();
        body.append('clientid', clientId);
        body.append('client_matter_id', matterId || '');
        dibpReceiptsBulkFiles.forEach(function (file, index) {
            if (typeof mmAppendBulkDocumentUploadFile === 'function') {
                mmAppendBulkDocumentUploadFile(body, file);
            } else if (typeof mmSanitizeDocumentUploadFilename === 'function') {
                body.append('files[]', file, mmSanitizeDocumentUploadFilename(file.name));
            } else {
                body.append('files[]', file, String(file.name).replace(/[^a-zA-Z0-9\-_.]/g, '_'));
            }
            body.append('mappings[]', JSON.stringify(collected.mappings[index] || { type: 'new', name: dibpReceiptsChecklistNameFromFile(file.name) }));
        });

        var progress = document.getElementById('dibp-receipts-bulk-progress');
        var bar = document.getElementById('dibp-receipts-bulk-progress-bar');
        var confirmBtn = document.getElementById('dibp-receipts-bulk-confirm');
        if (progress) {
            progress.removeAttribute('hidden');
        }
        if (confirmBtn) {
            confirmBtn.disabled = true;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        var csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf && csrf.getAttribute('content')) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf.getAttribute('content'));
        }
        xhr.upload.addEventListener('progress', function (event) {
            if (!event.lengthComputable || !bar) {
                return;
            }
            var percent = Math.round((event.loaded / event.total) * 100);
            bar.style.width = percent + '%';
            bar.textContent = percent + '%';
        });
        xhr.onload = function () {
            var payload = {};
            try {
                payload = JSON.parse(xhr.responseText || '{}');
            } catch (err) {
                payload = {};
            }
            if (xhr.status >= 200 && xhr.status < 300 && payload.status) {
                var message = payload.message || 'Files uploaded successfully!';
                if (payload.errors && payload.errors.length) {
                    message += '\n\nWarnings:\n' + payload.errors.join('\n');
                }
                window.alert(message);
                dibpReceiptsApplyBulkDocuments(payload.documents || []);
                dibpReceiptsResetBulkUi();
                return;
            }
            var errorMsg = payload.message || 'Upload failed';
            if (payload.errors && payload.errors.length) {
                errorMsg += '\n\nDetails:\n' + payload.errors.join('\n');
            }
            window.alert(errorMsg);
            if (progress) {
                progress.setAttribute('hidden', '');
            }
            if (confirmBtn) {
                confirmBtn.disabled = false;
            }
        };
        xhr.onerror = function () {
            window.alert('Upload failed');
            if (progress) {
                progress.setAttribute('hidden', '');
            }
            if (confirmBtn) {
                confirmBtn.disabled = false;
            }
        };
        xhr.send(body);
    }

    function dibpReceiptsRenameMarkup(value) {
        return '<div class="dibp-receipts-rename-row">' +
            '<input type="text" class="form-control form-control-sm dibp-receipts-rename-input" value="' + dibpReceiptsEscapeHtml(value) + '">' +
            '<button type="button" class="btn btn-sm btn-primary dibp-receipts-rename-save">Save</button>' +
            '<button type="button" class="btn btn-sm btn-secondary dibp-receipts-rename-cancel">Cancel</button>' +
            '</div>';
    }

    function dibpReceiptsStartChecklistRename(checklistEl) {
        if (!checklistEl || checklistEl.querySelector('.dibp-receipts-rename-input')) {
            return;
        }
        checklistEl.setAttribute('data-current-html', checklistEl.innerHTML);
        checklistEl.innerHTML = dibpReceiptsRenameMarkup(checklistEl.getAttribute('data-checklist') || '');
        var input = checklistEl.querySelector('.dibp-receipts-rename-input');
        if (input) {
            input.focus();
            input.select();
        }
    }

    function dibpReceiptsStartFileRename(fileEl) {
        if (!fileEl || fileEl.querySelector('.dibp-receipts-rename-input')) {
            return;
        }
        fileEl.setAttribute('data-current-html', fileEl.innerHTML);
        fileEl.innerHTML = dibpReceiptsRenameMarkup(fileEl.getAttribute('data-name') || '');
        var input = fileEl.querySelector('.dibp-receipts-rename-input');
        if (input) {
            input.focus();
            input.select();
        }
    }

    function dibpReceiptsDownload(fileEl) {
        var panel = dibpReceiptsPanel();
        if (!panel || !fileEl) {
            return;
        }
        var action = panel.getAttribute('data-download-url') || '';
        var clientId = panel.getAttribute('data-clientid') || '';
        var fileId = fileEl.getAttribute('data-id') || '';
        if (!action || !fileId) {
            window.alert('Download link not found. Please refresh the page and try again.');
            return;
        }
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = action;
        form.target = '_blank';
        form.style.display = 'none';
        var token = document.querySelector('meta[name="csrf-token"]');
        [['_token', token ? token.getAttribute('content') : ''], ['clientid', clientId], ['fileid', fileId]].forEach(function (pair) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = pair[0];
            input.value = pair[1] || '';
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
        setTimeout(function () {
            form.remove();
        }, 1000);
    }

    function dibpReceiptsSyncHubdocMenu(fileEl) {
        var item = document.getElementById('dibp-receipts-send-hubdoc');
        if (!item) {
            return;
        }
        var sent = fileEl && fileEl.getAttribute('data-hubdoc-sent') === '1';
        var sentAt = fileEl ? (fileEl.getAttribute('data-hubdoc-sent-at') || '') : '';
        var icon = (typeof window.crmI === 'function')
            ? window.crmI(sent ? 'fas fa-check' : 'fas fa-paper-plane')
            : '';
        item.textContent = '';
        item.insertAdjacentHTML('afterbegin', icon + (sent
            ? (' Already Sent to Hubdoc' + (sentAt ? ' (' + dibpReceiptsEscapeHtml(sentAt) + ')' : ''))
            : ' Send to Hubdoc'));
        item.style.color = sent ? '#28a745' : '';
    }

    function dibpReceiptsMarkFileHubdocSent(fileEl, formatted) {
        if (!fileEl) {
            return;
        }
        fileEl.setAttribute('data-hubdoc-sent', '1');
        fileEl.setAttribute('data-hubdoc-sent-at', formatted || '');
        dibpReceiptsSyncHubdocMenu(fileEl);
    }

    var dibpReceiptsHubdocSending = false;

    function dibpReceiptsHubdocButton() {
        return document.getElementById('dibp-receipts-send-hubdoc');
    }

    function dibpReceiptsSetHubdocLoading(loading) {
        var item = dibpReceiptsHubdocButton();
        if (!item) {
            return;
        }
        item.disabled = !!loading;
        if (loading) {
            item.classList.add('dibp-receipts-hubdoc-sending');
            var icon = (typeof window.crmI === 'function') ? window.crmI('fas fa-spinner fa-spin') : '';
            item.textContent = '';
            item.insertAdjacentHTML('afterbegin', icon + ' Sending...');
            item.style.color = '';
            return;
        }
        item.classList.remove('dibp-receipts-hubdoc-sending');
        item.disabled = false;
    }

    function dibpReceiptsShowHubdocProgress() {
        if (typeof Swal === 'undefined') {
            return;
        }
        Swal.fire({
            title: 'Sending to Hubdoc...',
            text: 'Please wait while the receipt is sent to Hubdoc.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function () {
                Swal.showLoading();
            }
        });
    }

    function dibpReceiptsShowHubdocResult(ok, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: ok ? 'success' : 'error',
                title: ok ? 'Success!' : 'Error',
                text: message
            });
            return;
        }
        window.alert((ok ? '' : 'Error: ') + message);
    }

    function dibpReceiptsSendHubdoc(fileEl) {
        var panel = dibpReceiptsPanel();
        if (!panel || !fileEl || dibpReceiptsHubdocSending) {
            return;
        }
        if (fileEl.getAttribute('data-hubdoc-sent') === '1') {
            window.alert('This receipt was already sent to Hubdoc.');
            dibpReceiptsHideContextMenus();
            return;
        }
        if (!window.confirm('Send this receipt to Hubdoc for processing?')) {
            return;
        }
        var url = panel.getAttribute('data-hubdoc-url') || '';
        if (!url) {
            window.alert('Unable to send to Hubdoc.');
            dibpReceiptsHideContextMenus();
            return;
        }
        dibpReceiptsHubdocSending = true;
        dibpReceiptsSetHubdocLoading(true);
        dibpReceiptsShowHubdocProgress();
        dibpReceiptsPostForm(url, {
            clientid: panel.getAttribute('data-clientid') || '',
            fileid: fileEl.getAttribute('data-id') || ''
        }).then(function (payload) {
            dibpReceiptsHubdocSending = false;
            dibpReceiptsSetHubdocLoading(false);
            if (payload.status) {
                dibpReceiptsMarkFileHubdocSent(fileEl, payload.hubdoc_sent_at_formatted || '');
                dibpReceiptsHideContextMenus();
                dibpReceiptsShowHubdocResult(true, payload.message || 'Receipt sent to Hubdoc successfully!');
                return;
            }
            dibpReceiptsSyncHubdocMenu(fileEl);
            dibpReceiptsHideContextMenus();
            dibpReceiptsShowHubdocResult(false, payload.message || 'Failed to send to Hubdoc.');
        }).catch(function () {
            dibpReceiptsHubdocSending = false;
            dibpReceiptsSetHubdocLoading(false);
            dibpReceiptsSyncHubdocMenu(fileEl);
            dibpReceiptsHideContextMenus();
            dibpReceiptsShowHubdocResult(false, 'Failed to send to Hubdoc.');
        });
    }

    function dibpReceiptsPostForm(url, fields) {
        var body = new FormData();
        Object.keys(fields).forEach(function (key) {
            body.append(key, fields[key]);
        });
        return fetch(url, {
            method: 'POST',
            headers: dibpReceiptsCsrfHeaders(),
            body: body,
            credentials: 'same-origin'
        }).then(function (response) {
            return response.json().then(function (payload) {
                payload.ok = response.ok;
                payload.httpStatus = response.status;
                return payload;
            });
        });
    }

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || typeof target.closest !== 'function') {
            return;
        }
        var bulkToggle = target.closest('#account-tab .dibp-receipts-bulk-toggle');
        if (bulkToggle) {
            event.preventDefault();
            var container = dibpReceiptsBulkDropzone();
            var isOpen = container && !container.hasAttribute('hidden');
            if (isOpen) {
                dibpReceiptsResetBulkUi();
            } else {
                var matterInput = document.getElementById('dibp-receipts-client-matter-id');
                if (matterInput) {
                    matterInput.value = selectedMatterId();
                }
                dibpReceiptsSetBulkDropzoneOpen(true);
            }
            return;
        }
        if (target.closest('#dibp-receipts-bulk-cancel') || target.closest('#dibp-receipts-bulk-close-mapping')) {
            event.preventDefault();
            dibpReceiptsHideBulkMapping();
            return;
        }
        if (target.closest('#dibp-receipts-bulk-confirm')) {
            event.preventDefault();
            dibpReceiptsSubmitBulkUpload();
            return;
        }
        var removeFile = target.closest('#dibp-receipts-bulk-mapping-table .dibp-receipts-bulk-remove-file');
        if (removeFile) {
            event.preventDefault();
            var removeIndex = parseInt(removeFile.getAttribute('data-file-index') || '-1', 10);
            if (removeIndex < 0 || !dibpReceiptsBulkFiles[removeIndex]) {
                return;
            }
            if (!window.confirm('Are you sure you want to remove "' + dibpReceiptsBulkFiles[removeIndex].name + '" from the upload list?')) {
                return;
            }
            dibpReceiptsBulkFiles.splice(removeIndex, 1);
            var count = document.getElementById('dibp-receipts-bulk-file-count');
            if (count) {
                count.textContent = String(dibpReceiptsBulkFiles.length);
            }
            if (!dibpReceiptsBulkFiles.length) {
                dibpReceiptsHideBulkMapping();
                var list = document.getElementById('dibp-receipts-bulk-file-list');
                if (list) {
                    list.setAttribute('hidden', '');
                }
                window.alert('All files have been removed. Please select files again to upload.');
                return;
            }
            dibpReceiptsRenderBulkMapping();
            return;
        }
        var bulkZone = target.closest('#account-tab .dibp-receipts-bulk-dropzone');
        if (bulkZone && !target.closest('#dibp-receipts-bulk-file-input')) {
            event.preventDefault();
            var bulkInput = document.getElementById('dibp-receipts-bulk-file-input');
            if (bulkInput) {
                bulkInput.click();
            }
            return;
        }
        var addButton = target.closest('#account-tab .dibp-receipts-add-checklist');
        if (!addButton) {
            return;
        }
        event.preventDefault();
        var matterInput = document.getElementById('dibp-receipts-client-matter-id');
        if (matterInput) {
            matterInput.value = selectedMatterId();
        }
        var errorEl = document.getElementById('dibp-receipts-add-checklist-error');
        if (errorEl) {
            errorEl.hidden = true;
            errorEl.textContent = '';
        }
        var nameInput = document.getElementById('dibp-receipts-checklist-name');
        if (nameInput) {
            nameInput.selectedIndex = 0;
            nameInput.value = '';
        }
        var modal = document.getElementById('dibp-receipts-add-checklist-modal');
        if (modal && typeof $ !== 'undefined' && typeof $(modal).modal === 'function') {
            $(modal).modal('show');
        }
    });

    var dibpReceiptsContextFile = null;
    var dibpReceiptsContextChecklist = null;

    document.addEventListener('contextmenu', function (event) {
        var target = event.target;
        if (!target || typeof target.closest !== 'function' || !target.closest('#account-tab')) {
            return;
        }
        var fileEl = target.closest('#account-tab .dibp-receipts-file');
        var checklistEl = target.closest('#account-tab .dibp-receipts-checklist');
        if (!fileEl && !checklistEl) {
            return;
        }
        if (dibpReceiptsHubdocSending) {
            event.preventDefault();
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        dibpReceiptsHideContextMenus();
        if (fileEl) {
            dibpReceiptsContextFile = fileEl;
            dibpReceiptsContextChecklist = null;
            var menu = document.getElementById('dibp-receipts-file-context-menu');
            if (menu) {
                dibpReceiptsSyncHubdocMenu(fileEl);
                dibpReceiptsPositionMenu(menu, event);
            }
            return;
        }
        dibpReceiptsContextChecklist = checklistEl;
        dibpReceiptsContextFile = null;
        var checklistMenu = document.getElementById('dibp-receipts-checklist-context-menu');
        if (checklistMenu) {
            dibpReceiptsPositionMenu(checklistMenu, event);
        }
    });

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || typeof target.closest !== 'function') {
            return;
        }
        var previewLink = target.closest('#account-tab .dibp-receipts-preview-link');
        if (previewLink) {
            event.preventDefault();
            var fileEl = previewLink.closest('.dibp-receipts-file');
            if (fileEl) {
                dibpReceiptsPreviewFile(fileEl.getAttribute('data-filetype'), fileEl.getAttribute('data-fileurl'));
            }
            return;
        }
        var contextItem = target.closest('.dibp-receipts-context-item');
        if (contextItem) {
            event.preventDefault();
            var action = contextItem.getAttribute('data-action');
            if (action === 'send-hubdoc' && dibpReceiptsContextFile) {
                dibpReceiptsSendHubdoc(dibpReceiptsContextFile);
                return;
            }
            dibpReceiptsHideContextMenus();
            if (action === 'preview' && dibpReceiptsContextFile) {
                dibpReceiptsPreviewFile(dibpReceiptsContextFile.getAttribute('data-filetype'), dibpReceiptsContextFile.getAttribute('data-fileurl'));
            } else if (action === 'download' && dibpReceiptsContextFile) {
                dibpReceiptsDownload(dibpReceiptsContextFile);
            } else if (action === 'rename-file' && dibpReceiptsContextFile) {
                dibpReceiptsStartFileRename(dibpReceiptsContextFile);
            } else if (action === 'rename-checklist' && dibpReceiptsContextChecklist) {
                dibpReceiptsStartChecklistRename(dibpReceiptsContextChecklist);
            }
            return;
        }
        var saveBtn = target.closest('#account-tab .dibp-receipts-rename-save');
        if (saveBtn) {
            event.preventDefault();
            var panel = dibpReceiptsPanel();
            var row = saveBtn.closest('.dibp-receipts-rename-row');
            var input = row ? row.querySelector('.dibp-receipts-rename-input') : null;
            var value = input ? String(input.value || '').trim() : '';
            var fileEl = saveBtn.closest('.dibp-receipts-file');
            var checklistEl = saveBtn.closest('.dibp-receipts-checklist');
            if (!panel || !value) {
                window.alert('This field is required');
                return;
            }
            if (fileEl) {
                dibpReceiptsPostForm(panel.getAttribute('data-rename-url') || '', {
                    clientid: panel.getAttribute('data-clientid') || '',
                    id: fileEl.getAttribute('data-id') || '',
                    filename: value
                }).then(function (payload) {
                    if (payload.status) {
                        fileEl.setAttribute('data-name', payload.filename || value);
                        if (payload.fileurl) {
                            fileEl.setAttribute('data-fileurl', payload.fileurl);
                        }
                        if (payload.filetype) {
                            fileEl.setAttribute('data-filetype', payload.filetype);
                        }
                        var label = (payload.filename || value) + (payload.filetype ? '.' + payload.filetype : '');
                        fileEl.innerHTML = '<a href="javascript:void(0);" class="dibp-receipts-preview-link"><span class="dibp-receipts-file-label">' + dibpReceiptsEscapeHtml(label) + '</span></a>';
                        return;
                    }
                    window.alert(payload.message || 'Unable to rename file.');
                }).catch(function () {
                    window.alert('Unable to rename file.');
                });
                return;
            }
            if (checklistEl) {
                dibpReceiptsPostForm(panel.getAttribute('data-rename-checklist-url') || '', {
                    clientid: panel.getAttribute('data-clientid') || '',
                    id: checklistEl.getAttribute('data-id') || '',
                    checklist: value
                }).then(function (payload) {
                    if (payload.status) {
                        checklistEl.setAttribute('data-checklist', payload.checklist || value);
                        checklistEl.textContent = payload.checklist || value;
                        return;
                    }
                    window.alert(payload.message || 'Unable to rename checklist.');
                }).catch(function () {
                    window.alert('Unable to rename checklist.');
                });
            }
            return;
        }
        var cancelBtn = target.closest('#account-tab .dibp-receipts-rename-cancel');
        if (cancelBtn) {
            event.preventDefault();
            var host = cancelBtn.closest('.dibp-receipts-file, .dibp-receipts-checklist');
            if (host && host.getAttribute('data-current-html')) {
                host.innerHTML = host.getAttribute('data-current-html');
            }
            return;
        }
        if (!target.closest('.dibp-receipts-context-menu') && !dibpReceiptsHubdocSending) {
            dibpReceiptsHideContextMenus();
        }
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || form.id !== 'dibp-receipts-add-checklist-form') {
            return;
        }
        event.preventDefault();
        var errorEl = document.getElementById('dibp-receipts-add-checklist-error');
        var csrf = document.querySelector('meta[name="csrf-token"]');
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (csrf && csrf.getAttribute('content')) {
            headers['X-CSRF-TOKEN'] = csrf.getAttribute('content');
        }
        fetch(form.action, {
            method: 'POST',
            headers: headers,
            body: new FormData(form),
            credentials: 'same-origin'
        })
        .then(function (response) {
            return response.json().then(function (payload) {
                payload.httpStatus = response.status;
                payload.ok = response.ok;
                return payload;
            });
        })
        .then(function (payload) {
            if (payload.status && payload.document) {
                appendDibpReceiptChecklistRow(payload.document);
                var modal = document.getElementById('dibp-receipts-add-checklist-modal');
                if (modal && typeof $ !== 'undefined' && typeof $(modal).modal === 'function') {
                    $(modal).modal('hide');
                }
                return;
            }
            var message = payload.message;
            if (!message && payload.errors) {
                var firstKey = Object.keys(payload.errors)[0];
                message = payload.errors[firstKey] && payload.errors[firstKey][0];
            }
            if (errorEl) {
                errorEl.hidden = false;
                errorEl.textContent = message || 'Unable to add checklist.';
            }
        })
        .catch(function () {
            if (errorEl) {
                errorEl.hidden = false;
                errorEl.textContent = 'Unable to add checklist.';
            }
        });
    });

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || typeof target.closest !== 'function') {
            return;
        }
        var zone = target.closest('#account-tab .dibp-receipts-drag-zone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        var input = dibpReceiptsUploadInputFromZone(zone);
        if (input) {
            input.click();
        }
    });

    document.addEventListener('dragenter', function (event) {
        var zone = event.target && event.target.closest && event.target.closest('#account-tab .dibp-receipts-drag-zone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        zone.classList.add('drag_over');
    });

    document.addEventListener('dragover', function (event) {
        var zone = event.target && event.target.closest && event.target.closest('#account-tab .dibp-receipts-drag-zone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        zone.classList.add('drag_over');
    });

    document.addEventListener('dragleave', function (event) {
        var zone = event.target && event.target.closest && event.target.closest('#account-tab .dibp-receipts-drag-zone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        zone.classList.remove('drag_over');
    });

    document.addEventListener('drop', function (event) {
        var zone = event.target && event.target.closest && event.target.closest('#account-tab .dibp-receipts-drag-zone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        zone.classList.remove('drag_over');
        var files = event.dataTransfer && event.dataTransfer.files;
        if (files && files[0]) {
            dibpReceiptsAssignFileAndUpload(zone, files[0]);
        }
    });

    document.addEventListener('dragenter', function (event) {
        var zone = event.target && event.target.closest && event.target.closest('#account-tab .dibp-receipts-bulk-dropzone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        zone.classList.add('drag_over');
    });

    document.addEventListener('dragover', function (event) {
        var zone = event.target && event.target.closest && event.target.closest('#account-tab .dibp-receipts-bulk-dropzone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        zone.classList.add('drag_over');
    });

    document.addEventListener('dragleave', function (event) {
        var zone = event.target && event.target.closest && event.target.closest('#account-tab .dibp-receipts-bulk-dropzone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        zone.classList.remove('drag_over');
    });

    document.addEventListener('drop', function (event) {
        var zone = event.target && event.target.closest && event.target.closest('#account-tab .dibp-receipts-bulk-dropzone');
        if (!zone) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        zone.classList.remove('drag_over');
        var files = event.dataTransfer && event.dataTransfer.files;
        if (files && files.length) {
            dibpReceiptsAddBulkFiles(files);
        }
    });

    document.addEventListener('change', function (event) {
        var input = event.target;
        if (!input || !input.classList) {
            return;
        }
        if (input.id === 'dibp-receipts-bulk-file-input' && input.files && input.files.length) {
            dibpReceiptsAddBulkFiles(input.files);
            input.value = '';
            return;
        }
        if (!input.classList.contains('dibp-receipts-bulk-checklist-select')) {
            return;
        }
        var fileIndex = input.getAttribute('data-file-index');
        var newInput = document.querySelector('#dibp-receipts-bulk-mapping-table .dibp-receipts-bulk-new-input[data-file-index="' + fileIndex + '"]');
        var status = input.closest('tr') ? input.closest('tr').querySelector('.dibp-receipts-bulk-status') : null;
        if (input.value === '__NEW__') {
            if (newInput) {
                newInput.removeAttribute('hidden');
            }
            if (status) {
                status.textContent = 'New checklist';
            }
            return;
        }
        if (newInput) {
            newInput.setAttribute('hidden', '');
        }
        if (status) {
            status.textContent = input.value ? 'Manual selection' : 'Manual selection';
        }
    });

    document.addEventListener('change', function (event) {
        var input = event.target;
        if (!input || !input.classList || !input.classList.contains('dibp-receipts-upload') || typeof input.closest !== 'function' || !input.closest('#account-tab')) {
            return;
        }
        var form = input.closest('.dibp-receipts-upload-form');
        if (!form || !input.files || !input.files[0]) {
            return;
        }
        var zone = form.querySelector('.dibp-receipts-drag-zone');
        if (zone) {
            zone.classList.add('uploading');
        }
        var csrf = document.querySelector('meta[name="csrf-token"]');
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (csrf && csrf.getAttribute('content')) {
            headers['X-CSRF-TOKEN'] = csrf.getAttribute('content');
        }
        var body = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: headers,
            body: body,
            credentials: 'same-origin'
        })
        .then(function (response) {
            return response.json().then(function (payload) {
                payload.ok = response.ok;
                return payload;
            });
        })
        .then(function (payload) {
            input.value = '';
            if (payload.status && payload.document) {
                replaceDibpReceiptFileCell(payload.document);
                return;
            }
            if (zone) {
                zone.classList.remove('uploading');
            }
            var message = payload.message || 'Unable to upload file.';
            window.alert(message);
        })
        .catch(function () {
            input.value = '';
            if (zone) {
                zone.classList.remove('uploading');
            }
            window.alert('Unable to upload file.');
        });
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            bootAccountTabIfNeeded();
            setTimeout(bootAccountTabIfNeeded, 300);
        });
    } else {
        bootAccountTabIfNeeded();
        setTimeout(bootAccountTabIfNeeded, 300);
    }
})();
