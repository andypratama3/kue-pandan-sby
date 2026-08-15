<?php

namespace Tests\Feature;

use App\Services\WhatsApp\MetaCloudProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppProviderParseTest extends TestCase
{
    public function test_meta_parse_incoming_normalizes_nested_payload()
    {
        $provider = new MetaCloudProvider;

        $parsed = $provider->parseIncoming([
            'object' => 'whatsapp_business_account',
            'entry' => [[
                'id' => 'WABA_ID',
                'changes' => [[
                    'value' => [
                        'messaging_product' => 'whatsapp',
                        'metadata' => [
                            'display_phone_number' => '15551234567',
                            'phone_number_id' => 'PHONE_NUMBER_ID',
                        ],
                        'contacts' => [[
                            'profile' => ['name' => 'Sheena Nelson'],
                            'wa_id' => '16505551234',
                        ]],
                        'messages' => [[
                            'from' => '16505551234',
                            'id' => 'wamid.HBgN',
                            'timestamp' => '1749416383',
                            'type' => 'text',
                            'text' => ['body' => 'Does it come in another color?'],
                        ]],
                    ],
                    'field' => 'messages',
                ]],
            ]],
        ]);

        $this->assertSame('16505551234', $parsed['sender']);
        $this->assertSame('Sheena Nelson', $parsed['name']);
        $this->assertSame('Does it come in another color?', $parsed['text']);
        $this->assertSame('text', $parsed['type']);
        $this->assertSame('wamid.HBgN', $parsed['raw_reply_context']['message_id']);
    }

    public function test_meta_parse_incoming_detects_image_type()
    {
        $provider = new MetaCloudProvider;

        $parsed = $provider->parseIncoming([
            'entry' => [[
                'changes' => [[
                    'value' => [
                        'messages' => [[
                            'from' => '16505551234',
                            'id' => 'wamid_img',
                            'type' => 'image',
                            'image' => ['id' => 'MEDIA_ID', 'mime_type' => 'image/jpeg'],
                        ]],
                    ],
                ]],
            ]],
        ]);

        $this->assertSame('image', $parsed['type']);
        $this->assertNull($parsed['text']);
    }

    public function test_meta_verify_webhook_matches_token()
    {
        config()->set('services.meta_whatsapp.verify_token', 'rahasia');

        $provider = new MetaCloudProvider;

        $ok = $provider->verifyWebhook([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'rahasia',
            'hub_challenge' => 'challenge-123',
        ]);
        $this->assertTrue($ok['verified']);
        $this->assertSame('challenge-123', $ok['challenge']);

        $bad = $provider->verifyWebhook([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'salah',
            'hub_challenge' => 'challenge-123',
        ]);
        $this->assertFalse($bad['verified']);
        $this->assertNull($bad['challenge']);
    }
}
