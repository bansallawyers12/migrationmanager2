<?php

namespace Tests\Unit\Support;

use App\Support\ClientDetailVerificationFields;
use App\Support\ClientDetailVerificationUi;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientDetailVerificationUiTest extends TestCase
{
    #[Test]
    public function change_request_icon_uses_request_change_hover_title(): void
    {
        $html = ClientDetailVerificationUi::icon([
            'id' => 12,
            'field_key' => 'gender',
            'status' => ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED,
            'original_value' => 'Other',
            'requested_value' => 'Male',
        ]);

        $this->assertStringContainsString('title="Request Change"', $html);
        $this->assertStringContainsString('data-change-request="1"', $html);
        $this->assertStringNotContainsString('title="Change requested', $html);
    }

    #[Test]
    public function confirmed_icon_uses_confirmed_hover_title(): void
    {
        $html = ClientDetailVerificationUi::icon([
            'field_key' => 'marital_status',
            'status' => ClientDetailVerificationFields::STATUS_CONFIRMED,
        ]);

        $this->assertStringContainsString('title="Confirmed"', $html);
        $this->assertStringNotContainsString('data-change-request', $html);
    }

    #[Test]
    public function verify_link_script_confirms_then_sends_sms_without_touching_other_actions(): void
    {
        $js = file_get_contents(base_path('public/js/crm/clients/verify-link.js'));
        $this->assertNotFalse($js);
        $this->assertStringContainsString("on('click', '.send-verify-link'", $js);
        $this->assertStringContainsString('Send verification SMS?', $js);
        $this->assertStringContainsString("confirmButtonText: 'Yes'", $js);
        $this->assertStringContainsString('sendVerificationSms', $js);
        $this->assertStringNotContainsString('.send-sms-btn', $js);
        $this->assertStringNotContainsString('#create_appoint', $js);
        $this->assertStringNotContainsString('clients.verifyDetails', $js);
    }
}
