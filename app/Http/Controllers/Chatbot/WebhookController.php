<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{

    protected $phoneNumberId;
    protected $token;
    protected $version;
    protected $baseUrl;

    protected $userPhoneNumber;
    protected $bodyText;
    protected $footerText;
    protected $mediaUrl;
    protected $messageId;

    public function __construct()
    {
        $this->phoneNumberId = env('META_PHONE_NUMBER_ID');
        $this->token = env('META_ACCESS_TOKEN');
        $this->version = env('META_VERSION');

        $this->baseUrl = "https://graph.facebook.com/{$this->version}/{$this->phoneNumberId}";
    }
    //
    public function verify(Request $request)
    {
        $verifyToken = env('META_VERIFY_TOKEN');

        $mode = $request->hub_mode;
        $token = $request->hub_verify_token;
        $challenge = $request->hub_challenge;

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    protected function client()
    {
        return Http::withToken($this->token);
    }

    private function sendMessage($data)
    {
        try {
            $this->client()->post("{$this->baseUrl}/messages", $data);
        } catch (\Exception $e) {
            Log::error('SEND MESSAGE ERROR: ' . $e->getMessage());
        }
    }

    public function receive(Request $request)
    {
        // Verifikasi signature Meta
        $data = $request->all();

        Log::info('Webhook masuk', $data);

        // cek ada message
        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {

            $message = $data['entry'][0]['changes'][0]['value']['messages'][0];

            $from = $message['from'] ?? null;
            $text = $message['text']['body'] ?? '';

            Log::info('Pesan dari: ' . $from);
            Log::info('Isi pesan: ' . $text);

            // balas otomatis
            $this->sendMessage(
                $from,
                'Halo, pesan kamu diterima: ' . $text
            );
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function typeMessage($type)
    {
        $payload = [];
        switch ($type) {
            case 'typing':
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'status' => "typing_on",
                    'message_id' => $this->messageId,
                    'typing_indicator' => [
                        'type' => "text"
                    ]
                ];
                break;
            case 'reading':
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'status' => "read",
                    'message_id' => $this->messageId,
                    'typing_indicator' => [
                        'type' => "text"
                    ]
                ];
                break;
            case 'text':
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->userPhoneNumber,
                    'type' => 'text',
                    'text' => [
                        'body' => $this->bodyText
                    ]
                ];
                break;
            case 'image':
                $payload = [
                    "messaging_product" => "whatsapp",
                    "recipient_type" => "individual",
                    "to" => $this->userPhoneNumber,
                    "type" => "image",
                    "image" => [
                        "link" => $this->mediaUrl,
                        "caption" => $this->bodyText
                    ]
                ];
                break;
            case 'interactive:location_request_message':
                $payload = [
                    "messaging_product" => "whatsapp",
                    "recipient_type" => "individual",
                    "type" => "interactive",
                    "to" => $this->userPhoneNumber,
                    "interactive" => [
                        "type" => "location_request_message",
                        "body" => [
                            "text" => $this->bodyText
                        ],
                        "action" => [
                            "name" => "send_location"
                        ]
                    ]
                ];
                break;
            case 'interactive:caraousel':
                $payload = [
                    "messaging_product" => "whatsapp",
                    "recipient_type" => "individual",
                    "to" => $this->userPhoneNumber,
                    "type" => "interactive",
                    "interactive" => [
                        "type" => "carousel",
                        "body" => [
                            "text" => $this->bodyText
                        ],
                        "action" => [
                            "cards" => [
                                [
                                    "card_index" => 0,
                                    "type" => "cta_url",
                                    "header" => [
                                        "type" => "image",
                                        "image" => [
                                            "link" => $this->mediaUrl
                                        ]
                                    ],
                                    "body" => [
                                        "text" => $this->bodyText
                                    ],
                                    "action" => [
                                        "name" => "cta_url",
                                        "parameters" => [
                                            "display_text" => "<URL_BUTTON_LABEL>",
                                            "url" => "<URL_BUTTON_URL>"
                                        ],
                                        "buttons" => [
                                            [
                                                "type" => "quick_reply",
                                                "quick_reply" => [
                                                    "id" => "<QUICK_REPLY_BUTTON_ID>",
                                                    "title" => "<QUICK_REPLY_BUTTON_LABEL>"
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
                break;
            case 'interactive:button':
                $payload = [
                    "messaging_product" => "whatsapp",
                    "recipient_type" => "individual",
                    "to" => $this->userPhoneNumber,
                    "type" => "interactive",
                    "interactive" => [
                        "type" => "button",
                        "header" => "text",
                        "body" => [
                            "text" => $this->bodyText
                        ],
                        "footer" => [
                            "text" => $this->footerText
                        ],
                        "action" => [
                            "buttons" => [
                                [
                                    "type" => "reply",
                                    "reply" =>   [
                                        "id" => "<BUTTON_ID>",
                                        "title" => "<BUTTON_LABEL_TEXT>"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
                break;
            default:
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => "",
                    'type' => 'text',
                    'text' => [
                        'body' => ""
                    ]
                ];
                break;
        }

        return $payload;
    }

    public function detectIntent($text)
    {
        // $text = strtolower($text);
        // if (strpos($text, 'halo') !== false) {
            // return 'greeting';
        // } elseif (strpos($text, 'bantuan') !== false) {
            // return 'help';
        // } else {
            // return 'unknown';
        // }
    }
}
