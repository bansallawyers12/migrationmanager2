/**
 * Client Edit Page JavaScript
 * Contains all functionality for the client edit form
 */

// ===== ULTRA-ROBUST TAB FUNCTIONALITY - IMMEDIATE DEFINITION =====

// Define openTab function IMMEDIATELY in global scope
window.openTab = function(evt, tabName) {
    try {
        // Prevent default behavior
        if (evt && evt.preventDefault) {
            evt.preventDefault();
        }
        
        // Hide all tab content
        var tabcontent = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        
        // Remove active class from all tab buttons
        var tablinks = document.getElementsByClassName("tab-button");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        
        // Show the selected tab content
        var targetTab = document.getElementById(tabName);
        if (targetTab) {
            targetTab.style.display = "block";
        } else {
            console.error('Tab content not found:', tabName);
        }
        
        // Add active class to the clicked button
        if (evt && evt.currentTarget) {
            evt.currentTarget.className += " active";
        }
        
        // Fallback: if evt.currentTarget is not available, find the button by onclick attribute
        if (!evt || !evt.currentTarget) {
            var buttons = document.querySelectorAll('.tab-button');
            for (var i = 0; i < buttons.length; i++) {
                if (buttons[i].getAttribute('onclick') && buttons[i].getAttribute('onclick').includes(tabName)) {
                    buttons[i].className += " active";
                    break;
                }
            }
        }
        
        console.log('Tab switched to:', tabName);
        
    } catch (error) {
        console.error('Error in openTab function:', error);
        // Fallback: try to show the tab content directly
        try {
            var fallbackTab = document.getElementById(tabName);
            if (fallbackTab) {
                // Hide all tabs first
                var allTabs = document.getElementsByClassName("tab-content");
                for (var i = 0; i < allTabs.length; i++) {
                    allTabs[i].style.display = "none";
                }
                // Show target tab
                fallbackTab.style.display = "block";
            }
        } catch (fallbackError) {
            console.error('Fallback also failed:', fallbackError);
        }
    }
};

// Ensure openTab is globally available
window.openTab = openTab;

// Initialize tab functionality when DOM is ready
function initializeTabs() {
    try {
        // Set up event listeners for tab buttons as backup
        var tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(function(button) {
            // Remove existing onclick to prevent conflicts
            var onclickAttr = button.getAttribute('onclick');
            if (onclickAttr && onclickAttr.includes('openTab')) {
                // Extract tab name from onclick
                var match = onclickAttr.match(/openTab\(event,\s*['"]([^'"]+)['"]\)/);
                if (match) {
                    var tabName = match[1];
                    // Add event listener as backup
                    button.addEventListener('click', function(e) {
                        openTab(e, tabName);
                    });
                }
            }
        });
        
        // Ensure first tab is active by default
        var firstTab = document.querySelector('.tab-button');
        var firstTabContent = document.getElementById('personalTab');
        if (firstTab && firstTabContent) {
            firstTab.classList.add('active');
            firstTabContent.style.display = 'block';
        }
        
        console.log('Tab functionality initialized successfully');
        
    } catch (error) {
        console.error('Error initializing tabs:', error);
    }
}

// Define validateForm function IMMEDIATELY to prevent "not defined" errors
window.validateForm = function() {
    const form = document.getElementById('editClientForm');
    const errors = [];
    
    // Check all required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(function(field) {
        const fieldName = field.name;
        let isValid = false;
        
        // Debug log for gender fields
        if (fieldName && (fieldName.includes('gender') || fieldName.includes('siblings_gender'))) {
            const tabContent = field.closest('.tab-content');
            const tabId = tabContent ? tabContent.id : 'unknown';
            console.log('Checking gender field:', fieldName, 'Value:', field.value, 'SelectedIndex:', field.selectedIndex, 'Tab:', tabId);
        }
        
        // Handle different field types
        if (field.tagName === 'SELECT') {
            // For select fields, check if a valid option is selected (not the first empty option)
            isValid = field.selectedIndex > 0 && field.value !== '';
        } else {
            // For other fields (input, textarea), check if they have a value
            isValid = field.value && field.value.trim() !== '';
        }
        
        // If field is valid, skip to next field
        if (isValid) {
            return;
        }
        
        // Field is invalid, add error with more context
        const label = field.closest('.form-group')?.querySelector('label')?.textContent?.trim() || field.name || 'Unknown field';
        
        // Add context about which tab/section the field is in
        let context = '';
        const tabContent = field.closest('.tab-content');
        if (tabContent) {
            const tabId = tabContent.id;
            if (tabId === 'personalTab') {
                context = ' (Personal tab)';
            } else if (tabId === 'familyTab') {
                context = ' (Family Information tab)';
            } else if (tabId === 'visaPassportCitizenshipTab') {
                context = ' (Visa, Passport & Citizenship tab)';
            } else if (tabId === 'addressTravelTab') {
                context = ' (Address & Travel tab)';
            } else if (tabId === 'skillsEducationTab') {
                context = ' (Skills & Education tab)';
            } else if (tabId === 'otherInformationTab') {
                context = ' (Other Information tab)';
            } else if (tabId === 'eoiReferenceTab') {
                context = ' (EOI Reference tab)';
            }
        }
        
        errors.push(`"${label}" is required${context}`);
    });
    
    // If there are errors, show them in alert and prevent submission
    if (errors.length > 0) {
        const errorMessage = 'Please fix the following errors:\n\n' + errors.join('\n');
        alert(errorMessage);
        return false;
    }
    
    return true;
};

