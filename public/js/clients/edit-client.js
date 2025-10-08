/**
 * Client Edit Page JavaScript
 * Contains all functionality for the client edit form
 */

// ===== SCROLL-TO-SECTION FUNCTIONALITY =====

// Define scrollToSection function IMMEDIATELY in global scope
window.scrollToSection = function(sectionId) {
    try {
        const section = document.getElementById(sectionId);
        if (section) {
            // Smooth scroll to section
            section.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
            
            // Update active tab button
            updateActiveTabButton(sectionId);
        } else {
            console.error('Section not found:', sectionId);
        }
    } catch (error) {
        console.error('Error in scrollToSection function:', error);
    }
};

// Update active nav item based on section
function updateActiveTabButton(sectionId) {
    // Remove active class from all nav items
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to the corresponding nav item
    const items = document.querySelectorAll('.nav-item');
    items.forEach(item => {
        const onclick = item.getAttribute('onclick');
        if (onclick && onclick.includes(sectionId)) {
            item.classList.add('active');
        }
    });
}

// Toggle sidebar for mobile
window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebarNav');
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
};

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebarNav');
    const toggle = document.querySelector('.sidebar-toggle');
    
    if (sidebar && toggle && window.innerWidth <= 1024) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    }
});

// Close sidebar when clicking on nav item on mobile
document.addEventListener('click', function(event) {
    if (event.target.closest('.nav-item') && window.innerWidth <= 1024) {
        const sidebar = document.getElementById('sidebarNav');
        if (sidebar) {
            sidebar.classList.remove('open');
        }
    }
});

