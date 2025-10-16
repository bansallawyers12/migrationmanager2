<!-- Client Portal Tab -->
<div class="tab-pane" id="application-tab">
    <div class="card full-width client-portal-container">
        <div class="portal-header">
            <h3><i class="fas fa-globe"></i> Client Portal Access</h3>
            <div class="portal-header-controls">
                <div class="portal-status-badge">
                    @if(isset($fetchedData->cp_status) && $fetchedData->cp_status == 1)
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                    @else
                        <span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Inactive</span>
                    @endif
                </div>
                
                <!-- Portal Toggle Switch -->
                <?php
                // Check if client has any records in client_matters table
                $client_matters_exist = DB::table('client_matters')
                    ->where('client_id', $fetchedData->id)
                    ->exists();
                ?>
                @if($client_matters_exist)
                <div class="portal-toggle-container">
                    <label class="portal-toggle-label">
                        <span class="toggle-text">Portal Access:</span>
                        <div class="toggle-switch">
                            <input type="checkbox" id="client-portal-toggle-tab" 
                                   data-client-id="{{ $fetchedData->id}}" 
                                   {{ isset($fetchedData->cp_status) && $fetchedData->cp_status == 1 ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </div>
                    </label>
                </div>
                @endif
            </div>
        </div>

        <div class="portal-content">
            @if(isset($fetchedData->cp_status) && $fetchedData->cp_status == 1)
                <!-- Portal is Active -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Client portal is currently active. The client can log in using their credentials.
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h5><i class="fas fa-user"></i> Portal Credentials</h5>
                            <div class="credential-item">
                                <label>Email:</label>
                                <div class="credential-value">
                                    <span>{{ $fetchedData->email ?? 'No email set' }}</span>
                                    @if($fetchedData->email)
                                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="{{ $fetchedData->email }}" title="Copy email">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="credential-item">
                                <label>Password:</label>
                                <div class="credential-value">
                                    <span class="text-muted"><i>Set by client or sent via activation email</i></span>
                                </div>
                            </div>
                            <div class="credential-actions">
                                <button class="btn btn-primary btn-sm" id="reset-portal-password" data-client-id="{{ $fetchedData->id }}">
                                    <i class="fas fa-key"></i> Reset Password & Send Email
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card">
                            <h5><i class="fas fa-link"></i> Portal Access</h5>
                            <div class="credential-item">
                                <label>Portal URL:</label>
                                <div class="credential-value">
                                    <span id="portal-url">{{ config('app.client_portal_url', url('/client-portal')) }}</span>
                                    <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="{{ config('app.client_portal_url', url('/client-portal')) }}" title="Copy URL">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="credential-item">
                                <label>Client ID:</label>
                                <div class="credential-value">
                                    <span>{{ $fetchedData->client_id ?? 'N/A' }}</span>
                                    @if($fetchedData->client_id)
                                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="{{ $fetchedData->client_id }}" title="Copy Client ID">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="info-card">
                            <h5><i class="fas fa-chart-line"></i> Portal Usage Information</h5>
                            <p class="text-muted">Client portal usage statistics and recent activity will be displayed here.</p>
                            
                            <?php
                            // Get client's active matters for portal
                            $activeMatters = DB::table('client_matters as cm')
                                ->join('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                                ->where('cm.client_id', $fetchedData->id)
                                ->where('cm.matter_status', 1)
                                ->select('cm.id', 'cm.client_unique_matter_no', 'm.title')
                                ->get();
                            ?>

                            @if($activeMatters && count($activeMatters) > 0)
                                <div class="mt-3">
                                    <label><strong>Active Matters Visible in Portal:</strong></label>
                                    <ul class="matter-list">
                                        @foreach($activeMatters as $matter)
                                            <li>
                                                <i class="fas fa-folder-open"></i> 
                                                {{ $matter->title }} - {{ $matter->client_unique_matter_no }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i> No active matters found. The client may have limited access in the portal.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="info-card portal-features">
                            <h5><i class="fas fa-list-check"></i> Available Features</h5>
                            <div class="feature-grid">
                                <div class="feature-item">
                                    <i class="fas fa-dashboard text-primary"></i>
                                    <span>Dashboard</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-file-alt text-success"></i>
                                    <span>Documents</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-calendar text-warning"></i>
                                    <span>Appointments</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-comments text-info"></i>
                                    <span>Messages</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-tasks text-secondary"></i>
                                    <span>Tasks & Deadlines</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-sitemap text-danger"></i>
                                    <span>Workflow Status</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-receipt text-purple"></i>
                                    <span>Invoices</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-user-circle text-dark"></i>
                                    <span>Profile Management</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- Portal is Inactive -->
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Client portal is currently inactive. Use the toggle in the sidebar to activate it.
                </div>

                <div class="info-card">
                    <h5><i class="fas fa-info-circle"></i> About Client Portal</h5>
                    <p>The client portal allows clients to:</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check text-success"></i> View their case status and progress</li>
                        <li><i class="fas fa-check text-success"></i> Access and download documents</li>
                        <li><i class="fas fa-check text-success"></i> Upload required documents</li>
                        <li><i class="fas fa-check text-success"></i> View appointments and deadlines</li>
                        <li><i class="fas fa-check text-success"></i> Communicate via secure messaging</li>
                        <li><i class="fas fa-check text-success"></i> View invoices and payment history</li>
                        <li><i class="fas fa-check text-success"></i> Update their profile information</li>
                    </ul>

                    <?php
                    // Check if client has required information for portal activation
                    $hasEmail = !empty($fetchedData->email);
                    $hasMatters = DB::table('client_matters')
                        ->where('client_id', $fetchedData->id)
                        ->where('matter_status', 1)
                        ->exists();
                    ?>

                    @if(!$hasEmail || !$hasMatters)
                        <div class="alert alert-danger mt-3">
                            <h6><i class="fas fa-exclamation-circle"></i> Portal Activation Requirements:</h6>
                            <ul class="mb-0">
                                @if(!$hasEmail)
                                    <li><i class="fas fa-times text-danger"></i> Client email address is required</li>
                                @endif
                                @if(!$hasMatters)
                                    <li><i class="fas fa-times text-danger"></i> At least one active matter is required</li>
                                @endif
                            </ul>
                            <p class="mt-2 mb-0"><strong>Please complete these requirements before activating the portal.</strong></p>
                        </div>
                    @else
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle"></i> All requirements met. You can activate the portal using the toggle in the sidebar.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.client-portal-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 0;
}

.portal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.portal-header-controls {
    display: flex;
    align-items: center;
    gap: 20px;
}

.portal-toggle-container {
    display: flex;
    align-items: center;
}

.portal-toggle-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    cursor: pointer;
}

.toggle-text {
    font-size: 0.9rem;
    font-weight: 500;
    color: white;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.3);
    transition: .4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #28a745;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.portal-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.portal-status-badge .badge {
    font-size: 0.95rem;
    padding: 8px 15px;
}

.portal-content {
    padding: 25px;
}

.info-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.info-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.info-card h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}