// ADDITIONAL FALLBACK: Set up event listeners as backup
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(function(button) {
        var onclickAttr = button.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('openTab')) {
            var match = onclickAttr.match(/openTab\(event,\s*['"]([^'"]+)['"]\)/);
            if (match) {
                var tabName = match[1];
                // Add event listener as backup
                button.addEventListener('click', function(e) {
                    if (typeof window.openTab === 'function') {
                        window.openTab(e, tabName);
                    } else {
                        // Direct fallback if function still not available
                        try {
                            var tabcontent = document.getElementsByClassName("tab-content");
                            for (var i = 0; i < tabcontent.length; i++) {
                                tabcontent[i].style.display = "none";
                            }
                            var tablinks = document.getElementsByClassName("tab-button");
                            for (var i = 0; i < tablinks.length; i++) {
                                tablinks[i].className = tablinks[i].className.replace(" active", "");
                            }
                            var targetTab = document.getElementById(tabName);
                            if (targetTab) {
                                targetTab.style.display = "block";
                            }
                            e.currentTarget.className += " active";
                        } catch (directError) {
                            console.error('Direct fallback failed:', directError);
                        }
                    }
                });
            }
        }
    });
});

// ===== END TAB FUNCTIONALITY =====

// Initialize arrays to track IDs of records marked for deletion
let phoneNumbersToDelete = [];
let emailsToDelete = [];

// Cache visa types to avoid multiple AJAX calls
let visaTypesCache = null;
let countriesCache = null;

/**
 * Function to fetch visa types via AJAX
 */
async function fetchVisaTypes() {
    if (visaTypesCache) {
        return visaTypesCache; // Return cached data if available
    }

    // Get the route from window object (set from blade)
    const route = window.editClientConfig?.visaTypesRoute;
    if (!route) {
        console.error('Visa types route not configured');
        return [];
    }

    try {
        const response = await $.ajax({
            url: route,
            method: 'GET',
            dataType: 'json',
        });
        visaTypesCache = response; // Cache the response
        return response;
    } catch (error) {
        console.error('Error fetching visa types:', error);
        return [];
    }
}

/**
 * Function to fetch countries via AJAX
 */
async function fetchCountries() {
    if (countriesCache) {
        return countriesCache; // Return cached data if available
    }

    // Get the route from window object (set from blade)
    const route = window.editClientConfig?.countriesRoute;
    if (!route) {
        console.error('Countries route not configured');
        return [];
    }

    try {
        const response = await $.ajax({
            url: route,
            method: 'GET',
            dataType: 'json',
        });
        countriesCache = response; // Cache the response
        return response;
    } catch (error) {
        console.error('Error fetching countries:', error);
        return [];
    }
}

/**
 * Modified addPartnerRow to handle different types with hidden extra fields by default
 */
