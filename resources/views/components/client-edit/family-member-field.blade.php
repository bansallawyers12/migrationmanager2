{{-- Unified Family Member Field Component (Partners & Children) --}}
@props(['index', 'member', 'type' => 'partner', 'relationshipOptions' => []])

@php
    $fieldPrefix = $type === 'partner' ? 'partner' : 'children';
    $relationshipIdField = $type === 'partner' ? 'relationship_id' : 'children_relationship_id';
    $memberIdField = $type === 'partner' ? 'partner_id' : 'children_id';
@endphp

<div class="repeatable-section">
    <button type="button" 
            class="remove-item-btn" 
            title="Remove {{ ucfirst($type) }}" 
            onclick="removePartnerRow(this, '{{ $type }}', {{ $member->id }})">
        <i class="fas fa-times-circle"></i>
    </button>
    
    <input type="hidden" name="{{ $memberIdField }}[{{ $index }}]" class="partner-id" value="{{ $member->related_client_id }}">
    <input type="hidden" name="{{ $relationshipIdField }}[{{ $index }}]" value="{{ $member->id }}">
    
    <div class="content-grid">
        <div class="form-group">
            <label>Details</label>
            <input type="text" 
                   name="{{ $fieldPrefix }}_details[{{ $index }}]" 
                   class="partner-details" 
                   value="{{ $member->relatedClient ? $member->relatedClient->first_name . ' ' . $member->relatedClient->last_name . ' (' . $member->relatedClient->email . ', ' . $member->relatedClient->phone . ', ' . $member->relatedClient->client_id . ')' : $member->details }}" 
                   placeholder="Search by Name, Email, Client ID, or Phone" 
                   readonly>
            <div class="autocomplete-items"></div>
        </div>
        
        <div class="form-group">
            <label>Relationship Type</label>
            <select name="{{ $fieldPrefix }}_relationship_type[{{ $index }}]" required>
                <option value="">Select Relationship</option>
                @foreach($relationshipOptions as $option)
                    <option value="{{ $option }}" {{ $member->relationship_type == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>Gender <span class="text-danger">*</span></label>
            <select name="{{ $fieldPrefix }}_gender[{{ $index }}]" required>
                <option value="">Select Gender</option>
                <option value="Male" {{ $member->gender == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $member->gender == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ $member->gender == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Company Type</label>
            <select name="{{ $fieldPrefix }}_company_type[{{ $index }}]">
                <option value="">Select Company Type</option>
                <option value="Accompany Member" {{ $member->company_type == 'Accompany Member' ? 'selected' : '' }}>Accompany Member</option>
                <option value="Non-Accompany Member" {{ $member->company_type == 'Non-Accompany Member' ? 'selected' : '' }}>Non-Accompany Member</option>
            </select>
        </div>
    </div>
    
    <div class="partner-extra-fields hidden-fields">
        <div class="content-grid single-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="{{ $fieldPrefix }}_email[{{ $index }}]" placeholder="Email">
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="{{ $fieldPrefix }}_first_name[{{ $index }}]" placeholder="First Name">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="{{ $fieldPrefix }}_last_name[{{ $index }}]" placeholder="Last Name">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="{{ $fieldPrefix }}_phone[{{ $index }}]" placeholder="Phone">
            </div>
            <div class="form-group">
                <label>DOB</label>
                <input type="text" name="{{ $fieldPrefix }}_dob[{{ $index }}]" placeholder="dd/mm/yyyy" class="date-picker">
            </div>
        </div>
    </div>
</div>

