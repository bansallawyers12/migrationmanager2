<?php

namespace Tests\Unit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountTabDibpReceiptsToggleTest extends TestCase
{
    #[Test]
    public function account_tab_adds_receipts_toggle_without_changing_create_entry_buttons(): void
    {
        $blade = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/account.blade.php'));
        Assert::assertNotFalse($blade);

        Assert::assertStringContainsString('data-receipt-type="1"', $blade);
        Assert::assertStringContainsString('data-receipt-type="2"', $blade);
        Assert::assertStringContainsString('data-receipt-type="3"', $blade);
        Assert::assertStringContainsString('class="account-layout"', $blade);

        Assert::assertStringContainsString('id="dibp-receipts-toggle"', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-panel"', $blade);
        Assert::assertMatchesRegularExpression('/class="[^"]*\bdibp-receipts-toggle\b[^"]*"/', $blade);
        Assert::assertDoesNotMatchRegularExpression(
            '/id="dibp-receipts-toggle"[^>]*createreceipt/',
            $blade
        );

        $invoicePos = strpos($blade, 'data-receipt-type="3"');
        $togglePos = strpos($blade, 'id="dibp-receipts-toggle"');
        $layoutPos = strpos($blade, 'class="account-layout"');
        $panelPos = strpos($blade, 'id="dibp-receipts-panel"');
        Assert::assertNotFalse($invoicePos);
        Assert::assertNotFalse($togglePos);
        Assert::assertNotFalse($layoutPos);
        Assert::assertNotFalse($panelPos);
        Assert::assertGreaterThan($invoicePos, $togglePos);
        Assert::assertGreaterThan($layoutPos, $panelPos);

        Assert::assertMatchesRegularExpression(
            '/id="dibp-receipts-panel"[^>]*\bhidden\b/',
            $blade
        );
        Assert::assertStringNotContainsString('class="account-layout" hidden', $blade);
        Assert::assertStringContainsString('dibp-receipts-add-checklist', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-checklist-name"', $blade);
        Assert::assertStringContainsString('$dibp_receipts_checklists', $blade);
        Assert::assertStringContainsString('<select class="form-control" name="checklist" id="dibp-receipts-checklist-name" required>', $blade);
        Assert::assertStringNotContainsString('id="dibp-receipts-checklist-name" maxlength="255"', $blade);
        Assert::assertStringNotContainsString('create_migration_docs', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-file-context-menu"', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-checklist-context-menu"', $blade);
        Assert::assertStringContainsString('Rename File Name', $blade);
        Assert::assertStringContainsString('Rename Checklist', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-send-hubdoc"', $blade);
        Assert::assertStringContainsString('Send to Hubdoc', $blade);
        Assert::assertStringContainsString('data-action="send-hubdoc"', $blade);
        Assert::assertStringContainsString('class="dropdown-item send-to-hubdoc-btn"', $blade);
    }

    #[Test]
    public function account_tab_receipts_panel_is_a_visa_like_table_shell_with_unique_ids(): void
    {
        $blade = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/account.blade.php'));
        Assert::assertNotFalse($blade);

        $panelStart = strpos($blade, 'id="dibp-receipts-panel"');
        $scriptStart = strpos($blade, '<!-- Account Tab JavaScript -->');
        Assert::assertNotFalse($panelStart);
        Assert::assertNotFalse($scriptStart);
        Assert::assertGreaterThan($panelStart, $scriptStart);
        $panel = substr($blade, $panelStart, $scriptStart - $panelStart);

        Assert::assertStringContainsString('id="dibp-receipts-table"', $panel);
        Assert::assertStringContainsString('id="dibp-receipts-list"', $panel);
        Assert::assertStringContainsString('<th>Checklist</th>', $panel);
        Assert::assertStringContainsString('<th>File Name</th>', $panel);
        Assert::assertStringContainsString('dibp-receipts-empty-row', $panel);
        Assert::assertStringContainsString('No receipts yet.', $panel);
        Assert::assertStringContainsString('$dibp_receipts_lists', $panel);
        Assert::assertStringContainsString('$dibp_receipts_checklists', $panel);
        Assert::assertStringContainsString('dibp-receipts-row-', $panel);
        Assert::assertStringNotContainsString('\\App\\Models\\Document::', $panel);

        Assert::assertStringNotContainsString('subtab6', $panel);
        Assert::assertStringNotContainsString('form956CreateForm', $panel);
        Assert::assertStringNotContainsString('add-visa-doc-category', $panel);
        Assert::assertStringContainsString('dibp-receipts-add-checklist', $panel);
        Assert::assertStringContainsString('id="dibp-receipts-bulk-toggle"', $panel);
        Assert::assertStringContainsString('id="dibp-receipts-bulk-dropzone"', $panel);
        Assert::assertStringContainsString('id="dibp-receipts-bulk-mapping-modal"', $panel);
        Assert::assertStringContainsString('id="dibp-receipts-bulk-confirm"', $panel);
        Assert::assertStringContainsString('data-bulk-url', $panel);
        Assert::assertStringContainsString('Drag and drop files here', $panel);
        Assert::assertStringNotContainsString('bulk-upload-toggle-btn-visa', $panel);
        Assert::assertStringNotContainsString('bulk-upload-dropzone-visa', $panel);
        Assert::assertStringNotContainsString('id="bulk-upload-mapping-modal"', $panel);
        Assert::assertStringNotContainsString('id="confirm-bulk-upload"', $panel);
        Assert::assertStringNotContainsString('id="auto-create-unmatched"', $panel);
        Assert::assertStringNotContainsString('add_migration_doc', $panel);
        Assert::assertStringNotContainsString('add-checklist-btn', $panel);
        Assert::assertStringNotContainsString('visachecklist-row', $panel);
        Assert::assertStringNotContainsString('migdocumnetlist', $panel);
        Assert::assertStringContainsString('dibp-receipts-upload', $panel);
        Assert::assertStringContainsString('dibp-receipts-drag-zone', $panel);
        Assert::assertStringContainsString('Drag file here or <strong>click to browse</strong>', $panel);
        Assert::assertStringContainsString('data-upload-url', $panel);
        Assert::assertStringNotContainsString('migdocupload', $panel);
        Assert::assertStringNotContainsString('visa-doc-drag-zone', $panel);
        Assert::assertStringNotContainsString('personal-doc-drag-zone', $panel);
        Assert::assertStringNotContainsString('mig_upload_form_', $panel);
        Assert::assertStringContainsString('id="dibp-receipts-preview"', $panel);
        Assert::assertStringContainsString('dibp-receipts-checklist', $panel);
        Assert::assertStringContainsString('dibp-receipts-file', $panel);
        Assert::assertStringContainsString('dibp-receipts-preview-link', $panel);
        Assert::assertStringContainsString('data-rename-url', $panel);
        Assert::assertStringContainsString('data-download-url', $panel);
        Assert::assertStringContainsString('data-hubdoc-url', $panel);
        Assert::assertStringContainsString('id="dibp-receipts-send-hubdoc"', $panel);
        Assert::assertStringNotContainsString('send-to-hubdoc-btn', $panel);
        Assert::assertStringNotContainsString('clients/sendToHubdoc', $panel);
        Assert::assertStringNotContainsString('preview-container-migdocumnetlist', $panel);
        Assert::assertStringNotContainsString('visaFileContextMenu', $panel);
        Assert::assertStringNotContainsString('showVisaFileContextMenu', $panel);
        Assert::assertStringNotContainsString('id="fileContextMenu"', $panel);
        Assert::assertStringNotContainsString('class="renamedoc"', $panel);
        Assert::assertStringNotContainsString('class="download-file"', $panel);
        Assert::assertStringNotContainsString('class="doc-row"', $panel);
    }

    #[Test]
    public function visa_documents_tab_markup_is_unchanged_by_receipts_shell(): void
    {
        $visa = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/visa_documents.blade.php'));
        Assert::assertNotFalse($visa);

        Assert::assertStringContainsString('id="visadocuments-tab"', $visa);
        Assert::assertStringContainsString('subtab6-button', $visa);
        Assert::assertStringContainsString('form956CreateForm', $visa);
        Assert::assertStringContainsString('add-visa-doc-category', $visa);
        Assert::assertStringContainsString('add_migration_doc', $visa);
        Assert::assertStringContainsString('visa-doc-drag-zone', $visa);
        Assert::assertStringContainsString('bulk-upload-toggle-btn-visa', $visa);
        Assert::assertStringContainsString('id="bulk-upload-mapping-modal"', $visa);
        Assert::assertStringContainsString('id="confirm-bulk-upload"', $visa);
        Assert::assertStringContainsString('migdocumnetlist1', $visa);
        Assert::assertStringContainsString('id="visaFileContextMenu"', $visa);
        Assert::assertStringContainsString('showVisaFileContextMenu', $visa);
        Assert::assertStringContainsString('class="renamedoc"', $visa);
        Assert::assertStringContainsString('class="download-file"', $visa);
        Assert::assertStringNotContainsString('dibp-receipts-send-hubdoc', $visa);
        Assert::assertStringContainsString('preview-container-migdocumnetlist', $visa);
        Assert::assertStringNotContainsString('dibp-receipts-', $visa);
    }

    #[Test]
    public function account_tab_script_toggles_receipts_panel_with_hidden_attribute(): void
    {
        $js = file_get_contents($this->projectPath('public/js/crm/clients/account-tab.js'));
        Assert::assertNotFalse($js);

        Assert::assertStringContainsString('#account-tab .dibp-receipts-toggle', $js);
        Assert::assertStringContainsString('.account-layout', $js);
        Assert::assertStringContainsString('#dibp-receipts-panel', $js);
        Assert::assertStringContainsString("setAttribute('hidden', '')", $js);
        Assert::assertStringContainsString("removeAttribute('hidden')", $js);
        Assert::assertStringContainsString('#account-tab .dibp-receipts-add-checklist', $js);
        Assert::assertStringContainsString('dibp-receipts-add-checklist-form', $js);
        Assert::assertStringContainsString('dibp-receipts-upload', $js);
        Assert::assertStringContainsString('dibp-receipts-drag-zone', $js);
        Assert::assertStringContainsString('dibp-receipts-bulk-dropzone', $js);
        Assert::assertStringContainsString('dibp-receipts-bulk-confirm', $js);
        Assert::assertStringContainsString('dibpReceiptsChecklistExistsOnTable', $js);
        Assert::assertStringContainsString('data-bulk-url', $js);
        Assert::assertStringNotContainsString('bulk-upload-dropzone-visa', $js);
        Assert::assertStringNotContainsString('id="confirm-bulk-upload"', $js);
        Assert::assertStringNotContainsString('bulkUploadVisaDocuments', $js);
        Assert::assertStringContainsString('Drag file here or <strong>click to browse</strong>', $js);
        Assert::assertStringContainsString('data-upload-url', $js);
        Assert::assertStringContainsString('dibp-receipts-file-context-menu', $js);
        Assert::assertStringContainsString('dibp-receipts-preview', $js);
        Assert::assertStringContainsString('dibpReceiptsPreviewFile', $js);
        Assert::assertStringContainsString('data-rename-url', $js);
        Assert::assertStringContainsString('data-download-url', $js);
        Assert::assertStringContainsString('data-hubdoc-url', $js);
        Assert::assertStringContainsString('dibpReceiptsSendHubdoc', $js);
        Assert::assertStringContainsString('dibp-receipts-send-hubdoc', $js);
        Assert::assertStringContainsString('dibp-receipts-hubdoc-sending', $js);
        Assert::assertStringContainsString('Sending...', $js);
        Assert::assertStringNotContainsString('showSendInProgress', $js);
        Assert::assertStringNotContainsString('sendToHubdocAjax', $js);
        Assert::assertStringNotContainsString('send-to-hubdoc-btn', $js);
        Assert::assertStringNotContainsString('/clients/sendToHubdoc', $js);
        Assert::assertStringNotContainsString('HubdocInvoiceMail', $js);
        Assert::assertStringNotContainsString('migdocupload', $js);
        Assert::assertStringNotContainsString('visa-doc-drag-zone', $js);
        Assert::assertStringNotContainsString('personal-doc-drag-zone', $js);
        Assert::assertStringNotContainsString('upload-visa-document', $js);
        Assert::assertStringNotContainsString('upload-edu-document', $js);
        Assert::assertStringNotContainsString('visadocuments-tab', $js);
        Assert::assertStringNotContainsString('subtab6', $js);
        Assert::assertStringNotContainsString('showVisaFileContextMenu', $js);
        Assert::assertStringNotContainsString('visaFileContextMenu', $js);
        Assert::assertStringNotContainsString('preview-container-migdocumnetlist', $js);

        $detailMain = file_get_contents($this->projectPath('public/js/crm/clients/detail-main.js'));
        Assert::assertNotFalse($detailMain);
        Assert::assertStringContainsString('/documents/upload-visa-document', $detailMain);
        Assert::assertStringContainsString('/documents/upload-edu-document', $detailMain);
        Assert::assertStringContainsString('.migdocupload', $detailMain);
        Assert::assertStringNotContainsString('dibp-receipts-upload', $detailMain);
        Assert::assertStringNotContainsString('upload-dibp-receipt-document', $detailMain);
        Assert::assertStringNotContainsString('dibp-receipts-file-context-menu', $detailMain);

        $documentsJs = file_get_contents($this->projectPath('public/js/crm/clients/modules/documents.js'));
        Assert::assertNotFalse($documentsJs);
        Assert::assertStringContainsString('.migdocumnetlist1 .renamedoc', $documentsJs);
        Assert::assertStringContainsString('.persdocumnetlist .renamedoc', $documentsJs);
        Assert::assertStringContainsString('.download-file', $documentsJs);
        Assert::assertStringNotContainsString('dibp-receipts-', $documentsJs);
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
