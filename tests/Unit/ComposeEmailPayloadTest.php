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

    public function test_normalize_signing_url_accepts_valid_sign_path(): void
    {
        $url = 'https://migrationmanager.example.com/sign/237011/abc123XYZ';

        $this->assertSame($url, ComposeEmailPayload::normalizeSigningUrl($url));
    }

    public function test_normalize_signing_url_accepts_token_with_underscore(): void
    {
        $url = 'https://migrationmanager.example.com/sign/193568/alp27l8edYFdBigKvamN68w59MXfl_XF5vw2qBApPmlsEhPk8NYil3Bg21NSzdSJ9';

        $this->assertSame($url, ComposeEmailPayload::normalizeSigningUrl($url));
    }

    public function test_normalize_signing_url_rejects_non_sign_urls(): void
    {
        $this->assertNull(ComposeEmailPayload::normalizeSigningUrl('https://example.com/other/page'));
        $this->assertNull(ComposeEmailPayload::normalizeSigningUrl(''));
    }

    public function test_apply_signing_link_replaces_macro_with_anchor(): void
    {
        $url = 'https://migrationmanager.example.com/sign/1/token123';
        $message = '<p>Agreement: {PDF_url_for_sign}</p>';

        $result = ComposeEmailPayload::applySigningLinkToMessage($message, $url);

        $this->assertStringContainsString('href="' . $url . '"', $result);
        $this->assertStringContainsString('Sign Service Agreement</a>', $result);
        $this->assertStringNotContainsString('{PDF_url_for_sign}', $result);
    }

    public function test_apply_signing_link_repairs_underline_only_label(): void
    {
        $url = 'https://migrationmanager.example.com/sign/99/abc123';
        $message = '<p>Please sign: <span style="text-decoration:underline;">Sign Service Agreement</span></p>';

        $result = ComposeEmailPayload::applySigningLinkToMessage($message, $url);

        $this->assertStringContainsString('href="' . $url . '"', $result);
        $this->assertStringNotContainsString('<span style="text-decoration:underline;">Sign Service Agreement</span>', $result);
    }

    public function test_apply_signing_link_skips_when_sign_href_already_present(): void
    {
        $url = 'https://migrationmanager.example.com/sign/2/token456';
        $message = '<a href="' . $url . '">Sign Service Agreement</a>';

        $this->assertSame($message, ComposeEmailPayload::applySigningLinkToMessage($message, $url));
    }

    public function test_decode_signing_url_decodes_base64_payload(): void
    {
        $url = 'https://migrationmanager.example.com/sign/42/token_abc';
        $encoded = base64_encode($url);

        $this->assertSame(
            $url,
            ComposeEmailPayload::decodeSigningUrl([
                'signing_url' => $encoded,
                'signing_url_encoding' => 'base64',
            ])
        );
    }

    public function test_decode_signing_url_accepts_plain_value_without_encoding_flag(): void
    {
        $url = 'https://migrationmanager.example.com/sign/7/plainToken';

        $this->assertSame(
            $url,
            ComposeEmailPayload::decodeSigningUrl(['signing_url' => $url])
        );
    }

    public function test_apply_signing_link_repairs_anchor_with_invalid_href(): void
    {
        $url = 'https://migrationmanager.example.com/sign/5/realToken';
        $message = '<p>Agreement: <a href="<a href=&quot;' . $url . '&quot;>broken">Sign Service Agreement</a></p>';

        $result = ComposeEmailPayload::applySigningLinkToMessage($message, $url);

        $this->assertStringContainsString('href="' . $url . '"', $result);
        $this->assertStringContainsString('Sign Service Agreement</a>', $result);
    }

    public function test_message_has_valid_sign_href_ignores_broken_sign_like_href(): void
    {
        $url = 'https://migrationmanager.example.com/sign/3/goodToken';
        $broken = '<a href="<a href=&quot;' . $url . '&quot;>x">Sign Service Agreement</a>';
        $valid = '<a href="' . $url . '">Sign Service Agreement</a>';

        $this->assertFalse(ComposeEmailPayload::messageHasValidSignHref($broken));
        $this->assertTrue(ComposeEmailPayload::messageHasValidSignHref($valid));
    }
}
