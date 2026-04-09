{{-- Single global full-page loader for CRM layouts. Class "popuploader" kept for existing JS ($('.popuploader')). --}}
<style>
    /* Specificity beats task-popover-modern.css .popuploader { position:absolute; transform:... } */
    .popuploader.crm-global-popuploader {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        transform: none !important;
        margin: 0 !important;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99999;
    }
    .popuploader.crm-global-popuploader .popuploader__panel {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    .popuploader.crm-global-popuploader .popuploader__panel i.fa-spinner {
        font-size: 32px;
        color: #667eea;
        display: block;
        margin-bottom: 15px;
    }
    .popuploader.crm-global-popuploader .popuploader__panel p {
        margin: 0;
        font-weight: 500;
        color: #2d3748;
    }
</style>
<div class="popuploader crm-global-popuploader" style="display: none;" aria-hidden="true" id="crmGlobalPopuploader">
    <div class="popuploader__panel">
        <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
        <p>Processing...</p>
    </div>
</div>
