{{-- Phone Number Field Component --}}
@props(['index', 'contact'])

<div class="repeatable-section">
    <button type="button" class="remove-item-btn" title="Remove Phone" onclick="removePhoneField(this)">
        <i class="fas fa-trash"></i>
    </button>
    
    <input type="hidden" name="contact_id[{{ $index }}]" value="{{ $contact->id }}">
    
    <div class="content-grid">
        <div class="form-group">
            <label>Type</label>
            <select name="contact_type_hidden[{{ $index }}]" class="contact-type-selector">
                <option value="Personal" {{ $contact->contact_type == 'Personal' ? 'selected' : '' }}>Personal</option>
                <option value="Work" {{ $contact->contact_type == 'Work' ? 'selected' : '' }}>Work</option>
                <option value="Mobile" {{ $contact->contact_type == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                <option value="Business" {{ $contact->contact_type == 'Business' ? 'selected' : '' }}>Business</option>
                <option value="Secondary" {{ $contact->contact_type == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                <option value="Father" {{ $contact->contact_type == 'Father' ? 'selected' : '' }}>Father</option>
                <option value="Mother" {{ $contact->contact_type == 'Mother' ? 'selected' : '' }}>Mother</option>
                <option value="Brother" {{ $contact->contact_type == 'Brother' ? 'selected' : '' }}>Brother</option>
                <option value="Sister" {{ $contact->contact_type == 'Sister' ? 'selected' : '' }}>Sister</option>
                <option value="Uncle" {{ $contact->contact_type == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                <option value="Aunt" {{ $contact->contact_type == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                <option value="Cousin" {{ $contact->contact_type == 'Cousin' ? 'selected' : '' }}>Cousin</option>
                <option value="Others" {{ $contact->contact_type == 'Others' ? 'selected' : '' }}>Others</option>
                <option value="Partner" {{ $contact->contact_type == 'Partner' ? 'selected' : '' }}>Partner</option>
                <option value="Not In Use" {{ $contact->contact_type == 'Not In Use' ? 'selected' : '' }}>Not In Use</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Number</label>
            <div class="cus_field_input flex-container">
                <div class="country_code">
                    <select name="country_code[{{ $index }}]" class="country-code-input">
                        <option value="+61" {{ $contact->country_code == '+61' ? 'selected' : '' }}>🇦🇺 +61</option>
                        <option value="+91" {{ $contact->country_code == '+91' ? 'selected' : '' }}>🇮🇳 +91</option>
                        <option value="+1" {{ $contact->country_code == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                        <option value="+44" {{ $contact->country_code == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                        <option value="+49" {{ $contact->country_code == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                        <option value="+33" {{ $contact->country_code == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                        <option value="+86" {{ $contact->country_code == '+86' ? 'selected' : '' }}>🇨🇳 +86</option>
                        <option value="+81" {{ $contact->country_code == '+81' ? 'selected' : '' }}>🇯🇵 +81</option>
                        <option value="+82" {{ $contact->country_code == '+82' ? 'selected' : '' }}>🇰🇷 +82</option>
                        <option value="+65" {{ $contact->country_code == '+65' ? 'selected' : '' }}>🇸🇬 +65</option>
                        <option value="+60" {{ $contact->country_code == '+60' ? 'selected' : '' }}>🇲🇾 +60</option>
                        <option value="+66" {{ $contact->country_code == '+66' ? 'selected' : '' }}>🇹🇭 +66</option>
                        <option value="+63" {{ $contact->country_code == '+63' ? 'selected' : '' }}>🇵🇭 +63</option>
                        <option value="+84" {{ $contact->country_code == '+84' ? 'selected' : '' }}>🇻🇳 +84</option>
                        <option value="+62" {{ $contact->country_code == '+62' ? 'selected' : '' }}>🇮🇩 +62</option>
                    </select>
                </div>
                <input type="tel" 
                       name="phone[{{ $index }}]" 
                       value="{{ $contact->phone }}" 
                       placeholder="Phone Number" 
                       class="phone-number-input phone-width" 
                       autocomplete="off">
            </div>
        </div>
    </div>
</div>

