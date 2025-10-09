/**
 * English Proficiency Level Detection System
 * Department of Home Affairs (DHA) Requirements Implementation
 * 
 * CRITICAL DATE: August 7, 2025
 * English language test requirements changed significantly on this date.
 * This system automatically detects test dates and applies the correct requirements.
 * 
 * This file contains all functions for detecting and displaying English proficiency levels
 * based on official DHA requirements for Australian visa applications.
 * 
 * Supported Test Types:
 * - IELTS Academic/General Training
 * - PTE Academic
 * - TOEFL iBT
 * - CAE (Cambridge Advanced)
 * - OET (Occupational English Test)
 * - CELPIP General
 * - MET (Michigan English Test)
 * - LANGUAGECERT Academic
 * 
 * Proficiency Levels:
 * - Superior English (20 points)
 * - Proficient English (10 points)
 * - Competent English (0 points)
 * - Vocational English (0 points)
 * - Functional English (0 points)
 * - Below Functional English (0 points)
 */

// ===== HELPER FUNCTIONS =====

/**
 * Validate test date format (dd/mm/yyyy)
 * @param {string} dateString - Date string to validate
 * @returns {boolean} - True if valid format
 */
function isValidDateFormat(dateString) {
    const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!dateRegex.test(dateString)) {
        return false;
    }
    
    const [day, month, year] = dateString.split('/').map(Number);
    const date = new Date(year, month - 1, day);
    
    return date.getDate() === day && 
           date.getMonth() === month - 1 && 
           date.getFullYear() === year;
}

// ===== MAIN ENGLISH PROFICIENCY LEVEL DETECTION FUNCTION =====

/**
 * Main function to detect English proficiency level based on test type and scores
 * @param {string} testType - The type of English test (IELTS, PTE, TOEFL, etc.)
 * @param {object} scores - Object containing listening, reading, writing, speaking, overall scores
 * @returns {object|null} - Proficiency level object with level, color, and points
 */
function detectEnglishProficiencyLevel(testType, scores) {
    const { listening, reading, writing, speaking, overall } = scores;
    
    // Determine if test was taken before or after 7 August 2025
    // This is the CRITICAL DATE when English language test requirements changed
    const testDate = document.querySelector(`input[name*="test_date"]`).value;
    const isAfterAug2025 = testDate && new Date(testDate.split('/').reverse().join('-')) >= new Date('2025-08-07');
    
    // Validate test date format and provide feedback
    if (testDate && !isValidDateFormat(testDate)) {
        console.warn('Invalid test date format. Expected dd/mm/yyyy format.');
    }
    
    switch (testType) {
        case 'IELTS':
        case 'IELTS_A':
            return detectIELTSLevel(listening, reading, writing, speaking, overall);
        case 'PTE':
            return detectPTELevel(listening, reading, writing, speaking, overall, isAfterAug2025);
        case 'TOEFL':
            return detectTOEFLLevel(listening, reading, writing, speaking, overall, isAfterAug2025);
        case 'CAE':
            // CAE is only valid for Functional English before August 7, 2025
            if (isAfterAug2025) {
                return { level: 'CAE Not Accepted After Aug 7, 2025', color: '#dc3545', points: 0 };
            }
            return detectCAELevel(listening, reading, writing, speaking, overall);
        case 'OET':
            return detectOETLevel(listening, reading, writing, speaking, overall, isAfterAug2025);
        case 'CELPIP':
            return detectCELPIPLevel(listening, reading, writing, speaking, overall);
        case 'MET':
            return detectMETLevel(listening, reading, writing, speaking, overall);
        case 'LANGUAGECERT':
            return detectLANGUAGECERTLevel(listening, reading, writing, speaking, overall);
        default:
            return null;
    }
}

// ===== IELTS PROFICIENCY DETECTION =====

/**
 * Detect IELTS proficiency level
 * IELTS scores: 0-9 for each component
 */