.credential-item {
    margin-bottom: 15px;
}

.credential-item label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    display: block;
}

.credential-value {
    display: flex;
    align-items: center;
    gap: 10px;
}

.credential-value span {
    flex: 1;
    padding: 8px 12px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.copy-btn {
    padding: 6px 12px;
    border-radius: 4px;
    transition: all 0.2s;
}

.copy-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.credential-actions {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
}

.feature-list {
    list-style: none;
    padding-left: 0;
}

.feature-list li {
    padding: 8px 0;
    font-size: 1rem;
}

.feature-list li i {
    margin-right: 10px;
}

.matter-list {
    list-style: none;
    padding-left: 0;
}

.matter-list li {
    padding: 10px 15px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin-bottom: 8px;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.2s;
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.feature-item i {
    font-size: 1.5rem;
}

.feature-item span {
    font-weight: 500;
    color: #2c3e50;
}

.text-purple {
    color: #9333ea !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy to clipboard functionality
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const textToCopy = this.getAttribute('data-copy');
            navigator.clipboard.writeText(textToCopy).then(() => {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.remove('btn-success');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard');
            });
        });
    });

    // Portal toggle functionality (both sidebar and tab toggles)
    function handlePortalToggle(toggleElement) {
        const clientId = toggleElement.getAttribute('data-client-id');
        const isChecked = toggleElement.checked;
        
        // Show loading state
        toggleElement.disabled = true;
        
        fetch('{{ route("admin.clients.toggleClientPortal") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                client_id: clientId,
                status: isChecked
            })
        })
        .then(response => response.json())
        .then(data => {
            toggleElement.disabled = false;
            
            if (data.success) {
                // Update both toggles to stay in sync
                const sidebarToggle = document.getElementById('client-portal-toggle');
                const tabToggle = document.getElementById('client-portal-toggle-tab');
                
                if (sidebarToggle) sidebarToggle.checked = isChecked;
                if (tabToggle) tabToggle.checked = isChecked;
                
                // Show success message
                alert(data.message);
                
                // Reload the page to update the tab content
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Revert toggle state on error
                toggleElement.checked = !isChecked;
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toggleElement.disabled = false;
            toggleElement.checked = !isChecked;
            alert('Error updating portal status. Please try again.');
        });
    }

    // Handle sidebar toggle
    const sidebarToggle = document.getElementById('client-portal-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('change', function() {
            handlePortalToggle(this);
        });
    }

    // Handle tab toggle
    const tabToggle = document.getElementById('client-portal-toggle-tab');
    if (tabToggle) {
        tabToggle.addEventListener('change', function() {
            handlePortalToggle(this);
        });
    }

    // Reset password functionality
    const resetBtn = document.getElementById('reset-portal-password');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            const clientId = this.getAttribute('data-client-id');
            
            if (confirm('This will generate a new password and send it to the client via email. Continue?')) {
                // Disable the toggle first
                const currentStatus = document.getElementById('client-portal-toggle');
                const isCurrentlyActive = currentStatus && currentStatus.checked;
                
                if (!isCurrentlyActive) {
                    alert('Client portal must be active to reset password');
                    return;
                }

                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
                this.disabled = true;

                // Toggle off and on to trigger password reset
                fetch('{{ route("admin.clients.toggleClientPortal") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        client_id: clientId,
                        status: false
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Now turn it back on with new password
                        return fetch('{{ route("admin.clients.toggleClientPortal") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                client_id: clientId,
                                status: true
                            })
                        });
                    } else {
                        throw new Error(data.message || 'Failed to reset password');
                    }
                })
                .then(response => response.json())
                .then(data => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                    
                    if (data.success) {
                        alert('Password reset successfully! An email with the new credentials has been sent to the client.');
                    } else {
                        throw new Error(data.message || 'Failed to reset password');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.innerHTML = originalText;
                    this.disabled = false;
                    alert('Error resetting password: ' + error.message);
                });
            }
        });
    }
});
</script>