function addPartnerRow(type) {
    const containerId = type + 'Container';
    const container = document.getElementById(containerId);
    const index = container.children.length;

    let relationshipOptions = '';
    switch (type) {
        case 'children':
            relationshipOptions = `
                <option value="">Select Relationship</option>
                <option value="Son">Son</option>
                <option value="Daughter">Daughter</option>
                <option value="Step Son">Step Son</option>
                <option value="Step Daughter">Step Daughter</option>
            `;
            break;
        case 'parent':
            relationshipOptions = `
                <option value="">Select Relationship</option>
                <option value="Father">Father</option>
                <option value="Mother">Mother</option>
                <option value="Step Father">Step Father</option>
                <option value="Step Mother">Step Mother</option>
            `;
            break;
        case 'siblings':
            relationshipOptions = `
                <option value="">Select Relationship</option>
                <option value="Brother">Brother</option>
                <option value="Sister">Sister</option>
                <option value="Step Brother">Step Brother</option>
                <option value="Step Sister">Step Sister</option>
            `;
            break;
        case 'others':
            relationshipOptions = `
                <option value="">Select Relationship</option>
                <option value="Cousin">Cousin</option>
                <option value="Friend">Friend</option>
                <option value="Uncle">Uncle</option>
                <option value="Aunt">Aunt</option>
                <option value="Grandchild">Grandchild</option>
                <option value="Granddaughter">Granddaughter</option>
                <option value="Grandparent">Grandparent</option>
                <option value="Niece">Niece</option>
                <option value="Nephew">Nephew</option>
                <option value="Grandfather">Grandfather</option>
            `;
            break;
        default: // Partner
            relationshipOptions = `
                <option value="">Select Relationship</option>
                <option value="Husband">Husband</option>
                <option value="Wife">Wife</option>
                <option value="Ex-Wife">Ex-Wife</option>
                <option value="Defacto">Defacto</option>
            `;
    }

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove ${type.charAt(0).toUpperCase() + type.slice(1)}" onclick="removePartnerRow(this, '${type}')"><i class="fas fa-times-circle"></i></button>
            <input type="hidden" name="${type}_id[${index}]" class="partner-id">
            <div class="content-grid">
                <div class="form-group">
                    <label>Details</label>
                    <input type="text" name="${type}_details[${index}]" class="partner-details" placeholder="Search by Name, Email, Client ID, or Phone">
                    <div class="autocomplete-items"></div>
                </div>
                <div class="form-group">
                    <label>Relationship Type</label>
                    <select name="${type}_relationship_type[${index}]" required>
                        ${relationshipOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label>Gender <span class="text-danger">*</span></label>
                    <select name="${type}_gender[${index}]" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Company Type</label>
                    <select name="${type}_company_type[${index}]">
                        <option value="">Select Company Type</option>
                        <option value="Accompany Member">Accompany Member</option>
                        <option value="Non-Accompany Member">Non-Accompany Member</option>
                    </select>
                </div>
            </div>
            <div class="partner-extra-fields" style="display: none;">
                <div class="content-grid single-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="${type}_email[${index}]" placeholder="Enter Email">
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="${type}_first_name[${index}]" placeholder="Enter First Name">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="${type}_last_name[${index}]" placeholder="Enter Last Name">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="${type}_phone[${index}]" placeholder="Enter Phone">
                    </div>
                    <div class="form-group">
                        <label>DOB</label>
                        <input type="text" name="${type}_dob[${index}]"  placeholder="dd/mm/yyyy" class="date-picker">
                    </div>
                </div>
            </div>
        </div>
    `);

    // Reinitialize datepickers for the newly added fields
    initializeDatepickers();
}

/**
 * Modified removePartnerRow to handle different types
 */
function removePartnerRow(button, type, relationshipId = null) {
    const section = button.closest('.repeatable-section');
    const confirmDelete = confirm(`Are you sure you want to delete this ${type} record?`);

    if (confirmDelete) {
        if (relationshipId) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `delete_${type}_ids[]`;
            hiddenInput.value = relationshipId;
            document.getElementById('editClientForm').appendChild(hiddenInput);
        }
        section.remove();
    }
}

/**
 * Function to add a new EOI Reference row
 */
function addEoiReference() {
    const container = document.getElementById('eoiReferencesContainer');
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove EOI Reference" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                <div class="form-group">
                    <label>EOI Number</label>
                    <input type="text" name="EOI_number[${index}]" placeholder="Enter EOI Number">
                </div>
                <div class="form-group">
                    <label>Subclass</label>
                    <input type="text" name="EOI_subclass[${index}]" placeholder="Enter Subclass">
                </div>
                <div class="form-group">
                    <label>Occupation</label>
                    <input type="text" name="EOI_occupation[${index}]" placeholder="Enter Occupation">
                </div>
                <div class="form-group">
                    <label>Point</label>
                    <input type="text" name="EOI_point[${index}]" placeholder="Enter Point">
                </div>
                <div class="form-group">
                    <label>State</label>
                    <input type="text" name="EOI_state[${index}]" placeholder="Enter State">
                </div>
                <div class="form-group">
                    <label>Submission Date</label>
                    <input type="text" name="EOI_submission_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
                <div class="form-group">
                    <label>ROI</label>
                    <input type="text" name="EOI_ROI[${index}]" placeholder="Enter ROI">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="password" name="EOI_password[${index}]" placeholder="Enter Password" class="eoi-password-input" data-index="${index}">
                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-password" data-index="${index}" title="Show/Hide Password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `);

    // Reinitialize datepickers for the newly added field
    initializeDatepickers();
}

/**
 * Toggle Visa Details and Visa Expiry Verified based on passport country
 */
function toggleVisaDetails() {
    const passportCountrySelector = document.getElementById('passportCountry');
    const visaDetailsSection = document.getElementById('visaDetailsSection');
    const addVisaButton = document.querySelector('button[onclick="addVisaDetail()"]');
    const visaExpiryVerifiedContainer = document.getElementById('visaExpiryVerifiedContainer');

    const isAustralia = passportCountrySelector.value === 'Australia';

    if (isAustralia) {
        visaDetailsSection.style.display = 'none';
        addVisaButton.style.display = 'none';
        visaExpiryVerifiedContainer.style.display = 'none';
    } else {
        visaDetailsSection.style.display = 'block';
        addVisaButton.style.display = 'block';
        visaExpiryVerifiedContainer.style.display = 'flex';
    }
}

/**
 * Add Passport Detail
 */