function detectIELTSLevel(listening, reading, writing, speaking, overall) {
    const scores = [parseFloat(listening), parseFloat(reading), parseFloat(writing), parseFloat(speaking)];
    const avgScore = scores.reduce((sum, score) => sum + score, 0) / scores.length;
    
    // Superior English: 8.0 in each component
    if (scores.every(score => score >= 8.0)) {
        return { level: 'Superior English', color: '#28a745', points: 20 };
    }
    
    // Proficient English: 7.0 in each component
    if (scores.every(score => score >= 7.0)) {
        return { level: 'Proficient English', color: '#007bff', points: 10 };
    }
    
    // Competent English: 6.0 in each component
    if (scores.every(score => score >= 6.0)) {
        return { level: 'Competent English', color: '#17a2b8', points: 0 };
    }
    
    // Vocational English: 5.0 in each component (for tests after Aug 2025)
    // Vocational English: 5.0 in each component (for tests before Aug 2025) - same requirement
    if (scores.every(score => score >= 5.0)) {
        return { level: 'Vocational English', color: '#ffc107', points: 0 };
    }
    
    // Functional English: 4.5 average (same for both before and after Aug 2025)
    if (avgScore >= 4.5) {
        return { level: 'Functional English', color: '#fd7e14', points: 0 };
    }
    
    return { level: 'Below Functional English', color: '#dc3545', points: 0 };
}

// ===== PTE PROFICIENCY DETECTION =====

/**
 * Detect PTE Academic proficiency level
 * PTE scores: 10-90 for each component, 10-90 overall
 */
function detectPTELevel(listening, reading, writing, speaking, overall, isAfterAug2025) {
    const scores = [parseFloat(listening), parseFloat(reading), parseFloat(writing), parseFloat(speaking)];
    const overallScore = parseFloat(overall);
    
    // Superior English: 79 in each component
    if (scores.every(score => score >= 79)) {
        return { level: 'Superior English', color: '#28a745', points: 20 };
    }
    
    // Proficient English: 65 in each component
    if (scores.every(score => score >= 65)) {
        return { level: 'Proficient English', color: '#007bff', points: 10 };
    }
    
    // Competent English: 50 in each component
    if (scores.every(score => score >= 50)) {
        return { level: 'Competent English', color: '#17a2b8', points: 0 };
    }
    
    // Vocational English: Different requirements based on test date
    if (isAfterAug2025) {
        // After Aug 2025: 33 Listening, 36 Reading, 29 Writing, 24 Speaking
        if (listening >= 33 && reading >= 36 && writing >= 29 && speaking >= 24) {
            return { level: 'Vocational English', color: '#ffc107', points: 0 };
        }
    } else {
        // Before Aug 2025: 36 in each component
        if (scores.every(score => score >= 36)) {
            return { level: 'Vocational English', color: '#ffc107', points: 0 };
        }
    }
    
    // Functional English: 24 overall (after Aug 2025) or 30 overall (before Aug 2025)
    // According to DHA: PTE Academic has the same name but different test and scores after Aug 7, 2025
    const functionalThreshold = isAfterAug2025 ? 24 : 30;
    if (overallScore >= functionalThreshold) {
        return { level: 'Functional English', color: '#fd7e14', points: 0 };
    }
    
    return { level: 'Below Functional English', color: '#dc3545', points: 0 };
}

// ===== TOEFL PROFICIENCY DETECTION =====

/**
 * Detect TOEFL iBT proficiency level
 * TOEFL scores: 0-30 for each component, 0-120 overall
 */
