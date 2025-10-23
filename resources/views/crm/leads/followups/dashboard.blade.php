@extends('layouts.crm_client_detail_dashboard')

@section('content')
<style>
    .followup-dashboard {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .followup-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #6777ef;
    }
    
    .stat-card.overdue { border-left-color: #fc544b; }
    .stat-card.today { border-left-color: #ffa426; }
    .stat-card.upcoming { border-left-color: #47c363; }
    
    .stat-card h3 {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 10px;
        font-weight: 500;
    }
    
    .stat-card .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #343a40;
    }
    
    .followup-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .section-header h2 {
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .badge-count {
        background: #e3eaef;
        color: #6c757d;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }
    
    .followup-card {
        border: 1px solid #e3e3e3;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .followup-card:hover {
        border-color: #6777ef;
        box-shadow: 0 2px 8px rgba(103, 119, 239, 0.15);
    }
    
    .followup-card.overdue {
        border-left: 4px solid #fc544b;
        background: #fff5f5;
    }
    
    .followup-card.today {
        border-left: 4px solid #ffa426;
        background: #fffaf3;
    }
    
    .followup-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 10px;
    }
    
    .lead-info {
        flex: 1;
    }
    
    .lead-name {
        font-weight: 600;
        font-size: 16px;
        color: #343a40;
        margin-bottom: 5px;
    }
    
    .followup-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        font-size: 13px;
        color: #6c757d;
    }
    
    .followup-meta i {
        margin-right: 5px;
    }
    
    .followup-actions {
        display: flex;
        gap: 5px;
    }
    
    .btn-action {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }
    
    .btn-complete {
        background: #47c363;
        color: white;
    }
    
    .btn-complete:hover {
        background: #3da854;
    }
    
    .btn-reschedule {
        background: #ffa426;
        color: white;
    }
    
    .btn-reschedule:hover {
        background: #e69520;
    }
    
    .priority-badge {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .priority-urgent { background: #fc544b; color: white; }
    .priority-high { background: #ffa426; color: white; }
    .priority-medium { background: #6777ef; color: white; }
    .priority-low { background: #95aac9; color: white; }
    
    .type-badge {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        background: #e3eaef;
        color: #6c757d;
        font-weight: 500;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #95aac9;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }
</style>

<div class="followup-dashboard">
    <div class="client-header" style="padding-bottom: 20px;">
        <div>
            <h1><i class="fas fa-calendar-check"></i> My Follow-ups</h1>
            <p style="color: #6c757d; margin-top: 5px;">Manage your scheduled follow-ups and track progress</p>
        </div>
        <div class="client-status">
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Leads
            </a>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="followup-stats">
        <div class="stat-card overdue">
            <h3>Overdue</h3>
            <div class="stat-value">{{ $stats['overdue'] }}</div>
        </div>
        <div class="stat-card today">
            <h3>Due Today</h3>
            <div class="stat-value">{{ $stats['today'] }}</div>
        </div>
        <div class="stat-card upcoming">
            <h3>Upcoming</h3>
            <div class="stat-value">{{ $stats['upcoming'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Completed This Week</h3>
            <div class="stat-value">{{ $stats['completed_this_week'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Completion Rate</h3>
            <div class="stat-value">{{ $stats['completion_rate'] }}%</div>
        </div>
    </div>
    
    <!-- Overdue Follow-ups -->
    @if($overdue->count() > 0)
    <div class="followup-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-exclamation-triangle" style="color: #fc544b;"></i>
                Overdue Follow-ups
                <span class="badge-count">{{ $overdue->count() }}</span>
            </h2>
        </div>
        @foreach($overdue as $followup)
            @include('crm.leads.followups.partials.followup-card', ['followup' => $followup, 'status' => 'overdue'])
        @endforeach
    </div>
    @endif
    
    <!-- Today's Follow-ups -->
    <div class="followup-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-calendar-day" style="color: #ffa426;"></i>
                Due Today
                <span class="badge-count">{{ $today->count() }}</span>
            </h2>
        </div>
        @if($today->count() > 0)
            @foreach($today as $followup)
                @include('crm.leads.followups.partials.followup-card', ['followup' => $followup, 'status' => 'today'])
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>No follow-ups due today. Great job!</p>
            </div>
        @endif
    </div>
    
    <!-- Upcoming Follow-ups -->
    <div class="followup-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-clock" style="color: #47c363;"></i>
                Upcoming Follow-ups
                <span class="badge-count">{{ $upcoming->count() }}</span>
            </h2>
        </div>
        @if($upcoming->count() > 0)
            @foreach($upcoming as $followup)
                @include('crm.leads.followups.partials.followup-card', ['followup' => $followup, 'status' => 'upcoming'])
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-calendar"></i>
                <p>No upcoming follow-ups scheduled</p>
            </div>
        @endif
    </div>
</div>

<!-- Complete Followup Modal -->
<div class="modal fade" id="completeFollowupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Follow-up</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="completeFollowupForm">
                @csrf
                <input type="hidden" id="complete_followup_id" name="followup_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Outcome <span class="text-danger">*</span></label>
                        <select name="outcome" class="form-control" required>
                            <option value="">Select outcome</option>
                            <option value="interested">Interested</option>
                            <option value="not_interested">Not Interested</option>
                            <option value="callback_later">Callback Later</option>
                            <option value="converted">Converted to Client</option>
                            <option value="no_response">No Response</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Add notes about this follow-up..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="schedule_next" value="1" checked> 
                            Auto-schedule next follow-up based on outcome
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Follow-up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reschedule Followup Modal -->
<div class="modal fade" id="rescheduleFollowupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reschedule Follow-up</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="rescheduleFollowupForm">
                @csrf
                <input type="hidden" id="reschedule_followup_id" name="followup_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>New Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="new_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Reason for Rescheduling</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Why are you rescheduling?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reschedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Complete followup
    window.completeFollowup = function(id) {
        $('#complete_followup_id').val(id);
        $('#completeFollowupModal').modal('show');
    };
    
    $('#completeFollowupForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#complete_followup_id').val();
        
        $.ajax({
            url: `/leads/followups/${id}/complete`,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#completeFollowupModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error completing follow-up');
            }
        });
    });
    
    // Reschedule followup
    window.rescheduleFollowup = function(id) {
        $('#reschedule_followup_id').val(id);
        $('#rescheduleFollowupModal').modal('show');
    };
    
    $('#rescheduleFollowupForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#reschedule_followup_id').val();
        
        $.ajax({
            url: `/leads/followups/${id}/reschedule`,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#rescheduleFollowupModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error rescheduling follow-up');
            }
        });
    });
});
</script>
@endsection

