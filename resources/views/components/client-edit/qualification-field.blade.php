{{-- Qualification Field Component --}}
@props(['index', 'qualification'])

<div class="repeatable-section">
    <button type="button" class="remove-item-btn" title="Remove Qualification" onclick="removeQualificationField(this)">
        <i class="fas fa-trash"></i>
    </button>
    
    <input type="hidden" name="qualification_id[{{ $index }}]" value="{{ $qualification->id ?? '' }}">
    
    <div class="content-grid">
        <div class="form-group">
            <label>Qualification</label>
            <input type="text" 
                   name="qualification[{{ $index }}]" 
                   value="{{ $qualification->qualification ?? '' }}" 
                   placeholder="Qualification">
        </div>
        
        <div class="form-group">
            <label>Institution</label>
            <input type="text" 
                   name="institution[{{ $index }}]" 
                   value="{{ $qualification->institution ?? '' }}" 
                   placeholder="Institution">
        </div>
        
        <div class="form-group">
            <label>Country</label>
            <input type="text" 
                   name="qual_country[{{ $index }}]" 
                   value="{{ $qualification->country ?? '' }}" 
                   placeholder="Country">
        </div>
        
        <div class="form-group">
            <label>Year</label>
            <input type="text" 
                   name="year[{{ $index }}]" 
                   value="{{ $qualification->year ?? '' }}" 
                   placeholder="Year">
        </div>
    </div>
</div>
