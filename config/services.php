<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'meta' => [
        'phone_id' => env('META_PHONE_ID'),
        'token' => env('META_WHATSAPP_TOKEN'),
        'verify_token' => env('META_VERIFY_TOKEN', 'Thiago'),
        'whatsapp_number' => env('META_WHATSAPP_NUMBER', '558183797797'),
        'app_secret' => env('META_APP_SECRET'),
        'skip_signature_validation' => env('META_SKIP_SIGNATURE_VALIDATION', false),

        /*
        |----------------------------------------------------------------
        | Templates HSM aprovados na Meta
        |----------------------------------------------------------------
        | A Meta WhatsApp Business API só entrega mensagens de TEXTO LIVRE
        | dentro da janela de 24h após o usuário enviar uma mensagem ao
        | bot. Fora dessa janela, apenas templates aprovados (HSM)
        | conseguem entregar — texto livre é aceito (200 + wamid) mas
        | nunca chega no celular (erro 131047 "Re-engagement message").
        |
        | Quando `whatsapp_use_templates` está ligado, as Notifications
        | que implementam `toWhatsappTemplate()` enviam template; caso
        | contrário (ou pra Notifications que não tem template), cai pro
        | texto livre (válido só dentro da janela de 24h).
        */
        'whatsapp_use_templates' => env('META_WHATSAPP_USE_TEMPLATES', true),
        'whatsapp_template_language' => env('META_WHATSAPP_TEMPLATE_LANG', 'pt_BR'),
        'whatsapp_templates' => [
            'lancamento_agendado' => env('META_TEMPLATE_LANCAMENTO_AGENDADO', 'lancamento_agendado_aviso'),
            'rateio_recebido'     => env('META_TEMPLATE_RATEIO_RECEBIDO',    'rateio_recebido_aviso'),
        ],
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    'webmaniabr' => [
        'app_key'    => env('WEBMANIABR_APP_KEY', ''),
        'app_secret' => env('WEBMANIABR_APP_SECRET', ''),
    ],

];
