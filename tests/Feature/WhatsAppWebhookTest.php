<?php

namespace Tests\Feature;

use App\Contracts\WhatsAppProviderInterface;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
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
        return [
            'sender' => (string) ($payload['sender'] ?? ''),
            'name' => $payload['name'] ?? null,
            'text' => $payload['message'] ?? null,
            'type' => $payload['type'] ?? 'text',
            'raw_reply_context' => ['inboxid' => $payload['inboxid'] ?? null, 'sender' => $payload['sender'] ?? null],
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
    }

    public function fonntePayload(string $message, ?string $type = null, ?string $inboxid = null): array
    {
        return array_filter([
            'device' => '1234',
            'sender' => '6281234567890',
            'message' => $message,
            'name' => 'Tester',
            'inboxid' => $inboxid ?? uniqid(),
            'timestamp' => time(),
            'type' => $type,
        ]);
    }

    protected function lastReply(): string
    {
        return $this->provider->sent[array_key_last($this->provider->sent)]['message'] ?? '';
    }

    protected function seedSurabayaWithKueIjo(): Region
    {
        $region = Region::create(['name' => 'Surabaya', 'slug' => 'surabaya']);
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

    public static function providers(): array
    {
        return [['fonnte'], ['meta']];
    }

    #[DataProvider('providers')]
    public function test_greeting_gets_ramah_reply(string $provider)
    {
        config()->set('services.whatsapp.provider', $provider);

        $this->postJson('/api/webhook/whatsapp', $this->fonntePayload('halo'))
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertStringContainsString('Halo', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', [
            'provider' => $provider,
            'sender_number' => '6281234567890',
            'detected_intent' => 'sapaan',
            'handled_by_ai' => false,
        ]);
    }

    #[DataProvider('providers')]
    public function test_price_question_uses_db_price_only(string $provider)
    {
        config()->set('services.whatsapp.provider', $provider);
        $this->seedSurabayaWithKueIjo();

        $this->postJson('/api/webhook/whatsapp', $this->fonntePayload('berapa harga kue ijo?'))
            ->assertOk();

        $reply = $this->lastReply();
        $this->assertStringContainsString('Kue Ijo', $reply);
        $this->assertStringContainsString('9.000', $reply);
        $this->assertStringContainsString('15.000', $reply);
        $this->assertDatabaseHas('chatbot_conversations', [
            'provider' => $provider,
            'detected_intent' => 'tanya_harga',
        ]);
    }

    #[DataProvider('providers')]
    public function test_menu_question_lists_products(string $provider)
    {
        config()->set('services.whatsapp.provider', $provider);
        $this->seedSurabayaWithKueIjo();

        $this->postJson('/api/webhook/whatsapp', $this->fonntePayload('menu apa saja?'))
            ->assertOk();

        $this->assertStringContainsString('Kue Ijo', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', ['detected_intent' => 'tanya_produk']);
    }

    #[DataProvider('providers')]
    public function test_location_question_returns_outlet_info(string $provider)
    {
        config()->set('services.whatsapp.provider', $provider);
        $this->seedSurabayaWithKueIjo();

        $this->postJson('/api/webhook/whatsapp', $this->fonntePayload('lokasi outlet dan jam bukanya?'))
            ->assertOk();

        $this->assertStringContainsString('Surabaya', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', ['detected_intent' => 'tanya_lokasi_jam']);
    }

    #[DataProvider('providers')]
    public function test_how_to_order_question(string $provider)
    {
        config()->set('services.whatsapp.provider', $provider);

        $this->postJson('/api/webhook/whatsapp', $this->fonntePayload('cara order kue'))
            ->assertOk();

        $this->assertStringContainsString('Cara memesan', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', ['detected_intent' => 'cara_order']);
    }

    #[DataProvider('providers')]
    public function test_out_of_topic_question_falls_back_politely(string $provider)
    {
        config()->set('services.whatsapp.provider', $provider);

        $this->postJson('/api/webhook/whatsapp', $this->fonntePayload('kapan indonesia merdeka?'))
            ->assertOk();

        $this->assertStringContainsString('Maaf', $this->lastReply());
        $this->assertDatabaseHas('chatbot_conversations', ['detected_intent' => 'lainnya']);
    }

    #[DataProvider('providers')]
    public function test_media_message_gets_polite_reply(string $provider)
    {
        config()->set('services.whatsapp.provider', $provider);

        $this->postJson('/api/webhook/whatsapp', $this->fonntePayload('', 'image', null))
            ->assertOk();

        $this->assertStringContainsString('pesan teks', $this->lastReply());
    }

    public function test_incomplete_payload_returns_200_without_exception()
    {
        $this->postJson('/api/webhook/whatsapp', [])
            ->assertOk()
            ->assertJson(['status' => true]);
    }

    public function test_meta_verify_returns_challenge_when_token_correct()
    {
        config()->set('services.meta_whatsapp.verify_token', 'secret-verify-123');

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
        config()->set('services.meta_whatsapp.verify_token', 'secret-verify-123');

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

        $this->postJson('/api/webhook/whatsapp', $this->fonntePayload('halo'))
            ->assertOk();
        $this->assertDatabaseHas('chatbot_conversations', [
            'sender_number' => '6281234567890',
            'region_id' => $region->id,
        ]);
    }
}