function addPassportDetail() {
    const container = document.getElementById('passportDetailsContainer');
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Passport" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Passport #</label>
                    <input type="text" name="passports[${index}][passport_number]" placeholder="Enter Passport Number">
                </div>
                <div class="form-group">
                    <label>Issue Date</label>
                    <input type="text" name="passports[${index}][issue_date]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="text" name="passports[${index}][expiry_date]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
            </div>
        </div>
    `);

    // Reinitialize datepickers for the newly added fields
    initializeDatepickers();
}

/**
 * Add Address
 */
function addAddress() {
    const container = document.getElementById('addressContainer');
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Address" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address[${index}]" rows="2" placeholder="Enter Address"></textarea>
                </div>
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="zip[${index}]" placeholder="Enter Postal Code">
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="text" name="address_start_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="text" name="address_end_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
            </div>
        </div>
    `);

    // Reinitialize datepickers for the newly added fields
    initializeDatepickers();
}

/**
 * Add Travel Detail
 */
async function addTravelDetail() {
    const container = document.getElementById('travelDetailsContainer');
    const index = container.children.length;

    // Fetch countries
    const countries = await fetchCountries();

    // Build the options for the country dropdown
    let countryOptionsHtml = '<option value="">Select Country</option>';
    countries.forEach(country => {
        countryOptionsHtml += `<option value="${country}">${country}</option>`;
    });

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Travel" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Country Visited</label>
                    <select name="travel_country_visited[${index}]">
                        ${countryOptionsHtml}
                    </select>
                </div>
                <div class="form-group">
                    <label>Arrival Date</label>
                    <input type="text" name="travel_arrival_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
                <div class="form-group">
                    <label>Departure Date</label>
                    <input type="text" name="travel_departure_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
                <div class="form-group">
                    <label>Travel Purpose</label>
                    <input type="text" name="travel_purpose[${index}]" placeholder="Enter Travel Purpose">
                </div>
            </div>
        </div>
    `);

    // Reinitialize datepickers for the newly added fields
    initializeDatepickers();
}

/**
 * Function to calculate age from date of birth (expects dd/mm/yyyy format)
 */
function calculateAge(dob) {
    if (!dob || !/^\d{2}\/\d{2}\/\d{4}$/.test(dob)) return '';

    try {
        const [day, month, year] = dob.split('/').map(Number);
        const dobDate = new Date(year, month - 1, day);
        if (isNaN(dobDate.getTime())) return ''; // Invalid date

        const today = new Date();
        let years = today.getFullYear() - dobDate.getFullYear();
        let months = today.getMonth() - dobDate.getMonth();

        if (months < 0) {
            years--;
            months += 12;
        }

        if (today.getDate() < dobDate.getDate()) {
            months--;
            if (months < 0) {
                years--;
                months += 12;
            }
        }

        return years + ' years ' + months + ' months';
    } catch (e) {
        return '';
    }
}

/**
 * Add Phone Number (Updated to exclude verification slider in repeatable section)
 */
function addPhoneNumber() {
    const container = document.getElementById('phoneNumbersContainer');
    const index = container.children.length;
    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Phone" onclick="this.parentElement.remove(); validatePersonalPhoneNumbers();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Type</label>
                    <select name="contact_type_hidden[${index}]" class="contact-type-selector">
                        <option value="Personal">Personal</option>
                        <option value="Work">Work</option>
                        <option value="Mobile">Mobile</option>
                        <option value="Business">Business</option>
                        <option value="Secondary">Secondary</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Brother">Brother</option>
                        <option value="Sister">Sister</option>
                        <option value="Uncle">Uncle</option>
                        <option value="Aunt">Aunt</option>
                        <option value="Cousin">Cousin</option>
                        <option value="Others">Others</option>
                        <option value="Partner">Partner</option>
                        <option value="Not In Use">Not In Use</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Number</label>
                    <div class="cus_field_input" style="display:flex;">
                        <div class="country_code">
                            <input class="telephone country-code-input" id="telephone" type="tel" name="country_code[${index}]" style="width: 55px;height: 42px;" readonly >
                        </div>
                        <input type="tel" name="phone[${index}]" placeholder="Enter Phone Number" style="width: 230px;" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    `);
    $(".telephone").intlTelInput();
    validatePersonalPhoneNumbers();
}

/**
 * Function to validate that "Personal" phone numbers are unique
 */
function validatePersonalPhoneNumbers() {
    const container = document.getElementById('phoneNumbersContainer');
    const sections = container.getElementsByClassName('repeatable-section');
    const personalPhones = {};

    // Clear previous error messages
    Array.from(sections).forEach(section => {
        const errorSpan = section.querySelector('.text-danger');
        if (errorSpan) errorSpan.remove();
    });

    // Check for duplicate "Personal" phone numbers
    Array.from(sections).forEach((section, index) => {
        const type = section.querySelector('.contact-type-selector').value;
        const countryCode = section.querySelector('.country-code-input').value;
        const phone = section.querySelector('.phone-number-input').value;
        const fullPhone = countryCode + phone;

        if (type === 'Personal' && phone) {
            if (personalPhones[fullPhone]) {
                // Duplicate found
                const errorMessage = `<span class="text-danger">Personal phone number ${fullPhone} is already used in another entry.</span>`;
                section.querySelector('.input-group').insertAdjacentHTML('afterend', errorMessage);
                // Disable the submit button
                document.querySelector('button[type="submit"]').disabled = true;
            } else {
                personalPhones[fullPhone] = true;
            }
        }
    });

    // Re-enable the submit button if no duplicates are found
    if (!Object.keys(personalPhones).some(phone => personalPhones[phone] === true && Object.keys(personalPhones).filter(p => p === phone).length > 1)) {
        document.querySelector('button[type="submit"]').disabled = false;
    }
}

