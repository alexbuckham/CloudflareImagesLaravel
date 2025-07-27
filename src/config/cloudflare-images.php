<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudflare Account ID
    |--------------------------------------------------------------------------
    |
    | Your Cloudflare account ID. You can find this in your Cloudflare
    | dashboard under the "Account ID" section on the right sidebar.
    |
    */
    'account_id' => env('CF_IMAGES_ACCOUNT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Account Hash
    |--------------------------------------------------------------------------
    |
    | Your Cloudflare account hash. This is used for generating image URLs
    | and can be found in your Cloudflare Images dashboard.
    |
    */
    'account_hash' => env('CF_IMAGES_ACCOUNT_HASH'),

    /*
    |--------------------------------------------------------------------------
    | Custom Domain
    |--------------------------------------------------------------------------
    |
    | If you're using a custom domain for Cloudflare Images delivery,
    | specify it here. Leave null to use the default imagedelivery.net domain.
    |
    */
    'custom_domain' => env('CF_IMAGES_CUSTOM_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Cloudflare API Token
    |--------------------------------------------------------------------------
    |
    | Your Cloudflare API token with Images permissions. You can create
    | a token in your Cloudflare dashboard under "My Profile" > "API Tokens".
    | Make sure it has "Cloudflare Images:Edit" permissions.
    |
    */
    'token' => env('CF_IMAGES_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Images Signing Key
    |--------------------------------------------------------------------------
    |
    | Your Cloudflare Images signing key for generating signed URLs.
    | You can create this in your Cloudflare Images dashboard under
    | "Keys" section. Required for private images and signed URLs.
    |
    */
    'key' => env('CF_IMAGES_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Image Delivery URL
    |--------------------------------------------------------------------------
    |
    | The base URL for image delivery. This is typically in the format:
    | https://imagedelivery.net/{account-hash}
    | You can find this in your Cloudflare Images dashboard.
    |
    */
    'delivery_url' => env('CF_IMAGES_DELIVERY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Default Upload Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for image uploads when not specified explicitly.
    |
    */
    'defaults' => [
        'require_signed_urls' => env('CF_IMAGES_DEFAULT_PRIVATE', false),
        'metadata' => env('CF_IMAGES_DEFAULT_METADATA', 'none'), // keep, copyright, none
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Variants
    |--------------------------------------------------------------------------
    |
    | Define your image variants here. Each variant specifies how images
    | should be transformed when requested. You can create variants
    | programmatically or define them here for easy reference.
    |
    | Available options:
    | - width: Image width in pixels
    | - height: Image height in pixels
    | - fit: How the image should fit (scale-down, contain, cover, crop, pad)
    | - metadata: Metadata to keep (keep, copyright, none)
    | - blur: Blur amount (0-100)
    | - always_public: Whether this variant should always be public
    |
    */
    'variants' => [
        // Example variants - uncomment and customize as needed
        // 'thumbnail' => [
        //     'width' => 200,
        //     'height' => 200,
        //     'fit' => 'cover',
        //     'metadata' => 'none',
        //     'always_public' => true,
        // ],
        // 'small' => [
        //     'width' => 400,
        //     'height' => 400,
        //     'fit' => 'scale-down',
        //     'metadata' => 'copyright',
        //     'always_public' => false,
        // ],
        // 'medium' => [
        //     'width' => 800,
        //     'height' => 800,
        //     'fit' => 'scale-down',
        //     'metadata' => 'copyright',
        //     'always_public' => false,
        // ],
        // 'large' => [
        //     'width' => 1200,
        //     'height' => 1200,
        //     'fit' => 'scale-down',
        //     'metadata' => 'copyright',
        //     'always_public' => false,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Options
    |--------------------------------------------------------------------------
    |
    | Options to pass to the Guzzle HTTP client used for API requests.
    | You can configure timeouts, retry logic, etc.
    |
    */
    'http_options' => [
        'timeout' => env('CF_IMAGES_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('CF_IMAGES_HTTP_CONNECT_TIMEOUT', 10),
    ],
];
