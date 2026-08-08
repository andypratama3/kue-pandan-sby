<?php

namespace Tests\Feature;

use App\Services\WhatsApp\FonnteProvider;
use App\Services\WhatsApp\MetaCloudProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppProviderParseTest extends TestCase
{
    public function test_fonnte_parse_incoming_normalizes_payload()
    {
        $provider = new FonnteProvider;

        $parsed = $provider->parseIncoming([
            'device' => '628xxx',
            'sender' => '6281234567890',
            'message' => 'berapa harga kue ijo?',
            'name' => 'Budi',
            'location' => null,
            'timestamp' => 1753156639,
            'inboxid' => 'inbox-99',
        ]);

        $this->assertSame('6281234567890', $parsed['sender']);
        $this->assertSame('Budi', $parsed['name']);
        $this->assertSame('berapa harga kue ijo?', $parsed['text']);
        $this->assertSame('text', $parsed['type']);
        $this->assertSame('inbox-99', $parsed['raw_reply_context']['inboxid']);
    }

    public function test_fonnte_parse_incoming_detects_media_and_location()
    {
        $provider = new FonnteProvider;

        $media = $provider->parseIncoming([
            'sender' => '6281',
            'message' => '',
            'url' => 'https://x.test/img.jpg',
            'filename' => 'img.jpg',
            'extension' => 'jpg',
        ]);
        $this->assertSame('media', $media['type']);

        $location = $provider->parseIncoming([
            'sender' => '6281',
            'location' => '-8.70,115.22',
        ]);
        $this->assertSame('location', $location['type']);
    }

    public function test_fonnte_verify_webhook_is_null()
    {
        $this->assertNull((new FonnteProvider)->verifyWebhook([]));
    }

    public function test_fonnte_normalize_number_converts_leading_zero_to_62()
    {
        $provider = new FonnteProvider;

        $this->assertSame('6282217160075', $provider->normalizeNumber('082217160075'));
        $this->assertSame('6282217160075', $provider->normalizeNumber('6282217160075'));
        $this->assertSame('6282217160075', $provider->normalizeNumber('+62 8221-7160075'));
    }

    public function test_fonnte_send_message_hits_correct_endpoint_with_normalized_number()
    {
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true, 'id' => ['msg-1']], 200),
        ]);

        config()->set('services.fonnte.token', 'dummy-token');
        config()->set('services.fonnte.base_url', 'https://api.fonnte.com');

        $provider = new FonnteProvider;
        $result = $provider->sendMessage('082217160075', 'Halo dari test');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '6282217160075'
                && $request['message'] === 'Halo dari test'
                && $request->hasHeader('Authorization', 'dummy-token');
        });

        $this->assertTrue($result['status']);
    }

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