/**
 * Add Email Address (Updated to exclude verification slider in repeatable section)
 */
function addEmailAddress() {
    const container = document.getElementById('emailAddressesContainer');
    const index = container.children.length;
    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Email" onclick="this.parentElement.remove(); validatePersonalEmailTypes();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Type</label>
                    <select name="email_type_hidden[${index}]" class="email-type-selector">
                        <option value="Personal">Personal</option>
                        <option value="Work">Work</option>
                        <option value="Business">Business</option>

                        <option value="Mobile">Mobile</option>
                        <option value="Secondary">Secondary</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Brother">Brother</option>
                        <option value="Sister">Sister</option>
                        <option value="Uncle">Uncle</option>
                        <option value="Aunt">Aunt</option>
                        <option value="Cousin">Cousin</option>
                        <option value="Others">Others</option>
                        <option value="Partner">Partner</option>
                        <option value="Not In Use">Not In Use</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email[${index}]" placeholder="Enter Email Address">
                </div>
            </div>
        </div>
    `);
    validatePersonalEmailTypes();
}

/**
 * Function to validate that there is at most one "Personal" type for emails
 */
function validatePersonalEmailTypes() {
    const container = document.getElementById('emailAddressesContainer');
    const sections = container.getElementsByClassName('repeatable-section');
    let personalCount = 0;

    // Clear previous error messages
    Array.from(sections).forEach(section => {
        const errorSpan = section.querySelector('.text-danger-email-personal');
        if (errorSpan) errorSpan.remove();
    });

    // Count "Personal" types
    Array.from(sections).forEach((section, index) => {
        const type = section.querySelector('.email-type-selector').value;
        if (type === 'Personal') {
            personalCount++;
            if (personalCount > 1) {
                // Display error message
                const errorMessage = `<span class="text-danger text-danger-email-personal">Only one email address can be of type Personal.</span>`;
                section.querySelector('.form-group').insertAdjacentHTML('afterend', errorMessage);
            }
        }
    });

    // Enable or disable the submit button based on validation
    const submitButton = document.querySelector('button[type="submit"]');
    if (personalCount > 1) {
        submitButton.disabled = true;
    } else {
        submitButton.disabled = false;
    }

    return personalCount <= 1;
}

/**
 * Add Visa Detail
 */
async function addVisaDetail() {
    const container = document.getElementById('visaDetailsContainer');
    const index = container.children.length;

    // Fetch visa types
    const visaTypes = await fetchVisaTypes();

    // Build the options for the dropdown
    let optionsHtml = '<option value="">Select Visa Type</option>';
    visaTypes.forEach(visa => {
        const nickName = visa.nick_name ? ` (${visa.nick_name})` : '';
        optionsHtml += `<option value="${visa.id}">${visa.title}${nickName}</option>`;
    });

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Visa"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Visa Type / Subclass</label>
                    <select name="visas[${index}][visa_type]" class="visa-type-field">
                        ${optionsHtml}
                    </select>
                </div>
                <div class="form-group">
                    <label>Visa Expiry Date</label>
                    <input type="text" name="visas[${index}][expiry_date]" placeholder="dd/mm/yyyy" class="visa-expiry-field date-picker">
                </div>
                <div class="form-group">
                    <label>Visa Grant Date</label>
                    <input type="text" name="visas[${index}][grant_date]" placeholder="dd/mm/yyyy" class="visa-grant-field date-picker">
                </div>
                <div class="form-group">
                    <label>Visa Description</label>
                    <input type="text" name="visas[${index}][description]" class="visa-description-field">
                </div>
            </div>
        </div>
    `);

    // Reinitialize datepicker for the newly added field
    initializeDatepickers();
    toggleVisaDetails();
}

/**
 * Initialize Datepickers for both empty and non-empty fields
 */
function initializeDatepickers() {
    $('.date-picker').each(function() {
        const $this = $(this);
        const currentValue = $this.val(); // Get the current value of the field

        // Initialize the datepicker regardless of whether the field is empty
        $this.daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false, // Prevent the datepicker from auto-filling the field
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ],
                firstDay: 1
            },
            minDate: '01/01/1000',
            minYear: 1000,
            maxYear: parseInt(moment().format('YYYY')) + 50,
            startDate: currentValue ? moment(currentValue, 'DD/MM/YYYY') : undefined, // Use existing value if present, otherwise no default date
            endDate: undefined
        }).on('apply.daterangepicker', function(ev, picker) {
            // On apply, set the selected date
            $this.val(picker.startDate.format('DD/MM/YYYY'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            // On cancel, clear the field
            $this.val('');
        });

        // If the field was empty, ensure it remains empty after initialization
        if (!currentValue) {
            $this.val('');
        }
    });
}