function detectTOEFLLevel(listening, reading, writing, speaking, overall, isAfterAug2025) {
    const scores = [parseFloat(listening), parseFloat(reading), parseFloat(writing), parseFloat(speaking)];
    const overallScore = parseFloat(overall);
    
    // Superior English: 110 total, 28 Listening, 29 Reading, 30 Writing, 26 Speaking
    if (overallScore >= 110 && listening >= 28 && reading >= 29 && writing >= 30 && speaking >= 26) {
        return { level: 'Superior English', color: '#28a745', points: 20 };
    }
    
    // Proficient English: 94 total, 24 Listening, 24 Reading, 27 Writing, 23 Speaking
    if (overallScore >= 94 && listening >= 24 && reading >= 24 && writing >= 27 && speaking >= 23) {
        return { level: 'Proficient English', color: '#007bff', points: 10 };
    }
    
    // Competent English: 64 total, 4 Reading/Listening, 14 Writing/Speaking
    if (overallScore >= 64 && reading >= 4 && listening >= 4 && writing >= 14 && speaking >= 14) {
        return { level: 'Competent English', color: '#17a2b8', points: 0 };
    }
    
    // Vocational English: Different requirements based on test date
    if (isAfterAug2025) {
        // After Aug 2025: 8 Listening, 8 Reading, 9 Writing, 14 Speaking
        if (listening >= 8 && reading >= 8 && writing >= 9 && speaking >= 14) {
            return { level: 'Vocational English', color: '#ffc107', points: 0 };
        }
    } else {
        // Before Aug 2025: 4 Reading/Listening, 14 Writing/Speaking
        if (reading >= 4 && listening >= 4 && writing >= 14 && speaking >= 14) {
            return { level: 'Vocational English', color: '#ffc107', points: 0 };
        }
    }
    
    // Functional English: 26 total (after Aug 2025) or 32 total (before Aug 2025)
    // Note: TOEFL iBT was not approved from 26 July 2023 to 4 May 2024
    const functionalThreshold = isAfterAug2025 ? 26 : 32;
    if (overallScore >= functionalThreshold) {
        return { level: 'Functional English', color: '#fd7e14', points: 0 };
    }
    
    return { level: 'Below Functional English', color: '#dc3545', points: 0 };
}

// ===== CAE PROFICIENCY DETECTION =====

/**
 * Detect CAE (Cambridge Advanced) proficiency level
 * CAE grades: A, B, C, D, E, F
 */
function detectCAELevel(listening, reading, writing, speaking, overall) {
    // CAE uses letter grades A-F, with A being highest
    const gradeOrder = { 'A': 6, 'B': 5, 'C': 4, 'D': 3, 'E': 2, 'F': 1 };
    const scores = [gradeOrder[listening], gradeOrder[reading], gradeOrder[writing], gradeOrder[speaking]];
    const overallGrade = gradeOrder[overall];
    
    // Superior English: 200 in each component (Grade A)
    if (scores.every(score => score >= 6)) {
        return { level: 'Superior English', color: '#28a745', points: 20 };
    }
    
    // Proficient English: 185 in each component (Grade A)
    if (scores.every(score => score >= 5)) {
        return { level: 'Proficient English', color: '#007bff', points: 10 };
    }
    
    // Competent English: 169 in each component (Grade B)
    if (scores.every(score => score >= 5)) {
        return { level: 'Competent English', color: '#17a2b8', points: 0 };
    }
    
    // Vocational English: 154 in each component (Grade C) - for tests before Aug 2025
    if (scores.every(score => score >= 4)) { // Grade C = 4
        return { level: 'Vocational English', color: '#ffc107', points: 0 };
    }
    
    // Functional English: 147 overall (Grade C) - ONLY for tests before Aug 7, 2025
    // CAE is not accepted for Functional English after Aug 7, 2025
    if (overallGrade >= 4) {
        return { level: 'Functional English', color: '#fd7e14', points: 0 };
    }
    
    return { level: 'Below Functional English', color: '#dc3545', points: 0 };
}

// ===== OET PROFICIENCY DETECTION =====

/**
 * Detect OET (Occupational English Test) proficiency level
 * OET grades: A, B, C, D, E (alphabetical) or numerical scores (after Aug 2025)
 */
