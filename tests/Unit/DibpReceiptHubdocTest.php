<?php

namespace Tests\Unit;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Requests\StoreDibpReceiptHubdocRequest;
use App\Mail\HubdocDibpReceiptMail;
use App\Mail\HubdocInvoiceMail;
use App\Models\Document;
use App\Models\Staff;
use App\Services\SystemEmailLogService;
use App\Support\ClientDetailDocumentsTab;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DibpReceiptHubdocTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();
    }

    #[Test]
    public function hubdoc_request_does_not_accept_invoice_or_visa_doc_type(): void
    {
        $rules = (new StoreDibpReceiptHubdocRequest)->rules();

        Assert::assertArrayHasKey('clientid', $rules);
        Assert::assertArrayHasKey('fileid', $rules);
        Assert::assertArrayNotHasKey('doctype', $rules);
        Assert::assertArrayNotHasKey('receipt_id', $rules);
        Assert::assertArrayNotHasKey('invoice_id', $rules);
    }

    #[Test]
    public function send_endpoint_emails_receipt_file_and_rejects_visa_rows(): void
    {
        Storage::fake('s3');
        Mail::fake();
        $this->seedClient(20);

        $this->mock(SystemEmailLogService::class, function ($mock) {
            $mock->shouldReceive('logAndSendMailable')
                ->once()
                ->withArgs(function (array $meta, $mailable, $to) {
                    return $mailable instanceof HubdocDibpReceiptMail
                        && ! ($mailable instanceof HubdocInvoiceMail)
                        && ($meta['category'] ?? null) === 'hubdoc'
                        && ($meta['subject'] ?? null) === 'Receipt for Hubdoc Processing'
                        && is_string($to);
                });
        });

        $staff = Staff::query()->create([
            'first_name' => 'Dibp',
            'last_name' => 'Hubdoc',
            'email' => 'dibp-hubdoc@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $dibp = ClientDetailDocumentsTab::addChecklist(20, (int) $staff->id, 'Lodgement', 4);
        Storage::disk('s3')->put('C20/dibp_receipt/Test_Lodgement.pdf', 'receipt-bytes');
        $dibp->file_name = 'Test_Lodgement';
        $dibp->filetype = 'pdf';
        $dibp->myfile_key = 'Test_Lodgement.pdf';
        $dibp->myfile = Storage::disk('s3')->url('C20/dibp_receipt/Test_Lodgement.pdf');
        $dibp->save();

        $visa = Document::query()->create([
            'client_id' => 20,
            'user_id' => $staff->id,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '1',
            'checklist' => 'Visa form',
            'file_name' => 'Visa_form',
            'filetype' => 'pdf',
            'myfile_key' => 'Visa_form.pdf',
        ]);

        $ok = $this->actingAs($staff, 'admin')
            ->postJson('/documents/send-dibp-receipt-to-hubdoc', [
                'clientid' => 20,
                'fileid' => $dibp->id,
                'receipt_id' => 999,
                'doctype' => 'visa',
            ]);

        $ok->assertOk();
        Assert::assertTrue($ok->json('status'));
        $dibp->refresh();
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $dibp->doc_type);
        Assert::assertTrue((bool) $dibp->hubdoc_sent);
        Assert::assertNotNull($dibp->hubdoc_sent_at);

        Mail::assertNotSent(HubdocInvoiceMail::class);

        $rejected = $this->actingAs($staff, 'admin')
            ->postJson('/documents/send-dibp-receipt-to-hubdoc', [
                'clientid' => 20,
                'fileid' => $visa->id,
            ]);

        $rejected->assertStatus(422);
        $visa->refresh();
        Assert::assertSame('visa', $visa->doc_type);
        Assert::assertFalse((bool) $visa->hubdoc_sent);
    }

    #[Test]
    public function invoice_hubdoc_route_stays_on_account_controller(): void
    {
        $router = app('router');
        $invoice = $router->getRoutes()->match(Request::create('/clients/sendToHubdoc/12', 'POST'));
        Assert::assertSame('clients.sendToHubdoc', $invoice->getName());
        Assert::assertSame('sendToHubdoc', $invoice->getActionMethod());

        $dibp = $router->getRoutes()->match(Request::create('/documents/send-dibp-receipt-to-hubdoc', 'POST'));
        Assert::assertSame('clients.documents.sendDibpReceiptToHubdoc', $dibp->getName());
        Assert::assertSame('sendDibpReceiptToHubdoc', $dibp->getActionMethod());

        $sendToClient = file_get_contents($this->projectPath('public/js/crm/clients/modules/send-to-client.js'));
        Assert::assertNotFalse($sendToClient);
        Assert::assertStringContainsString('.send-to-hubdoc-btn', $sendToClient);
        Assert::assertStringContainsString('sendToHubdocAjax', $sendToClient);
        Assert::assertStringContainsString('showSendInProgress', $sendToClient);
        Assert::assertStringContainsString('Sending to Hubdoc...', $sendToClient);
        Assert::assertStringNotContainsString('dibp-receipts-send-hubdoc', $sendToClient);
        Assert::assertStringNotContainsString('send-dibp-receipt-to-hubdoc', $sendToClient);
    }

    private function seedClient(int $id): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('client_id')->nullable();
                $table->string('first_name')->nullable();
                $table->unsignedTinyInteger('is_company')->nullable();
                $table->timestamps();
            });
        }

        DB::table('admins')->updateOrInsert(
            ['id' => $id],
            [
                'client_id' => 'C'.$id,
                'first_name' => 'Test',
                'is_company' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->unsignedInteger('role')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('type')->nullable();
                $table->string('folder_name')->nullable();
                $table->string('file_name')->nullable();
                $table->string('checklist')->nullable();
                $table->string('myfile')->nullable();
                $table->string('myfile_key')->nullable();
                $table->string('filetype')->nullable();
                $table->unsignedInteger('file_size')->nullable();
                $table->string('status')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
                $table->boolean('hubdoc_sent')->nullable();
                $table->timestamp('hubdoc_sent_at')->nullable();
                $table->timestamps();
            });
        }

        foreach (['myfile', 'myfile_key', 'filetype', 'folder_name', 'checklist'] as $column) {
            if (! Schema::hasColumn('documents', $column)) {
                Schema::table('documents', function (Blueprint $table) use ($column) {
                    $table->string($column)->nullable();
                });
            }
        }
        if (! Schema::hasColumn('documents', 'file_size')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedInteger('file_size')->nullable();
            });
        }
        if (! Schema::hasColumn('documents', 'hubdoc_sent')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->boolean('hubdoc_sent')->nullable();
            });
        }
        if (! Schema::hasColumn('documents', 'hubdoc_sent_at')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->timestamp('hubdoc_sent_at')->nullable();
            });
        }

        foreach (['password', 'role', 'status'] as $column) {
            if (Schema::hasTable('staff') && ! Schema::hasColumn('staff', $column)) {
                Schema::table('staff', function (Blueprint $table) use ($column) {
                    if ($column === 'password') {
                        $table->string($column)->nullable();
                    } else {
                        $table->unsignedInteger($column)->nullable();
                    }
                });
            }
        }

        if (! Schema::hasTable('signers')) {
            Schema::create('signers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->timestamps();
            });
        }
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