/**
 * Toggle Spouse Details Section based on Marital Status
 */
function toggleSpouseDetailsSection() {
    const maritalStatus = document.getElementById('martialStatus').value;
    const spouseDetailsSection = document.getElementById('spouseDetailsSection');

    if (maritalStatus === 'Married' || maritalStatus === 'Defacto') {
        spouseDetailsSection.style.display = 'block';
    } else {
        spouseDetailsSection.style.display = 'none';
    }

    // Reinitialize datepickers when showing spouse details
    if (maritalStatus === 'Married' || maritalStatus === 'Defacto') {
        initializeDatepickers();
    }
}

/**
 * Add Qualification
 */
function addQualification() {
    const container = document.getElementById('qualificationsContainer');
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Qualification" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="qualification[${index}]" placeholder="Enter Qualification">
                </div>
                <div class="form-group">
                    <label>Institution</label>
                    <input type="text" name="institution[${index}]" placeholder="Enter Institution">
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="qual_country[${index}]" placeholder="Enter Country">
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <input type="text" name="year[${index}]" placeholder="Enter Year">
                </div>
            </div>
        </div>
    `);
}

/**
 * Add Experience
 */
function addExperience() {
    const container = document.getElementById('experienceContainer');
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Experience" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Company</label>
                    <input type="text" name="company[${index}]" placeholder="Enter Company">
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="position[${index}]" placeholder="Enter Position">
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="text" name="exp_start_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="text" name="exp_end_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
            </div>
        </div>
    `);

    // Reinitialize datepickers for the newly added fields
    initializeDatepickers();
}

/**
 * Function to add a new character row
 */