function detectOETLevel(listening, reading, writing, speaking, overall, isAfterAug2025) {
    // OET uses letter grades A-E, with A being highest
    const gradeOrder = { 'A': 5, 'B': 4, 'C': 3, 'D': 2, 'E': 1 };
    const scores = [gradeOrder[listening], gradeOrder[reading], gradeOrder[writing], gradeOrder[speaking]];
    
    // Superior English: Grade A in each component
    if (scores.every(score => score >= 5)) {
        return { level: 'Superior English', color: '#28a745', points: 20 };
    }
    
    // Proficient English: Grade B in each component
    if (scores.every(score => score >= 4)) {
        return { level: 'Proficient English', color: '#007bff', points: 10 };
    }
    
    // Competent English: Grade B in each component
    if (scores.every(score => score >= 4)) {
        return { level: 'Competent English', color: '#17a2b8', points: 0 };
    }
    
    // Vocational English: Different requirements based on test date
    if (isAfterAug2025) {
        // After Aug 2025: 220 Listening, 240 Reading, 200 Writing, 270 Speaking (numerical scores)
        const listeningNum = parseFloat(listening);
        const readingNum = parseFloat(reading);
        const writingNum = parseFloat(writing);
        const speakingNum = parseFloat(speaking);
        
        if (listeningNum >= 220 && readingNum >= 240 && writingNum >= 200 && speakingNum >= 270) {
            return { level: 'Vocational English', color: '#ffc107', points: 0 };
        }
    } else {
        // Before Aug 2025: Grade B in each component (alphabetical scores)
        if (scores.every(score => score >= 4)) { // Grade B = 4
            return { level: 'Vocational English', color: '#ffc107', points: 0 };
        }
    }
    
    // Functional English: 1020 overall (for tests after Aug 7, 2025)
    // OET scoring format changed from alphabetical to numerical on 7 August 2025
    const overallScore = parseFloat(overall);
    if (overallScore >= 1020) {
        return { level: 'Functional English', color: '#fd7e14', points: 0 };
    }
    
    return { level: 'Below Functional English', color: '#dc3545', points: 0 };
}

// ===== CELPIP PROFICIENCY DETECTION =====

/**
 * Detect CELPIP General proficiency level
 * CELPIP scores: 1-12 for each component, 1-12 overall
 */
function detectCELPIPLevel(listening, reading, writing, speaking, overall) {
    const scores = [parseFloat(listening), parseFloat(reading), parseFloat(writing), parseFloat(speaking)];
    
    // Superior English: 12 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 12)) {
        return { level: 'Superior English', color: '#28a745', points: 20 };
    }
    
    // Proficient English: 10 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 10)) {
        return { level: 'Proficient English', color: '#007bff', points: 10 };
    }
    
    // Competent English: 7 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 7)) {
        return { level: 'Competent English', color: '#17a2b8', points: 0 };
    }
    
    // Vocational English: 5 in each component (from DHA table)
    if (scores.every(score => score >= 5)) {
        return { level: 'Vocational English', color: '#ffc107', points: 0 };
    }
    
    // Functional English: 5 overall (from DHA Table 2 - tests after Aug 7, 2025)
    const overallScore = parseFloat(overall);
    if (overallScore >= 5) {
        return { level: 'Functional English', color: '#fd7e14', points: 0 };
    }
    
    return { level: 'Below Functional English', color: '#dc3545', points: 0 };
}

// ===== MET PROFICIENCY DETECTION =====

/**
 * Detect MET (Michigan English Test) proficiency level
 * MET scores: 0-100 for each component, 0-100 overall
 */
function detectMETLevel(listening, reading, writing, speaking, overall) {
    const scores = [parseFloat(listening), parseFloat(reading), parseFloat(writing), parseFloat(speaking)];
    
    // Superior English: 80 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 80)) {
        return { level: 'Superior English', color: '#28a745', points: 20 };
    }
    
    // Proficient English: 65 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 65)) {
        return { level: 'Proficient English', color: '#007bff', points: 10 };
    }
    
    // Competent English: 50 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 50)) {
        return { level: 'Competent English', color: '#17a2b8', points: 0 };
    }
    
    // Vocational English: 49 Listening, 47 Reading, 45 Writing, 38 Speaking (from DHA table)
    if (listening >= 49 && reading >= 47 && writing >= 45 && speaking >= 38) {
        return { level: 'Vocational English', color: '#ffc107', points: 0 };
    }
    
    // Functional English: 38 overall (from DHA Table 2 - tests after Aug 7, 2025)
    const overallScore = parseFloat(overall);
    if (overallScore >= 38) {
        return { level: 'Functional English', color: '#fd7e14', points: 0 };
    }
    
    return { level: 'Below Functional English', color: '#dc3545', points: 0 };
}

