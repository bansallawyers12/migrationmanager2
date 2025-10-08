{{-- Work Experience Field Component --}}
@props(['index', 'experience'])

<div class="repeatable-section">
    <button type="button" class="remove-item-btn" title="Remove Experience" onclick="removeExperienceField(this)">
        <i class="fas fa-trash"></i>
    </button>
    
    <input type="hidden" name="experience_id[{{ $index }}]" value="{{ $experience->id ?? '' }}">
    
    <div class="content-grid">
        <div class="form-group">
            <label>Company</label>
            <input type="text" 
                   name="company[{{ $index }}]" 
                   value="{{ $experience->company ?? '' }}" 
                   placeholder="Company">
        </div>
        
        <div class="form-group">
            <label>Position</label>
            <input type="text" 
                   name="position[{{ $index }}]" 
                   value="{{ $experience->position ?? '' }}" 
                   placeholder="Position">
        </div>
        
        <div class="form-group">
            <label>Start Date</label>
            <input type="text" 
                   name="exp_start_date[{{ $index }}]" 
                   value="{{ $experience && $experience->start_date ? date('d/m/Y', strtotime($experience->start_date)) : '' }}" 
                   placeholder="dd/mm/yyyy" 
                   class="date-picker">
        </div>
        
        <div class="form-group">
            <label>End Date</label>
            <input type="text" 
                   name="exp_end_date[{{ $index }}]" 
                   value="{{ $experience && $experience->end_date ? date('d/m/Y', strtotime($experience->end_date)) : '' }}" 
                   placeholder="dd/mm/yyyy" 
                   class="date-picker">
        </div>
    </div>
</div>
