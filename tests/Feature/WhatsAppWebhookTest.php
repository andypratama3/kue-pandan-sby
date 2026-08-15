<?php

namespace Tests\Feature;

use App\Contracts\WhatsAppProviderInterface;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FakeWhatsAppProvider implements WhatsAppProviderInterface
{
    public array $sent = [];

    public function sendMessage(string $target, string $message, array $context = []): array
    {
        $this->sent[] = ['target' => $target, 'message' => $message];

        return ['success' => true, 'message_id' => 'fake-id', 'raw' => []];
    }

    public function sendTyping(string $target, int $durationSeconds = 2): void {}

    public function parseIncoming(array $payload): array
    {
        $entry = $payload['entry'][0] ?? [];
        $change = $entry['changes'][0] ?? [];
        $field = $change['field'] ?? null;
        $value = $change['value'] ?? [];
        $message = $value['messages'][0] ?? [];
        $metadata = $value['metadata'] ?? [];

        if ($field === 'message_echoes') {
            return [
                'sender' => '',
                'name' => null,
                'text' => null,
                'type' => 'echo',
                'recipient' => null,
                'raw_reply_context' => ['message_id' => null, 'sender' => null],
            ];
        }

        return [
            'sender' => (string) ($message['from'] ?? ''),
            'name' => $value['contacts'][0]['profile']['name'] ?? null,
            'text' => $message['text']['body'] ?? null,
            'type' => $message['type'] ?? 'text',
            'recipient' => $metadata['display_phone_number'] ?? null,
            'raw_reply_context' => ['message_id' => $message['id'] ?? null, 'sender' => $message['from'] ?? null],
        ];
    }

    public function verifyWebhook(array $query): ?array
    {
        if (($query['hub_mode'] ?? '') === 'subscribe'
            && ($query['hub_verify_token'] ?? null) === config('services.meta_whatsapp.verify_token')) {
            return ['verified' => true, 'challenge' => (string) ($query['hub_challenge'] ?? '')];
        }

        return ['verified' => false, 'challenge' => null];
    }
}

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected FakeWhatsAppProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new FakeWhatsAppProvider;
        $this->app->instance(WhatsAppProviderInterface::class, $this->provider);

        config()->set('services.whatsapp.provider', 'meta');
        config()->set('services.meta_whatsapp.verify_token', 'secret-verify-123');
        config()->set('services.meta_whatsapp.app_secret', 'test-app-secret');
    }

    public function metaPayload(string $message, ?string $type = null, ?string $messageId = null): array
    {
        $payload = [
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
                            'profile' => ['name' => 'Tester'],
                            'wa_id' => '6281234567890',
                        ]],
                        'messages' => [[
                            'from' => '6281234567890',
                            'id' => $messageId ?? uniqid('wamid.'),
                            'timestamp' => (string) time(),
                            'type' => $type ?? 'text',
                            'text' => ['body' => $message],
                        ]],
                    ],
                    'field' => 'messages',
                ]],
            ]],
        ];

        return $payload;
    }

    protected function postMeta(array $payload, array $headers = []): \Illuminate\Testing\TestResponse
    {
        $content = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = 'sha256='.hash_hmac('sha256', $content, config('services.meta_whatsapp.app_secret'));

        return $this->call(
            'POST',
            '/api/webhook/whatsapp/meta',
            [],
            [],
            [],
            $this->transformHeadersToServerVars([
                'Content-Type' => 'application/json',
                'X-Hub-Signature-256' => $signature,
            ] + $headers),
            $content,
        );
    }

    protected function lastReply(): string
    {
        return $this->provider->sent[array_key_last($this->provider->sent)]['message'] ?? '';
    }

    protected function seedSurabayaWithKueIjo(): Region
    {
        $region = Region::create([
            'name' => 'Surabaya',
            'slug' => 'surabaya',
            'is_active' => true,
            'address' => 'Jl. Test Surabaya',
            'operating_hours' => ['open' => '06:00', 'close' => '23:00'],
            'contact_email' => 'test@surabaya.com',
            'contact_phone' => '081234567890',
        ]);
        $category = Category::create(['name' => 'Produk', 'slug' => 'produk']);

        $kueIjo = $category->products()->create([
            'name' => 'Kue Ijo',
            'description' => 'Kue pandan tradisional.',
            'region_id' => $region->id,
            'tag' => 'Ala Carte',
            'is_active' => true,
        ]);
        $kueIjo->variants()->createMany([
            ['name' => 'Isi 3 Kemasan Mika', 'price' => 9000],
            ['name' => 'Isi 5 Kemasan Mika', 'price' => 15000],
        ]);

        return $region;
    }

    public function test_greeting_gets_ramah_reply()
    {
        $this->postMeta($this->metaPayload('halo'))
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertStringContainsString('Halo', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', [
            'provider' => 'meta',
            'sender_number' => '6281234567890',
            'detected_intent' => 'sapaan',
            'handled_by_ai' => false,
        ]);
    }

    public function test_price_question_uses_db_price_only()
    {
        $this->seedSurabayaWithKueIjo();

        $this->postMeta($this->metaPayload('berapa harga kue ijo?'))
            ->assertOk();

        $reply = $this->lastReply();
        $this->assertStringContainsString('Kue Ijo', $reply);
        $this->assertStringContainsString('9.000', $reply);
        $this->assertStringContainsString('15.000', $reply);
        $this->assertDatabaseHas('chatbot_conversations', [
            'provider' => 'meta',
            'detected_intent' => 'tanya_harga',
        ]);
    }

    public function test_menu_question_lists_products()
    {
        $this->seedSurabayaWithKueIjo();

        $this->postMeta($this->metaPayload('menu apa saja?'))
            ->assertOk();

        $this->assertStringContainsString('Kue Ijo', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', ['detected_intent' => 'tanya_produk']);
    }

    public function test_location_question_returns_outlet_info()
    {
        $this->seedSurabayaWithKueIjo();

        $this->postMeta($this->metaPayload('lokasi outlet dan jam bukanya?'))
            ->assertOk();

        $this->assertStringContainsString('Surabaya', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', ['detected_intent' => 'tanya_lokasi_jam']);
    }

    public function test_how_to_order_question()
    {
        $this->postMeta($this->metaPayload('cara order kue'))
            ->assertOk();

        $this->assertStringContainsString('Cara memesan', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', ['detected_intent' => 'cara_order']);
    }

    public function test_out_of_topic_question_falls_back_politely()
    {
        $this->postMeta($this->metaPayload('kapan indonesia merdeka?'))
            ->assertOk();

        $this->assertStringContainsString('Maaf', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', ['detected_intent' => 'lainnya']);
    }

    public function test_media_message_gets_polite_reply()
    {
        $this->postMeta($this->metaPayload('', 'image'))
            ->assertOk();

        $this->assertStringContainsString('pesan teks', $this->lastReply());
    }

    public function test_duplicate_message_id_is_ignored()
    {
        $messageId = 'wamid.duplicate-1';

        $this->postMeta($this->metaPayload('halo', null, $messageId))
            ->assertOk();
        $this->postMeta($this->metaPayload('halo', null, $messageId))
            ->assertOk()
            ->assertJson(['status' => true, 'duplicate' => true]);

        $this->assertCount(1, $this->provider->sent);
    }

    public function test_incomplete_payload_returns_200_without_exception()
    {
        $this->postMeta([])
            ->assertOk()
            ->assertJson(['status' => true]);
    }

    public function test_non_message_events_return_200_without_reply()
    {
        $events = [
            'account_alerts' => ['alerts' => ['data' => 'x']],
            'account_update' => ['account_review' => 'APPROVED'],
            'security' => ['security_code' => 'abc'],
            'message_template_status_update' => ['event' => 'APPROVED'],
            'phone_number_quality_update' => ['quality' => 'GREEN'],
            'messages' => ['statuses' => [[
                'id' => 'wamid.sent-1',
                'status' => 'delivered',
                'recipient_id' => '6281234567890',
            ]]],
        ];

        foreach ($events as $field => $value) {
            $payload = [
                'object' => 'whatsapp_business_account',
                'entry' => [[
                    'id' => 'WABA_ID',
                    'changes' => [[
                        'field' => $field,
                        'value' => $value,
                    ]],
                ]],
            ];

            $this->postMeta($payload)
                ->assertOk()
                ->assertJson(['status' => true]);
        }

        $this->assertEmpty($this->provider->sent);
    }

    public function test_message_echo_event_does_not_trigger_reply_loop()
    {
        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [[
                'id' => 'WABA_ID',
                'changes' => [[
                    'field' => 'message_echoes',
                    'value' => [
                        'metadata' => [
                            'display_phone_number' => '15551234567',
                            'phone_number_id' => 'PHONE_NUMBER_ID',
                        ],
                        'contacts' => [[
                            'profile' => ['name' => 'Tester'],
                            'wa_id' => '6281234567890',
                        ]],
                        'messages' => [[
                            'from' => '15551234567',
                            'id' => 'wamid.echo-1',
                            'timestamp' => (string) time(),
                            'type' => 'text',
                            'text' => ['body' => 'Halo, ini balasan bot'],
                        ]],
                    ],
                ]],
            ]],
        ];

        $this->postMeta($payload)
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertEmpty($this->provider->sent);
        $this->assertDatabaseCount('chatbot_conversations', 0);
    }

    public function test_webhook_rejects_invalid_signature()
    {
        $content = json_encode($this->metaPayload('halo'));

        $this->call(
            'POST',
            '/api/webhook/whatsapp/meta',
            [],
            [],
            [],
            $this->transformHeadersToServerVars(['X-Hub-Signature-256' => 'sha256=invalid']),
            $content,
        )->assertForbidden();

        $this->call(
            'POST',
            '/api/webhook/whatsapp/meta',
            [],
            [],
            [],
            $this->transformHeadersToServerVars([]),
            $content,
        )->assertForbidden();

        $this->assertEmpty($this->provider->sent);
    }

    public function test_meta_verify_returns_challenge_when_token_correct()
    {
        $this->get('/api/webhook/whatsapp/meta?'.http_build_query([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'secret-verify-123',
            'hub_challenge' => 'challenge-abc',
        ]))
            ->assertOk()
            ->assertSee('challenge-abc');
    }

    public function test_meta_verify_returns_403_when_token_wrong()
    {
        $this->get('/api/webhook/whatsapp/meta?'.http_build_query([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'salah',
            'hub_challenge' => 'challenge-abc',
        ]))
            ->assertForbidden();
    }

    public function test_sender_region_mapping_persists_across_conversations()
    {
        $region = $this->seedSurabayaWithKueIjo();

        $this->postMeta($this->metaPayload('halo'))
            ->assertOk();
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'region_id' => $region->id,
        ]);
    }

    // ====== ALUR ORDER (WhatsAppOrderService) ======

    protected function seedOrderFlowRegion(): Region
    {
        $region = $this->seedSurabayaWithKueIjo();

        \App\Models\ShippingArea::create([
            'region_id' => $region->id,
            'area_name' => 'Rungkut',
            'distance_km' => 5,
            'shipping_fee' => 10000,
        ]);
        \App\Models\ShippingArea::create([
            'region_id' => $region->id,
            'area_name' => 'Wonokromo',
            'distance_km' => 12,
            'shipping_fee' => 15000,
        ]);

        return $region;
    }

    public function test_order_flow_full_turn_creates_order_from_database()
    {
        $region = $this->seedOrderFlowRegion();

        // 1. Mulai order -> katalog ditampilkan.
        $this->postMeta($this->metaPayload('pesan'))
            ->assertOk();
        $this->assertStringContainsString('Kue Ijo', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_catalog',
        ]);

        // 2. Pilih produk dengan nomor -> varian ditampilkan.
        $this->postMeta($this->metaPayload('1'))
            ->assertOk();
        $this->assertStringContainsString('Isi 3 Kemasan Mika', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_variant',
        ]);

        // 3. Pilih varian -> minta jumlah.
        $this->postMeta($this->metaPayload('1'))
            ->assertOk();
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_quantity',
        ]);

        // 4. Jumlah -> minta area.
        $this->postMeta($this->metaPayload('2'))
            ->assertOk();
        $this->assertStringContainsString('area', mb_strtolower($this->lastReply()));
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_location',
        ]);

        // 5. Area -> minta alamat + hitung ongkir (Rp 10.000 untuk Rungkut).
        $this->postMeta($this->metaPayload('Rungkut'))
            ->assertOk();
        $this->assertStringContainsString('10.000', $this->lastReply());
        $this->assertStringContainsString('alamat', mb_strtolower($this->lastReply()));
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_address',
        ]);

        // 6. Alamat -> ringkasan pesanan.
        $this->postMeta($this->metaPayload('Jl. Rungkut Harapan 12, Surabaya'))
            ->assertOk();
        $this->assertStringContainsString('TOTAL', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_confirm',
        ]);

        // 7. Konfirmasi -> order dibuat (source wa_bot, harga dari DB).
        $this->postMeta($this->metaPayload('YA'))
            ->assertOk();
        $this->assertStringContainsString('Pesanan berhasil dibuat', $this->lastReply());

        $order = \App\Models\Order::where('source', 'wa_bot')->first();
        $this->assertNotNull($order);
        $this->assertEquals('baru', $order->status);
        $this->assertEquals('wa_bot', $order->payment_method);
        $this->assertEquals($region->id, $order->region_id);
        $this->assertEquals(28000, (int) $order->total_amount); // 2x9000 + ongkir 10.000
        $this->assertSame('INV/WA/', substr($order->invoice_number, 0, 7));

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'variant_name' => 'Isi 3 Kemasan Mika',
            'quantity' => 2,
            'price' => 9000,
        ]);

        $this->assertDatabaseHas('customers', [
            'phone' => '6281234567890',
        ]);
    }

    public function test_order_flow_cancel_from_catalog_resets_state()
    {
        $this->seedOrderFlowRegion();

        $this->postMeta($this->metaPayload('pesan'))
            ->assertOk();
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_catalog',
        ]);

        $this->postMeta($this->metaPayload('batal'))
            ->assertOk();
        $this->assertStringContainsString('dibatalkan', mb_strtolower($this->lastReply()));
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'idle',
        ]);
    }

    public function test_order_flow_invalid_product_stays_on_catalog()
    {
        $this->seedOrderFlowRegion();

        $this->postMeta($this->metaPayload('pesan'))->assertOk();
        $this->postMeta($this->metaPayload('produk yang tidak ada'))
            ->assertOk();
        $this->assertStringContainsString('ulangi', mb_strtolower($this->lastReply()));
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_catalog',
        ]);
    }

    public function test_order_flow_stays_in_flow_when_question_asked_after_start()
    {
        $this->seedOrderFlowRegion();

        // Mulai order, lalu (mis. user berubah pikiran) tanya harga produk.
        $this->postMeta($this->metaPayload('pesan'))->assertOk();
        $this->postMeta($this->metaPayload('berapa harga kue ijo'))
            ->assertOk();

        // Karena masih berada di step order, pesan tersebut dianggap pilihan
        // produk (bukan navigasi bebas) - state tidak keluar dari alur order.
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'current_step' => 'order_catalog',
        ]);
    }
}