// ===== LANGUAGECERT PROFICIENCY DETECTION =====

/**
 * Detect LANGUAGECERT Academic proficiency level
 * LANGUAGECERT scores: 0-100 for each component, 0-100 overall
 */
function detectLANGUAGECERTLevel(listening, reading, writing, speaking, overall) {
    const scores = [parseFloat(listening), parseFloat(reading), parseFloat(writing), parseFloat(speaking)];
    
    // Superior English: 80 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 80)) {
        return { level: 'Superior English', color: '#28a745', points: 20 };
    }
    
    // Proficient English: 65 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 65)) {
        return { level: 'Proficient English', color: '#007bff', points: 10 };
    }
    
    // Competent English: 50 in each component (estimated - not in DHA tables)
    if (scores.every(score => score >= 50)) {
        return { level: 'Competent English', color: '#17a2b8', points: 0 };
    }
    
    // Vocational English: 41 Listening, 44 Reading, 45 Writing, 54 Speaking (from DHA table)
    if (listening >= 41 && reading >= 44 && writing >= 45 && speaking >= 54) {
        return { level: 'Vocational English', color: '#ffc107', points: 0 };
    }
    
    // Functional English: 38 overall (from DHA Table 2 - tests after Aug 7, 2025)
    const overallScore = parseFloat(overall);
    if (overallScore >= 38) {
        return { level: 'Functional English', color: '#fd7e14', points: 0 };
    }
    
    return { level: 'Below Functional English', color: '#dc3545', points: 0 };
}

// ===== DISPLAY AND UI FUNCTIONS =====

/**
 * Update the English proficiency display for a test score container
 * @param {HTMLElement} container - The container element for the test score form
 */
function updateEnglishProficiencyDisplay(container) {
    const testTypeSelect = container.querySelector('.test-type-selector');
    const listeningInput = container.querySelector('.listening');
    const readingInput = container.querySelector('.reading');
    const writingInput = container.querySelector('.writing');
    const speakingInput = container.querySelector('.speaking');
    const overallInput = container.querySelector('.overall_score');
    
    if (!testTypeSelect || !listeningInput || !readingInput || !writingInput || !speakingInput || !overallInput) {
        return;
    }
    
    const testType = testTypeSelect.value;
    const scores = {
        listening: listeningInput.value,
        reading: readingInput.value,
        writing: writingInput.value,
        speaking: speakingInput.value,
        overall: overallInput.value
    };
    
    // Check if all required fields are filled
    if (!testType || !scores.listening || !scores.reading || !scores.writing || !scores.speaking || !scores.overall) {
        hideProficiencyDisplay(container);
        return;
    }
    
    const proficiencyLevel = detectEnglishProficiencyLevel(testType, scores);
    
    if (proficiencyLevel) {
        showProficiencyDisplay(container, proficiencyLevel);
    } else {
        hideProficiencyDisplay(container);
    }
}

/**
 * Show the proficiency level display
 * @param {HTMLElement} container - The container element
 * @param {object} proficiencyLevel - The proficiency level object with level, color, and points
 */
