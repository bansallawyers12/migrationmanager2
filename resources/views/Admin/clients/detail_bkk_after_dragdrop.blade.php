@extends('layouts.admin_client_detail')
@section('title', 'Client Detail')

@section('content')
<link rel="stylesheet" href="{{URL::asset('css/bootstrap-datepicker.min.css')}}">

<!-- Drag and Drop Upload Styles for Personal Documents -->
<style>
/* Drag and Drop Upload Area Styles */
.drag-drop-area {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: #f9f9f9;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}

.drag-drop-area.drag-over {
    border-color: #007bff;
    background: #e3f2fd;
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.drag-drop-zone i {
    font-size: 2em;
    color: #666;
    margin-bottom: 10px;
    display: block;
}

.drag-drop-zone p {
    margin: 5px 0;
    color: #333;
    font-size: 14px;
}

.click-to-upload {
    color: #007bff;
    text-decoration: underline;
    cursor: pointer;
    font-weight: 500;
}

.click-to-upload:hover {
    color: #0056b3;
}

.drag-drop-zone small {
    color: #666;
    font-size: 12px;
    display: block;
    margin-top: 5px;
}

/* Upload Progress Bar Styles */
.upload-progress {
    margin-top: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: #e0e0e0;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 5px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #007bff, #0056b3);
    width: 0%;
    transition: width 0.3s ease;
    border-radius: 3px;
}

.progress-text {
    font-size: 12px;
    color: #666;
    display: block;
    text-align: center;
}

/* File Preview Styles */
.file-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.file-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 8px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
    max-width: 200px;
}

.file-item .remove-file {
    color: #dc3545;
    cursor: pointer;
    font-weight: bold;
    margin-left: auto;
}

.file-item .file-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .drag-drop-area {
        padding: 15px;
        min-height: 100px;
    }
    
    .drag-drop-zone i {
        font-size: 1.5em;
    }
    
    .drag-drop-zone p {
        font-size: 13px;
    }
}
</style>
<style>
     /* Start AI Tab CSS */
   .AIS-container {
        padding: 25px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
    }

    .AIS-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .AIS-header h3 {
        font-size: 1.4rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .AIS-header h3 i {
        margin-right: 10px;
        color: #6c757d;
    }

    .AIS-header .btn {
        background-color: #4a90e2 !important;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: background-color 0.3s ease, transform 0.1s ease;
    }

    .AIS-header .btn:hover {
        background-color: #357ab8 !important;
        transform: translateY(-1px);
    }

    .AI_term_list {
        display: grid;
        gap: 15px;
    }

    .AI-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e9ecef;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .AI-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .AI-card.pinned {
        border-left: 5px solid #ffd700;
        background: #fffdf5;
    }

    .AI-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .AI-header h4 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .AI-header h4 a {
        /*color: #2c3e50;*/
        color: #0d6efd !important;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .AI-header h4 a:hover {
        color: #4a90e2;
    }

    .pin-icon {
        color: #ffd700;
        font-size: 1.2rem;
    }

    .AI-body p {
        margin: 0 0 12px;
        color: #4a4a4a;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .AI-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
        padding-top: 10px;
        border-top: 1px solid #e9ecef;
    }

    .AI-meta {
        display: flex;
        flex-direction: column;
    }

    .author-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .author-initial {
        width: 28px;
        height: 28px;
        background: #4a90e2;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .author-name {
        font-weight: 500;
        color: #2c3e50;
    }

    .AI-timestamp small {
        color: #999;
    }

    .AI-actions .btn-link {
        color: #6c757d;
        text-decoration: none;
        padding: 5px;
    }

    .AI-actions .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .AI-actions .dropdown-item {
        font-size: 0.9rem;
        padding: 8px 15px;
        color: #2c3e50;
        transition: background-color 0.2s ease;
    }

    .AI-actions .dropdown-item:hover {
        background-color: #f1f5f9;
        color: #4a90e2;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .AIS-header {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }

        .AIS-header h3 {
            font-size: 1.2rem;
        }

        .AI-card {
            padding: 15px;
        }

        .AI-header h4 {
            font-size: 1rem;
        }

        .AI-body p {
            font-size: 0.9rem;
        }

        .AI-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
    /* End AI Tab CSS */

    /* Start Notes Tab CSS */
   .notes-container {
        padding: 25px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
    }

    .notes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .notes-header h3 {
        font-size: 1.4rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .notes-header h3 i {
        margin-right: 10px;
        color: #6c757d;
    }

    .notes-header .btn {
        background-color: #4a90e2 !important;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: background-color 0.3s ease, transform 0.1s ease;
    }

    .notes-header .btn:hover {
        background-color: #357ab8 !important;
        transform: translateY(-1px);
    }

    .note_term_list {
        display: grid;
        gap: 15px;
    }

    .note-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e9ecef;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .note-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .note-card.pinned {
        border-left: 5px solid #ffd700;
        background: #fffdf5;
    }

    .note-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .note-header h4 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .note-header h4 a {
        /*color: #2c3e50;*/
        color: #0d6efd !important;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .note-header h4 a:hover {
        color: #4a90e2;
    }

    .pin-icon {
        color: #ffd700;
        font-size: 1.2rem;
    }

    .note-body p {
        margin: 0 0 12px;
        color: #4a4a4a;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .note-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
        padding-top: 10px;
        border-top: 1px solid #e9ecef;
    }

    .note-meta {
        display: flex;
        flex-direction: column;
    }

    .author-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .author-initial {
        width: 28px;
        height: 28px;
        background: #4a90e2;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .author-name {
        font-weight: 500;
        color: #2c3e50;
    }

    .note-timestamp small {
        color: #999;
    }

    .note-actions .btn-link {
        color: #6c757d;
        text-decoration: none;
        padding: 5px;
    }

    .note-actions .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .note-actions .dropdown-item {
        font-size: 0.9rem;
        padding: 8px 15px;
        color: #2c3e50;
        transition: background-color 0.2s ease;
    }

    .note-actions .dropdown-item:hover {
        background-color: #f1f5f9;
        color: #4a90e2;
    }

    .subtab8-button{
        padding: 10px 20px;
        background: none;
        border: none;
        /*color: white;*/
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .subtab8-button:hover{
        background-color: #357ab8; /* Darker blue on hover */
        border-radius: 5px;
        color: white;
    }

    .subtab8-button.active {
        background-color: #357ab8; /* Darker blue for active tab */
        border-radius: 5px;
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .notes-header {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }

        .notes-header h3 {
            font-size: 1.2rem;
        }

        .note-card {
            padding: 15px;
        }

        .note-header h4 {
            font-size: 1rem;
        }

        .note-body p {
            font-size: 0.9rem;
        }

        .note-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
    /* End Notes Tab CSS */

    /* Start Documents Tab CSS*/
    .documentalls-container {
        padding: 0;
        background-color: #4a90e2; /* Blue background from screenshot */
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-top: 20px;
    }

    .subtabs {
        display: flex;
        background-color: #4a90e2;
        padding: 10px;
        border-radius: 8px 8px 0 0;
        gap: 10px;
    }
    .subtab-button.ml-auto {
        margin-left: auto;
    }

    .subtab-button , .subtab2-button, .subtab6-button{
        padding: 10px 20px;
        background: none;
        border: none;
        color: white;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .subtab-button:hover , .subtab2-button:hover, .subtab6-button:hover{
        background-color: #357ab8; /* Darker blue on hover */
        border-radius: 5px;
    }

    .subtab-button.active ,  .subtab2-button.active,  .subtab6-button.active {
        background-color: #357ab8; /* Darker blue for active tab */
        border-radius: 5px;
    }

    .subtab-content , .subtab2-content, .subtab6-content{
        padding: 15px;
        background: #fff; /* White background for content area */
        display: flex;
        flex-direction: row;
        gap: 20px;
    }

    .subtab-pane , .subtab2-pane, .subtab6-pane{
        display: none;
        width: 100%;
    }

    .subtab-pane.active ,  .subtab2-pane.active,  .subtab6-pane.active{
        display: flex;
        flex-direction: row;
        gap: 20px;
    }

    .subtab-header, .subtab2-header, .subtab6-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .subtab-header h3 , .subtab2-header h3, .subtab6-header h3{
        font-size: 1.5rem;
        color: #333;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .subtab-header h3 i , .subtab2-header h3 i, .subtab6-header h3 i{
        margin-right: 8px;
        color: #4a90e2;
    }

    .subtab-header .add-checklist-btn , .subtab2-header .add-checklist-btn, .subtab6-header .add-checklist-btn, .add_personal_doc_cat-btn,.add-visa-doc-category-btn {
        background-color: #4a90e2 !important; /* Blue button color */
        color: white;
        border: none;
        padding: 8px 16px;
        font-weight: 500;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .subtab-header .add-checklist-btn:hover , .subtab2-header .add-checklist-btn:hover, .subtab6-header .add-checklist-btn:hover, .add_personal_doc_cat-btn:hover,.add-visa-doc-category-btn:hover{
        background-color: #357ab8 !important; /* Darker blue on hover */
        color: white;
    }

    .checklist-table-container {
        /*flex: 3;*/
        flex: 1.5;
        overflow-x: auto;
    }

    .checklist-table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        color: #333;
    }

    .checklist-table thead {
        background: #f8f9fa; /* Light gray header */
    }

    .checklist-table th {
        padding: 10px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 1px solid #dee2e6;
    }

    .checklist-table tbody tr {
        border-bottom: 1px solid #dee2e6;
    }

    .checklist-table td {
        padding: 10px;
        color: #333;
    }

    .checklist-table td a.file-link {
        color: #4a90e2;
        text-decoration: none;
    }

    .checklist-table td a.file-link:hover {
        text-decoration: underline;
    }

    .checklist-table td small {
        color: #666;
    }

    .preview-pane {
        flex: 1;
        background: #ffffff;
        border-radius: 5px;
        padding: 15px;
        color: #333;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .subtabs {
            flex-direction: column;
            padding: 10px;
        }

        .subtab-button {
            padding: 8px 10px;
            font-size: 0.9rem;
        }

        .subtab-pane.active {
            flex-direction: column;
        }

        .subtab-header {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .checklist-table th,
        .checklist-table td {
            padding: 8px;
            font-size: 0.9rem;
        }

        .preview-pane {
            min-height: 150px;
            margin-top: 20px;
        }
    }

    /* End Documents Tab CSS */

    /* Start Accounts Tab Css  */

    .account-section {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
        max-width: 100%;
        min-width: 0;
    }
    .account-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 15px;
    }
    .account-section-header h2 {
        margin: 0;
        font-size: 1.3em;
        font-weight: 600;
        color: #005792;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .account-section-header .balance-display {
        text-align: right;
    }
    .account-section-header .balance-label {
        font-size: 0.9em;
        color: #6c757d;
        margin-bottom: 2px;
    }
    .account-section-header .balance-amount {
        font-size: 1.6em;
        font-weight: 700;
    }
    .account-section-header .balance-amount.funds-held { color: #28a745; }
    .account-section-header .balance-amount.outstanding { color: #ffc107; }

    /* --- Transaction Table Styling --- */
    .transaction-table-wrapper {
        flex-grow: 1;
        overflow-x: auto;
    }
    .transaction-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9em;
    }
    .transaction-table th, .transaction-table td {
        padding: 10px 8px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }
    .transaction-table thead th {
        font-weight: 600;
        color: #6c757d;
        background-color: #f8f9fa;
        white-space: nowrap;
        border-bottom-width: 2px;
        font-size: 0.85em;
        text-transform: uppercase;
    }
    .transaction-table tbody tr:last-child td {
        border-bottom: none;
    }
    .transaction-table tbody tr:hover {
        background-color: #f1f5f9;
    }
    .transaction-table td.currency {
        text-align: right;
        font-weight: 500;
        white-space: nowrap;
        font-family: monospace;
    }
    .transaction-table td.text-success {
        color: #28a745;
    }
    .transaction-table td.text-danger {
        color: #dc3545;
    }
    .transaction-table td.description {
        color: #6c757d;
        font-size: 0.95em;
    }
    .transaction-table td.balance {
        font-weight: 600;
        font-family: monospace;
        text-align: right;
    }
    .transaction-table td.type-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .transaction-table td .type-icon {
        font-size: 1em;
        width: 18px;
        text-align: center;
    }
    .transaction-table td .type-icon.fa-arrow-down,
    .transaction-table td .type-icon.fa-receipt {
        color: #28a745;
    }
    .transaction-table td .type-icon.fa-arrow-right-from-bracket {
        color: #17a2b8;
    }
    .transaction-table td .type-icon.fa-arrow-up,
    .transaction-table td .type-icon.fa-file-invoice {
        color: #dc3545;
    }
    .transaction-table td .type-icon.fa-undo {
        color: #ffc107;
    }

    .status-badge {
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.8em;
        font-weight: 500;
        text-transform: uppercase;
        white-space: nowrap;

    }
    .status-paid {
        background-color: rgba(40, 167, 69, 0.08);
        color: #28a745;
        border: 1px solid #28a745;
    }
    .status-unpaid {
        background-color: rgba(220, 53, 69, 0.08);
        color: #dc3545;
        border: 1px solid #dc3545;
    }
    .status-partial {
        background-color: rgba(255, 193, 7, 0.1);
        color: #b8860b;
        border: 1px solid #ffc107;
    }

    /* --- Main Layout Grid --- */
    .account-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 25px;
        overflow-x: hidden;
        max-width: 100%;
    }
    @media (min-width: 992px) {
        .account-layout {
            grid-template-columns: 1fr 1fr;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .account-section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .account-section-header .balance-display {
            text-align: left;
            margin-top: 10px;
        }
        .transaction-table {
            font-size: 0.85em;
        }
        .transaction-table th, .transaction-table td {
            padding: 8px 6px;
        }
    }

    /* End Accounts Tab CSS */

    /* Additional Accounts Tab CSS to prevent overflow */
    .account-section {
        overflow-x: hidden !important;
        max-width: 100% !important;
        min-width: 0 !important;
    }

    .account-layout {
        overflow-x: hidden !important;
        max-width: 100% !important;
    }

    .transaction-table-wrapper {
        overflow-x: auto !important;
        max-width: 100% !important;
    }

    .transaction-table {
        max-width: 100% !important;
        /*table-layout: fixed !important;*/
    }

    .account-section * {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Start Emails Tab CSS */
    .emails-container {
        padding: 0;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .subtabs {
        display: flex;
        background-color: #4a90e2; /* Blue background from screenshot */
        padding: 10px;
        border-radius: 8px 8px 0 0;
    }

    .subtab-button {
        padding: 10px 20px;
        background: none;
        border: none;
        color: white;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .subtab-button:hover {
        background-color: #357ab8; /* Darker blue on hover */
        border-radius: 5px;
    }

    .subtab-button.active {
        background-color: #357ab8; /* Darker blue for active tab */
        border-radius: 5px;
    }

    .subtab-content {
        padding: 0;
        background: #fff;
    }

    .subtab-pane {
        display: none;
    }

    .subtab-pane.active {
        display: block;
    }

    .email-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }

    .email-header h3 {
        font-size: 1.5rem;
        color: #333;
        margin: 0;
    }

    .email-actions {
        display: flex;
        gap: 10px;
    }

    .email-actions .btn {
        background-color: #6777ef !important;
        border: none;
        padding: 8px 16px;
        font-size: 0.9rem;
        transition: background-color 0.3s ease;
    }

    .email-actions .btn:hover {
        background-color: #5566cc !important;
    }

    .email-filters {
        display: flex;
        gap: 15px;
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
        background-color: #f9f9f9;
    }

    .filter-select {
        padding: 8px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        font-size: 0.9rem;
        color: #333;
        background-color: #fff;
        cursor: pointer;
        width: 150px;
    }

    .search-input {
        flex: 1;
        padding: 8px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        font-size: 0.9rem;
        color: #333;
    }

    .email-list,.email-list1 {
        display: grid;
        gap: 15px;
        padding: 20px;
    }

    .email-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .email-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .email-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .author-initial {
        width: 36px;
        height: 36px;
        background: #6777ef;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
    }

    .email-info {
        display: flex;
        flex-direction: column;
    }

    .author-name {
        font-weight: 500;
        color: #333;
        font-size: 1rem;
    }

    .email-timestamp {
        color: #999;
        font-size: 0.85rem;
    }

    .email-body h4 {
        margin: 0 0 5px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }

    .email-body p {
        margin: 0;
        color: #555;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .email-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .email-actions .btn-link {
        color: #FFF;
        text-decoration: none;
        font-size: 0.9rem;
        padding: 5px 10px;
        border: 1px solid #6777ef;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .email-actions .btn-link:hover {
        background-color: #6777ef;
        color: #fff;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 15px 0;
        border-top: 1px solid #dee2e6;
    }

    .pagination .btn {
        padding: 5px 10px;
        font-size: 0.9rem;
    }

    .pagination .btn-primary {
        background-color: #6777ef !important;
        border: none;
    }

    .pagination .btn-link {
        color: #6777ef;
        text-decoration: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .email-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .email-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .email-filters {
            flex-direction: column;
            gap: 10px;
        }

        .filter-select {
            width: 100%;
        }

        .email-card {
            padding: 10px;
        }

        .email-body h4 {
            font-size: 1rem;
        }

        .email-body p {
            font-size: 0.9rem;
        }

        .email-actions {
            flex-direction: column;
            gap: 5px;
        }

        .email-actions .btn-link {
            width: 100%;
            text-align: center;
        }
    }
    /* End Emails Tab CSS */

    .strike-through {
        text-decoration: line-through;
        color: #000;
    }


    /* Start Form generation Tab CSS */
    .forms-container {
        padding: 0;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .subtabs3 {
        display: flex;
        background-color: #4a90e2; /* Blue background from screenshot */
        padding: 10px;
        border-radius: 8px 8px 0 0;
    }

    .subtab3-button {
        padding: 10px 20px;
        background: none;
        border: none;
        color: white;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .subtab3-button:hover {
        background-color: #357ab8; /* Darker blue on hover */
        border-radius: 5px;
    }

    .subtab3-button.active {
        background-color: #357ab8; /* Darker blue for active tab */
        border-radius: 5px;
    }

    .subtab3-content {
        padding: 0;
        background: #fff;
    }

    .subtab3-pane {
        display: none;
    }

    .subtab3-pane.active {
        display: block;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }

    .form-header h3 {
        font-size: 1.5rem;
        color: #333;
        margin: 0;
    }

    .form-actions {
        display: flex;
        gap: 10px;
    }

    .form-actions .btn {
        background-color: #6777ef !important;
        border: none;
        padding: 8px 16px;
        font-size: 0.9rem;
        transition: background-color 0.3s ease;
    }

    .form-actions .btn:hover {
        background-color: #5566cc !important;
    }

    .form-filters {
        display: flex;
        gap: 15px;
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
        background-color: #f9f9f9;
    }

    .filter-select {
        padding: 8px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        font-size: 0.9rem;
        color: #333;
        background-color: #fff;
        cursor: pointer;
        width: 150px;
    }

    .search-input {
        flex: 1;
        padding: 8px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        font-size: 0.9rem;
        color: #333;
    }

    .form-list,.form-list1,.form-list2 {
        display: grid;
        gap: 15px;
        padding: 20px;
    }

    .form-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .form-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .form-author-initial {
        width: 36px;
        height: 36px;
        background: #6777ef;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
    }

    .form-info {
        display: flex;
        flex-direction: column;
    }

    .form-author-name {
        font-weight: 500;
        color: #333;
        font-size: 1rem;
    }

    .form-timestamp {
        color: #999;
        font-size: 0.85rem;
    }

    .form-body h4 {
        margin: 0 0 5px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }

    .form-body p {
        margin: 0;
        color: #555;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .form-actions .btn-link {
        color: #FFF;
        text-decoration: none;
        font-size: 0.9rem;
        padding: 5px 10px;
        border: 1px solid #6777ef;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .form-actions .btn-link:hover {
        background-color: #6777ef;
        color: #fff;
    }

    .form-pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 15px 0;
        border-top: 1px solid #dee2e6;
    }

    .form-pagination .btn {
        padding: 5px 10px;
        font-size: 0.9rem;
    }

    .form-pagination .btn-primary {
        background-color: #6777ef !important;
        border: none;
    }

    .form-pagination .btn-link {
        color: #6777ef;
        text-decoration: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .form-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .form-filters {
            flex-direction: column;
            gap: 10px;
        }

        .filter-select {
            width: 100%;
        }

        .form-card {
            padding: 10px;
        }

        .form-body h4 {
            font-size: 1rem;
        }

        .form-body p {
            font-size: 0.9rem;
        }

        .form-actions {
            flex-direction: column;
            gap: 5px;
        }

        .form-actions .btn-link {
            width: 100%;
            text-align: center;
        }
    }
    /* End Form generation Tab CSS */

    /*Start Personal Document Category Css */
    .subtab-header-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        margin-bottom: 10px;
    }

    .subtabs2,.subtabs6 {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .button-container:hover .action-buttons {
        display: inline-block !important;
    }

    .add_personal_doc_cat-btn {
        padding: 5px 10px;
        margin-top: 0;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .subtab-header-container {
            flex-direction: column;
            align-items: stretch;
        }
        .add_personal_doc_cat-btn {
            margin-top: 10px;
            width: 100%;
        }
    }
    /*End Personal Document Category Css */

     /*////////////////////////////////////////////
    ////// appointment popup css chnages start /////////
    //////////////////////////////////////////// */


.timeslots .timeslot_col.active{/*border:1px solid #0062cc;background-color:#fff;*/background-color: #007bff;color: #FFFFFF;margin: 0px 10px 8px 0px;}
#preloaderbook {
	display:none;
    background: #0d104d;
    background: -webkit-linear-gradient(to right, #0d104d, #28408b);
    background: linear-gradient(to right, #0d104d, #28408b);
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 5000;
}
#preloaderbook .circle-preloader {
    display: block;
    width: 60px;
    height: 60px;
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-bottom-color: #ffffff;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    animation: spin 2s infinite linear;
}

#loading, #loading_popup{
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    display: none;
    opacity: 0.7;
    background-color: #fff;
    z-index: 99;
    text-align: center;
}

#loading-image {
    position: absolute;
    top: 100px;
    left: 600px;
    z-index: 100;
}

#loading-image_popup {
    position: absolute;
    top: 100px;
    left: 100px;
    z-index: 100;
}

.services_item_title_span {
    font-size: 18px;
    line-height: 21px;
    color: #828F9A;
    display: inline-block;
    padding-left: 10px;
}

.services_item_price {
    float: right;
    display: inline-block;
    font-size: 24px;
    line-height: 30px;
    color: #53d56c;
    /* margin-top: 10px; */
}
.services_item_description {
    font-size: 14px;
    /* line-height: 18px; */
    color: #828F9A;
    display: inline-block;
    margin-bottom: 10px;
    margin-left: 25px;
    margin-top: 5px;
}
#datetimepicker {
    max-width: 330px;
    font-size: 14px;
    line-height: 21px;
    margin: 0px auto;
    background: #d3d4ec;
    padding: 8px;
    border-radius: 5px;
}
.timeslots .timeslot_col {
    display: flex;
    flex-direction: column;
    width: calc(33% - 10px);
    float: left;
    background: #d3d4ec;
    padding: 5px;
    margin: 0px 10px 10px 0px;
    text-align: center;
}

/*////////////////////////////////////////////
////// appointment popup css chnages end /////////
//////////////////////////////////////////// */
</style>
<?php
use App\Http\Controllers\Controller;
?>
<div class="crm-container">
    <main class="main-content" id="main-content">
        <div class="server-error">
            @include('../Elements/flash-message')
        </div>
        <div class="custom-error-msg">
        </div>
        <header class="client-header">
            <div class="client-identity">
                <h1 style="max-width:343px;">
                    <?php
                    if($id1) { //if client unique reference id is present in url
                        $matter_info_arr = \App\Models\ClientMatter::select('client_unique_matter_no')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                    ?>
                        {{$fetchedData->client_id}}-{{$matter_info_arr->client_unique_matter_no}}
                    <?php
                    } else {
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                        if($matter_cnt >0){
                            $matter_info_arr = \App\Models\ClientMatter::select('client_unique_matter_no')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                        ?>
                            {{$fetchedData->client_id}}-{{$matter_info_arr->client_unique_matter_no}}
                        <?php
                        } else {
                        ?>
                            {{$fetchedData->client_id}}
                        <?php
                        }
                    } ?>
                    <br/> {{$fetchedData->first_name}} {{$fetchedData->last_name}}
                </h1>
                <span class="">
                    <div class="author-mail_sms">
                        <a style="color: #0d6efd !important;" href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="sendmsg" title="Send Message"><i class="fas fa-comment-alt"></i></a>
                        <a style="color: #0d6efd !important;" href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a>
                        <a style="color: #0d6efd !important;" href="{{URL::to('/admin/clients/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>
                        @if($fetchedData->is_archived == 0)
                            <a style="color: #0d6efd !important;"  class="arcivedval" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'admins')" title="Archive"><i class="fas fa-archive"></i></a>
                        @else
                            <a style="color: #0d6efd !important;"  class="arcivedval" style="background-color:#007bff;" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'admins')" title="UnArchive"><i style="color: #fff;" class="fas fa-archive"></i></a>
                        @endif
                    </div>
                </span>

            </div>

            <style>
                .badge-outline {
                    display: inline-block;
                    padding: 5px 8px;
                    line-height: 12px;
                    border: 1px solid;
                    border-radius: 0.25rem;
                    font-weight: 400;
                    font-size: 13px;
                }
                .badge-outline.col-greenf.active {
                    background: #4caf50 !important;
                    color: #fff !important;
                }
                .col-greenf {
                    color: #9b9f9b !important;
                }
            </style>
            <div class="convert-lead-to-cient">
                <h4 class="author-box-name clientNameCls">
                    <a class="badge-outline col-greenf convertLeadToClient <?php if($fetchedData->type == 'client'){ echo 'active'; }?>" href="javascript:;" role="button">Client</a>
                    <a href="javascript:;" class="badge-outline col-greenf <?php if($fetchedData->type == 'lead'){ echo 'active'; } ?>">Lead</a>
                    <div class="card-header-action" style="margin-top: 5px;">
                        <!--<a href="javascript:;" id="open-rating-modal" class="btn btn-primary" style="padding: 0px 17px;border-radius:5px !important;margin-left: 5px;"><i class="fas fa-star"></i></a>-->
                        <a href="javascript:;" data-admin-id="{{ $fetchedData->id }}" title="Mark Star Client" id="mark-star-client-modal" class="btn btn-primary" style="padding: 0px 17px;border-radius:5px !important;margin-left: 5px;"><i class="fas fa-star"></i></a>

                        <div class="custom-switches" style="display: inline-block;float: left;margin-left: -25px;">
                            <label class="custom-switch">
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked>
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                    </div>
                </h4>
            </div>

            <div class="client-status">
                <span class="status-badge" style="background-color:#FFF !important;"><?php
                    $assign_info_arr = \App\Models\Admin::select('type')->where('id',@$fetchedData->id)->first();
                    ?>
                    @if($assign_info_arr->type == 'client')
                        <?php
                        $general_matter_list_arr = DB::table('client_matters')
                        ->select('client_matters.id','client_matters.client_unique_matter_no')
                        ->where('client_matters.client_id',@$fetchedData->id)
                        ->where('client_matters.sel_matter_id',1)
                        ->get();
                        if( !empty($general_matter_list_arr) && count($general_matter_list_arr)>0 ){ ?>
                            @foreach($general_matter_list_arr as $generallist)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input general_matter_checkbox_client_detail" type="checkbox" id="general_matter_checkbox_client_detail_{{$generallist->id}}" value="{{$generallist->id}}" data-clientuniquematterno="{{@$generallist->client_unique_matter_no}}">
                                    <label class="form-check-label" for="general_matter_checkbox_client_detail_{{$generallist->id}}">General Matter ({{@$generallist->client_unique_matter_no}})</label>
                                </div>
                            @endforeach
                        <?php
                        }?>

                        <?php
                        if($id1)
                        {
                            //if client_unique_matter_no is present in url
                            $matter_cnt = DB::table('client_matters')
                            ->select('client_matters.id')
                            ->where('client_matters.client_id',@$fetchedData->id)
                            ->where('client_matters.client_unique_matter_no',$id1)
                            ->where('client_matters.matter_status',1)
                            ->whereNotNull('client_matters.sel_matter_id')
                            ->count(); //dd($matter_cnt);
                            if( $matter_cnt >0 )
                            {
                                // Fetch all matters, but we'll sort them in Blade to prioritize the URL matter
                                $matter_list_arr = DB::table('client_matters')
                                ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                ->select('client_matters.id','client_matters.client_unique_matter_no','matters.title')
                                ->where('client_matters.client_id',@$fetchedData->id)
                                //->where('client_matters.client_unique_matter_no',$id1)
                                ->where('client_matters.matter_status',1)
                                ->where('client_matters.sel_matter_id','!=',1)
                                ->get(); //dd($matter_list_arr);
                                $clientmatter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                $latestClientMatterId = $clientmatter_info_arr ? $clientmatter_info_arr->id : null;

                                // Convert matter_list_arr to an array for sorting
                                $matter_list_arr = $matter_list_arr->toArray();
                                // Sort matters: URL matter ($id1) comes first, others follow
                                usort($matter_list_arr, function($a, $b) use ($id1) {
                                    if ($a->client_unique_matter_no == $id1 && $b->client_unique_matter_no != $id1) {
                                        return -1; // $a (URL matter) comes first
                                    } elseif ($a->client_unique_matter_no != $id1 && $b->client_unique_matter_no == $id1) {
                                        return 1; // $b (URL matter) comes first
                                    }
                                    return 0; // Maintain original order for other matters
                                });
                                ?>
                            <select name="matter_id" id="sel_matter_id_client_detail" class="form-control select2" data-valid="required" style="width: 200px !important;">
                                <option value="">Select Matters</option>
                                @foreach($matter_list_arr as $matterlist)
                                    <option value="{{$matterlist->id}}" {{ $matterlist->id == $latestClientMatterId ? 'selected' : '' }} data-clientuniquematterno="{{@$matterlist->client_unique_matter_no}}">{{@$matterlist->title}}({{@$matterlist->client_unique_matter_no}})</option>
                                @endforeach
                            </select>
                        <?php
                            }
                        }
                        else
                        {
                            $matter_cnt = DB::table('client_matters')
                            ->select('client_matters.id')
                            ->where('client_matters.client_id',@$fetchedData->id)
                            ->where('client_matters.matter_status',1)
                            ->whereNotNull('client_matters.sel_matter_id')
                            ->count(); //dd($matter_cnt);
                            if( $matter_cnt >0 )
                            {
                                $matter_list_arr = DB::table('client_matters')
                                ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                ->select('client_matters.id','client_matters.client_unique_matter_no','matters.title')
                                ->where('client_matters.client_id',@$fetchedData->id)
                                ->where('client_matters.matter_status',1)
                                ->where('client_matters.sel_matter_id','!=',1)
                                ->orderBy('client_matters.created_at', 'desc')
                                ->get();
                                $latestClientMatter = \App\Models\ClientMatter::where('client_id',$fetchedData->id)->where('matter_status',1)->latest()->first();
                                $latestClientMatterId = $latestClientMatter ? $latestClientMatter->id : null;
                                ?>
                            <select name="matter_id" id="sel_matter_id_client_detail" class="form-control select2" data-valid="required" style="width: 200px;">
                                <option value="">Select Matters</option>
                                @foreach($matter_list_arr as $matterlist)
                                    <option value="{{$matterlist->id}}" {{ $matterlist->id == $latestClientMatterId ? 'selected' : '' }} data-clientuniquematterno="{{@$matterlist->client_unique_matter_no}}">{{@$matterlist->title}}({{@$matterlist->client_unique_matter_no}})</option>
                                @endforeach
                            </select>
                        <?php
                            }
                        }
                        ?>
                    @endif
                </span>
                <button class="btn btn-primary btn-sm not_picked_call" datatype="not_picked_call"  style="background-color:#0d6efd !important;padding: 8px 15px;"> NP</button>
                <button class="btn btn-primary btn-sm create_note_d" datatype="note" style="background-color:#0d6efd !important;padding: 8px 15px;"><i class="fas fa-plus"></i> Add Notes</button>
             </div>
        </header>

        <h5 style="text-align: right;">
            <?php
            // Initialize $workflow_stage_arr to null to avoid undefined variable issues
            $workflow_stage_arr = null;

            // Check if $id1 is set and truthy
            if ($id1) {
                $workflow_stage_arr = DB::table('client_matters')
                    ->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
                    ->select('workflow_stages.name')
                    ->where('client_id', $fetchedData->id)
                    ->where('client_unique_matter_no', $id1)
                    ->first();
            } else {
                $clientMatterInfo = DB::table('client_matters')
                    ->select('client_unique_matter_no')
                    ->where('client_id', $fetchedData->id)
                    ->where('matter_status', 1)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($clientMatterInfo) {
                    $workflow_stage_arr = DB::table('client_matters')
                        ->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
                        ->select('workflow_stages.name')
                        ->where('client_id', $fetchedData->id)
                        ->where('client_unique_matter_no', $clientMatterInfo->client_unique_matter_no)
                        ->first();
                }
            }

            // Check if $workflow_stage_arr is set and not null before accessing its property
            if ($workflow_stage_arr) {
                echo $workflow_stage_arr->name;
            } else {
                // Handle the case where $workflow_stage_arr is not set
                echo "";
            }
            ?>
        </h5>
        <nav class="content-tabs">
            <?php
            $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
            if( isset($id1) && $id1 != "" || $matter_cnt >0 )
            {  //if client unique reference id is present in url
            ?>
                <button class="tab-button active" data-tab="personaldetails">Personal Details</button>
                <button class="tab-button" data-tab="noteterm">Notes</button>
                <button class="tab-button" data-tab="documentalls">Documents</button>
                <button class="tab-button" data-tab="accounts">Accounts</button>
                <button class="tab-button" data-tab="conversations">Emails</button>
                <button class="tab-button" data-tab="artificialintelligences">Matter AI</button>
                <button class="tab-button" data-tab="formgenerations">Form Generation</button>
                 <button class="tab-button" data-tab="appointments">Appointments</button>
            <?php
            }
            else
            {  //If no matter is exist
            ?>
                <button class="tab-button active" data-tab="personaldetails">Personal Details</button>
                <button class="tab-button" data-tab="noteterm">Notes</button>
                <button class="tab-button" data-tab="documentalls">Documents</button>
                <button class="tab-button" data-tab="artificialintelligences">Matter AI</button>
                <button class="tab-button" data-tab="formgenerationsL">Form Generation</button>
                <button class="tab-button" data-tab="appointments">Appointments</button>

            <?php
            }
            ?>
        </nav>

        <!-- Tab Contents -->
        <div class="tab-content" id="tab-content">
            <!-- Personal Details Tab (Default Active) -->
            <div class="tab-pane active" id="personaldetails-tab">
                <div class="content-grid">
                    <div class="card">
                        <h3><i class="fas fa-user"></i> Personal Information</h3>
                        <div class="field-group">
                            <span class="field-label">Age</span>
                            <span class="field-value">
                                <?php
                                if ( isset($fetchedData->age) && $fetchedData->age != '') {
                                    $verifiedDob = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('dob_verified_date')->first();
                                    if ( $verifiedDob) {
                                        $verifiedDobTick = '<i class="fas fa-check-circle verified-icon fa-lg"></i>';
                                    } else {
                                        $verifiedDobTick = '<i class="far fa-circle unverified-icon fa-lg"></i>';
                                    }
                                    echo $fetchedData->age.' '.$verifiedDobTick;
                                } else {
                                    echo 'N/A';
                                } ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Gender</span>
                            <span class="field-value">
                                <?php
                                if ( isset($fetchedData->gender) && $fetchedData->gender != '') {
                                    echo $fetchedData->gender;
                                } else {
                                    echo 'N/A';
                                } ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Marital Status</span>
                            <span class="field-value">
                                <?php
                                if ( isset($fetchedData->martial_status) && $fetchedData->martial_status != '') {
                                    echo $fetchedData->martial_status;
                                } else {
                                    echo 'N/A';
                                } ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Client Email</span>
                            <span class="field-value">
                                <?php
                                if( \App\Models\ClientEmail::where('client_id', $fetchedData->id)->exists()) {
                                    $clientEmails = \App\Models\ClientEmail::select('email','email_type')->where('client_id', $fetchedData->id)->get();
                                } else {
                                    if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                        $clientEmails = \App\Models\Admin::select('email','email_type')->where('id', $fetchedData->id)->get();
                                    } else {
                                        $clientEmails = array();
                                    }
                                } //dd($clientEmails);
                                if( !empty($clientEmails) && count($clientEmails)>0 ){
                                    $emailStr = "";
                                    foreach($clientEmails as $emailKey=>$emailVal){
                                        $verifiedEmail = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('email_verified_date')->first();

                                        //Check email is verified or not
                                        $check_verified_email = $emailVal->email_type."".$emailVal->email;
                                        if( isset($emailVal->email_type) && $emailVal->email_type != "" ){
                                            if( $emailVal->email_type == "Personal" ){
                                                if ( $verifiedEmail) {
                                                    //$emailStr .= $emailVal->email.'('.$emailVal->email_type .') <i class="fas fa-check-circle verified-icon fa-lg"></i> <br/>';
                                                    $emailStr .= $emailVal->email.' <i class="fas fa-check-circle verified-icon fa-lg"></i> <br/>';
                                                } else {
                                                    //$emailStr .= $emailVal->email.'('.$emailVal->email_type .') <i class="far fa-circle unverified-icon fa-lg"></i> <br/>';
                                                    $emailStr .= $emailVal->email.' <i class="far fa-circle unverified-icon fa-lg"></i> <br/>';
                                                }
                                            } else {
                                                //$emailStr .= $emailVal->email.'('.$emailVal->email_type .') <br/>';
                                                $emailStr .= $emailVal->email.' <br/>';
                                            }
                                        } else {
                                            $emailStr .= $emailVal->email.' <br/>';
                                        }
                                    }
                                    echo $emailStr;
                                } else {
                                    echo "N/A";
                                }?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Client Phone</span>
                            <span class="field-value">
                                <?php
                                if( \App\Models\ClientContact::where('client_id', $fetchedData->id)->exists()) {
                                    $clientContacts = \App\Models\ClientContact::select('phone','country_code','contact_type')->where('client_id', $fetchedData->id)->where('contact_type', '!=', 'Not In Use')->get();
                                } else {
                                    if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                        $clientContacts = \App\Models\Admin::select('phone','country_code','contact_type')->where('id', $fetchedData->id)->get();
                                    } else {
                                        $clientContacts = array();
                                    }
                                } //dd($clientContacts);
                                if( !empty($clientContacts) && count($clientContacts)>0 ){
                                    $phonenoStr = "";
                                    foreach($clientContacts as $conKey=>$conVal){
                                        //Check phone is verified or not
                                        $check_verified_phoneno = $conVal->country_code."".$conVal->phone;
                                        $verifiedNumber = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('phone_verified_date')->first();
                                        if( isset($conVal->country_code) && $conVal->country_code != "" ){
                                            $country_code = $conVal->country_code;
                                        } else {
                                            $country_code = "";
                                        }

                                        if( isset($conVal->contact_type) && $conVal->contact_type != "" ){
                                            if( $conVal->contact_type == "Personal" ){
                                                if ( $verifiedNumber) {
                                                    //$phonenoStr .= $country_code."".$conVal->phone.'('.$conVal->contact_type .') <i class="fas fa-check-circle verified-icon fa-lg"></i> <br/>';
                                                    $phonenoStr .= $country_code."".$conVal->phone.' <i class="fas fa-check-circle verified-icon fa-lg"></i> <br/>';
                                                } else {
                                                    //$phonenoStr .= $country_code."".$conVal->phone.'('.$conVal->contact_type .') <i class="far fa-circle unverified-icon fa-lg"></i> <br/>';
                                                    $phonenoStr .= $country_code."".$conVal->phone.' <i class="far fa-circle unverified-icon fa-lg"></i> <br/>';
                                                }
                                            } else {
                                                //$phonenoStr .= $country_code."".$conVal->phone.'('.$conVal->contact_type .') <br/>';
                                                $phonenoStr .= $country_code."".$conVal->phone.' <br/>';
                                            }
                                        } else {
                                            $phonenoStr .= $country_code."".$conVal->phone.' <br/>';
                                        }
                                    }
                                    echo $phonenoStr;
                                } else {
                                    echo "N/A";
                                }?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Residential Address</span>
                            <span class="field-value">
                                <?php
                                $postcode_Info = App\ClientAddress::select('zip','address')->where('client_id', $fetchedData->id)->latest('id')->first();
                                if( $postcode_Info && $postcode_Info->zip != "" ){ echo $postcode_Info->zip; } else { echo 'N/A'; }
                                ?>

                                <?php
                                if( $postcode_Info && $postcode_Info->address != "" ){ echo ' / '.$postcode_Info->address; } else { echo ' / '.'N/A'; }
                                ?>
                            </span>
                        </div>
                    </div>


                    <div class="card">
                        <h3><i class="fas fa-passport"></i>Visa</h3>
                        <div class="field-group">
                            <span class="field-label">Country Of Passport</span>
                            <span class="field-value">
                                <?php
                                $visa_Info = App\ClientVisaCountry::select('visa_country','visa_type','visa_expiry_date','visa_grant_date','visa_description')->where('client_id', $fetchedData->id)->latest('id')->first();
                                if( $visa_Info && $visa_Info->visa_country != "" ){ echo $visa_Info->visa_country; } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>
                        <div class="field-group">
                            <span class="field-label">Visa Type / Stream</span>
                            <span class="field-value">
                                <?php
                                if( $visa_Info && $visa_Info->visa_type != "" ){
                                    $Matter_get = App\Matter::select('id','title','nick_name')->where('id',$visa_Info->visa_type)->first();
                                    if(!empty($Matter_get)){
                                        $verifiedVisa = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('visa_expiry_verified_at')->first();
                                        if ( $verifiedVisa) {
                                            $verifiedVisaTick = '<i class="fas fa-check-circle verified-icon fa-lg"></i>';
                                        } else {
                                            $verifiedVisaTick = '<i class="far fa-circle unverified-icon fa-lg"></i>';
                                        }
                                        echo $Matter_get->title.'('.$Matter_get->nick_name.') '.$verifiedVisaTick;
                                    } else {
                                        echo 'N/A';
                                    }
                                } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>
                        <div class="field-group">
                            <span class="field-label">Visa Expiry Date</span>
                            <span class="field-value">
                                <?php
                                if( $visa_Info && $visa_Info->visa_expiry_date != "" ){
                                    if( $visa_Info->visa_expiry_date == '0000-00-00'){
                                        echo 'N/A';
                                    } else {
                                        echo \Carbon\Carbon::parse($visa_Info->visa_expiry_date)->format('d/m/Y');
                                    }
                                } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Visa Grant Date</span>
                            <span class="field-value">
                                <?php
                                if( $visa_Info && $visa_Info->visa_grant_date != "" ){
                                    if( $visa_Info->visa_grant_date == '0000-00-00'){
                                        echo 'N/A';
                                    } else {
                                        echo \Carbon\Carbon::parse($visa_Info->visa_grant_date)->format('d/m/Y');
                                    }
                                } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Visa Description</span>
                            <span class="field-value">
                                <?php
                                if( $visa_Info && $visa_Info->visa_description != "" ){
                                    echo $visa_Info->visa_description;
                                } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Nomi Occupation / Code / Assessing Authority</span>
                            <span class="field-value">
                                <?php
                                $clientOccupation_Info = App\ClientOccupation::select('skill_assessment','nomi_occupation','occupation_code','list','visa_subclass','dates')->where('client_id', $fetchedData->id)->latest('id')->first();
                                if( $clientOccupation_Info && $clientOccupation_Info->nomi_occupation != "" ){ echo $clientOccupation_Info->nomi_occupation; } else { echo 'N/A'; }
                                ?>
                                <?php
                                if( $clientOccupation_Info && $clientOccupation_Info->occupation_code != "" ){ echo ' / '.$clientOccupation_Info->occupation_code; } else { echo ' / '.'N/A'; }
                                ?>
                                <?php
                                if( $clientOccupation_Info && $clientOccupation_Info->list != "" ){ echo ' / '.$clientOccupation_Info->list; } else { echo ' / '.'N/A'; }
                                ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">English Test Score</span>
                            <span class="field-value">
                                <?php
                                $clientTest_Info = App\ClientTestScore::select('test_type','listening','reading','writing','speaking','overall_score','test_date')->where('client_id', $fetchedData->id)->latest('id')->first();
                                if( $clientTest_Info && $clientTest_Info->test_type != "" ){ echo $clientTest_Info->test_type.": "; } else { echo 'N/A'; }
                                ?>


                                <?php
                                if( $clientTest_Info && $clientTest_Info->listening != "" ){ echo "L".$clientTest_Info->listening; } else { echo 'N/A'; }
                                ?>
                                <?php
                                if( $clientTest_Info && $clientTest_Info->reading != "" ){ echo " R".$clientTest_Info->reading; } else { echo 'N/A'; }
                                ?>
                                <?php
                                if( $clientTest_Info && $clientTest_Info->writing != "" ){ echo " W".$clientTest_Info->writing; } else { echo 'N/A'; }
                                ?>

                                <?php
                                if( $clientTest_Info && $clientTest_Info->speaking != "" ){ echo " S".$clientTest_Info->speaking; } else { echo 'N/A'; }
                                ?>

                                <?php
                                if( $clientTest_Info && $clientTest_Info->overall_score != "" ){ echo " O".$clientTest_Info->overall_score; } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>
                    </div>


                    <?php
                    $clientQualification_Info = App\ClientQualification::select('level','name','qual_campus','finish_date')->where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
                    ?>
                    @if(!empty($clientQualification_Info) && $clientQualification_Info->count() > 0)
                    <div class="card">
                        <div class="qualification-section">
                            <h3><i class="fas fa-info-circle"></i> Qualification</h3>
                            <div class="qualification-list" style="overflow: hidden;">
                                <table class="table eoi-table">
                                    <thead>
                                        <tr>
                                            <th>Level</th>
                                            <th>Name</th>
                                            <th>Campus</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientQualification_Info as $qualification)
                                            <tr>
                                                <td>{{ $qualification->level ?: 'N/A' }}</td>
                                                <td>{{ $qualification->name ?: 'N/A' }}</td>
                                                <td>{{ $qualification->qual_campus ?: 'N/A' }}</td>
                                                <td>{{ $qualification->finish_date ? \Carbon\Carbon::parse($qualification->finish_date)->format('d/m/Y') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                        /*.qualification-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        .qualification-table th, .qualification-table td {
                            padding: 10px;
                            border-bottom: 1px solid #dee2e6;
                            text-align: left;
                        }
                        .qualification-table th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #6c757d !important;
                        }
                        .qualification-table tbody tr:hover {
                            background-color: #f1f5f9;
                        }
                        .qualification-table td {
                            color: #212529;
                        }*/
                    </style>


                    <?php
                    $clientExperience_Info = App\ClientExperience::select('job_title','job_country','job_start_date','job_finish_date')->where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
                    ?>
                    @if(!empty($clientExperience_Info) && $clientExperience_Info->count() > 0)
                    <div class="card">
                        <div class="experience-section">
                            <h3><i class="fas fa-info-circle"></i> Work Experience</h3>
                            <div class="experience-list" style="overflow: hidden;">
                                <table class="table eoi-table">
                                    <thead>
                                        <tr>
                                            <th>Job Title</th>
                                            <th>Country</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientExperience_Info as $experience)
                                            <tr>
                                                <td>{{ $experience->job_title ?: 'N/A' }}</td>
                                                <td>{{ $experience->job_country ?: 'N/A' }}</td>
                                                <td>{{ $experience->job_start_date ? \Carbon\Carbon::parse($experience->job_start_date)->format('d/m/Y') : 'N/A' }}</td>
                                                <td>{{ $experience->job_finish_date ? \Carbon\Carbon::parse($experience->job_finish_date)->format('d/m/Y') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                       /* .experience-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        .experience-table th, .experience-table td {
                            padding: 10px;
                            border-bottom: 1px solid #dee2e6;
                            text-align: left;
                        }
                        .experience-table th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #6c757d !important;
                        }
                        .experience-table tbody tr:hover {
                            background-color: #f1f5f9;
                        }
                        .experience-table td {
                            color: #212529;
                        }*/
                    </style>



                    @if(!empty($clientFamilyDetails) && $clientFamilyDetails->count() > 0)
                    <div class="card">
                        <div class="relationship-section">
                            <h3><i class="fas fa-address-card"></i> Relationships</h3>
                            <div class="relationship-list" style="max-height: 300px; overflow-y: auto;">
                                <table class="table relationship-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Relation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientFamilyDetails as $relationship)
                                            <?php
                                            //dd($relationship->related_client_id);
                                            if(isset($relationship->related_client_id) && $relationship->related_client_id != "")
                                            { //Existing Client
                                                $relatedClientInfo = App\\Models\\Admin::select('client_id','first_name','last_name')->where('id', $relationship->related_client_id)->first();
                                                //dd($relatedClientInfo);
                                                if($relatedClientInfo){
                                                    $relatedClientId = $relatedClientInfo->client_id;
                                                    $relatedClientFullName = $relatedClientInfo->first_name.' '.$relatedClientInfo->last_name."<br/>";
                                                    $relatedClientFullName .= $relatedClientId;
                                                } else {
                                                    $relatedClientId = 'NA';
                                                    $relatedClientFullName = 'NA';
                                                }
                                            }  else { //New Client
                                                $relatedClientId = 'NA';
                                                $relatedClientFullName = $relationship->first_name . ' ' . $relationship->last_name;
                                            }?>
                                            <tr>
                                                <td style="color: #6c757d;">
                                                    <?php
                                                    if(isset($relationship->related_client_id) && $relationship->related_client_id != "")
                                                    { ?>
                                                        <a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$relationship->related_client_id)))}}"><?php echo $relatedClientFullName;?> </a>
                                                    <?php
                                                    }  else {
                                                        echo $relatedClientFullName;
                                                    } ?>
                                                </td>
                                                <td style="color: #6c757d;">{{ $relationship->relationship_type ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                        .relationship-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        .relationship-table th, .relationship-table td {
                            padding: 10px;
                            border-bottom: 1px solid #dee2e6;
                            text-align: left;
                        }
                        .relationship-table th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #6c757d !important;
                        }
                        .relationship-table tbody tr:hover {
                            background-color: #f1f5f9;
                        }
                    </style>


                    <?php
                    if($fetchedData->related_files != '')
                    { ?>
                    <div class="card">
                        <h3><i class="fas fa-address-card"></i> Related Files</h3>
                        <div class="field-group">
                            <ul style="margin-left: 15px;">
                                <?php
                                //if($fetchedData->related_files != '')
                                //{
                                    $exploder = explode(',', $fetchedData->related_files);
                                    foreach($exploder AS $EXP)
                                    {
                                        $relatedclients = \App\Models\Admin::where('id', $EXP)->first();
                                        ?>
                                        <li><a target="_blank" href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$relatedclients->id)))}}">{{$relatedclients->first_name}} {{$relatedclients->last_name}}</a></li>
                                    <?php
                                    }
                                //} ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                    } ?>

                    <?php
                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                    //dd($matter_cnt);
                    if($matter_cnt >0)
                    {
                        //Display reference values
                        $matter_dis_ref_info_arr = array(); // Always a Collection
                        if($id1)
                        { //if client unique reference id is present in url
                            $matter_dis_ref_info_arr = \App\Models\ClientMatter::select('department_reference','other_reference')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                        }
                        else
                        {
                            $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                            //dd($matter_cnt);
                            if($matter_cnt >0){
                                $matter_dis_ref_info_arr = \App\Models\ClientMatter::select('department_reference','other_reference')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                            }
                        } //dd($matter_dis_ref_info_arr);


                        if(
                            ( isset($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->department_reference != '' )
                            ||
                            ( isset($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->other_reference != '' )
                        )
                        { ?>
                            <div class="card">
                                <h3><i class="fas fa-user"></i> Reference Information</h3>
                                <div class="field-group">
                                    <span class="field-label">Department Reference</span>
                                    <span class="field-value">
                                        <?php
                                        if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->department_reference != '') {
                                            echo $matter_dis_ref_info_arr->department_reference;
                                        } else {
                                            echo 'N/A';
                                        }?>

                                    </span>
                                </div>
                                <div class="field-group">
                                    <span class="field-label">Other Reference</span>
                                    <span class="field-value">
                                        <?php
                                        if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->other_reference != ''){
                                            echo $matter_dis_ref_info_arr->other_reference;
                                        } else {
                                            echo 'N/A';
                                        } ?>
                                    </span>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    ?>

                    <?php
                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                    //dd($matter_cnt);
                    if($matter_cnt >0)
                    {
                    ?>
                        <div class="card">
                            <h3><i class="fas fa-user"></i> Matter Assignee  <a style="margin-left: 110px;" class="changeMatterAssignee" href="javascript:;" role="button">Change Assignee</a></h3>

                            <?php
                            //Display reference values
                            $matter_dis_ref_info_arr = array(); // Always a Collection
                            if($id1)
                            { //if client unique reference id is present in url
                                $matter_dis_ref_info_arr = \App\Models\ClientMatter::select('sel_migration_agent','sel_person_responsible','sel_person_assisting')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                            }
                            else
                            {
                                $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                //dd($matter_cnt);
                                if($matter_cnt >0){
                                    $matter_dis_ref_info_arr = \App\Models\ClientMatter::select('sel_migration_agent','sel_person_responsible','sel_person_assisting')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                }
                            } //dd($matter_dis_ref_info_arr);
                            ?>

                            <div class="field-group">
                                <span class="field-label">Migration Agent</span>
                                <span class="field-value">
                                    <?php
                                    if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->sel_migration_agent != '') {
                                        $mig_agent_info_arr = \App\Models\Admin::select('first_name','last_name')->where('id', $matter_dis_ref_info_arr->sel_migration_agent)->first();
                                        if($mig_agent_info_arr){
                                            echo $mig_agent_info_arr->first_name.' '.$mig_agent_info_arr->last_name;
                                        }
                                    } else {
                                        echo 'N/A';
                                    }?>

                                </span>
                            </div>
                            <div class="field-group">
                                <span class="field-label">Person Responsible</span>
                                <span class="field-value">
                                    <?php
                                    if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->sel_person_responsible != ''){
                                        $sel_person_responsible_info_arr = \App\Models\Admin::select('first_name','last_name')->where('id', $matter_dis_ref_info_arr->sel_person_responsible)->first();
                                        if($sel_person_responsible_info_arr){
                                            echo $sel_person_responsible_info_arr->first_name.' '.$sel_person_responsible_info_arr->last_name;
                                        }
                                    } else {
                                        echo 'N/A';
                                    } ?>
                                </span>
                            </div>

                            <div class="field-group">
                                <span class="field-label">Person Assisting</span>
                                <span class="field-value">
                                    <?php
                                    if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->sel_person_assisting != ''){
                                        $sel_person_assisting_info_arr = \App\Models\Admin::select('first_name','last_name')->where('id', $matter_dis_ref_info_arr->sel_person_assisting)->first();
                                        if($sel_person_assisting_info_arr){
                                            echo $sel_person_assisting_info_arr->first_name.' '.$sel_person_assisting_info_arr->last_name;
                                        }
                                    } else {
                                        echo 'N/A';
                                    } ?>
                                </span>
                            </div>
                        </div>
                    <?php
                    } ?>


                    <?php
                    $clientEoi_Info = App\ClientEoiReference::where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
                    ?>
                    @if(!empty($clientEoi_Info) && $clientEoi_Info->count() > 0)
                    <div class="card">
                        <div class="eoi-section">
                            <h3><i class="fas fa-file-alt"></i> EOI Reference Information</h3>
                            <div class="eoi-list" style="overflow: hidden;/*max-height: 300px; overflow-y: auto;*/">
                                <table class="table eoi-table">
                                    <thead>
                                        <tr>
                                            <th>Subclass</th>
                                            <th>Occupation</th>
                                            <th>Point</th>
                                            <th>State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientEoi_Info as $Eoi_Info)
                                            <tr>
                                                <td>{{ $Eoi_Info->EOI_subclass ?: 'N/A' }}</td>
                                                <td>{{ $Eoi_Info->EOI_occupation ?: 'N/A' }}</td>
                                                <td>{{ $Eoi_Info->EOI_point ?: 'N/A' }}</td>
                                                <td>{{ $Eoi_Info->EOI_state ?: 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                        .eoi-table{
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                            table-layout: fixed;
                        }
                        .eoi-table th, .eoi-table td {
                            padding: 10px;
                            border-bottom: 1px solid #dee2e6;
                            text-align: left;
                            word-wrap: break-word;
                            overflow-wrap: break-word;
                        }
                        .eoi-table th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #6c757d !important;
                        }
                        .eoi-table tbody tr:hover {
                            background-color: #f1f5f9;
                        }
                        .eoi-table td {
                            color: #212529;
                        }
                    </style>

                </div>
            </div>

             
            <!-- Notes Tab -->
            <div class="tab-pane" id="noteterm-tab">
                <div class="card full-width notes-container">
                    <div class="notes-header">
                        <h3><i class="fas fa-file-alt"></i> Notes</h3>
                        <button class="btn btn-primary btn-sm create_note_d" datatype="note">
                            <i class="fas fa-plus"></i> Add Note
                        </button>
                    </div>

                    <!-- Redesigned Tabs -->
                    <div class="subtab-header-container">
                        <nav class="subtabs8 note-pills" style="margin: 10px 0 0 10px; display: flex; gap: 10px;">
                            <button class="subtab8-button pill-tab active" data-subtab8="All">All</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Call">Call</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Email">Email</button>
                            <button class="subtab8-button pill-tab" data-subtab8="In-Person">In-Person</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Others">Others</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Attention">Attention</button>
                        </nav>
                    </div>

                    <style>
                        .note-pills .pill-tab {
                            border-radius: 999px;
                            padding: 8px 22px;
                            border: none;
                            background: #f1f5f9;
                            color: #333;
                            font-weight: 500;
                            font-size: 1rem;
                            transition: background 0.2s, color 0.2s;
                        }
                        .note-pills .pill-tab.active {
                            background: #2563eb;
                            color: #fff;
                        }
                        .note-pills .pill-tab:not(.active):hover {
                            background: #e0e7ef;
                        }
                        .note-card-redesign {
                            background: #f9f9f9;
                            border-radius: 16px;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
                            padding: 24px 28px 20px 28px;
                            margin-bottom: 18px;
                            border: none;
                            position: relative;
                        }
                        .note-type-label {
                            display: inline-block;
                            font-size: 0.95rem;
                            font-weight: 600;
                            border-radius: 16px;
                            padding: 6px 18px;
                            margin-bottom: 5px;
                        }
                        .note-type-inperson { background: #e6f4ea; color: #219653; }
                        .note-type-call { background: #e3f0fd; color: #2563eb; }
                        .note-type-email { background: #fdeaea; color: #e74c3c; }
                        .note-type-attention { background: #f3e8ff; color: #8e44ad; }
                        .note-type-others { background: #f5f5f5; color: #888; }
                        .note-title {
                            font-size: 1.18rem;
                            font-weight: 700;
                            color: #22223b;
                            margin-bottom: 2px;
                        }
                        .note-meta-redesign {
                            font-size: 0.97rem;
                            color: #6c757d;
                            margin-bottom: 8px;
                        }
                        .note-content-redesign {
                            color: #444;
                            font-size: 1.01rem;
                            margin-bottom: 8px;
                        }
                        .viewnote {
                            color: #2563eb;
                            font-size: 0.97rem;
                            text-decoration: underline;
                            cursor: pointer;
                        }
                        .author-name-created {
                            font-size:0.67rem;
                            color:#6c757d;
                        }
                        .author-updated-date-time {
                            font-size:0.67rem;
                            color:#6c757d;
                        }
                        .note-card-info {
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            margin-bottom:rgba(41, 31, 31, 0.07)
                        }
                        .note-toggle-btn-div {
                            position:absolute;
                            top:18px;
                            right:18px;
                        }
                        .note-toggle-btn-div-type {
                            display:inline-grid;
                            width: 133px;
                        }
                    </style>

                    <!-- Notes List -->
                    <div class="note_term_list subtab8-content">
                        <?php
                        $notelist = \App\Models\Note::where('client_id', $fetchedData->id)
                            ->whereNull('assigned_to')
                            ->where('type', 'client')
                            ->orderby('pin', 'DESC')
                            ->orderBy('updated_at', 'DESC')
                            ->get();
                        foreach($notelist as $list) {
                            $admin = \App\Models\Admin::where('id', $list->user_id)->first();
                            // Determine type label and color
                            $type = strtolower($list->task_group ?? $list->task_group ?? 'others');
                            $typeLabel = 'Others';
                            $typeClass = 'note-type-others';

                            if(strpos($type, 'call') !== false) { $typeLabel = 'Call'; $typeClass = 'note-type-call'; }
                            else if(strpos($type, 'email') !== false) { $typeLabel = 'Email'; $typeClass = 'note-type-email'; }
                            else if(strpos($type, 'in-person') !== false) { $typeLabel = 'In-Person'; $typeClass = 'note-type-inperson'; }
                            else if(strpos($type, 'others') !== false) { $typeLabel = 'Others'; $typeClass = 'note-type-others'; }
                            else if(strpos($type, 'attention') !== false) { $typeLabel = 'Attention'; $typeClass = 'note-type-attention'; }

                            $desc = strip_tags($list->description);
                            $descShort = mb_strlen($desc) > 120 ? mb_substr($desc, 0, 120) . '...' : $desc;
                            $showReadMore = mb_strlen($desc) > 120;
                        ?>
                        <div class="note-card-redesign <?php if($list->pin == 1) echo 'pinned'; ?>" data-matterid="{{ $list->matter_id }}" id="note_id_{{$list->id}}" data-type="{{ $typeLabel }}">
                            <div class="note-card-info">
                                <span class="note-type-label {{ $typeClass }}">{{ $typeLabel }}</span>
                                <span class="author-name-created">{{ $admin->first_name ?? 'NA' }} {{ $admin->last_name ?? 'NA' }}</span>
                                <span class="author-updated-date-time">{{date('d/m/Y h:i A', strtotime($list->updated_at))}}</span>
                            </div>
                            <!--<div class="note-title">{{-- @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '40', '...')--}}</div>-->
                            <div class="note-content-redesign">{!! nl2br(e($desc)) !!}
                                <?php //if($showReadMore): ?>
                                    <!--<span class="viewnote" data-id="{{$list->id}}">Read More</span>-->
                                <?php //endif; ?>
                            </div>
                            <div class="note-toggle-btn-div">
                                <div class="dropdown">
                                    <button class="btn btn-link dropdown-toggle note-toggle-btn-div-type" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item opennoteform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
                                        <a data-id="{{$list->id}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;">Delete</a>
                                        <?php if($list->pin == 1) { ?>
                                            <a data-id="{{$list->id}}" class="dropdown-item pinnote" href="javascript:;">Unpin</a>
                                        <?php } else { ?>
                                            <a data-id="{{$list->id}}" class="dropdown-item pinnote" href="javascript:;">Pin</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.subtab8-button.pill-tab').forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        // Get selected matter
                        let selectedMatter;
                        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                            selectedMatter = $('.general_matter_checkbox_client_detail').val();
                        } else {
                            selectedMatter = $('#sel_matter_id_client_detail').val();
                        }
                        // Remove active from all tabs
                        document.querySelectorAll('.subtab8-button.pill-tab').forEach(t => t.classList.remove('active'));
                        this.classList.add('active');
                        const type = this.getAttribute('data-subtab8');
                        // Show/hide notes based on type and matter
                        document.querySelectorAll('.note-card-redesign').forEach(card => {
                            const cardType = card.getAttribute('data-type');
                            const cardMatter = card.getAttribute('data-matterid');
                            const typeMatch = (type === 'All' || cardType === type);
                            const matterMatch = (selectedMatter === null || selectedMatter === '' || cardMatter === selectedMatter);
                            if (typeMatch && matterMatch) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                });
                // Trigger default (All) on load
                document.querySelector('.subtab8-button.pill-tab.active').click();
            });
            </script>

            <!-- Documents Tab -->
            <div class="tab-pane" id="documentalls-tab">
                <div class="card full-width documentalls-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs">
                        <button class="subtab-button active" data-subtab="documents">Personal Document</button>
                        <?php
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                        //dd($matter_cnt);
                        if($matter_cnt >0 ) { ?>
                            <button class="subtab-button" data-subtab="migrationdocuments">Visa Document</button>
                        <?php
                        }
                        ?>
                        <button class="subtab-button ml-auto" data-subtab="notuseddocuments">Not Used Documents</button>
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab-content" id="subtab-content">

                        <?php
                        $clientId = $fetchedData->id ?? null;
                        $persDocCatList = \App\Models\PersonalDocumentType::select('id', 'title','client_id')
                            ->where('status', 1)
                            ->where(function($query) use ($clientId) {
                                $query->whereNull('client_id')
                                    ->orWhere('client_id', $clientId);
                            })
                            ->orderBy('id', 'ASC')
                            ->get();
                        ?>

                        <!-- Personal Documents Subtab -->
                        <div class="subtab-pane active" id="documents-subtab">
                            <!-- Document Type Subtabs Container -->
                            <div class="subtab-header-container">
                                <nav class="subtabs2" style="margin: 10px 0 0 10px; display: inline-block; flex-grow: 1;">
                                    <?php foreach ($persDocCatList as $catVal): ?>
                                        <?php
                                        $id = $catVal->id;
                                        $isActive = $id == 1 ? 'active' : '';
                                        $isClientGenerated = $catVal->client_id !== null;
                                        ?>
                                        <div style="display: inline-block; position: relative;" class="button-container">
                                            <button class="subtab2-button <?= $isActive ?>" data-subtab2="<?= $id ?>" style="color: #000 !important;">
                                                <?= htmlspecialchars($catVal->title) ?>
                                            </button>
                                            <?php if ($isClientGenerated): ?>
                                                <div class="action-buttons" style="display: none; position: absolute; top: 0; right: -8px;">
                                                    <button class="btn btn-sm btn-warning update-personal-cat-title" data-id="<?= $id ?>" style="padding: 2px 0px 2px 6px;"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </nav>
                                <button style="margin-top: 10px;" class="btn add_personal_doc_cat-btn add_personal_doc_cat" data-type="personal" data-categoryid="">
                                    <i class="fas fa-plus"></i> Add Personal Document Category
                                </button>
                            </div>

                            <!-- Subtab2 Contents -->
                            <div class="subtab2-content">
                                <?php foreach ($persDocCatList as $catVal): ?>
                                    <?php
                                    $id = $catVal->id;
                                    $isActive = $id == 1 ? 'active' : '';
                                    $folderName = $id;
                                    ?>

                                    <div class="subtab2-pane <?= $isActive ?>" id="<?= $id ?>-subtab2">
                                        <div class="checklist-table-container" style="vertical-align: top; margin-top: 10px; width: 760px;">
                                            <div class="subtab2-header" style="margin-left: 10px;">
                                                <h3><i class="fas fa-file-alt"></i> <?= htmlspecialchars($catVal->title) ?> Documents</h3>
                                                <button class="btn add-checklist-btn add_education_doc" data-type="personal" data-categoryid="<?= $id ?>">
                                                    <i class="fas fa-plus"></i> Add Personal Checklist
                                                </button>
                                            </div>
                                            <table class="checklist-table">
                                                <thead>
                                                    <tr>
                                                        <th>SNo.</th>
                                                        <th>Checklist</th>
                                                        <th>Added By</th>
                                                        <th>File Name</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tdata persdocumnetlist documnetlist_<?= $id ?>">
                                                    <?php
                                                    $documents = \App\Models\Document::where('client_id', $clientId)
                                                        ->whereNull('not_used_doc')
                                                        ->where('doc_type', 'personal')
                                                        ->where('folder_name', $folderName)
                                                        ->where('type', 'client')
                                                        ->orderBy('created_at', 'DESC')
                                                        ->get();
                                                    ?>
                                                    <?php foreach ($documents as $docKey => $fetch): ?>
                                                        <?php
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        $fileUrl = $fetch->myfile_key
                                                            ? asset($fetch->myfile)
                                                            : 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $clientId . '/personal/' . $fetch->myfile;
                                                        ?>
                                                        <tr class="drow" id="id_<?= $fetch->id ?>">
                                                            <td><?= $docKey + 1 ?></td>
                                                            <td style="white-space: initial;">
                                                                <div data-id="<?= $fetch->id ?>" data-personalchecklistname="<?= htmlspecialchars($fetch->checklist) ?>" class="personalchecklist-row">
                                                                    <span><?= htmlspecialchars($fetch->checklist) ?></span>
                                                                </div>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?= htmlspecialchars($admin->first_name ?? 'NA') ?><br>
                                                                <?= date('d/m/Y', strtotime($fetch->created_at)) ?>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?php if ($fetch->file_name): ?>
                                                                    <div data-id="<?= $fetch->id ?>" data-name="<?= htmlspecialchars($fetch->file_name) ?>" class="doc-row">
                                                                        <a href="javascript:void(0);" onclick="previewFile('<?= $fetch->filetype ?>','<?= $fileUrl ?>','preview-container-<?= $id ?>')">
                                                                            <i class="fas fa-file-image"></i> <span><?= htmlspecialchars($fetch->file_name . '.' . $fetch->filetype) ?></span>
                                                                        </a>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="upload_document" style="display: inline-block;">
                                                                        <form method="POST" enctype="multipart/form-data" id="upload_form_<?= $fetch->id ?>">
                                                                            @csrf
                                                                            <input type="hidden" name="clientid" value="<?= $clientId ?>">
                                                                            <input type="hidden" name="fileid" value="<?= $fetch->id ?>">
                                                                            <input type="hidden" name="type" value="client">
                                                                            <input type="hidden" name="doctype" value="personal">
                                                                            <input type="hidden" name="doccategory" value="<?= $catVal->title ?>">

                                                                            <!-- Drag and Drop Upload Area for Personal Documents -->
                                                                            <div class="drag-drop-area" data-fileid="<?= $fetch->id ?>" data-doccategory="<?= $id ?>">
                                                                                <div class="drag-drop-zone">
                                                                                    <i class="fa fa-cloud-upload-alt"></i>
                                                                                    <p>Drag & drop files here or <span class="click-to-upload">click to browse</span></p>
                                                                                    <small>Multiple files supported</small>
                                                                                </div>
                                                                                <input class="docupload" data-fileid="<?= $fetch->id ?>" data-doccategory="<?= $id ?>" type="file" name="document_upload[]" multiple style="display: none;">
                                                                            </div>
                                                                            
                                                                            <!-- Upload Progress Bar -->
                                                                            <div class="upload-progress" style="display: none;">
                                                                                <div class="progress-bar">
                                                                                    <div class="progress-fill"></div>
                                                                                </div>
                                                                                <span class="progress-text">Uploading...</span>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($fetch->myfile): ?>
                                                                    <div class="dropdown d-inline">
                                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Action</button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                                                            <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>
                                                                            <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                            <?php
                                                                            $fileExt = pathinfo($fetch->myfile, PATHINFO_EXTENSION);
                                                                            if (in_array($fileExt, ['jpg', 'png', 'jpeg'])): ?>
                                                                                <a target="_blank" class="dropdown-item" href="<?= URL::to('/admin/document/download/pdf') ?>/<?= $fetch->id ?>">PDF</a>
                                                                            <?php endif; ?>
                                                                            <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                            <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?= $catVal->title ?>" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                            @if (strtolower($fetch->filetype) === 'pdf')
                                                                                @if ($fetch->status === 'draft')
                                                                                    <form method="GET" action="{{ route('documents.edit', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Send To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'sent')
                                                                                    <form method="GET" action="{{ route('documents.index', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Check To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'signed')
                                                                                    <a target="_blank" href="{{ route('download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                                @endif

                                                                            @endif


                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="grid_data griddata_<?= $id ?>">
                                            <?php foreach ($documents as $fetch): ?>
                                                <?php if ($fetch->myfile): ?>
                                                    <div class="grid_list" id="gid_<?= $fetch->id ?>">
                                                        <div class="grid_col">
                                                            <div class="grid_icon">
                                                                <i class="fas fa-file-image"></i>
                                                            </div>
                                                            <div class="grid_content">
                                                                <span id="grid_<?= $fetch->id ?>" class="gridfilename"><?= htmlspecialchars($fetch->file_name) ?></span>
                                                                <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                                    <a class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                                    <div class="dropdown-menu">
                                                                        <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                        <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                        <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?= $catVal->title ?>" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                         @if (strtolower($fetch->filetype) === 'pdf')

                                                                                @if ($fetch->status === 'draft')
                                                                                    <form method="GET" action="{{ route('documents.edit', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Send To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'sent')
                                                                                    <form method="GET" action="{{ route('documents.index', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Check To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'signed')
                                                                                    <a target="_blank" href="{{ route('admin.download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                                @endif

                                                                            @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <div class="clearfix"></div>
                                        </div>

                                        <div class="preview-pane file-preview-container preview-container-<?= $id ?>" style="display: inline; margin-top: 15px !important; width: 499px;">
                                            <h3>File Preview</h3>
                                            <p>Click on a file to preview it here.</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php
                        $client_selected_matter_id1 = null;
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count(); //dd($matter_cnt);
                        if( $matter_cnt >0 ) {
                            //if client unique reference id is present in url
                            if( isset($id1) && $id1 != "") {
                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                            } else {
                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id', $fetchedData->id)->orderBy('id', 'desc')->first();
                            } //dd($matter_get_id);
                            if($matter_get_id ){
                                $client_selected_matter_id1 = $matter_get_id->id;
                            }
                        }//dd('client_selected_matter_id==='.$client_selected_matter_id1);

                        $visaDocCatList = \App\Models\VisaDocumentType::select('id', 'title','client_id','client_matter_id')
                        ->where('status', 1)
                        ->where(function($query) use ($client_selected_matter_id1) {
                            $query->whereNull('client_matter_id')
                                ->orWhere('client_matter_id', (int) $client_selected_matter_id1);
                        })
                        ->orderBy('id', 'ASC')
                        ->get();
                        //dd($visaDocCatList);
                        ?>
                        <!-- Visa Documents Subtab -->
                        <div class="subtab-pane" id="migrationdocuments-subtab">
                            <!-- Visa Document Type Subtabs Container -->
                            <div class="subtab-header-container">
                                <nav class="subtabs6" style="margin: 10px 0 0 10px; display: inline-block; flex-grow: 1;">
                                    <?php foreach ($visaDocCatList as $catVal): ?>
                                        <?php
                                        $id = $catVal->id;
                                        $isActive = $id == 1 ? 'active' : '';
                                        $folderName = $id;
                                        $isClientGenerated = $catVal->client_matter_id !== null;
                                        ?>
                                        <div style="display: inline-block; position: relative;" class="button-container">
                                            <button class="subtab6-button <?= $isActive ?>" data-subtab6="<?= $id ?>" style="color: #000 !important;">
                                                <?= htmlspecialchars($catVal->title) ?>
                                            </button>
                                            <?php if ($isClientGenerated): ?>
                                                <div class="action-buttons" style="display: none; position: absolute; top: 0; right: -8px;">
                                                    <button class="btn btn-sm btn-warning update-visa-cat-title" data-id="<?= $id ?>" style="padding: 2px 0px 2px 6px;"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </nav>

                                <button style="margin-top: 10px;" class="btn add-visa-doc-category-btn add-visa-doc-category" data-type="visa" data-categoryid="">
                                    <i class="fas fa-plus"></i> Add Visa Document Category
                                </button>
                            </div>

                            <!-- Subtab6 Contents -->
                            <div class="subtab6-content">
                                <?php foreach ($visaDocCatList as $catVal):
                                    $id = $catVal->id;
                                    $isActive = $id == 1 ? 'active' : '';
                                    $folderName = $id;
                                    ?>
                                    <div class="subtab6-pane <?= $isActive ?>" id="<?= $id ?>-subtab6">
                                        <div class="checklist-table-container" style="vertical-align: top; margin-top: 10px; width: 760px;">
                                            <div class="subtab6-header" style="margin-left: 10px;">
                                                <h3><i class="fas fa-file-alt"></i> <?= htmlspecialchars($catVal->title) ?> Documents</h3>
                                                <button class="btn add-checklist-btn add_migration_doc" data-type="visa" data-categoryid="<?= $id ?>">
                                                    <i class="fas fa-plus"></i> Add Visa Checklist
                                                </button>
                                            </div>
                                            <table class="checklist-table">
                                                <thead>
                                                    <tr>
                                                        <th>SNo.</th>
                                                        <th>Checklist</th>
                                                        <th>Added By</th>
                                                        <th>File Name</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tdata migdocumnetlist1 migdocumnetlist_<?= $id ?>">
                                                    <?php
                                                     $documents = \App\Models\Document::where('client_id', $fetchedData->id)
                                                        //->where('client_matter_id', $fetchedData->client_matter_id ?? null)
                                                        ->whereNull('not_used_doc')
                                                        ->where('doc_type', 'visa')
                                                        ->where('folder_name', $folderName)
                                                        ->where('type', 'client')
                                                        ->orderBy('created_at', 'DESC')
                                                        ->get();
                                                    ?>
                                                    <?php foreach ($documents as $visaKey => $fetch): ?>
                                                        <?php
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        $fileUrl = $fetch->myfile_key ? asset($fetch->myfile) : 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $fetchedData->id . '/visa/' . $fetch->myfile;
                                                        ?>
                                                        <tr class="drow" data-matterid="<?= $fetch->client_matter_id ?>" id="id_<?= $fetch->id ?>">
                                                            <td><?= $visaKey + 1 ?></td>
                                                            <td style="white-space: initial;">
                                                                <div data-id="<?= $fetch->id ?>" data-visachecklistname="<?= htmlspecialchars($fetch->checklist) ?>" class="visachecklist-row">
                                                                    <span><?= htmlspecialchars($fetch->checklist) ?></span>
                                                                </div>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?= htmlspecialchars($admin->first_name ?? 'NA') ?><br>
                                                                <?= date('d/m/Y', strtotime($fetch->created_at)) ?>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?php if ($fetch->file_name): ?>
                                                                    <div data-id="<?= $fetch->id ?>" data-name="<?= htmlspecialchars($fetch->file_name) ?>" class="doc-row">
                                                                        <a href="javascript:void(0);" onclick="previewFile('<?= $fetch->filetype ?>','<?= asset($fetch->myfile) ?>','preview-container-migdocumnetlist')">
                                                                            <i class="fas fa-file-image"></i> <span><?= htmlspecialchars($fetch->file_name . '.' . $fetch->filetype) ?></span>
                                                                        </a>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="migration_upload_document" style="display: inline-block;">
                                                                        <form method="POST" enctype="multipart/form-data" id="mig_upload_form_<?= $fetch->id ?>">
                                                                            @csrf
                                                                            <input type="hidden" name="clientid" value="<?= $fetchedData->id ?>">
                                                                            <input type="hidden" name="client_matter_id" value="<?= $fetch->client_matter_id ?? '' ?>">
                                                                            <input type="hidden" name="fileid" value="<?= $fetch->id ?>">
                                                                            <input type="hidden" name="type" value="client">
                                                                            <input type="hidden" name="doctype" value="visa">
                                                                            <input type="hidden" name="doccategory" value="<?= $catVal->title ?>">
                                                                            <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                                                            <input class="migdocupload" data-fileid="<?= $fetch->id ?>" data-doccategory="<?= $id ?>" type="file" name="document_upload"/>
                                                                        </form>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($fetch->myfile): ?>
                                                                    <div class="dropdown d-inline">
                                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Action</button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                                                            <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>
                                                                            <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                            <?php $fileExt = pathinfo($fetch->myfile, PATHINFO_EXTENSION); if (in_array($fileExt, ['jpg', 'png', 'jpeg'])): ?>
                                                                                <a target="_blank" class="dropdown-item" href="<?= URL::to('/admin/document/download/pdf') ?>/<?= $fetch->id ?>">PDF</a>
                                                                            <?php endif; ?>
                                                                            <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                            <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                            @if (strtolower($fetch->filetype) === 'pdf')

                                                                                @if ($fetch->status === 'draft')
                                                                                    <a target="_blank" href="{{ route('documents.edit', $fetch->id) }}" class="dropdown-item">Send To Signature</a>
                                                                                @endif

                                                                                @if($fetch->status === 'sent')
                                                                                    <a target="_blank" href="{{ route('documents.index', $fetch->id) }}" class="dropdown-item">Check To Signature</a>
                                                                                @endif

                                                                                @if($fetch->status === 'signed')
                                                                                    <a target="_blank" href="{{ route('download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                                @endif

                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="grid_data miggriddata" style="display:none;">
                                            <?php foreach ($visaDocCatList as $catVal):
                                                $id = $catVal->id;
                                                $documents = \App\Models\Document::where('client_id', $fetchedData->id)
                                                    //->where('client_matter_id', $fetchedData->client_matter_id ?? null)
                                                    ->whereNull('not_used_doc')
                                                    ->where('doc_type', 'visa')
                                                    ->where('folder_name', $id)
                                                    ->where('type', 'client')
                                                    ->orderBy('updated_at', 'DESC')
                                                    ->get();
                                                foreach ($documents as $fetch):
                                                    if ($fetch->myfile):
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        ?>
                                                        <div class="grid_list" id="gid_<?= $fetch->id ?>">
                                                            <div class="grid_col">
                                                                <div class="grid_icon">
                                                                    <i class="fas fa-file-image"></i>
                                                                </div>
                                                                <div class="grid_content">
                                                                    <span id="grid_<?= $fetch->id ?>" class="gridfilename"><?= htmlspecialchars($fetch->file_name) ?></span>
                                                                    <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                                        <a class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                                        <div class="dropdown-menu">
                                                                            <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                            <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                            <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                            @if (strtolower($fetch->filetype) === 'pdf')

                                                                                @if ($fetch->status === 'draft')
                                                                                    <form method="GET" action="{{ route('documents.edit', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Send To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'sent')
                                                                                    <a target="_blank" href="{{ route('documents.index', $fetch->id) }}" class="dropdown-item">Check To Signature</a>
                                                                                @endif

                                                                                @if($fetch->status === 'signed')
                                                                                    <a target="_blank" href="{{ route('download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                                @endif

                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <div class="clearfix"></div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="preview-pane file-preview-container preview-container-migdocumnetlist" style="display: inline;margin-top: 15px !important; width: 499px;">
                                            <h3>File Preview</h3>
                                            <p>Click on a file to preview it here.</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Not Used Documents Subtab -->
                        <div class="subtab-pane" id="notuseddocuments-subtab">
                            <div style="display: flex; gap: 20px; padding: 15px;">
                                <!-- Table Container -->
                                <div style="flex: 1; min-width: 0;">
                                    <div class="subtab-header" style="margin-bottom: 15px;">
                                        <h3><i class="fas fa-folder"></i> Not Used Documents</h3>
                                    </div>
                                    <div style="overflow: auto; max-height: calc(100vh - 250px);">
                                        <table class="checklist-table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>SNo.</th>
                                                    <th>Checklist</th>
                                                    <th>Document Type</th>
                                                    <th>Added By</th>
                                                    <th>File Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="tdata notuseddocumnetlist">
                                                <?php
                                                $fetchd = \App\Models\Document::where('client_id', $fetchedData->id)
                                                ->where('not_used_doc', 1)
                                                ->where('type','client')
                                                ->where(function($query) {
                                                    $query->orWhere('doc_type','personal')
                                                    ->orWhere('doc_type','visa');
                                                })->orderBy('type', 'DESC')->get(); //dd($fetchd);
                                                foreach($fetchd as $notuseKey=>$fetch)
                                                {
                                                    $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                    ?>
                                                    <tr class="drow" id="id_{{$fetch->id}}">
                                                        <td><?php echo $notuseKey+1;?></td>
                                                        <td style="white-space: initial;"><?php echo $fetch->checklist; ?></td>
                                                        <td style="white-space: initial;"><?php echo $fetch->doc_type; ?></td>
                                                        <td style="white-space: initial;">
                                                            <?php
                                                            echo ($admin->first_name ?? 'NA') . "<br>";
                                                            echo date('d/m/Y', strtotime($fetch->created_at));
                                                            ?>
                                                        </td>
                                                        <td style="white-space: initial;">
                                                            <?php
                                                            if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                                                <div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                                                    <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-notuseddocumnetlist')">
                                                                            <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                        </a>
                                                                    <?php } else {  //For old file upload
                                                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                        ?>
                                                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-notuseddocumnetlist')">
                                                                            <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                        </a>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php
                                                            }
                                                            else
                                                            {
                                                                echo "N/A";
                                                            }?>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown d-inline">
                                                                <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Action</button>
                                                                <div class="dropdown-menu">
                                                                    <?php
                                                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                    ?>
                                                                    <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                        <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                                                    <?php } else {  //For old file upload ?>
                                                                        <a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
                                                                    <?php } ?>

                                                                    <a data-id="<?= $fetch->id ?>" class="dropdown-item deletenote" data-doccategory="<?= $fetch->doc_type ?>" data-href="deletedocs" href="javascript:;">Delete</a>
                                                                    <a data-id="{{$fetch->id}}" class="dropdown-item backtodoc" data-doctype="{{$fetch->doc_type}}" data-href="backtodoc" href="javascript:;">Back To Document</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php
                                                } //end foreach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Preview Container -->
                                <div class="preview-pane file-preview-container preview-container-notuseddocumnetlist" style="display: inline;margin-top: 15px !important;width: 499px;">
                                    <h3>File Preview</h3>
                                    <p>Click on a file to preview it here.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accounts Tab -->
            <div class="tab-pane" id="accounts-tab">

                <div class="card full-width">
                    <div style="margin-bottom: 10px;">
                        <?php
                        //Display reference values
                        $matter__ref_info_arr = array(); // Always a Collection
                        if($id1)
                        { //if client unique reference id is present in url
                            $matter__ref_info_arr = \App\Models\ClientMatter::select('department_reference','other_reference')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                        }
                        else
                        {
                            $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                            //dd($matter_cnt);
                            if($matter_cnt >0){
                                $matter__ref_info_arr = \App\Models\ClientMatter::select('department_reference','other_reference')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                            }
                        } //dd($matter__ref_info_arr);
                        ?>
                        <input type="text" id="department_reference" name="department_reference" placeholder="Department Reference" value="<?php if( isset($matter__ref_info_arr) && !empty($matter__ref_info_arr) && $matter__ref_info_arr->department_reference != ''){ echo $matter__ref_info_arr->department_reference;} ?>">
                        <input type="text" id="other_reference" name="other_reference" placeholder="Other Reference" value="<?php if( isset($matter__ref_info_arr) && !empty($matter__ref_info_arr) && $matter__ref_info_arr->other_reference != ''){ echo $matter__ref_info_arr->other_reference;} ?>">
                        <input class="btn btn-primary saveReferenceValue" type="button" name="save" value="Submit">

                        <a style="margin-left: 500px;" class="btn btn-primary createreceipt" href="javascript:;" role="button">Create Entry</a>
                    </div>
                    <div class="account-layout" style="overflow-x: hidden; max-width: 100%;">
                        <!-- Client Funds Ledger Section -->
                        <section class="account-section client-account">
                            <div class="account-section-header">
                                <h2><i class="fas fa-wallet" style="color: #28a745;"></i> Client Funds Ledger</h2>
                                <div class="balance-display">
                                    <div class="balance-label">Current Funds Held</div>
                                    <div class="balance-amount funds-held">
                                        <?php
                                        //echo $id1;
                                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count(); //dd($matter_cnt);
                                        if( isset($id1) && $id1 != "" || $matter_cnt >0 )
                                        {  //dd('ifff'.$fetchedData->id);
                                            //if client unique reference id is present in url
                                            if( isset($id1) && $id1 != "") {
                                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                            } else {
                                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id', $fetchedData->id)->orderBy('id', 'desc')->first();
                                            }
                                            //dd($matter_get_id);
                                            if($matter_get_id )
                                            {
                                                $client_selected_matter_id = $matter_get_id->id;
                                            } else {
                                                $client_selected_matter_id = '';
                                            } //dd($client_selected_matter_id);
                                        }
                                        else
                                        {  //dd('elseee');
                                            $client_selected_matter_id = '';
                                        }
                                        $latest_balance = DB::table('account_client_receipts')
                                        ->where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $client_selected_matter_id)
                                        ->where('receipt_type', 1)
                                        ->orderBy('id', 'desc') // or 'created_at', if you have it
                                        ->value('balance_amount');
                                        ?>
                                        {{ is_numeric($latest_balance) ? '$ ' . number_format($latest_balance, 2) : '$ 0.00' }}

                                    </div>
                                </div>
                            </div>
                            <p style="font-size: 0.85em; color: #6c757d; margin-top: -15px; margin-bottom: 15px;">Funds held in trust/client account on behalf of the client.</p>
                            <div class="transaction-table-wrapper">
                                <table class="transaction-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th colspan="2">Type</th>
                                            <th>Description</th>
                                            <th>Reference</th>
                                            <th class="currency">Funds In (+)</th>
                                            <th class="currency">Funds Out (-)</th>
                                            <th class="currency">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitemList">
                                        <?php
                                        $receipts_lists = DB::table('account_client_receipts')->where('client_matter_id',$client_selected_matter_id)->where('client_id',$fetchedData->id)->where('receipt_type',1)->orderBy('id', 'desc')->get();
                                        //dd($receipts_lists);
                                        if(!empty($receipts_lists) && count($receipts_lists)>0 )
                                        {
                                            foreach($receipts_lists as $rec_list=>$rec_val)
                                            {
                                            ?>
                                        <tr class="drow_account_ledger" data-matterid="{{$rec_val->client_matter_id}}">
                                            <td>
                                                <span style="display: inline-flex;">
                                                    <?php
                                                    if( isset($rec_val->validate_receipt) && $rec_val->validate_receipt == '1' )
                                                    { ?>
                                                        <i class="fas fa-check-circle" title="Verified Receipt" style="margin-top: 7px;"></i>
                                                    <?php
                                                    } ?>
                                                    <?php echo $rec_val->trans_date;?>
                                                </span>
                                            </td>

                                            <td class="type-cell">
                                                <?php
                                                if($rec_val->client_fund_ledger_type == 'Deposit' ){
                                                    $type_icon = 'fa-arrow-down';
                                                } else if($rec_val->client_fund_ledger_type == 'Fee Transfer' ){
                                                    $type_icon = 'fa-arrow-right-from-bracket';
                                                } else if($rec_val->client_fund_ledger_type == 'Disbursement' ){
                                                    $type_icon = 'fa-arrow-up';
                                                } else if($rec_val->client_fund_ledger_type == 'Refund' ){
                                                    $type_icon = 'fa-arrow-up';
                                                } else {
                                                    $type_icon = 'fa-arrow-up';
                                                }?>
                                                <i class="fas {{$type_icon}} type-icon" title="{{$rec_val->client_fund_ledger_type}}"></i>
                                                <span>
                                                    {{$rec_val->client_fund_ledger_type}}
                                                    <?php
                                                    if( isset($rec_val->extra_amount_receipt) &&  $rec_val->extra_amount_receipt == 'exceed' ) { ?>
                                                        <br/>
                                                        {{ !empty($rec_val->invoice_no) ? '('.$rec_val->invoice_no.')' : '' }}
                                                    <?php } else { ?>
                                                        <br/>
                                                        {{ !empty($rec_val->invoice_no) ? '('.$rec_val->invoice_no.')' : '' }}

                                                    <?php
                                                    }?>
                                                </span>
                                                <?php
                                                if($rec_val->client_fund_ledger_type !== 'Fee Transfer'){?>
                                                    <a title="Edit Entry" class="link-primary edit-ledger-entry" href="javascript:;"
                                                    data-id="<?php echo $rec_val->id; ?>"
                                                    data-receiptid="<?php echo $rec_val->receipt_id; ?>"
                                                    data-trans-date="<?php echo htmlspecialchars($rec_val->trans_date, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-entry-date="<?php echo htmlspecialchars($rec_val->entry_date, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-type="<?php echo htmlspecialchars($rec_val->client_fund_ledger_type, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-description="<?php echo htmlspecialchars($rec_val->description ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-deposit="<?php echo htmlspecialchars($rec_val->deposit_amount ?? 0, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-withdraw="<?php echo htmlspecialchars($rec_val->withdraw_amount ?? 0, ENT_QUOTES, 'UTF-8'); ?>">
                                                     <i class="fas fa-pencil-alt"></i>
                                                 </a>
                                                <?php
                                                }?>

                                                <?php
                                                if(isset($rec_val->uploaded_doc_id) && $rec_val->uploaded_doc_id != ""){
                                                    $client_doc_list = DB::table('documents')->select('myfile')->where('id',$rec_val->uploaded_doc_id)->first();
                                                    if($client_doc_list){ ?>
                                                        <a target="_blank" title="See Attached Document" class="link-primary" href="<?php echo $client_doc_list->myfile;?>"><i class="fas fa-file-pdf"></i></a>
                                                    <?php
                                                    }
                                                } ?>
                                            </td><td></td>

                                            <td class="description"><?php echo $rec_val->description;?></td>

                                            <!--<td><a href="#" title="View Receipt ".<?php //echo $rec_val->trans_no;?>><?php //echo $rec_val->trans_no;?></a></td>-->
                                            <td><a target="_blank" href="{{URL::to('/admin/clients/genClientFundLedgerInvoice')}}/{{$rec_val->id}}" title="View Receipt"><?php echo $rec_val->trans_no;?></a></td>

                                            <td class="currency text-success">{{ !empty($rec_val->deposit_amount) ? '$ ' . number_format($rec_val->deposit_amount, 2) : '' }}</td>
                                            <td class="currency">{{ !empty($rec_val->withdraw_amount) ? '$ ' . number_format($rec_val->withdraw_amount, 2) : '' }}</td>
                                            <td class="currency balance">{{ !empty($rec_val->balance_amount) ? '$ ' . number_format($rec_val->balance_amount, 2) : '' }}</td>
                                        </tr>
                                        <?php
                                            } //end foreach
                                        }?>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- Invoicing & Office Receipts Section -->
                        <section class="account-section office-account">
                            <div class="account-section-header">
                                <h2><i class="fas fa-file-invoice-dollar" style="color: #007bff;"></i> Invoicing & Office Receipts</h2>
                                <div class="balance-display">
                                    <div class="balance-label">Outstanding Balance</div>
                                    <div class="balance-amount outstanding outstanding-balance">
                                        <?php
                                        //echo $id1;
                                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                        if( isset($id1) && $id1 != "" || $matter_cnt >0 )
                                        {  //if client unique reference id is present in url
                                            //dd('ifff'.$fetchedData->id);
                                            //if client unique reference id is present in url
                                            if( isset($id1) && $id1 != "") {
                                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                            } else {
                                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id', $fetchedData->id)->orderBy('id', 'desc')->first();
                                            }
                                            //dd($matter_get_id);
                                            if($matter_get_id )
                                            {
                                                $client_selected_matter_id = $matter_get_id->id;
                                            } else {
                                                $client_selected_matter_id = '';
                                            } //dd($client_selected_matter_id);
                                        }
                                        else
                                        {
                                            $client_selected_matter_id = '';
                                        }

                                        $latest_outstanding_balance = DB::table('account_client_receipts')
                                        ->where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $client_selected_matter_id)
                                        ->where('receipt_type', 3) // Invoice
                                        ->where(function ($query) {
                                            $query->whereIn('invoice_status', [0, 2])
                                                ->orWhere(function ($q) {
                                                    $q->where('invoice_status', 1)
                                                        ->where('balance_amount', '!=', 0);
                                                });
                                        })
                                        ->sum('balance_amount');
                                        ?>
                                        {{ is_numeric($latest_outstanding_balance) ? '$ ' . number_format($latest_outstanding_balance, 2) : '$ 0.00' }}

                                        <?php if ($latest_outstanding_balance < 0): ?>
                                            <a class="link-primary adjustinvoice" href="javascript:;" title="Adjust Invoice">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <p style="font-size: 0.85em; color: #6c757d; margin-top: -15px; margin-bottom: 15px;">Tracks invoices issued and payments received directly by the office.</p>
                            <div class="transaction-table-wrapper">
                                <h4 style="margin-top:0; margin-bottom: 10px; font-weight: 600;">Invoices Issued</h4>
                                <table class="transaction-table">
                                    <thead>
                                        <tr>
                                            <th>Inv #</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th class="currency">Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitemList_invoice">
                                        <?php
                                        $receipts_lists_invoice = DB::table('account_client_receipts')->where('client_matter_id',$client_selected_matter_id)->where('client_id',$fetchedData->id)->where('receipt_type',3)->groupBy('receipt_id')->orderBy('id', 'desc')->get();
                                        //dd($receipts_lists_invoice);
                                        if(!empty($receipts_lists_invoice) && count($receipts_lists_invoice)>0 )
                                        {
                                            foreach($receipts_lists_invoice as $inc_list=>$inc_val)
                                            {
                                                if($inc_val->void_invoice == 1 ) {
                                                    $trcls = 'strike-through';
                                                } else {
                                                    $trcls = '';
                                                }  //dd(Auth::user()->role);
                                                ?>
                                                <tr class="drow_account_invoice invoiceTrRow <?php echo $trcls;?>" id="invoiceTrRow_<?php echo $inc_val->id;?>" data-matterid="{{$inc_val->client_matter_id}}">
                                                    <td>
                                                        <?php echo $inc_val->trans_no."<br/>";?>
                                                        <?php
                                                        if($inc_val->save_type == 'draft'){?>
                                                            <a title="Edit Draft Invoice" class="link-primary updatedraftinvoice" href="javascript:;" data-receiptid="<?php echo $inc_val->receipt_id;?>"><i class="fas fa-pencil-alt"></i></a>
                                                        <?php
                                                        }
                                                        else if($inc_val->save_type == 'final') {?>
                                                            <a title="Final Invoice" target="_blank" class="link-primary" href="{{URL::to('/admin/clients/genInvoice')}}/{{$inc_val->receipt_id}}"><i class="fas fa-file-pdf"></i></a>
                                                        <?php
                                                        } ?>
                                                    </td>
                                                    <td><?php echo $inc_val->trans_date;?></td>
                                                    <td><?php echo $inc_val->description;?></td>
                                                    <td class="currency">
                                                        @if($inc_val->invoice_status == 1 && ($inc_val->balance_amount == 0 || $inc_val->balance_amount == 0.00))
                                                            {{ !empty($inc_val->partial_paid_amount) ? '$ ' . number_format($inc_val->partial_paid_amount, 2) : '' }}
                                                        @else
                                                            {{ !empty($inc_val->balance_amount) ? '$ ' . number_format($inc_val->payment_type == 'Discount' ? abs($inc_val->balance_amount) : $inc_val->balance_amount, 2) : '' }}
                                                        @endif
                                                    </td>
                                                        <?php
                                                        $statusClassMap = [
                                                            '0' => 'status-unpaid',
                                                            '1' => 'status-paid',
                                                            '2' => 'status-partial',
                                                            '3' => 'status-void'
                                                        ];

                                                        $statusVal = [
                                                            '0' => 'Unpaid',
                                                            '1' => 'Paid',
                                                            '2' => 'Partial',
                                                            '3' => 'Void',
                                                            '4' => 'Discount'

                                                        ];

                                                        $status = $inc_val->invoice_status;
                                                        $statusClass = $statusClassMap[$status];
                                                        if( isset($inc_val->payment_type) && $inc_val->payment_type == 'Discount'){
                                                            $status = 4; //Discount
                                                        } else {
                                                            $status = $status;
                                                        }
                                                        $statusDes = $statusVal[$status];
                                                        ?>

                                                    <td>
                                                        <span class="status-badge <?php echo $statusClass; ?>">
                                                            <?php echo $statusDes; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                        <?php
                                            } //end foreach
                                        }
                                        ?>

                                    </tbody>
                                </table>

                                <h4 style="margin-top:25px; margin-bottom: 10px; font-weight: 600;">Direct Office Receipts</h4>
                                <table class="transaction-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th colspan="2">Method</th>
                                            <th>Description</th>
                                            <th>Reference</th>
                                            <th class="currency" style="white-space: initial;">Amount Received</th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitemList_office">
                                        <?php
                                        $receipts_lists_office = DB::table('account_client_receipts')->where('client_matter_id',$client_selected_matter_id)->where('client_id',$fetchedData->id)->where('receipt_type',2)->orderBy('id', 'desc')->get();
                                        //dd($receipts_lists_office);
                                        if(!empty($receipts_lists_office) && count($receipts_lists_office)>0 )
                                        {
                                            foreach($receipts_lists_office as $off_list=>$off_val)
                                            {
                                            ?>
                                            <tr class="drow_account_office" data-matterid="{{$off_val->client_matter_id}}">
                                                <td>
                                                    <span style="display: inline-flex;">
                                                        <?php
                                                        if( isset($off_val->validate_receipt) && $off_val->validate_receipt == '1' )
                                                        { ?>
                                                            <i class="fas fa-check-circle" title="Verified Receipt" style="margin-top: 7px;"></i>
                                                        <?php
                                                        } ?>
                                                        <?php echo $off_val->trans_date;?>
                                                    </span>
                                                    <?php
                                                    if(isset($off_val->uploaded_doc_id) && $off_val->uploaded_doc_id >0){
                                                        $office_doc_list = DB::table('documents')->select('myfile')->where('id',$off_val->uploaded_doc_id)->first();
                                                        if($office_doc_list){ ?>
                                                            <a title="See Attached Document" target="_blank" class="link-primary" href="<?php echo $office_doc_list->myfile;?>"><i class="fas fa-file-pdf"></i></a>
                                                        <?php
                                                        }
                                                    } ?>
                                                </td>
                                                <?php
                                                $payClassMap = [
                                                    'Cash' => 'fa-arrow-down',
                                                    'Bank transfer' => 'fa-arrow-right-from-bracket',
                                                    'EFTPOS' => 'fa-arrow-right-from-bracket',
                                                    'Refund' => 'fa-arrow-right-from-bracket'
                                                ];
                                                ?>
                                                <td class="type-cell">
                                                   <i class="fas  <?php echo $payClassMap[$off_val->payment_method]; ?> type-icon"></i>
                                                   <span>
                                                    {{$off_val->payment_method}}

                                                    <?php
                                                    if( isset($off_val->extra_amount_receipt) &&  $off_val->extra_amount_receipt == 'exceed' ) {

                                                    } else { ?>
                                                        <br/>
                                                        {{ !empty($off_val->invoice_no) ? '('.$off_val->invoice_no.')' : '' }}
                                                    <?php
                                                    }?>

                                                   </span>
                                                </td><td></td>

                                                <td class="description"><?php echo $off_val->description;?></td>
                                                <!--<td><a href="#" title="View Receipt {{--$off_val->trans_no--}}"><?php //echo $off_val->trans_no;?></a></td>-->
                                                <td><a target="_blank" href="{{URL::to('/admin/clients/genofficereceiptInvoice')}}/{{$off_val->id}}" title="View Receipt"><?php echo $off_val->trans_no;?></a></td>

                                                <td class="currency text-success">{{ !empty($off_val->deposit_amount) ? '$ ' . number_format($off_val->deposit_amount, 2) : '' }}</td>
                                            </tr>
                                        <?php
                                            } //end foreach
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>
            </div>


            <!-- Emails Tab -->
            <div class="tab-pane" id="conversations-tab">
                <div class="card full-width emails-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs">
                        <button class="subtab-button active" data-subtab="inbox">Inbox</button>
                        <button class="subtab-button" data-subtab="sent">Sent</button>
                        <!--<button class="subtab-button" data-subtab="sms">SMS</button>-->
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab-content" id="subtab-content">
                        <!-- Inbox Subtab -->
                        <div class="subtab-pane active" id="inbox-subtab">
                            <div class="email-header">
                                <h3>Inbox Emails</h3>
                                <div class="email-actions">
                                    <button class="btn btn-primary btn-sm uploadAndFetchMail" id="new-email-btn">
                                        <i class="fas fa-plus"></i> Upload Inbox Mail
                                    </button>
                                </div>
                            </div>
                            <div class="email-filters">
                                <select  class="filter-select" id="filter-status">
                                    <option value="">Filter by Status</option>
                                    <option value="2">Unread</option>
                                    <option value="1">Read</option>
                                </select>
                                <input type="text" name="search_communication" class="search-input" id="search-communication" placeholder="Search Communication...">
                            </div>
                            <div class="email-list" id="email-list">
                                <?php
                                //inbox mail
                                $mailreports = \App\Models\MailReport::where('client_id',$fetchedData->id)->where('type','client')->where('mail_type',1)->where('conversion_type','conversion_email_fetch')->where('mail_body_type','inbox')->orderby('created_at', 'DESC')->get();
                                //dd($mailreports);
                                foreach($mailreports as $mailreport)
                                {
                                    $DocInfo = \App\Models\Document::select('id','doc_type','myfile','myfile_key','mail_type')->where('id',$mailreport->uploaded_doc_id)->first();
                                    $AdminInfo = \App\Models\Admin::select('client_id')->where('id',$mailreport->client_id)->first();
                                    ?>
                                    <div class="email-card" data-matterid="{{$mailreport->client_matter_id}}">
                                        <div class="email-meta">
                                            <span class="author-initial">{{ strtoupper(substr(@$mailreport->from_mail, 0, 1)) }}</span>
                                            <div class="email-info">
                                                <span class="author-name">From: {{@$mailreport->from_mail}}</span>
                                                <span class="email-timestamp">
                                                    <?php if(isset($mailreport->fetch_mail_sent_time) && $mailreport->fetch_mail_sent_time != "") { ?>
                                                        <span>{{$mailreport->fetch_mail_sent_time}}</span>
                                                    <?php }  else {?>
                                                        <span></span>
                                                    <?php } ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="email-body">
                                            <h4>{{ substr(@$mailreport->subject, 0, 50) }}</h4>
                                            <p>To:{{@$mailreport->to_mail}}</p>
                                        </div>
                                        <div class="email-actions">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            if($DocInfo)
                                            { ?>
                                                <?php if( isset($DocInfo->myfile_key) && $DocInfo->myfile_key != ""){ ?>
                                                <a class="btn btn-link mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank"  href="<?php echo $DocInfo->myfile; ?>" >Preview</a>
                                                <?php } else { ?>
                                                    <a class="btn btn-link mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank" href="<?php echo $url.$AdminInfo->client_id.'/'.$DocInfo->doc_type.'/'.$DocInfo->mail_type.'/'.$DocInfo->myfile; ?>">Preview</a>
                                                <?php } ?>
                                            <?php
                                            }?>
                                            <button class="btn btn-link create_note" datamailid="{{$mailreport->id}}" datasubject="{{@$mailreport->subject}}" datatype="mailnote">Create Note</button>
                                            <button class="btn btn-link inbox_reassignemail_modal" memail_id="{{@$mailreport->id}}" user_mail="{{@$mailreport->to_mail}}" uploaded_doc_id="{{@$mailreport->uploaded_doc_id}}" href="javascript:;">Reassign</button>

                                        </div>
                                    </div>
                                <?php
                                }  //end foreach ?>
                            </div>
                        </div>

                        <!-- Sent Subtab -->
                        <div class="subtab-pane" id="sent-subtab">
                            <div class="email-header">
                                <h3>Sent Emails</h3>
                                <div class="email-actions">
                                    <a class="btn btn-primary btn-sm clientemail" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" id="email-tab" href="#email" role="tab" aria-controls="email" aria-selected="true">Compose Email</a>
                                    <a class="btn btn-primary btn-sm uploadSentAndFetchMail" id="new-email-btn-sent" href="javascript:;"><i class="fas fa-plus"></i> Upload Sent Mail</a>
                                </div>
                            </div>
                            <div class="email-filters">
                                <select class="filter-select" id="filter-type1">
                                    <option value="">Filter by Type</option>
                                    <option value="1">Assigned</option>
                                    <option value="2">Delivered</option>
                                </select>

                                <select  class="filter-select" id="filter-status1">
                                    <option value="">Filter by Status</option>
                                    <option value="2">Unread</option>
                                    <option value="1">Read</option>
                                </select>


                                <input type="text" class="search-input" id="search-communication1" placeholder="Search Communication...">
                            </div>
                            <div class="email-list1" id="email-list1">
                                <?php
                                //Sent Mail after assign user and Compose email
                                $mailreports = \App\Models\MailReport::where('client_id', $fetchedData->id)
                                ->where('type', 'client')
                                ->where('mail_type', 1)
                                ->where(function($query) {
                                    $query->whereNull('conversion_type')
                                    ->orWhere(function($subQuery) {
                                        $subQuery->where('conversion_type', 'conversion_email_fetch')
                                        ->where('mail_body_type', 'sent');
                                    });
                                })
                                ->orderBy('created_at', 'DESC')
                                ->get();
                                foreach($mailreports as $mailreport)
                                {
                                    $admin = \App\Models\Admin::where('id', $mailreport->user_id)->first();
                                    $client = \App\Models\Admin::Where('id', $fetchedData->id)->first();
                                    $subject = str_replace('{Client First Name}',$client->first_name, $mailreport->subject);
                                    $message = $mailreport->message;
                                    $message = str_replace('{Client First Name}',$client->first_name, $message);
                                    $message = str_replace('{Client Assignee Name}',$client->first_name, $message);
                                    $message = str_replace('{Company Name}',Auth::user()->company_name, $message);
                                    ?>
                                    <div class="email-card" data-matterid="{{$mailreport->client_matter_id}}">
                                        <div class="email-meta">
                                            <span class="author-initial">{{ strtoupper(substr($admin->first_name, 0, 1)) }}</span>
                                            <div class="email-info">
                                                <span class="author-name">Sent by:
                                                    <strong>{{@$admin->first_name}}</strong> [{{$mailreport->from_mail}}]

                                                    <?php
                                                if( isset($mailreport->conversion_type) && $mailreport->conversion_type != ""){ ?>
                                                    <span style="background: #21ba45;color: #fff;font-size: 12px;line-height: 16px;padding: 3px 8px;border-radius: 4px;">Assigned</span>
                                                <?php } else { ?>
                                                    <span style="background: #FCCD02;color: #fff;font-size: 12px;line-height: 16px;padding: 3px 8px;border-radius: 4px;">Delivered</span>
                                                <?php } ?>
                                                </span>



                                                <span class="email-timestamp">
                                                    <?php
                                                    if( isset($mailreport->conversion_type) && $mailreport->conversion_type == "conversion_email_fetch")
                                                    {?>
                                                        <div class="date">
                                                            <?php if(isset($mailreport->fetch_mail_sent_time) && $mailreport->fetch_mail_sent_time != "") { ?>
                                                                <span>{{ $mailreport->fetch_mail_sent_time}}</span>
                                                            <?php }  else {?>
                                                                <span></span>
                                                            <?php } ?>
                                                        </div>
                                                    <?php
                                                    }
                                                    else
                                                    { ?>
                                                        <div class="date">
                                                            <?php if(isset($mailreport->created_at) && $mailreport->created_at != "") { ?>
                                                                <span>{{@date('d/m/Y h:i A',strtotime($mailreport->created_at))}}</span>
                                                            <?php }  else {?>
                                                                <span></span>
                                                            <?php } ?>
                                                        </div>
                                                    <?php
                                                    }?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="email-body">
                                            <h4>{{$subject}}</h4>
                                            <p>Sent To: {{$client->email}}</p>
                                        </div>
                                        <div class="email-actions">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            $AdminInfo = \App\Models\Admin::select('client_id')->where('id',$fetchedData->id)->first();
                                            if( isset($mailreport->uploaded_doc_id) && $mailreport->uploaded_doc_id != "")
                                            {
                                                $DocInfo = \App\Models\Document::select('id','doc_type','myfile','myfile_key','mail_type')->where('id',$mailreport->uploaded_doc_id)->first();
                                                if($DocInfo)
                                                { ?>
                                                    <?php if( isset($DocInfo->myfile_key) && $DocInfo->myfile_key != ""){ ?>
                                                        <a class="btn btn-link mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank"  href="<?php echo $DocInfo->myfile; ?>" ><i class="fas fa-eye"></i> Preview</a>
                                                    <?php } else { ?>
                                                        <a class="btn btn-link mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank"  href="<?php echo $url.$AdminInfo->client_id.'/'.$DocInfo->doc_type.'/'.$DocInfo->mail_type.'/'.$DocInfo->myfile; ?>" ><i class="fas fa-eye"></i> Preview</a>
                                                    <?php } ?>
                                                <?php
                                                }
                                            }
                                            else
                                            { ?>
                                                <a class="btn btn-link sent_mail_preview_modal" memail_message="{{@$mailreport->message}}" memail_subject="{{@$mailreport->subject}}"><i class="fas fa-eye"></i> Preview Mail</a>
                                            <?php
                                            } ?>

                                            <button class="btn btn-link create_note" datamailid="{{$mailreport->id}}" datasubject="{{@$mailreport->subject}}" datatype="mailnote">Create Note</button>
                                            <?php
                                            if( isset($mailreport->conversion_type) && $mailreport->conversion_type != "")
                                            { ?>
                                                <button class="btn btn-link sent_reassignemail_modal" memail_id="{{@$mailreport->id}}" user_mail="{{@$mailreport->to_mail}}" uploaded_doc_id="{{@$mailreport->uploaded_doc_id}}" href="javascript:;">Reassign</button>
                                            <?php
                                            } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- SMS Subtab -->
                        <!--<div class="subtab-pane" id="sms-subtab">
                            <div class="email-header">
                                <h3>SMS Messages</h3>
                                <div class="email-actions">
                                    <button class="btn btn-primary btn-sm" id="new-sms-btn">
                                        <i class="fas fa-plus"></i> New SMS
                                    </button>
                                </div>
                            </div>
                            <div class="email-list">
                                <p>No SMS messages available.</p>

                            </div>
                        </div>-->
                    </div>
                </div>
            </div>


            <!-- AI Tab -->
            <div class="tab-pane" id="artificialintelligences-tab">
                <div class="AIS-container">
                    <div class="row">
                        <!-- Left Side: Chat History -->
                        <div class="col-md-4">
                            <div class="AIS-header">
                                <h3><i class="fas fa-history"></i> Chat History</h3>
                                <button class="btn new-chat-btn">New Chat</button>
                            </div>
                            <div class="chat-history-list">
                                <div class="loading-history text-center">
                                    <p>Loading chat history...</p>
                                </div>
                                <div class="chat-history-content" style="display: none;">
                                    <!-- Chat history will be populated dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Chat Area -->
                        <div class="col-md-8">
                            <div class="chat-area">
                                <div class="loading-data text-center">
                                    <p id="client-data-loading">Client data are loading...</p>
                                    <p id="client-notes-loading">Client notes are loading...</p>
                                    <p id="client-details-loading">Client personal details are loading...</p>
                                </div>
                                <div class="ai-ready-message text-center" style="display: none;">
                                    <p>AI is ready to answer your query.</p>
                                </div>
                                <div class="chat-content" style="display: none;">
                                    <div class="chat-messages" style="height: 400px; overflow-y: auto; border: 1px solid #e9ecef; padding: 15px; border-radius: 8px;">
                                        <!-- Messages will be populated dynamically -->
                                    </div>
                                    <div class="chat-input mt-3">
                                        <textarea class="form-control" id="chat-input" rows="3" placeholder="Type your question here..."></textarea>
                                        <button class="btn btn-primary mt-2 send-chat-btn" style="background-color: #4a90e2 !important;">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .chat-history-list {
                    max-height: 500px;
                    overflow-y: auto;
                    border: 1px solid #e9ecef;
                    border-radius: 8px;
                    padding: 10px;
                }

                .chat-history-item {
                    padding: 10px;
                    border-bottom: 1px solid #e9ecef;
                    cursor: pointer;
                    transition: background-color 0.2s ease;
                }

                .chat-history-item:hover {
                    background-color: #f1f5f9;
                }

                .chat-history-item.active {
                    background-color: #e9ecef;
                    font-weight: 600;
                }

                .chat-history-item h5 {
                    margin: 0;
                    font-size: 1rem;
                    color: #2c3e50;
                }

                .chat-history-item p {
                    margin: 0;
                    font-size: 0.85rem;
                    color: #6c757d;
                }

                .chat-messages .message {
                    margin-bottom: 15px;
                }

                .chat-messages .message.user {
                    text-align: right;
                }

                .chat-messages .message.ai {
                    text-align: left;
                }

                .chat-messages .message .message-content {
                    display: inline-block;
                    padding: 10px 15px;
                    border-radius: 8px;
                    max-width: 70%;
                }

                .chat-messages .message.user .message-content {
                    background-color: #4a90e2;
                    color: #fff;
                }

                .chat-messages .message.ai .message-content {
                    background-color: #f1f5f9;
                    color: #2c3e50;
                }

                .chat-input textarea {
                    resize: none;
                }
            </style>

            <!-- Form generation Tab Client -->
            <div class="tab-pane" id="formgenerations-tab">
                <div class="card full-width forms-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs3">
                        <button class="subtab3-button active" data-subtab="form956">Form 956</button>
                        <button class="subtab3-button" data-subtab="costform">Cost Assignment</button>
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab3-content" id="subtab3-content">
                        <style>
                            .form956-table th, .form956-table td ,.costform-table th, .costform-table td {
                                color: #343a40 !important;
                            }
                        </style>
                        <!-- form956 Subtab -->
                        <div class="subtab3-pane active" id="form956-subtab">
                            <div class="form-header">
                                <h3 class="text-2xl font-semibold text-gray-800">Form 956</h3>
                                <div class="form-actions">
                                    <button class="btn btn-primary btn-sm form956CreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200" id="new-form-btn">
                                        <i class="fas fa-plus mr-2"></i> Create Form 956
                                    </button>
                                </div>
                            </div>

                            <div class="form-list" id="form-list">
                                <?php
                                // Fetch Form 956 records for the given client
                                $formlists = collect(); // Always a Collection
                                if($id1)
                                { //if client unique reference id is present in url
                                    $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                    if($matter_info_arr->id){
                                        $formlists = \App\Models\Form956::where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $matter_info_arr->id)
                                        ->with(['client', 'agent']) // Eager load relationships
                                        ->orderBy('created_at', 'DESC')
                                        ->get(); //dd($formlists);
                                    }
                                }
                                else
                                {
                                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                    //dd($matter_cnt);
                                    if($matter_cnt >0){
                                        $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                        if($matter_info_arr->id){
                                            $formlists = \App\Models\Form956::where('client_id', $fetchedData->id)
                                            ->where('client_matter_id', $matter_info_arr->id)
                                            ->with(['client', 'agent']) // Eager load relationships
                                            ->orderBy('created_at', 'DESC')
                                            ->get(); //dd($formlists);
                                        }
                                    }
                                }
                                ?>

                                @if($formlists->isEmpty())
                                    <p class="text-gray-600 text-center py-6">No Form 956 records found for this client.</p>
                                @else
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">
                                        <div class="border-t border-gray-200">
                                            <table class="min-w-full form956-table border border-gray-300" style="width: 1227px !important;">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="p-4 text-center border">Client</th>
                                                        <th class="p-4 text-center border">Form Type</th>
                                                        <th class="p-4 text-center border">Agent</th>
                                                        <th class="p-4 text-center border">Agent Type</th>
                                                        <th class="p-4 text-center border">Assistance Type</th>
                                                        <th class="p-4 text-center border">Authorized Recipient</th>
                                                        <th class="p-4 text-center border">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    @foreach($formlists as $formlist)
                                                        <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                                                            <!-- Client -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{$formlist->client->first_name . ' ' . $formlist->client->last_name}}
                                                            </td>
                                                            <!-- Form Type -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist->form_type === 'appointment' ? 'New Appointment' : 'Withdrawal' }}
                                                            </td>
                                                            <!-- Agent -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist->agent->first_name. ' ' . $formlist->agent->last_name }} <br/> ({{ $formlist->agent->company_name }})
                                                            </td>
                                                            <!-- Agent Type -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                @if ($formlist->is_registered_migration_agent) Registered Migration Agent @endif
                                                                @if ($formlist->is_legal_practitioner) Legal Practitioner @endif
                                                                @if ($formlist->is_exempt_person) Exempt Person @endif
                                                            </td>
                                                            <!-- Assistance Type -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                @if ($formlist->assistance_visa_application) Visa Application<br> @endif
                                                                @if ($formlist->assistance_sponsorship) Sponsorship<br> @endif
                                                                @if ($formlist->assistance_nomination) Nomination<br> @endif
                                                                @if ($formlist->assistance_cancellation) Cancellation<br> @endif
                                                                @if ($formlist->assistance_ministerial_intervention) Ministerial Intervention<br> @endif
                                                                @if ($formlist->assistance_other) Other: {{ $formlist->assistance_other_details }} @endif
                                                            </td>
                                                            <!-- Authorized Recipient -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist->is_authorized_recipient ? 'Yes' : 'No' }}
                                                            </td>
                                                            <!-- Actions -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                <a title="Preview PDF" href="{{ route('forms.preview', $formlist) }}" target="_blank" > <i class="fas fa-eye"></i></a><br/>
                                                                <a title="Download PDF" href="{{ route('forms.pdf', $formlist) }}" ><i class="fas fa-download"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        

                        <!-- Cost Assignment Form Subtab -->
                        <div class="subtab3-pane" id="costform-subtab">
                            <div class="form-header">
                                <h3 class="text-2xl font-semibold text-gray-800">Cost Assignment Form</h3>
                                <div class="form-actions">
                                    <button class="btn btn-primary btn-sm costAssignmentCreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                        <i class="fas fa-plus mr-2"></i> Create Cost Assignment
                                    </button>
                                </div>
                            </div>

                            <div class="form-list1" id="form-list1">
                                <?php
                                $formlists1 = collect(); // Always a Collection
                                // Fetch cost_assignment_forms for the given client
                                if($id1)
                                { //if client unique reference id is present in url
                                    $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                    if($matter_info_arr->id){
                                        $formlists1 = \App\Models\CostAssignmentForm::where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $matter_info_arr->id)
                                        ->with(['client', 'agent']) // Eager load relationships
                                        ->orderBy('created_at', 'DESC')
                                        ->get(); //dd($formlists1);
                                    }
                                }
                                else
                                {
                                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                    if($matter_cnt >0){
                                        $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                        if($matter_info_arr->id){
                                            $formlists1 = \App\Models\CostAssignmentForm::where('client_id', $fetchedData->id)
                                            ->where('client_matter_id', $matter_info_arr->id)
                                            ->with(['client', 'agent']) // Eager load relationships
                                            ->orderBy('created_at', 'DESC')
                                            ->get(); //dd($formlists1);
                                        }
                                    }
                                }
                                ?>

                                @if($formlists1->isEmpty())
                                    <p class="text-gray-600 text-center py-6">No Cost Assignment records found for this client.</p>
                                @else
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">
                                        <div class="border-t border-gray-200">
                                            <table class="min-w-full costform-table border border-gray-300" style="width: 1227px !important;">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="p-4 text-center border">Client</th>
                                                        <th class="p-4 text-center border">Agent</th>
                                                        <th class="p-4 text-center border">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    @foreach($formlists1 as $formlist1)
                                                        <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                                                            <!-- Client -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{$formlist1->client->first_name . ' ' . $formlist1->client->last_name}}
                                                            </td>

                                                            <!-- Agent -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist1->agent->first_name. ' ' . $formlist1->agent->last_name }} <br/> ({{ $formlist1->agent->company_name }})
                                                            </td>

                                                            <!-- Actions -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                <button class="btn btn-primary btn-sm costAssignmentCreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                                                    <i class="fas fa-eye"></i> Preview Cost Assignment
                                                                </button>

                                                                <button class="btn btn-primary btn-sm visaAgreementCreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                                                    <i class="fas fa-plus mr-2"></i> Create Visa Agreement
                                                                </button>

                                                                <button class="btn btn-primary btn-sm finalizeAgreementConvertToPdf inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                                                    <i class="fas fa-lock"></i> Finalize Agreement and Upload PDF
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="form-list2" id="form-list2">
                                <?php
                                $formlists2 = collect(); // Always a Collection
                                // Fetch cost_assignment_forms for the given client
                                if($id1)
                                { //if client unique reference id is present in url
                                    $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                    if($matter_info_arr->id){
                                        $formlists2 = \App\Models\Document::where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $matter_info_arr->id)
                                        ->where('type', 'client')
                                        ->where('doc_type', 'agreement')
                                        ->orderBy('updated_at', 'DESC')
                                        ->get(); //dd($formlists2);
                                    }
                                }
                                else
                                {
                                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                    if($matter_cnt >0){
                                        $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                        if($matter_info_arr->id){
                                            $formlists2 = \App\Models\Document::where('client_id', $fetchedData->id)
                                            ->where('client_matter_id', $matter_info_arr->id)
                                            ->where('type', 'client')
                                            ->where('doc_type', 'agreement')
                                            ->orderBy('updated_at', 'DESC')
                                            ->get(); //dd($formlists2);
                                        }
                                    }
                                }
                                //echo "<pre>formlists2="; print_r($formlists2);
                                ?>
                                @if($formlists2->isEmpty())
                                    <h2>List of Agreement</h2>
                                    <p class="text-gray-600 text-center py-6">No Agreement List records found for this client.</p>
                                @else
                                    <h2>List of Agreement</h2>
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">
                                        <div class="border-t border-gray-200">
                                            <table class="min-w-full costform-table border border-gray-300" style="width: 1227px !important;">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="p-4 text-center border">SNo.</th>
                                                        <th class="p-4 text-center border">Added By</th>
                                                        <th class="p-4 text-center border">File Name</th>
                                                        <th class="p-4 text-center border">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    <?php //echo "<pre>formlists2="; print_r($formlists2); ?>
                                                    @foreach($formlists2 as $formlistkey2=>$formlist2)
                                                    <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                                                        <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                            <?= $formlistkey2 + 1 ?>
                                                        </td>
                                                        <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                            <?php
                                                            $admin = \App\Models\Admin::where('id', $formlist2->user_id)->first();
                                                            ?>
                                                            <?= htmlspecialchars($admin->first_name ?? 'NA') ?><br>
                                                            <?= date('d/m/Y', strtotime($formlist2->created_at)) ?>
                                                        </td>
                                                        <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                            <a target="_blank" class="dropdown-item" href="{{$formlist2->myfile}}">
                                                            {{$formlist2->file_name.'.'.$formlist2->filetype}}
                                                            </a>
                                                        </td>
                                                        <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                            @if (strtolower($formlist2->filetype) === 'pdf')
                                                                @if ($formlist2->status === 'draft')
                                                                    <a target="_blank" href="{{ route('documents.edit', $formlist2->id) }}" class="btn btn-primary">Create Signature Link</a>
                                                                @endif

                                                                @if($formlist2->status === 'sent')
                                                                    <a target="_blank" href="{{ route('documents.index', $formlist2->id) }}" class="btn btn-primary">Check To Signature</a>
                                                                @endif

                                                                @if($formlist2->status === 'signed')
                                                                    <a target="_blank" href="{{ route('download.signed', $formlist2->id) }}" class="btn btn-primary">Download Signed</a>
                                                                @endif

                                                                <a target="_blank" href="{{ route('documents.index', $formlist2->id) }}" class="btn btn-primary">Go To Document</a>


                                                                
                                                                
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>

                    </div>
                </div>
            </div>



            <!-- Form generation Tab Lead -->
            <div class="tab-pane" id="formgenerationsL-tab">
                <div class="card full-width forms-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs3">
                        <button class="subtab3-button active" data-subtab="costformL">Cost Assignment</button>
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab3-content" id="subtab3-content">
                        <!-- Cost Assignment Form Subtab Lead -->
                        <div class="subtab3-pane active" id="costformL-subtab">
                            <div class="form-header">
                                <h3 class="text-2xl font-semibold text-gray-800">Cost Assignment Form</h3>
                                <div class="form-actions">
                                    <button class="btn btn-primary btn-sm costAssignmentCreateFormLead inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                        <i class="fas fa-plus mr-2"></i> Create Cost Assignment
                                    </button>
                                </div>
                            </div>

                            <div class="form-list1" id="form-list1">
                                <?php
                                $formlists1 = collect(); // Always a Collection
                                // Fetch cost_assignment_forms for the given client
                                if($id1)
                                { //if client unique reference id is present in url
                                    $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                    if($matter_info_arr->id){
                                        $formlists1 = \App\Models\CostAssignmentForm::where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $matter_info_arr->id)
                                        ->with(['client', 'agent']) // Eager load relationships
                                        ->orderBy('created_at', 'DESC')
                                        ->get(); //dd($formlists1);
                                    }
                                }
                                else
                                {
                                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                    if($matter_cnt >0){
                                        $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                        if($matter_info_arr->id){
                                            $formlists1 = \App\Models\CostAssignmentForm::where('client_id', $fetchedData->id)
                                            ->where('client_matter_id', $matter_info_arr->id)
                                            ->with(['client', 'agent']) // Eager load relationships
                                            ->orderBy('created_at', 'DESC')
                                            ->get(); //dd($formlists1);
                                        }
                                    }
                                }
                                ?>

                                @if($formlists1->isEmpty())
                                    <p class="text-gray-600 text-center py-6">No Cost Assignment records found for this client.</p>
                                @else
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">
                                        <div class="border-t border-gray-200">
                                            <table class="min-w-full costform-table border border-gray-300" style="width: 1227px !important;">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="p-4 text-center border">Client</th>
                                                        <th class="p-4 text-center border">Agent</th>
                                                        <th class="p-4 text-center border">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    @foreach($formlists1 as $formlist1)
                                                        <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                                                            <!-- Client -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{$formlist1->client->first_name . ' ' . $formlist1->client->last_name}}
                                                            </td>

                                                            <!-- Agent -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist1->agent->first_name. ' ' . $formlist1->agent->last_name }} <br/> ({{ $formlist1->agent->company_name }})
                                                            </td>

                                                            <!-- Actions -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                <button class="btn btn-primary btn-sm costAssignmentCreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                                                    <i class="fas fa-eye"></i> Preview Cost Assignment
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Appointment Tab -->
            <div class="tab-pane" id="appointments-tab">
                <div class="card-header-action text-right" style="padding-bottom:15px;">
                    <a href="javascript:;" data-toggle="modal" class="btn btn-primary createaddapointment"><i class="fa fa-plus"></i> Add</a>
                </div>
                <div class="appointmentlist">
                    <div class="row">
                        <div class="col-md-5 appointment_grid_list">
                            <?php
                            $rr=0;
                            $appointmentdata = array();
                            $appointmentlists = \App\Models\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'client')->orderby('created_at', 'DESC')->get();

                            /*if($_SERVER["REMOTE_ADDR"] == '49.36.214.255') {
                                dd($appointmentlists);
                            }*/
                            $appointmentlistslast = \App\Models\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'client')->orderby('created_at', 'DESC')->first();
                            foreach($appointmentlists as $appointmentlist){
                                $admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $appointmentlist->user_id)->first();
                                $first_name= $admin->first_name ?? 'N/A';
                                $datetime = $appointmentlist->created_at;
                                $timeago = Controller::time_elapsed_string($datetime);

                                $appointmentdata[$appointmentlist->id] = array(
                                    'title' => $appointmentlist->title,
                                    'time' => date('H:i A', strtotime($appointmentlist->time)),
                                    'date' => date('d D, M Y', strtotime($appointmentlist->date)),
                                    //'description' => $appointmentlist->description,
                                    'description' => htmlspecialchars($appointmentlist->description, ENT_QUOTES, 'UTF-8'),
                                    'createdby' => substr($first_name, 0, 1),
                                    'createdname' => $first_name,
                                    'createdemail' => $admin->email ?? 'N/A',
                                );
                            ?>

                            <div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
                                <div class="appointment_col">
                                    <div class="appointdate">
                                        <h5><?php echo date('d D', strtotime($appointmentlist->date)); ?></h5>
                                        <p><?php echo date('H:i A', strtotime($appointmentlist->time)); ?><br>
                                        <i><small><?php echo $timeago ?></small></i></p>
                                    </div>
                                    <div class="title_desc">
                                        <h5><?php echo $appointmentlist->title; ?></h5>
                                        <p><?php echo $appointmentlist->description; ?></p>
                                    </div>
                                    <div class="appoint_created">
                                        <span class="span_label">Created By:
                                        <span><?php echo substr($first_name, 0, 1); ?></span></span>
                                        <div class="dropdown d-inline dropdown_ellipsis_icon">
                                            <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu">
                                                <!--<a class="dropdown-item edit_appointment" data-id="{{--$appointmentlist->id--}}" href="javascript:;">Edit</a>-->
                                                <a data-id="{{$appointmentlist->id}}" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $rr++; } ?>
                        </div>
                        <div class="col-md-7">
                            <div class="editappointment">
                            @if($appointmentlistslast)
                                <!--<a class="edit_link edit_appointment" href="javascript:;" data-id="<?php //echo @$appointmentlistslast->id; ?>"><i class="fa fa-edit"></i></a>-->
                                <?php
                                $adminfirst = \App\Models\Admin::select('id', 'first_name','email')->where('id', @$appointmentlistslast->user_id)->first();
                                ?>
                                <div class="content">
                                    <h4 class="appointmentname"><?php echo @$appointmentlistslast->title; ?></h4>
                                    <div class="appitem">
                                        <i class="fa fa-clock"></i>
                                        <span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime(@$appointmentlistslast->time)); ?></span>
                                    </div>
                                    <div class="appitem">
                                        <i class="fa fa-calendar"></i>
                                        <span class="appcontent appointmentdate"><?php echo date('d D, M Y', strtotime(@$appointmentlistslast->date)); ?></span>
                                    </div>
                                    <div class="description appointmentdescription">
                                        <p><?php echo @$appointmentlistslast->description; ?></p>
                                    </div>
                                    <div class="created_by">
                                        <span class="label">Created By:</span>
                                        <div class="createdby">
                                            <span class="appointmentcreatedby"><?php echo substr(@$adminfirst->first_name, 0, 1); ?></span>
                                        </div>
                                        <div class="createdinfo">
                                            <a href="" class="appointmentcreatedname"><?php echo @$adminfirst->first_name ?></a>
                                            <p class="appointmentcreatedemail"><?php echo @$adminfirst->primary_email; ?></p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Activity Feed (Only visible with Personal Details) -->
    <aside class="activity-feed" id="activity-feed">
        <h2><i class="fas fa-history"></i> Activity Feed</h2>
        <ul class="feed-list">
            <?php
            if(
                ( isset($_REQUEST['user']) && $_REQUEST['user'] != "" )
                ||
                ( isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != "" )
            ){
                $user_search = $_REQUEST['user'];
                $keyword_search = $_REQUEST['keyword'];

                if($user_search != "" && $keyword_search != "") {
                    $activities = \App\Models\ActivitiesLog::select('activities_logs.*')
                    ->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($user_search) {
                        $query->where('admins.first_name', 'like', '%'.$user_search.'%');
                    })
                    ->where(function($query) use ($keyword_search) {
                        $query->where('activities_logs.description', 'like', '%'.$keyword_search.'%');
                        $query->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
                }
                else if($user_search == "" && $keyword_search != "") {
                    $activities = \App\Models\ActivitiesLog::select('activities_logs.*')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($keyword_search) {
                        $query->where('activities_logs.description', 'like', '%'.$keyword_search.'%');
                        $query->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
                }
                else if($user_search != "" && $keyword_search == "") {
                    $activities = \App\Models\ActivitiesLog::select('activities_logs.*','admins.first_name','admins.last_name','admins.email')
                    ->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
                    ->where('activities_logs.client_id', $fetchedData->id)
                    ->where(function($query) use ($user_search) {
                        $query->where('admins.first_name', 'like', '%'.$user_search.'%');
                    })
                    ->orderby('activities_logs.created_at', 'DESC')
                    ->get();
                }
            } else {
                $activities = \App\Models\ActivitiesLog::where('client_id', $fetchedData->id)
                ->orderby('created_at', 'DESC')
                ->get();
            }
            //dd($activities);
            foreach($activities as $activit)
            {
                $admin = \App\Models\Admin::where('id', $activit->created_by)->first();
                ?>
                <li class="feed-item feed-item--email activity" id="activity_{{$activit->id}}">
                    <span class="feed-icon">
                        <?php
                        if (str_contains($activit->subject, "document")) {
                            echo '<i class="fas fa-file-alt"></i>';
                        } else {
                            echo '<i class="fas fa-sticky-note"></i>';
                        }?>
                    </span>
                    <div class="feed-content">
                        <p><strong>{{ $admin->first_name ?? 'NA' }}  <?php echo @$activit->subject; ?></strong> -
                            @if($activit->description != '')
                                <p>{!!$activit->description!!}</p>
                            @endif
                        </p>
                        <span class="feed-timestamp">{{date('d M Y, H:i A', strtotime($activit->created_at))}}</span>
                    </div>
                </li>
            <?php
			}
			?>
        </ul>
        <!--<button class="btn btn-secondary btn-block">View Full History</button>-->
    </aside>
</div>

@include('Admin/clients/addclientmodal')
@include('Admin/clients/editclientmodal')

<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="{{URL::to('/admin/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
                    <input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                    <input type="hidden" name="mail_type" value="1">
                    <input type="hidden" name="mail_body_type" value="sent">
                    <input type="hidden" name="compose_client_matter_id" id="compose_client_matter_id" value="">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from" data-valid="required">
                                    <option value="">Select From</option>
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $nemail){
										?>
											<option value="<?php echo $nemail->email; ?>"><?php echo $nemail->email; ?></option>
										<?php
									}?>
								</select>
								@if ($errors->has('email_from'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_from') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_to">To <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax" name="email_to[]"></select>

								@if ($errors->has('email_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_to') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxccd" name="email_cc[]"></select>

								@if ($errors->has('email_cc'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_cc') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<!--<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
								<select data-valid="" class="form-control select2 selecttemplate" name="template">
									<option value="">Select</option>
									{{--@foreach(\App\Models\CrmEmailTemplate::all() as $list)--}}
										<option value="{{--$list->id--}}">{{--$list->name--}}</option>
									{{--@endforeach--}}
								</select>
                            </div>
						</div>-->

                        <div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
                                <?php
                                $assignee = \App\Models\Admin::select('first_name')->where('id',@$fetchedData->assignee)->first();
                                if($assignee){
                                    $clientAssigneeName = $assignee->first_name;
                                } else {
                                    $clientAssigneeName = 'NA';
                                }
                                ?>
								<select data-valid="" class="form-control select2 selecttemplate" name="template" data-clientid="{{@$fetchedData->id}}" data-clientfirstname="{{@$fetchedData->first_name}}" data-clientvisaExpiry="{{@$fetchedData->visaExpiry}}" data-clientreference_number="{{@$fetchedData->client_id}}" data-clientassignee_name="{{@$clientAssigneeName}}">
									<option value="">Select</option>
									@foreach( \App\Models\CrmEmailTemplate::orderBy('id', 'desc')->get() as $list)
										<option value="{{$list->id}}">{{$list->name}}</option>
									@endforeach
								</select>
                            </div>
						</div>

                        <!-- Inline ChatGPT Section (hidden by default) -->
                        <div id="chatGptSection" class="collapse mt-3 col-9 col-md-9 col-lg-9">
                            <div class="card card-body">
                                <div class="form-group">
                                    <label for="chatGptInput" style="color: #FFF;">Enter your message to enhance:</label>
                                    <textarea class="form-control" id="chatGptInput" rows="5" placeholder="Type your message here..."></textarea>
                                </div>
                                <div class="mt-2 text-end">
                                    <button type="button" class="btn btn-primary" id="enhanceMessageBtn" style="margin:0px !important;">Enhance</button>
                                    <button type="button" class="btn btn-secondary" id="chatGptClose">Close</button>
                                </div>
                            </div>
                        </div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span>
                                    <button type="button" class="btn btn-info" id="chatGptToggle">ChatGPT Enhance</button>
                                </label>
								<input type="text" name="subject" id="compose_email_subject" class="form-control selectedsubject" data-valid="required" autocomplete="off" placeholder="Enter Subject" value="" />
								@if ($errors->has('subject'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('subject') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" id="compose_email_message" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						     <div class="form-group">
						        <label>Attachment</label>
						        <input type="file" name="attach[]" class="form-control" multiple>
						     </div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						    <div class="table-responsive uploadchecklists">
							<table id="mychecklist-datatable" class="table text_wrap table-2">
							    <thead>
							        <tr>
							            <th></th>
							            <th>File Name</th>
							            <th>File</th>
							        </tr>
							    </thead>
							    <tbody>
							        @foreach(\App\Models\UploadChecklist::all() as $uclist)
							        <tr>
							            <td><input type="checkbox" name="checklistfile[]" value="<?php echo $uclist->id; ?>"></td>
							            <td><?php echo $uclist->name; ?></td>
							             <td><a target="_blank" href="<?php echo URL::to('/checklists/'.$uclist->file); ?>"><?php echo $uclist->name; ?></a></td>
							        </tr>
							        @endforeach
							    </tbody>
							</table>
						</div>
							</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Send Message-->
<div id="sendmsgmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="messageModalLabel">Send Message</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmsg" id="sendmsg" action="{{URL::to('/admin/sendmsg')}}" autocomplete="off" enctype="multipart/form-data">
				    @csrf
                    <input type="hidden" name="client_id" id="sendmsg_client_id" value="">
                    <input type="hidden" name="vtype" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" name="message" data-valid="required"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmsg')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<div class="modal fade  custom_modal" id="interest_service_view" tabindex="-1" role="dialog" aria-labelledby="interest_serviceModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content showinterestedservice">

		</div>
	</div>
</div>

<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmNotUseDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this document in Not Use Tab?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmBackToDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this in related document Tab again?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to verify this doc?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Verify</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>


<div id="confirmLogModal" tabindex="-1" role="dialog" aria-labelledby="confirmLogModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this log?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmEducationModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accepteducation">Delete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmcompleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to complete the Application?</h4>
				<button  data-id="" type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptapplication">Complete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmpublishdocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Publish Document?</h4>
				<h5 class="">Publishing documents will allow client to access from client portal , Are you sure you want to continue ?</h5>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptpublishdoc">Publish Anyway</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="application_opensaleforcast" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/saleforcast')}}" name="saleforcast" id="saleforcast" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcast')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="application_ownership" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Application Ownership Ratio</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/application_ownership')}}" name="xapplication_ownership" id="xapplication_ownership" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="mapp_id" id="mapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sus_agent"> </label>
								<input type="number" max="100" min="0" step="0.01" class="form-control ration" name="ratio">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('xapplication_ownership')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="superagent_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Select Super Agent</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/spagent_application')}}" name="spagent_application" id="spagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="siapp_id" id="siapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Super Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control super_agent" id="super_agent" name="super_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\AgentDetails::whereRaw('FIND_IN_SET("Super Agent", agent_type)')->get(); ?>
									@foreach($sagents as $sa)
										<option value="{{$sa->id}}">{{$sa->full_name}} {{$sa->email}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('spagent_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="subagent_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Select Sub Agent</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/sbagent_application')}}" name="sbagent_application" id="sbagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="sbapp_id" id="sbapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sub_agent">Sub Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control sub_agent" id="sub_agent" name="sub_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\AgentDetails::whereRaw('FIND_IN_SET("Sub Agent", agent_type)')->where('is_acrchived',0)->get(); ?>
									@foreach($sagents as $sa)
										<option value="{{$sa->id}}">{{$sa->full_name}} {{$sa->email}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sbagent_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="tags_clients" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Tags</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/save_tag')}}" name="stags_application" id="stags_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="client_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Tags <span class="span_req">*</span></label>
								<select data-valid="required" multiple class="tagsselec form-control super_tag" id="tag" name="tag[]">
								<?php $r = array();
								if($fetchedData->tagname != ''){
									$r = explode(',', $fetchedData->tagname);
								}
								?>
									<option value="">Please Select</option>
									<?php $stagd = \App\Models\Tag::where('id','!=','')->get(); ?>
									@foreach($stagd as $sa)
										<option <?php if(in_array($sa->id, $r)){ echo 'selected'; } ?> value="{{$sa->id}}">{{$sa->name}}</option>
									@endforeach
								</select>

							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('stags_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="new_fee_option" tabindex="-1" role="dialog" aria-labelledby="feeoptionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="feeoptionModalLabel">Fee Option</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showproductfee">

			</div>
		</div>
	</div>
</div>


<div class="modal fade custom_modal" id="new_fee_option_serv" tabindex="-1" role="dialog" aria-labelledby="feeoptionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="feeoptionModalLabel">Fee Option</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showproductfeeserv">

			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="application_opensaleforcast" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/saleforcast')}}" name="saleforcast" id="saleforcast" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcast')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="application_opensaleforcastservice" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="close closeservmodal" >
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/application/saleforcastservice')}}" name="saleforcastservice" id="saleforcastservice" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcastservice')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div class="modal fade custom_modal" id="serviceTaken" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Service Taken</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form method="post" action="{{URL::to('/admin/client/createservicetaken')}}" name="createservicetaken" id="createservicetaken" autocomplete="off" enctype="multipart/form-data">
				@csrf
                    <input id="logged_client_id" name="logged_client_id"  type="hidden" value="<?php echo $fetchedData->id;?>">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">

							<div class="form-group">
								<label style="display:block;" for="service_type">Select Service Type:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Migration_inv" value="Migration" name="service_type" checked>
									<label class="form-check-label" for="Migration_inv">Migration</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Eductaion_inv" value="Eductaion" name="service_type">
									<label class="form-check-label" for="Eductaion_inv">Eductaion</label>
								</div>
								<span class="custom-error service_type_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12 is_Migration_inv">
                            <div class="form-group">
								<label for="mig_ref_no">Reference No: <span class="span_req">*</span></label>
                                <input type="text" name="mig_ref_no" id="mig_ref_no" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_service">Service: <span class="span_req">*</span></label>
                                <input type="text" name="mig_service" id="mig_service" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="mig_notes" id="mig_notes" value="" class="form-control" data-valid="required">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 is_Eductaion_inv" style="display:none;">
                            <div class="form-group">
								<label for="edu_course">Course: <span class="span_req">*</span></label>
                                <input type="text" name="edu_course" id="edu_course" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_college">College: <span class="span_req">*</span></label>
                                <input type="text" name="edu_college" id="edu_college" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_service_start_date">Service Start Date: <span class="span_req">*</span></label>
                                <input type="text" name="edu_service_start_date" id="edu_service_start_date" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="edu_notes" id="edu_notes" value="" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('createservicetaken')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="rating-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ratingModalLabel">Client Rating</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           <div class="modal-body">
                <div class="rating-section">
                    <h3 class="rating-title">Education Client Rating</h3>
                    <div class="star-rating">
                        <input type="radio" id="education-star-5" name="education_rating" value="5">
                        <label for="education-star-5" title="5 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="education-star-4" name="education_rating" value="4">
                        <label for="education-star-4" title="4 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="education-star-3" name="education_rating" value="3">
                        <label for="education-star-3" title="3 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="education-star-2" name="education_rating" value="2">
                        <label for="education-star-2" title="2 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="education-star-1" name="education_rating" value="1">
                        <label for="education-star-1" title="1 star"><i class="fas fa-star"></i></label>
                    </div>
                </div>
                <div class="rating-section">
                    <h3 class="rating-title">Migration Client Rating</h3>
                    <div class="star-rating">
                        <input type="radio" id="migration-star-5" name="migration_rating" value="5">
                        <label for="migration-star-5" title="5 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="migration-star-4" name="migration_rating" value="4">
                        <label for="migration-star-4" title="4 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="migration-star-3" name="migration_rating" value="3">
                        <label for="migration-star-3" title="3 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="migration-star-2" name="migration_rating" value="2">
                        <label for="migration-star-2" title="2 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="migration-star-1" name="migration_rating" value="1">
                        <label for="migration-star-1" title="1 star"><i class="fas fa-star"></i></label>
                    </div>
                </div>
            </div>

            <style>
            .rating-section {
                margin-bottom: 20px;
            }

            .rating-title {
                font-size: 1.2em;
                font-weight: 600;
                margin-bottom: 10px;
                color: #333;
                text-align: center;
            }

            .star-rating {
                display: flex;
                justify-content: center;
                margin: 0;
                padding: 0;
            }

            .star-rating input {
                display: none;
            }

            .star-rating label {
                cursor: pointer;
                color: #ccc;
                font-size: 2em;
                transition: color 0.3s ease;
                margin: 0 2px;
            }

            .star-rating input:checked ~ label {
                color: #f39c12;
            }

            .star-rating label:hover,
            .star-rating label:hover ~ label {
                color: #f39c12;
            }
            </style>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="submit-rating" type="button" class="btn btn-primary">Submit Rating</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inbox_reassignemail_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title">Re-assign Inbox Email</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<form method="POST" action="{{ url('/admin/reassiginboxemail') }}" name="inbox-email-reassign-to-client-matter" autocomplete="off" enctype="multipart/form-data" id="inbox-email-reassign-to-client-matter">
			@csrf
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12">
						<input id="memail_id" name="memail_id" type="hidden" value="">
                        <input id="mail_type" name="mail_type" type="hidden" value="inbox">
                        <input id="user_mail" name="user_mail" type="hidden" value="">
                        <input id="uploaded_doc_id" name="uploaded_doc_id" type="hidden" value="">
						<select id="reassign_client_id" name="reassign_client_id" class="form-control select2" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" data-valid="required">
							<option value="">Select Client</option>
							@foreach(\App\Models\Admin::Where('role','7')->Where('type','client')->get() as $ulist)
							<option value="{{@$ulist->id}}">{{@$ulist->first_name}} {{@$ulist->last_name}}({{@$ulist->client_id}})</option>
							@endforeach
						</select>
					</div>
				</div>

                <div class="form-group row">
					<div class="col-sm-12">
						<select id="reassign_client_matter_id" name="reassign_client_matter_id" class="form-control select2 " style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" disabled>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="customValidate('inbox-email-reassign-to-client-matter')">
					<i class="fa fa-save"></i> Re-assign Inbox Email
				</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="sent_reassignemail_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title">Re-assign Sent Email</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<form method="POST" action="{{ url('/admin/reassigsentemail') }}" name="sent-email-reassign-to-client-matter" autocomplete="off" enctype="multipart/form-data" id="sent-email-reassign-to-client-matter">
			@csrf
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12">
						<input id="memail_id" name="memail_id" type="hidden" value="">
                        <input id="mail_type" name="mail_type" type="hidden" value="sent">
                        <input id="user_mail" name="user_mail" type="hidden" value="">
                        <input id="uploaded_doc_id" name="uploaded_doc_id" type="hidden" value="">
						<select id="reassign_sent_client_id" name="reassign_sent_client_id" class="form-control select2" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" data-valid="required">
							<option value="">Select Client</option>
							@foreach(\App\Models\Admin::Where('role','7')->Where('type','client')->get() as $ulist)
							<option value="{{@$ulist->id}}">{{@$ulist->first_name}} {{@$ulist->last_name}}({{@$ulist->client_id}})</option>
							@endforeach
						</select>
					</div>
				</div>

                <div class="form-group row">
					<div class="col-sm-12">
						<select id="reassign_sent_client_matter_id" name="reassign_sent_client_matter_id" class="form-control select2 " style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" disabled>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="customValidate('sent-email-reassign-to-client-matter')">
					<i class="fa fa-save"></i> Re-assign Sent Email
				</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="sent_mail_preview_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title" id="memail_subject"></h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12" id="memail_message">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>





<style>
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .full-width { width: 100%; }

    .relationship-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
</style>

@endsection
@section('scripts')
<script src="{{URL::to('/')}}/js/popover.js"></script>
<script src="{{URL::asset('js/bootstrap-datepicker.js')}}"></script>

<!-- JavaScript to switch forms -->
<script>
    $(document).ready(function() {

        function adjustActivityFeedHeight() {
            let mainContentHeight = $('.main-content').outerHeight();
            let activityFeedHeight = $('.activity-feed').outerHeight();
            console.log('mainContentHeight='+mainContentHeight);
            console.log('activityFeedHeight='+activityFeedHeight);
            let windowHeight = $(window).height();

            if (mainContentHeight <= activityFeedHeight) {
                // Case 1: main-content smaller → activity-feed should use viewport height
                $('.activity-feed').css('max-height', `calc(100vh)`);
            } else {
                // Case 2: main-content bigger → activity-feed should match main-content height
                $('.activity-feed').css('max-height', mainContentHeight + 'px');
            }
        }

        // Run on load
        adjustActivityFeedHeight();

        // Run on resize (for responsiveness)
        $(window).on('resize', function () {
            adjustActivityFeedHeight();
        });

        //Update Personal Doucment Category
        $('.update-personal-cat-title').on('click', function() {
            var id = $(this).data('id');
            var newTitle = prompt('Enter new title for the category:');
            if (newTitle) {
                $.ajax({
                    url: '/admin/update-personal-doc-category',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                        title: newTitle
                    },
                    success: function(response) {
                        if (response.status) {
                            alert(response.message);
                            location.reload();
                        } else {
                            //alert('Failed to update title.');
                            alert(response.message);
                        }
                    }
                });
            }
        });

        /*$('.delete-personal-cat-title').on('click', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this category?')) {
                $.ajax({
                    url: '/delete-personal-doc-category',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete category.');
                        }
                    }
                });
            }
        });*/

        //Update Visa Doucment Category
        $('.update-visa-cat-title').on('click', function() {
            var id = $(this).data('id');
            var newTitle = prompt('Enter new title for the category:');
            if (newTitle) {
                $.ajax({
                    url: '/admin/update-visa-doc-category',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                        title: newTitle
                    },
                    success: function(response) {
                        if (response.status) {
                            alert(response.message);
                            location.reload();
                        } else {
                            //alert('Failed to update title.');
                            alert(response.message);
                        }
                    }
                });
            }
        });

        //Save reference click
        $(document).delegate('.saveReferenceValue', 'click', function() {
            let department_reference = $('#department_reference').val();
            let other_reference = $('#other_reference').val();
            let client_id = '<?php echo $fetchedData->id;?>';
            let selectedMatter = $('#sel_matter_id_client_detail').val();
            if(department_reference == '' || other_reference == ''){
                alert('Please enter department reference, other refence value');
            } else {
                $.ajax({
                    url: '{{ route('references.store') }}', // Laravel route name
                    type: 'POST',
                    data: {
                        department_reference: department_reference,
                        other_reference: other_reference,
                        client_id: client_id,
                        client_matter_id:selectedMatter,
                        _token: '{{ csrf_token() }}' // Always include CSRF token
                    },
                    success: function (response) {
                        alert('Saved successfully');
                        location.reload(); //Page reload after success
                    },
                    error: function (xhr) {
                        alert('Error saving data');
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        function load_visa_expiry_messages(client_id,view = '') {
            var playing = false;
            $.ajax({
                url:"{{URL::to('/admin/fetch-visa_expiry_messages')}}",
                method:"GET",
                data: { client_id:client_id},
                success:function(data) {
                    if(data != 0){
                        iziToast.show({
                            backgroundColor: 'rgba(0,0,255,0.3)',
                            messageColor: 'rgba(255,255,255)',
                            title: '',
                            message: data,
                            position: 'bottomRight'
                        });
                        $(this).toggleClass("down");

                        if (playing == false) {
                            document.getElementById('player').play();
                            playing = true;
                            $(this).text("stop sound");

                        } else {
                            document.getElementById('player').pause();
                            playing = false;
                            $(this).text("restart sound");
                        }
                    }
                }
            });
        }

        setInterval(function(){
            var client_id = '<?php echo $fetchedData->id;?>';
            load_visa_expiry_messages(client_id);
        },900000 ); //15 min interval


       // On page load, check if the URL contains a matter ID and set the dropdown/checkbox state
        var currentUrl = window.location.href;
        var urlSegments = currentUrl.split('/');
        var matterIdInUrl = urlSegments.length > 7 ? urlSegments[urlSegments.length - 1] : null;

        if (matterIdInUrl === null) {
            // Case 1: No matter ID in URL
            // a) First check the dropdown and select the first option with value != ''
            let firstNonEmptyOption = $('#sel_matter_id_client_detail option').filter(function() {
                return $(this).val() !== '';
            }).first();

            if (firstNonEmptyOption.length) {
                // If a non-empty option exists in the dropdown, select it
                $('#sel_matter_id_client_detail').val(firstNonEmptyOption.val()).trigger('change');
                selectedMatter = firstNonEmptyOption.val();
                console.log('No matter ID in URL, selected first non-empty dropdown option:', selectedMatter);
            } else {
                // b) If no non-empty options in dropdown, check the checkbox
                let firstCheckbox = $('.general_matter_checkbox_client_detail').first();
                if (firstCheckbox.length) {
                    // If a checkbox exists, check it
                    firstCheckbox.prop('checked', true).trigger('change');
                    selectedMatter = firstCheckbox.val();
                    console.log('No matter ID in URL, no dropdown options, checked first checkbox:', selectedMatter);
                } else {
                    // c) If no dropdown options and no checkbox, do nothing
                    selectedMatter = '';
                    console.log('No matter ID in URL, no dropdown options or checkboxes found');
                }
            }
        }
        else {
            // Case 2: Matter ID exists in URL
            let matchFound = false;

            // a) First check the dropdown for a matching option
            $('#sel_matter_id_client_detail option').each(function() {
                var uniqueMatterNo = $(this).data('clientuniquematterno');
                if (uniqueMatterNo === matterIdInUrl) {
                    $('#sel_matter_id_client_detail').val($(this).val()).trigger('change');
                    selectedMatter = $(this).val();
                    matchFound = true;
                    console.log('Matter ID found in URL, selected matching dropdown option:', selectedMatter);
                }
            });

            // If no matching option in dropdown, proceed with further checks
            if (!matchFound) {
                // b) Check for a matching checkbox
                let checkboxMatchFound = false;
                $('.general_matter_checkbox_client_detail').each(function() {
                    var uniqueMatterNo = $(this).data('clientuniquematterno');
                    if (uniqueMatterNo === matterIdInUrl) {
                        $(this).prop('checked', true).trigger('change');
                        selectedMatter = $(this).val();
                        checkboxMatchFound = true;
                        console.log('Matter ID in URL, checked matching checkbox:', selectedMatter);
                        return false; // Exit the loop once a match is found
                    }
                });

                // If no matching checkbox, check the first non-empty dropdown option
                if (!checkboxMatchFound) {
                    let firstNonEmptyOption = $('#sel_matter_id_client_detail option').filter(function() {
                        return $(this).val() !== '';
                    }).first();

                    if (firstNonEmptyOption.length) {
                        // If a non-empty option exists in the dropdown, select it
                        $('#sel_matter_id_client_detail').val(firstNonEmptyOption.val()).trigger('change');
                        selectedMatter = firstNonEmptyOption.val();
                        console.log('Matter ID in URL, no checkbox match, selected first non-empty dropdown option:', selectedMatter);
                    } else {
                        // If no non-empty dropdown options, check the first checkbox
                        let firstCheckbox = $('.general_matter_checkbox_client_detail').first();
                        if (firstCheckbox.length) {
                            firstCheckbox.prop('checked', true).trigger('change');
                            selectedMatter = firstCheckbox.val();
                            console.log('Matter ID in URL, no dropdown match, checked first checkbox:', selectedMatter);
                        } else {
                            selectedMatter = '';
                            console.log('Matter ID in URL, no matches in dropdown or checkboxes');
                        }
                    }
                }
            }
        }

        // When Matter AI tab is clicked
        $(document).delegate('.tab-button[data-tab="artificialintelligences"]', 'click', function() { //alert('click');
            // Show loading states
            $('.loading-data').show();
            $('.ai-ready-message').hide();
            $('.chat-content').hide();
            $('.loading-history').show();
            $('.chat-history-content').hide();

            // Fetch client data, notes, and personal details
            $.ajax({
                url: '{{URL::to('/admin/load-matter-ai-data')}}',
                type: 'POST',
                data: {
                    client_id: '{{$fetchedData->id}}',
                    client_unique_matter_no: '{{$id1 ?? ($matter_info_arr->client_unique_matter_no ?? null)}}',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status) {
                        // Update loading states
                        $('#client-data-loading').text('Client data loaded.');
                        $('#client-notes-loading').text('Client notes loaded.');
                        $('#client-details-loading').text('Client personal details loaded.');

                        // Show AI ready message after a short delay
                        setTimeout(function() {
                            $('.loading-data').hide();
                            $('.ai-ready-message').show();
                            $('.chat-content').show();
                        }, 1000);

                        // Load chat history
                        loadChatHistory();
                    } else {
                        $('.loading-data').html('<p class="text-danger">Error loading data. Please try again.</p>');
                    }
                },
                error: function() {
                    $('.loading-data').html('<p class="text-danger">Error loading data. Please try again.</p>');
                }
            });
        });

        // Load chat history
        function loadChatHistory() {
            $.ajax({
                url: '{{URL::to('/admin/get-chat-history')}}',
                type: 'POST',
                data: {
                    client_id: '{{$fetchedData->id}}',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    $('.loading-history').hide();
                    $('.chat-history-content').show();

                    if (data.status && data.chats.length > 0) {
                        var historyHtml = '';
                        $.each(data.chats, function(index, chat) {
                            historyHtml += `
                                <div class="chat-history-item" data-chat-id="${chat.id}">
                                    <h5>${chat.title}</h5>
                                    <p>${chat.created_at}</p>
                                </div>
                            `;
                        });
                        $('.chat-history-content').html(historyHtml);
                    } else {
                        $('.chat-history-content').html('<p>No chat history available.</p>');
                    }
                }
            });
        }

        // New Chat Button
        $(document).delegate('.new-chat-btn', 'click', function() {
            $('.chat-messages').html('');
            $('.chat-content').show();
            $('.chat-history-item').removeClass('active');
            $('#chat-input').val('');
        });

        // Load chat messages when a chat history item is clicked
        $(document).delegate('.chat-history-item', 'click', function() {
            var chatId = $(this).data('chat-id');
            $('.chat-history-item').removeClass('active');
            $(this).addClass('active');

            $.ajax({
                url: '{{URL::to('/admin/get-chat-messages')}}',
                type: 'POST',
                data: {
                    chat_id: chatId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status) {
                        var messagesHtml = '';
                        $.each(data.messages, function(index, message) {
                            var messageClass = message.sender === 'user' ? 'user' : 'ai';
                            messagesHtml += `
                                <div class="message ${messageClass}">
                                    <div class="message-content">${message.message}</div>
                                </div>
                            `;
                        });
                        $('.chat-messages').html(messagesHtml);
                        $('.chat-messages').scrollTop($('.chat-messages')[0].scrollHeight);
                    }
                }
            });
        });

        // Send chat message
        $(document).delegate('.send-chat-btn', 'click', function() {
            var message = $('#chat-input').val().trim();
            if (message === '') return;

            // Append user message to chat
            $('.chat-messages').append(`
                <div class="message user">
                    <div class="message-content">${message}</div>
                </div>
            `);
            $('.chat-messages').scrollTop($('.chat-messages')[0].scrollHeight);
            $('#chat-input').val('');

            // Send message to server
            $.ajax({
                url: '{{URL::to('/admin/send-ai-message')}}',
                type: 'POST',
                data: {
                    client_id: '{{$fetchedData->id}}',
                    message: message,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status) {
                        // Append AI response
                        $('.chat-messages').append(`
                            <div class="message ai">
                                <div class="message-content">${data.response}</div>
                            </div>
                        `);
                        $('.chat-messages').scrollTop($('.chat-messages')[0].scrollHeight);

                        // Reload chat history
                        loadChatHistory();
                    } else {
                        $('.chat-messages').append(`
                            <div class="message ai">
                                <div class="message-content text-danger">Error: ${data.message}</div>
                            </div>
                        `);
                    }
                }
            });
        });


    });

    document.addEventListener('DOMContentLoaded', function () {
    // Handle tab switching
    const tabs = document.querySelectorAll('.nav-tabs .nav-link');
    const tabContents = document.querySelectorAll('.tab-content .tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const target = this.getAttribute('href').substring(1);

            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('show', 'active'));

            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(target).classList.add('show', 'active');
        });
    });

    // Activate the first tab by default if not already set
    if (tabs.length > 0 && !document.querySelector('.nav-tabs .nav-link.active')) {
        tabs[0].classList.add('active');
        tabContents[0].classList.add('show', 'active');
    }
});

    //For download document - jQuery version for better compatibility
    $(document).ready(function() {
        $(document).on('click', '.download-file', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('Download file clicked:', this);
            console.log('Element data:', $(this).data());

            const $this = $(this);
            const filelink = $this.data('filelink');
            const filename = $this.data('filename');

            console.log('File link:', filelink);
            console.log('File name:', filename);

            if (!filelink || !filename) {
                console.error('Missing file info - filelink:', filelink, 'filename:', filename);
                alert('Missing file info. Please try again.');
                return false;
            }

            // Show loading indicator
            $this.html('<i class="fas fa-spinner fa-spin"></i> Downloading...');
            $this.prop('disabled', true);

            // Create and submit a hidden form
            const form = $('<form>', {
                method: 'POST',
                action: '{{ url("/admin/download-document") }}',
                target: '_blank',
                style: 'display: none;'
            });

            // CSRF token
            const token = $('meta[name="csrf-token"]').attr('content');
            if (!token) {
                console.error('CSRF token not found');
                alert('Security token not found. Please refresh the page and try again.');
                $this.html('Download').prop('disabled', false);
                return false;
            }

            // Add form fields
            form.append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: token
            }));

            form.append($('<input>', {
                type: 'hidden',
                name: 'filelink',
                value: filelink
            }));

            form.append($('<input>', {
                type: 'hidden',
                name: 'filename',
                value: filename
            }));

            console.log('Submitting download form for:', filename);
            console.log('Form action:', form.attr('action'));

            // Append form to body and submit
            $('body').append(form);
            
            try {
                form[0].submit();
                console.log('Download form submitted successfully');
                
                // Reset button after a short delay
                setTimeout(function() {
                    $this.html('Download').prop('disabled', false);
                }, 2000);
                
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Error initiating download. Please try again.');
                $this.html('Download').prop('disabled', false);
            }

            // Remove form after submission
            setTimeout(function() {
                form.remove();
            }, 1000);

            return false;
        });
    });

    // Alternative vanilla JavaScript version as backup
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (e) {
            const target = e.target.closest('a.download-file');
            if (!target) return;

            e.preventDefault();
            e.stopPropagation();

            console.log('Vanilla JS download handler triggered');
            console.log('Download file clicked:', target);

            const filelink = target.dataset.filelink;
            const filename = target.dataset.filename;

            console.log('File link:', filelink);
            console.log('File name:', filename);

            if (!filelink || !filename) {
                console.error('Missing file info - filelink:', filelink, 'filename:', filename);
                alert('Missing file info. Please try again.');
                return;
            }

            // Show loading state
            const originalText = target.innerHTML;
            target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
            target.style.pointerEvents = 'none';

            // Create and submit a hidden form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("/admin/download-document") }}';
            form.target = '_blank';
            form.style.display = 'none';

            // CSRF token
            const tokenElement = document.querySelector('meta[name="csrf-token"]');
            if (!tokenElement) {
                console.error('CSRF token not found');
                alert('Security token not found. Please refresh the page and try again.');
                target.innerHTML = originalText;
                target.style.pointerEvents = 'auto';
                return;
            }
            
            const token = tokenElement.getAttribute('content');
            
            // Add form fields
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = token;
            form.appendChild(tokenInput);

            const filelinkInput = document.createElement('input');
            filelinkInput.type = 'hidden';
            filelinkInput.name = 'filelink';
            filelinkInput.value = filelink;
            form.appendChild(filelinkInput);

            const filenameInput = document.createElement('input');
            filenameInput.type = 'hidden';
            filenameInput.name = 'filename';
            filenameInput.value = filename;
            form.appendChild(filenameInput);

            console.log('Submitting download form for:', filename);
            console.log('Form action:', form.action);

            // Append form to body and submit
            document.body.appendChild(form);
            
            try {
                form.submit();
                console.log('Download form submitted successfully');
                
                // Reset button after a short delay
                setTimeout(function() {
                    target.innerHTML = originalText;
                    target.style.pointerEvents = 'auto';
                }, 2000);
                
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Error initiating download. Please try again.');
                target.innerHTML = originalText;
                target.style.pointerEvents = 'auto';
            }

            // Remove form after submission
            setTimeout(function() {
                if (form.parentNode) {
                    form.parentNode.removeChild(form);
                }
            }, 1000);
        });
    });


    //JavaScript to Show File Selection Hint
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger file input click when "Add Document" button is clicked
        document.querySelector('.add-document-btn').addEventListener('click', function() {
            document.querySelector('.docclientreceiptupload').click();
        });

        // Show file selection hint when files are selected
        document.querySelector('.docclientreceiptupload').addEventListener('change', function(e) {
            const files = e.target.files;
            const hintElement = document.querySelector('.file-selection-hint');

            if (files.length > 0) {
                if (files.length === 1) {
                    // Show the file name if only one file is selected
                    hintElement.textContent = `${files[0].name} selected`;
                } else {
                    // Show the number of files if multiple files are selected
                    hintElement.textContent = `${files.length} Files selected`;
                }
            } else {
                // Clear the hint if no files are selected
                hintElement.textContent = '';
            }
        });


        // Trigger file input click when "Add Document" button is clicked
        document.querySelector('.add-document-btn1').addEventListener('click', function() {
            document.querySelector('.docofficereceiptupload').click();
        });

        // Show file selection hint when files are selected
        document.querySelector('.docofficereceiptupload').addEventListener('change', function(e) {
            const files = e.target.files;
            const hintElement1 = document.querySelector('.file-selection-hint1');

            if (files.length > 0) {
                if (files.length === 1) {
                    // Show the file name if only one file is selected
                    hintElement1.textContent = `${files[0].name} selected`;
                } else {
                    // Show the number of files if multiple files are selected
                    hintElement1.textContent = `${files.length} Files selected`;
                }
            } else {
                // Clear the hint if no files are selected
                hintElement1.textContent = '';
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="receipt_type"]');
        const forms = document.querySelectorAll('.form-type');

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                forms.forEach(form => form.style.display = 'none');
                const selected = this.value; //alert(selected);
                document.getElementById(selected + '_form').style.display = 'block';
                //var selectedMatter = $('#sel_matter_id_client_detail').val();
                let selectedMatter;
                if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                    // If checkbox is checked, get its value
                    selectedMatter = $('.general_matter_checkbox_client_detail').val();
                    console.log('Checkbox is checked, selected value:', selectedMatter);
                } else {
                    // If checkbox is not checked, get the value from the dropdown
                    selectedMatter = $('#sel_matter_id_client_detail').val();
                    console.log('Checkbox is not checked, selected dropdown value:', selectedMatter);
                }
                console.log('selectedMatter==='+selectedMatter);
                if(selected == 'office_receipt'){
                    listOfInvoice();
                    $('#client_matter_id_office').val(selectedMatter);
                }
                else if(selected == 'invoice_receipt'){
                    //alert('function_type=='+ $('#function_type').val() )
                    if($('#function_type').val() == '' || $('#function_type').val() == 'add' ) {
                        $('#function_type').val("add");
                        getTopInvoiceNoFromDB(3);
                    }
                    $('#client_matter_id_invoice').val(selectedMatter);
                }
                else if(selected == 'client_receipt'){
                    listOfInvoice();
                    clientLedgerBalanceAmount(selectedMatter);
                    $('#client_matter_id_ledger').val(selectedMatter);
                }
            });
        });
    });

    function getTopInvoiceNoFromDB(type) {
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/getTopInvoiceNoFromDB')}}',
            sync:true,
            data: {type:type},
            success: function(response){
                var obj = $.parseJSON(response); console.log('record_count=='+obj.record_count);
                $('.invoice_no').val(obj.max_receipt_id);
                $('.unique_invoice_no').text(obj.max_receipt_id);
            }
        });
    }

    function getTopReceiptValInDB(type) {
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/getTopReceiptValInDB')}}',
            sync:true,
            data: {type:type},
            success: function(response){
                var obj = $.parseJSON(response); console.log('record_count=='+obj.record_count);
                if(obj.receipt_type == 1){ //client receipt
                    if(obj.record_count >0){
                        $('#top_value_db').val(obj.record_count);
                    } else {
                        $('#top_value_db').val(obj.record_count);
                    }
                }

                if(obj.receipt_type == 2){ //office receipt
                    if(obj.record_count >0){
                        $('#office_top_value_db').val(obj.record_count);
                    } else {
                        $('#office_top_value_db').val(obj.record_count);
                    }
                }

                if(obj.receipt_type == 4){ //journal receipt
                    if(obj.record_count >0){
                        $('#journal_top_value_db').val(obj.record_count);
                    } else {
                        $('#journal_top_value_db').val(obj.record_count);
                    }
                }

                if(obj.receipt_type == 3){ //invoice receipt
                    if(obj.record_count >0){
                        $('#invoice_top_value_db').val(obj.record_count);
                    } else {
                        $('#invoice_top_value_db').val(obj.record_count);
                    }

                    if(obj.max_receipt_id >0){
                        var max_receipt_id = obj.max_receipt_id +1;
                        max_receipt_id = "Inv000"+max_receipt_id;
                        $('.unique_invoice_no').text(max_receipt_id);
                        $('.invoice_no').val(max_receipt_id);
                    } else {
                        var max_receipt_id = obj.max_receipt_id +1;
                        max_receipt_id = "Inv000"+max_receipt_id;
                        $('.unique_invoice_no').text(max_receipt_id);
                        $('.invoice_no').val(max_receipt_id);
                    }
                }
            }
        });
    }

    //List of invoice values for drop down
    function listOfInvoice() {
        var client_id = '<?php echo $fetchedData->id;?>';
        let selectedMatter;
        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
            selectedMatter = $('.general_matter_checkbox_client_detail').val();
        } else {
            selectedMatter = $('#sel_matter_id_client_detail').val();
        }
        console.log('client_id=='+client_id);
        console.log('selectedMatter=='+selectedMatter);
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/listOfInvoice')}}',
            sync:true,
            data: { client_id:client_id, selectedMatter:selectedMatter},
            success: function(response){
                var obj = $.parseJSON(response); console.log('record_get=='+obj.record_get);
                $('.invoice_no_cls').html(obj.record_get);
            }
        });
    }

    function clientLedgerBalanceAmount(selectedMatter) {
        var client_id = '<?php echo $fetchedData->id;?>';
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/clientLedgerBalanceAmount')}}',
            sync:true,
            data: { client_id:client_id , selectedMatter:selectedMatter },
            success: function(response){
                var obj = $.parseJSON(response); console.log('record_get=='+obj.record_get);
                $('#client_ledger_balance_amount').val(obj.record_get);
            }
        });
    }
</script>
<script>
    function downloadFile(url, fileName) {
        // Create a temporary anchor element
        const link = document.createElement('a');
        link.href = url;
        link.download = fileName; // Set the desired file name
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link); // Clean up
    }
$(document).ready(function() {
    //Get Client Australian PR Points Details
     $(document).delegate('.get_client_details', 'click', function(){
        $('.popuploader').show();
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/get_client_au_pr_point_details')}}',
            data: {client_id:'<?php echo $fetchedData->id;?>'},
            success: function(response){
                var obj = $.parseJSON(response);
                var details = obj.final_arr;

                var formatted =
                    '*Age:* ' + details.clientAge + ' \n' +
                    '*Test:* ' + details.test_info + ' \n' +
                    '*Experience :* ' + details.exp_info + ' \n' +
                    '*Qualification:* ' + details.qualification_info + ' \n';
                $('#pr_details_info').val(formatted);
                $('.popuploader').hide();
            }
        });
    });

     //CalculatePoints PR Points Details
     $(document).delegate('.CalculatePoints', 'click', function(){
        var pr_details_info =  $('#pr_details_info').val();
        $('.popuploader').show();
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/CalculatePoints')}}',
            data: {pr_details_info: pr_details_info},
            success: function(response){
                var obj = $.parseJSON(response); //alert(obj.result_arr);
                if (obj.status && obj.result_arr && obj.result_arr.result) {
                    $('.pr_details_result_div').show();
                    $('.pr_details_result_whole').html(obj.result_arr.result.replace(/\n/g, '<br>'));
                } else {
                    $('.pr_details_result_div').show();
                    $('.pr_details_result_whole').html('<p>Unable to calculate points. Please check your input.</p>');
                }
                $('.popuploader').hide();
            },
            error: function(xhr) {
                $('.pr_details_result_div').show();
                $('.pr_details_result_whole').html('<p>An error occurred while processing your request.</p>');
                $('.popuploader').hide();
            }
        });
    });

     //PR points add to notes
     $(document).delegate('.prpoints_add_to_notes', 'click', function(){
        $('.popuploader').show();
        var pr_details_info =  $('.pr_details_result_whole').html();  //alert(pr_details_info);
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/prpoints_add_to_notes')}}',
            data: {client_id:'<?php echo $fetchedData->id;?>',pr_details_info:pr_details_info},
            success: function(response){
                var obj = $.parseJSON(response); //alert(obj.status);
                if (obj.status) {
                    $('.prpoints_add_to_notes_msg').show();
                    $('.prpoints_add_to_notes_msg').html(obj.message);
                } else {
                    $('.prpoints_add_to_notes_msg').show();
                    $('.prpoints_add_to_notes_msg').html(obj.message);
                }
                //getallactivities();
                //getallnotes();
                location.reload();
                $('.popuploader').hide();
            },
            error: function(xhr) {
                $('.prpoints_add_to_notes_msg').show();
                $('.prpoints_add_to_notes_msg').html('<p>An error occurred.</p>');
                $('.popuploader').hide();
            }
        });
    });


    //Send message
    $(document).delegate('.sendmsg', 'click', function(){
        $('#sendmsgmodal').modal('show');
        var client_id = $(this).attr('data-id');
        $('#sendmsg_client_id').val(client_id);
    });

    $('.grid_data').hide();
    // Handle main tab switching
    $('.tab-button').click(function() {
        // Remove active class from all buttons and panes
        $('.tab-button').removeClass('active');
        $('.tab-pane').removeClass('active');

        // Add active class to clicked button
        $(this).addClass('active');

        // Show corresponding pane
        const tabId = $(this).data('tab');
        $(`#${tabId}-tab`).addClass('active');
        // Show/hide Activity Feed based on tab
        if (tabId === 'personaldetails') {
            $('#activity-feed').show();
            $('#main-content').css('flex', '1');
        } else {
            if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                var selectedMatter = $('.general_matter_checkbox_client_detail').val();
            } else {
                var selectedMatter = $('#sel_matter_id_client_detail').val();
            }
            if (tabId === 'noteterm') {
                var target = tabId+'-tab';
                if(selectedMatter != "" ) {
                    $('#'+target).find('.note-card-redesign').each(function() {
                        if ($(this).data('matterid') == selectedMatter) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }  else {
                    $('#'+target).find('.note-card-redesign').each(function() {
                        $(this).hide();
                    });
                }
            }

            if( tabId === 'conversations') {
                if(selectedMatter != "" ) {
                    $('#inbox-subtab #email-list').find('.email-card').each(function() {
                        if ($(this).data('matterid') == selectedMatter) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }  else {
                    $(this).hide();
                }
            }
            $('#activity-feed').hide();
            //$('#main-content').css('flex', '0 0 100%');
        }
        // Store the active tab in localStorage when a tab is clicked
        localStorage.setItem('activeTab', tabId);
    });

    // On page load, check localStorage and activate the correct tab
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        // Find the button corresponding to the stored tabId and trigger its click event
        const $targetButton = $(`.tab-button[data-tab="${activeTab}"]`);
        if ($targetButton.length) {
            $targetButton.click(); // Trigger the click event to reuse the existing logic
        }
        // Clear localStorage to prevent persistence on future loads (optional)
        localStorage.removeItem('activeTab');
    }


   // Assuming this is part of the rendering logic for Client Funds Ledger entries
    function renderClientFundsLedger(entries) {
        var trRows = "";
        $.each(entries, function(index, entry) {
            let typeIconMap = {
                'Deposit': 'fa-arrow-down',
                'Fee Transfer': 'fa-arrow-right-from-bracket',
                'Disbursement': 'fa-arrow-right-from-bracket',
                'Refund': 'fa-arrow-right-from-bracket'
            };
            let typeIcon = typeIconMap[entry.client_fund_ledger_type] || 'fa-money-bill';
            let typeClass = entry.client_fund_ledger_type === 'Deposit' ? 'text-success' : 'text-primary';

            let depositAmount = entry.deposit_amount ? '$' + parseFloat(entry.deposit_amount).toFixed(2) : '$0.00';
            let withdrawAmount = entry.withdraw_amount ? '$' + parseFloat(entry.withdraw_amount).toFixed(2) : '$0.00';
            let balanceAmount = entry.balance_amount ? '$' + parseFloat(entry.balance_amount).toFixed(2) : '$0.00';

            // Add pencil icon for non-Fee Transfer entries
            let editIcon = entry.client_fund_ledger_type !== 'Fee Transfer' ?
                `<a href="#" class="edit-ledger-entry" data-id="${entry.id}" data-trans-date="${entry.trans_date}" data-entry-date="${entry.entry_date}" data-type="${entry.client_fund_ledger_type}" data-description="${entry.description}" data-deposit="${entry.deposit_amount}" data-withdraw="${entry.withdraw_amount}"><i class="fas fa-pencil-alt"></i></a>` : '';

            trRows += `
                <tr data-id="${entry.id}">
                    <td>${entry.trans_date} ${editIcon}</td>
                    <td class="type-cell">
                        <i class="fas ${typeIcon} type-icon ${typeClass}"></i>
                        <span>${entry.client_fund_ledger_type}${entry.invoice_no ? '<br/>(' + entry.invoice_no + ')' : ''}</span>
                    </td>
                    <td class="description">${entry.description}</td>
                    <td><a href="#" title="View Receipt ${entry.trans_no}">${entry.trans_no}</a></td>
                    <td class="currency text-success">${depositAmount}</td>
                    <td class="currency text-danger">${withdrawAmount}</td>
                    <td class="currency">${balanceAmount}</td>
                </tr>
            `;
        });

        $('.client-funds-ledger-list').html(trRows);
    }

    // Handle pencil icon click to open modal
    $(document).on('click', '.edit-ledger-entry', function(e) {
        e.preventDefault();
        var $row = $(this).closest('tr');
        var id = $(this).data('id');
        var transDate = $(this).data('trans-date');
        var entryDate = $(this).data('entry-date');
        var type = $(this).data('type');
        var description = $(this).data('description');
        var deposit = $(this).data('deposit');
        var withdraw = $(this).data('withdraw');

        // Populate modal fields
        $('#editLedgerModal input[name="id"]').val(id);
        $('#editLedgerModal input[name="trans_date"]').val(transDate);
        $('#editLedgerModal input[name="entry_date"]').val(entryDate);
        $('#editLedgerModal input[name="client_fund_ledger_type"]').val(type).prop('readonly', true); // Make type readonly
        $('#editLedgerModal input[name="description"]').val(description);

        // Handle Funds In and Funds Out - disable if zero
        if (parseFloat(deposit) === 0) {
            $('#editLedgerModal input[name="deposit_amount"]').val(deposit).prop('readonly', true);
        } else {
            $('#editLedgerModal input[name="deposit_amount"]').val(deposit).prop('readonly', false);
        }

        if (parseFloat(withdraw) === 0) {
            $('#editLedgerModal input[name="withdraw_amount"]').val(withdraw).prop('readonly', true);
        } else {
            $('#editLedgerModal input[name="withdraw_amount"]').val(withdraw).prop('readonly', false);
        }

        // Initialize datepickers
        $('#editLedgerModal input[name="trans_date"]').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        $('#editLedgerModal input[name="entry_date"]').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Show modal
        $('#editLedgerModal').modal('show');
    });


    // Handle update button click in modal
    $('#updateLedgerEntryBtn').on('click', function() {
        var form = $('#editLedgerForm')[0];
        var formData = new FormData(form); // Use FormData to include file uploads

        $.ajax({
            type: 'POST',
            url: "{{route('admin.clients.update-client-funds-ledger')}}",
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Let the browser set the content type for multipart/form-data
            success: function(response) {
                if (response.status) {
                    $('#editLedgerModal').modal('hide');
                    localStorage.setItem('activeTab', 'accounts');
                    location.reload();
                    $('.custom-error-msg').html('<span class="alert alert-success">' + response.message + '</span>');

                    // Update the Client Funds Ledger table
                    renderClientFundsLedger(response.updatedEntries);

                    // Update Current Funds Held
                    $('.current-funds-held').text('$ ' + parseFloat(response.currentFundsHeld).toFixed(2));
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');
                }
            },
            error: function(xhr, status, error) {
                $('.custom-error-msg').html('<span class="alert alert-danger">An error occurred. Please try again.</span>');
                console.error('AJAX error:', status, error);
            }
        });
    });

    // Handle document subtab switching
    $('.subtab-button').click(function() {
        // Remove active class from all document subtab buttons and panes
        $('.subtab-button').removeClass('active');
        $('.subtab-pane').removeClass('active');

        // Add active class to clicked button
        $(this).addClass('active');

        // Show corresponding pane
        const subtabId = $(this).data('subtab');
        $(`#${subtabId}-subtab`).addClass('active');

        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
            var selectedMatter = $('.general_matter_checkbox_client_detail').val();
        } else {
            var selectedMatter = $('#sel_matter_id_client_detail').val();
        }

       

        if( subtabId == 'inbox') {
            if(selectedMatter != "" ) {
                $('#inbox-subtab #email-list').find('.email-card').each(function() {
                    if ($(this).data('matterid') == selectedMatter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }  else {
                $(this).hide();
            }
        }

        if( subtabId == 'sent') {
            if(selectedMatter != "" ) {
                $('#sent-subtab #email-list1').find('.email-card').each(function() {
                    if ($(this).data('matterid') == selectedMatter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }  else {
                $(this).hide();
            }
        }

        //alert(subtabId);
        if( subtabId == 'migrationdocuments') {
            //var selectedMatter = $('#sel_matter_id_client_detail').val();
            //console.log('selectedMatter&&&&==='+selectedMatter);
            if(selectedMatter != "" ) {
                $('#migrationdocuments-subtab .migdocumnetlist1').find('.drow').each(function() {
                    if ($(this).data('matterid') == selectedMatter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }  else {
                $(this).hide();
            }
        }

        // Store the active tab in localStorage when a tab is clicked
        localStorage.setItem('subactiveTab', subtabId);
    });

    // On page load, check localStorage and activate the correct tab
    const subactiveTab = localStorage.getItem('subactiveTab');
    if (subactiveTab) {
        // Find the button corresponding to the stored tabId and trigger its click event
        const $subtargetButton = $(`.subtab-button[data-tab="${subactiveTab}"]`);
        if ($subtargetButton.length) {
            $subtargetButton.click(); // Trigger the click event to reuse the existing logic
        }
        // Clear localStorage to prevent persistence on future loads (optional)
        localStorage.removeItem('subactiveTab');
    }

    //Notes subtab click
    $('.subtab8-button').click(function(e) {
        e.preventDefault();

        // Remove active class from all buttons and panes
        $('.subtab8-button').removeClass('active');
        $('.subtab8-pane').removeClass('active');

        // Add active class to clicked button
        $(this).addClass('active');

        // Show corresponding pane
        const subtabId8 = $(this).data('subtab8'); //alert(subtabId8);
        $(`#${subtabId8}-subtab8`).addClass('active');
    });

    //Document subtab like - Personal
    $('.subtab2-button').click(function(e) {
        e.preventDefault();

        // Remove active class from all buttons and panes
        $('.subtab2-button').removeClass('active');
        $('.subtab2-pane').removeClass('active');

        // Add active class to clicked button
        $(this).addClass('active');

        // Show corresponding pane
        const subtabId2 = $(this).data('subtab2'); //alert(subtabId2);
        $(`#${subtabId2}-subtab2`).addClass('active');
    });

    //Document subtab like - Visa
    $('.subtab6-button').click(function(e) {
        e.preventDefault();

        // Remove active class from all buttons and panes
        $('.subtab6-button').removeClass('active');
        $('.subtab6-pane').removeClass('active');

        // Add active class to clicked button
        $(this).addClass('active');

        // Show corresponding pane
        const subtabId6 = $(this).data('subtab6'); //alert(subtabId6);
        $(`#${subtabId6}-subtab6`).addClass('active');
    });


    // Handle form generation subtab switching
    $('.subtab3-button').click(function() {
        // Remove active class from all document subtab buttons and panes
        $('.subtab3-button').removeClass('active');
        $('.subtab3-pane').removeClass('active');

        // Add active class to clicked button
        $(this).addClass('active');

        // Show corresponding pane
        const subtabId = $(this).data('subtab');
        $(`#${subtabId}-subtab`).addClass('active');

        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
            var selectedMatter = $('.general_matter_checkbox_client_detail').val();
        } else {
            var selectedMatter = $('#sel_matter_id_client_detail').val();
        }

		if( subtabId == 'form956') {
            if(selectedMatter != "" ) {
                $('#form956-subtab #form-list').find('.form-card').each(function() {
                    if ($(this).data('matterid') == selectedMatter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }  else {
                $(this).hide();
            }
        }

        if( subtabId == 'costform') {
            if(selectedMatter != "" ) {
                $('#costform-subtab #form-list1').find('.form-card').each(function() {
                    if ($(this).data('matterid') == selectedMatter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }  else {
                $(this).hide();
            }
        }

		/*if( subtabId == 'visaagreementform') {
            if(selectedMatter != "" ) {
                $('#visaagreementform-subtab #form-list2').find('.form-card').each(function() {
                    if ($(this).data('matterid') == selectedMatter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }  else {
                $(this).hide();
            }
        }*/


        if( subtabId == 'costformL') {
            if(selectedMatter != "" ) {
                $('#costformL-subtab #form-list1').find('.form-card').each(function() {
                    if ($(this).data('matterid') == selectedMatter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }  else {
                $(this).hide();
            }
        }
    });

    // Handle Filter by Status and Search Communication
    $('#filter-status, #search-communication').on('change keyup', function() {
        var status = $('#filter-status').val();
        var search = $('#search-communication').val();

        $.ajax({
            url: "{{route('admin.clients.filter.emails')}}",
            type: 'POST',
            data: {
                client_id: '{{ $fetchedData->id }}',
                status: status,
                search: search,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                try {
                    // Parse the response as JSON
                    var emails = typeof response === 'string' ? JSON.parse(response) : response;

                    // Check if the response indicates an error
                    if (emails.status === 'error') {
                        $('#email-list').html('<p>' + emails.message + '</p>');
                        return;
                    }

                    var emailList = $('#email-list');
                    emailList.empty(); // Clear current emails

                    if (emails.length > 0) {
                        emails.forEach(function(email) {
                            var emailCard = `
                                <div class="email-card" data-email-id="${email.id}">
                                    <div class="email-meta">
                                        <span class="author-initial">${email.from_mail ? email.from_mail.charAt(0) : 'N/A'}</span>
                                        <div class="email-info">
                                            <span class="author-name">${email.from_mail || 'Unknown'}</span>
                                            <span class="email-timestamp">${new Date(email.created_at).toLocaleString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) || 'N/A'}</span>
                                        </div>
                                    </div>
                                    <div class="email-body">
                                        <h4>${email.subject || 'No Subject'}</h4>
                                        <p>To: ${email.to_mail || 'Unknown'}</p>
                                    </div>
                                    <div class="email-actions">

                                        ${email.preview_url ? `<a href="${email.preview_url}" class="btn btn-link mail_preview_modal" memail_id="${email.id}" target="_blank">Preview</a>` : '<span>No Preview Available</span>'}
                                        <button class="btn btn-link create_note" datamailid="${email.id}" datasubject="${email.subject}" datatype="mailnote">Create Note</button>
					                    <button class="btn btn-link inbox_reassignemail_modal" memail_id="${email.id}" user_mail="${email.to_mail}" uploaded_doc_id="${email.uploaded_doc_id}" href="javascript:;">Reassign</button>
                                    </div>
                                </div>`;
                            emailList.append(emailCard);
                        });
                    } else {
                        emailList.append('<p>No emails found.</p>');
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    $('#email-list').html('<p>Error parsing email data. Please try again.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                $('#email-list').html('<p>Error fetching emails. Please try again. Status: ' + status + '</p>');
            }
        });
    });


    // Handle Filter by type ,status and Search Communication
    $('#filter-type1, #filter-status1, #search-communication1').on('change keyup', function() {
        const type = $('#filter-type1').val();
        const status = $('#filter-status1').val();
        const search = $('#search-communication1').val();
        const clientId = '{{ $fetchedData->id }}';
        const emailList = $('#email-list1');

        $.ajax({
            url: "{{ route('admin.clients.filter.sentmails') }}",
            type: 'POST',
            data: {
                client_id: clientId,
                type: type,
                status: status,
                search: search,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                emailList.html('<p>Loading...</p>');
            },
            success: function(response) {
                emailList.empty();

                if (response.status === 'error') {
                    emailList.html(`<p class="text-danger">${response.message}</p>`);
                    return;
                }

                if (!Array.isArray(response) || response.length === 0) {
                    emailList.append('<p>No emails found.</p>');
                    return;
                }

                response.forEach(email => {
                    // Sanitize and format data
                    const authorInitial = email.from_mail ? email.from_mail.charAt(0).toUpperCase() : 'N/A';
                    const timestamp = email.created_at
                        ? new Date(email.created_at).toLocaleString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })
                        : email.fetch_mail_sent_time || 'N/A';
                    const typeBadge = email.conversion_type
                        ? '<span class="badge badge-success">Assigned</span>'
                        : '<span class="badge badge-warning">Delivered</span>';
                    const previewLink = email.preview_url
                        ? `<a href="${email.preview_url}" class="btn btn-link mail_preview_modal" memail_id="${email.id}" target="_blank"><i class="fas fa-eye"></i> Preview</a>`
                        : `<a class="btn btn-link sent_mail_preview_modal" memail_message="${email.message || ''}" memail_subject="${email.subject || ''}"><i class="fas fa-eye"></i> Preview Mail</a>`;
                    const reassignButton = email.conversion_type
                        ? `<button class="btn btn-link sent_reassignemail_modal" memail_id="${email.id}" user_mail="${email.to_mail || ''}" uploaded_doc_id="${email.uploaded_doc_id || ''}">Reassign</button>`
                        : '';

                    const emailCard = `
                        <div class="email-card" data-email-id="${email.id}">
                            <div class="email-meta">
                                <span class="author-initial">${authorInitial}</span>
                                <div class="email-info">
                                    <span class="author-name">Sent by: <strong>${email.from_mail || 'Unknown'}</strong> ${typeBadge}</span>
                                    <span class="email-timestamp">${timestamp}</span>
                                </div>
                            </div>
                            <div class="email-body">
                                <h4>${email.subject || 'No Subject'}</h4>
                                <p>Sent To: ${email.to_mail || 'Unknown'}</p>
                            </div>
                            <div class="email-actions">
                                ${previewLink}
                                <button class="btn btn-link create_note" datamailid="${email.id}" datasubject="${email.subject || ''}" datatype="mailnote"><i class="fas fa-sticky-note"></i> Create Note</button>
                                ${reassignButton}
                            </div>
                        </div>`;
                    emailList.append(emailCard);
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                emailList.html('<p class="text-danger">Error fetching emails. Please try again later.</p>');
            }
        });
    });


    // Initialize Activity Feed visibility on page load
    if ($('#personaldetails-tab').hasClass('active')) {
        $('#activity-feed').show();
        $('#main-content').css('flex', '1');
    } else {
        $('#activity-feed').hide();
        //$('#main-content').css('flex', '0 0 100%');
    }
});
</script>
<script>
    function previewFile(fileType, fileUrl, containerId) {
        console.log('fileType='+fileType);
        console.log('fileUrl='+fileUrl);
        console.log('containerId='+containerId);
        const container = $(`.${containerId}`);

        // Show loading state
        container.html(`
            <h3 style="margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; font-size: 18px; color: #333;">File Preview</h3>
            <div class="preview-content" style="flex: 1; display: flex; align-items: center; justify-content: center;">
                <div style="text-align: center;">
                    <i class="fas fa-spinner fa-spin fa-2x" style="color: #4a90e2;"></i>
                    <p style="margin-top: 10px; color: #666;">Loading preview...</p>
                </div>
            </div>
        `);

        // Determine content based on file type
        let content = '';

        if (fileType.toLowerCase().match(/(jpg|jpeg|png|gif)$/)) {
            const img = new Image();
            img.onload = function() {
                container.html(`
                    <h3 style="margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; font-size: 18px; color: #333;">File Preview</h3>
                    <div class="preview-content" style="flex: 1; overflow: auto; text-align: center;">
                        <img src="${fileUrl}" alt="Document Preview" style="max-width: 100%; max-height: calc(100vh - 300px); margin: auto; display: block;" />
                    </div>
                `);
            };
            img.src = fileUrl;
        } else if (fileType.toLowerCase() === 'pdf') {
            container.html(`
                <h3 style="margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; font-size: 18px; color: #333;">File Preview</h3>
                <div class="preview-content" style="flex: 1; overflow: hidden;width: 475px !important;">
                    <iframe src="${fileUrl}" type="application/pdf" style="width: 100%; height: calc(100vh - 100px); border: none;"></iframe>
                </div>
            `);
        }
        else if (fileType.toLowerCase().match(/^(docx?|xlsx?|pptx?)$/)) {
            const officeViewerUrl = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`;
            container.html(`
                <h3 style="margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; font-size: 18px; color: #333;">File Preview</h3>
                <div class="preview-content" style="flex: 1; overflow: hidden; width: 100%;">
                    <iframe src="${officeViewerUrl}" class="doc-viewer" style="width: 100%; height: calc(100vh - 100px); border: none;"></iframe>
                </div>
            `);
        }
        else {
            container.html(`
                <h3 style="margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; font-size: 18px; color: #333;">File Preview</h3>
                <div class="preview-content" style="flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                    <i class="fas fa-file fa-3x" style="color: #6c757d; margin-bottom: 15px;"></i>
                    <p style="margin-bottom: 15px;">Preview not available for this file type.</p>
                    <a href="${fileUrl}" target="_blank" class="btn btn-primary">Open File</a>
                </div>
            `);
        }
    }

    // Update preview container styles when the document is ready
    $(document).ready(function() {
        // Style all preview containers
        $('.preview-pane.file-preview-container').css({
            'display': 'flex',
            'flex-direction': 'column',
            'margin-top': '15px',
            'width': '499px',
            'min-height': '500px',
            'height': 'calc(100vh - 200px)',
            'border': '1px solid #dee2e6',
            'border-radius': '4px',
            'padding': '15px',
            'background': '#fff',
            'position': 'sticky',
            'top': '20px'
        });

        // Handle window resize
        $(window).resize(function() {
            adjustPreviewContainers();
        }).resize(); // Trigger on load
    });

    // Function to adjust preview containers
    function adjustPreviewContainers() {
        $('.preview-pane.file-preview-container').each(function() {
            const windowHeight = $(window).height();
            const containerTop = $(this).offset().top;
            const desiredHeight = windowHeight - containerTop - 50; // 50px buffer

            if (desiredHeight >= 600) { // Minimum height
                $(this).css('height', desiredHeight + 'px');
            } else {
                $(this).css('height', '600px');
            }
        });
    }

    document.getElementById('chatGptToggle').addEventListener('click', function() {
        const section = document.getElementById('chatGptSection');
        section.classList.toggle('collapse');
    });

    document.getElementById('chatGptClose').addEventListener('click', function() {
        const section = document.getElementById('chatGptSection');
        section.classList.add('collapse');
    });

    document.getElementById('enhanceMessageBtn').addEventListener('click', function() {
        const chatGptInput = document.getElementById('chatGptInput').value;
        if (!chatGptInput) {
            alert('Please enter a message to enhance.');
            return;
        }

        fetch("{{ route('admin.mail.enhance') }}", {  // Use Laravel's route helper
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content") // Fetch CSRF token dynamically
            },
            body: JSON.stringify({ message: chatGptInput })
        })
        .then(response => response.json())
        .then(data => {
            if (data.enhanced_message) {
                // Split the enhanced message into lines
                const lines = data.enhanced_message.split('\n').filter(line => line.trim() !== '');

                // First line is the subject
                const subject = lines[0] || '';

                // Remaining lines are the body
                const body = lines.slice(1).join('\n') || '';

                // Update the subject and message fields
                document.getElementById('compose_email_subject').value = subject;
                //document.getElementById('compose_email_message').value = body;
                // Ensure Summernote is initialized before updating content
                $("#emailmodal .summernote-simple").summernote('code',body);

                // Close the ChatGPT section
                document.getElementById('chatGptSection').classList.add('collapse');
            } else {
                alert(data.error || 'Failed to enhance message.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while enhancing the message.');
        });
    });


    jQuery(document).ready(function($){

        $('.selecttemplate').select2({dropdownParent: $('#emailmodal')});

        //mail preview click update mail_is_read bit
        $('.mail_preview_modal').on('click', function(){
            var mail_report_id = $(this).attr('memail_id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.clients.updatemailreadbit')}}",
                method: "POST",
                data: {mail_report_id:mail_report_id},
                datatype: 'json',
                success: function(response) {
                }
            });
        });

        //inbox mail reassign Model popup code start
        $(document).on('click', '.inbox_reassignemail_modal', function() {
            var val = $(this).attr('memail_id');
            $('#inbox_reassignemail_modal #memail_id').val(val);
            var user_mail = $(this).attr('user_mail');
            $('#inbox_reassignemail_modal #user_mail').val(user_mail);
            var uploaded_doc_id = $(this).attr('uploaded_doc_id');
            $('#inbox_reassignemail_modal #uploaded_doc_id').val(uploaded_doc_id);
            $('#inbox_reassignemail_modal').modal('show');
        });

        //Initialize both Select2 dropdowns
        $('#reassign_client_id').select2();
        $('#reassign_client_matter_id').select2();

        $(document).delegate('#reassign_client_id', 'change', function(){
            let selected_client_id = $(this).val();
            console.log('selected_client_id='+selected_client_id);

            if (selected_client_id != "") {
                $('.popuploader').show();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('admin.clients.listAllMattersWRTSelClient')}}",
                    method: "POST",
                    data: {client_id:selected_client_id},
                    datatype: 'json',
                    success: function(response) {
                        $('.popuploader').hide();
                        var obj = $.parseJSON(response); console.log(obj.clientMatetrs);
                        var matterlist = '<option value="">Select Client Matter</option>';
                        $.each(obj.clientMatetrs, function(index, subArray) {
                            matterlist += '<option value="'+subArray.id+'">'+subArray.title+'('+subArray.client_unique_matter_no+')</option>';
                        });
                        $('#reassign_client_matter_id').html(matterlist);
                    }
                });
                $('#reassign_client_matter_id').prop('disabled', false).select2();
            } else {
                $('#reassign_client_matter_id').prop('disabled', true).select2();
            }
        });


        //sent mail reassign Model popup code start
        $(document).on('click', '.sent_reassignemail_modal', function() {
            var val = $(this).attr('memail_id');
            $('#sent_reassignemail_modal #memail_id').val(val);
            var user_mail = $(this).attr('user_mail');
            $('#sent_reassignemail_modal #user_mail').val(user_mail);
            var uploaded_doc_id = $(this).attr('uploaded_doc_id');
            $('#sent_reassignemail_modal #uploaded_doc_id').val(uploaded_doc_id);
            $('#sent_reassignemail_modal').modal('show');
        });

        $('.sent_mail_preview_modal').on('click', function(){
            var memail_subject = $(this).attr('memail_subject');
            $('#sent_mail_preview_modal #memail_subject').html(memail_subject);

            var memail_message = $(this).attr('memail_message');
            $('#sent_mail_preview_modal #memail_message').html(memail_message);

            $('#sent_mail_preview_modal').modal('show');
        });

        //Initialize both Select2 dropdowns
        $('#reassign_sent_client_id').select2();
        $('#reassign_sent_client_matter_id').select2();

        $(document).delegate('#reassign_sent_client_id', 'change', function(){
            let selected_client_id = $(this).val();
            console.log('selected_client_id='+selected_client_id);

            if (selected_client_id != "") {
                $('.popuploader').show();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('admin.clients.listAllMattersWRTSelClient')}}",
                    method: "POST",
                    data: {client_id:selected_client_id},
                    datatype: 'json',
                    success: function(response) {
                        $('.popuploader').hide();
                        var obj = $.parseJSON(response); console.log(obj.clientMatetrs);
                        var matterlist = '<option value="">Select Client Matter</option>';
                        $.each(obj.clientMatetrs, function(index, subArray) {
                            matterlist += '<option value="'+subArray.id+'">'+subArray.title+'('+subArray.client_unique_matter_no+')</option>';
                        });
                        $('#reassign_sent_client_matter_id').html(matterlist);
                    }
                });
                $('#reassign_sent_client_matter_id').prop('disabled', false).select2();
            } else {
                $('#reassign_sent_client_matter_id').prop('disabled', true).select2();
            }
        });



        // Open modal when star icon button is clicked
        $("#open-rating-modal").click(function () {
            $("#rating-modal").modal('show');
        });

        // Handle the submit rating button click
        $("#submit-rating").click(function () {
            const educationRating = $("input[name='education_rating']:checked").val();
            const migrationRating = $("input[name='migration_rating']:checked").val();

            $.ajax({
                url: "{{ route('admin.clients.saveRating') }}",
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    education_rating: educationRating,
                    migration_rating: migrationRating
                },
                success: function(response) {
                    if (response.status) {
                        window.location.href = "";

                        alert("Ratings saved successfully!");

                    } else {
                        alert("Error saving ratings.");
                    }
                    $("#rating-modal").modal('hide');
                },
                error: function(xhr, status, error) {
                    alert("An error occurred: " + error);
                    $("#rating-modal").modal('hide');
                }
            });
        });


        $(document).on('click', '#mark-star-client-modal', function () {
            var adminId = $(this).data('admin-id');
            $.ajax({
                url: '{{ route("check.star.client") }}',
                type: 'POST',
                data: {
                    admin_id: adminId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 'exists') {
                        alert('Client is already a star employee.');
                    } else if (response.status === 'not_star') {
                        if (confirm('Do you want to make this client a star client?')) {
                            // Send update request
                            $.ajax({
                                url: '{{ route("check.star.client") }}',
                                type: 'POST',
                                data: {
                                    admin_id: adminId,
                                    update: true,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (res) {
                                    if (res.status === 'updated') {
                                        alert('Client marked as star successfully.');
                                    }
                                }
                            });
                        }
                    } else {
                        alert(response.message || 'Something went wrong.');
                    }
                }
            });
        });




        // Handle click event on the action button
        $(document).delegate('.btn-assignuser', 'click', function(){
            // Get the value from the #note_description textarea
            var note_description = $('#note_description').val();

            // Display the value in an alert
             // alert(note_description);

            // Transfer the value to the #assignnote textarea in the #create_action_popup modal
            $('#assignnote').val(note_description);

            // Close the #create_note_d modal
            $('#create_note_d').modal('hide');

            // Show the #create_action_popup modal
            $('#create_action_popup').modal('show');
        });

        // Toggle dropdown menu on button click
        $('.dropdown-toggle').on('click', function() {
            $(this).parent().toggleClass('show');
        });

        // Close the dropdown if clicked outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown-multi-select').length) {
                $('.dropdown-multi-select').removeClass('show');
            }
        });

        // Handle checkbox click events
        $('.checkbox-item').on('change', function() {
            var selectedValues = [];

            // Collect selected checkboxes values
            $('.checkbox-item:checked').each(function() {
                selectedValues.push($(this).val());
            });

            // Set the selected values in the hidden select dropdown
            $('#rem_cat').val(selectedValues).trigger('change');
        });

        // Handle "Select All" functionality (If needed, you can include this part)
        $('#select-all').on('change', function() {
            if ($(this).is(':checked')) {
                // Select all checkboxes
                $('.checkbox-item').prop('checked', true).trigger('change');
            } else {
                // Deselect all checkboxes
                $('.checkbox-item').prop('checked', false).trigger('change');
            }
        });


        //Matter checkbox start
        var selectedMatter = '';

        //Add note popup Select client
        $(document).delegate('.general_matter_checkbox', 'change', function(){
            if (this.checked) {
                $('#matter_id').prop('disabled', true).trigger('change');
                $('#matter_id').removeAttr('data-valid').trigger('change');
            } else {
                $('#matter_id').prop('disabled', false).trigger('change');
                $('#matter_id').attr('data-valid', 'required').trigger('change');
            }
        });

        $(document).delegate('.general_matter_checkbox', 'click', function(){
            // Uncheck all checkboxes
            $('.general_matter_checkbox').not(this).prop('checked', false);
        });


        //Convert lead to client popup and select matter
        $(document).delegate('#general_matter_checkbox_new', 'change', function(){
            if (this.checked) {
                $('#sel_matter_id').prop('disabled', true).trigger('change');
                $('#sel_matter_id').removeAttr('data-valid').trigger('change');
            } else {
                $('#sel_matter_id').prop('disabled', false).trigger('change');
                $('#sel_matter_id').attr('data-valid', 'required').trigger('change');
            }
        });


        //Client detail page Select general matter checkbox and assign matter id
        $(document).delegate('.general_matter_checkbox_client_detail', 'change', function(){
            if (this.checked) {
                $('#sel_matter_id_client_detail').prop('disabled', true).trigger('change');
                $('#sel_matter_id_client_detail').removeAttr('data-valid').trigger('change');
                selectedMatter = $(this).val();
                console.log('selectedMatter-checkbox:', selectedMatter);

                var uniqueMatterNo = $(this).data('clientuniquematterno');
                var currentUrl = window.location.href;
                console.log('selectedMatter-checkbox-uniqueMatterNo:', uniqueMatterNo);
                // Get the active sub tab
                var activeSubTab = $('.subtab-button.active').data('subtab');
                console.log('activeTab==='+activeTab);
                console.log('activeSubTab==='+activeSubTab);

                // Split the URL into segments
                var urlSegments = currentUrl.split('/');
                // Check if the URL has a matter ID (more than 7 segments for http://127.0.0.1:8000/admin/clients/detail/JSxTOFQtQzBgCmAK)
                var baseUrl;
                console.log('urlSegments-length='+urlSegments.length);
                if (urlSegments.length > 7) {
                    // If there's a matter ID (e.g., CT_1 in .../JSxTOFQtQzBgCmAK/CT_1), remove it
                    urlSegments.pop();
                    baseUrl = urlSegments.join('/');
                } else {
                    // If there's no matter ID (e.g., .../JSxTOFQtQzBgCmAK), use the URL as is
                    baseUrl = currentUrl;
                }

                if (selectedMatter != '') {
                    // Append the new matter ID (e.g., AP_1) to the base URL
                    window.location.href = baseUrl + '/' + uniqueMatterNo;
                } else {
                    // If no matter is selected, redirect to the base URL (without matter ID)
                    window.location.href = baseUrl;
                }

                if( activeTab == 'noteterm' ) {
                    if(selectedMatter != "" ) {
                        $('#noteterm-tab').find('.note-card-redesign').each(function() {
                            if ($(this).data('matterid') == selectedMatter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }  else {
                        $('#noteterm-tab').find('.note-card-redesign').each(function() {
                            if ($(this).data('matterid') == '') {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }
                }
                else if( activeTab == 'documentalls' && activeSubTab == 'migrationdocuments') {
                    if(selectedMatter != "" ) {
                        $('#migrationdocuments-subtab .migdocumnetlist1').find('.drow').each(function() {
                            if ($(this).data('matterid') == selectedMatter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }  else {
                        $(this).hide();
                    }
                }

                else if( activeTab == 'conversations' && activeSubTab == 'inbox') {
                    if(selectedMatter != "" ) {
                        $('#inbox-subtab #email-list').find('.email-card').each(function() {
                            if ($(this).data('matterid') == selectedMatter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }  else {
                        $(this).hide();
                    }
                }

                else if( activeTab == 'conversations' && activeSubTab == 'sent') {
                    if(selectedMatter != "" ) {
                        $('#sent-subtab #email-list1').find('.email-card').each(function() {
                            if ($(this).data('matterid') == selectedMatter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }  else {
                        $(this).hide();
                    }
                }
            } else {
                $('#sel_matter_id_client_detail').prop('disabled', false).trigger('change');
                $('#sel_matter_id_client_detail').attr('data-valid', 'required').trigger('change');
                selectedMatter = "";
            }
        });

        //Select matter drop down chnage
        $('#sel_matter_id_client_detail').on('change', function() {
            selectedMatter = $(this).val();
            var uniqueMatterNo = $(this).find('option:selected').data('clientuniquematterno');
            var currentUrl = window.location.href;
            // Get the active tab
            var activeTab = $('.tab-button.active').data('tab');

             // Get the active sub tab
            var activeSubTab = $('.subtab-button.active').data('subtab');
            console.log('activeTab==='+activeTab);
            console.log('activeSubTab==='+activeSubTab);

            // Split the URL into segments
            var urlSegments = currentUrl.split('/');
            // Check if the URL has a matter ID (more than 7 segments for http://127.0.0.1:8000/admin/clients/detail/JSxTOFQtQzBgCmAK)
            var baseUrl;
            console.log('urlSegments-length='+urlSegments.length);
            if (urlSegments.length > 7) {
                // If there's a matter ID (e.g., CT_1 in .../JSxTOFQtQzBgCmAK/CT_1), remove it
                urlSegments.pop();
                baseUrl = urlSegments.join('/');
            } else {
                // If there's no matter ID (e.g., .../JSxTOFQtQzBgCmAK), use the URL as is
                baseUrl = currentUrl;
            }

            if (selectedMatter != '') {
                // Append the new matter ID (e.g., AP_1) to the base URL
                window.location.href = baseUrl + '/' + uniqueMatterNo;
            } else {
                // If no matter is selected, redirect to the base URL (without matter ID)
                window.location.href = baseUrl;
            }

            if( activeTab == 'noteterm' ) {
                if(selectedMatter != "" ) {
                    $('#noteterm-tab').find('.note-card-redesign').each(function() {
                        if ($(this).data('matterid') == selectedMatter) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }  else {
                    $('#noteterm-tab').find('.note-card-redesign').each(function() {
                        if ($(this).data('matterid') == '') {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            }
            else if( activeTab == 'documentalls' && activeSubTab == 'migrationdocuments') {
                if(selectedMatter != "" ) {
                    $('#migrationdocuments-subtab .migdocumnetlist1').find('.drow').each(function() {
                        if ($(this).data('matterid') == selectedMatter) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }  else {
                    $(this).hide();
                }
            }

            else if( activeTab == 'conversations' && activeSubTab == 'inbox') {
                if(selectedMatter != "" ) {
                    $('#inbox-subtab #email-list').find('.email-card').each(function() {
                        if ($(this).data('matterid') == selectedMatter) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }  else {
                    $(this).hide();
                }
            }

             else if( activeTab == 'conversations' && activeSubTab == 'sent') {
                if(selectedMatter != "" ) {
                    $('#sent-subtab #email-list1').find('.email-card').each(function() {
                        if ($(this).data('matterid') == selectedMatter) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }  else {
                    $(this).hide();
                }
            }

            //var activeTab = $('.nav-item .nav-link.active');
            console.log('Active tab ID:', activeTab.attr('id'));
            /*if( activeTab.attr('id') == 'noteterm-tab' ) {
                // Trigger click on the active tab
                activeTab.trigger('click');
            }
            else if( activeTab.attr('id') == 'migrationdocuments-tab' ) {
                // Trigger click on the active tab
                activeTab.trigger('click');
            }
            else if( activeTab.attr('id') == 'conversations-tab' ) {
                // Trigger click on the active tab
                activeTab.trigger('click');
            }*/
        });


        //Tab click
        $(document).delegate('#client_tabs a', 'click', function(){
            // Get the target tab's href
            var target = $(this).attr('href'); console.log(target);

            // Reset the visibility and classes
            $('.left_section').hide(); // Hide the left section by default
            $('.right_section').parent().removeClass('col-8 col-md-8 col-lg-8').addClass('col-12 col-md-12 col-lg-12');

            // Adjust based on the selected tab
            if (target === '#activities') {
                $('.left_section').show(); // Show the left section for Activities tab
                $('.left_section').removeClass('col-4 col-md-4 col-lg-4').addClass('col-4 col-md-4 col-lg-4');
                $('.right_section').parent().removeClass('col-12 col-md-12 col-lg-12').addClass('col-8 col-md-8 col-lg-8');
            }

            else if (target === '#noteterm') {
                if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                    selectedMatter = $('.general_matter_checkbox_client_detail').val();
                } else {
                    selectedMatter = $('#sel_matter_id_client_detail').val();
                }
                console.log('target=='+ target);
                console.log('selectedMatter=='+ selectedMatter);
                if(target == '#noteterm' ){
                    if(selectedMatter != "" ) {
                        $(target).find('.note_col').each(function() {
                            console.log('matterid=='+ $(this).data('matterid'));
                            if ($(this).data('matterid') == selectedMatter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }  else {
                        //alert('Please select matter from matter drop down.');
                        $(target).find('.note_col').each(function() {
                            $(this).hide();
                        });
                    }
                }
            }

            else if (target === '#migrationdocuments') { //alert('migrationdocuments');
                if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                    selectedMatter = $('.general_matter_checkbox_client_detail').val();
                } else {
                    selectedMatter = $('#sel_matter_id_client_detail').val();
                }
                console.log('target=='+ target);
                console.log('selectedMatter=='+ selectedMatter);
                if(target == '#migrationdocuments' ){
                    if(selectedMatter != "" ) {
                        $(target).find('.drow').each(function() {
                            console.log('matterid=='+ $(this).data('matterid'));
                            if ($(this).data('matterid') == selectedMatter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }  else {
                        //alert('Please select matter from matter drop down.');
                        $(target).find('.drow').each(function() {
                            $(this).hide();
                        });
                    }
                }
            }

            else if (target === '#conversations') {
                if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                    selectedMatter = $('.general_matter_checkbox_client_detail').val();
                } else {
                    selectedMatter = $('#sel_matter_id_client_detail').val();
                }
                console.log('target=='+ target);
                console.log('selectedMatter=='+ selectedMatter);
                if(target == '#conversations' ){
                    if(selectedMatter != "" ) {
                        //inbox mail
                        $(target).find('.inbox_conversation_list').each(function() {
                            console.log('matterid=='+ $(this).data('matterid'));
                            if ($(this).data('matterid') == selectedMatter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });

                        //sent mail
                        $(target).find('.sent_conversation_list').each(function() {
                            console.log('matterid=='+ $(this).data('matterid'));
                            if ($(this).data('matterid') == selectedMatter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }  else {
                        //alert('Please select matter from matter drop down.');
                        //inbox mail
                        $(target).find('.inbox_conversation_list').each(function() {
                            $(this).hide();
                        });

                        //sent mail
                        $(target).find('.sent_conversation_list').each(function() {
                            $(this).hide();
                        });
                    }
                }
            }
            else if (target === '#clientdetailform') {
                var right_section_height = $('#clientdetailform').height();
                right_section_height = right_section_height+200;
                console.log('total_left='+total_left);
                console.log('right_section_height='+right_section_height);
                $('.right_section').css({"maxHeight":right_section_height});
            }
        });

        $(document).delegate('.general_matter_checkbox_client_detail', 'click', function(){
            // Uncheck all checkboxes
            $('.general_matter_checkbox_client_detail').not(this).prop('checked', false);
        });
        //Matter checkbox end


        //create client receipt start
        $('.report_date_fields').datepicker({ format: 'dd/mm/yyyy',autoclose: true });
        $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        /*$(document).delegate('.openproductrinfo', 'click', function(){
            var clonedval = $('.clonedrow').html();
            $('.productitem').append('<tr class="product_field_clone">'+clonedval+'</tr>');
            $('.report_date_fields,.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy', autoclose: true  });
           // $('.report_entry_date_fields').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });*/

        $(document).delegate('.openproductrinfo', 'click', function() {
            var clonedval = $('.clonedrow').html();
            var $newRow = $('<tr class="product_field_clone">' + clonedval + '</tr>');
            // Hide the invoice input inside the cloned row
            $newRow.find('.invoice_no_cls').hide();
            $('.productitem').append($newRow);
            $('.report_date_fields,.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy', autoclose: true  });
            //$('.report_entry_date_fields').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });


        $(document).delegate('.removeitems', 'click', function(){
            var $tr    = $(this).closest('.product_field_clone');
            var trclone = $('.product_field_clone').length;
            if(trclone > 0){
                $tr.remove();
            }
            grandtotalAccountTab();
        });

        $(document).delegate('.deposit_amount_per_row,.withdraw_amount_per_row', 'keyup', function(){
            grandtotalAccountTab();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        function getInfoByReceiptId11(receiptid) {
            $.ajax({
                type:'post',
                url: '{{URL::to('/admin/clients/getInfoByReceiptId')}}',
                sync:true,
                data: {receiptid:receiptid},
                success: function(response){
                    var obj = $.parseJSON(response); console.log('record_get=='+obj.record_get);
                    if(obj.status){
                        $('#function_type').val("edit");
                        $('#createreceiptmodal').modal('show');

                        const invoiceRadio = document.querySelector('input[name="receipt_type"][value="invoice_receipt"]');
                        if (invoiceRadio) {
                            invoiceRadio.checked = true;

                            // Manually trigger the change event
                            invoiceRadio.dispatchEvent(new Event('change'));
                        }

                        if(obj.record_get){
                            var record_get = obj.record_get;
                            //var trRows_office = "";
                            var sum = 0;
                            $('.productitem_invoice tr.clonedrow_invoice').remove();
                            $('.productitem_invoice tr.product_field_clone_invoice').remove();
                            $.each(record_get, function(index, subArray) {
                                console.log('index=='+index);
                                console.log('invoice=='+subArray.invoice_no);

                                var value_sum = parseFloat(subArray.withdraw_amount);
                                if (!isNaN(value_sum)) {
                                    sum += value_sum;
                                }

                                var rowCls = index < 1 ? 'clonedrow_invoice' : 'product_field_clone_invoice';

                                //var trRows_office = '<tr class="'+rowCls+'"><td><input name="id[]" type="hidden" value="'+subArray.id+'" /><input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="'+subArray.trans_date+'" /></td><td><input data-valid="required" class="form-control report_date_fields_invoice" name="entry_date[]" type="text" value="'+subArray.entry_date+'" /></td><td><select class="form-control gst_included_cls" name="gst_included[]"><option value="">Select</option><option value="Yes">Yes</option><option value="No">No</option></select></td><td><select class="form-control payment_type_cls" name="payment_type[]"><option value="">Select</option><option value="Professional Fee">Professional Fee</option><option value="Department Charges">Department Charges</option><option value="Surcharge">Surcharge</option><option value="Disbursements">Disbursements</option><option value="Other Cost">Other Cost</option></select></td><td><input data-valid="required" class="form-control" name="description[]" type="text" value="'+subArray.description+'" /></td><td><span class="currencyinput">$</span><input data-valid="required" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="'+subArray.withdraw_amount+'" /></td><td><a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a></td></tr>';
                                var trRows_office = `<tr class="${rowCls}">
                                    <td>
                                        <input name="id[]" type="hidden" value="${subArray.id}" />
                                        <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="${subArray.trans_date}" />
                                    </td>
                                    <td>
                                        <input data-valid="required" class="form-control report_date_fields_invoice" name="entry_date[]" type="text" value="${subArray.entry_date}" />
                                    </td>

                                    <td>
                                        <select class="form-control gst_included_cls" name="gst_included[]">
                                            <option value="">Select</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control payment_type_cls" name="payment_type[]">
                                            <option value="">Select</option>
                                            <option value="Professional Fee">Professional Fee</option>
                                            <option value="Department Charges">Department Charges</option>
                                            <option value="Surcharge">Surcharge</option>
                                            <option value="Disbursements">Disbursements</option>
                                            <option value="Other Cost">Other Cost</option>
                                            <option value="Discount">Discount</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input data-valid="required" class="form-control" name="description[]" type="text" value="${subArray.description}" />
                                    </td>
                                    <td>
                                        <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                        <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="${subArray.withdraw_amount}" />
                                    </td>
                                    <td>
                                        <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>`;

                                let $newRow = $(trRows_office);
                                $('.productitem_invoice').append($newRow);

                                // Set selected values
                                $newRow.find('.gst_included_cls').val(subArray.gst_included);
                                $newRow.find('.payment_type_cls').val(subArray.payment_type);

                                $('.report_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy', autoclose: true  });
                                $('.report_entry_date_fields_invoice').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
                                if(index <1 ){
                                    $('.invoice_no').val(subArray.invoice_no);
                                    $('.unique_invoice_no').text(subArray.invoice_no);
                                    $('#receipt_id').val(subArray.receipt_id);
                                }
                            });
                            $('.total_withdraw_amount_all_rows_invoice').text("$"+sum.toFixed(2));
                        }
                    }
                }
            });
        }

        function getInfoByReceiptId(receiptid) {
            $.ajax({
                type: 'post',
                url: '{{URL::to('/admin/clients/getInfoByReceiptId')}}',
                sync: true,
                data: { receiptid: receiptid },
                success: function (response) {
                    var obj = $.parseJSON(response);
                    if (obj.status) {
                        $('#function_type').val("edit");
                        $('#createreceiptmodal').modal('show');

                        const invoiceRadio = document.querySelector('input[name="receipt_type"][value="invoice_receipt"]');
                        if (invoiceRadio) {
                            invoiceRadio.checked = true;
                            invoiceRadio.dispatchEvent(new Event('change'));
                        }

                        if (obj.record_get) {
                            var record_get = obj.record_get;
                            var sum = 0;
                            $('.productitem_invoice tr.clonedrow_invoice').remove();
                            $('.productitem_invoice tr.product_field_clone_invoice').remove();

                            $.each(record_get, function (index, subArray) {
                                console.log('invoice==' + subArray.invoice_no);

                                var value_sum = parseFloat(subArray.withdraw_amount);
                                if (!isNaN(value_sum)) {
                                    sum += value_sum;
                                }

                                var rowCls = index < 1 ? 'clonedrow_invoice' : 'product_field_clone_invoice';

                                var trRows_office = `<tr class="${rowCls}">
                                    <td>
                                        <input name="id[]" type="hidden" value="${subArray.id}" />
                                        <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="${subArray.trans_date}" />
                                    </td>
                                    <td>
                                        <input data-valid="required" class="form-control report_date_fields_invoice" name="entry_date[]" type="text" value="${subArray.entry_date}" />
                                    </td>
                                    <td>
                                        <select class="form-control gst_included_cls" name="gst_included[]">
                                            <option value="">Select</option>
                                            <option value="Yes" ${subArray.gst_included === 'Yes' ? 'selected' : ''}>Yes</option>
                                            <option value="No" ${subArray.gst_included === 'No' ? 'selected' : ''}>No</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control payment_type_cls" name="payment_type[]">
                                            <option value="">Select</option>
                                            <option value="Professional Fee" ${subArray.payment_type === 'Professional Fee' ? 'selected' : ''}>Professional Fee</option>
                                            <option value="Department Charges" ${subArray.payment_type === 'Department Charges' ? 'selected' : ''}>Department Charges</option>
                                            <option value="Surcharge" ${subArray.payment_type === 'Surcharge' ? 'selected' : ''}>Surcharge</option>
                                            <option value="Disbursements" ${subArray.payment_type === 'Disbursements' ? 'selected' : ''}>Disbursements</option>
                                            <option value="Other Cost" ${subArray.payment_type === 'Other Cost' ? 'selected' : ''}>Other Cost</option>
                                            <option value="Discount" ${subArray.payment_type === 'Discount' ? 'selected' : ''}>Discount</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input data-valid="required" class="form-control" name="description[]" type="text" value="${subArray.description}" />
                                    </td>
                                    <td>
                                        <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                        <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="${subArray.withdraw_amount}" />
                                    </td>
                                    <td>
                                        <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>`;

                                let $newRow = $(trRows_office);
                                $('.productitem_invoice').append($newRow);

                                $('.report_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy', autoclose: true });
                                $('.report_entry_date_fields_invoice').last().datepicker({ format: 'dd/mm/yyyy', todayHighlight: true, autoclose: true }).datepicker('setDate', new Date());

                                if (index < 1) {
                                    $('.invoice_no').val(subArray.invoice_no);
                                    $('.unique_invoice_no').text(subArray.invoice_no);
                                    $('#receipt_id').val(subArray.receipt_id);
                                }
                            });

                            $('.total_withdraw_amount_all_rows_invoice').text("$" + sum.toFixed(2));
                        }
                    }
                }
            });
        }


        $(document).on('change', '.client_fund_ledger_type', function () {
            var $row = $(this).closest('tr');
            var ledgerType = $(this).val();

            var $depositInput = $row.find('.deposit_amount_per_row');
            var $withdrawInput = $row.find('.withdraw_amount_per_row');
            var $invoiceInput = $row.find('.invoice_no_cls');

            // Invoice show/hide based on Fee Transfer
            if (ledgerType === 'Fee Transfer') {
                $invoiceInput.show().attr('data-valid', 'required');
            } else {
                $invoiceInput.hide().removeAttr('data-valid').val('');
            }

            if (ledgerType !== "") {
                var fundType = (ledgerType === 'Deposit') ? 'deposit' : 'withdraw';

                if (fundType === 'deposit') {
                    $depositInput.removeAttr('readonly').attr('data-valid', 'required').val("");
                    $withdrawInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");
                } else if (fundType === 'withdraw') {
                    $withdrawInput.removeAttr('readonly').attr('data-valid', 'required').val("");
                    $depositInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");
                } else {
                    $depositInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");
                    $withdrawInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");
                }
            } else {
                $depositInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");
                $withdrawInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");
            }
        });


        function grandtotalAccountTab() {
            var total_deposit_amount_all_rows = 0;
            var total_withdraw_amount_all_rows = 0;

            $('.productitem tr').each(function() {
                var $row = $(this);

                // Handle deposit amount
                var depositVal = $row.find('.deposit_amount_per_row').val();
                var depositAmount = parseFloat(depositVal) || 0; // fallback to 0 if NaN
                total_deposit_amount_all_rows += depositAmount;

                // Handle withdraw amount
                var withdrawVal = $row.find('.withdraw_amount_per_row').val();
                var withdrawAmount = parseFloat(withdrawVal) || 0; // fallback to 0 if NaN
                total_withdraw_amount_all_rows += withdrawAmount;
            });

            $('.total_deposit_amount_all_rows').html("$" + total_deposit_amount_all_rows.toFixed(2));
            $('.total_withdraw_amount_all_rows').html("$" + total_withdraw_amount_all_rows.toFixed(2));
        }

        //create client receipt changes end


        //create invoice receipt start
        $('.report_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy', autoclose: true });
        $('.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());


        $(document).delegate('.openproductrinfo_invoice', 'click', function(){
            var clonedval_invoice = `<td>
                            <input name="id[]" type="hidden" value="" />
                            <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="" />
                        </td>
                        <td>
                            <input data-valid="required" class="form-control report_entry_date_fields_invoice" name="entry_date[]" type="text" value="" />
                        </td>

                        <td>
                            <select class="form-control" name="gst_included[]">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </td>

                        <td>
                            <select class="form-control payment_type_invoice_per_row" name="payment_type[]">
                                <option value="">Select</option>
                                <option value="Professional Fee">Professional Fee</option>
                                <option value="Department Charges">Department Charges</option>
                                <option value="Surcharge">Surcharge</option>
                                <option value="Disbursements">Disbursements</option>
                                <option value="Other Cost">Other Cost</option>
                                <option value="Discount">Discount</option>

                            </select>
                        </td>
                        <td>
                            <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                        </td>

                        <td>
                            <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                            <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="" />
                        </td>

                        <td>
                            <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>
                        </td>>`;

                //var clonedval_invoice = $('.clonedrow_invoice').html();
                $('.productitem_invoice').append('<tr class="product_field_clone_invoice">'+clonedval_invoice+'</tr>');
                $('.report_date_fields_invoice,.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy', autoclose: true });
                $('.report_entry_date_fields_invoice').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });

        $(document).delegate('.removeitems_invoice', 'click', function(){
            var $tr_invoice    = $(this).closest('.product_field_clone_invoice');
            var trclone_invoice = $('.product_field_clone_invoice').length;
            if(trclone_invoice > 0){
                $tr_invoice.remove();
            }
            grandtotalAccountTab_invoice();
        });

        $(document).delegate('.withdraw_amount_invoice_per_row, .payment_type_invoice_per_row', 'blur', function() {
            grandtotalAccountTab_invoice();
        });

        /*function grandtotalAccountTab_invoice() {
            console.log('grandtotalAccountTab_invoice triggered');
            var total_withdraw_amount_all_rows_invoice = 0;

            // Loop through only visible rows
            $('.productitem_invoice tr:visible').each(function(index) {
                var $row = $(this);

                // Get the withdraw amount from the input field
                var withdrawVal = $row.find('.withdraw_amount_invoice_per_row').val();
                if (withdrawVal) {
                    // Remove currency symbols, commas, and spaces
                    withdrawVal = withdrawVal.replace(/[^0-9.-]+/g, '');
                    var withdrawAmount = parseFloat(withdrawVal) || 0; // Fallback to 0 if NaN
                    console.log('Row ' + (index + 1) + ': withdrawAmount = ' + withdrawAmount);
                    total_withdraw_amount_all_rows_invoice += withdrawAmount;
                } else {
                    console.log('Row ' + (index + 1) + ': No withdraw amount found');
                }
            });

            console.log('Total calculated: ' + total_withdraw_amount_all_rows_invoice);
            $('.total_withdraw_amount_all_rows_invoice').html('$' + total_withdraw_amount_all_rows_invoice.toFixed(2));
        }*/

        function grandtotalAccountTab_invoice() {
            console.log('grandtotalAccountTab_invoice triggered');
            var total_withdraw_amount_all_rows_invoice = 0;

            // Loop through only visible rows
            $('.productitem_invoice tr:visible').each(function(index) {
                var $row = $(this);

                // Get the withdraw amount from the input field
                var withdrawVal = $row.find('.withdraw_amount_invoice_per_row').val();
                // Get the payment type from the select field
                var paymentType = $row.find('select[name="payment_type[]"]').val();

                if (withdrawVal) {
                    // Remove currency symbols, commas, and spaces
                    withdrawVal = withdrawVal.replace(/[^0-9.-]+/g, '');
                    var withdrawAmount = parseFloat(withdrawVal) || 0; // Fallback to 0 if NaN

                    // Adjust total based on payment type
                    if (paymentType === 'Discount') {
                        total_withdraw_amount_all_rows_invoice -= withdrawAmount;
                    } else {
                        total_withdraw_amount_all_rows_invoice += withdrawAmount;
                    }

                    console.log('Row ' + (index + 1) + ': withdrawAmount = ' + withdrawAmount + ', Payment Type = ' + paymentType);
                } else {
                    console.log('Row ' + (index + 1) + ': No withdraw amount found');
                }
            });

            console.log('Total calculated: ' + total_withdraw_amount_all_rows_invoice);
            $('.total_withdraw_amount_all_rows_invoice').html('$' + total_withdraw_amount_all_rows_invoice.toFixed(2));
        }


        //create invoice changes end


        //create office receipt start
        $('.report_date_fields_office').datepicker({ format: 'dd/mm/yyyy', autoclose: true });
        $('.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        $(document).delegate('.openproductrinfo_office', 'click', function(){
            var clonedval_office = $('.clonedrow_office').html();
            $('.productitem_office').append('<tr class="product_field_clone_office">'+clonedval_office+'</tr>');
            $('.report_date_fields_office,.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy', autoclose: true });
            $('.report_entry_date_fields_office').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });

        $(document).delegate('.removeitems_office', 'click', function(){
            var $tr_office    = $(this).closest('.product_field_clone_office');
            var trclone_office = $('.product_field_clone_office').length;
            if(trclone_office > 0){
                $tr_office.remove();
            }
            grandtotalAccountTab_office();
        });

        $(document).delegate('.total_deposit_amount_office', 'keyup', function(){
            grandtotalAccountTab_office();
        });

        function grandtotalAccountTab_office() {
            var total_deposit_amount_all_rows = 0;
            $('.productitem_office tr').each(function() {
                var $row = $(this);

                // Handle deposit amount
                var depositVal = $row.find('.total_deposit_amount_office').val();
                var depositAmount = parseFloat(depositVal) || 0; // fallback to 0 if NaN
                total_deposit_amount_all_rows += depositAmount;
            });

            $('.total_deposit_amount_all_rows_office').html("$" + total_deposit_amount_all_rows.toFixed(2));
        }
        //create office receipt changes end


        //create journal receipt start
        $('.report_date_fields_journal').datepicker({ format: 'dd/mm/yyyy', autoclose: true });
        $('.report_entry_date_fields_journal').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        $(document).delegate('.openproductrinfo_journal', 'click', function(){
            var clonedval_journal = $('.clonedrow_journal').html();
            $('.productitem_journal').append('<tr class="product_field_clone_journal">'+clonedval_journal+'</tr>');
            $('.report_date_fields_journal').datepicker({ format: 'dd/mm/yyyy', autoclose: true });
            $('.report_entry_date_fields_journal').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });

        $(document).delegate('.removeitems_journal', 'click', function(){
            var $tr_journal    = $(this).closest('.product_field_clone_journal');
            var trclone_journal = $('.product_field_clone_journal').length;
            if(trclone_journal > 0){
                $tr_journal.remove();
            }
            grandtotalAccountTab_journal();
        });

        $(document).delegate('.total_withdrawal_amount_journal,.total_deposit_amount_journal', 'keyup', function(){
            grandtotalAccountTab_journal();
        });

        $(document).delegate('.total_withdrawal_amount_journal', 'blur', function(){
            if( $(this).val() != ""){
                var randomNumber = $('#journal_top_value_db').val();
                randomNumber = Number(randomNumber);
                randomNumber = randomNumber + 1; console.log(randomNumber);
                $('#journal_top_value_db').val(randomNumber);
                randomNumber = "Trans"+randomNumber;
                $(this).closest('tr').find('.unique_trans_no_journal').val(randomNumber);
                $(this).closest('tr').find('.unique_trans_no_hidden_journal').val(randomNumber);
            } else {
                $(this).closest('tr').find('.unique_trans_no_journal').val();
                $(this).closest('tr').find('.unique_trans_no_hidden_journal').val();
            }
        });

        $(document).delegate('.total_deposit_amount_journal', 'blur', function(){
            if( $(this).val() != ""){
                var randomNumber = $('#journal_top_value_db').val();
                randomNumber = Number(randomNumber);
                randomNumber = randomNumber + 1; console.log(randomNumber);
                $('#journal_top_value_db').val(randomNumber);
                randomNumber = "Rec"+randomNumber;
                $(this).closest('tr').find('.unique_trans_no_journal').val(randomNumber);
                $(this).closest('tr').find('.unique_trans_no_hidden_journal').val(randomNumber);
            } else {
                $(this).closest('tr').find('.unique_trans_no_journal').val();
                $(this).closest('tr').find('.unique_trans_no_hidden_journal').val();
            }
        });

        function grandtotalAccountTab_journal(){
            var total_withdrawal_amount_all_rows_journal = 0;
            $('.productitem_journal tr').each(function(){
            if($(this).find('.total_withdrawal_amount_journal').val() != ''){
                    var withdrawal_amount_per_row_journal = $(this).find('.total_withdrawal_amount_journal').val();
                }else{
                    var withdrawal_amount_per_row_journal = 0;
                }
                total_withdrawal_amount_all_rows_journal += parseFloat(withdrawal_amount_per_row_journal);
            });
            $('.total_withdraw_amount_all_rows_journal').html("$"+total_withdrawal_amount_all_rows_journal.toFixed(2));
        }
        //create journal receipt changes end

        $('#edu_service_start_date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        $('.filter_btn').on('click', function(){
            $('.filter_panel').slideToggle();
        });

        //Service type on chnage div
        $('.modal-body form#createservicetaken input[name="service_type"]').on('change', function(){
            var invid = $(this).attr('id');
            if(invid == 'Migration_inv'){
                $('.modal-body form#createservicetaken .is_Migration_inv').show();
                $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', 'required');
                $('.modal-body form#createservicetaken .is_Eductaion_inv').hide();
                $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', '');
            }
            else {
                $('.modal-body form#createservicetaken .is_Eductaion_inv').show();
                $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', 'required');
                $('.modal-body form#createservicetaken .is_Migration_inv').hide();
                $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', '');
            }
        });

        //Set select2 drop down box width
        $('#changeassignee').select2();
        $('#changeassignee').next('.select2-container').first().css('width', '220px');

        var windowsize = $(window).width();
        if(windowsize > 2000){
            $('.add_note').css('width','980px');
        }

        /////////////////////////////////////////////
        ////// not picked call button code start /////////
        /////////////////////////////////////////////
        $(document).delegate('.not_picked_call', 'click', function(e){
            var conf = confirm('Are you sure want to proceed this?');
            if(conf){
                var not_picked_call = 1;
                $.ajax({
                    url: '{{URL::to('/admin/not-picked-call')}}',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type:'POST',
                    datatype:'json',
                    data:{id:'{{$fetchedData->id}}',not_picked_call:not_picked_call},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        if(obj.not_picked_call == 1){
                            alert(obj.message);
                            //location.reload();
                        } else {
                            alert(obj.message);
                        }
                        getallactivities();
                    }
                });
            } else {
                return false;
            }
        });

        /////////////////////////////////////////////
        ////// not picked call button code end //////
        /////////////////////////////////////////////

        /////////////////////////////////////////////
        ////// appointment popup chnages start //////
        /////////////////////////////////////////////

        $(document).delegate('.enquiry_item', 'change', function(){
            var id = $(this).val();
            if(id != ""){
                var v = 'services';
                if(id == 8){  //If nature of service == INDIA/UK/CANADA/EUROPE TO AUSTRALIA
                    $('#serviceval_2').hide();
                } else {
                    $('#serviceval_2').show();
                }

                $('.services_row').show();
                $('#myTab .nav-item #nature_of_enquiry-tab').addClass('disabled');
                $('#myTab .nav-item #services-tab').removeClass('disabled');
                $('#myTab a[href="#'+v+'"]').trigger('click');

                $('.services_item').prop('checked', false);
                $('.appointment_row').hide();
                $('.info_row').hide();
                $('.confirm_row').hide();

                $('.timeslots').html('');
                $('.showselecteddate').html('');

                $('#timeslot_col_date').val("");
                $('#timeslot_col_time').val(""); //Do blank Timeslot selected date and time
            } else {
                var v = 'nature_of_enquiry';
                $('.services_row').hide();
                $('.appointment_row').hide();
                $('.info_row').hide();
                $('.confirm_row').hide();

                $('#myTab .nav-item #services-tab').addClass('disabled');
                $('#myTab .nav-item #nature_of_enquiry-tab').removeClass('disabled');
                $('#myTab a[href="#'+v+'"]').trigger('click');
            }
            $('input[name="noe_id"]').val(id);
        });

        $(document).on('change', '.inperson_address', function() {
            var id = $("input[name='inperson_address']:checked").attr('data-val'); //alert(id);
            if(id != ""){
                var v = 'info';
                $('.info_row').show();
                $('.appointment_details_cls').show();

                $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
                $('#myTab .nav-item #info-tab').removeClass('disabled');
                $('#myTab a[href="#'+v+'"]').trigger('click');
            } else {
                var v = 'appointment_details';
                $('.info_row').hide();
                $('.appointment_details_cls').hide();
                $('.confirm_row').hide();

                $('#myTab .nav-item #info-tab').addClass('disabled');
                $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');
                $('#myTab a[href="#'+v+'"]').trigger('click');
            }

            if( $("input[name='radioGroup']:checked").val() == 1  ){ //paid
                $('#promo_code_used').css('display','inline-block');
            } else { //free
                $('#promo_code_used').css('display','none');
            }

            $("input[name='inperson_address']:checked").val(id);
            $('.timeslots').html('');
            if(id != ""){
                var enquiry_item  = $('.enquiry_item').val(); //alert(enquiry_item);
                var service_id = $("input[name='radioGroup']:checked").val(); //alert(service_id);
                var inperson_address = $("input[name='inperson_address']:checked").attr('data-val'); //alert(inperson_address);
                $.ajax({
                    url:'{{URL::to('/getdatetimebackend')}}',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type:'POST',
                    data:{id:service_id, enquiry_item:enquiry_item, inperson_address:inperson_address },
                    datatype:'json',
                    success:function(res){
                        var obj = JSON.parse(res);
                        if(obj.success){
                            duration = obj.duration;
                            daysOfWeek =  obj.weeks;
                            starttime =  obj.start_time;
                            endtime =  obj.end_time;
                            disabledtimeslotes = obj.disabledtimeslotes;
                            var datesForDisable = obj.disabledatesarray;

                            $('#datetimepicker').datepicker({
                                inline: true,
                                startDate: new Date(),
                                datesDisabled: datesForDisable,
                                daysOfWeekDisabled: daysOfWeek,
                                format: 'dd/mm/yyyy'
                            }).on('changeDate', function(e) {
                                var date = e.format();
                                var checked_date=e.date.toLocaleDateString('en-US');

                                $('.showselecteddate').html(date);
                                $('input[name="date"]').val(date);
                                $('#timeslot_col_date').val(date);


                                $('.timeslots').html('');
                                var start_time = parseTime(starttime),
                                end_time = parseTime(endtime),
                                interval = parseInt(duration);
                                var service_id = $("input[name='radioGroup']:checked").val(); //alert(service_id);
                                var inperson_address = $("input[name='inperson_address']:checked").attr('data-val'); //alert(inperson_address);
                                var enquiry_item  = $('.enquiry_item').val(); //alert(enquiry_item);
                                //var slot_overwrite_hidden = $('#slot_overwrite_hidden').val();
                                console.log('slot_overwrite_hidden='+slot_overwrite_hidden)
                                $.ajax({
                                    url:'{{URL::to('/getdisableddatetime')}}',
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    type:'POST',
                                    data:{service_id:service_id,sel_date:date, enquiry_item:enquiry_item,inperson_address:inperson_address},
                                    datatype:'json',
                                    success:function(res){
                                        $('.timeslots').html('');
                                        var obj = JSON.parse(res);
                                        if(obj.success){
                                            console.log('slot_overwrite_hidden='+$('#slot_overwrite_hidden').val() )
                                            if( $('#slot_overwrite_hidden').val() == 1){
                                                var objdisable = [];
                                            } else {
                                                var objdisable = obj.disabledtimeslotes;
                                            }
                                            console.log('objdisable='+objdisable);

                                            var start_timer = start_time;
                                            for(var i = start_time; i<end_time; i = i+interval){
                                                var timeString = start_timer + interval;
                                                // Prepend any date. Use your birthday.
                                                const timeString12hr = new Date('1970-01-01T' + convertHours(start_timer) + 'Z')
                                                .toLocaleTimeString('en-US',
                                                    {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                                );
                                                const timetoString12hr = new Date('1970-01-01T' + convertHours(timeString) + 'Z')
                                                .toLocaleTimeString('en-US',
                                                    {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                                );

                                                var today_date = new Date();
                                                //const options = { timeZone: 'Australia/Sydney'};
                                                today_date = today_date.toLocaleDateString('en-US');

                                                // current time
                                                var now = new Date();
                                                var nowTime = new Date('1/1/1900 ' + now.toLocaleTimeString(navigator.language, {
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                    hour12: true
                                                }));

                                                var current_time=nowTime.toLocaleTimeString('en-US');
                                                if(objdisable.length > 0){
                                                    if(jQuery.inArray(timeString12hr, objdisable) != -1  ) {

                                                    } else if ((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){ console.log('elseee-ifff');
                                                    } else{
                                                        $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');
                                                    }
                                                } else{
                                                    if((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){
                                                    } else {
                                                        $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');
                                                    }
                                                }
                                                start_timer = timeString;
                                            }
                                        }else{

                                        }
                                    }
                                });
                            });

                            if(id != ""){
                                var v = 'appointment_details';
                                $('#myTab .nav-item #services-tab').addClass('disabled');
                                $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');
                                $('#myTab a[href="#'+v+'"]').trigger('click');
                            } else {
                                var v = 'services';
                                $('#myTab .nav-item #services-tab').removeClass('disabled');
                                $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
                                $('#myTab a[href="#'+v+'"]').trigger('click');
                            }
                            $('input[name="service_id"]').val($("input[name='radioGroup']:checked").val());
                        } else {
                            $('input[name="service_id"]').val('');
                            var v = 'services';
                            alert('There is a problem in our system. please try again');
                            $('#myTab .nav-item #services-tab').removeClass('disabled');
                            $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
                        }
                    }
                })
            }
        });

        $(document).delegate('.appointment_item', 'change', function(){
            var id = $(this).val();
            if(id != ""){
                $('input[name="appointment_details"]').val(id);
            } else {
                $('input[name="appointment_details"]').val("");
            }
        });

        $(document).delegate('#promo_code', 'blur', function(){
            var promo_code_val = $(this).val();
            var client_id = '<?php echo $fetchedData->id;?>';
            $.ajax({
                url:'{{URL::to('/admin/promo-code/checkpromocode')}}',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                data:{promo_code_val:promo_code_val, client_id:client_id },
                datatype:'json',
                success:function(res){
                    var obj = JSON.parse(res);
                    if(obj.success == true){
                        $('#promocode_id').val(obj.promocode_id);
                        $('#promo_msg').css('display','block');
                        $('#promo_msg').css('color','green');
                        $('#promo_msg').text(obj.msg);
                        $('#appointform_save').prop('disabled', false);
                    } else {
                        $('#promocode_id').val("");
                        $('#promo_msg').css('display','block');
                        $('#promo_msg').css('color','red');
                        $('#promo_msg').text(obj.msg);
                        $('#appointform_save').prop('disabled', true);
                    }
                }
            });
        });

        $(document).delegate('.services_item', 'change', function(){
            $('.info_row').hide();
            $('.confirm_row').hide();
            $("input[name='inperson_address']").prop("checked", false);
            $('.appointment_item').val("");
            $('.appointment_details_cls').hide();

            $('#timeslot_col_date').val("");
            $('#timeslot_col_time').val(""); //Do blank Timeslot selected date and time

            var id = $(this).val(); console.log('id='+id);
            if ($("input[name='radioGroup'][value='+id+']").prop("checked")) {
                $('#service_id').val(id);
            }

            if( $('#service_id').val() == 1 ){ //paid
                $('#promo_code_used').css('display','inline-block');
                $('.submitappointment_paid').show();
                $('.submitappointment').hide();
            } else { //free
                $('#promo_code_used').css('display','none');
                $('.submitappointment').show();
                $('.submitappointment_paid').hide();
            }

            if(id != ""){
                var v = 'appointment_details';
                if( id == 1 ){ //paid service
                    // Show the "Zoom / Google Meeting" option
                    $('select[name="appointment_details"] option[value="zoom_google_meeting"]').show();
                } else {
                    // Hide the "Zoom / Google Meeting" option
                    $('select[name="appointment_details"] option[value="zoom_google_meeting"]').hide();
                }
                $('.appointment_row').show();
            } else {
                var v = 'services';
                $('.appointment_row').hide();
            }
            $('.timeslots').html('');
            $('.showselecteddate').html('');
        });

        $('.slot_overwrite_time_dropdown').change(function() {
            $('#timeslot_col_time').val("");
            var currentSelVal = $(this).val();
            console.log('currentSelVal='+currentSelVal);
            $('#timeslot_col_time').val(currentSelVal);
        });

        $('#slot_overwrite').change(function() {
            $('#timeslot_col_date').val("");
            $('#timeslot_col_time').val("");
            if ($(this).is(':checked')) { console.log('checked');
                $('#slot_overwrite_hidden').val(1);
                $('.timeslotDivCls').hide();
                $('.slotTimeOverwriteDivCls').show();
            } else { console.log('not-checked');
                $('#slot_overwrite_hidden').val(0);
                $('.timeslotDivCls').show();
                $('.slotTimeOverwriteDivCls').hide();
            }
        });


        $(document).delegate('.nextbtn', 'click', function(){
            var v = $(this).attr('data-steps');
            $(".custom-error").remove();
            var flag = 1;
            if(v == 'confirm'){ //datetime
                $('#sendCodeBtn_txt').html("");
                $('#sendCodeBtn_txt').hide();
                var fullname = $('.fullname').val();
                var email = $('.email').val();
                //var title = $('.title').val();
                var phone = $('.phone').val();
                var description = $('.description').val();
                var timeslot_col_date = $('#timeslot_col_date').val();
                var timeslot_col_time = $('#timeslot_col_time').val();

                //var phoneRegex = /^[0-9]{10,}$/;
                var phoneRegex = /^\+?[0-9]{1,4}[-.\s]?[0-9]{10,}$/;
                // Regular expression to allow only letters and spaces (no special characters)
                var nameRegex = /^[a-zA-Z\s]+$/;

                var appointment_item = $('.appointment_item').val();
                if( !$.trim(appointment_item) ){
                    flag = 0;
                    $('.appointment_item').after('<span class="custom-error" role="alert">Appointment detail is required</span>');
                }
                if( !$.trim(fullname) ){
                    flag = 0;
                    $('.fullname').after('<span class="custom-error" role="alert">Fullname is required</span>');
                }
                else if (!nameRegex.test(fullname)) {
                    flag = 0;
                    // Show error message if fullname contains special characters
                    $('.fullname').after('<span class="custom-error" role="alert">Full name must not contain special characters</span>');
                }
                if( !ValidateEmail(email) ){
                    flag = 0;
                    if(!$.trim(email)){
                        $('.email').after('<span class="custom-error" role="alert">Email is required.</span>');
                    }else{
                        $('.email').after('<span class="custom-error" role="alert">You have entered an invalid email address!</span>');
                    }
                }

                if( !$.trim(phone) ){
                    flag = 0;
                    $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number is required</span>');
                } else if (!phoneRegex.test(phone)) {
                    flag = 0;
                    // Show error message if phone number is not valid (less than 10 digits or contains non-digits)
                    //$('.phone').after('<span class="custom-error" role="alert">Phone number must be at least 10 digits and only contain numbers</span>');
                    $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone must contain extension with phone.</span>');
                } else if( $('#phone_verified_bit').val() != "1" ){
                    flag = 0;
                    $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number is not verified</span>');
                }

                if( !$.trim(description) ){
                    flag = 0;
                    $('.description').after('<span class="custom-error" role="alert">Description is required</span>');
                }
                if( !$.trim(description) ){
                    flag = 0;
                    $('.description').after('<span class="custom-error" role="alert">Description is required</span>');
                }
                if( !$.trim(timeslot_col_date) || !$.trim(timeslot_col_time)  ){
                    flag = 0;
                    $('.timeslot_col_date_time').after('<span class="custom-error" role="alert">Date and Time is required</span>');
                }
            }/*else if(v == 'confirm'){

            }*/
            //alert('flag=='+flag+'---v=='+v);
            if(flag == 1 && v == 'confirm'){
                $('.confirm_row').show();
                $('#myTab .nav-item .nav-link').addClass('disabled');
                $('#myTab .nav-item #'+v+'-tab').removeClass('disabled');
                $('#myTab a[href="#'+v+'"]').trigger('click');

                $('.full_name').text($('.fullname').val());
                $('.email').text($('.email').val());
                //$('.title').text($('.title').val());
                $('.phone').text($('.phone').val());
                $('.description').text($('.description').val());
                $('.date').text($('input[name="date"]').val());
                $('.time').text($('input[name="time"]').val());
                //$('.date').text($('#timeslot_col_date').val());
                //$('.time').text($('#timeslot_col_time').val());

                if(  $("input[name='radioGroup']:checked").val() == 1 ){ //paid
                    $('.submitappointment_paid').show();
                    $('.submitappointment').hide();
                } else { //free
                    $('.submitappointment').show();
                    $('.submitappointment_paid').hide();
                }
            } else {
                $('.confirm_row').hide();
            }

            function ValidateEmail(inputText) {
                var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                if(inputText.match(mailformat)) {
                    return true;
                } else {
                    // alert("You have entered an invalid email address!");
                    return false;
                }
            }
        });

        $(document).delegate('.timeslot_col', 'click', function(){
            $('.timeslot_col').removeClass('active');
            $(this).addClass('active');
            var service_id_val = $("input[name='radioGroup']:checked").val(); //alert(service_id_val);
            var fromtime = $(this).attr('data-fromtime');
            if(service_id_val == 2){ //15 min service
                var fromtime11 = parseTimeLatest(fromtime);
                var interval11 = 15;
                var timeString11 = fromtime11 + interval11;
                var totime = new Date('1970-01-01T' + convertHours(timeString11) + 'Z')
                .toLocaleTimeString('en-US',
                    {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                );
            } else {
                var totime = $(this).attr('data-totime');
            }
            //alert('totime='+totime);
            $('input[name="time"]').val(fromtime+'-'+totime);
            $('#timeslot_col_time').val(fromtime+'-'+totime);
        });

        function parseTime(s) {
            var c = s.split(':');
            return parseInt(c[0]) * 60 + parseInt(c[1]);
        }

        function parseTimeLatest(s) {
            var c = s.split(':');
            var c11 = c[1].split(' ');
            if(c11[1] == 'PM'){
                if(parseInt(c[0]) != 12 ){
                    return ( parseInt(c[0])+12 ) * 60 + parseInt(c[1]);
                } else {
                    return parseInt(c[0]) * 60 + parseInt(c[1]);
                }
            } else {
                return parseInt(c[0]) * 60 + parseInt(c[1]);
            }
        }

        function convertHours(mins){
            var hour = Math.floor(mins/60);
            var mins = mins%60;
            var converted = pad(hour, 2)+':'+pad(mins, 2);
            return converted;
        }

        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }

        function calculate_time_slot(start_time, end_time, interval = "15"){
            var i, formatted_time;
            var time_slots = new Array();
            for(var i=start_time; i<=end_time; i = i+interval){
                formatted_time = convertHours(i);
                const timeString = formatted_time;

                time_slots.push(timeString);
            }
            return time_slots;
        }


        /////////////////////////////////////////////
        ////// appointment popup chnages end /////////
        /////////////////////////////////////////////

        $('.manual_email_phone_verified').on('change', function(){
            if( $(this).is(":checked") ) {
                $('.manual_email_phone_verified').val(1);
                var manual_email_phone_verified = 1;
            } else {
                $('.manual_email_phone_verified').val(0);
                var manual_email_phone_verified = 0;
            }

            var client_id = '<?php echo $fetchedData->id;?>'; //alert(site_url);
            $.ajax({
                url: site_url+'/admin/clients/update-email-verified',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                data:{manual_email_phone_verified:manual_email_phone_verified,client_id:client_id},
                success: function(responses){
                    location.reload();
                }
            });
        });

        //alert('ready');
        $('#feather-icon').click(function(){
            var windowsize = $(window).width(); console.log('windowsize='+windowsize);
            console.log('click'+ $('.main-sidebar').width());
            if($('.main-sidebar').width() == 65){
                if(windowsize > 2000){
                    $('.add_note,.last_updated_date').css('width','980px');
                } else {
                    $('.add_note').css('width','338px');
                    $('.last_updated_date').css('width','348px');
                }

            } else if($('.main-sidebar').width() == 250) {
                if(windowsize > 2000){
                    $('.add_note,.last_updated_date').css('width','1040px');
                } else {
                    $('.add_note').css('width','433px');
                    $('.last_updated_date').css('width','442px');
                }
            }
        });
        //set height of right side section
        var left_upper_height = $('.left_section_upper').height();
        //var left_section_lower = $('.left_section_lower').height();
        var left_section_lower = 0;
        var total_left  = left_upper_height + left_section_lower;
        total_left = total_left +25;

        var right_section_height = $('.right_section').height();
        console.log('total_left='+total_left);
        console.log('right_section_height='+right_section_height);

        //alert(left_upper_height+'==='+left_section_lower+'==='+total_left+'==='+right_section_height);
        if(right_section_height >total_left ){ console.log('ifff');
            var total_left_px = total_left+'px';
            $('.right_section').css({"maxHeight":total_left_px});
            $('.right_section').css({"overflow": 'scroll' });
        } else {  console.log('elseee');
            var total_left_px = total_left+'px';
            $('.right_section').css({"maxHeight":total_left_px});
        }


        let css_property =
            {
                "display": "none",
            }
        $('#create_note_d').hide();
        $('.main-footer').css(css_property);



        $(document).delegate('.uploadmail','click', function(){
            $('#maclient_id').val('<?php echo $fetchedData->id;?>');
            $('#uploadmail').modal('show');
        });

        $(document).delegate('.uploadAndFetchMail','click', function(){
            $('#maclient_id_fetch').val('<?php echo $fetchedData->id;?>');
            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();
            $('#upload_inbox_mail_client_matter_id').val(hidden_client_matter_id);
            $('#uploadAndFetchMailModel').modal('show');
        });



        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Handle form submission via AJAX
        $('#createForm956').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        // Hide the modal
                        $('#form956CreateFormModel').modal('hide');
                        // Reload the page to reflect the new data
                        location.reload();
                    }
                },
                error: function(xhr) {
                    $('.custom-error-msg').html('');
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $('.custom-error-msg').append('<p class="text-red-600">' + errors[field][0] + '</p>');
                    }
                }
            });
        });

        // Handle not lodged checkbox
        document.querySelector('input[name="not_lodged"]').addEventListener('change', function() {
            const dateLodgedInput = document.getElementById('date_lodged');
            dateLodgedInput.disabled = this.checked;
            if (this.checked) {
                dateLodgedInput.value = '';
            }
        });

        // Populate agent details when the modal opens
        $(document).delegate('.form956CreateForm', 'click', function() {
            $('#form956_client_id').val('<?php echo $fetchedData->id; ?>');
            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();
            console.log('hidden_client_matter_id='+hidden_client_matter_id);
            $('#form956_client_matter_id').val(hidden_client_matter_id);

            getMigrationAgentDetail(hidden_client_matter_id);
            $('#form956CreateFormModel').modal('show');
        });

        //Get Migration Agent Detail
        function getMigrationAgentDetail(client_matter_id) {
            $.ajax({
                type:'post',
                url: '{{URL::to('/admin/clients/getMigrationAgentDetail')}}',
                sync:true,
                data: {client_matter_id:client_matter_id},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.agentInfo){
                        $('#agent_id').val(obj.agentInfo.agentId);
                        if(obj.agentInfo.last_name != ''){
                            var agentFullName = obj.agentInfo.first_name+' '+obj.agentInfo.last_name;
                        } else {
                            var agentFullName =  obj.agentInfo.first_name;
                        }
                        $('#agent_name').val(agentFullName);
                        $('#agent_name_label').html(agentFullName);

                        $('#business_name').val(obj.agentInfo.company_name);
                        $('#business_name_label').html(obj.agentInfo.company_name);

                        $('#application_type').val(obj.matterInfo.title);
                        $('#application_type_label').html(obj.matterInfo.title);
                    }
                }
            });
        }

        // Direct AJAX submission for visa agreement
        /*$(document).delegate('.visaAgreementCreateForm', 'click', function() {
            var client_id = '<?php echo $fetchedData->id; ?>';
            var client_matter_id = $('#sel_matter_id_client_detail').val();
            $.ajax({
                url: '{{URL::to('/admin/clients/check-cost-assignment')}}',
                type: "POST",
                data: {
                    client_id: client_id,
                    client_matter_id: client_matter_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.exists) {
                        $('#visa_agreement_client_id').val(client_id);
                        $('#visa_agreement_client_matter_id').val(client_matter_id);
                        getVisaAggreementMigrationAgentDetail(client_matter_id);
                        $('#visaAgreementCreateFormModel').modal('show');
                    } else {
                        alert("Please first create Cost Assignment.");
                    }
                }
            });
        });*/

        $(document).delegate('.visaAgreementCreateForm', 'click', function() {
            var client_id = '<?php echo $fetchedData->id; ?>';
            var client_matter_id = $('#sel_matter_id_client_detail').val();

            // First check if cost assignment exists
            $.ajax({
                url: '{{URL::to('/admin/clients/check-cost-assignment')}}',
                type: "POST",
                data: {
                    client_id: client_id,
                    client_matter_id: client_matter_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.exists) {
                        // Get agent details and then submit via AJAX
                        $.ajax({
                            type: 'post',
                            url: '{{URL::to('/admin/clients/getVisaAggreementMigrationAgentDetail')}}',
                            data: {client_matter_id: client_matter_id},
                            success: function(agentResponse) {
                                var obj = $.parseJSON(agentResponse);
                                if(obj.agentInfo) {
                                    // Prepare form data for AJAX submission
                                    var formData = {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        client_id: client_id,
                                        client_matter_id: client_matter_id,
                                        agent_id: obj.agentInfo.agentId,
                                        agent_name: obj.agentInfo.last_name != '' ?
                                            obj.agentInfo.first_name + ' ' + obj.agentInfo.last_name :
                                            obj.agentInfo.first_name,
                                        business_name: obj.agentInfo.company_name || ''
                                    };

                                    // Submit via AJAX
                                    $.ajax({
                                        url: '{{route("clients.generateagreement")}}',
                                        method: 'POST',
                                        data: formData,
                                        success: function(response) {
                                            // Handle successful response
                                            if (response.success && response.download_url) {
                                                // Create a temporary link to trigger download
                                                var link = document.createElement('a');
                                                link.href = response.download_url;
                                                link.download = 'visa_agreement.docx';
                                                document.body.appendChild(link);
                                                link.click();
                                                document.body.removeChild(link);

                                                // Show success message
                                                alert('Visa agreement generated successfully!');
                                            } else {
                                                alert('Document generated but no download URL returned.');
                                            }
                                        },
                                        error: function(xhr) {
                                            // Handle errors
                                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                                alert('Error: ' + xhr.responseJSON.message);
                                            } else {
                                                alert('Error generating visa agreement.');
                                            }
                                        }
                                    });
                                } else {
                                    alert("Agent information not found.");
                                }
                            },
                            error: function() {
                                alert("Error fetching agent details.");
                            }
                        });
                    } else {
                        alert("Please first create Cost Assignment.");
                    }
                },
                error: function() {
                    alert("Error checking cost assignment.");
                }
            });
        });

         //Get Visa agreement Migration Agent Detail
        function getVisaAggreementMigrationAgentDetail(client_matter_id) {
            $.ajax({
                type:'post',
                url: '{{URL::to('/admin/clients/getVisaAggreementMigrationAgentDetail')}}',
                sync:true,
                data: {client_matter_id:client_matter_id},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.agentInfo){
                        $('#visaagree_agent_id').val(obj.agentInfo.agentId);
                        if(obj.agentInfo.last_name != ''){
                            var agentFullName = obj.agentInfo.first_name+' '+obj.agentInfo.last_name;
                        } else {
                            var agentFullName =  obj.agentInfo.first_name;
                        }
                        $('#visaagree_agent_name').val(agentFullName);
                        $('#visaagree_agent_name_label').html(agentFullName);

                        $('#visaagree_business_name').val(obj.agentInfo.company_name);
                        $('#visaagree_business_name_label').html(obj.agentInfo.company_name);
                    }
                }
            });
        }

        // Handle form submission via AJAX
        $('#visaagreementform11').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Hide modal if needed
                    $('#visaAgreementCreateFormModel').modal('hide');

                    // Redirect to download URL
                    if (response.download_url) {
                        window.location.href = response.download_url;
                    } else {
                        alert('Document generated but no download URL returned.');
                    }
                },
                error: function(xhr) {
                    $('.custom-error-msg').html('');
                    let errors = xhr.responseJSON?.errors || {};
                    for (let field in errors) {
                        $('.custom-error-msg').append('<p class="text-red-600">' + errors[field][0] + '</p>');
                    }
                }
            });
        });


        // Populate agent details when the modal opens
        $(document).delegate('.costAssignmentCreateForm', 'click', function() {
            $('#cost_assignment_client_id').val('<?php echo $fetchedData->id; ?>');
            var hidden_client_matter_id_assignment = $('#sel_matter_id_client_detail').val();
            $('#cost_assignment_client_matter_id').val(hidden_client_matter_id_assignment);
            getCostAssignmentMigrationAgentDetail('<?php echo $fetchedData->id; ?>',hidden_client_matter_id_assignment);
            $('#costAssignmentCreateFormModel').modal('show');
        });

         //Get Cost assignment Migration Agent Detail
        function getCostAssignmentMigrationAgentDetail(client_id,client_matter_id) {
            $.ajax({
                type:'post',
                url: '{{URL::to('/admin/clients/getCostAssignmentMigrationAgentDetail')}}',
                sync:true,
                data: {client_id:client_id,client_matter_id:client_matter_id},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.agentInfo){
                        $('#costassign_agent_id').val(obj.agentInfo.agentId);
                        if(obj.agentInfo.last_name != ''){
                            var agentFullName = obj.agentInfo.first_name+' '+obj.agentInfo.last_name;
                        } else {
                            var agentFullName =  obj.agentInfo.first_name;
                        }
                        //$('#costassign_agent_name').val(agentFullName);
                        $('#costassign_agent_name_label').html(agentFullName);

                        //$('#costassign_business_name').val(obj.agentInfo.company_name);
                        $('#costassign_business_name_label').html(obj.agentInfo.company_name);
                        $('#costassign_client_matter_name_label').html(obj.matterInfo.title);

                        //Fetch matter related cost assignments
                        if(obj.cost_assignment_matterInfo){
                            $('#surcharge').val(obj.cost_assignment_matterInfo.surcharge);
                            $('#Dept_Base_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Base_Application_Charge);
                            $('#Dept_Base_Application_Charge_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Base_Application_Charge_no_of_person);

                            $('#Dept_Non_Internet_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Non_Internet_Application_Charge);
                            $('#Dept_Non_Internet_Application_Charge_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Non_Internet_Application_Charge_no_of_person);

                            $('#Dept_Additional_Applicant_Charge_18_Plus').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_18_Plus);
                            $('#Dept_Additional_Applicant_Charge_18_Plus_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_18_Plus_no_of_person);

                            $('#Dept_Additional_Applicant_Charge_Under_18').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18);
                            $('#Dept_Additional_Applicant_Charge_Under_18_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18_no_of_person);

                            $('#Dept_Subsequent_Temp_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Subsequent_Temp_Application_Charge);
                            $('#Dept_Subsequent_Temp_Application_Charge_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18_no_of_person);

                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus);
                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person);

                            $('#Dept_Second_VAC_Instalment_Under_18').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Under_18);
                            $('#Dept_Second_VAC_Instalment_Under_18_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Under_18_no_of_person);

                            $('#Dept_Nomination_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Nomination_Application_Charge);
                            $('#Dept_Sponsorship_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Sponsorship_Application_Charge);

                            $('#TotalDoHACharges').val(obj.cost_assignment_matterInfo.TotalDoHACharges);
                            $('#TotalDoHASurcharges').val(obj.cost_assignment_matterInfo.TotalDoHASurcharges);

                            $('#Block_1_Ex_Tax').val(obj.cost_assignment_matterInfo.Block_1_Ex_Tax);
                            $('#Block_2_Ex_Tax').val(obj.cost_assignment_matterInfo.Block_2_Ex_Tax);
                            $('#Block_3_Ex_Tax').val(obj.cost_assignment_matterInfo.Block_3_Ex_Tax);

                            $('#additional_fee_1').val(obj.cost_assignment_matterInfo.additional_fee_1);
                            $('#TotalBLOCKFEE').val(obj.cost_assignment_matterInfo.TotalBLOCKFEE);
                        } else {
                            $('#surcharge').val(obj.matterInfo.surcharge);
                            $('#Dept_Base_Application_Charge').val(obj.matterInfo.Dept_Base_Application_Charge);
                            $('#Dept_Non_Internet_Application_Charge').val(obj.matterInfo.Dept_Non_Internet_Application_Charge);
                            $('#Dept_Additional_Applicant_Charge_18_Plus').val(obj.matterInfo.Dept_Additional_Applicant_Charge_18_Plus);
                            $('#Dept_Additional_Applicant_Charge_Under_18').val(obj.matterInfo.Dept_Additional_Applicant_Charge_Under_18);
                            $('#Dept_Subsequent_Temp_Application_Charge').val(obj.matterInfo.Dept_Subsequent_Temp_Application_Charge);
                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus').val(obj.matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus);
                            $('#Dept_Second_VAC_Instalment_Under_18').val(obj.matterInfo.Dept_Second_VAC_Instalment_Under_18);
                            $('#Dept_Nomination_Application_Charge').val(obj.matterInfo.Dept_Nomination_Application_Charge);
                            $('#Dept_Sponsorship_Application_Charge').val(obj.matterInfo.Dept_Sponsorship_Application_Charge);

                            $('#Block_1_Ex_Tax').val(obj.matterInfo.Block_1_Ex_Tax);
                            $('#Block_2_Ex_Tax').val(obj.matterInfo.Block_2_Ex_Tax);
                            $('#Block_3_Ex_Tax').val(obj.matterInfo.Block_3_Ex_Tax);

                            $('#additional_fee_1').val(obj.matterInfo.additional_fee_1);
                            $('#TotalBLOCKFEE').val(obj.matterInfo.TotalBLOCKFEE);
                            $('#TotalDoHACharges').val(obj.matterInfo.TotalDoHACharges);
                            $('#TotalDoHASurcharges').val(obj.matterInfo.TotalDoHASurcharges);
                        }
                    }
                }
            });
        }

        // Handle form submission via AJAX
        $(document).on('submit', '#costAssignmentform', function(e) {
            e.preventDefault();
            console.log('Form submitted!'); // Debug log
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    console.log('Success response:', response); // Debug log
                    // Check if response is already an object or needs parsing
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    if (obj.status) {
                        // Hide the modal
                        $('#costAssignmentCreateFormModel').modal('hide');
                        // Reload the page to reflect the new data
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', xhr, status, error); // Debug log
                    $('.custom-error-msg').html('');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $('.custom-error-msg').append('<p class="text-red-600">' + errors[field][0] + '</p>');
                        }
                    } else {
                        $('.custom-error-msg').append('<p class="text-red-600">An error occurred while submitting the form.</p>');
                    }
                }
            });
        });

        // Alternative approach: Handle button click directly
        $(document).on('click', '#costAssignmentform button[type="submit"]', function(e) {
            e.preventDefault();
            console.log('Submit button clicked!'); // Debug log
            var form = $('#costAssignmentform');
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    console.log('Success response:', response); // Debug log
                    // Check if response is already an object or needs parsing
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    if (obj.status) {
                        // Hide the modal
                        $('#costAssignmentCreateFormModel').modal('hide');
                        // Reload the page to reflect the new data
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', xhr, status, error); // Debug log
                    $('.custom-error-msg').html('');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $('.custom-error-msg').append('<p class="text-red-600">' + errors[field][0] + '</p>');
                        }
                    } else {
                        $('.custom-error-msg').append('<p class="text-red-600">An error occurred while submitting the form.</p>');
                    }
                }
            });
        });

        //Lead Section Start
            // Populate agent details when the modal opens
            $(document).delegate('.costAssignmentCreateFormLead', 'click', function() {
                $('#cost_assignment_lead_id').val('<?php echo $fetchedData->id; ?>');
                $('#sel_migration_agent_id_lead,#sel_person_responsible_id_lead,#sel_person_assisting_id_lead,#sel_matter_id_lead').select2({
                    dropdownParent: $('#costAssignmentCreateFormModelLead')
                });
                $('#costAssignmentCreateFormModelLead').modal('show');
            });

            $(document).delegate('#sel_matter_id_lead','change', function(){
                var client_matter_id = $(this).val();
                var client_id = '<?php echo $fetchedData->id; ?>';
                if (client_id && client_matter_id) {
                    getCostAssignmentMigrationAgentDetailLead(client_id, client_matter_id);
                }
            });

            //Get Cost assignment Migration Agent Detail
            function getCostAssignmentMigrationAgentDetailLead(client_id,client_matter_id) {
                $.ajax({
                    type:'post',
                    url: '{{URL::to('/admin/clients/getCostAssignmentMigrationAgentDetailLead')}}',
                    sync:true,
                    data: {client_id:client_id,client_matter_id:client_matter_id},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        //Fetch matter related cost assignments
                        if(obj.cost_assignment_matterInfo){
                            $('#surcharge_lead').val(obj.cost_assignment_matterInfo.surcharge);
                            $('#Dept_Base_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Base_Application_Charge);
                            $('#Dept_Base_Application_Charge_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Base_Application_Charge_no_of_person);

                            $('#Dept_Non_Internet_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Non_Internet_Application_Charge);
                            $('#Dept_Non_Internet_Application_Charge_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Non_Internet_Application_Charge_no_of_person);

                            $('#Dept_Additional_Applicant_Charge_18_Plus_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_18_Plus);
                            $('#Dept_Additional_Applicant_Charge_18_Plus_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_18_Plus_no_of_person);

                            $('#Dept_Additional_Applicant_Charge_Under_18_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18);
                            $('#Dept_Additional_Applicant_Charge_Under_18_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18_no_of_person);

                            $('#Dept_Subsequent_Temp_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Subsequent_Temp_Application_Charge);
                            $('#Dept_Subsequent_Temp_Application_Charge_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18_no_of_person);

                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus_lead').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus);
                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person);

                            $('#Dept_Second_VAC_Instalment_Under_18_lead').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Under_18);
                            $('#Dept_Second_VAC_Instalment_Under_18_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Under_18_no_of_person);

                            $('#Dept_Nomination_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Nomination_Application_Charge);
                            $('#Dept_Sponsorship_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Sponsorship_Application_Charge);

                            $('#TotalDoHACharges_lead').val(obj.cost_assignment_matterInfo.TotalDoHACharges);
                            $('#TotalDoHASurcharges_lead').val(obj.cost_assignment_matterInfo.TotalDoHASurcharges);

                            $('#Block_1_Ex_Tax_lead').val(obj.cost_assignment_matterInfo.Block_1_Ex_Tax);
                            $('#Block_2_Ex_Tax_lead').val(obj.cost_assignment_matterInfo.Block_2_Ex_Tax);
                            $('#Block_3_Ex_Tax_lead').val(obj.cost_assignment_matterInfo.Block_3_Ex_Tax);

                            $('#additional_fee_1_lead').val(obj.cost_assignment_matterInfo.additional_fee_1);
                            $('#TotalBLOCKFEE_lead').val(obj.cost_assignment_matterInfo.TotalBLOCKFEE);
                        }
                        else {
                            $('#surcharge_lead').val(obj.matterInfo.surcharge);
                            $('#Dept_Base_Application_Charge_lead').val(obj.matterInfo.Dept_Base_Application_Charge);
                            $('#Dept_Non_Internet_Application_Charge_lead').val(obj.matterInfo.Dept_Non_Internet_Application_Charge);
                            $('#Dept_Additional_Applicant_Charge_18_Plus_lead').val(obj.matterInfo.Dept_Additional_Applicant_Charge_18_Plus);
                            $('#Dept_Additional_Applicant_Charge_Under_18_lead').val(obj.matterInfo.Dept_Additional_Applicant_Charge_Under_18);
                            $('#Dept_Subsequent_Temp_Application_Charge_lead').val(obj.matterInfo.Dept_Subsequent_Temp_Application_Charge);
                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus_lead').val(obj.matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus);
                            $('#Dept_Second_VAC_Instalment_Under_18_lead').val(obj.matterInfo.Dept_Second_VAC_Instalment_Under_18);
                            $('#Dept_Nomination_Application_Charge_lead').val(obj.matterInfo.Dept_Nomination_Application_Charge);
                            $('#Dept_Sponsorship_Application_Charge_lead').val(obj.matterInfo.Dept_Sponsorship_Application_Charge);

                            $('#Block_1_Ex_Tax_lead').val(obj.matterInfo.Block_1_Ex_Tax);
                            $('#Block_2_Ex_Tax_lead').val(obj.matterInfo.Block_2_Ex_Tax);
                            $('#Block_3_Ex_Tax_lead').val(obj.matterInfo.Block_3_Ex_Tax);

                            $('#additional_fee_1_lead').val(obj.matterInfo.additional_fee_1);
                            $('#TotalBLOCKFEE_lead').val(obj.matterInfo.TotalBLOCKFEE);
                            $('#TotalDoHACharges_lead').val(obj.matterInfo.TotalDoHACharges);
                            $('#TotalDoHASurcharges_lead').val(obj.matterInfo.TotalDoHASurcharges);
                        }
                    }
                });
            }
        //Lead Section End

        //Open Agreement model window
        $(document).delegate('.finalizeAgreementConvertToPdf', 'click', function() {
            var hidden_client_matter_id_assignment = $('#sel_matter_id_client_detail').val();
            $('#agreemnt_clientmatterid').val(hidden_client_matter_id_assignment);
            $('#agreementModal').modal('show');
        });

        $(document).on('submit', '#agreementUploadForm', function(e) {
            console.log('Form submit event triggered');
            e.preventDefault();
            console.log('Form prevented default');
            var formData = new FormData(this);
            console.log('FormData created');
            $('.popuploader').show();
            console.log('Starting AJAX request to:', '{{ route("clients.uploadAgreement", $fetchedData->id) }}');
            $.ajax({
                url: '{{ route("clients.uploadAgreement", $fetchedData->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(response) {
                    console.log('AJAX success response:', response);
                    $('.popuploader').hide();
                    if(response.status){
                        $('#agreementModal').modal('hide');
                        //alert('Agreement uploaded and converted successfully!');
                        $('.custom-error-msg').html('<span class="alert alert-success">'+response.message+'</span>');
                        location.reload();
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                    console.log('XHR response:', xhr.responseText);
                    $('.popuploader').hide();
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        $('.custom-error-msg').html('<span class="alert alert-danger">Error: ' + xhr.responseJSON.message + '</span>');
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">An error occurred while uploading the agreement.</span>');
                    }
                }
            });
        });

        // Backup click handler for the submit button
        $(document).on('click', '#agreementUploadForm button[type="submit"]', function(e) {
            console.log('Submit button clicked');
            e.preventDefault();
            var form = $('#agreementUploadForm')[0];
            var formData = new FormData(form);
            $('.popuploader').show();
            console.log('Starting AJAX request from button click');
            $.ajax({
                url: '{{ route("clients.uploadAgreement", $fetchedData->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(response) {
                    console.log('AJAX success response from button click:', response);
                    $('.popuploader').hide();
                    if(response.status){
                        $('#agreementModal').modal('hide');
                        $('.custom-error-msg').html('<span class="alert alert-success">'+response.message+'</span>');
                        location.reload();
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error from button click:', status, error);
                    console.log('XHR response from button click:', xhr.responseText);
                    $('.popuploader').hide();
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        $('.custom-error-msg').html('<span class="alert alert-danger">Error: ' + xhr.responseJSON.message + '</span>');
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">An error occurred while uploading the agreement.</span>');
                    }
                }
            });
        });

        $(document).delegate('.uploadSentAndFetchMail','click', function(){
            $('#maclient_id_fetch_sent').val('<?php echo $fetchedData->id;?>');
            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();
            $('#upload_sent_mail_client_matter_id').val(hidden_client_matter_id);
            $('#uploadSentAndFetchMailModel').modal('show');
        });

        $(document).delegate('.addnewprevvisa','click', function(){
            var $clone = $('.multiplevisa:eq(0)').clone(true,true);

            $clone.find('.lastfiledcol').after('<div class="col-md-4"><a href="javascript:;" class="removenewprevvisa btn btn-danger btn-sm">Remove</a></div>');
            $clone.find("input:text").val("");
            $clone.find("input.visadatesse").val("");
            $('.multiplevisa:last').after($clone);
        });

        $('#note_deadline_checkbox').on('click', function() {
            if ($(this).is(':checked')) {
                $('#note_deadline').prop('disabled', false);
                $('#note_deadline_checkbox').val(1);
            } else {
                $('#note_deadline').prop('disabled', true);
                $('#note_deadline_checkbox').val(0);
            }
        });

        $('#noteType').on('change', function() {
            var selectedValue = $(this).val();
            var additionalFields = $("#additionalFields");

            // Clear any existing fields
            additionalFields.html("");

            if(selectedValue === "Call") {
                additionalFields.append(`
                    <div class="form-group" style="margin-top:10px;">
                        <label for="mobileNumber">Mobile Number:</label>
                        <select name="mobileNumber" id="mobileNumber" class="form-control" data-valid="required"></select>
                        <span id="mobileNumberError" class="text-danger"></span>
                    </div>
                `);

                //Fetch all contact list of any client at create note popup
                var client_id = $('#client_id').val();
                $('.popuploader').show();
                $.ajax({
                    url: "{{URL::to('/admin/clients/fetchClientContactNo')}}",
                    method: "POST",
                    data: {client_id:client_id},
                    datatype: 'json',
                    success: function(response) {
                        $('.popuploader').hide();
                        var obj = $.parseJSON(response); console.log(obj.clientContacts);
                        var contactlist = '<option value="">Select Contact</option>';
                        $.each(obj.clientContacts, function(index, subArray) {
                            contactlist += '<option value="'+subArray.phone+'">'+subArray.phone+'</option>';
                        });
                        $('#mobileNumber').append(contactlist);
                    }
                });

                // Add validation for only digits and max 10 digits
                /*$("#mobileNumber").on("input", function() {
                    var mobileNumber = $(this).val();
                    var digitOnly = /^[0-9]*$/;

                    if (!digitOnly.test(mobileNumber)) {
                        $("#mobileNumberError").text("Please enter only digits.");
                    } else if (mobileNumber.length > 10) {
                        $("#mobileNumberError").text("Mobile number cannot exceed 10 digits.");
                    } else {
                        $("#mobileNumberError").text("");
                    }
                });*/
            }
        });


        var activeLink = $('.nav-link.active');
        if (activeLink.length > 0) {
            var href = activeLink.attr('href');
            if(href == '#activities' ) {
                $('.filter_btn').css('display','inline-block');
                $('.filter_panel').css('display','none');
            } else {
                $('.filter_btn,.filter_panel').css('display','none');
            }
        } else {
            $('.filter_btn,.filter_panel').css('display','none');
        }


        $(document).delegate('.nav-link','click', function(){
            var activeLink = $('.nav-link.active');
            if (activeLink.length > 0) {
                var href = activeLink.attr('href');
                if(href == '#activities' ) {
                    $('.filter_btn').css('display','inline-block');
                    $('.filter_panel').css('display','none');
                } else {
                    $('.filter_btn,.filter_panel').css('display','none');
                }
            } else {
                $('.filter_btn,.filter_panel').css('display','none');
            }
        });

        $(document).delegate('.btn-assignuser','click', function(){
            //$('#create_note_d').modal('hide');
            //$('#myModal textarea').prop('disabled', false);
            var note_description = $('#note_description').val();
            // Remove <p> tags using regex
            var cleanedText = note_description.replace(/<\/?p>/g, '');
        // cleanedText = cleanedText.replace(/<\/?p>/g, '');
            $('#assignnote').val(cleanedText);
        });



        $(document).delegate('.removenewprevvisa','click', function(){
            $(this).parent().parent().parent().remove();
        });

        // assignUser function is now handled in addclientmodal.blade.php to avoid conflicts
        $(document).on('click', '#assignUser', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            $(".popuploader").show();
            let flag = true;
            let error = "";
            $(".custom-error").remove();

            // Get all checked assignee IDs from checkboxes
            let selectedAssignees = [];
            $('.checkbox-item:checked').each(function() {
                selectedAssignees.push($(this).val());
            });
            
            var selectedValues = selectedAssignees; // Use the checked values
            console.log('Selected assignees:', selectedValues);

            // Validation - Check if at least one assignee is selected
            if (selectedAssignees.length === 0) {
                $('.popuploader').hide();
                error = "At least one assignee must be selected.";
                $('#dropdownMenuButton').after("<span class='custom-error' role='alert' style='color: red; font-size: 12px; display: block; margin-top: 5px;'>" + error + "</span>");
                flag = false;
            }
            
            if ($('#assignnote').val().trim() === '') {
                $('.popuploader').hide();
                error = "Note field is required.";
                $('#assignnote').after("<span class='custom-error' role='alert' style='color: red; font-size: 12px; display: block; margin-top: 5px;'>" + error + "</span>");
                flag = false;
            }
            
            if ($('#task_group').val() === '') {
                $('.popuploader').hide();
                error = "Group field is required.";
                $('#task_group').after("<span class='custom-error' role='alert' style='color: red; font-size: 12px; display: block; margin-top: 5px;'>" + error + "</span>");
                flag = false;
            }

            if (flag) {
                $.ajax({
                    type: 'POST',
                    url: "{{URL::to('/')}}/admin/clients/followup/store",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        note_type: 'follow_up',
                        description: $('#assignnote').val(),
                        client_id: $('#assign_client_id').val(),
                        followup_datetime: $('#popoverdatetime').val(),
                        rem_cat: selectedValues,
                        task_group: $('#task_group option:selected').val(),
                        note_deadline_checkbox: $('#note_deadline_checkbox').val(),
                        note_deadline: $('#note_deadline').val()
                    },
                    success: function(response) {
                        $('.popuploader').hide();
                        $('#create_action_popup').modal('hide');
                        var obj = $.parseJSON(response);
                        if (obj.success) {
                            $("[data-role=popover]").each(function() {
                                (($(this).popover('hide').data('bs.popover') || {}).inState || {}).click = false; // fix for BS 3.3.6
                            });
                            // Call the functions to refresh the data
                            if (typeof getallactivities === 'function') {
                                getallactivities();
                            }
                            if (typeof getallnotes === 'function') {
                                getallnotes();
                            }
                        } else {
                            // Handle failure
                            alert('Error: ' + (obj.message || 'Something went wrong'));
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.popuploader').hide();
                        console.error('Ajax error:', error);
                        alert('Error: ' + error);
                    }
                });
            } else {
                $('.popuploader').hide();
            }
        });

        $(document).delegate('.opentaskview', 'click', function(){
            $('#opentaskview').modal('show');
            var v = $(this).attr('id');
            $.ajax({
                url: site_url+'/admin/get-task-detail',
                type:'GET',
                data:{task_id:v},
                success: function(responses){

                    $('.taskview').html(responses);
                }
            });
        });

        function getallnotes(){
            $.ajax({
                url: site_url+'/admin/get-notes',
                type:'GET',
                data:{clientid:'{{$fetchedData->id}}',type:'client'},
                success: function(responses){
                    $('.popuploader').hide();
                    $('.note_term_list').html(responses);
                }
            });
        }

        function getallactivities(){
            $.ajax({
                url: site_url+'/admin/get-activities',
                type:'GET',
                datatype:'json',
                data:{id:'{{$fetchedData->id}}'},
                success: function(responses){
                    /*var ress = JSON.parse(responses);
                    var html = '';
                    $.each(ress.data, function(k, v) {
                        if(v.subject != ""){
                            if(v.subject === null){
                                var subject =  "";
                            } else {
                                var subject =  v.subject;
                            }
                        } else {
                            var subject =  "";
                        }

                        if(v.pin == 1){
                            html += '<div class="activity" id="activity_'+v.activity_id+'" ><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail" style="border: 1px solid #dbdbdb;"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> '+ subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div><div class="right" style="float: right;margin-top: -40px;"><div class="pined_note"><i class="fa fa-thumbtack" style="font-size: 12px;color: #6777ef;"></i></div><div class="dropdown d-inline dropdown_ellipsis_icon"><a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a><div class="dropdown-menu"><a data-id="'+v.activity_id+'" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a><a data-id="'+v.activity_id+'"  class="dropdown-item pinactivitylog" href="javascript:;" >UnPin</a></div></div></div>';
                        } else {
                            html += '<div class="activity" id="activity_'+v.activity_id+'" ><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail" style="border: 1px solid #dbdbdb;"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> '+ subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div><div class="right" style="float: right;margin-top: -40px;"><div class="dropdown d-inline dropdown_ellipsis_icon"><a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a><div class="dropdown-menu"><a data-id="'+v.activity_id+'" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a><a data-id="'+v.activity_id+'"  class="dropdown-item pinactivitylog" href="javascript:;" >Pin</a></div></div></div>';
                        }

                        if(v.message != null){
                            html += '<p>'+v.message+'</p>';
                        }
                        if(v.followup_date != null){
                            html += '<p>'+v.followup_date+'</p>';
                        }
                        if(v.task_group != null){
                            html += '<p>'+v.task_group+'</p>';
                        }
                        html += '</div></div>';
                    }); */

                    var ress = JSON.parse(responses);
                    var html = '';

                    $.each(ress.data, function (k, v) {
                        var subjectIcon = v.subject && v.subject.toLowerCase().includes("document")
                            ? '<i class="fas fa-file-alt"></i>'
                            : '<i class="fas fa-sticky-note"></i>';

                        var subject = v.subject ?? '';
                        var description = v.message ?? '';
                        var taskGroup = v.task_group ?? '';
                        var followupDate = v.followup_date ?? '';
                        var date = v.date ?? '';
                        var createdBy = v.createdname ?? 'Unknown';
                        var fullName = v.name ?? '';

                        html += `
                            <li class="feed-item feed-item--email activity" id="activity_${v.activity_id}">
                                <span class="feed-icon">
                                    ${subjectIcon}
                                </span>
                                <div class="feed-content">
                                    <p><strong>${fullName} ${subject}</strong></p>
                                    ${description !== '' ? `<p>${description}</p>` : ''}
                                    ${taskGroup !== '' ? `<p>${taskGroup}</p>` : ''}
                                    ${followupDate !== '' ? `<p>${followupDate}</p>` : ''}
                                    <span class="feed-timestamp">${date}</span>
                                </div>
                            </li>
                        `;
                    });

                    $('.feed-list').html(html);
                    //$('.activities').html(html);
                    $('.popuploader').hide();
                }
            });
        }

        var appcid = '';
        $(document).delegate('.publishdoc', 'click', function(){
            $('#confirmpublishdocModal').modal('show');
            appcid = $(this).attr('data-id');
        });

        $(document).delegate('.openassigneeshow', 'click', function(){
            $('.assigneeshow').show();
        });

        $(document).delegate('.closeassigneeshow', 'click', function(){
            $('.assigneeshow').hide();
        });

        $(document).delegate('.saveassignee', 'click', function(){
            var appliid = $(this).attr('data-id');
            $('.popuploader').show();
            $.ajax({
                url: site_url+'/admin/clients/change_assignee',
                type:'GET',
                data:{id: appliid,assinee: $('#changeassignee').val()},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        alert(obj.message);
                        location.reload();
                    }else{
                        alert(obj.message);
                    }
                }
            });
        });

        $(document).delegate('#confirmpublishdocModal .acceptpublishdoc', 'click', function(){
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/')}}/'+'application/publishdoc',
                type:'GET',
                datatype:'json',
                data:{appid:appcid,status:'1'},
                success:function(response){
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    $('#confirmpublishdocModal').modal('hide');
                    if(res.status){
                        $('.mychecklistdocdata').html(res.doclistdata);
                    }else{
                        alert(res.message);
                    }
                }
            });
        });

        var verify_doc_id = '';
        var verify_doc_href = '';
        var verify_doc_type = '';

        $(document).delegate('.verifydoc', 'click', function(){
            $('#confirmDocModal').modal('show');
            verify_doc_id = $(this).attr('data-id');
            verify_doc_href = $(this).attr('data-href');
            verify_doc_type = $(this).attr('data-doctype');
        });

        $(document).delegate('#confirmDocModal .accept', 'click', function(){
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/')}}/'+verify_doc_href,
                type:'POST',
                datatype:'json',
                data:{doc_id:verify_doc_id, doc_type:verify_doc_type},
                success:function(response){
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    $('#confirmDocModal').modal('hide');
                    if(res.status){
                        if(res.doc_type == 'personal') {
                            $('.documnetlist_'+res.doc_category+' #docverifiedby_'+verify_doc_id).html(res.verified_by + "<br>" + res.verified_at);
                        } else if( res.doc_type == 'visa') {
                            $('.migdocumnetlist1 #visadocverifiedby_'+verify_doc_id).html(res.verified_by + "<br>" + res.verified_at);
                        }
                        getallactivities();
                    }
                }
            });
        });


        var notuse_doc_id = '';
        var notuse_doc_href = '';
        var notuse_doc_type = '';

        // Move the notuseddoc click handler inside document ready
        $(document).on('click', '.notuseddoc', function(e){
            e.preventDefault();
            console.log('notuseddoc clicked'); // Debug log
            
            // Check if modal exists
            if($('#confirmNotUseDocModal').length === 0) {
                console.error('Modal #confirmNotUseDocModal not found!');
                return;
            }
            
            $('#confirmNotUseDocModal').modal('show');
            notuse_doc_id = $(this).attr('data-id');
            notuse_doc_href = $(this).attr('data-href');
            notuse_doc_type = $(this).attr('data-doctype');
            console.log('Modal data:', {id: notuse_doc_id, href: notuse_doc_href, type: notuse_doc_type}); // Debug log
        });

        // Alternative approach using delegate for better compatibility
        $(document).delegate('.notuseddoc', 'click', function(e){
            e.preventDefault();
            console.log('notuseddoc delegate clicked'); // Debug log
            
            // Check if modal exists
            if($('#confirmNotUseDocModal').length === 0) {
                console.error('Modal #confirmNotUseDocModal not found!');
                return;
            }
            
            $('#confirmNotUseDocModal').modal('show');
            notuse_doc_id = $(this).attr('data-id');
            notuse_doc_href = $(this).attr('data-href');
            notuse_doc_type = $(this).attr('data-doctype');
            console.log('Modal data delegate:', {id: notuse_doc_id, href: notuse_doc_href, type: notuse_doc_type}); // Debug log
        });

        // Test if elements with .notuseddoc class exist
        console.log('Elements with .notuseddoc class:', $('.notuseddoc').length);
        $('.notuseddoc').each(function(index) {
            console.log('notuseddoc element ' + index + ':', $(this).attr('data-id'), $(this).attr('data-href'), $(this).attr('data-doctype'));
            // Add a test click handler to see if the element is clickable
            $(this).css('cursor', 'pointer');
        });

        // Additional fallback - bind directly to existing elements
        $('.notuseddoc').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Direct click handler triggered');
            $('#confirmNotUseDocModal').modal('show');
            notuse_doc_id = $(this).attr('data-id');
            notuse_doc_href = $(this).attr('data-href');
            notuse_doc_type = $(this).attr('data-doctype');
            console.log('Direct handler data:', {id: notuse_doc_id, href: notuse_doc_href, type: notuse_doc_type});
        });

        $(document).delegate('#confirmNotUseDocModal .accept', 'click', function(){
            console.log('Modal accept clicked'); // Debug log
            console.log('Sending data:', {doc_id: notuse_doc_id, doc_type: notuse_doc_type, href: notuse_doc_href}); // Debug log
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/')}}/'+notuse_doc_href,
                type:'POST',
                datatype:'json',
                data:{doc_id:notuse_doc_id, doc_type:notuse_doc_type },
                success:function(response){
                    console.log('Ajax response:', response); // Debug log
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    $('#confirmNotUseDocModal').modal('hide');
                    if(res.status){
                        if(res.doc_type == 'personal') {
                            $('.documnetlist_'+res.doc_category+' #id_'+res.doc_id).remove();
                        } else if( res.doc_type == 'visa') {
                            $('.migdocumnetlist1 #id_'+res.doc_id).remove();
                        }
                        location.reload();
                        /*if(res.docInfo) {
                            var subArray = res.docInfo;
                            var trRow = "";
                            trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+subArray.doc_type+"</td><td>"+res.Added_By+"</td><td>"+res.Added_date+"</td><td><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></div></td><td>"+res.Verified_By+"</td><td>"+res.Verified_At+"</td></tr>";
                            $('.notuseddocumnetlist').append(trRow);
                        }*/
                        getallactivities();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Ajax error:', error); // Debug log
                    console.log('Status:', status); // Debug log
                    console.log('Response:', xhr.responseText); // Debug log
                    $('.popuploader').hide();
                }
            });
        });


        var backto_doc_id = '';
        var backto_doc_href = '';
        var backto_doc_type = '';
        $('.backtodoc').off('click').on('click', function(e) { 
            e.preventDefault();
            e.stopPropagation();
            $('#confirmBackToDocModal').modal('show');
            backto_doc_id = $(this).attr('data-id');
            backto_doc_href = $(this).attr('data-href');
            backto_doc_type = $(this).attr('data-doctype');
        });

        $(document).delegate('#confirmBackToDocModal .accept', 'click', function(){
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/')}}/'+backto_doc_href,
                type:'POST',
                datatype:'json',
                data:{doc_id:backto_doc_id, doc_type:backto_doc_type },
                success:function(response){
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    $('#confirmBackToDocModal').modal('hide');
                    if(res.status){
                        //if(res.doc_type == 'documents') {
                            $('.notuseddocumnetlist #id_'+res.doc_id).remove();
                        //}
                        location.reload();
                        /*if(res.docInfo) {
                            var subArray = res.docInfo;
                            var trRow = "";
                            trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";
                            $('.notuseddocumnetlist').append(trRow);
                        }*/
                        getallactivities();
                    }
                }
            });
        });


        var notid = '';
        var delhref = '';
        $('.deletenote').off('click').on('click', function(e) { 
            e.preventDefault();
            e.stopPropagation();
            $('#confirmModal').modal('show');
            notid = $(this).attr('data-id');
            delhref = $(this).attr('data-href');
        });

        $(document).delegate('#confirmModal .accept', 'click', function(){
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/')}}/'+delhref,
                type:'GET',
                datatype:'json',
                data:{note_id:notid},
                success:function(response){
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    $('#confirmModal').modal('hide');
                    if(res.status){
                        $('#note_id_'+notid).remove();
                        if(res.status == true){
                            $('#id_'+notid).remove();
                        }

                        if(delhref == 'deletedocs'){
                            $('.documnetlist_'+res.doc_categry+' #id_'+notid).remove();
                        }
                        if(delhref == 'deleteservices'){
                            $.ajax({
                                url: site_url+'/admin/get-services',
                                type:'GET',
                                data:{clientid:'{{$fetchedData->id}}'},
                                success: function(responses){
                                    $('.interest_serv_list').html(responses);
                                }
                            });
                        }else if(delhref == 'superagent'){
                            $('.supagent_data').html('');
                        }else if(delhref == 'subagent'){
                            $('.subagent_data').html('');
                        }else if(delhref == 'deleteappointment'){
                            $.ajax({
                                url: site_url+'/admin/get-appointments',
                                type:'GET',
                                data:{clientid:'{{$fetchedData->id}}'},
                                success: function(responses){
                                    $('.appointmentlist').html(responses);
                                }
                            });
                        } else if(delhref == 'deletepaymentschedule'){
                            $.ajax({
                                url: site_url+'/admin/get-all-paymentschedules',
                                type:'GET',
                                data:{client_id:'{{$fetchedData->id}}',appid:res.application_id},
                                success: function(responses){
                                    $('.showpaymentscheduledata').html(responses);
                                }
                            });
                        } else if(delhref == 'deleteapplicationdocs'){
                            $('.mychecklistdocdata').html(res.doclistdata);
                            $('.checklistuploadcount').html(res.applicationuploadcount);
                            $('.'+res.type+'_checklists').html(res.checklistdata);
                        } else {
                            getallnotes();
                        }
                        getallactivities();
                    }
                }
            });
        });



        var activitylogid = '';
        var delloghref = '';
        $(document).delegate('.deleteactivitylog', 'click', function(){
            $('#confirmLogModal').modal('show');
            activitylogid = $(this).attr('data-id');
            delloghref = $(this).attr('data-href');
        });

        $(document).delegate('#confirmLogModal .accept', 'click', function(){
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/')}}/'+delloghref,
                type:'GET',
                datatype:'json',
                data:{activitylogid:activitylogid},
                success:function(response){
                    //$('.popuploader').hide();
                    var res = JSON.parse(response);
                    $('#confirmLogModal').modal('hide');
                    //location.reload();
                    if(res.status){
                        $('#activity_'+activitylogid).remove();
                        if(res.status == true){
                            $('#activity_'+activitylogid).remove();
                        }
                        getallactivities();
                    }
                }
            });
        });


        $(document).delegate('.pinnote', 'click', function(){
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/pinnote')}}/',
                type:'GET',
                datatype:'json',
                data:{note_id:$(this).attr('data-id')},
                success:function(response){
                    getallnotes();
                }
            });
        });

        //Pin activity log click
        $(document).delegate('.pinactivitylog', 'click', function(){
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/pinactivitylog')}}/',
                type:'GET',
                datatype:'json',
                data:{activity_id:$(this).attr('data-id')},
                success:function(response){
                    getallactivities();
                }
            });
        });

        $(document).delegate('.createapplicationnewinvoice', 'click', function(){
            $('#opencreateinvoiceform').modal('show');
            var sid	= $(this).attr('data-id');
            var cid	= $(this).attr('data-cid');
            var aid	= $(this).attr('data-app-id');
            $('#client_id').val(cid);
            $('#app_id').val(aid);
            $('#schedule_id').val(sid);
        });

        $(document).delegate('.create_note_d', 'click', function(){

            $('#create_note_d').modal('show');
            $('#create_note_d input[name="mailid"]').val(0);


            $('#create_note_d input[name="title"]').val("Matter Discussion");

            //$('#create_note input[name="title"]').val('');
            $('#create_note_d #appliationModalLabel').html('Create Note');
            // alert('yes');
            //	$('#create_note input[name="title"]').val('');
            //	$("#create_note .summernote-simple").val('');
            //	$('#create_note input[name="noteid"]').val('');
            //	$("#create_note .summernote-simple").summernote('code','');

            if($(this).attr('datatype') == 'note'){
                $('.is_not_note').hide();
            }else{
                var datasubject = $(this).attr('datasubject');
                var datamailid = $(this).attr('datamailid');
                $('#create_note_d input[name="title"]').val(datasubject);
                $('#create_note_d input[name="mailid"]').val(datamailid);
                $('.is_not_note').show();
            }
        });

        $(document).delegate('.create_note', 'click', function(){
            $('#create_note').modal('show');
            $('#create_note input[name="mailid"]').val(0);
            $('#create_note input[name="title"]').val('');
            $('#create_note #appliationModalLabel').html('Create Note');
            $('#create_note input[name="title"]').val('');
            $("#create_note .summernote-simple").val('');
            $('#create_note input[name="noteid"]').val('');
            $("#create_note .summernote-simple").summernote('code','');
            if($(this).attr('datatype') == 'note'){
                $('.is_not_note').hide();
            }else{
                var datasubject = $(this).attr('datasubject');
                var datamailid = $(this).attr('datamailid');
                $('#create_note input[name="title"]').val(datasubject);
                $('#create_note input[name="mailid"]').val(datamailid);
                $('.is_not_note').show();
            }
        });

        $(document).delegate('.opentaskmodal', 'click', function(){
            $('#opentaskmodal').modal('show');
            $('#opentaskmodal input[name="mailid"]').val(0);
            $('#opentaskmodal input[name="title"]').val('');
            $('#opentaskmodal #appliationModalLabel').html('Create Note');
            $('#opentaskmodal input[name="attachments"]').val('');
            $('#opentaskmodal input[name="title"]').val('');
            $('#opentaskmodal .showattachment').val('Choose file');

            var datasubject = $(this).attr('datasubject');
            var datamailid = $(this).attr('datamailid');
            $('#opentaskmodal input[name="title"]').val(datasubject);
            $('#opentaskmodal input[name="mailid"]').val(datamailid);
        });

        $('.js-data-example-ajaxcc').select2({
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $('#create_note'),
            ajax: {
                url: '{{URL::to('/admin/clients/get-recipients')}}',
                dataType: 'json',
                processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data.items
                };

                },
                cache: true

            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        $('.js-data-example-ajaxccapp').select2({
                multiple: true,
                closeOnSelect: false,
                dropdownParent: $('#applicationemailmodal'),
                ajax: {
                    url: '{{URL::to('/admin/clients/get-recipients')}}',
                    dataType: 'json',
                    processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data.items
                    };

                    },
                    cache: true

                },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        $('.js-data-example-ajaxcontact').select2({
                multiple: true,
                closeOnSelect: false,
                dropdownParent: $('#opentaskmodal'),
                ajax: {
                    url: '{{URL::to('/admin/clients/get-recipients')}}',
                    dataType: 'json',
                    processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data.items
                    };

                    },
                    cache: true

                },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        function formatRepo (repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

            "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

            "</div>" +
            "</div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +

                "<span class='ui label yellow select2-result-repository__statistics'>" +

                "</span>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.name);
        $container.find(".select2-result-repository__description").text(repo.email);
        $container.find(".select2-result-repository__statistics").append(repo.status);

        return $container;
        }

        //Function is used for complete the session
        $(document).delegate('.complete_session', 'click', function(){
            var client_id = $(this).attr('data-clientid'); //alert(client_id);
            if(client_id !=""){
                $.ajax({
                    type:'post',
                    url:"{{URL::to('/')}}/admin/clients/update-session-completed",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {client_id:client_id },
                    success: function(response){
                        console.log(response);
                        var obj = $.parseJSON(response);
                        location.reload();
                    }
                });
            }
        });

        function formatRepoSelection (repo) {
            return repo.name || repo.text;
        }

        //Update note
        $(document).delegate('.opennoteform', 'click', function(){
            $('#create_note').modal('show');
            $('#create_note #appliationModalLabel').html('Edit Note');
            var v = $(this).attr('data-id');
            $('#create_note input[name="noteid"]').val(v);
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/getnotedetail')}}',
                type:'GET',
                datatype:'json',
                data:{note_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    if(res.status){
                        $('#create_note select[name="task_group"]').val(res.data.task_group);
                        $("#create_note .summernote-simple").val(res.data.description);
                        $("#create_note .summernote-simple").summernote('code',res.data.description);
                    }
                }
            });
        });

        $(document).delegate('.viewnote', 'click', function(){
            $('#view_note').modal('show');
            var v = $(this).attr('data-id');
            $('#view_note input[name="noteid"]').val(v);
                $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/viewnotedetail')}}',
                type:'GET',
                datatype:'json',
                data:{note_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    var res = JSON.parse(response);

                    if(res.status){
                        $('#view_note .modal-body .note_content h5').html(res.data.title);
                        $("#view_note .modal-body .note_content p").html(res.data.description);

                    }
                }
            });
        });

        $(document).delegate('.viewapplicationnote', 'click', function(){
            $('#view_application_note').modal('show');
            var v = $(this).attr('data-id');
            $('#view_application_note input[name="noteid"]').val(v);
                $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/viewapplicationnote')}}',
                type:'GET',
                datatype:'json',
                data:{note_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    var res = JSON.parse(response);

                    if(res.status){
                        $('#view_application_note .modal-body .note_content h5').html(res.data.title);
                        $("#view_application_note .modal-body .note_content p").html(res.data.description);

                    }
                }
            });
        });

	    $(document).delegate('.add_appliation #workflow', 'change', function(){
            var v = $('.add_appliation #workflow option:selected').val();
			if(v != ''){
				$('.popuploader').show();
                $.ajax({
                    url: '{{URL::to('/admin/getpartnerbranch')}}',
                    type:'GET',
                    data:{cat_id:v},
                    success:function(response){
                        $('.popuploader').hide();
                        $('.add_appliation #partner').html(response);

                        $(".add_appliation #partner").val('').trigger('change');
                        $(".add_appliation #product").val('').trigger('change');
                        $(".add_appliation #branch").val('').trigger('change');
                    }
                });
			}
	    });

        $(document).delegate('.clientemail', 'click', function(){
            if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                var selectedMatterL = $('.general_matter_checkbox_client_detail').val();
            } else {
                var selectedMatterL = $('#sel_matter_id_client_detail').val();
            }
            $('#emailmodal #compose_client_matter_id').val(selectedMatterL);
            $('#emailmodal').modal('show');
            var array = [];
            var data = [];

            var id = $(this).attr('data-id');
            array.push(id);
            var email = $(this).attr('data-email');
            var name = $(this).attr('data-name');
            var status = 'Client';

            data.push({
                id: id,
                text: name,
                html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

                "<div  class='ag-flex ag-align-start'>" +
                    "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +

                "</div>" +
                "</div>" +
                "<div class='ag-flex ag-flex-column ag-align-end'>" +

                    "<span class='ui label yellow select2-result-repository__statistics'>"+ status +

                    "</span>" +
                "</div>" +
                "</div>",
                title: name
            });

            $(".js-data-example-ajax").select2({
                data: data,
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(data) {
                    return data.html;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            })

            $('.js-data-example-ajax').val(array);
            $('.js-data-example-ajax').trigger('change');
        });

        $(document).delegate('.change_client_status', 'click', function(e){

            var v = $(this).attr('rating');
            $('.change_client_status').removeClass('active');
            $(this).addClass('active');

            $.ajax({
                url: '{{URL::to('/admin/change-client-status')}}',
                type:'GET',
                datatype:'json',
                data:{id:'{{$fetchedData->id}}',rating:v},
                success: function(response){
                    var res = JSON.parse(response);
                    if(res.status){

                        $('.custom-error-msg').html('<span class="alert alert-success">'+res.message+'</span>');
                        getallactivities();
                    }else{
                        $('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');
                    }

                }
            });
        });

        /*$(document).delegate('.selecttemplate', 'change', function(){
            var v = $(this).val();
            $.ajax({
                url: '{{URL::to('/admin/get-templates')}}',
                type:'GET',
                datatype:'json',
                data:{id:v},
                success: function(response){
                    var res = JSON.parse(response);
                    $('.selectedsubject').val(res.subject);
                    $("#emailmodal .summernote-simple").summernote('reset');
                    $("#emailmodal .summernote-simple").summernote('code', res.description);
                    $("#emailmodal .summernote-simple").val(res.description);
                }
            });
        });*/

        $(document).delegate('.selecttemplate', 'change', function(){
            var client_id = $(this).data('clientid'); //alert(client_id);
            var client_firstname = $(this).data('clientfirstname'); //alert(client_firstname);
            if (client_firstname) {
                client_firstname = client_firstname.charAt(0).toUpperCase() + client_firstname.slice(1);
            }
            var client_reference_number = $(this).data('clientreference_number'); //alert(client_reference_number);
            var company_name = 'Bansal Education Group';
            var visa_valid_upto = $(this).data('clientvisaExpiry');
            if ( visa_valid_upto != '' && visa_valid_upto != '0000-00-00') {
                visa_valid_upto = visa_valid_upto;
            } else {
                visa_valid_upto = '';
            }

            var clientassignee_name = $(this).data('clientassignee_name');
            if ( clientassignee_name != '') {
                clientassignee_name = clientassignee_name;
            } else {
                clientassignee_name = '';
            }

            var v = $(this).val();
            $.ajax({
                url: '{{URL::to('/admin/get-templates')}}',
                type:'GET',
                datatype:'json',
                data:{id:v},
                success: function(response){
                    var res = JSON.parse(response);

                    // Replace {Client First Name} with actual client name
                    //var subjct_message = res.subject
                    //.replace('{Client First Name}', client_firstname)
                    //.replace(/Ref:\s*\.{1,}\s*/, 'Ref: ' + client_reference_number)
                    //.replace(/Ref_\s*-{1,}\s*/, 'Ref_' + client_reference_number)
                    //.replace('{client reference}', client_reference_number);

                    var subjct_message = res.subject.replace('{Client First Name}', client_firstname).replace('{client reference}', client_reference_number);
                    $('.selectedsubject').val(subjct_message);

                    $("#emailmodal .summernote-simple").summernote('reset');
                    //$("#emailmodal .summernote-simple").summernote('code', res.description);
                    //$("#emailmodal .summernote-simple").val(res.description);
                    //var subjct_description = res.description.replace('{Client First Name}', client_firstname);

                    //var subjct_description = res.description
                    //.replace(/Dear\s*\.{2,}\s*/, 'Dear ' + client_firstname)
                    //.replace('{Client First Name}', client_firstname)
                // .replace('{Company Name}', company_name)
                    //.replace('{Visa Valid Upto}', visa_valid_upto)
                    //.replace('{Client Assignee Name}', clientassignee_name)
                    //.replace(/Reference:\s*\.{2,}\s*/, 'Reference: ' + client_reference_number)
                    //.replace('{client reference}', client_reference_number);

                    var subjct_description = res.description
                    .replace('{Client First Name}', client_firstname)
                    .replace('{Company Name}', company_name)
                    .replace('{Visa Valid Upto}', visa_valid_upto)
                    .replace('{Client Assignee Name}', clientassignee_name)
                    .replace('{client reference}', client_reference_number);

                    $("#emailmodal .summernote-simple").summernote('code', subjct_description);
                    $("#emailmodal .summernote-simple").val(subjct_description);
                }
            });
        });

        $(document).delegate('.selectapplicationtemplate', 'change', function(){
            var v = $(this).val();
            $.ajax({
                url: '{{URL::to('/admin/get-templates')}}',
                type:'GET',
                datatype:'json',
                data:{id:v},
                success: function(response){
                    var res = JSON.parse(response);
                    $('.selectedappsubject').val(res.subject);
                    $("#applicationemailmodal .summernote-simple").summernote('reset');
                    $("#applicationemailmodal .summernote-simple").summernote('code', res.description);
                    $("#applicationemailmodal .summernote-simple").val(res.description);
                }
            });
        });

        $('.js-data-example-ajax').select2({
                multiple: true,
                closeOnSelect: false,
                dropdownParent: $('#emailmodal'),
                ajax: {
                    url: '{{URL::to('/admin/clients/get-recipients')}}',
                    dataType: 'json',
                    processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data.items
                    };

                    },
                    cache: true

                },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        $('.js-data-example-ajaxccd').select2({
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $('#emailmodal'),
            ajax: {
                url: '{{URL::to('/admin/clients/get-recipients')}}',
                dataType: 'json',
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        function formatRepo (repo) {
            if (repo.loading) {
                return repo.text;
            }

            var $container = $(
                "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

                    "<div  class='ag-flex ag-align-start'>" +
                    "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

                    "</div>" +
                    "</div>" +
                    "<div class='ag-flex ag-flex-column ag-align-end'>" +

                    "<span class='ui label yellow select2-result-repository__statistics'>" +

                    "</span>" +
                    "</div>" +
                "</div>"
                );

            $container.find(".select2-result-repository__title").text(repo.name);
            $container.find(".select2-result-repository__description").text(repo.email);
            $container.find(".select2-result-repository__statistics").append(repo.status);
            return $container;
        }

        function formatRepoSelection (repo) {
            return repo.name || repo.text;
        }

        /* $(".table-2").dataTable({
            "searching": false,
            "lengthChange": false,
        "columnDefs": [
            { "sortable": false, "targets": [0, 2, 3] }
        ],
        order: [[1, "desc"]] //column indexes is zero based

        }); */
        $('#mychecklist-datatable').dataTable({"searching": true,});
        $(".invoicetable").dataTable({
            "searching": false,
            "lengthChange": false,
            "columnDefs": [
            { "sortable": false, "targets": [0, 2, 3] }
        ],
        order: [[1, "desc"]] //column indexes is zero based

        });


        $(document).delegate('#intrested_workflow', 'change', function(){

			var v = $('#intrested_workflow option:selected').val();
            if(v != ''){
                $('.popuploader').show();
                $.ajax({
                    url: '{{URL::to('/admin/getpartner')}}',
                    type:'GET',
                    data:{cat_id:v},
                    success:function(response){
                        $('.popuploader').hide();
                        $('#intrested_partner').html(response);

                        $("#intrested_partner").val('').trigger('change');
                    $("#intrested_product").val('').trigger('change');
                    $("#intrested_branch").val('').trigger('change');
                    }
                });
            }
	    });

        $(document).delegate('#edit_intrested_workflow', 'change', function(){

                    var v = $('#edit_intrested_workflow option:selected').val();

                    if(v != ''){
                            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/getpartner')}}',
                type:'GET',
                data:{cat_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('#edit_intrested_partner').html(response);

                    $("#edit_intrested_partner").val('').trigger('change');
                $("#edit_intrested_product").val('').trigger('change');
                $("#edit_intrested_branch").val('').trigger('change');
                }
            });
                    }
        });

        $(document).delegate('#intrested_partner','change', function(){
            var v = $('#intrested_partner option:selected').val();
            if(v != ''){
                $('.popuploader').show();
                $.ajax({
                    url: '{{URL::to('/admin/getproduct')}}',
                    type:'GET',
                    data:{cat_id:v},
                    success:function(response){
                        $('.popuploader').hide();
                        $('#intrested_product').html(response);
                        $("#intrested_product").val('').trigger('change');
                    $("#intrested_branch").val('').trigger('change');
                    }
                });
            }
        });

        $(document).delegate('#edit_intrested_partner','change', function(){
            var v = $('#edit_intrested_partner option:selected').val();
            if(v != ''){
                $('.popuploader').show();
                $.ajax({
                    url: '{{URL::to('/admin/getproduct')}}',
                    type:'GET',
                    data:{cat_id:v},
                    success:function(response){
                        $('.popuploader').hide();
                        $('#edit_intrested_product').html(response);
                        $("#edit_intrested_product").val('').trigger('change');
                    $("#edit_intrested_branch").val('').trigger('change');
                    }
                });
            }
        });

        $(document).delegate('#intrested_product','change', function(){
            var v = $('#intrested_product option:selected').val();
            if(v != ''){
                $('.popuploader').show();
                $.ajax({
                    url: '{{URL::to('/admin/getbranch')}}',
                    type:'GET',
                    data:{cat_id:v},
                    success:function(response){
                        $('.popuploader').hide();
                        $('#intrested_branch').html(response);
                        $("#intrested_branch").val('').trigger('change');
                    }
                });
            }
        });


        /*$(document).delegate('.docupload', 'click', function() {
            $(this).attr("value", "");
        });

        $(document).delegate('.docupload', 'change', function() {
            var fileInput = this.files[0];

            if (!fileInput) return; // Prevent empty uploads

            var fileName = fileInput.name;  //alert(fileName);

            // Allowed: letters, numbers, dash, underscore, space, dot
            var validNameRegex = /^[a-zA-Z0-9_\-\.\s]+$/;

            if (!validNameRegex.test(fileName)) {
                alert("File name can only contain letters, numbers, dashes (-), underscores (_), spaces, and dots (.). Please rename the file and try again.");
                $(this).val(''); // Clear the file input
                return false;
            }

            $('.popuploader').show();
            var fileidL = $(this).attr("data-fileid");
            console.log('fileidL='+fileidL);
            var doccategoryL = $(this).attr("data-doccategory");
            console.log('doccategoryL='+doccategoryL);
            var formData = new FormData($('#upload_form_'+fileidL)[0]);
            console.log(formData);
            $.ajax({
                url: site_url+'/admin/upload-edudocument',
                type:'POST',
                datatype:'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function(responses){
                    $('.popuploader').hide();
                    var ress = JSON.parse(responses);
                    if(ress.status){
                        $('.custom-error-msg').html('<span class="alert alert-success">'+ress.message+'</span>');
                        $('.documnetlist_'+doccategoryL).html(ress.data);
                        $('.griddata_'+doccategoryL).html(ress.griddata);
                    }else{
                       $('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');
                    }
                    getallactivities();
                }
            });
        });*/

        // ========================================
        // DRAG AND DROP UPLOAD FUNCTIONALITY FOR PERSONAL DOCUMENTS
        // ========================================
        
        /**
         * Initialize drag and drop functionality for Personal Documents
         * This function sets up all event listeners for drag and drop upload areas
         */
        function initializeDragAndDropUpload() {
            // Handle click to upload for drag drop areas
            $(document).on('click', '.drag-drop-area', function(e) {
                // Prevent triggering if clicking on remove file button
                if (!$(e.target).hasClass('remove-file')) {
                    $(this).find('.docupload').click();
                }
            });
            
            // Handle file selection via file input (for both drag drop and click)
            $(document).on('change', '.docupload', function() {
                handleFileSelection(this);
            });
            
            // Drag over event - add visual feedback
            $(document).on('dragover', '.drag-drop-area', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('drag-over');
            });
            
            // Drag leave event - remove visual feedback
            $(document).on('dragleave', '.drag-drop-area', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');
            });
            
            // Drop event - handle file drop
            $(document).on('drop', '.drag-drop-area', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');
                
                const files = e.originalEvent.dataTransfer.files;
                const fileInput = $(this).find('.docupload')[0];
                
                // Set files to the file input
                fileInput.files = files;
                handleFileSelection(fileInput);
            });
        }
        
        /**
         * Handle file selection from both drag drop and file input
         * @param {HTMLElement} fileInput - The file input element
         */
        function handleFileSelection(fileInput) {
            const files = fileInput.files;
            if (!files || files.length === 0) return;
            
            const fileid = $(fileInput).attr('data-fileid');
            const doccategory = $(fileInput).attr('data-doccategory');
            const uploadArea = $(fileInput).closest('.drag-drop-area');
            
            // Validate required attributes
            if (!fileid || !doccategory) {
                console.error('Missing required attributes: fileid or doccategory');
                alert('Upload configuration error. Please refresh the page and try again.');
                return;
            }
            
            // Validate files
            const validFiles = [];
            const invalidFiles = [];
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const validNameRegex = /^[a-zA-Z0-9_\-\.\s\$]+$/;
                
                // Check file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    invalidFiles.push(file.name + ' (file too large - max 10MB)');
                    continue;
                }
                
                if (!validNameRegex.test(file.name)) {
                    invalidFiles.push(file.name);
                } else {
                    validFiles.push(file);
                }
            }
            
            // Show error for invalid files
            if (invalidFiles.length > 0) {
                alert('Invalid files:\n' + invalidFiles.join('\n') + '\n\nValid file names can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($).\nMaximum file size is 10MB.');
            }
            
            // Upload valid files
            if (validFiles.length > 0) {
                uploadMultipleFiles(validFiles, fileid, doccategory, uploadArea);
            }
        }
        
        /**
         * Upload multiple files with progress tracking
         * @param {Array} files - Array of File objects
         * @param {string} fileid - Document ID
         * @param {string} doccategory - Document category
         * @param {jQuery} uploadArea - Upload area element
         */
        function uploadMultipleFiles(files, fileid, doccategory, uploadArea) {
            const progressBar = uploadArea.siblings('.upload-progress');
            const progressFill = progressBar.find('.progress-fill');
            const progressText = progressBar.find('.progress-text');
            
            // Show progress bar
            progressBar.show();
            
            let uploadedCount = 0;
            let totalFiles = files.length;
            let successCount = 0;
            let errorCount = 0;
            
            // Upload each file individually
            files.forEach((file, index) => {
                const formData = new FormData();
                formData.append('document_upload', file);
                
                // Get clientid from the form or from the page URL
                let clientid = $('input[name="clientid"]').val();
                if (!clientid) {
                    // Try to get from URL if not in form
                    const urlParams = new URLSearchParams(window.location.search);
                    clientid = urlParams.get('id') || $('input[name="client_id"]').val();
                }
                formData.append('clientid', clientid);
                formData.append('fileid', fileid);
                formData.append('type', 'client');
                formData.append('doctype', 'personal');
                formData.append('doccategory', doccategory);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                $.ajax({
                    url: site_url + '/admin/upload-edudocument',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = (evt.loaded / evt.total) * 100;
                                const totalProgress = ((uploadedCount + (percentComplete / 100)) / totalFiles) * 100;
                                progressFill.css('width', totalProgress + '%');
                                progressText.text(`Uploading ${uploadedCount + 1}/${totalFiles}: ${Math.round(percentComplete)}%`);
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        uploadedCount++;
                        progressFill.css('width', (uploadedCount / totalFiles) * 100 + '%');
                        progressText.text(`Uploaded ${uploadedCount}/${totalFiles}`);
                        
                        if (response.status) {
                            successCount++;
                            // Update the UI for successful upload
                            updateDocumentRowAfterUpload(fileid, response, doccategory);
                        } else {
                            errorCount++;
                            console.error('Upload failed for file:', file.name, response.message);
                        }
                        
                        // Check if all uploads are complete
                        if (uploadedCount === totalFiles) {
                            setTimeout(() => {
                                progressBar.hide();
                                progressFill.css('width', '0%');
                                
                                // Show final status message
                                if (successCount > 0) {
                                    $('.custom-error-msg').html('<span class="alert alert-success">Successfully uploaded ' + successCount + ' file(s)</span>');
                                    
                                    // Refresh page after successful upload to show updated document list
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                }
                                if (errorCount > 0) {
                                    $('.custom-error-msg').html('<span class="alert alert-warning">Uploaded ' + successCount + ' file(s), ' + errorCount + ' failed</span>');
                                }
                                
                                // Clear the file input
                                $(uploadArea).find('.docupload').val('');
                                
                                // Refresh activities
                                getallactivities();
                            }, 2000);
                        }
                    },
                    error: function(xhr, status, error) {
                        uploadedCount++;
                        errorCount++;
                        console.error('Upload error for file:', file.name, error);
                        
                        if (uploadedCount === totalFiles) {
                            setTimeout(() => {
                                progressBar.hide();
                                progressFill.css('width', '0%');
                                $(uploadArea).find('.docupload').val('');
                                getallactivities();
                            }, 2000);
                        }
                    }
                });
            });
        }
        
        /**
         * Update document row after successful upload
         * @param {string} fileid - Document ID
         * @param {Object} response - Server response
         * @param {string} doccategory - Document category
         */
        function updateDocumentRowAfterUpload(fileid, response, doccategory) {
            console.log('Upload response:', response); // Debug log
            
            if (response.status) {
                var row = $('#id_' + fileid);
                console.log('Found row:', row.length); // Debug log
                
                // Check if row exists, if not, refresh the page to show updated data
                if (row.length === 0) {
                    console.log('Row not found, refreshing page to show updated data');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    return;
                }
                
                var docNameWithoutExt = response.filename.replace(/\.[^/.]+$/, "").replace(/\s+/g, "_").toLowerCase();

                // Replace upload TD content
                var uploadTd = row.find('td').eq(3);
                uploadTd.html(
                    '<div data-id="' + fileid + '" data-name="' + docNameWithoutExt + '" class="doc-row">' +
                        '<a href="javascript:void(0);" onclick="previewFile(\'' + response.filetype + '\', \'' + response.fileurl + '\', \'preview-container-' + doccategory + '\')">' +
                            '<i class="fas fa-file-image"></i> <span>' + response.filename + '</span>' +
                        '</a>' +
                    '</div>'
                );

                // Generate signature-related anchor
                let signatureAction = '';
                if (response.filetype.toLowerCase() === 'pdf') {
                    if (response.status_value === 'draft') {
                        signatureAction = '<a target="_blank" href="' + site_url + '/admin/documents/' + fileid + '/edit" class="dropdown-item">Send To Signature</a>';
                    } else if (response.status_value === 'sent') {
                        signatureAction = '<a target="_blank" href="' + site_url + '/admin/documents/' + fileid + '" class="dropdown-item">Check To Signature</a>';
                    } else if (response.status_value === 'signed') {
                        signatureAction = '<a target="_blank" href="' + site_url + '/admin/download-signed/' + fileid + '" class="dropdown-item">Download Signed</a>';
                    }
                }

                // Replace action TD content
                var actionTd = row.find('td').eq(4);
                console.log('Action TD found:', actionTd.length); // Debug log
                console.log('Response data for action buttons:', {
                    fileidL: fileid,
                    fileurl: response.fileurl,
                    filekey: response.filekey,
                    doctype: response.doctype,
                    doccategory: response.doccategory
                }); // Debug log
                
                actionTd.html(
                    '<div class="dropdown d-inline">' +
                        '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Action</button>' +
                        '<div class="dropdown-menu">' +
                            '<a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>' +
                            '<a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>' +
                            '<a target="_blank" class="dropdown-item" href="' + response.fileurl + '">Preview</a>' +
                            '<a href="#" class="dropdown-item download-file" data-filelink="' + response.fileurl + '" data-filename="' + response.filekey + '">Download</a>' +
                            '<a data-id="' + fileid + '" class="dropdown-item notuseddoc" data-doctype="' + response.doctype + '" data-doccategory="' + response.doccategory + '" data-href="notuseddoc" href="javascript:;">Not Used</a>' +
                            '<a target="_blank" href="' + site_url + '/admin/documents/' + fileid + '/edit" class="dropdown-item">Send To Signature</a>' +
                                '</div>' +
                            '</div>'
                        );
                        
                        // Ensure the row has the proper class for event delegation
                        row.addClass('drow');
                        console.log('Row classes after update:', row.attr('class')); // Debug log
                        
                        // Re-attach event handlers for the newly created elements
                        setTimeout(function() {
                            // Re-initialize any necessary event handlers
                            if (typeof $.fn.dropdown !== 'undefined') {
                                actionTd.find('.dropdown-toggle').dropdown();
                            }
                            console.log('Action buttons generated successfully'); // Debug log
                            
                            // Ensure the row is properly structured for event delegation
                            if (!row.hasClass('drow')) {
                                row.addClass('drow');
                            }
                            
                            // Force re-initialization of event handlers by triggering a custom event
                            $(document).trigger('actionButtonsCreated', [actionTd]);
                        }, 100);
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');
                    }
                    getallactivities();
                }


        $(document).delegate('.add_education_doc', 'click', function () {
            $("#doccategory").val($(this).attr('data-categoryid'));
            $("#folder_name").val($(this).attr('data-categoryid'));
            $('.create_education_docs').modal('show');
            $("#checklist").select2({dropdownParent: $(".create_education_docs")});
        });

        //Add Personal Document category
        $(document).delegate('.add_personal_doc_cat', 'click', function () {
            $('.addpersonaldoccatmodel').modal('show');
        });

        //Add Visa Document category
        $(document).delegate('.add-visa-doc-category', 'click', function () {
            let selectedMatterFM;

            if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                // If checkbox is checked, get its value
                selectedMatterFM = $('.general_matter_checkbox_client_detail').val();
            } else {
                // If checkbox is not checked, get the value from the dropdown
                selectedMatterFM = $('#sel_matter_id_client_detail').val();
            }
            console.log('selectedMatterFM==='+selectedMatterFM);
            $('#visaclientmatterid').val(selectedMatterFM);
            $('.addvisadoccatmodel').modal('show');
        });


        $(document).delegate('.add_migration_doc', 'click', function () {
            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();
            $('#hidden_client_matter_id').val(hidden_client_matter_id);
            $("#visa_folder_name").val($(this).attr('data-categoryid'));
            $('.create_migration_docs').modal('show');
            $("#visa_checklist").select2({dropdownParent: $("#openmigrationdocsmodal")});
        });

        $(document).delegate('.add_application_btn', 'click', function () {
            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();
            $('#hidden_client_matter_id_latest').val(hidden_client_matter_id);
        });

        $(document).delegate('.migdocupload', 'click', function() {
            $(this).attr("value", "");
        });

        $(document).delegate('.migdocupload', 'change', function() {
            var fileInput = this.files[0];

            if (!fileInput) return; // Prevent empty uploads

            var fileName = fileInput.name;  //alert(fileName);

            // Allowed: letters, numbers, dash, underscore, space, dot, dollar sign
            var validNameRegex = /^[a-zA-Z0-9_\-\.\s\$]+$/;

            if (!validNameRegex.test(fileName)) {
                alert("File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.");
                $(this).val(''); // Clear the file input
                return false;
            }

            $('.popuploader').show();
            var fileidL1 = $(this).attr("data-fileid");
            console.log('fileidL1='+fileidL1);

            var visa_doc_cat = $(this).attr("data-doccategory");
            console.log('visa_doc_cat='+visa_doc_cat);

            var formData = new FormData($('#mig_upload_form_'+fileidL1)[0]);
            // Append extra data manually
            formData.append('visa_doc_cat', visa_doc_cat);
            $.ajax({
                url: site_url+'/admin/upload-visadocument',
                type:'POST',
                datatype:'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function(responses){
                    $('.popuploader').hide();
                    var ress = JSON.parse(responses);
                    if(ress.status){ //alert('visa_doc_cat=='+visa_doc_cat);
                        $('.custom-error-msg').html('<span class="alert alert-success">'+ress.message+'</span>');
                        $('.migdocumnetlist_'+ress.visa_doc_cat).html(ress.data);
                        $('.miggriddata').html(ress.griddata);
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');
                    }
                    getallactivities();
                }
            });
        });


        $(document).delegate('.converttoapplication','click', function(){
            var v = $(this).attr('data-id');
            if(v != ''){
                $('.popuploader').show();
                $.ajax({
                    url: '{{URL::to('/admin/convertapplication')}}',
                    type:'GET',
                    data:{cat_id:v,clientid:'{{$fetchedData->id}}'},
                    success:function(response){
                        $.ajax({
                            url: site_url+'/admin/get-services',
                            type:'GET',
                            data:{clientid:'{{$fetchedData->id}}'},
                            success: function(responses){

                                $('.interest_serv_list').html(responses);
                            }
                        });
                        $.ajax({
                            url: site_url+'/admin/get-application-lists',
                            type:'GET',
                            datatype:'json',
                            data:{id:'{{$fetchedData->id}}'},
                            success: function(responses){
                                $('.applicationtdata').html(responses);
                            }
                        });
                        //getallactivities();
                        $('.popuploader').hide();
                    }
                });
            }
        });

        $(document).on('click', '#application-tab', function () {
            $('.popuploader').show();
            $.ajax({
                url: site_url+'/admin/get-application-lists',
                type:'GET',
                datatype:'json',
                data:{id:'{{$fetchedData->id}}'},
                success: function(responses){
                        $('.popuploader').hide();
                    $('.applicationtdata').html(responses);
                }
            });
        });

        // Initialize event handlers for rename functionality
        $(document).ready(function() {
            console.log('Document ready - initializing rename handlers');
            
            // Test if renamechecklist elements exist
            var renameElements = $('.renamechecklist');
            console.log('Found renamechecklist elements:', renameElements.length);
            
            // Test if personalchecklist-row elements exist
            var personalRows = $('.personalchecklist-row');
            console.log('Found personalchecklist-row elements:', personalRows.length);
        });

        //Rename File Name Personal Document
        $(document).on('click', '.persdocumnetlist .renamedoc', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Rename document clicked:', this);
            
            var parent = $(this).closest('.drow').find('.doc-row');
            console.log('Found doc-row parent:', parent);
            console.log('Parent length:', parent.length);
            
            if (parent.length === 0) {
                console.error('Document row not found');
                return false;
            }
            
            parent.data('current-html', parent.html());
            var opentime = parent.data('name');
            console.log('Document name:', opentime);
            
            if (!opentime) {
                console.error('Document name not found');
                return false;
            }
            
            parent.empty().append(
                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
            );
            
            console.log('Rename document interface created successfully');
            return false;
        });

        $(document).on('click', '.persdocumnetlist .btn-danger', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Cancel rename document clicked');
            
            var parent = $(this).closest('.drow').find('.doc-row');
            if (parent.length === 0) {
                console.error('Document row not found for cancel');
                return false;
            }
            
            var hourid = parent.data('id');
            if (hourid) {
                parent.html(parent.data('current-html'));
            } else {
                parent.remove();
            }
            return false;
        });

        $(document).on('click', '.persdocumnetlist .btn-primary', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Save rename document clicked');
            
            var parent = $(this).closest('.drow').find('.doc-row');
            if (parent.length === 0) {
                console.error('Document row not found for save');
                return false;
            }
            
            parent.find('.opentime').removeClass('is-invalid');
            parent.find('.invalid-feedback').remove();
            var opentime = parent.find('.opentime').val();
            
            if (!opentime) {
                parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                parent.append($("<div class='invalid-feedback'>This field is required</div>"));
                return false;
            }
            
            console.log('Saving document name:', opentime);
            
            $.ajax({
                type: "POST",
                data: {"_token": $('meta[name="csrf-token"]').attr('content'),"filename": opentime, "id": parent.data('id')},
                url: '{{URL::to('/admin/renamedoc')}}',
                success: function(result){
                    var obj = JSON.parse(result);
                    if (obj.status) {
                        var previewUrl = obj.fileurl;
                        var filetype = obj.filetype;
                        var folderName = obj.folder_name;
                        var fileName = obj.filename + '.' + obj.filetype;

                        parent.empty()
                            .data('id', obj.Id)
                            .data('name', opentime)
                            .append(
                                $('<a>', {
                                    href: 'javascript:void(0);',
                                    onclick: `previewFile('${filetype}', '${previewUrl}', '${folderName}')`
                                }).append(
                                    $('<i>', { class: 'fas fa-file-image' }),
                                    ' ',
                                    $('<span>').text(fileName)
                                )
                            );
                        
                        if ($('#grid_'+obj.Id).length) {
                            $('#grid_'+obj.Id).html(fileName);
                        }

                        // 🔁 Update the Preview & Download links in dropdown
                        var dropdownMenu = $(parent).closest('.drow').find('.dropdown-menu');

                        // Preview link
                        dropdownMenu.find('.dropdown-item[href^="http"]').filter(function() {
                            return $(this).text().trim() === 'Preview';
                        }).attr('href', previewUrl);

                        // Download link
                        dropdownMenu.find('.dropdown-item.download-file')
                            .attr('data-filelink', previewUrl)
                            .attr('data-filename', fileName);
                            
                        console.log('Document renamed successfully');
                    } else {
                        parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                        parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                        console.error('Failed to rename document:', obj.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">An error occurred while saving</div>'));
                }
            });
            return false;
        });


        //Rename Checklist Name Personal Document
        $(document).on('click', '.persdocumnetlist .renamechecklist', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Rename checklist clicked:', this);
            console.log('Current element:', $(this));
            console.log('Closest drow:', $(this).closest('.drow'));
            
            var parent = $(this).closest('.drow').find('.personalchecklist-row');
            console.log('Found parent:', parent);
            console.log('Parent length:', parent.length);
            
            if (parent.length === 0) {
                console.error('Personal checklist row not found');
                return false;
            }
            
            parent.data('current-html', parent.html());
            var opentime = parent.data('personalchecklistname');
            console.log('Personal checklist name:', opentime);
            
            if (!opentime) {
                console.error('Personal checklist name not found');
                return false;
            }
            
            parent.empty().append(
                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
            );
            
            console.log('Rename interface created successfully');
            return false;
        });

        // Alternative event handler for better compatibility
        $(document).on('click', '.persdocumnetlist a.renamechecklist', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Alternative rename checklist handler triggered');
            
            var $this = $(this);
            var $drow = $this.closest('.drow');
            var $parent = $drow.find('.personalchecklist-row');
            
            if ($parent.length === 0) {
                console.error('Personal checklist row not found in alternative handler');
                return false;
            }
            
            var opentime = $parent.data('personalchecklistname');
            if (!opentime) {
                console.error('Personal checklist name not found in alternative handler');
                return false;
            }
            
            $parent.data('current-html', $parent.html());
            $parent.empty().append(
                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
            );
            
            console.log('Alternative rename interface created successfully');
            return false;
        });

        $(document).on('click', '.persdocumnetlist .btn-personaldanger', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            var parent = $(this).closest('.drow').find('.personalchecklist-row');
            if (parent.length === 0) {
                console.error('Personal checklist row not found for cancel');
                return false;
            }
            
            var hourid = parent.data('id');
            if (hourid) {
                parent.html(parent.data('current-html'));
            } else {
                parent.remove();
            }
            return false;
        });

        $(document).on('click', '.persdocumnetlist .btn-personalprimary', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            var parent = $(this).closest('.drow').find('.personalchecklist-row');
            if (parent.length === 0) {
                console.error('Personal checklist row not found for save');
                return false;
            }
            
            parent.find('.opentime').removeClass('is-invalid');
            parent.find('.invalid-feedback').remove();
            var opentime = parent.find('.opentime').val();
            
            if (!opentime) {
                parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                parent.append($("<div class='invalid-feedback'>This field is required</div>"));
                return false;
            }
            
            $.ajax({
                type: "POST",
                data: {"_token": $('meta[name="csrf-token"]').attr('content'),"checklist": opentime, "id": parent.data('id')},
                url: '{{URL::to('/admin/renamechecklistdoc')}}',
                success: function(result){
                    var obj = JSON.parse(result);
                    if (obj.status) {
                        parent.empty()
                            .data('id', obj.Id)
                            .data('personalchecklistname', opentime)
                            .append(
                                $('<span>').html(obj.checklist)
                            );
                        if ($('#grid_'+obj.Id).length) {
                            $('#grid_'+obj.Id).html(obj.checklist);
                        }
                    } else {
                        parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                        parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">An error occurred while saving</div>'));
                }
            });
            return false;
        });


        //Rename File Name Visa Document
        $(document).on('click', '.migdocumnetlist1 .renamedoc', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Rename visa document clicked:', this);
            
            var parent = $(this).closest('.drow').find('.doc-row');
            console.log('Found visa doc-row parent:', parent);
            console.log('Parent length:', parent.length);
            
            if (parent.length === 0) {
                console.error('Visa document row not found');
                return false;
            }
            
            parent.data('current-html', parent.html());
            var opentime = parent.data('name');
            console.log('Visa document name:', opentime);
            
            if (!opentime) {
                console.error('Visa document name not found');
                return false;
            }
            
            parent.empty().append(
                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
            );
            
            console.log('Rename visa document interface created successfully');
            return false;
        });


        $(document).on('click', '.migdocumnetlist1 .btn-danger', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Cancel rename visa document clicked');
            
            var parent = $(this).closest('.drow').find('.doc-row');
            if (parent.length === 0) {
                console.error('Visa document row not found for cancel');
                return false;
            }
            
            var hourid = parent.data('id');
            if (hourid) {
                parent.html(parent.data('current-html'));
            } else {
                parent.remove();
            }
            return false;
        });

        $(document).on('click', '.migdocumnetlist1 .btn-primary', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Save rename visa document clicked');
            
            var parent = $(this).closest('.drow').find('.doc-row');
            if (parent.length === 0) {
                console.error('Visa document row not found for save');
                return false;
            }
            
            parent.find('.opentime').removeClass('is-invalid');
            parent.find('.invalid-feedback').remove();
            var opentime = parent.find('.opentime').val();
            
            if (!opentime) {
                parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                parent.append($("<div class='invalid-feedback'>This field is required</div>"));
                return false;
            }
            
            console.log('Saving visa document name:', opentime);
            
            $.ajax({
                type: "POST",
                data: {"_token": $('meta[name="csrf-token"]').attr('content'),"filename": opentime, "id": parent.data('id')},
                url: '{{URL::to('/admin/renamedoc')}}',
                success: function(result){
                    var obj = JSON.parse(result);
                    if (obj.status) {
                        var previewUrl = obj.fileurl;
                        var filetype = obj.filetype;
                        var folderName = obj.folder_name;
                        var fileName = obj.filename + '.' + obj.filetype;

                        parent.empty()
                            .data('id', obj.Id)
                            .data('name', opentime)
                            .append(
                                $('<a>', {
                                    href: 'javascript:void(0);',
                                    onclick: `previewFile('${filetype}', '${previewUrl}', '${folderName}')`
                                }).append(
                                    $('<i>', { class: 'fas fa-file-image' }),
                                    ' ',
                                    $('<span>').text(fileName)
                                )
                            );

                        if ($('#grid_'+obj.Id).length) {
                            $('#grid_'+obj.Id).html(fileName);
                        }
                        
                        // 🔁 Update the Preview & Download links in dropdown
                        var dropdownMenu = $(parent).closest('.drow').find('.dropdown-menu');

                        // Preview link
                        dropdownMenu.find('.dropdown-item[href^="http"]').filter(function() {
                            return $(this).text().trim() === 'Preview';
                        }).attr('href', previewUrl);

                        // Download link
                        dropdownMenu.find('.dropdown-item.download-file')
                            .attr('data-filelink', previewUrl)
                            .attr('data-filename', fileName);
                            
                        console.log('Visa document renamed successfully');
                    } else {
                        parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                        parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                        console.error('Failed to rename visa document:', obj.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">An error occurred while saving</div>'));
                }
            });
            return false;
        });

        //Rename Checklist Name Visa Document
        $(document).on('click', '.migdocumnetlist1 .renamechecklist', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Rename visa checklist clicked:', this);
            
            var parent = $(this).closest('.drow').find('.visachecklist-row');
            console.log('Found visa checklist parent:', parent);
            console.log('Parent length:', parent.length);
            
            if (parent.length === 0) {
                console.error('Visa checklist row not found');
                return false;
            }
            
            parent.data('current-html', parent.html());
            var opentime = parent.data('visachecklistname');
            console.log('Visa checklist name:', opentime);
            
            if (!opentime) {
                //console.error('Visa checklist name not found');
                return false;
            }
            
            parent.empty().append(
                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                $('<button class="btn btn-visaprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                $('<button class="btn btn-visadanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
            );
            
            //('Rename visa checklist interface created successfully');
            return false;
        });


        $(document).on('click', '.migdocumnetlist1 .btn-visadanger', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
           // console.log('Cancel rename visa checklist clicked');
            
            var parent = $(this).closest('.drow').find('.visachecklist-row');
            if (parent.length === 0) {
                //console.error('Visa checklist row not found for cancel');
                return false;
            }
            
            var hourid = parent.data('id');
            if (hourid) {
                parent.html(parent.data('current-html'));
            } else {
                parent.remove();
            }
            return false;
        });

        $(document).on('click', '.migdocumnetlist1 .btn-visaprimary', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Save rename visa checklist clicked');
            
            var parent = $(this).closest('.drow').find('.visachecklist-row');
            if (parent.length === 0) {
                //console.error('Visa checklist row not found for save');
                return false;
            }
            
            parent.find('.opentime').removeClass('is-invalid');
            parent.find('.invalid-feedback').remove();
            var opentime = parent.find('.opentime').val();
            
            if (!opentime) {
                parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                parent.append($("<div class='invalid-feedback'>This field is required</div>"));
                return false;
            }
            
            console.log('Saving visa checklist name:', opentime);
            
            $.ajax({
                type: "POST",
                data: {"_token": $('meta[name="csrf-token"]').attr('content'),"checklist": opentime, "id": parent.data('id')},
                url: '{{URL::to('/admin/renamechecklistdoc')}}',
                success: function(result){
                    var obj = JSON.parse(result);
                    if (obj.status) {
                        parent.empty()
                            .data('id', obj.Id)
                            .data('visachecklistname', opentime)
                            .append(
                                $('<span>').html(obj.checklist)
                            );
                        
                        if ($('#grid_'+obj.Id).length) {
                            $('#grid_'+obj.Id).html(obj.checklist);
                        }
                        
                        console.log('Visa checklist renamed successfully');
                    } else {
                        parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                        parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                       console.error('Failed to rename visa checklist:', obj.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">An error occurred while saving</div>'));
                }
            });
            return false;
        });



        <?php
        $appointmentdata = array();
        $json = json_encode ( $appointmentdata, JSON_FORCE_OBJECT );
        ?>
        $(document).delegate('.appointmentdata', 'click', function () {
            var v = $(this).attr('data-id');
            $('.appointmentdata').removeClass('active');
            $(this).addClass('active');
            var res = $.parseJSON('<?php echo $json; ?>');

            $('.appointmentname').html(res[v].title);
            $('.appointmenttime').html(res[v].time);
            $('.appointmentdate').html(res[v].date);
            $('.appointmentdescription').html(res[v].description);
            $('.appointmentcreatedby').html(res[v].createdby);
            $('.appointmentcreatedname').html(res[v].createdname);
            $('.appointmentcreatedemail').html(res[v].createdemail);
            $('.editappointment .edit_link').attr('data-id', v);
        });

        $(document).delegate('.opencreate_task', 'click', function () {
            $('#tasktermform')[0].reset();
            $('#tasktermform select').val('').trigger('change');
            $('.create_task').modal('show');
            $('.ifselecttask').hide();
            $('.ifselecttask select').attr('data-valid', '');
        });

        var eduid = '';
        $(document).delegate('.deleteeducation', 'click', function(){
            eduid = $(this).attr('data-id');
            $('#confirmEducationModal').modal('show');
        });

        $(document).delegate('#confirmEducationModal .accepteducation', 'click', function(){
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/')}}/delete-education',
                type:'GET',
                datatype:'json',
                data:{edu_id:eduid},
                success:function(response){
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    $('#confirmEducationModal').modal('hide');
                    if(res.status){
                        $('#edu_id_'+eduid).remove();
                    }else{
                        alert('Please try again')
                    }
                }
            });
        });

        $(document).delegate('#educationform #subjectlist', 'change', function(){
            var v = $('#educationform #subjectlist option:selected').val();
            if(v != ''){
                $('.popuploader').show();
                $.ajax({
                    url: '{{URL::to('/admin/getsubjects')}}',
                    type:'GET',
                    data:{cat_id:v},
                    success:function(response){
                        $('.popuploader').hide();
                        $('#educationform #subject').html(response);
                        $(".add_appliation #subject").val('').trigger('change');
                    }
                });
            }
        });

        $(document).delegate('.edit_appointment', 'click', function(){
            var v = $(this).attr('data-id');
            $('.popuploader').show();
            $('#edit_appointment').modal('show');
            $.ajax({
                url: '{{URL::to('/admin/getAppointmentdetail')}}',
                type:'GET',
                data:{id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('.showappointmentdetail').html(response);
                    $(".datepicker").daterangepicker({
                        locale: { format: "YYYY-MM-DD" },
                        singleDatePicker: true,
                        showDropdowns: true
                    });
                    $(".timepicker").timepicker({
                            icons: {
                                up: "fas fa-chevron-up",
                                down: "fas fa-chevron-down"
                            }
                    });
                    $(".timezoneselects2").select2({
                        dropdownParent: $("#edit_appointment")
                    });

                    $(".invitesselects2").select2({
                        dropdownParent: $("#edit_appointment")
                    });
                }
            });
        });

        $(".applicationselect2").select2({
            dropdownParent: $(".add_appliation")
        });
        $(".partner_branchselect2").select2({
            dropdownParent: $(".add_appliation")
        });
        $(".approductselect2").select2({
            dropdownParent: $(".add_appliation")
        });
            $(".workflowselect2").select2({
            dropdownParent: $(".add_interested_service")
        });
        $(".partnerselect2").select2({
            dropdownParent: $(".add_interested_service")
        });
        $(".productselect2").select2({
            dropdownParent: $(".add_interested_service")
        });
        $(".branchselect2").select2({
            dropdownParent: $(".add_interested_service")
        });

        $(document).delegate('.editeducation', 'click', function(){
            var v = $(this).attr('data-id');
            $('.popuploader').show();
            $('#edit_education').modal('show');
            $.ajax({
                url: '{{URL::to('/admin/getEducationdetail')}}',
                type:'GET',
                data:{id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('.showeducationdetail').html(response);
                    $(".datepicker").daterangepicker({
                        locale: { format: "YYYY-MM-DD" },
                        singleDatePicker: true,
                        showDropdowns: true
                    });

                }
            });
        });

        $(document).delegate('.interest_service_view', 'click', function(){
            var v = $(this).attr('data-id');
            $('.popuploader').show();
            $('#interest_service_view').modal('show');
            $.ajax({
                url: '{{URL::to('/admin/getintrestedservice')}}',
                type:'GET',
                data:{id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('.showinterestedservice').html(response);
                }
            });
        });


        $(document).delegate('.openeditservices', 'click', function(){
            var v = $(this).attr('data-id');
            $('.popuploader').show();
            $('#interest_service_view').modal('hide');
            $('#eidt_interested_service').modal('show');
            $.ajax({
                url: '{{URL::to('/admin/getintrestedserviceedit')}}',
                type:'GET',
                data:{id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('.showinterestedserviceedit').html(response);
                        $(".workflowselect2").select2({
                        dropdownParent: $("#eidt_interested_service")
                    });

                    $(".partnerselect2").select2({
                        dropdownParent: $("#eidt_interested_service")
                    });

                    $(".productselect2").select2({
                        dropdownParent: $("#eidt_interested_service")
                    });

                    $(".branchselect2").select2({
                        dropdownParent: $("#eidt_interested_service")
                    });
                    $(".datepicker").daterangepicker({
                        locale: { format: "YYYY-MM-DD" },
                        singleDatePicker: true,
                        showDropdowns: true
                    });
                }
            });
        });

        $(document).delegate('.opencommissioninvoice', 'click', function(){
            $('#opencommissionmodal').modal('show');
        });

        $(document).delegate('.opengeneralinvoice', 'click', function(){
            $('#opengeneralinvoice').modal('show');
        });

        //Convert Lead to Client Popup
        $(document).delegate('.convertLeadToClient', 'click', function(){
            $('#convertLeadToClientModal').modal('show');
            $('#sel_migration_agent_id,#sel_person_responsible_id,#sel_person_assisting_id,#sel_matter_id').select2({
                dropdownParent: $('#convertLeadToClientModal')
            });
        });

        //Change matter assignee
        $(document).delegate('.changeMatterAssignee', 'click', function(){
            let selectedMatterLM;

            if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                // If checkbox is checked, get its value
                selectedMatterLM = $('.general_matter_checkbox_client_detail').val();
            } else {
                // If checkbox is not checked, get the value from the dropdown
                selectedMatterLM = $('#sel_matter_id_client_detail').val();
            }

            console.log('selectedMatterLM==='+selectedMatterLM);
            $('#selectedMatterLM').val(selectedMatterLM);

            $.ajax({
                type:'post',
                url: '{{URL::to('/admin/clients/fetchClientMatterAssignee')}}',
                sync:true,
                data: { client_matter_id:selectedMatterLM},
                success: function(response){
                    var obj = $.parseJSON(response);

                    $('#change_sel_migration_agent_id').val(obj.matter_info.sel_migration_agent);
                    $('#change_sel_person_responsible_id').val(obj.matter_info.sel_person_responsible);
                    $('#change_sel_person_assisting_id').val(obj.matter_info.sel_person_assisting);

                    $('#changeMatterAssigneeModal').modal('show');
                    $('#change_sel_migration_agent_id,#change_sel_person_responsible_id,#change_sel_person_assisting_id').select2({
                        dropdownParent: $('#changeMatterAssigneeModal')
                    });
                }
            });
        });

        //Account Tab Receipt Popup
        $(document).delegate('.createreceipt', 'click', function(){
            $('#createreceiptmodal').modal('show');

            // Wait for the modal to be fully shown to check for the visible form
            $('#createreceiptmodal').on('shown.bs.modal', function() {
                // Find the visible form inside the modal
                const activeForm = $(this).find('.form-type:visible');
                // Get the form ID or any attribute you want
                const activeFormId = activeForm.attr('id');
                console.log('Active form when modal closed:', activeFormId);
                //var selectedMatter = $('#sel_matter_id_client_detail').val();
                // Get the value based on checkbox state
                let selectedMatter;

                if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                    // If checkbox is checked, get its value
                    selectedMatter = $('.general_matter_checkbox_client_detail').val();
                    console.log('Checkbox is checked, selected value:', selectedMatter);
                } else {
                    // If checkbox is not checked, get the value from the dropdown
                    selectedMatter = $('#sel_matter_id_client_detail').val();
                    console.log('Checkbox is not checked, selected dropdown value:', selectedMatter);
                }

                console.log('selectedMatter==='+selectedMatter);
                // You can also take action based on the form
                if (activeFormId === 'client_receipt_form') {
                    listOfInvoice();
                    clientLedgerBalanceAmount(selectedMatter);
                    $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
                    $('#client_matter_id_ledger').val(selectedMatter);
                }
                else if (activeFormId === 'invoice_receipt_form')
                {
                    $('.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
                    $('#client_matter_id_invoice').val(selectedMatter);
                }
                else if (activeFormId === 'office_receipt_form')
                {
                    listOfInvoice();
                    //var recordCnt = isAnyInvoiceNoExistInDB();
                    $('.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
                    $('#client_matter_id_office').val(selectedMatter);
                }
            });
        });

        $('#createreceiptmodal,#createadjustinvoicereceiptmodal').on('show.bs.modal', function() {
            $('.modal-dialog').css('max-width', '85%');
        });

        $('#createReceiptModal').on('hide.bs.modal', function () {
            const activeForm = $(this).find('.form-type:visible');
            const activeFormId = activeForm.attr('id');
            console.log('Active form when modal is closing:', activeFormId);

            // You can also take action based on the form
            if (activeFormId === 'client_receipt_form') {
                console.log('Client Receipt form was active');
                $('#client_receipt_form')[0].reset();
                $('.total_deposit_amount_all_rows').text("");
                //$('#sel_client_agent_id').val("").trigger('change');
                $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
                $('#client_matter_id_ledger').val("");
            }
            else if (activeFormId === 'invoice_receipt_form') {
                console.log('Invoice form was active');
                $('#function_type').val("");
                $('#invoice_receipt_form')[0].reset();
                $('.total_deposit_amount_all_rows_invoice').text("");
                //$('#sel_invoice_agent_id').val("").trigger('change');
                $('.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
                $('#client_matter_id_invoice').val("");
            }
            else if (activeFormId === 'office_receipt_form') {
                console.log('Office Receipt form was active');
                $('#office_receipt_form')[0].reset();
                $('.total_withdraw_amount_all_rows_office').text("");
                //$('#sel_office_agent_id').val("").trigger('change');
                $('.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
                $('#client_matter_id_office').val("");
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).delegate('.createclientreceipt', 'click', function(){
            getTopReceiptValInDB(1);
            $('#createclientreceiptmodal').modal('show');
        });

        $(document).delegate('.createofficereceipt', 'click', function(){
            getTopReceiptValInDB(2);
            listOfInvoice();
            var recordCnt = isAnyInvoiceNoExistInDB();
        });

        $(document).delegate('.createinvoicereceipt', 'click', function(){
            getTopReceiptValInDB(3);
            $('#function_type').val("add");
            $('#createinvoicereceiptmodal').modal('show');
        });

        $(document).delegate('.createjournalreceipt', 'click', function(){
            getTopReceiptValInDB(4);
            listOfInvoice();
            $('#createjournalreceiptmodal').modal('show');
        });

        $(document).delegate('.updatedraftinvoice', 'click', function(){
            var receiptid = $(this).data('receiptid');
            $('#function_type').val("edit");
            console.log('receiptid'+receiptid);
            getInfoByReceiptId(receiptid);
        });

        //adjust invocie
        $(document).delegate('.adjustinvoice', 'click', function(){
            $('#createadjustinvoicereceiptmodal').modal('show');
            $('#function_type').val("add");
            getTopInvoiceNoFromDB(3);
        });

        $('#createclientreceiptmodal,#createofficereceiptmodal,#createjournalreceiptmodal').on('show.bs.modal', function() {
            $('.modal-dialog').css('max-width', '80%');
        });

        $('#createinvoicereceiptmodal').on('show.bs.modal', function() {
            $('.modal-dialog').css('max-width', '85%');
        });

        //On Close Hide all content from popups
        $('#createclientreceiptmodal').on('hidden.bs.modal', function() {
            $('#create_client_receipt')[0].reset();
            $('.total_deposit_amount_all_rows').text("");
            $('#sel_client_agent_id').val("").trigger('change');
            $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });

        $('#createinvoicereceiptmodal').on('hidden.bs.modal', function() {
            $('#function_type').val("");
            $('#create_invoice_receipt')[0].reset();
            $('.total_deposit_amount_all_rows_invoice').text("");
            $('#sel_invoice_agent_id').val("").trigger('change');
            $('.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });

        $('#createofficereceiptmodal').on('hidden.bs.modal', function() {
            $('#create_office_receipt')[0].reset();
            $('.total_withdraw_amount_all_rows_office').text("");
            $('#sel_office_agent_id').val("").trigger('change');
            $('.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });

        $('#createjournalreceiptmodal').on('hidden.bs.modal', function() {
            $('#create_journal_receipt')[0].reset();
            $('.total_withdraw_amount_all_rows_journal').text("");
            $('#sel_journal_agent_id').val("").trigger('change');
            $('.report_entry_date_fields_journal').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        });


        $(document).delegate('.addpaymentmodal','click', function(){
            var v = $(this).attr('data-invoiceid');
            var netamount = $(this).attr('data-netamount');
            var dueamount = $(this).attr('data-dueamount');
            $('#invoice_id').val(v);
            $('.invoicenetamount').html(netamount+' AUD');
            $('.totldueamount').html(dueamount);
            $('.totldueamount').attr('data-totaldue', dueamount);
            $('#addpaymentmodal').modal('show');
            $('.payment_field_clone').remove();
            $('.paymentAmount').val('');
        });

        $(document).delegate('.paymentAmount','keyup', function(){
		    grandtotal();
        });

		function grandtotal(){
			var p =0;
			$('.paymentAmount').each(function(){
				if($(this).val() != ''){
					p += parseFloat($(this).val());
				}
			});

			var tamount = $('.totldueamount').attr('data-totaldue');
            var am = parseFloat(tamount) - parseFloat(p);
			$('.totldueamount').html(am.toFixed(2));
		}

        $('.add_payment_field a').on('click', function(){
            var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();
            $('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">'+clonedval+'</div>');
        });

        $('.add_fee_type a.fee_type_btn').on('click', function(){
            var clonedval = $('.fees_type_sec .fee_type_row .fees_type_col').html();
            $('.fees_type_sec .fee_type_row').append('<div class="custom_type_col fees_type_clone">'+clonedval+'</div>');
        });

        $(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){
            var $tr    = $(this).closest('.payment_field_clone');
            var trclone = $('.payment_field_clone').length;
            if(trclone > 0){
                $tr.remove();
                grandtotal();
            }
        });
        $(document).delegate('.fees_type_sec .fee_type_row .fees_type_clone a.remove_btn', 'click', function(){
            var $tr    = $(this).closest('.fees_type_clone');
            var trclone = $('.fees_type_clone').length;
            if(trclone > 0){
                $tr.remove();
                grandtotal();
            }
        });

        <?php
        if(isset($_GET['tab']) && $_GET['tab'] == 'application' && isset($_GET['appid']) && $_GET['appid'] != ''){
            ?>
            var appliid = '{{@$_GET['appid']}}';
            $('.if_applicationdetail').hide();
            $('.ifapplicationdetailnot').show();
            $.ajax({
                url: '{{URL::to('/admin/getapplicationdetail')}}',
                type:'GET',
                data:{id:appliid},
                success:function(response){
                    $('.popuploader').hide();
                    $('.ifapplicationdetailnot').html(response);
                    $('.datepicker').daterangepicker({
                    locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
                                    singleDatePicker: true,

                                    showDropdowns: true,
                    }, function(start, end, label) {
                        $('#popuploader').show();
                        $.ajax({
                            url:"{{URL::to('/admin/application/updateintake')}}",
                            method: "GET", // or POST
                            dataType: "json",
                            data: {from: start.format('YYYY-MM-DD'), appid: appliid},
                            success:function(result) {
                                $('#popuploader').hide();
                                console.log("sent back -> do whatever you want now");
                            }
                        });
                    });

                    $('.expectdatepicker').daterangepicker({
                    locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
                                    singleDatePicker: true,

                                    showDropdowns: true,
                    }, function(start, end, label) {
                        $('#popuploader').show();
                        $.ajax({
                            url:"{{URL::to('/admin/application/updateexpectwin')}}",
                            method: "GET", // or POST
                            dataType: "json",
                            data: {from: start.format('YYYY-MM-DD'), appid: appliid},
                            success:function(result) {
                                $('#popuploader').hide();
                                console.log("sent back -> do whatever you want now");
                            }
                        });
                    });

                    $('.startdatepicker').daterangepicker({
                    locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
                                    singleDatePicker: true,

                                    showDropdowns: true,
                    }, function(start, end, label) {
                        $('#popuploader').show();
                        $.ajax({
                            url:"{{URL::to('/admin/application/updatedates')}}",
                            method: "GET", // or POST
                            dataType: "json",
                            data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'start'},
                            success:function(result) {
                                $('#popuploader').hide();
                                    var obj = result;
                                if(obj.status){
                                    $('.app_start_date .month').html(obj.dates.month);
                                    $('.app_start_date .day').html(obj.dates.date);
                                    $('.app_start_date .year').html(obj.dates.year);
                                }
                                console.log("sent back -> do whatever you want now");
                            }
                        });
                    });

                    $('.enddatepicker').daterangepicker({
                    locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
                                    singleDatePicker: true,

                                    showDropdowns: true,
                    }, function(start, end, label) {
                        $('#popuploader').show();
                        $.ajax({
                            url:"{{URL::to('/admin/application/updatedates')}}",
                            method: "GET", // or POST
                            dataType: "json",
                            data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'end'},
                            success:function(result) {
                                $('#popuploader').hide();
                                    var obj =result;
                                if(obj.status){
                                    $('.app_end_date .month').html(obj.dates.month);
                                    $('.app_end_date .day').html(obj.dates.date);
                                    $('.app_end_date .year').html(obj.dates.year);
                                }
                                console.log("sent back -> do whatever you want now");
                            }
                        });
                    });

                }
            });
            <?php
        }
        ?>

        $(document).delegate('.discon_application', 'click', function(){
            var appliid = $(this).attr('data-id');
            $('#discon_application').modal('show');
            $('input[name="diapp_id"]').val(appliid);
        });

        $(document).delegate('.revertapp', 'click', function(){
            var appliid = $(this).attr('data-id');
            $('#revert_application').modal('show');
            $('input[name="revapp_id"]').val(appliid);
        });

        $(document).delegate('.completestage', 'click', function(){
            var appliid = $(this).attr('data-id');
            $('#confirmcompleteModal').modal('show');
            $('.acceptapplication').attr('data-id',appliid)

        });

        $(document).delegate('.openapplicationdetail', 'click', function(){
            var appliid = $(this).attr('data-id');
            $('.if_applicationdetail').hide();
            $('.ifapplicationdetailnot').show();
            $.ajax({
                url: '{{URL::to('/admin/getapplicationdetail')}}',
                type:'GET',
                data:{id:appliid},
                success:function(response){
                    $('.popuploader').hide();
                    $('.ifapplicationdetailnot').html(response);
                    $('.datepicker').daterangepicker({
                        locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
                        singleDatePicker: true,
                        showDropdowns: true,
                    }, function(start, end, label) {
                        $('#popuploader').show();
                        $.ajax({
                            url:"{{URL::to('/admin/application/updateintake')}}",
                            method: "GET", // or POST
                            dataType: "json",
                            data: {from: start.format('YYYY-MM-DD'), appid: appliid},
                            success:function(result) {
                                $('#popuploader').hide();
                                console.log("sent back -> do whatever you want now");
                            }
                        });
                    });

                    $('.expectdatepicker').daterangepicker({
                        locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
                        singleDatePicker: true,
                        showDropdowns: true,
                    }, function(start, end, label) {
                        $('#popuploader').show();
                        $.ajax({
                            url:"{{URL::to('/admin/application/updateexpectwin')}}",
                            method: "GET", // or POST
                            dataType: "json",
                            data: {from: start.format('YYYY-MM-DD'), appid: appliid},
                            success:function(result) {
                                $('#popuploader').hide();
                            }
                        });
                    });

                    $('.startdatepicker').daterangepicker({
                        locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
                        singleDatePicker: true,
                        showDropdowns: true,
                    }, function(start, end, label) {
                        $('#popuploader').show();
                        $.ajax({
                            url:"{{URL::to('/admin/application/updatedates')}}",
                            method: "GET", // or POST
                            dataType: "json",
                            data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'start'},
                            success:function(result) {
                                $('#popuploader').hide();
                                var obj = result;
                                if(obj.status){
                                    $('.app_start_date .month').html(obj.dates.month);
                                    $('.app_start_date .day').html(obj.dates.date);
                                    $('.app_start_date .year').html(obj.dates.year);
                                }
                                console.log("sent back -> do whatever you want now");
                            }
                        });
                    });

                    $('.enddatepicker').daterangepicker({
                        locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
                        singleDatePicker: true,
                        showDropdowns: true,
                    }, function(start, end, label) {
                        $('#popuploader').show();
                        $.ajax({
                            url:"{{URL::to('/admin/application/updatedates')}}",
                            method: "GET", // or POST
                            dataType: "json",
                            data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'end'},
                            success:function(result) {
                                $('#popuploader').hide();
                                var obj = result;
                                if(obj.status){
                                    $('.app_end_date .month').html(obj.dates.month);
                                    $('.app_end_date .day').html(obj.dates.date);
                                    $('.app_end_date .year').html(obj.dates.year);
                                }
                                console.log("sent back -> do whatever you want now");
                            }
                        });
                    });
                }
            });
        });


        $(document).delegate('#application-tab,#stages-tab', 'click', function(){
            $('.if_applicationdetail').show();
            $('.ifapplicationdetailnot').hide();
            $('.ifapplicationdetailnot').html('<h4>Please wait ...</h4>');
        });

        $(document).delegate('.openappnote', 'click', function(){
            var apptype = $(this).attr('data-app-type');
            var id = $(this).attr('data-id');
            $('#create_applicationnote #noteid').val(id);
            $('#create_applicationnote #type').val(apptype);
            $('#create_applicationnote').modal('show');
        });
        $(document).delegate('.openappappoint', 'click', function(){
            var id = $(this).attr('data-id');
            var apptype = $(this).attr('data-app-type');
            $('#create_applicationappoint #type').val(apptype);
            $('#create_applicationappoint #appointid').val(id);
            $('#create_applicationappoint').modal('show');
        });

        $(document).delegate('.openclientemail', 'click', function(){
            var id = $(this).attr('data-id');
            var apptype = $(this).attr('data-app-type');
            $('#applicationemailmodal #type').val(apptype);
            $('#applicationemailmodal #appointid').val(id);
            $('#applicationemailmodal').modal('show');
        });

        $(document).delegate('.openchecklist', 'click', function(){
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            var typename = $(this).attr('data-typename');
            $('#create_checklist #checklistapp_id').val(id);
            $('#create_checklist #checklist_type').val(type);
            $('#create_checklist #checklist_typename').val(typename);
            $('#create_checklist').modal('show');
        });

        $(document).delegate('.openpaymentschedule', 'click', function(){
            var id = $(this).attr('data-id');
            //$('#create_apppaymentschedule #application_id').val(id);
            $('#addpaymentschedule').modal('show');
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/addscheduleinvoicedetail')}}',
                type: 'GET',
                data: {id: $(this).attr('data-id')},
                success: function(res){
                    $('.popuploader').hide();
                    $('.showpoppaymentscheduledata').html(res);
                    $(".datepicker").daterangepicker({
                        locale: { format: "YYYY-MM-DD" },
                        singleDatePicker: true,
                        showDropdowns: true
                    });
                }
            });
        });

        $(document).delegate('.addfee', 'click', function(){
            var clonedval = $('.feetypecopy').html();
            $('.fee_type_sec .fee_fields').append('<div class="fee_fields_row field_clone">'+clonedval+'</div>');
        });

        $(document).delegate('.payremoveitems', 'click', function(){
            $(this).parent().parent().remove();
            schedulecalculatetotal();
        });

        $(document).delegate('.payfee_amount', 'keyup', function(){
            schedulecalculatetotal();
        });

        $(document).delegate('.paydiscount', 'keyup', function(){
            schedulecalculatetotal();
        });

        function schedulecalculatetotal(){
            var feeamount = 0;
            $('.payfee_amount').each(function(){
                if($(this).val() != ''){
                    feeamount += parseFloat($(this).val());
                }
            });
            var discount = 0;
            if($('.paydiscount').val() != ''){
                discount = $('.paydiscount').val();
            }
            var netfee = feeamount - parseFloat(discount);
            $('.paytotlfee').html(feeamount.toFixed(2));
            $('.paynetfeeamt').html(netfee.toFixed(2));
        }

        $(document).delegate('.createaddapointment', 'click', function(){
            $('#create_appoint').modal('show');
        });

        $(document).delegate('.openfileupload', 'click', function(){
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            var typename = $(this).attr('data-typename');
            var aid = $(this).attr('data-aid');
            $(".checklisttype").val(type);
            $(".checklistid").val(id);
            $(".checklisttypename").val(typename);
            $(".application_id").val(aid);
            $('#openfileuploadmodal').modal('show');
        });

        $(document).delegate('.opendocnote', 'click', function(){
            var id = '';
            var type = $(this).attr('data-app-type');
            var aid = $(this).attr('data-id');
            $(".checklisttype").val(type);
            $(".checklistid").val(id);
            $(".application_id").val(aid);
            $('#openfileuploadmodal').modal('show');
        });

        $(document).delegate('.due_date_sec a.due_date_btn', 'click', function(){
            $('.due_date_sec .due_date_col').show();
            $(this).hide();
            $('.checklistdue_date').val(1);
        });

        $(document).delegate('.remove_col a.remove_btn', 'click', function(){
            $('.due_date_sec .due_date_col').hide();
            $('.due_date_sec a.due_date_btn').show();
            $('.checklistdue_date').val(0);
        });

        $(document).delegate('.nextstage', 'click', function(){
            var appliid = $(this).attr('data-id');
            var stage = $(this).attr('data-stage');
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/updatestage')}}',
                type:'GET',
                datatype:'json',
                data:{id:appliid, client_id:'{{$fetchedData->id}}'},
                success:function(response){
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                        $('.curerentstage').text(obj.stage);
                        $('.progress-circle span').html(obj.width+' %');
                        var over = '';
                        if(obj.width > 50){
                            over = '50';
                        }
                        $("#progresscir").removeClass();
                        $("#progresscir").addClass('progress-circle');
                        $("#progresscir").addClass('prgs_'+obj.width);
                        $("#progresscir").addClass('over_'+over);
                        if(obj.displaycomplete){

                            $('.completestage').show();
                            $('.nextstage').hide();
                        }
                        $.ajax({
                            url: site_url+'/admin/get-applications-logs',
                            type:'GET',
                            data:{clientid:'{{$fetchedData->id}}',id: appliid},
                            success: function(responses){

                                $('#accordion').html(responses);
                            }
                        });
                    }else{
                        $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                    }
                }
            });
        });

        $(document).delegate('.acceptapplication', 'click', function(){
            var appliid = $(this).attr('data-id');

            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/completestage')}}',
                type:'GET',
                datatype:'json',
                data:{id:appliid, client_id:'{{$fetchedData->id}}'},
                success:function(response){
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        $('.progress-circle span').html(obj.width+' %');
                        var over = '';
                        if(obj.width > 50){
                            over = '50';
                        }
                        $("#progresscir").removeClass();
                        $("#progresscir").addClass('progress-circle');
                        $("#progresscir").addClass('prgs_'+obj.width);
                        $("#progresscir").addClass('over_'+over);
                        $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                            $('.ifdiscont').hide();
                            $('.revertapp').show();
                        $('#confirmcompleteModal').modal('hide');
                        $.ajax({
                                url: site_url+'/admin/get-applications-logs',
                                type:'GET',
                                data:{clientid:'{{$fetchedData->id}}',id: appliid},
                                success: function(responses){

                                    $('#accordion').html(responses);
                                }
                            });
                    }else{
                        $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                    }
                }
            });
        });

        $(document).delegate('.backstage', 'click', function(){
            var appliid = $(this).attr('data-id');
            var stage = $(this).attr('data-stage');
            if(stage == 'Application'){

            }else{
                $('.popuploader').show();
                $.ajax({
                    url: '{{URL::to('/admin/updatebackstage')}}',
                    type:'GET',
                    datatype:'json',
                    data:{id:appliid, client_id:'{{$fetchedData->id}}'},
                    success:function(response){
                        var obj = $.parseJSON(response);
                        $('.popuploader').hide();
                        if(obj.status){
                            $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                            $('.curerentstage').text(obj.stage);
                            $('.progress-circle span').html(obj.width+' %');
                        var over = '';
                        if(obj.width > 50){
                            over = '50';
                        }
                        $("#progresscir").removeClass();
                        $("#progresscir").addClass('progress-circle');
                        $("#progresscir").addClass('prgs_'+obj.width);
                        $("#progresscir").addClass('over_'+over);
                            if(obj.displaycomplete == false){
                                $('.completestage').hide();
                                $('.nextstage').show();
                            }
                            $.ajax({
                                url: site_url+'/admin/get-applications-logs',
                                type:'GET',
                                data:{clientid:'{{$fetchedData->id}}',id: appliid},
                                success: function(responses){

                                    $('#accordion').html(responses);
                                }
                            });
                        }else{
                            $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                        }
                    }
                });
            }
        });


        $(document).delegate('#notes-tab', 'click', function(){
            var appliid = $(this).attr('data-id');
            $('.if_applicationdetail').hide();
            $('.ifapplicationdetailnot').show();
            $.ajax({
                url: '{{URL::to('/admin/getapplicationnotes')}}',
                type:'GET',
                data:{id:appliid},
                success:function(response){
                    $('.popuploader').hide();
                    $('#notes').html(response);
                }
            });
        });

        $(".timezoneselects2").select2({
            dropdownParent: $("#create_appoint")
        });

        $(".Inviteesselects2").select2({
            dropdownParent: $("#create_appoint")
        });

        $(".assignee").select2({
            dropdownParent: $("#opentaskmodal")
        });

        $(".timezoneselects2").select2({
            dropdownParent: $("#create_applicationappoint")
        });

        $('#attachments').on('change',function(){
            // output raw value of file input
            $('.showattachment').html('');
            // or, manipulate it further with regex etc.
            var filename = $(this).val().replace(/.*(\/|\\)/, '');
            // .. do your magic
            $('.showattachment').html(filename);
        });

        $(document).delegate('.opensuperagent', 'click', function(){
            var appid = $(this).attr('data-id');
            $('#superagent_application').modal('show');
            $('#superagent_application #siapp_id').val(appid);
        });

        $(document).delegate('.opentagspopup', 'click', function(){
            var appid = $(this).attr('data-id');
            $('#tags_clients').modal('show');
            $('#tags_clients #client_id').val(appid);
            $(".tagsselec").select2({ tags: true, dropdownParent: $("#tags_clients .modal-content") });
        });

        $(document).delegate('.serviceTaken','click', function(){
            $('#serviceTaken').modal('show');
        });

        $(document).delegate('.opensubagent', 'click', function(){
            var appid = $(this).attr('data-id');
            $('#subagent_application').modal('show');
            $('#subagent_application #sbapp_id').val(appid);
        });


        $(document).delegate('.removesuperagent', 'click', function(){
            var appid = $(this).attr('data-id');
        });

        $(document).delegate('.application_ownership', 'click', function(){
            var appid = $(this).attr('data-id');
            var ration = $(this).attr('data-ration');
            $('#application_ownership #mapp_id').val(appid);
            $('#application_ownership .sus_agent').val($(this).attr('data-name'));
            $('#application_ownership .ration').val(ration);
            $('#application_ownership').modal('show');
        });

        $(document).delegate('.opensaleforcast', 'click', function(){
            var fapp_id = $(this).attr('data-id');
            var client_revenue = $(this).attr('data-client_revenue');
            var partner_revenue = $(this).attr('data-partner_revenue');
            var discounts = $(this).attr('data-discounts');
            $('#application_opensaleforcast #fapp_id').val(fapp_id);
            $('#application_opensaleforcast #client_revenue').val(client_revenue);
            $('#application_opensaleforcast #partner_revenue').val(partner_revenue);
            $('#application_opensaleforcast #discounts').val(discounts);
            $('#application_opensaleforcast').modal('show');
        });

        $(document).delegate('.openpaymentfee', 'click', function(){
            var appliid = $(this).attr('data-id');
            $('.popuploader').show();
            $('#new_fee_option').modal('show');
            $.ajax({
                url: '{{URL::to('/admin/showproductfee')}}',
                type:'GET',
                data:{id:appliid},
                success:function(response){
                    $('.popuploader').hide();
                    $('.showproductfee').html(response);
                }
            });
        });

        $(document).delegate('.openpaymentfeeserv', 'click', function(){
            var appliid = $(this).attr('data-id');
            $('.popuploader').show();
            $('#interest_service_view').modal('hide');
            $('#new_fee_option_serv').modal('show');
            $.ajax({
                url: '{{URL::to('/admin/showproductfeeserv')}}',
                type:'GET',
                data:{id:appliid},
                success:function(response){
                    $('.popuploader').hide();
                    $('.showproductfeeserv').html(response);
                }
            });
            $(document).on("hidden.bs.modal", "#interest_service_view", function (e) {
                $('body').addClass('modal-open');
            });
        });

        $(document).delegate('.opensaleforcastservice', 'click', function(){
            var fapp_id = $(this).attr('data-id');
            var client_revenue = $(this).attr('data-client_revenue');
            var partner_revenue = $(this).attr('data-partner_revenue');
            var discounts = $(this).attr('data-discounts');
            $('#application_opensaleforcastservice #fapp_id').val(fapp_id);
            $('#application_opensaleforcastservice #client_revenue').val(client_revenue);
            $('#application_opensaleforcastservice #partner_revenue').val(partner_revenue);
            $('#application_opensaleforcastservice #discounts').val(discounts);
            $('#interest_service_view').modal('hide');
            $('#application_opensaleforcastservice').modal('show');
        });

        $(document).delegate('.closeservmodal', 'click', function(){
            $('#interest_service_view').modal('hide');
            $('#application_opensaleforcastservice').modal('hide');
        });

        $(document).on("hidden.bs.modal", "#application_opensaleforcastservice", function (e) {
            $('body').addClass('modal-open');
        });

	    $(document).delegate('#new_fee_option .fee_option_addbtn a', 'click', function(){
		    var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option><option value="Accommodation Fee">Accommodation Fee</option><option value="Administration Fee">Administration Fee</option><option value="Airline Ticket">Airline Ticket</option><option value="Airport Transfer Fee">Airport Transfer Fee</option><option value="Application Fee">Application Fee</option><option value="Bond">Bond</option></select></td><td><input type="number" value="0" class="form-control semester_amount" name="semester_amount[]"></td><td><input type="number" value="1" class="form-control no_semester" name="no_semester[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_semester[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';
		    $('#new_fee_option #productitemview tbody').append(html);
        });

        $(document).delegate('#new_fee_option .removefeetype', 'click', function(){
            $(this).parent().parent().remove();

            var price = 0;
            $('#new_fee_option .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });

            var discount_sem = $('.discount_sem').val();
            var discount_amount = $('.discount_amount').val();
            var cservd = 0.00;
            if(discount_sem != ''){
                cservd = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cservd);
            var duductdis = price - dis;

            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));
        });


        $(document).delegate('#new_fee_option .semester_amount','keyup', function(){
            var installment_amount = $(this).val();
            var cserv = 0.00;
            if(installment_amount != ''){
                cserv = installment_amount;
            }

            var installment = $(this).parent().parent().find('.no_semester').val();

            var totalamount = parseFloat(cserv) * parseInt(installment);
            $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
            $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
            var price = 0;
            $('#new_fee_option .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });

            var discount_sem = $('.discount_sem').val();
            var discount_amount = $('.discount_amount').val();
            var cservd = 0.00;
            if(discount_sem != ''){
                cservd = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cservd);
            var duductdis = price - dis;

            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));
        });


        $(document).delegate('#new_fee_option .no_semester','keyup', function(){
            var installment = $(this).val();


            var installment_amount = $(this).parent().parent().find('.semester_amount').val();
            var cserv = 0.00;
            if(installment_amount != ''){
                cserv = installment_amount;
            }
            var totalamount = parseFloat(cserv) * parseInt(installment);
            $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
            $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
            var price = 0;
            $('#new_fee_option .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });

            var discount_sem = $('.discount_sem').val();
            var discount_amount = $('.discount_amount').val();
            var cservd = 0.00;
            if(discount_sem != ''){
                cservd = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cservd);
            var duductdis = price - dis;

            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));
        });

        $(document).delegate('#new_fee_option .discount_amount','keyup', function(){
            var discount_amount = $(this).val();
            var discount_sem = $('.discount_sem').val();
            var cserv = 0.00;
            if(discount_sem != ''){
                cserv = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cserv);
            $('.totaldis span').html(dis.toFixed(2));
            var price = 0;
            $('#new_fee_option .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });
            var duductdis = price - dis;
            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));
            $('.totaldis .total_dis_am').val(dis.toFixed(2));

        });

        $(document).delegate('#new_fee_option .discount_sem','keyup', function(){
            var discount_sem = $(this).val();
            var discount_amount = $('.discount_amount').val();
            var cserv = 0.00;
            if(discount_sem != ''){
                cserv = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cserv);
            $('.totaldis span').html(dis.toFixed(2));
            $('.totaldis .total_dis_am').val(dis.toFixed(2));

            var price = 0;
            $('#new_fee_option .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });
            var duductdis = price - dis;
            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));

        });

        $(document).delegate('.editpaymentschedule', 'click', function(){
            $('#editpaymentschedule').modal('show');
            $('.popuploader').show();
            $.ajax({
                url: '{{URL::to('/admin/scheduleinvoicedetail')}}',
                type: 'GET',
                data: {id: $(this).attr('data-id'),t:'application'},
                success: function(res){
                    $('.popuploader').hide();
                    $('.showeditmodule').html(res);
                    $(".editclientname").select2({
                        dropdownParent: $("#editpaymentschedule .modal-content")
                    });

                    $(".datepicker").daterangepicker({
                        locale: { format: "YYYY-MM-DD" },
                        singleDatePicker: true,
                        showDropdowns: true
                    });
                }
            });
        });

    });

    $(document).ready(function() {
        $(document).delegate("#ddArea", "dragover", function() {
          $(this).addClass("drag_over");
          return false;
        });

        $(document).delegate("#ddArea", "dragleave", function() {
          $(this).removeClass("drag_over");
          return false;
        });

        $(document).delegate("#ddArea", "click", function(e) {
          file_explorer();
        });

        $(document).delegate("#ddArea", "drop", function(e) {
            e.preventDefault();
            $(this).removeClass("drag_over");
            var formData = new FormData();
            var files = e.originalEvent.dataTransfer.files;
            for (var i = 0; i < files.length; i++) {
                formData.append("file[]", files[i]);
            }
            uploadFormData(formData);
        });

        function file_explorer() {
            document.getElementById("selectfile").click();
            document.getElementById("selectfile").onchange = function() {
                files = document.getElementById("selectfile").files;
                var formData = new FormData();

                for (var i = 0; i < files.length; i++) {
                formData.append("file[]", files[i]);
                }
                formData.append("type", $('.checklisttype').val());
                formData.append("typename", $('.checklisttypename').val());
                formData.append("id", $('.checklistid').val());
                formData.append("application_id", $('.application_id').val());

                uploadFormData(formData);
            };
        }

        function uploadFormData(form_data) {
            $('.popuploader').show();
            $.ajax({
                url: "{{URL::to('/admin/application/checklistupload')}}",
                method: "POST",
                data: form_data,
                datatype: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
				    var obj = $.parseJSON(response);
                    $('.popuploader').hide();
                    $('#openfileuploadmodal').modal('hide');
                    $('.mychecklistdocdata').html(obj.doclistdata);
                    $('.checklistuploadcount').html(obj.applicationuploadcount);
			        $('.'+obj.type+'_checklists').html(obj.checklistdata);
			        $('#selectfile').val('');
                }
            });
        }


        $(document).delegate('#new_fee_option_serv .fee_option_addbtn a', 'click', function(){
            var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option><option value="Accommodation Fee">Accommodation Fee</option><option value="Administration Fee">Administration Fee</option><option value="Airline Ticket">Airline Ticket</option><option value="Airport Transfer Fee">Airport Transfer Fee</option><option value="Application Fee">Application Fee</option><option value="Bond">Bond</option></select></td><td><input type="number" value="0" class="form-control semester_amount" name="semester_amount[]"></td><td><input type="number" value="1" class="form-control no_semester" name="no_semester[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_semester[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';
            $('#new_fee_option_serv #productitemview tbody').append(html);
        });

        $(document).delegate('#new_fee_option_serv .removefeetype', 'click', function(){
            $(this).parent().parent().remove();

            var price = 0;
            $('#new_fee_option_serv .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });

            var discount_sem = $('#new_fee_option_serv .discount_sem').val();
            var discount_amount = $('#new_fee_option_serv .discount_amount').val();
            var cservd = 0.00;
            if(discount_sem != ''){
                cservd = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cservd);
            var duductdis = price - dis;

            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
        });


        $(document).delegate('#new_fee_option_serv .semester_amount','keyup', function(){
            var installment_amount = $(this).val();
            var cserv = 0.00;
            if(installment_amount != ''){
                cserv = installment_amount;
            }

            var installment = $(this).parent().parent().find('.no_semester').val();

            var totalamount = parseFloat(cserv) * parseInt(installment);
            $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
            $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
            var price = 0;
            $('#new_fee_option_serv .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });

            var discount_sem = $('#new_fee_option_serv .discount_sem').val();
            var discount_amount = $('#new_fee_option_serv .discount_amount').val();
            var cservd = 0.00;
            if(discount_sem != ''){
                cservd = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cservd);
            var duductdis = price - dis;

            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
        });


        $(document).delegate('#new_fee_option_serv .no_semester','keyup', function(){
            var installment = $(this).val();


            var installment_amount = $(this).parent().parent().find('.semester_amount').val();
            var cserv = 0.00;
            if(installment_amount != ''){
                cserv = installment_amount;
            }
            var totalamount = parseFloat(cserv) * parseInt(installment);
            $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
            $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
            var price = 0;
            $('#new_fee_option_serv .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });

            var discount_sem = $('.discount_sem').val();
            var discount_amount = $('.discount_amount').val();
            var cservd = 0.00;
            if(discount_sem != ''){
                cservd = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cservd);
            var duductdis = price - dis;

            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
        });

        $(document).delegate('#new_fee_option_serv .discount_amount','keyup', function(){
            var discount_amount = $(this).val();
            var discount_sem = $('#new_fee_option_serv .discount_sem').val();
            var cserv = 0.00;
            if(discount_sem != ''){
                cserv = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cserv);
            $('#new_fee_option_serv .totaldis span').html(dis.toFixed(2));
            var price = 0;
            $('#new_fee_option_serv .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });
            var duductdis = price - dis;
            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
            $('#new_fee_option_serv .totaldis .total_dis_am').val(dis.toFixed(2));

        });

        $(document).delegate('#new_fee_option_serv .discount_sem','keyup', function(){
            var discount_sem = $(this).val();
            var discount_amount = $('#new_fee_option_serv .discount_amount').val();
            var cserv = 0.00;
            if(discount_sem != ''){
                cserv = discount_sem;
            }

            var cservs = 0.00;
            if(discount_amount != ''){
                cservs = discount_amount;
            }
            var dis = parseFloat(cservs) * parseFloat(cserv);
            $('#new_fee_option_serv .totaldis span').html(dis.toFixed(2));
            $('#new_fee_option_serv .totaldis .total_dis_am').val(dis.toFixed(2));

            var price = 0;
            $('#new_fee_option_serv .total_fee_am').each(function(){
                price += parseFloat($(this).val());
            });
            var duductdis = price - dis;
            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));

        });
    });

    function arcivedAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;
			} else {
				$('#popuploader').show();
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:'{{URL::to('/')}}/admin/delete_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						$('#popuploader').hide();
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							location.reload();

						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#popuploader").hide();
					},
					beforeSend: function() {
						$("#popuploader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}

	// Test function to debug rename checklist functionality
	function testRenameChecklist() {
		console.log('=== Testing Rename Checklist Functionality ===');
		
		// Check for renamechecklist elements
		var renameElements = $('.renamechecklist');
		console.log('Rename checklist elements found:', renameElements.length);
		
		// Check for personalchecklist-row elements
		var personalRows = $('.personalchecklist-row');
		console.log('Personal checklist row elements found:', personalRows.length);
		
		// Check for drow elements
		var drowElements = $('.drow');
		console.log('Drow elements found:', drowElements.length);
		
		// Test event binding
		renameElements.each(function(index) {
			console.log('Rename element ' + index + ':', $(this));
			console.log('Has click events:', $._data(this, 'events'));
		});
		
		// Test parent-child relationships
		renameElements.each(function(index) {
			var $this = $(this);
			var $drow = $this.closest('.drow');
			var $parent = $drow.find('.personalchecklist-row');
			console.log('Element ' + index + ' - Drow found:', $drow.length > 0);
			console.log('Element ' + index + ' - Parent found:', $parent.length > 0);
			if ($parent.length > 0) {
				console.log('Element ' + index + ' - Parent data:', $parent.data());
			}
		});
	}

	// Test function to debug rename document functionality
	function testRenameDocument() {
		console.log('=== Testing Rename Document Functionality ===');
		
		// Check for renamedoc elements
		var renameDocElements = $('.renamedoc');
		console.log('Rename document elements found:', renameDocElements.length);
		
		// Check for doc-row elements
		var docRows = $('.doc-row');
		console.log('Document row elements found:', docRows.length);
		
		// Test parent-child relationships
		renameDocElements.each(function(index) {
			var $this = $(this);
			var $drow = $this.closest('.drow');
			var $parent = $drow.find('.doc-row');
			console.log('Rename doc element ' + index + ' - Drow found:', $drow.length > 0);
			console.log('Rename doc element ' + index + ' - Parent found:', $parent.length > 0);
			if ($parent.length > 0) {
				console.log('Rename doc element ' + index + ' - Parent data:', $parent.data());
			}
		});
	}

	// Test function to debug download file functionality
	function testDownloadFile() {
		console.log('=== Testing Download File Functionality ===');
		
		// Check for download-file elements
		var downloadElements = document.querySelectorAll('.download-file');
		console.log('Download file elements found:', downloadElements.length);
		
		// Test data attributes
		downloadElements.forEach(function(element, index) {
			console.log('Download element ' + index + ':', element);
			console.log('Download element ' + index + ' dataset:', element.dataset);
			console.log('Download element ' + index + ' filelink:', element.dataset.filelink);
			console.log('Download element ' + index + ' filename:', element.dataset.filename);
		});
		
		// Test jQuery data attributes
		$('.download-file').each(function(index) {
			console.log('jQuery Download element ' + index + ':', $(this));
			console.log('jQuery Download element ' + index + ' data:', $(this).data());
			console.log('jQuery Download element ' + index + ' filelink:', $(this).data('filelink'));
			console.log('jQuery Download element ' + index + ' filename:', $(this).data('filename'));
		});
		
		// Test event binding
		$('.download-file').each(function(index) {
			console.log('Download element ' + index + ' has click events:', $._data(this, 'events'));
		});
		

	}

	// Test function to debug visa checklist functionality
	function testVisaChecklist() {
		console.log('=== Testing Visa Checklist Functionality ===');
		
		// Check for visa checklist elements
		var visaChecklistElements = $('.migdocumnetlist1 .renamechecklist');
		console.log('Visa checklist rename elements found:', visaChecklistElements.length);
		
		// Check for visachecklist-row elements
		var visaChecklistRows = $('.visachecklist-row');
		console.log('Visa checklist row elements found:', visaChecklistRows.length);
		
		// Test parent-child relationships
		visaChecklistElements.each(function(index) {
			var $this = $(this);
			var $drow = $this.closest('.drow');
			var $parent = $drow.find('.visachecklist-row');
			console.log('Visa checklist element ' + index + ' - Drow found:', $drow.length > 0);
			console.log('Visa checklist element ' + index + ' - Parent found:', $parent.length > 0);
			if ($parent.length > 0) {
				console.log('Visa checklist element ' + index + ' - Parent data:', $parent.data());
			}
		});
		
		// Test event binding
		visaChecklistElements.each(function(index) {
			console.log('Visa checklist element ' + index + ' has click events:', $._data(this, 'events'));
		});
		

	}

	// Run test on page load
	$(document).ready(function() {
		// Initialize drag and drop upload functionality for Personal Documents
		initializeDragAndDropUpload();
		
		setTimeout(function() {
			testRenameChecklist();
			testRenameDocument();
			testDownloadFile();
			testVisaChecklist();
		}, 2000);
		
		// Ensure renamechecklist elements are clickable
		$('.renamechecklist').css({
			'pointer-events': 'auto',
			'cursor': 'pointer',
			'z-index': '1000'
		});
		
		// Add hover effect to make it clear they're clickable
		$('.renamechecklist').hover(
			function() {
				$(this).css('background-color', '#f8f9fa');
			},
			function() {
				$(this).css('background-color', '');
			}
		);
		
		// Direct event binding for renamechecklist elements
		$('.renamechecklist').off('click').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			console.log('Direct click handler triggered');
			
			var $this = $(this);
			var $drow = $this.closest('.drow');
			var $parent = $drow.find('.personalchecklist-row');
			
			if ($parent.length === 0) {
				console.error('Personal checklist row not found in direct handler');
				return false;
			}
			
			var opentime = $parent.data('personalchecklistname');
			if (!opentime) {
				console.error('Personal checklist name not found in direct handler');
				return false;
			}
			
			$parent.data('current-html', $parent.html());
			$parent.empty().append(
				$('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
				$('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
				$('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
			);
			
			console.log('Direct rename interface created successfully');
			return false;
		});

		// Direct event binding for renamedoc elements
		$('.renamedoc').off('click').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			console.log('Direct renamedoc click handler triggered');
			
			var $this = $(this);
			var $drow = $this.closest('.drow');
			var $parent = $drow.find('.doc-row');
			
			if ($parent.length === 0) {
				console.error('Document row not found in direct handler');
				return false;
			}
			
			var opentime = $parent.data('name');
			if (!opentime) {
				console.error('Document name not found in direct handler');
				return false;
			}
			
			$parent.data('current-html', $parent.html());
			$parent.empty().append(
				$('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
				$('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
				$('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
			);
			
			console.log('Direct renamedoc interface created successfully');
			return false;
		});

		// Direct event binding for visa checklist rename elements
		$('.migdocumnetlist1 .renamechecklist').off('click').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			console.log('Direct visa checklist rename click handler triggered');
			
			var $this = $(this);
			var $drow = $this.closest('.drow');
			var $parent = $drow.find('.visachecklist-row');
			
			if ($parent.length === 0) {
				console.error('Visa checklist row not found in direct handler');
				return false;
			}
			
			var opentime = $parent.data('visachecklistname');
			if (!opentime) {
				console.error('Visa checklist name not found in direct handler');
				return false;
			}
			
			$parent.data('current-html', $parent.html());
			$parent.empty().append(
				$('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
				$('<button class="btn btn-visaprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
				$('<button class="btn btn-visadanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
			);
			
			console.log('Direct visa checklist rename interface created successfully');
			return false;
		});

		// Ensure download-file elements are clickable
		$('.download-file').css({
			'pointer-events': 'auto',
			'cursor': 'pointer',
			'z-index': '1000'
		});
		
		// Add hover effect to download-file elements
		$('.download-file').hover(
			function() {
				$(this).css('background-color', '#f8f9fa');
			},
			function() {
				$(this).css('background-color', '');
			}
		);

		// Direct event binding for download-file elements
		$('.download-file').off('click').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			console.log('Direct download-file click handler triggered');
			
			const $this = $(this);
			const filelink = $this.data('filelink');
			const filename = $this.data('filename');
			
			console.log('Direct handler - File link:', filelink);
			console.log('Direct handler - File name:', filename);
			
			if (!filelink || !filename) {
				console.error('Direct handler - Missing file info');
				alert('Missing file info. Please try again.');
				return false;
			}
			
			// Show loading indicator
			$this.html('<i class="fas fa-spinner fa-spin"></i> Downloading...');
			$this.prop('disabled', true);
			
			// Create and submit a hidden form
			const form = $('<form>', {
				method: 'POST',
				action: '{{ url("/admin/download-document") }}',
				target: '_blank',
				style: 'display: none;'
			});
			
			// CSRF token
			const token = $('meta[name="csrf-token"]').attr('content');
			if (!token) {
				console.error('Direct handler - CSRF token not found');
				alert('Security token not found. Please refresh the page and try again.');
				$this.html('Download').prop('disabled', false);
				return false;
			}
			
			// Add form fields
			form.append($('<input>', {
				type: 'hidden',
				name: '_token',
				value: token
			}));
			
			form.append($('<input>', {
				type: 'hidden',
				name: 'filelink',
				value: filelink
			}));
			
			form.append($('<input>', {
				type: 'hidden',
				name: 'filename',
				value: filename
			}));
			
			console.log('Direct handler - Submitting download form for:', filename);
			
			// Append form to body and submit
			$('body').append(form);
			
			try {
				form[0].submit();
				console.log('Direct handler - Download form submitted successfully');
				
				// Reset button after a short delay
				setTimeout(function() {
					$this.html('Download').prop('disabled', false);
				}, 2000);
				
			} catch (error) {
				console.error('Direct handler - Error submitting form:', error);
				alert('Error initiating download. Please try again.');
				$this.html('Download').prop('disabled', false);
			}
			
			// Remove form after submission
			setTimeout(function() {
				form.remove();
			}, 1000);
			
			return false;
		});
		
		// Function to open signature link in new tab
		function openSignatureLink(url) {
			try {
				// Method 1: Try window.open
				const newWindow = window.open(url, '_blank');
				if (newWindow) {
					newWindow.focus();
					return true;
				}
				
				// Method 2: If window.open fails, try location.href
				window.location.href = url;
				return true;
				
			} catch (error) {
				console.error('Error opening signature link:', error);
				alert('Unable to open signature link. Please try again or copy the URL manually.');
				return false;
			}
		}
	});

</script>
@endsection