function addCharacterRow(containerId, fieldName) {
    const container = document.getElementById(containerId);
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Character" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Detail</label>
                    <textarea name="${fieldName}[${index}]" rows="2" placeholder="Enter Detail"></textarea>
                </div>
            </div>
        </div>
    `);
}

/**
 * Initialize Google Maps autocomplete for address inputs
 */
function initGoogleMaps() {
    const inputs = document.querySelectorAll('.address-input');
    inputs.forEach(input => {
        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['address'],
            fields: ['formatted_address', 'address_components']
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.formatted_address) {
                return;
            }

            input.value = place.formatted_address;

            const row = input.closest('.repeatable-section');
            if (row) {
                const postcodeInput = row.querySelector('.postcode-input, input[name*="zip"]');
                
                let postcode = '';
                if (place.address_components) {
                    place.address_components.forEach(component => {
                        if (component.types.includes('postal_code')) {
                            postcode = component.long_name;
                        }
                    });
                }
                
                if (postcodeInput && postcode) {
                    postcodeInput.value = postcode;
                }
            }
        });
    });
}

// Make functions globally available
window.initGoogleMaps = initGoogleMaps;
window.addPartnerRow = addPartnerRow;
window.removePartnerRow = removePartnerRow;
window.addEoiReference = addEoiReference;
window.toggleVisaDetails = toggleVisaDetails;
window.addPassportDetail = addPassportDetail;
window.addTravelDetail = addTravelDetail;
window.addAddress = addAddress;
window.addQualification = addQualification;
window.addExperience = addExperience;
window.calculateAge = calculateAge;
window.addPhoneNumber = addPhoneNumber;
window.validatePersonalPhoneNumbers = validatePersonalPhoneNumbers;
window.addEmailAddress = addEmailAddress;
window.validatePersonalEmailTypes = validatePersonalEmailTypes;
window.addVisaDetail = addVisaDetail;
window.initializeDatepickers = initializeDatepickers;
window.toggleSpouseDetailsSection = toggleSpouseDetailsSection;
window.addCharacterRow = addCharacterRow;

// ===== DOCUMENT READY =====
$(document).ready(function() {
    // Call initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeTabs);
    } else {
        initializeTabs();
    }
    
    // Initialize datepickers on page load
    initializeDatepickers();

    // Initialize age on page load and set up datepicker for DOB
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');

    if (dobInput && ageInput) {
        // Initialize age if DOB exists
        if (dobInput.value) {
            ageInput.value = calculateAge(dobInput.value);
        }

        // Function to update age
        const updateAge = function() {
            const dobValue = dobInput.value;
            ageInput.value = calculateAge(dobValue);
        };

        // Handle manual input changes (e.g., typing or pasting)
        dobInput.addEventListener('input', updateAge);

        // Ensure datepicker is initialized and handle datepicker changes
        $(dobInput).daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ],
                firstDay: 1
            },
            autoApply: true,
            minDate: '01/01/1000',
            minYear: 1000,
            maxYear: parseInt(moment().format('YYYY')) + 50
        }).on('apply.daterangepicker', function(ev, picker) {
            // Update the input value and calculate age when a date is selected
            dobInput.value = picker.startDate.format('DD/MM/YYYY');
            updateAge();
        }).on('change', updateAge); // Fallback for any direct changes
    }

    // Password toggle functionality
    $(document).on('click', '.toggle-password', function() {
        const index = $(this).data('index');
        const passwordInput = $(`.eoi-password-input[data-index="${index}"]`);
        const icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password change confirmation
    $(document).on('focus', '.eoi-password-input', function() {
        const currentValue = $(this).val();
        const originalValue = $(this).data('original-value') || '';
        
        // Set original value if not set
        if (!$(this).data('original-value-set')) {
            $(this).data('original-value', currentValue);
            $(this).data('original-value-set', true);
        }
        
        // Reset confirmation flag if the current value matches the original
        if (currentValue === originalValue) {
            $(this).data('confirmation-shown', false);
        }
    });

    // Password change confirmation - only trigger once when user starts typing
    $(document).on('keydown', '.eoi-password-input', function(e) {
        const currentValue = $(this).val();
        const originalValue = $(this).data('original-value') || '';
        const hasShownConfirmation = $(this).data('confirmation-shown') || false;
        
        // Only show confirmation if:
        // 1. There was an original password value (not empty)
        // 2. We haven't shown the confirmation yet for this session
        // 3. User is about to type (not deleting)
        if (originalValue !== '' && !hasShownConfirmation && e.key.length === 1) {
            const confirmChange = confirm('Do you want to change the password?');
            $(this).data('confirmation-shown', true);
            
            if (!confirmChange) {
                e.preventDefault();
                return false;
            } else {
                // If user confirms, allow typing to continue
                $(this).data('original-value', ''); // Clear original value to prevent further confirmations
            }
        }
    });

    // Update autocomplete to handle all family member types
    $(document).on('input', '.partner-details', function() {
        const $input = $(this);
        const query = $input.val().trim();
        const $row = $input.closest('.repeatable-section');
        const $autocomplete = $row.find('.autocomplete-items');
        const $extraFields = $row.find('.partner-extra-fields');

        const type = $row.closest('[id$="Container"]').attr('id').replace('Container', '');
        const $partnerIdInput = $row.find('.partner-id');

        // Clear autocomplete if query is too short
        if (query.length < 3) {
            $autocomplete.empty();
            // Do not show extra fields until a search is performed and no results are found
            return;
        }

        // Get the search route from window config
        const searchRoute = window.editClientConfig?.searchPartnerRoute;
        const csrfToken = window.editClientConfig?.csrfToken;

        if (!searchRoute || !csrfToken) {
            console.error('Search partner route or CSRF token not configured');
            return;
        }

        $.ajax({
            url: searchRoute,
            method: 'POST',
            data: {
                query: query,
                _token: csrfToken
            },
            success: function(response) {
                $autocomplete.empty();
                // Check if response contains partners array
                if (response.partners && Array.isArray(response.partners) && response.partners.length > 0) {
                    response.partners.forEach(function(client) {
                        const displayText = `${client.first_name} ${client.last_name || ''} (${client.email}, ${client.phone}, ${client.client_id})`;
                        const $item = $('<div class="autocomplete-item"></div>')
                            .text(displayText)
                            .data('client', client)
                            .appendTo($autocomplete);

                        $item.on('click', function() {
                            const clientData = $(this).data('client');
                            $input.val(displayText);
                            $input.attr('readonly', true);
                            $partnerIdInput.val(clientData.id); // Set the partner ID
                            $extraFields.hide();
                            $autocomplete.empty();
                        });
                    });
                } else {
                    $autocomplete.html('<div class="autocomplete-item autocomplete-no-results">No results found</div>');
                    // Show confirmation prompt before displaying extra fields
                    setTimeout(() => {
                        const addNewUser = confirm('No matching record found. Do you want to save details of your client then Proceed?');
                        if (addNewUser) {
                            $input.attr('readonly', true);
                            $partnerIdInput.val(''); // Clear the partner ID
                            $extraFields.show();
                        } else {
                            $extraFields.hide();
                        }
                        $autocomplete.empty();
                    }, 300); // Small delay to ensure the "No results found" message is visible briefly
                }
            },
            error: function(xhr) {
                $autocomplete.empty();
                let errorMsg = 'Error fetching client details.';
                if (xhr.status === 422) {
                    // Handle validation errors
                    const errors = xhr.responseJSON.errors;
                    if (errors && errors.query) {
                        errorMsg = errors.query[0];
                    }
                }
                $autocomplete.html(`<div class="autocomplete-item autocomplete-error">${errorMsg}</div>`);
            }
        });
    });

    // Close autocomplete when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.partner-details').length && !$(e.target).closest('.autocomplete-items').length) {
            $('.autocomplete-items').empty();
        }
    });

    // Run on page load to set initial state
    toggleSpouseDetailsSection();

    // Run on Marital Status change
    const martialStatusElement = document.getElementById('martialStatus');
    if (martialStatusElement) {
        martialStatusElement.addEventListener('change', function() {
            toggleSpouseDetailsSection();
        });
    }

    // Run on page load
    toggleVisaDetails();

    // Run on passport country change
    const passportCountryElement = document.getElementById('passportCountry');
    if (passportCountryElement) {
        passportCountryElement.addEventListener('change', function() {
            toggleVisaDetails();
        });
    }

    // Add event listeners for real-time validation and form submission
    const phoneNumbersContainer = document.getElementById('phoneNumbersContainer');
    if (phoneNumbersContainer) {
        // Validate on change of type
        phoneNumbersContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('contact-type-selector')) {
                validatePersonalPhoneNumbers();
            }
        });

        // Validate on form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            validatePersonalPhoneNumbers();
        });

        // Initial validation on page load
        validatePersonalPhoneNumbers();
    }

    // Add event listeners for real-time validation and form submission (emails)
    const emailAddressesContainer = document.getElementById('emailAddressesContainer');
    if (emailAddressesContainer) {
        // Validate on change of type
        emailAddressesContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('email-type-selector')) {
                validatePersonalEmailTypes();
            }
        });

        // Validate on form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            if (!validatePersonalEmailTypes()) {
                e.preventDefault();
                alert('Only one email address can be of type Personal. Please correct the entries.');
            }
        });

        // Initial validation on page load
        validatePersonalEmailTypes();
    }

    // Handle EOI Reference removal with confirmation
    const eoiReferencesContainer = document.getElementById('eoiReferencesContainer');
    if (eoiReferencesContainer) {
        eoiReferencesContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const section = e.target.closest('.repeatable-section');
                const eoiIdInput = section.querySelector('input[name^="eoi_id"]');
                const confirmDelete = confirm('Are you sure you want to delete this EOI Reference?');

                if (confirmDelete) {
                    if (eoiIdInput) {
                        const eoiId = eoiIdInput.value;
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'delete_eoi_ids[]';
                        hiddenInput.value = eoiId;
                        document.getElementById('editClientForm').appendChild(hiddenInput);
                    }
                    section.remove();
                }
            }
        });
    }

    // Handle qualification removal
    const qualificationsContainer = document.getElementById('qualificationsContainer');
    if (qualificationsContainer) {
        qualificationsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const section = e.target.closest('.repeatable-section');
                const qualificationIdInput = section.querySelector('input[name^="qualification_id"]');
                const confirmDelete = confirm('Are you sure you want to delete this qualification?');

                if (confirmDelete) {
                    if (qualificationIdInput) {
                        const qualificationId = qualificationIdInput.value;
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'delete_qualification_ids[]';
                        hiddenInput.value = qualificationId;
                        document.getElementById('editClientForm').appendChild(hiddenInput);
                    }
                    section.remove();
                }
            }
        });
    }

    // Handle experience removal
    const experienceContainer = document.getElementById('experienceContainer');
    if (experienceContainer) {
        experienceContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const section = e.target.closest('.repeatable-section');
                const experienceIdInput = section.querySelector('input[name^="experience_id"]');
                const confirmDelete = confirm('Are you sure you want to delete this experience?');

                if (confirmDelete) {
                    if (experienceIdInput) {
                        const experienceId = experienceIdInput.value;
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'delete_experience_ids[]';
                        hiddenInput.value = experienceId;
                        document.getElementById('editClientForm').appendChild(hiddenInput);
                    }
                    section.remove();
                }
            }
        });
    }

    // Handle occupation removal
    const occupationContainer = document.getElementById('occupationContainer');
    if (occupationContainer) {
        occupationContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const section = e.target.closest('.repeatable-section');
                const occupationIdInput = section.querySelector('input[name^="occupation_id"]');
                const confirmDelete = confirm('Are you sure you want to delete this occupation?');

                if (confirmDelete) {
                    if (occupationIdInput) {
                        const occupationId = occupationIdInput.value;
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'delete_occupation_ids[]';
                        hiddenInput.value = occupationId;
                        document.getElementById('editClientForm').appendChild(hiddenInput);
                    }
                    section.remove();
                }
            }
        });
    }

    // Handle test score removal
    const testScoresContainer = document.getElementById('testScoresContainer');
    if (testScoresContainer) {
        testScoresContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const section = e.target.closest('.repeatable-section');
                const testScoreIdInput = section.querySelector('input[name^="test_score_id"]');
                const confirmDelete = confirm('Are you sure you want to delete this test score?');

                if (confirmDelete) {
                    if (testScoreIdInput) {
                        const testScoreId = testScoreIdInput.value;
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'delete_test_score_ids[]';
                        hiddenInput.value = testScoreId;
                        document.getElementById('editClientForm').appendChild(hiddenInput);
                    }
                    section.remove();
                }
            }
        });
    }
});