function showProficiencyDisplay(container, proficiencyLevel) {
    // Remove existing display
    hideProficiencyDisplay(container);
    
    // Create proficiency display
    const displayDiv = document.createElement('div');
    displayDiv.className = 'english-proficiency-display';
    displayDiv.style.cssText = `
        margin-top: 10px;
        padding: 8px 12px;
        border-radius: 6px;
        background-color: ${proficiencyLevel.color}15;
        border-left: 4px solid ${proficiencyLevel.color};
        font-size: 13px;
        font-weight: 600;
    `;
    
    displayDiv.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: ${proficiencyLevel.color};">
                <i class="fas fa-language"></i> ${proficiencyLevel.level}
            </span>
            ${proficiencyLevel.points > 0 ? `<span style="color: #6c757d; font-size: 11px;">+${proficiencyLevel.points} points</span>` : ''}
        </div>
    `;
    
    // Insert after the form fields
    const formGroup = container.querySelector('.form-group:last-child');
    if (formGroup) {
        formGroup.parentNode.insertBefore(displayDiv, formGroup.nextSibling);
    }
}

/**
 * Hide the proficiency level display
 * @param {HTMLElement} container - The container element
 */
function hideProficiencyDisplay(container) {
    const existingDisplay = container.querySelector('.english-proficiency-display');
    if (existingDisplay) {
        existingDisplay.remove();
    }
}

// ===== EVENT LISTENERS AND INITIALIZATION =====

/**
 * Initialize English proficiency display functionality
 * This function sets up event listeners for real-time proficiency level updates
 */
function initializeEnglishProficiencyDisplay() {
    // Add event listeners to update proficiency display when scores change
    document.addEventListener('input', function(e) {
        if (e.target.matches('.listening, .reading, .writing, .speaking, .overall_score, .test-type-selector')) {
            const container = e.target.closest('.repeatable-section');
            if (container) {
                updateEnglishProficiencyDisplay(container);
            }
        }
    });
    
    // Also listen for changes on existing test score fields
    document.querySelectorAll('.test-type-selector').forEach(selector => {
        selector.addEventListener('change', function() {
            const container = this.closest('.repeatable-section');
            if (container) {
                updateEnglishProficiencyDisplay(container);
            }
        });
    });
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeEnglishProficiencyDisplay();
    calculateExistingTestScoreLevels();
});

/**
 * Calculate and display proficiency levels for existing test scores on page load
 */
function calculateExistingTestScoreLevels() {
    const calculationBoxes = document.querySelectorAll('.english-level-calculation-box');
    
    calculationBoxes.forEach((box, index) => {
        const testType = box.getAttribute('data-test-type');
        const listening = box.getAttribute('data-listening');
        const reading = box.getAttribute('data-reading');
        const writing = box.getAttribute('data-writing');
        const speaking = box.getAttribute('data-speaking');
        const overall = box.getAttribute('data-overall');
        const testDate = box.getAttribute('data-test-date');
        
        // Check if all required data is available
        if (!testType || !listening || !reading || !writing || !speaking || !overall) {
            const displayElement = document.getElementById(`proficiency-level-${index}`);
            if (displayElement) {
                displayElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Incomplete Data';
                displayElement.style.backgroundColor = '#f8d7da';
                displayElement.style.color = '#721c24';
                displayElement.style.border = '1px solid #f5c6cb';
            }
            return;
        }
        
        const scores = { listening, reading, writing, speaking, overall };
        const proficiencyLevel = detectEnglishProficiencyLevel(testType, scores);
        
        const displayElement = document.getElementById(`proficiency-level-${index}`);
        if (displayElement && proficiencyLevel) {
            displayElement.innerHTML = `
                <i class="fas fa-language"></i> ${proficiencyLevel.level}
                ${proficiencyLevel.points > 0 ? ` <span style="font-size: 0.8em; opacity: 0.8;">(+${proficiencyLevel.points} points)</span>` : ''}
            `;
            displayElement.style.backgroundColor = `${proficiencyLevel.color}15`;
            displayElement.style.color = proficiencyLevel.color;
            displayElement.style.border = `2px solid ${proficiencyLevel.color}`;
        } else if (displayElement) {
            displayElement.innerHTML = '<i class="fas fa-question-circle"></i> Unable to Calculate';
            displayElement.style.backgroundColor = '#f8d7da';
            displayElement.style.color = '#721c24';
            displayElement.style.border = '1px solid #f5c6cb';
        }
    });
}
