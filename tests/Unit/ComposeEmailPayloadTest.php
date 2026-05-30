<?php

namespace Tests\Unit;

use App\Support\ComposeEmailPayload;
use PHPUnit\Framework\TestCase;

class ComposeEmailPayloadTest extends TestCase
{
    public function test_decode_subject_restores_amp_placeholder(): void
    {
        $this->assertSame(
            'A & B',
            ComposeEmailPayload::decodeSubject(['subject' => 'A __AMP__ B'])
        );
    }

    public function test_decode_message_returns_plain_when_not_base64(): void
    {
        $html = '<p>Hello <a href="https://example.com">link</a></p>';

        $this->assertSame(
            $html,
            ComposeEmailPayload::decodeMessage(['message' => $html])
        );
    }

    public function test_decode_message_decodes_base64_utf8_body(): void
    {
        $html = '<p>Dear Vipul,</p><p>Visit https://afpnationalpolicechecks.converga.com.au</p>';
        $encoded = base64_encode($html);

        $this->assertSame(
            $html,
            ComposeEmailPayload::decodeMessage([
                'message' => $encoded,
                'message_encoding' => 'base64',
            ])
        );
    }

    public function test_decode_message_falls_back_on_invalid_base64(): void
    {
        $this->assertSame(
            'not-valid-base64!!!',
            ComposeEmailPayload::decodeMessage([
                'message' => 'not-valid-base64!!!',
                'message_encoding' => 'base64',
            ])
        );
    }
}
