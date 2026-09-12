<?php

namespace Tests\Unit;

use App\Http\Requests\StoreDibpReceiptBulkUploadRequest;
use App\Http\Requests\StoreDibpReceiptChecklistRequest;
use App\Http\Requests\StoreDibpReceiptUploadRequest;
use App\Models\DocumentChecklist;
use App\Support\ClientDetailDocumentsTab;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DibpReceiptIsolationTest extends TestCase
{
    #[Test]
    public function receipts_toggle_keeps_create_entry_and_does_not_jquery_hide_the_ledger(): void
    {
        $blade = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/account.blade.php'));
        Assert::assertNotFalse($blade);

        Assert::assertStringContainsString('class="btn btn-success createreceipt"', $blade);
        Assert::assertStringContainsString('class="btn btn-primary createreceipt"', $blade);
        Assert::assertStringContainsString('class="btn btn-info createreceipt"', $blade);
        Assert::assertStringContainsString('data-receipt-type="1"', $blade);
        Assert::assertStringContainsString('data-receipt-type="2"', $blade);
        Assert::assertStringContainsString('data-receipt-type="3"', $blade);
        Assert::assertStringContainsString('$receipts_lists', $blade);
        Assert::assertStringContainsString('$receipts_lists_office', $blade);
        Assert::assertStringContainsString('$receipts_lists_invoice', $blade);
        Assert::assertStringContainsString('#client_receipt_form', $blade);
        Assert::assertStringContainsString('#office_receipt_form', $blade);
        Assert::assertStringContainsString('#invoice_receipt_form', $blade);
        Assert::assertStringContainsString("'.createreceipt[data-account-entry=\"true\"]'", $blade);

        Assert::assertStringContainsString('id="dibp-receipts-toggle"', $blade);
        Assert::assertStringContainsString('class="btn btn-warning dibp-receipts-toggle"', $blade);
        Assert::assertStringNotContainsString('class="btn btn-outline-secondary dibp-receipts-toggle"', $blade);
        Assert::assertDoesNotMatchRegularExpression(
            '/id="dibp-receipts-toggle"[^>]*createreceipt/',
            $blade
        );
        Assert::assertStringNotContainsString('class="account-layout" hidden', $blade);

        $js = file_get_contents($this->projectPath('public/js/crm/clients/dibp-receipts-tab.js'));
        Assert::assertNotFalse($js);
        Assert::assertStringContainsString('#account-tab .dibp-receipts-toggle', $js);
        Assert::assertStringContainsString("layout.setAttribute('hidden', '')", $js);
        Assert::assertStringContainsString("layout.removeAttribute('hidden')", $js);
        Assert::assertStringNotContainsString(".account-layout').hide(", $js);
        Assert::assertStringNotContainsString('.account-layout").hide(', $js);
        Assert::assertStringNotContainsString('window.listOfInvoice', $js);
        Assert::assertStringNotContainsString('window.clientLedgerBalanceAmount', $js);
        Assert::assertStringNotContainsString('ensureAccountTabLoaded', $js);

        $accountTabJs = file_get_contents($this->projectPath('public/js/crm/clients/account-tab.js'));
        Assert::assertNotFalse($accountTabJs);
        Assert::assertStringContainsString('window.listOfInvoice', $accountTabJs);
        Assert::assertStringContainsString('window.clientLedgerBalanceAmount', $accountTabJs);
        Assert::assertStringNotContainsString('dibp-receipts-toggle', $accountTabJs);
    }

    #[Test]
    public function visa_personal_and_not_used_tabs_do_not_include_dibp_receipts_ui(): void
    {
        $tabs = [
            'resources/views/crm/clients/tabs/visa_documents.blade.php' => [
                'id="visadocuments-tab"',
                'add_migration_doc',
                'migdocumnetlist1',
                'migdocupload',
                'visa-doc-drag-zone',
                'class="renamedoc"',
            ],
            'resources/views/crm/clients/tabs/personal_documents.blade.php' => [
                'persdocumnetlist',
                'add_personal_doc',
                'personal-doc-drag-zone',
                'class="renamedoc"',
                'showFileContextMenu',
            ],
            'resources/views/crm/clients/tabs/not_used_documents.blade.php' => [
                'notuseddocumnetlist',
                'backtodoc',
            ],
        ];

        foreach ($tabs as $relative => $mustContain) {
            $contents = file_get_contents($this->projectPath($relative));
            Assert::assertNotFalse($contents, $relative);
            foreach ($mustContain as $needle) {
                Assert::assertStringContainsString($needle, $contents, $relative.' missing '.$needle);
            }
            Assert::assertStringNotContainsString('dibp-receipts-', $contents, $relative);
            Assert::assertStringNotContainsString('upload-dibp-receipt-document', $contents, $relative);
            Assert::assertStringNotContainsString('doc_type = dibp_receipt', $contents, $relative);
        }
    }

    #[Test]
    public function dibp_upload_and_checklist_requests_do_not_accept_visa_or_personal_doc_type(): void
    {
        $upload = (new StoreDibpReceiptUploadRequest)->rules();
        Assert::assertArrayHasKey('clientid', $upload);
        Assert::assertArrayHasKey('fileid', $upload);
        Assert::assertArrayHasKey('document_upload', $upload);
        Assert::assertArrayNotHasKey('doctype', $upload);
        Assert::assertArrayNotHasKey('doc_type', $upload);
        Assert::assertArrayNotHasKey('folder_name', $upload);

        $bulk = (new StoreDibpReceiptBulkUploadRequest)->rules();
        Assert::assertArrayHasKey('files', $bulk);
        Assert::assertArrayHasKey('mappings', $bulk);
        Assert::assertArrayNotHasKey('doctype', $bulk);
        Assert::assertArrayNotHasKey('categoryid', $bulk);

        $checklist = (new StoreDibpReceiptChecklistRequest)->rules();
        Assert::assertArrayHasKey('clientid', $checklist);
        Assert::assertArrayHasKey('checklist', $checklist);
        Assert::assertArrayNotHasKey('doctype', $checklist);
        Assert::assertArrayNotHasKey('doc_type', $checklist);
        Assert::assertArrayNotHasKey('folder_name', $checklist);
        Assert::assertSame(4, DocumentChecklist::DOC_TYPE_DIBP_RECEIPT);

        Assert::assertSame('dibp_receipt', ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE);
        Assert::assertSame('dibp_receipt', ClientDetailDocumentsTab::storageFolder());
        Assert::assertSame('general', ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME);
        Assert::assertNotSame('visa', ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE);
        Assert::assertNotSame('personal', ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE);
    }

    #[Test]
    public function visa_and_personal_document_scripts_still_upload_to_their_own_endpoints(): void
    {
        $detailMain = file_get_contents($this->projectPath('public/js/crm/clients/detail-main.js'));
        Assert::assertNotFalse($detailMain);
        Assert::assertStringContainsString('/documents/upload-visa-document', $detailMain);
        Assert::assertStringContainsString('/documents/upload-edu-document', $detailMain);
        Assert::assertStringContainsString('.migdocupload', $detailMain);
        Assert::assertStringNotContainsString('upload-dibp-receipt-document', $detailMain);
        Assert::assertStringNotContainsString('bulk-upload-dibp-receipt', $detailMain);
        Assert::assertStringNotContainsString('dibp-receipts-', $detailMain);

        $documentsJs = file_get_contents($this->projectPath('public/js/crm/clients/modules/documents.js'));
        Assert::assertNotFalse($documentsJs);
        Assert::assertStringContainsString('.migdocumnetlist1 .renamedoc', $documentsJs);
        Assert::assertStringContainsString('.persdocumnetlist .renamedoc', $documentsJs);
        Assert::assertStringNotContainsString('dibp-receipts-', $documentsJs);

        $accountJs = file_get_contents($this->projectPath('public/js/crm/clients/account-tab.js'));
        Assert::assertNotFalse($accountJs);
        Assert::assertStringNotContainsString('upload-visa-document', $accountJs);
        Assert::assertStringNotContainsString('upload-edu-document', $accountJs);
        Assert::assertStringNotContainsString('migdocupload', $accountJs);
        Assert::assertStringNotContainsString('visa-doc-drag-zone', $accountJs);
        Assert::assertStringNotContainsString('dibp-receipts-', $accountJs);

        $receiptsJs = file_get_contents($this->projectPath('public/js/crm/clients/dibp-receipts-tab.js'));
        Assert::assertNotFalse($receiptsJs);
        Assert::assertStringContainsString('dibp-receipts-drag-zone', $receiptsJs);
        Assert::assertStringContainsString('dibp-receipts-bulk-dropzone', $receiptsJs);
        Assert::assertSame(8, substr_count($receiptsJs, '}, true);'));
        Assert::assertSame(2, substr_count($receiptsJs, "addEventListener('drop'"));
        Assert::assertSame(2, substr_count($receiptsJs, "addEventListener('dragover'"));
        Assert::assertStringContainsString('.personal-doc-drag-zone, .visa-doc-drag-zone, .nomination-doc-drag-zone', $detailMain);
        Assert::assertStringNotContainsString('dibp-receipts-drag-zone', $detailMain);
        Assert::assertStringNotContainsString('dibp-receipts-bulk-dropzone', $detailMain);
        Assert::assertStringContainsString('dibpReceiptsSendHubdoc', $receiptsJs);
        Assert::assertStringContainsString('dibp-receipts-hubdoc-sending', $receiptsJs);
        Assert::assertStringNotContainsString('showSendInProgress', $receiptsJs);
        Assert::assertStringNotContainsString('sendToHubdocAjax', $receiptsJs);
        Assert::assertStringNotContainsString('send-to-hubdoc-btn', $receiptsJs);
        Assert::assertStringNotContainsString('/clients/sendToHubdoc', $receiptsJs);
        Assert::assertStringContainsString('dibp-receipts-bulk-dropzone', $receiptsJs);
        Assert::assertStringContainsString('dibp-receipts-bulk-confirm', $receiptsJs);
        Assert::assertStringNotContainsString('bulk-upload-dropzone-visa', $receiptsJs);
        Assert::assertStringNotContainsString('bulkUploadVisaDocuments', $receiptsJs);
        Assert::assertStringNotContainsString('bulk-upload-personal', $receiptsJs);
        Assert::assertStringContainsString('Drag file here or <strong>click to browse</strong>', $receiptsJs);
        Assert::assertStringNotContainsString('upload-visa-document', $receiptsJs);
        Assert::assertStringNotContainsString('ensureAccountTabLoaded', $receiptsJs);
    }

    #[Test]
    public function personal_visa_and_nomination_checklist_modals_do_not_load_dibp_receipt_type(): void
    {
        $blade = file_get_contents($this->projectPath('resources/views/crm/clients/modals/checklists.blade.php'));
        Assert::assertNotFalse($blade);
        Assert::assertStringContainsString("where('doc_type',1)", $blade);
        Assert::assertStringContainsString("where('doc_type',2)", $blade);
        Assert::assertStringContainsString("where('doc_type',3)", $blade);
        Assert::assertStringNotContainsString("where('doc_type',4)", $blade);
        Assert::assertStringNotContainsString('dibp-receipts-', $blade);

        $create = file_get_contents($this->projectPath('resources/views/AdminConsole/features/documentchecklist/create.blade.php'));
        Assert::assertNotFalse($create);
        Assert::assertStringContainsString('value="4"', $create);
        Assert::assertStringContainsString('DIBP Receipt', $create);
        Assert::assertStringContainsString('value="1"', $create);
        Assert::assertStringContainsString('value="2"', $create);
        Assert::assertStringContainsString('value="3"', $create);
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
