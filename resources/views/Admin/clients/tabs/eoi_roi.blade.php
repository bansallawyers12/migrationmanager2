{{-- EOI/ROI Tab Content --}}
<div class="tab-pane" id="eoiroi" style="display:none;">
    <div class="eoi-roi-container">
        <div class="eoi-roi-header">
            <h2>
                <i class="fas fa-passport"></i> EOI / ROI Management
            </h2>
            <button type="button" class="btn btn-primary" id="btn-add-eoi">
                <i class="fas fa-plus"></i> Add New EOI
            </button>
        </div>

        {{-- EOI/ROI Entries Table --}}
        <div class="eoi-roi-section">
            <h3>EOI / ROI Entries</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="eoi-roi-table">
                    <thead>
                        <tr>
                            <th>EOI Ref</th>
                            <th>Subclass(es)</th>
                            <th>State(s)</th>
                            <th>Occupation</th>
                            <th>Points</th>
                            <th>Submission</th>
                            <th>ROI</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="eoi-roi-tbody">
                        <tr class="no-data-row">
                            <td colspan="9" class="text-center">
                                <i class="fas fa-info-circle"></i> No EOI/ROI records found. Click "Add New EOI" to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- EOI/ROI Form --}}
        <div class="eoi-roi-section" id="eoi-roi-form-section" style="display:none;">
            <h3 id="form-title">Add / Edit EOI Record</h3>
            <form id="eoi-roi-form">
                <input type="hidden" id="eoi-id" name="id">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="eoi-number">EOI Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eoi-number" name="eoi_number" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="eoi-occupation">Occupation (ANZSCO)</label>
                            <input type="text" class="form-control" id="eoi-occupation" name="eoi_occupation" 
                                   placeholder="e.g., 261313">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Subclass(es) <span class="text-danger">*</span></label>
                            <div class="checkbox-group">
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="eoi_subclasses[]" value="189"> 189
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="eoi_subclasses[]" value="190"> 190
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="eoi_subclasses[]" value="491"> 491
                                </label>
                            </div>
                            <small class="form-text text-muted">Select all applicable subclasses</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>State(s) <span class="text-danger">*</span></label>
                            <select class="form-control select2-multiple" id="eoi-states" name="eoi_states[]" 
                                    multiple="multiple" required>
                                <option value="ACT">ACT</option>
                                <option value="NSW">NSW</option>
                                <option value="NT">NT</option>
                                <option value="QLD">QLD</option>
                                <option value="SA">SA</option>
                                <option value="TAS">TAS</option>
                                <option value="VIC">VIC</option>
                                <option value="WA">WA</option>
                                <option value="FED">FED (Federal)</option>
                            </select>
                            <small class="form-text text-muted">Select all applicable states</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="eoi-points">Points</label>
                            <input type="number" class="form-control" id="eoi-points" name="eoi_points" 
                                   min="0" max="200">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="eoi-submission-date">Submission Date</label>
                            <input type="text" class="form-control datepicker" id="eoi-submission-date" 
                                   name="eoi_submission_date" placeholder="dd/mm/yyyy">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="eoi-status">Status</label>
                            <select class="form-control" id="eoi-status" name="eoi_status">
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="invited">Invited</option>
                                <option value="nominated">Nominated</option>
                                <option value="rejected">Rejected</option>
                                <option value="withdrawn">Withdrawn</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="eoi-invitation-date">Invitation Date</label>
                            <input type="text" class="form-control datepicker" id="eoi-invitation-date" 
                                   name="eoi_invitation_date" placeholder="dd/mm/yyyy">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="eoi-nomination-date">Nomination Date</label>
                            <input type="text" class="form-control datepicker" id="eoi-nomination-date" 
                                   name="eoi_nomination_date" placeholder="dd/mm/yyyy">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="eoi-roi">ROI Reference</label>
                            <input type="text" class="form-control" id="eoi-roi" name="eoi_roi">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="eoi-password">EOI Portal Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="eoi-password" 
                                       name="eoi_password" autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            id="toggle-password" title="Show/Hide Password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Password will be encrypted</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success" id="btn-save-eoi">
                        <i class="fas fa-save"></i> Save EOI
                    </button>
                    <button type="button" class="btn btn-secondary" id="btn-cancel-eoi">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger float-right" id="btn-delete-eoi" 
                            style="display:none;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>

        {{-- Points Summary Section --}}
        <div class="eoi-roi-section" id="points-summary-section" style="display:none;">
            <h3>
                <i class="fas fa-calculator"></i> Points Calculation Summary
                <button type="button" class="btn btn-sm btn-info float-right" id="btn-refresh-points">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </h3>
            
            <div class="points-summary-controls">
                <label>Calculate for Subclass:</label>
                <select class="form-control" id="points-subclass-selector" style="width: auto; display: inline-block;">
                    <option value="">No subclass</option>
                    <option value="189">Subclass 189</option>
                    <option value="190">Subclass 190</option>
                    <option value="491">Subclass 491</option>
                </select>
            </div>

            <div id="points-summary-content">
                <div class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin"></i> Loading points calculation...
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.eoi-roi-container {
    padding: 20px;
}

.eoi-roi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e0e0e0;
}

.eoi-roi-header h2 {
    margin: 0;
    font-size: 24px;
    color: #333;
}

.eoi-roi-section {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.eoi-roi-section h3 {
    font-size: 18px;
    margin-bottom: 20px;
    color: #333;
}

#eoi-roi-table {
    font-size: 14px;
}

#eoi-roi-table thead {
    background-color: #f8f9fa;
}

#eoi-roi-table tbody tr {
    cursor: pointer;
}

#eoi-roi-table tbody tr:hover {
    background-color: #f5f5f5;
}

.checkbox-inline {
    margin-right: 15px;
}

.form-actions {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.points-summary-controls {
    margin-bottom: 20px;
}

.points-total-badge {
    font-size: 48px;
    font-weight: bold;
    color: #28a745;
    text-align: center;
    margin: 20px 0;
}

.points-breakdown {
    margin: 20px 0;
}

.points-breakdown-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.points-warnings {
    margin-top: 20px;
}

.points-warning {
    padding: 10px;
    margin: 5px 0;
    border-left: 4px solid #ffc107;
    background-color: #fff3cd;
}

.points-warning.severity-high {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}

.badge-status {
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.badge-status.draft { background-color: #6c757d; color: white; }
.badge-status.submitted { background-color: #007bff; color: white; }
.badge-status.invited { background-color: #28a745; color: white; }
.badge-status.nominated { background-color: #17a2b8; color: white; }
.badge-status.rejected { background-color: #dc3545; color: white; }
.badge-status.withdrawn { background-color: #6c757d; color: white; }
</style>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/clients/eoi-roi.js') }}"></script>
@endpush