// Scroll spy functionality - update active nav item based on scroll position
function initScrollSpy() {
    const sections = document.querySelectorAll('.content-section');
    const navItems = document.querySelectorAll('.nav-item:not(.summary-nav)');
    
    if (sections.length === 0 || navItems.length === 0) return;
    
    function updateActiveNav() {
        let current = '';
        const scrollPosition = window.scrollY + 100; // Offset for better detection
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        if (current) {
            // Remove active class from all nav items
            navItems.forEach(item => item.classList.remove('active'));
            
            // Add active class to current nav item
            navItems.forEach(item => {
                const onclick = item.getAttribute('onclick');
                if (onclick && onclick.includes(current)) {
                    item.classList.add('active');
                }
            });
        }
    }
    
    // Throttle scroll events for better performance
    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(function() {
                updateActiveNav();
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Initial call
    updateActiveNav();
}

// ===== GO TO TOP BUTTON FUNCTIONALITY =====

// Scroll to top function
window.scrollToTop = function() {
    try {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    } catch (error) {
        console.error('Error scrolling to top:', error);
        // Fallback for older browsers
        document.documentElement.scrollTop = 0;
    }
};

// Show/hide Go to Top button based on scroll position
function initGoToTopButton() {
    const goToTopBtn = document.getElementById('goToTopBtn');
    if (!goToTopBtn) return;
    
    function toggleGoToTopButton() {
        const scrollPosition = window.scrollY;
        const showThreshold = 300; // Show button after scrolling 300px
        
        if (scrollPosition > showThreshold) {
            if (!goToTopBtn.classList.contains('show')) {
                goToTopBtn.classList.remove('hide');
                goToTopBtn.classList.add('show');
            }
        } else {
            if (goToTopBtn.classList.contains('show')) {
                goToTopBtn.classList.remove('show');
                goToTopBtn.classList.add('hide');
                
                // Remove hide class after animation completes
                setTimeout(() => {
                    goToTopBtn.classList.remove('hide');
                }, 300);
            }
        }
    }
    
    // Throttle scroll events for better performance
    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(function() {
                toggleGoToTopButton();
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Initial call
    toggleGoToTopButton();
}





// ===== LEGACY TAB FUNCTIONALITY (KEPT FOR COMPATIBILITY) =====

// Define openTab function IMMEDIATELY in global scope (legacy support)
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
                <option value="Defacto">De Facto</option>
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
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('eoiInfoSummary');
    const editView = document.getElementById('eoiInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('eoiInfo');
    }
    
    const container = document.getElementById('eoiReferencesContainer');
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove EOI Reference" onclick="removeEoiField(this)"><i class="fas fa-trash"></i></button>
            <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                <div class="form-group">
                    <label>EOI Number</label>
                    <input type="text" name="EOI_number[${index}]" placeholder="EOI Number">
                </div>
                <div class="form-group">
                    <label>Subclass</label>
                    <input type="text" name="EOI_subclass[${index}]" placeholder="Subclass">
                </div>
                <div class="form-group">
                    <label>Occupation</label>
                    <input type="text" name="EOI_occupation[${index}]" placeholder="Occupation">
                </div>
                <div class="form-group">
                    <label>Point</label>
                    <input type="text" name="EOI_point[${index}]" placeholder="Point">
                </div>
                <div class="form-group">
                    <label>State</label>
                    <input type="text" name="EOI_state[${index}]" placeholder="State">
                </div>
                <div class="form-group">
                    <label>Submission Date</label>
                    <input type="text" name="EOI_submission_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                </div>
                <div class="form-group">
                    <label>ROI</label>
                    <input type="text" name="EOI_ROI[${index}]" placeholder="ROI">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="password" name="EOI_password[${index}]" placeholder="Password" class="eoi-password-input" data-index="${index}">
                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-password" data-index="${index}" title="Show/Hide Password">
                            <i class="fas fa-eye-slash"></i>
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

    // Check if passportCountry element exists before accessing its value
    if (!passportCountrySelector) {
        return;
    }

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
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('passportInfoSummary');
    const editView = document.getElementById('passportInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('passportInfo');
    }
    
    const container = document.getElementById('passportDetailsContainer');
    const index = container.children.length;

    // Get country options from existing select if available, otherwise use default
    let countryOptions = '<option value="">Select Country</option>';
    const existingSelect = document.querySelector('.passport-country-field');
    if (existingSelect) {
        Array.from(existingSelect.options).forEach(option => {
            countryOptions += `<option value="${option.value}">${option.text}</option>`;
        });
    } else {
        // Fallback if no existing select found
        countryOptions += '<option value="India">India</option><option value="Australia">Australia</option>';
    }

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Passport" onclick="removePassportField(this)"><i class="fas fa-trash"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Country</label>
                    <select name="passports[${index}][passport_country]" class="passport-country-field">
                        ${countryOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label>Passport #</label>
                    <input type="text" name="passports[${index}][passport_number]" placeholder="Passport Number">
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
 * Add Another Address (for new component system)
 */
function addAnotherAddress() {
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('addressInfoSummary');
    const editView = document.getElementById('addressInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('addressInfo');
    }
    
    const container = document.getElementById('addresses-container');
    if (!container) {
        console.error('Address container not found');
        return;
    }
    
    const index = container.querySelectorAll('.address-entry-wrapper').length;
    
    const addressHTML = `
        <div class="address-entry-wrapper" data-address-index="${index}">
            <button type="button" class="remove-address-btn" onclick="removeAddressEntry(this)" title="Remove Address">
                <i class="fas fa-times"></i>
            </button>
            
            <input type="hidden" name="address_id[]" value="">
            
            <div class="form-group address-search-container">
                <label for="address_search_${index}">Search Address</label>
                <input type="text" 
                       id="address_search_${index}" 
                       name="address_search[]" 
                       class="address-search-input" 
                       placeholder="Start typing an address..."
                       autocomplete="off"
                       data-address-index="${index}">
            </div>
            
            <div class="address-fields-grid">
                <div class="form-group">
                    <label for="address_line_1_${index}">Address Line 1 *</label>
                    <input type="text" 
                           id="address_line_1_${index}" 
                           name="address_line_1[]" 
                           placeholder="Street number and name"
                           class="address-required">
                </div>
                
                <div class="form-group">
                    <label for="address_line_2_${index}">Address Line 2</label>
                    <input type="text" 
                           id="address_line_2_${index}" 
                           name="address_line_2[]" 
                           placeholder="Apartment, suite, unit, etc.">
                </div>
            </div>
            
            <div class="address-fields-grid">
                <div class="form-group">
                    <label for="suburb_${index}">Suburb *</label>
                    <input type="text" 
                           id="suburb_${index}" 
                           name="suburb[]" 
                           placeholder="Suburb"
                           class="address-required">
                </div>
                
                <div class="form-group">
                    <label for="state_${index}">State *</label>
                    <input type="text" 
                           id="state_${index}" 
                           name="state[]" 
                           placeholder="State"
                           class="address-required">
                </div>
            </div>
            
            <div class="address-fields-grid">
                <div class="form-group">
                    <label for="zip_${index}">Postcode *</label>
                    <input type="text" 
                           id="zip_${index}" 
                           name="zip[]" 
                           placeholder="Postcode"
                           class="address-required">
                </div>
                
                <div class="form-group">
                    <label for="country_${index}">Country *</label>
                    <input type="text" 
                           id="country_${index}" 
                           name="country[]" 
                           value="Australia"
                           placeholder="Country"
                           class="address-required">
                </div>
            </div>
            
            <div class="form-group">
                <label for="regional_code_${index}">Regional Code</label>
                <input type="text" 
                       id="regional_code_${index}" 
                       name="regional_code[]" 
                       placeholder="Regional code (auto-calculated)"
                       class="regional-code-field"
                       readonly>
            </div>
            
            <div class="date-fields">
                <div class="form-group">
                    <label for="address_start_date_${index}">Start Date</label>
                    <input type="text" 
                           id="address_start_date_${index}" 
                           name="address_start_date[]" 
                           placeholder="dd/mm/yyyy"
                           class="date-picker">
                </div>
                
                <div class="form-group">
                    <label for="address_end_date_${index}">End Date</label>
                    <input type="text" 
                           id="address_end_date_${index}" 
                           name="address_end_date[]" 
                           placeholder="dd/mm/yyyy"
                           class="date-picker">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', addressHTML);
    
    // Reinitialize date pickers
    initializeDatepickers();
}

/**
 * Remove Address Entry
 */
function removeAddressEntry(button) {
    if (confirm('Are you sure you want to remove this address?')) {
        const wrapper = button.closest('.address-entry-wrapper');
        wrapper.remove();
    }
}

/**
 * Add Address (old function kept for backward compatibility)
 */
function addAddress() {
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('addressInfoSummary');
    const editView = document.getElementById('addressInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('addressInfo');
    }
    
    // Use the new component-compatible function
    if (typeof addAnotherAddress === 'function') {
        addAnotherAddress();
        return;
    }
    
    // Fallback to manual row addition (legacy system)
    const container = document.getElementById('address-fields-wrapper');
    if (!container) {
        console.error('Address container not found');
        return;
    }
    const index = container.querySelectorAll('.address-fields').length;

    container.insertAdjacentHTML('beforeend', `
        <div class="address-fields row mb-3">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="zip">Post Code</label>
                    <input type="text" name="zip[]" class="form-control postal_code" autocomplete="off" placeholder="Enter Post Code">
                    <div class="autocomplete-items"></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address[]" class="form-control address-input" autocomplete="off" placeholder="Search Box">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for="regional_code">Regional Code Info</label>
                    <input type="text" name="regional_code[]"  class="form-control regional_code_info" placeholder="Regional Code info" readonly>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for="address_start_date">Start Date</label>
                    <input type="text" name="address_start_date[]" class="form-control date-picker" placeholder="dd/mm/yyyy">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for="address_end_date">End Date</label>
                    <input type="text" name="address_end_date[]" class="form-control date-picker" placeholder="dd/mm/yyyy">
                </div>
            </div>
            <div class="col-sm-1 d-flex align-items-center">
                <button type="button" class="btn btn-primary add-row-btn">+</button>
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
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('travelInfoSummary');
    const editView = document.getElementById('travelInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('travelInfo');
    }
    
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
            <button type="button" class="remove-item-btn" title="Remove Travel" onclick="removeTravelField(this)"><i class="fas fa-trash"></i></button>
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
                    <input type="text" name="travel_purpose[${index}]" placeholder="Travel Purpose">
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
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('phoneNumbersSummary');
    const editView = document.getElementById('phoneNumbersEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('phoneNumbers');
    }
    
    const container = document.getElementById('phoneNumbersContainer');
    const index = container.children.length;
    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Phone" onclick="removePhoneField(this)"><i class="fas fa-trash"></i></button>
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
                            <select name="country_code[${index}]" class="country-code-input">
                                <option value="+61">🇦🇺 +61</option>
                                <option value="+91">🇮🇳 +91</option>
                                <option value="+1">🇺🇸 +1</option>
                                <option value="+44">🇬🇧 +44</option>
                                <option value="+49">🇩🇪 +49</option>
                                <option value="+33">🇫🇷 +33</option>
                                <option value="+86">🇨🇳 +86</option>
                                <option value="+81">🇯🇵 +81</option>
                                <option value="+82">🇰🇷 +82</option>
                                <option value="+65">🇸🇬 +65</option>
                                <option value="+60">🇲🇾 +60</option>
                                <option value="+66">🇹🇭 +66</option>
                                <option value="+63">🇵🇭 +63</option>
                                <option value="+84">🇻🇳 +84</option>
                                <option value="+62">🇮🇩 +62</option>
                            </select>
                        </div>
                                                    <input type="tel" name="phone[${index}]" placeholder="Phone Number" class="phone-number-input" style="width: 140px;" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    `);
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
                section.querySelector('.content-grid').insertAdjacentHTML('afterend', errorMessage);
                // Disable the submit button
                const submitButton = document.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                }
            } else {
                personalPhones[fullPhone] = true;
            }
        }
    });

    // Re-enable the submit button if no duplicates are found
    if (!Object.keys(personalPhones).some(phone => personalPhones[phone] === true && Object.keys(personalPhones).filter(p => p === phone).length > 1)) {
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = false;
        }
    }
}

/**
 * Add Email Address (Updated to exclude verification slider in repeatable section)
 */
function addEmailAddress() {
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('emailAddressesSummary');
    const editView = document.getElementById('emailAddressesEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('emailAddresses');
    }
    
    const container = document.getElementById('emailAddressesContainer');
    const index = container.children.length;
    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Email" onclick="removeEmailField(this)"><i class="fas fa-trash"></i></button>
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
    if (submitButton) {
        if (personalCount > 1) {
            submitButton.disabled = true;
        } else {
            submitButton.disabled = false;
        }
    }

    return personalCount <= 1;
}

/**
 * Add Visa Detail
 */
async function addVisaDetail() {
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('visaInfoSummary');
    const editView = document.getElementById('visaInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('visaInfo');
    }
    
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
            <button type="button" class="remove-item-btn" title="Remove Visa" onclick="removeVisaField(this)"><i class="fas fa-trash"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Visa Type / Subclass</label>
                    <select name="visa_type_hidden[${index}]" class="visa-type-field">
                        ${optionsHtml}
                    </select>
                </div>
                <div class="form-group">
                    <label>Visa Expiry Date</label>
                    <input type="text" name="visa_expiry_date[${index}]" placeholder="dd/mm/yyyy" class="visa-expiry-field date-picker">
                </div>
                <div class="form-group">
                    <label>Visa Grant Date</label>
                    <input type="text" name="visa_grant_date[${index}]" placeholder="dd/mm/yyyy" class="visa-grant-field date-picker">
                </div>
                <div class="form-group">
                    <label>Visa Description</label>
                    <input type="text" name="visa_description[${index}]" class="visa-description-field" placeholder="Description">
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

    // Check if the spouseDetailsSection element exists before trying to access its style
    if (spouseDetailsSection) {
        if (maritalStatus === 'Married' || maritalStatus === 'Defacto') {
            spouseDetailsSection.style.display = 'block';
        } else {
            spouseDetailsSection.style.display = 'none';
        }
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
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('qualificationsInfoSummary');
    const editView = document.getElementById('qualificationsInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('qualificationsInfo');
    }
    
    const container = document.getElementById('qualificationsContainer');
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Qualification" onclick="removeQualificationField(this)"><i class="fas fa-trash"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="qualification[${index}]" placeholder="Qualification">
                </div>
                <div class="form-group">
                    <label>Institution</label>
                    <input type="text" name="institution[${index}]" placeholder="Institution">
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="qual_country[${index}]" placeholder="Country">
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <input type="text" name="year[${index}]" placeholder="Year">
                </div>
            </div>
        </div>
    `);
}

/**
 * Add Experience
 */
function addExperience() {
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('experienceInfoSummary');
    const editView = document.getElementById('experienceInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('experienceInfo');
    }
    
    const container = document.getElementById('experienceContainer');
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Experience" onclick="removeExperienceField(this)"><i class="fas fa-trash"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Company</label>
                    <input type="text" name="company[${index}]" placeholder="Company">
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="position[${index}]" placeholder="Position">
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
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('characterInfoSummary');
    const editView = document.getElementById('characterInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('characterInfo');
    }
    
    const container = document.getElementById(containerId);
    const index = container.children.length;

    container.insertAdjacentHTML('beforeend', `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Character" onclick="removeCharacterField(this)"><i class="fas fa-trash"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Detail</label>
                    <textarea name="${fieldName}[${index}]" rows="2" placeholder="Detail"></textarea>
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

// ===== NEW SUMMARY/EDIT MODE FUNCTIONALITY =====

/**
 * Toggle edit mode for sections
 */
window.toggleEditMode = function(sectionType) {
    const summaryView = document.getElementById(sectionType + 'Summary');
    const editView = document.getElementById(sectionType + 'Edit');
    
    if (summaryView && editView) {
        // Hide summary view (support both inline styles and classes)
        summaryView.style.display = 'none';
        summaryView.classList.add('hidden');
        
        // Show edit view (support both inline styles and classes)
        editView.style.display = 'block';
        editView.classList.remove('hidden');
        
        // Section-specific initialization
        if (sectionType === 'addressInfo') {
            // Re-initialize datepickers when entering edit mode for address section
            setTimeout(function() {
                initializeDatepickers();
                console.log('✅ Date pickers initialized for address edit mode');
            }, 100);
        } else if (sectionType === 'emailAddresses') {
            // Start email verification polling when opening email section
            console.log('📧 Opening email section - starting verification polling');
            setTimeout(function() {
                initializeEmailSectionPolling();
            }, 100);
        }
    }
};

/**
 * Cancel edit mode and return to summary view
 */
window.cancelEdit = function(sectionType) {
    const summaryView = document.getElementById(sectionType + 'Summary');
    const editView = document.getElementById(sectionType + 'Edit');
    
    if (summaryView && editView) {
        // Hide edit view (support both inline styles and classes)
        editView.style.display = 'none';
        editView.classList.add('hidden');
        
        // Show summary view (support both inline styles and classes)
        summaryView.style.display = 'block';
        summaryView.classList.remove('hidden');
        
        // Section-specific cleanup
        if (sectionType === 'emailAddresses') {
            // Stop email verification polling when leaving email section
            console.log('📧 Closing email section - stopping verification polling');
            stopAllEmailPolling();
            
            // Do a final refresh of email statuses
            setTimeout(function() {
                initializeEmailSectionPolling();
            }, 100);
        }
    }
};

/**
 * Save basic information and update summary
 */
/**
 * Generic function to save section data via AJAX
 */
window.saveSectionData = function(sectionName, formData, successCallback) {
    const form = document.getElementById('editClientForm');
    const clientId = form.querySelector('input[name="id"]').value;
    const type = form.querySelector('input[name="type"]').value;
    
    // Get CSRF token from meta tag or form
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                     || document.querySelector('input[name="_token"]')?.value 
                     || '';
    
    // Add section data to form data
    formData.append('_token', csrfToken);
    formData.append('client_id', clientId);
    formData.append('type', type);
    formData.append('section', sectionName);
    
    fetch('/admin/clients/save-section', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Handle non-200 responses
        if (!response.ok) {
            return response.json().then(data => {
                throw { status: response.status, data: data };
            }).catch(error => {
                if (error.status) throw error;
                throw { status: response.status, data: { message: 'Server error occurred' } };
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            successCallback();
            showNotification(data.message || `${sectionName} updated successfully!`, 'success');
        } else {
            showNotification(data.message || `Error updating ${sectionName}`, 'error');
            if (data.errors) {
                displaySectionErrors(sectionName, data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Handle validation errors (422 status)
        if (error.status === 422 && error.data && error.data.errors) {
            displaySectionErrors(sectionName, error.data.errors);
            showNotification('Please fix the validation errors', 'error');
        } else {
            const message = error.data?.message || `Error updating ${sectionName}. Please try again.`;
            showNotification(message, 'error');
        }
    });
};

/**
 * Display errors for a specific section
 */
window.displaySectionErrors = function(sectionName, errors) {
    const editView = document.getElementById(sectionName + 'Edit');
    if (!editView) return;
    
    // Clear previous errors
    editView.querySelectorAll('.field-error').forEach(error => error.remove());
    
    // Display new errors
    Object.keys(errors).forEach(fieldName => {
        const field = editView.querySelector(`[name*="${fieldName}"]`);
        if (field) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error text-danger';
            errorDiv.textContent = errors[fieldName][0];
            field.parentNode.appendChild(errorDiv);
        }
    });
};

window.saveBasicInfo = function() {
    // Validate required fields
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const clientId = document.getElementById('clientId').value.trim();
    
    if (!firstName || !lastName || !clientId) {
        showNotification('Please fill in all required fields (First Name, Last Name, Client ID)', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('client_id', clientId);
    formData.append('dob', document.getElementById('dob').value);
    formData.append('age', document.getElementById('age').value);
    formData.append('gender', document.getElementById('gender').value);
    formData.append('marital_status', document.getElementById('martialStatus').value);
    
    saveSectionData('basicInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('basicInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        summaryGrid.innerHTML = `
            <div class="summary-item">
                <span class="summary-label">Name:</span>
                <span class="summary-value">${firstName} ${lastName}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Client ID:</span>
                <span class="summary-value">${clientId}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Date of Birth:</span>
                <span class="summary-value">${document.getElementById('dob').value || 'Not set'}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Age:</span>
                <span class="summary-value">${document.getElementById('age').value || 'Not calculated'}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Gender:</span>
                <span class="summary-value">${document.getElementById('gender').value || 'Not set'}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Marital Status:</span>
                <span class="summary-value">${document.getElementById('martialStatus').value || 'Not set'}</span>
            </div>
        `;
        
        // Return to summary view
        cancelEdit('basicInfo');
    });
};

/**
 * Save phone numbers and update summary
 */
window.savePhoneNumbers = function() {
    // Get all phone number entries
    const container = document.getElementById('phoneNumbersContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const phoneNumbers = [];
    
    sections.forEach((section, index) => {
        const type = section.querySelector('.contact-type-selector').value;
        const countryCode = section.querySelector('.country-code-input').value;
        const phone = section.querySelector('.phone-number-input').value;
        const contactId = section.querySelector('input[name*="contact_id"]')?.value;
        
        if (type && phone) {
            phoneNumbers.push({
                id: contactId || '',
                contact_type: type,
                country_code: countryCode,
                phone: phone
            });
        }
    });
    
    const formData = new FormData();
    formData.append('phone_numbers', JSON.stringify(phoneNumbers));
    
    saveSectionData('phoneNumbers', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('phoneNumbersSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (phoneNumbers.length > 0) {
            summaryGrid.innerHTML = phoneNumbers.map((phone, index) => {
                // For newly saved numbers, show verify button for +61 numbers
                // The actual verification status will be loaded from the server on page refresh
                const verificationButton = phone.country_code === '+61' ? 
                    `<button type="button" class="btn-verify-phone" onclick="sendOTP('${phone.id || 'pending'}', '${phone.phone}', '${phone.country_code}')" data-contact-id="${phone.id || 'pending'}">
                        <i class="fas fa-lock"></i> Verify
                     </button>` : '';
                
                return `
                    <div class="summary-item">
                        <span class="summary-label">${phone.contact_type}:</span>
                        <span class="summary-value">${phone.country_code}${phone.phone}</span>
                        ${verificationButton}
                    </div>
                `;
            }).join('');
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No phone numbers added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('phoneNumbers');
        
        // Refresh the page to get updated verification status from server
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });
};

/**
 * Save email addresses and update summary
 */
window.saveEmailAddresses = function() {
    // Get all email entries
    const container = document.getElementById('emailAddressesContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const emails = [];
    
    sections.forEach(section => {
        const type = section.querySelector('.email-type-selector').value;
        const email = section.querySelector('input[type="email"]').value;
        const emailId = section.querySelector('input[name*="email_id"]')?.value;
        
        if (type && email) {
            emails.push({
                email_id: emailId || '',
                email_type: type,
                email: email
            });
        }
    });
    
    const formData = new FormData();
    formData.append('emails', JSON.stringify(emails));
    
    saveSectionData('emailAddresses', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('emailAddressesSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (emails.length > 0) {
            summaryGrid.innerHTML = emails.map((email, index) => {
                // For newly saved emails, show verify button
                const verificationButton = !email.is_verified ? 
                    `<button type="button" class="btn-verify-email" onclick="sendEmailVerification('${email.email_id || 'pending'}', '${email.email}')" data-email-id="${email.email_id || 'pending'}">
                        <i class="fas fa-lock"></i> Verify
                     </button>` : 
                    `<span class="verified-badge">
                        <i class="fas fa-check-circle"></i> Verified
                     </span>`;
                
                return `
                    <div class="summary-item">
                        <span class="summary-label">${email.email_type}:</span>
                        <span class="summary-value">${email.email}</span>
                        ${verificationButton}
                    </div>
                `;
            }).join('');
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No email addresses added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('emailAddresses');
        
        // Start polling for newly saved unverified emails
        setTimeout(() => {
            const newEmailVerifyButtons = document.querySelectorAll('.btn-verify-email');
            newEmailVerifyButtons.forEach(button => {
                const emailId = button.getAttribute('data-email-id');
                
                // Same comprehensive validation
                if (emailId && 
                    emailId !== 'pending' && 
                    emailId !== 'null' && 
                    emailId !== 'undefined' &&
                    emailId !== '' &&
                    emailId !== '0' &&
                    !isNaN(parseInt(emailId)) && 
                    parseInt(emailId) > 0) {
                    
                    startEmailVerificationPolling(parseInt(emailId));
                }
            });
        }, 1000);
    });
};

/**
 * Edit individual phone number
 */
window.editPhoneNumber = function(index) {
    // Switch to edit mode
    toggleEditMode('phoneNumbers');
    
    // Focus on the specific phone number field
    const container = document.getElementById('phoneNumbersContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    if (sections[index]) {
        const phoneInput = sections[index].querySelector('.phone-number-input');
        if (phoneInput) {
            phoneInput.focus();
        }
    }
};

/**
 * Edit individual email address
 */
window.editEmailAddress = function(index) {
    // Switch to edit mode
    toggleEditMode('emailAddresses');
    
    // Focus on the specific email field
    const container = document.getElementById('emailAddressesContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    if (sections[index]) {
        const emailInput = sections[index].querySelector('input[type="email"]');
        if (emailInput) {
            emailInput.focus();
        }
    }
};

/**
 * Remove phone number
 */
window.removePhoneNumber = function(id, index) {
    if (confirm('Are you sure you want to remove this phone number?')) {
        if (id) {
            // Mark for deletion in database
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'delete_contact_ids[]';
            hiddenInput.value = id;
            document.getElementById('editClientForm').appendChild(hiddenInput);
        }
        
        // Remove from DOM
        const container = document.getElementById('phoneNumbersContainer');
        const sections = container.querySelectorAll('.repeatable-section');
        if (sections[index]) {
            sections[index].remove();
        }
        
        // Update summary
        savePhoneNumbers();
    }
};

/**
 * Remove email address
 */
window.removeEmailAddress = function(id, index) {
    if (confirm('Are you sure you want to remove this email address?')) {
        if (id) {
            // Mark for deletion in database
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'delete_email_ids[]';
            hiddenInput.value = id;
            document.getElementById('editClientForm').appendChild(hiddenInput);
        }
        
        // Remove from DOM
        const container = document.getElementById('emailAddressesContainer');
        const sections = container.querySelectorAll('.repeatable-section');
        if (sections[index]) {
            sections[index].remove();
        }
        
        // Update summary
        saveEmailAddresses();
    }
};

/**
 * Save passport information and update summary
 */
window.savePassportInfo = function() {
    // Get all passport entries
    const container = document.getElementById('passportDetailsContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const passports = [];
    
    sections.forEach(section => {
        const passportId = section.querySelector('input[name*="passport_id"]')?.value;
        const passportCountry = section.querySelector('select[name*="passport_country"]')?.value;
        const passportNumber = section.querySelector('input[name*="passport_number"]').value;
        const issueDate = section.querySelector('input[name*="issue_date"]').value;
        const expiryDate = section.querySelector('input[name*="expiry_date"]').value;
        
        if (passportNumber || issueDate || expiryDate || passportCountry) {
            passports.push({
                passport_id: passportId || '',
                passport_country: passportCountry || '',
                passport_number: passportNumber,
                issue_date: issueDate,
                expiry_date: expiryDate
            });
        }
    });
    
    const formData = new FormData();
    formData.append('passports', JSON.stringify(passports));
    
    saveSectionData('passportInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('passportInfoSummary');
        
        let summaryHTML = '';
        
        if (passports.length > 0) {
            summaryHTML += '<div style="margin-top: 15px;">';
            passports.forEach(passport => {
                summaryHTML += `
                    <div class="passport-entry-compact" style="margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #007bff;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: center;">
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">COUNTRY:</span>
                                <span class="summary-value" style="color: #212529; font-weight: 500;">${passport.passport_country || 'Not set'}</span>
                            </div>
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">PASSPORT #:</span>
                                <span class="summary-value" style="color: #212529; font-weight: 500;">${passport.passport_number || 'Not set'}</span>
                            </div>
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">ISSUE DATE:</span>
                                <span class="summary-value" style="color: #212529;">${passport.issue_date || 'Not set'}</span>
                            </div>
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">EXPIRY DATE:</span>
                                <span class="summary-value" style="color: #212529;">${passport.expiry_date || 'Not set'}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            summaryHTML += '</div>';
        } else {
            summaryHTML += '<div class="empty-state" style="margin-top: 15px;"><p>No passport details added yet.</p></div>';
        }
        
        summaryView.innerHTML = summaryHTML;
        
        // Return to summary view
        cancelEdit('passportInfo');
    });
};

/**
 * Save visa information and update summary
 */
window.saveVisaInfo = function() {
    // Get visa expiry verified status
    const visaExpiryVerified = document.querySelector('input[name="visa_expiry_verified"]').checked ? '1' : '0';
    
    // Get all visa entries
    const container = document.getElementById('visaDetailsContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const visas = [];
    
    sections.forEach(section => {
        const visaId = section.querySelector('input[name*="visa_id"]')?.value;
        const visaType = section.querySelector('.visa-type-field').value;
        const expiryDate = section.querySelector('.visa-expiry-field').value;
        const grantDate = section.querySelector('.visa-grant-field').value;
        const description = section.querySelector('.visa-description-field').value;
        
        if (visaType || expiryDate || grantDate || description) {
            visas.push({
                visa_id: visaId || '',
                visa_type_hidden: visaType,
                visa_expiry_date: expiryDate,
                visa_grant_date: grantDate,
                visa_description: description
            });
        }
    });
    
    const formData = new FormData();
    formData.append('visa_expiry_verified', visaExpiryVerified);
    formData.append('visas', JSON.stringify(visas));
    
    saveSectionData('visaInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('visaInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        let summaryHTML = `
            <div class="summary-item">
                <span class="summary-label">Visa Expiry Verified:</span>
                <span class="summary-value">${visaExpiryVerified === '1' ? 'Yes' : 'No'}</span>
            </div>
        `;
        
        if (visas.length > 0) {
            // Sort visas by expiry date (longest expiry date first)
            visas.sort((a, b) => {
                // Handle cases where expiry date might be null or empty
                if (!a.visa_expiry_date && !b.visa_expiry_date) return 0;
                if (!a.visa_expiry_date) return 1; // Put null dates at the end
                if (!b.visa_expiry_date) return -1; // Put null dates at the end
                
                // Convert dd/mm/yyyy to Date object for comparison
                const dateA = new Date(a.visa_expiry_date.split('/').reverse().join('-'));
                const dateB = new Date(b.visa_expiry_date.split('/').reverse().join('-'));
                
                // Sort in descending order (longest expiry date first)
                return dateB - dateA;
            });
            
            summaryHTML += '<div style="margin-top: 15px;">';
            visas.forEach(visa => {
                // Get visa type name from the selected option
                const visaTypeSelect = document.querySelector(`select[name*="visa_type_hidden"][value="${visa.visa_type_hidden}"]`);
                const visaTypeName = visaTypeSelect ? visaTypeSelect.textContent : (visa.visa_type_hidden || 'Not set');
                
                summaryHTML += `
                    <div class="visa-entry-compact" style="margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #28a745;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: center;">
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">VISA TYPE:</span>
                                <span class="summary-value" style="color: #212529; font-weight: 500;">${visaTypeName}</span>
                            </div>
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">EXPIRY DATE:</span>
                                <span class="summary-value" style="color: #212529;">${visa.visa_expiry_date || 'Not set'}</span>
                            </div>
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">GRANT DATE:</span>
                                <span class="summary-value" style="color: #212529;">${visa.visa_grant_date || 'Not set'}</span>
                            </div>
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">DESCRIPTION:</span>
                                <span class="summary-value" style="color: #212529;">${visa.visa_description || 'Not set'}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            summaryHTML += '</div>';
        } else {
            summaryHTML += '<div class="empty-state" style="margin-top: 15px;"><p>No visa details added yet.</p></div>';
        }
        
        summaryGrid.innerHTML = summaryHTML;
        
        // Return to summary view
        cancelEdit('visaInfo');
    });
};

/**
 * Save address information and update summary
 */
window.saveAddressInfo = function() {
    console.log('🚀 ====== saveAddressInfo START ======');
    console.log('🚀 Function called at:', new Date().toISOString());
    
    const $addressesContainer = $('#addresses-container');
    if (!$addressesContainer.length) {
        console.error('❌ #addresses-container not found!');
        alert('Error: Address container not found. Please refresh the page and try again.');
        return;
    }
    
    const $allWrappers = $addressesContainer.find('.address-entry-wrapper');
    console.log('🔍 Total address wrappers found:', $allWrappers.length);
    
    if ($allWrappers.length === 0) {
        console.error('❌ No address wrappers found!');
        alert('Error: No address entries found. Please refresh the page and try again.');
        return;
    }
    
    // Log each wrapper's details
    $allWrappers.each(function(i) {
        const $wrapper = $(this);
        console.log(`  Wrapper ${i}:`, {
            index: $wrapper.data('address-index'),
            hasTemplateClass: $wrapper.hasClass('address-template'),
            addressLine1: $wrapper.find('input[name="address_line_1[]"]').val(),
            isVisible: $wrapper.is(':visible')
        });
    });
    
    // Get all address entries, but exclude only the template ones
    // Note: We need to include the default empty entry (index 0) even if it has address-template class
    const $addressEntries = $addressesContainer.find('.address-entry-wrapper').filter(function() {
        const $entry = $(this);
        const index = $entry.data('address-index');
        // Include the first entry (index 0) even if it has address-template class
        // Only exclude entries that are actual templates (not the default empty entry)
        return index === 0 || !$entry.hasClass('address-template');
    });
    
    console.log('💾 Address entries to save:', $addressEntries.length);
    
    if ($addressEntries.length === 0) {
        console.error('❌ No valid address entries to save!');
        alert('Error: No valid address entries found. Please add at least one address.');
        return;
    }
    
    // Validation: Only require country and suburb
    let validationErrors = [];
    let hasAtLeastOneValidAddress = false;
    
    $addressEntries.each(function(idx) {
        const $entry = $(this);
        const addressLine1 = $.trim($entry.find('input[name="address_line_1[]"]').val() || '');
        const suburb = $.trim($entry.find('input[name="suburb[]"]').val() || '');
        const state = $.trim($entry.find('input[name="state[]"]').val() || '');
        const zip = $.trim($entry.find('input[name="zip[]"]').val() || '');
        const country = $.trim($entry.find('input[name="country[]"]').val() || '');
        
        console.log(`📝 Validating Address ${idx + 1}:`, {
            addressLine1: addressLine1 || '(empty)',
            suburb: suburb || '(empty)',
            state: state || '(empty)',
            zip: zip || '(empty)',
            country: country || '(empty)'
        });
        
        // Check if any field has data
        const hasAnyData = addressLine1 || suburb || state || zip || country;
        
        if (hasAnyData) {
            // Only require country and suburb - other fields are optional
            const missingFields = [];
            if (!suburb) missingFields.push('Suburb');
            if (!country) missingFields.push('Country');
            
            if (missingFields.length > 0) {
                validationErrors.push(`Address ${idx + 1} is incomplete. Missing: ${missingFields.join(', ')}`);
                console.warn(`⚠️ Address ${idx + 1} incomplete:`, missingFields);
            } else {
                hasAtLeastOneValidAddress = true;
                console.log(`✅ Address ${idx + 1} is valid (has suburb and country)`);
            }
        } else {
            console.log(`ℹ️ Address ${idx + 1} is empty (will be skipped)`);
        }
    });
    
    // Show validation errors
    if (validationErrors.length > 0) {
        console.error('❌ Validation failed:', validationErrors);
        alert('Please fix the following errors:\n\n' + validationErrors.join('\n'));
        return;
    }
    
    // Check if we have at least one valid address
    if (!hasAtLeastOneValidAddress) {
        console.error('❌ No valid addresses found');
        alert('Please add at least one address with suburb and country before saving.');
        return;
    }
    
    console.log('✅ Validation passed - preparing data...');
    
    const formData = new FormData();
    let addressCount = 0;
    
    $addressEntries.each(function(index) {
        const $entry = $(this);
        
        const addressId = $entry.find('input[name="address_id[]"]').val();
        const addressLine1 = $entry.find('input[name="address_line_1[]"]').val();
        const addressLine2 = $entry.find('input[name="address_line_2[]"]').val();
        const suburb = $entry.find('input[name="suburb[]"]').val();
        const state = $entry.find('input[name="state[]"]').val();
        const country = $entry.find('input[name="country[]"]').val();
        const zip = $entry.find('input[name="zip[]"]').val();
        const regionalCode = $entry.find('input[name="regional_code[]"]').val();
        const startDate = $entry.find('input[name="address_start_date[]"]').val();
        const endDate = $entry.find('input[name="address_end_date[]"]').val();
        
        // Only include addresses that have data
        if (addressLine1 || suburb || state || zip) {
            console.log(`📦 Packaging Address ${addressCount + 1}:`, {
                addressId: addressId || '(new)',
                addressLine1,
                suburb,
                state,
                zip,
                country
            });
            
            // Always append all fields, even if empty (except for addressId which can be empty for new entries)
            formData.append('address_id[]', addressId || '');
            formData.append('address_line_1[]', addressLine1 || '');
            formData.append('address_line_2[]', addressLine2 || '');
            formData.append('suburb[]', suburb || '');
            formData.append('state[]', state || '');
            formData.append('country[]', country || 'Australia');
            formData.append('zip[]', zip || '');
            formData.append('regional_code[]', regionalCode || '');
            formData.append('address_start_date[]', startDate || '');
            formData.append('address_end_date[]', endDate || '');
            
            addressCount++;
        }
    });
    
    console.log(`📤 Sending ${addressCount} addresses to server...`);
    
    // Check if saveSectionData exists
    if (typeof saveSectionData !== 'function') {
        console.error('❌ saveSectionData function not found!');
        alert('Error: Save function not available. Please refresh the page and try again.');
        return;
    }
    
    console.log('📡 Calling saveSectionData...');
    
    saveSectionData('addressInfo', formData, function() {
        console.log('✅ Server responded successfully');
        console.log('🔄 Reloading page...');
        window.location.reload();
    });
    
    console.log('🚀 ====== saveAddressInfo END ======');
};

function updateAddressSummary($entries) {
    const summaryView = document.getElementById('addressInfoSummary');
    let summaryHTML = '<div>';
    
    $entries.each(function() {
        const addressLine1 = $(this).find('input[name="address_line_1[]"]').val();
        const addressLine2 = $(this).find('input[name="address_line_2[]"]').val();
        const suburb = $(this).find('input[name="suburb[]"]').val();
        const state = $(this).find('input[name="state[]"]').val();
        const zip = $(this).find('input[name="zip[]"]').val();
        const country = $(this).find('input[name="country[]"]').val();
        const startDate = $(this).find('input[name="address_start_date[]"]').val();
        const endDate = $(this).find('input[name="address_end_date[]"]').val();
        
        const fullAddress = [addressLine1, addressLine2, suburb, state, zip, country]
            .filter(Boolean).join(', ');
        
        summaryHTML += `
            <div class="address-entry">
                <div class="summary-item">
                    <span class="summary-label">Full Address:</span>
                    <span class="summary-value">${fullAddress || 'Not set'}</span>
                </div>
                ${startDate ? `<div class="summary-item">
                    <span class="summary-label">Start Date:</span>
                    <span class="summary-value">${startDate}</span>
                </div>` : ''}
                ${endDate ? `<div class="summary-item">
                    <span class="summary-label">End Date:</span>
                    <span class="summary-value">${endDate}</span>
                </div>` : ''}
            </div>
        `;
    });
    
    summaryHTML += '</div>';
    summaryView.innerHTML = summaryHTML;
}

/**
 * Save travel information and update summary
 */
window.saveTravelInfo = function() {
    // Get all travel entries
    const container = document.getElementById('travelDetailsContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const travels = [];
    
    sections.forEach(section => {
        const travelId = section.querySelector('input[name*="travel_id"]')?.value;
        const countryVisited = section.querySelector('input[name*="travel_country_visited"]').value;
        const arrivalDate = section.querySelector('input[name*="travel_arrival_date"]').value;
        const departureDate = section.querySelector('input[name*="travel_departure_date"]').value;
        const travelPurpose = section.querySelector('input[name*="travel_purpose"]').value;
        
        if (countryVisited || arrivalDate || departureDate || travelPurpose) {
            travels.push({
                travel_id: travelId || '',
                country_visited: countryVisited,
                arrival_date: arrivalDate,
                departure_date: departureDate,
                purpose: travelPurpose
            });
        }
    });
    
    const formData = new FormData();
    formData.append('travels', JSON.stringify(travels));
    
    saveSectionData('travelInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('travelInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (travels.length > 0) {
            let summaryHTML = '';
            travels.forEach(travel => {
                summaryHTML += `
                    <div class="summary-item">
                        <span class="summary-label">Country Visited:</span>
                        <span class="summary-value">${travel.country_visited || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Arrival Date:</span>
                        <span class="summary-value">${travel.arrival_date || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Departure Date:</span>
                        <span class="summary-value">${travel.departure_date || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Travel Purpose:</span>
                        <span class="summary-value">${travel.purpose || 'Not set'}</span>
                    </div>
                `;
            });
            summaryGrid.innerHTML = summaryHTML;
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No travel details added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('travelInfo');
    });
};

/**
 * Save qualifications information and update summary
 */
window.saveQualificationsInfo = function() {
    // Get all qualification entries
    const container = document.getElementById('qualificationsContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const qualifications = [];
    
    sections.forEach(section => {
        const qualId = section.querySelector('input[name*="qualification_id"]')?.value;
        const qualification = section.querySelector('input[name*="qualification"]').value;
        const institution = section.querySelector('input[name*="institution"]').value;
        const country = section.querySelector('input[name*="qual_country"]').value;
        const year = section.querySelector('input[name*="year"]').value;
        
        if (qualification || institution || country || year) {
            qualifications.push({
                qualification_id: qualId || '',
                qualification: qualification,
                institution: institution,
                country: country,
                year: year
            });
        }
    });
    
    const formData = new FormData();
    formData.append('qualifications', JSON.stringify(qualifications));
    
    saveSectionData('qualificationsInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('qualificationsInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (qualifications.length > 0) {
            let summaryHTML = '';
            qualifications.forEach(qual => {
                summaryHTML += `
                    <div class="summary-item">
                        <span class="summary-label">Qualification:</span>
                        <span class="summary-value">${qual.qualification || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Institution:</span>
                        <span class="summary-value">${qual.institution || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Country:</span>
                        <span class="summary-value">${qual.country || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Year:</span>
                        <span class="summary-value">${qual.year || 'Not set'}</span>
                    </div>
                `;
            });
            summaryGrid.innerHTML = summaryHTML;
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No qualifications added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('qualificationsInfo');
    });
};

/**
 * Save experience information and update summary
 */
window.saveExperienceInfo = function() {
    // Get all experience entries
    const container = document.getElementById('experienceContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const experiences = [];
    
    sections.forEach(section => {
        const expId = section.querySelector('input[name*="experience_id"]')?.value;
        const company = section.querySelector('input[name*="company"]').value;
        const position = section.querySelector('input[name*="position"]').value;
        const startDate = section.querySelector('input[name*="exp_start_date"]').value;
        const endDate = section.querySelector('input[name*="exp_end_date"]').value;
        
        if (company || position || startDate || endDate) {
            experiences.push({
                experience_id: expId || '',
                company: company,
                position: position,
                start_date: startDate,
                end_date: endDate
            });
        }
    });
    
    const formData = new FormData();
    formData.append('experiences', JSON.stringify(experiences));
    
    saveSectionData('experienceInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('experienceInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (experiences.length > 0) {
            let summaryHTML = '';
            experiences.forEach(exp => {
                summaryHTML += `
                    <div class="summary-item">
                        <span class="summary-label">Company:</span>
                        <span class="summary-value">${exp.company || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Position:</span>
                        <span class="summary-value">${exp.position || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Start Date:</span>
                        <span class="summary-value">${exp.start_date || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">End Date:</span>
                        <span class="summary-value">${exp.end_date || 'Not set'}</span>
                    </div>
                `;
            });
            summaryGrid.innerHTML = summaryHTML;
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No work experience added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('experienceInfo');
    });
};

/**
 * Save additional information and update summary
 */
window.saveAdditionalInfo = function() {
    // Get form values
    const naatiTest = document.getElementById('naatiTest').value;
    const naatiDate = document.getElementById('naatiDate').value;
    const pyTest = document.getElementById('pyTest').value;
    const pyDate = document.getElementById('pyDate').value;
    
    const formData = new FormData();
    formData.append('naati_test', naatiTest);
    formData.append('naati_date', naatiDate);
    formData.append('py_test', pyTest);
    formData.append('py_date', pyDate);
    
    saveSectionData('additionalInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('additionalInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        summaryGrid.innerHTML = `
            <div class="summary-item">
                <span class="summary-label">NAATI Test:</span>
                <span class="summary-value">${naatiTest == '1' ? 'Yes' : 'No'}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">NAATI Date:</span>
                <span class="summary-value">${naatiDate || 'Not set'}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">PY Test:</span>
                <span class="summary-value">${pyTest == '1' ? 'Yes' : 'No'}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">PY Date:</span>
                <span class="summary-value">${pyDate || 'Not set'}</span>
            </div>
        `;
        
        // Return to summary view
        cancelEdit('additionalInfo');
    });
};

/**
 * Save character information and update summary
 */
window.saveCharacterInfo = function() {
    // Get all character entries
    const container = document.getElementById('characterContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const characters = [];
    
    sections.forEach(section => {
        const charId = section.querySelector('input[name*="character_id"]')?.value;
        const detail = section.querySelector('textarea[name*="character_detail"]').value;
        
        if (detail) {
            characters.push({
                character_id: charId || '',
                detail: detail
            });
        }
    });
    
    const formData = new FormData();
    formData.append('characters', JSON.stringify(characters));
    
    saveSectionData('characterInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('characterInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (characters.length > 0) {
            let summaryHTML = '';
            characters.forEach(character => {
                summaryHTML += `
                    <div class="summary-item">
                        <span class="summary-label">Detail:</span>
                        <span class="summary-value">${character.detail || 'Not set'}</span>
                    </div>
                `;
            });
            summaryGrid.innerHTML = summaryHTML;
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No character information added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('characterInfo');
    });
};

/**
 * Save partner information and update summary
 */
window.savePartnerInfo = function() {
    // Get all partner entries
    const container = document.getElementById('partnerContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const partners = [];
    
    sections.forEach(section => {
        const partnerId = section.querySelector('input[name*="partner_id"]')?.value;
        const details = section.querySelector('.partner-details').value;
        const relationshipType = section.querySelector('select[name*="partner_relationship_type"]').value;
        const gender = section.querySelector('select[name*="partner_gender"]').value;
        const companyType = section.querySelector('select[name*="partner_company_type"]').value;
        
        if (details || relationshipType || gender || companyType) {
            partners.push({
                partner_id: partnerId || '',
                details: details,
                relationship_type: relationshipType,
                gender: gender,
                company_type: companyType
            });
        }
    });
    
    const formData = new FormData();
    formData.append('partners', JSON.stringify(partners));
    
    saveSectionData('partnerInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('partnerInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (partners.length > 0) {
            let summaryHTML = '';
            partners.forEach(partner => {
                summaryHTML += `
                    <div class="summary-item">
                        <span class="summary-label">Details:</span>
                        <span class="summary-value">${partner.details || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Relationship:</span>
                        <span class="summary-value">${partner.relationship_type || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Gender:</span>
                        <span class="summary-value">${partner.gender || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Company Type:</span>
                        <span class="summary-value">${partner.company_type || 'Not set'}</span>
                    </div>
                `;
            });
            summaryGrid.innerHTML = summaryHTML;
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No partner information added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('partnerInfo');
    });
};

/**
 * Save children information and update summary
 */
window.saveChildrenInfo = function() {
    // Get all children entries
    const container = document.getElementById('childrenContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const children = [];
    
    sections.forEach(section => {
        const childId = section.querySelector('input[name*="children_id"]')?.value;
        const details = section.querySelector('.partner-details').value;
        const relationshipType = section.querySelector('select[name*="children_relationship_type"]').value;
        const gender = section.querySelector('select[name*="children_gender"]').value;
        const companyType = section.querySelector('select[name*="children_company_type"]').value;
        
        if (details || relationshipType || gender || companyType) {
            children.push({
                child_id: childId || '',
                details: details,
                relationship_type: relationshipType,
                gender: gender,
                company_type: companyType
            });
        }
    });
    
    const formData = new FormData();
    formData.append('children', JSON.stringify(children));
    
    saveSectionData('childrenInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('childrenInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (children.length > 0) {
            let summaryHTML = '';
            children.forEach(child => {
                summaryHTML += `
                    <div class="summary-item">
                        <span class="summary-label">Details:</span>
                        <span class="summary-value">${child.details || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Relationship:</span>
                        <span class="summary-value">${child.relationship_type || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Gender:</span>
                        <span class="summary-value">${child.gender || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Company Type:</span>
                        <span class="summary-value">${child.company_type || 'Not set'}</span>
                    </div>
                `;
            });
            summaryGrid.innerHTML = summaryHTML;
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No children information added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('childrenInfo');
    });
};

/**
 * Save EOI information and update summary
 */
window.saveEoiInfo = function() {
    // Get all EOI entries
    const container = document.getElementById('eoiReferencesContainer');
    const sections = container.querySelectorAll('.repeatable-section');
    const eois = [];
    
    sections.forEach(section => {
        const eoiId = section.querySelector('input[name*="eoi_id"]')?.value;
        const eoiNumber = section.querySelector('input[name*="EOI_number"]').value;
        const subclass = section.querySelector('input[name*="EOI_subclass"]').value;
        const occupation = section.querySelector('input[name*="EOI_occupation"]').value;
        const point = section.querySelector('input[name*="EOI_point"]').value;
        const state = section.querySelector('input[name*="EOI_state"]').value;
        const submissionDate = section.querySelector('input[name*="EOI_submission_date"]').value;
        const roi = section.querySelector('input[name*="EOI_ROI"]').value;
        const password = section.querySelector('input[name*="EOI_password"]').value;
        
        if (eoiNumber || subclass || occupation || point || state || submissionDate || roi || password) {
            eois.push({
                eoi_id: eoiId || '',
                eoi_number: eoiNumber,
                subclass: subclass,
                occupation: occupation,
                point: point,
                state: state,
                submission_date: submissionDate,
                roi: roi,
                password: password
            });
        }
    });
    
    const formData = new FormData();
    formData.append('eois', JSON.stringify(eois));
    
    saveSectionData('eoiInfo', formData, function() {
        // Update summary view on success
        const summaryView = document.getElementById('eoiInfoSummary');
        const summaryGrid = summaryView.querySelector('.summary-grid');
        
        if (eois.length > 0) {
            let summaryHTML = '';
            eois.forEach(eoi => {
                summaryHTML += `
                    <div class="summary-item">
                        <span class="summary-label">EOI Number:</span>
                        <span class="summary-value">${eoi.eoi_number || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Subclass:</span>
                        <span class="summary-value">${eoi.subclass || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Occupation:</span>
                        <span class="summary-value">${eoi.occupation || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Point:</span>
                        <span class="summary-value">${eoi.point || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">State:</span>
                        <span class="summary-value">${eoi.state || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Submission Date:</span>
                        <span class="summary-value">${eoi.submission_date || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">ROI:</span>
                        <span class="summary-value">${eoi.roi || 'Not set'}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Password:</span>
                        <span class="summary-value">${eoi.password ? '••••••••' : 'Not set'}</span>
                    </div>
                `;
            });
            summaryGrid.innerHTML = summaryHTML;
        } else {
            summaryView.innerHTML = '<div class="empty-state"><p>No EOI references added yet.</p></div>';
        }
        
        // Return to summary view
        cancelEdit('eoiInfo');
    });
};

/**
 * Remove phone field with confirmation
 */
window.removePhoneField = function(button) {
    if (confirm('Are you sure you want to remove this phone number?')) {
        button.closest('.repeatable-section').remove();
        validatePersonalPhoneNumbers();
    }
};

/**
 * Remove email field with confirmation
 */
window.removeEmailField = function(button) {
    if (confirm('Are you sure you want to remove this email address?')) {
        button.closest('.repeatable-section').remove();
        validatePersonalEmailTypes();
    }
};

/**
 * Remove passport field with confirmation
 */
window.removePassportField = function(button) {
    if (confirm('Are you sure you want to remove this passport detail?')) {
        const section = button.closest('.repeatable-section');
        
        if (!section) {
            return;
        }
        
        // Get the passport ID if it exists (for existing records)
        const passportIdInput = section.querySelector('input[name*="passport_id"]');
        if (passportIdInput && passportIdInput.value) {
            // Create hidden input to track deletion
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'delete_passport_ids[]';
            hiddenInput.value = passportIdInput.value;
            
            const form = document.getElementById('editClientForm');
            if (form) {
                form.appendChild(hiddenInput);
            }
        }
        
        // Remove the section from DOM
        section.remove();
    }
};

/**
 * Remove visa field with confirmation
 */
window.removeVisaField = function(button) {
    if (confirm('Are you sure you want to remove this visa detail?')) {
        button.closest('.repeatable-section').remove();
    }
};

/**
 * Remove address field with confirmation
 */
window.removeAddressField = function(button) {
    if (confirm('Are you sure you want to remove this address?')) {
        button.closest('.repeatable-section').remove();
    }
};

/**
 * Remove travel field with confirmation
 */
window.removeTravelField = function(button) {
    if (confirm('Are you sure you want to remove this travel detail?')) {
        button.closest('.repeatable-section').remove();
    }
};

/**
 * Remove qualification field with confirmation
 */
window.removeQualificationField = function(button) {
    if (confirm('Are you sure you want to remove this qualification?')) {
        button.closest('.repeatable-section').remove();
    }
};

/**
 * Remove experience field with confirmation
 */
window.removeExperienceField = function(button) {
    if (confirm('Are you sure you want to remove this work experience?')) {
        button.closest('.repeatable-section').remove();
    }
};

/**
 * Remove character field with confirmation
 */
window.removeCharacterField = function(button) {
    if (confirm('Are you sure you want to remove this character information?')) {
        button.closest('.repeatable-section').remove();
    }
};

/**
 * Remove EOI field with confirmation
 */
window.removeEoiField = function(button) {
    if (confirm('Are you sure you want to remove this EOI reference?')) {
        button.closest('.repeatable-section').remove();
    }
};

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
    // Determine icon based on notification type
    let icon = 'info-circle';
    if (type === 'success') {
        icon = 'check-circle';
    } else if (type === 'error') {
        icon = 'exclamation-circle';
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${icon}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds for errors, 3 seconds for others
    const duration = type === 'error' ? 5000 : 3000;
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, duration);
}

// Make functions globally available
// Global function assignments
window.initGoogleMaps = initGoogleMaps;
window.addPartnerRow = addPartnerRow;
window.removePartnerRow = removePartnerRow;
window.addEoiReference = addEoiReference;
window.toggleVisaDetails = toggleVisaDetails;
window.addPassportDetail = addPassportDetail;
window.addTravelDetail = addTravelDetail;
window.addAddress = addAddress;
window.addAnotherAddress = addAnotherAddress;
window.removeAddressEntry = removeAddressEntry;
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

// New scroll and modal functions
window.scrollToSection = scrollToSection;
window.toggleSidebar = toggleSidebar;
window.scrollToTop = scrollToTop;

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
    
    // Initialize scroll spy for sidebar navigation
    initScrollSpy();
    
    // Initialize Go to Top button
    initGoToTopButton();

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

    // Phone Verification Functions
    let currentContactId = null;
    let otpTimer = null;
    let resendTimer = null;
    let otpExpiryTime = null;

    /**
     * Send OTP to phone number
     */
    function sendOTP(contactId, phone, countryCode) {
        currentContactId = contactId;
        const fullPhone = countryCode + phone;
        
        // Show modal
        document.getElementById('otpPhoneDisplay').textContent = fullPhone;
        document.getElementById('otpVerificationModal').style.display = 'block';
        
        // Clear any previous messages
        hideOTPMessages();
        
        // Clear OTP inputs
        clearOTPInputs();
        
        // Disable verify button initially
        document.getElementById('verifyOTPBtn').disabled = true;
        
        // Send OTP request
        fetch('/admin/clients/phone/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                contact_id: contactId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showOTPSuccessMessage('Verification code sent to client! Please ask them to provide the code.');
                startOTPTimer(data.expires_in_seconds || 300);
                startResendTimer(30);
            } else {
                showOTPErrorMessage(data.message || 'Failed to send verification code');
            }
        })
        .catch(error => {
            console.error('Error sending OTP:', error);
            showOTPErrorMessage('Network error. Please try again.');
        });
    }

    /**
     * Verify OTP
     */
    function verifyOTP() {
        const otpCode = getOTPCode();
        
        if (otpCode.length !== 6) {
            showOTPErrorMessage('Please enter all 6 digits');
            return;
        }
        
        // Disable verify button
        document.getElementById('verifyOTPBtn').disabled = true;
        
        fetch('/admin/clients/phone/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                contact_id: currentContactId,
                otp_code: otpCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showOTPSuccessMessage('Phone number verified successfully!');
                
                // Update UI after a short delay
                setTimeout(() => {
                    updateVerificationStatus(currentContactId, true);
                    closeOTPModal();
                }, 1500);
            } else {
                showOTPErrorMessage(data.message || 'Invalid verification code');
                document.getElementById('verifyOTPBtn').disabled = false;
                
                // Clear OTP inputs on error
                if (data.message && data.message.includes('Invalid')) {
                    clearOTPInputs();
                    document.querySelector('.otp-digit[data-index="0"]').focus();
                }
            }
        })
        .catch(error => {
            console.error('Error verifying OTP:', error);
            showOTPErrorMessage('Network error. Please try again.');
            document.getElementById('verifyOTPBtn').disabled = false;
        });
    }

    /**
     * Resend OTP
     */
    function resendOTP() {
        if (!currentContactId) return;
        
        // Disable resend button temporarily
        document.getElementById('resendOTPBtn').disabled = true;
        
        fetch('/admin/clients/phone/resend-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                contact_id: currentContactId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showOTPSuccessMessage('New verification code sent to client! Please ask them for the updated code.');
                clearOTPInputs();
                startOTPTimer(data.expires_in_seconds || 300);
                startResendTimer(30);
            } else {
                showOTPErrorMessage(data.message || 'Failed to resend verification code');
                document.getElementById('resendOTPBtn').disabled = false;
            }
        })
        .catch(error => {
            console.error('Error resending OTP:', error);
            showOTPErrorMessage('Network error. Please try again.');
            document.getElementById('resendOTPBtn').disabled = false;
        });
    }

    /**
     * Close OTP modal
     */
    function closeOTPModal() {
        document.getElementById('otpVerificationModal').style.display = 'none';
        currentContactId = null;
        clearOTPTimers();
        clearOTPInputs();
        hideOTPMessages();
    }

    /**
     * Get OTP code from inputs
     */
    function getOTPCode() {
        let otpCode = '';
        for (let i = 0; i < 6; i++) {
            const digit = document.querySelector(`.otp-digit[data-index="${i}"]`).value;
            otpCode += digit || '';
        }
        return otpCode;
    }

    /**
     * Clear OTP inputs
     */
    function clearOTPInputs() {
        for (let i = 0; i < 6; i++) {
            const input = document.querySelector(`.otp-digit[data-index="${i}"]`);
            input.value = '';
            input.classList.remove('filled');
        }
    }

    /**
     * Start OTP expiry timer
     */
    function startOTPTimer(seconds) {
        clearOTPTimers();
        
        let timeLeft = seconds;
        const timerElement = document.getElementById('timerCountdown');
        
        otpTimer = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            timerElement.textContent = `${minutes}:${secs.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(otpTimer);
                showOTPErrorMessage('Verification code has expired');
                document.getElementById('verifyOTPBtn').disabled = true;
            }
            
            timeLeft--;
        }, 1000);
    }

    /**
     * Start resend timer
     */
    function startResendTimer(seconds) {
        let timeLeft = seconds;
        const resendBtn = document.getElementById('resendOTPBtn');
        const resendTimer = document.getElementById('resendTimer');
        const countdownElement = document.getElementById('resendCountdown');
        
        resendBtn.disabled = true;
        resendTimer.style.display = 'inline';
        
        resendTimer = setInterval(() => {
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(resendTimer);
                resendBtn.disabled = false;
                resendTimer.style.display = 'none';
            }
            
            timeLeft--;
        }, 1000);
    }

    /**
     * Clear OTP timers
     */
    function clearOTPTimers() {
        if (otpTimer) {
            clearInterval(otpTimer);
            otpTimer = null;
        }
        if (resendTimer) {
            clearInterval(resendTimer);
            resendTimer = null;
        }
    }

    /**
     * Show OTP error message
     */
    function showOTPErrorMessage(message) {
        const errorElement = document.getElementById('otpErrorMessage');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        document.getElementById('otpSuccessMessage').style.display = 'none';
    }

    /**
     * Show OTP success message
     */
    function showOTPSuccessMessage(message) {
        const successElement = document.getElementById('otpSuccessMessage');
        successElement.textContent = message;
        successElement.style.display = 'block';
        document.getElementById('otpErrorMessage').style.display = 'none';
    }

    /**
     * Hide OTP messages
     */
    function hideOTPMessages() {
        document.getElementById('otpErrorMessage').style.display = 'none';
        document.getElementById('otpSuccessMessage').style.display = 'none';
    }

    /**
     * Update verification status in UI
     */
    function updateVerificationStatus(contactId, isVerified) {
        const verifyBtn = document.querySelector(`button[data-contact-id="${contactId}"]`);
        if (verifyBtn) {
            if (isVerified) {
                const summaryItem = verifyBtn.closest('.summary-item');
                if (summaryItem) {
                    const verifiedBadge = document.createElement('span');
                    verifiedBadge.className = 'verified-badge';
                    verifiedBadge.innerHTML = '<i class="fas fa-check-circle"></i> Verified';
                    verifiedBadge.title = 'Verified on ' + new Date().toLocaleString();
                    
                    verifyBtn.replaceWith(verifiedBadge);
                }
            }
        }
    }

    // OTP Input Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Handle OTP input auto-focus and validation
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('otp-digit')) {
                const index = parseInt(e.target.dataset.index);
                const value = e.target.value;
                
                // Add filled class for styling
                if (value) {
                    e.target.classList.add('filled');
                } else {
                    e.target.classList.remove('filled');
                }
                
                // Auto-focus next input
                if (value && index < 5) {
                    const nextInput = document.querySelector(`.otp-digit[data-index="${index + 1}"]`);
                    if (nextInput) {
                        nextInput.focus();
                    }
                }
                
                // Enable verify button when all digits are entered
                const otpCode = getOTPCode();
                document.getElementById('verifyOTPBtn').disabled = otpCode.length !== 6;
            }
        });
        
        // Handle backspace navigation
        document.addEventListener('keydown', function(e) {
            if (e.target.classList.contains('otp-digit') && e.key === 'Backspace') {
                const index = parseInt(e.target.dataset.index);
                
                if (!e.target.value && index > 0) {
                    // Move to previous input if current is empty
                    const prevInput = document.querySelector(`.otp-digit[data-index="${index - 1}"]`);
                    if (prevInput) {
                        prevInput.focus();
                    }
                }
            }
        });
        
        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('otpVerificationModal').style.display === 'block') {
                closeOTPModal();
            }
        });
    });

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

    // One-time check of email verification status on page load
    // (Does NOT start continuous polling - polling starts when email section is opened)
    setTimeout(function() {
        const emailVerifyButtons = document.querySelectorAll('.btn-verify-email');
        if (emailVerifyButtons.length > 0) {
            console.log('🔄 Page load: Checking email verification status (one-time check, no continuous polling)');
            emailVerifyButtons.forEach(button => {
                const emailId = button.getAttribute('data-email-id');
                if (isValidEmailId(emailId)) {
                    checkEmailVerificationStatus(parseInt(emailId));
                }
            });
        }
    }, 1000); // Wait 1 second after page load
});

/**
 * Go back with refresh to ensure consistent information
 */
window.goBackWithRefresh = function() {
    // Check if we came from a client detail page
    const referrer = document.referrer;
    const currentUrl = window.location.href;
    
    // If we're on an edit page and came from a detail page, refresh the detail page
    if (referrer && referrer.includes('/admin/clients/detail/') && currentUrl.includes('/admin/clients/edit/')) {
        // Navigate back and force refresh
        window.location.href = referrer + (referrer.includes('?') ? '&' : '?') + '_t=' + Date.now();
    } else {
        // Fallback to normal back navigation
        window.history.back();
    }
};

/**
 * Email Verification Functions
 */

// Send email verification
window.sendEmailVerification = function(emailId, emailAddress) {
    if (!emailId || !emailAddress) {
        alert('Invalid email information');
        return;
    }

    if (!confirm(`Send verification email to ${emailAddress}?`)) {
        return;
    }

    // Show loading state
    const button = document.querySelector(`button[data-email-id="${emailId}"]`);
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    button.disabled = true;

    fetch('/admin/clients/email/send-verification', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            email_id: emailId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Verification email sent successfully! Please ask the client to check their email and click the verification link.');
            
            // Update button to show resend option with polling indicator
            button.innerHTML = '<i class="fas fa-redo"></i> Resend <i class="fas fa-spinner fa-spin" style="margin-left: 5px; font-size: 10px;"></i>';
            button.onclick = function() { resendEmailVerification(emailId, emailAddress); };
            
            // Start polling for verification status
            startEmailVerificationPolling(emailId);
        } else {
            alert('Error: ' + (data.message || 'Failed to send verification email'));
            button.innerHTML = originalContent;
        }
        button.disabled = false;
    })
    .catch(error => {
        console.error('Error sending verification email:', error);
        alert('Network error. Please try again.');
        button.innerHTML = originalContent;
        button.disabled = false;
    });
};

// Resend email verification
function resendEmailVerification(emailId, emailAddress) {
    if (!confirm(`Resend verification email to ${emailAddress}?`)) {
        return;
    }

    const button = document.querySelector(`button[data-email-id="${emailId}"]`);
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    button.disabled = true;

    fetch('/admin/clients/email/resend-verification', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            email_id: emailId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Verification email resent successfully!');
            
            // Start polling for verification status
            startEmailVerificationPolling(emailId);
        } else {
            alert('Error: ' + (data.message || 'Failed to resend verification email'));
        }
        button.innerHTML = originalContent;
        button.disabled = false;
    })
    .catch(error => {
        console.error('Error resending verification email:', error);
        alert('Network error. Please try again.');
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Update verification status in UI
function updateEmailVerificationStatus(emailId, isVerified) {
    const verifyBtn = document.querySelector(`button[data-email-id="${emailId}"]`);
    if (verifyBtn) {
        if (isVerified) {
            const summaryItem = verifyBtn.closest('.summary-item');
            if (summaryItem) {
                const verifiedBadge = document.createElement('span');
                verifiedBadge.className = 'verified-badge';
                verifiedBadge.innerHTML = '<i class="fas fa-check-circle"></i> Verified';
                verifiedBadge.title = 'Verified on ' + new Date().toLocaleString();
                
                // Replace the verify button with verified badge
                verifyBtn.parentNode.replaceChild(verifiedBadge, verifyBtn);
            }
        }
    }
    
    // Also update any detail view icons
    updateDetailViewEmailIcons(emailId, isVerified);
}

// Update email verification icons in detail views
function updateDetailViewEmailIcons(emailId, isVerified) {
    // Find the email address in detail views and update its icon
    const emailElements = document.querySelectorAll('span, div');
    emailElements.forEach(element => {
        if (element.textContent && element.textContent.includes('@')) {
            // Check if this element contains an email address
            const emailMatch = element.textContent.match(/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/);
            if (emailMatch) {
                const emailAddress = emailMatch[1];
                
                // Find the corresponding ClientEmail record (this would need to be passed from the backend)
                // For now, we'll update based on the email address pattern
                const iconElement = element.querySelector('i');
                if (iconElement) {
                    if (isVerified) {
                        iconElement.className = 'fas fa-check-circle verified-icon fa-lg';
                        iconElement.style.color = '#28a745';
                        iconElement.title = 'Verified on ' + new Date().toLocaleString();
                    } else {
                        iconElement.className = 'far fa-circle unverified-icon fa-lg';
                        iconElement.style.color = '#6c757d';
                        iconElement.title = 'Not verified';
                    }
                }
            }
        }
    });
}

/**
 * Validate if email ID is valid for polling
 */
function isValidEmailId(emailId) {
    return emailId && 
           emailId !== 'pending' && 
           emailId !== 'null' && 
           emailId !== 'undefined' &&
           emailId !== '' &&
           emailId !== '0' &&
           !isNaN(parseInt(emailId)) && 
           parseInt(emailId) > 0;
}

/**
 * Store active polling intervals for cleanup
 */
const activeEmailPollingIntervals = new Map();

// Check email verification status
function checkEmailVerificationStatus(emailId) {
    if (!emailId || emailId === 'pending') return;
    
    fetch(`/admin/clients/email/status/${emailId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.is_verified) {
            updateEmailVerificationStatus(emailId, true);
            
            // Show success notification
            showNotification('Email verified successfully!', 'success');
            
            // Refresh the page after a short delay to update all views
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error checking email verification status:', error);
    });
}

// Start polling for email verification status
function startEmailVerificationPolling(emailId) {
    if (!emailId || emailId === 'pending') return;
    
    // Stop any existing polling for this email ID
    if (activeEmailPollingIntervals.has(emailId)) {
        clearInterval(activeEmailPollingIntervals.get(emailId));
        activeEmailPollingIntervals.delete(emailId);
    }
    
    console.log(`  ↳ Starting continuous polling for email ID: ${emailId}`);
    
    // Check immediately
    checkEmailVerificationStatus(emailId);
    
    // Then check every 5 seconds for 2 minutes
    let pollCount = 0;
    const maxPolls = 24; // 2 minutes (24 * 5 seconds)
    
    const pollInterval = setInterval(() => {
        pollCount++;
        
        // Check if button still exists (not verified yet)
        const verifyBtn = document.querySelector(`button[data-email-id="${emailId}"]`);
        if (!verifyBtn) {
            // Button was replaced with verified badge, stop polling
            clearInterval(pollInterval);
            activeEmailPollingIntervals.delete(emailId);
            console.log(`  ↳ Stopped polling for email ID ${emailId} (verified)`);
            return;
        }
        
        checkEmailVerificationStatus(emailId);
        
        // Stop polling after max attempts
        if (pollCount >= maxPolls) {
            clearInterval(pollInterval);
            activeEmailPollingIntervals.delete(emailId);
            console.log(`  ↳ Stopped polling for email ID ${emailId} (max attempts reached)`);
            // Remove spinner from button
            if (verifyBtn && verifyBtn.innerHTML.includes('fa-spinner')) {
                verifyBtn.innerHTML = verifyBtn.innerHTML.replace('<i class="fas fa-spinner fa-spin" style="margin-left: 5px; font-size: 10px;"></i>', '');
            }
        }
    }, 5000); // Check every 5 seconds
    
    // Store interval for cleanup
    activeEmailPollingIntervals.set(emailId, pollInterval);
}

/**
 * Stop all email verification polling
 */
function stopAllEmailPolling() {
    console.log('🛑 Stopping all email verification polling');
    activeEmailPollingIntervals.forEach((interval, emailId) => {
        clearInterval(interval);
        console.log(`  ↳ Stopped polling for email ID: ${emailId}`);
    });
    activeEmailPollingIntervals.clear();
}

/**
 * Initialize email section polling (one-time status check + start polling for unverified)
 */
function initializeEmailSectionPolling() {
    console.log('🔄 Initializing email section polling');
    
    const emailSection = document.getElementById('emailAddressesSummary');
    if (!emailSection) {
        console.warn('⚠️ Email section not found');
        return;
    }
    
    const emailVerifyButtons = emailSection.querySelectorAll('.btn-verify-email');
    
    if (emailVerifyButtons.length === 0) {
        console.log('✅ No unverified emails, skipping polling');
        return;
    }
    
    console.log(`📧 Found ${emailVerifyButtons.length} unverified email(s)`);
    
    // First, do a one-time refresh of all email statuses
    emailVerifyButtons.forEach(button => {
        const emailId = button.getAttribute('data-email-id');
        
        if (isValidEmailId(emailId)) {
            console.log(`  ↳ Checking status for email ID: ${emailId}`);
            // Single check, not continuous polling yet
            checkEmailVerificationStatus(parseInt(emailId));
        } else {
            console.warn(`  ↳ Invalid email ID, skipping: ${emailId}`);
        }
    });
    
    // Then start continuous polling only for valid emails
    setTimeout(() => {
        emailVerifyButtons.forEach(button => {
            const emailId = button.getAttribute('data-email-id');
            
            if (isValidEmailId(emailId)) {
                startEmailVerificationPolling(parseInt(emailId));
            }
        });
    }, 1000); // Delay to avoid race condition with initial check
}

// ===== OCCUPATION & SKILLS FUNCTIONS =====

function addOccupation() {
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('occupationInfoSummary');
    const editView = document.getElementById('occupationInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('occupationInfo');
        // Wait a bit for the edit view to be displayed, then add the occupation
        setTimeout(() => {
            addOccupationRow();
        }, 100);
        return;
    }
    
    addOccupationRow();
}

function addOccupationRow() {
    const container = document.getElementById('occupationContainer');
    if (!container) {
        console.error('Occupation container not found');
        return;
    }
    
    const index = container.children.length;
    
    const newOccupationHTML = `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Occupation" onclick="removeOccupationField(this)"><i class="fas fa-trash"></i></button>
            <div class="content-grid">
                <div class="form-group">
                    <label>Skill Assessment</label>
                    <select name="skill_assessment_hidden[${index}]" class="skill-assessment-select">
                        <option value="">Select</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nominated Occupation</label>
                    <input type="text" name="nomi_occupation[${index}]" class="nomi_occupation" placeholder="Enter Occupation">
                    <div class="autocomplete-items"></div>
                </div>
                <div class="form-group">
                    <label>Occupation Code (ANZSCO)</label>
                    <input type="text" name="occupation_code[${index}]" class="occupation_code" placeholder="Enter Code">
                </div>
                <div class="form-group">
                    <label>Assessing Authority</label>
                    <input type="text" name="list[${index}]" class="list" placeholder="e.g., ACS, VETASSESS">
                </div>
                <div class="form-group">
                    <label>Target Visa Subclass</label>
                    <input type="text" name="visa_subclass[${index}]" class="visa_subclass" placeholder="e.g., 189, 190">
                </div>
                <div class="form-group">
                    <label>Assessment Date</label>
                    <input type="text" name="dates[${index}]" class="dates date-picker" placeholder="dd/mm/yyyy">
                </div>
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="text" name="expiry_dates[${index}]" class="expiry_dates date-picker" placeholder="dd/mm/yyyy">
                </div>
                <div class="form-group">
                    <label>Reference No</label>
                    <input type="text" name="occ_reference_no[${index}]" placeholder="Enter Reference No.">
                </div>
                <div class="form-group" style="align-items: center;">
                    <label style="margin-bottom: 0;">Relevant Occupation</label>
                    <input type="checkbox" name="relevant_occupation_hidden[${index}]" value="1" style="margin-left: 10px;">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newOccupationHTML);
    
    // Initialize date pickers for the new row
    initializeDatepickers();
    
    // Initialize autocomplete for nominated occupation
    initializeOccupationAutocomplete();
}

function removeOccupationField(button) {
    const section = button.closest('.repeatable-section');
    const confirmDelete = confirm('Are you sure you want to delete this occupation record?');
    
    if (confirmDelete) {
        section.remove();
    }
}

async function saveOccupationInfo() {
    const form = document.getElementById('editClientForm');
    if (!form) {
        showNotification('Form not found', 'error');
        return;
    }
    
    const formData = new FormData(form);
    formData.append('section', 'occupation');
    
    try {
        const response = await fetch('/admin/clients/save-section', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Occupation information saved successfully!', 'success');
            toggleEditMode('occupationInfo');
            // Refresh the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Error saving occupation information', 'error');
        }
    } catch (error) {
        console.error('Error saving occupation info:', error);
        showNotification('Error saving occupation information', 'error');
    }
}

// ===== ENGLISH TEST SCORES FUNCTIONS =====

function addTestScore() {
    // Check if we're in summary mode, if so switch to edit mode first
    const summaryView = document.getElementById('testScoreInfoSummary');
    const editView = document.getElementById('testScoreInfoEdit');
    
    if (summaryView && editView && summaryView.style.display !== 'none') {
        toggleEditMode('testScoreInfo');
        // Wait a bit for the edit view to be displayed, then add the test score
        setTimeout(() => {
            addTestScoreRow();
        }, 100);
        return;
    }
    
    addTestScoreRow();
}

function addTestScoreRow() {
    const container = document.getElementById('testScoresContainer');
    if (!container) {
        console.error('Test scores container not found');
        return;
    }
    
    const index = container.children.length;
    
    const newTestScoreHTML = `
        <div class="repeatable-section">
            <button type="button" class="remove-item-btn" title="Remove Test" onclick="removeTestScoreField(this)"><i class="fas fa-trash"></i></button>
            <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px;">
                <div class="form-group">
                    <label>Test Type</label>
                    <select name="test_type_hidden[${index}]" class="test-type-selector" onchange="updateTestScoreValidation(this, ${index})">
                        <option value="">Select Test Type</option>
                        <option value="IELTS">IELTS</option>
                        <option value="IELTS_A">IELTS Academic</option>
                        <option value="PTE">PTE</option>
                        <option value="TOEFL">TOEFL</option>
                        <option value="CAE">CAE</option>
                        <option value="OET">OET</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Listening</label>
                    <input type="text" name="listening[${index}]" class="listening" placeholder="Score" maxlength="5">
                </div>
                <div class="form-group">
                    <label>Reading</label>
                    <input type="text" name="reading[${index}]" class="reading" placeholder="Score" maxlength="5">
                </div>
                <div class="form-group">
                    <label>Writing</label>
                    <input type="text" name="writing[${index}]" class="writing" placeholder="Score" maxlength="5">
                </div>
                <div class="form-group">
                    <label>Speaking</label>
                    <input type="text" name="speaking[${index}]" class="speaking" placeholder="Score" maxlength="5">
                </div>
                <div class="form-group">
                    <label>Overall</label>
                    <input type="text" name="overall_score[${index}]" class="overall_score" placeholder="Overall" maxlength="5">
                </div>
                <div class="form-group">
                    <label>Test Date</label>
                    <input type="text" name="test_date[${index}]" class="test_date date-picker" placeholder="dd/mm/yyyy">
                </div>
                <div class="form-group">
                    <label>Reference No</label>
                    <input type="text" name="test_reference_no[${index}]" placeholder="Reference No.">
                </div>
                <div class="form-group" style="align-items: center;">
                    <label style="margin-bottom: 0;">Relevant Test</label>
                    <input type="checkbox" name="relevant_test_hidden[${index}]" value="1" style="margin-left: 10px;">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newTestScoreHTML);
    
    // Initialize date pickers for the new row
    initializeDatepickers();
}

function removeTestScoreField(button) {
    const section = button.closest('.repeatable-section');
    const confirmDelete = confirm('Are you sure you want to delete this test score record?');
    
    if (confirmDelete) {
        section.remove();
    }
}

async function saveTestScoreInfo() {
    const form = document.getElementById('editClientForm');
    if (!form) {
        showNotification('Form not found', 'error');
        return;
    }
    
    const formData = new FormData(form);
    formData.append('section', 'test_scores');
    
    try {
        const response = await fetch('/admin/clients/save-section', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Test score information saved successfully!', 'success');
            toggleEditMode('testScoreInfo');
            // Refresh the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Error saving test score information', 'error');
        }
    } catch (error) {
        console.error('Error saving test score info:', error);
        showNotification('Error saving test score information', 'error');
    }
}

// ===== TEST SCORE VALIDATION FUNCTIONS =====

function updateTestScoreValidation(selectElement, index) {
    const testType = selectElement.value;
    const container = selectElement.closest('.repeatable-section');
    
    if (!container) return;
    
    const listeningInput = container.querySelector('.listening');
    const readingInput = container.querySelector('.reading');
    const writingInput = container.querySelector('.writing');
    const speakingInput = container.querySelector('.speaking');
    const overallInput = container.querySelector('.overall_score');
    
    // Clear existing validation messages
    [listeningInput, readingInput, writingInput, speakingInput, overallInput].forEach(input => {
        if (input) {
            input.style.borderColor = '';
            const existingError = input.parentNode.querySelector('.validation-error');
            if (existingError) {
                existingError.remove();
            }
        }
    });
    
    if (!testType) return;
    
    // Set validation based on test type
    switch (testType) {
        case 'IELTS':
        case 'IELTS_A':
            // IELTS: 0-9 for each component, overall 0-9
            setValidationMessages(container, 'IELTS scores range from 0-9', '0-9');
            break;
        case 'PTE':
            // PTE: 10-90 for each component, overall 10-90
            setValidationMessages(container, 'PTE scores range from 10-90', '10-90');
            break;
        case 'TOEFL':
            // TOEFL: 0-30 for each component, overall 0-120
            setValidationMessages(container, 'TOEFL scores: components 0-30, overall 0-120', '0-30');
            break;
        case 'CAE':
            // CAE: A, B, C, D, E, F for each component, overall A-F
            setValidationMessages(container, 'CAE grades: A, B, C, D, E, F', 'A-F');
            break;
        case 'OET':
            // OET: A, B, C, D, E for each component, overall A-E
            setValidationMessages(container, 'OET grades: A, B, C, D, E', 'A-E');
            break;
    }
}

function setValidationMessages(container, message, range) {
    const inputs = container.querySelectorAll('.listening, .reading, .writing, .speaking, .overall_score');
    
    inputs.forEach(input => {
        // Add validation message
        const existingMsg = input.parentNode.querySelector('.validation-error');
        if (existingMsg) {
            existingMsg.remove();
        }
        
        const msgDiv = document.createElement('div');
        msgDiv.className = 'validation-error';
        msgDiv.style.fontSize = '11px';
        msgDiv.style.color = '#6c757d';
        msgDiv.style.marginTop = '2px';
        msgDiv.textContent = `${message}`;
        input.parentNode.appendChild(msgDiv);
        
        // Add input event listener for real-time validation
        input.addEventListener('input', function() {
            validateTestScoreInput(this, range);
        });
    });
}

function validateTestScoreInput(input, range) {
    const value = input.value.trim();
    
    if (!value) {
        input.style.borderColor = '';
        return true;
    }
    
    let isValid = false;
    
    switch (range) {
        case '0-9':
            isValid = /^[0-9](\.\d)?$/.test(value) && parseFloat(value) >= 0 && parseFloat(value) <= 9;
            break;
        case '10-90':
            isValid = /^\d{1,2}$/.test(value) && parseInt(value) >= 10 && parseInt(value) <= 90;
            break;
        case '0-30':
            isValid = /^\d{1,2}$/.test(value) && parseInt(value) >= 0 && parseInt(value) <= 30;
            break;
        case 'A-F':
        case 'A-E':
            isValid = /^[A-F]$/.test(value);
            break;
    }
    
    if (isValid) {
        input.style.borderColor = '#28a745';
    } else {
        input.style.borderColor = '#dc3545';
    }
    
    return isValid;
}

// ===== OCCUPATION AUTOCOMPLETE FUNCTIONS =====

function initializeOccupationAutocomplete() {
    const occupationInputs = document.querySelectorAll('.nomi_occupation');
    
    occupationInputs.forEach(input => {
        if (input.dataset.autocompleteInitialized) return;
        
        input.addEventListener('input', function() {
            const query = this.value;
            const autocompleteContainer = this.nextElementSibling;
            
            if (query.length < 2) {
                autocompleteContainer.innerHTML = '';
                return;
            }
            
            // Simple autocomplete - you can enhance this with actual API calls
            const occupations = [
                'Software Engineer', 'Data Analyst', 'Business Analyst', 'Project Manager',
                'Accountant', 'Marketing Manager', 'Sales Representative', 'Teacher',
                'Nurse', 'Doctor', 'Engineer', 'Architect', 'Lawyer', 'Consultant'
            ];
            
            const matches = occupations.filter(occ => 
                occ.toLowerCase().includes(query.toLowerCase())
            );
            
            autocompleteContainer.innerHTML = '';
            matches.forEach(match => {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.textContent = match;
                item.addEventListener('click', function() {
                    input.value = match;
                    autocompleteContainer.innerHTML = '';
                });
                autocompleteContainer.appendChild(item);
            });
        });
        
        input.dataset.autocompleteInitialized = 'true';
    });
}

// ===== INITIALIZATION FUNCTIONS =====

// Initialize occupation autocomplete and test score validation on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize occupation autocomplete for existing fields
    initializeOccupationAutocomplete();
    
    // Initialize test score validation for existing fields
    const existingTestSelectors = document.querySelectorAll('.test-type-selector');
    existingTestSelectors.forEach((selector, index) => {
        if (selector.value) {
            updateTestScoreValidation(selector, index);
        }
    });
});

