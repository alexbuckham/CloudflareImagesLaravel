# CloudflareImagesLaravel

[![Latest Stable Version](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/v)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![Total Downloads](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/downloads)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![Latest Unstable Version](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/v/unstable)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![License](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/license)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![PHP Version Require](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/require/php)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel)

A Laravel package that provides seamless integration with Cloudflare Images service. Upload, transform, and deliver images with ease using Cloudflare's powerful image optimization platform.

## Features

- 🚀 **Easy Integration**: Simple Laravel service provider and facade
- 🔒 **Secure Uploads**: Support for private images and signed URLs
- 🎨 **Image Variants**: Create and manage image transformations
- 📤 **Direct Uploads**: Generate secure upload URLs for client-side uploads
- ⚡ **Laravel 10, 11, 12 Compatible**: Full support for modern Laravel versions (v3.x)
- 🧪 **Well Tested**: Comprehensive test suite included

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x
- Cloudflare account with Images enabled

## Table of Contents

* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
  * [Basic Upload](#basic-upload)
  * [Creating Variants](#creating-variants)
  * [Direct Upload URLs](#direct-upload-urls)
  * [Signed URLs](#signed-urls)
  * [Using the Facade](#using-the-facade)
* [Configuration Options](#configuration-options)
* [Testing](#testing)
* [Contributing](#contributing)
* [License](#license)

## Installation

Install the package via Composer:

```bash
composer require alexbuckham/cloudflare-images-laravel
```

### Laravel Auto-Discovery

The package will automatically register itself via Laravel's package auto-discovery feature. No manual service provider registration is needed.

### Publish Configuration

Publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --tag="cloudflare-images-config"
```

This will create a `config/cloudflare-images.php` file in your application.

## Configuration

### Environment Variables

Add the following environment variables to your `.env` file:

```env
# Required
CF_IMAGES_ACCOUNT_ID=your-cloudflare-account-id
CF_IMAGES_TOKEN=your-cloudflare-api-token

# Required for signed URLs and private images
CF_IMAGES_KEY=your-cloudflare-images-signing-key

# Required for URL generation
CF_IMAGES_DELIVERY_URL=https://imagedelivery.net/your-account-hash

# Optional
CF_IMAGES_ACCOUNT_HASH=your-account-hash
CF_IMAGES_CUSTOM_DOMAIN=your-custom-domain.com
CF_IMAGES_DEFAULT_PRIVATE=false
CF_IMAGES_DEFAULT_METADATA=none
CF_IMAGES_HTTP_TIMEOUT=30
CF_IMAGES_HTTP_CONNECT_TIMEOUT=10
```

### Getting Your Credentials

1. **Account ID**: Found in your Cloudflare dashboard sidebar
2. **API Token**: Create one in "My Profile" > "API Tokens" with "Cloudflare Images:Edit" permissions
3. **Signing Key**: Generate in Cloudflare Images dashboard under "Keys"
4. **Delivery URL**: Copy from your Cloudflare Images dashboard

## Usage

### Basic Upload

Upload an image file:

```php
use AlexBuckham\CloudflareImagesLaravel\CloudflareImages;

$cloudflareImages = new CloudflareImages();

// Upload from file path
$result = $cloudflareImages->upload('/path/to/image.jpg');

// Upload from UploadedFile (e.g., from a form)
$result = $cloudflareImages->upload($request->file('image'));

// Upload as private image (requires signed URLs)
$result = $cloudflareImages->upload('/path/to/image.jpg', true);

echo $result->id; // Image UUID
echo $result->filename; // Original filename
```

### Creating Variants

Create image variants for different use cases:

```php
use AlexBuckham\CloudflareImagesLaravel\ImageVariant;
use AlexBuckham\CloudflareImagesLaravel\CloudflareImages;

// Create a thumbnail variant
$variant = new ImageVariant('thumbnail');
$variant->width(200)
       ->height(200)
       ->fit(ImageVariant::FIT_COVER)
       ->metaData(ImageVariant::METADATA_NONE)
       ->alwaysPublic(true);

$cloudflareImages = new CloudflareImages();
$result = $cloudflareImages->createVariant($variant);

// Create from configuration array
$variant = ImageVariant::fromConfig('large', [
    'width' => 1200,
    'height' => 1200,
    'fit' => ImageVariant::FIT_SCALE_DOWN,
    'metadata' => ImageVariant::METADATA_COPYRIGHT,
    'blur' => 0,
    'always_public' => false,
]);
```

#### Available Fit Options

- `ImageVariant::FIT_SCALE_DOWN` - Scale down to fit within dimensions
- `ImageVariant::FIT_CONTAIN` - Contain within dimensions, may add padding
- `ImageVariant::FIT_COVER` - Cover entire area, may crop
- `ImageVariant::FIT_CROP` - Crop to exact dimensions
- `ImageVariant::FIT_PAD` - Pad image to exact dimensions

#### Available Metadata Options

- `ImageVariant::METADATA_KEEP` - Keep all metadata
- `ImageVariant::METADATA_COPYRIGHT` - Keep only copyright information
- `ImageVariant::METADATA_NONE` - Remove all metadata

### Direct Upload URLs

Generate secure upload URLs for client-side uploads:

```php
$cloudflareImages = new CloudflareImages();

// Generate public upload URL
$uploadUrl = $cloudflareImages->generateUploadUrl();

// Generate private upload URL
$uploadUrl = $cloudflareImages->generateUploadUrl(true);

echo $uploadUrl->uploadURL; // Use this URL for client-side uploads
```

### Signed URLs

Generate signed URLs for private images:

```php
$cloudflareImages = new CloudflareImages();

// Create signed URL that expires in 1 day
$signedUrl = $cloudflareImages->getSignedUrl(
    'image-uuid-here',
    new DateTime('+1 day')
);

echo $signedUrl; // https://imagedelivery.net/account-hash/image-uuid?exp=...&sig=...
```

### Using the Facade

You can also use the facade for cleaner syntax:

```php
use CloudflareImages; // Auto-registered alias

$result = CloudflareImages::upload('/path/to/image.jpg');
$uploadUrl = CloudflareImages::generateUploadUrl();
$signedUrl = CloudflareImages::getSignedUrl('uuid', new DateTime('+1 hour'));
```

### Service Container

The package registers itself in Laravel's service container:

```php
// Dependency injection
public function upload(CloudflareImages $cloudflareImages, Request $request)
{
    $result = $cloudflareImages->upload($request->file('image'));
    return response()->json($result);
}

// Manual resolution
$cloudflareImages = app(CloudflareImages::class);
```

### Overriding Configuration

You can override configuration values by passing them to the constructor:

```php
$cloudflareImages = new CloudflareImages(
    'custom-account-id',
    'custom-token',
    'custom-key',
    'https://custom-delivery-url.com'
);
```

## Configuration Options

The `config/cloudflare-images.php` file contains all available configuration options:

```php
return [
    // API Credentials
    'account_id' => env('CF_IMAGES_ACCOUNT_ID'),
    'token' => env('CF_IMAGES_TOKEN'),
    'key' => env('CF_IMAGES_KEY'),
    
    // Image delivery
    'delivery_url' => env('CF_IMAGES_DELIVERY_URL'),
    'custom_domain' => env('CF_IMAGES_CUSTOM_DOMAIN'),
    
    // Default settings
    'defaults' => [
        'require_signed_urls' => env('CF_IMAGES_DEFAULT_PRIVATE', false),
        'metadata' => env('CF_IMAGES_DEFAULT_METADATA', 'none'),
    ],
    
    // HTTP client options
    'http_options' => [
        'timeout' => env('CF_IMAGES_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('CF_IMAGES_HTTP_CONNECT_TIMEOUT', 10),
    ],
    
    // Pre-defined variants
    'variants' => [
        // Define your variants here
    ],
];
```

## Testing

Run the test suite:

```bash
composer test
```

Or run with coverage:

```bash
composer test-coverage
```

### Writing Tests

The package includes a comprehensive test suite. When writing your own tests, you can mock the Cloudflare API calls:

```php
use AlexBuckham\CloudflareImagesLaravel\CloudflareImages;

public function test_image_upload()
{
    $mock = Mockery::mock(CloudflareImages::class);
    $mock->shouldReceive('upload')
         ->once()
         ->with('/path/to/image.jpg')
         ->andReturn((object)['id' => 'test-uuid']);
    
    $this->app->instance(CloudflareImages::class, $mock);
    
    // Your test code here
}
```

## Version Compatibility

| Laravel Version | PHP Version | Package Version |
|----------------|-------------|-----------------|
| 10.x           | 8.1 - 8.3   | ^3.0           |
| 11.x           | 8.2 - 8.4   | ^3.0           |
| 12.x           | 8.2 - 8.4   | ^3.0           |

## Error Handling

The package throws exceptions for various error conditions:

```php
try {
    $result = $cloudflareImages->upload('/path/to/image.jpg');
} catch (\Exception $e) {
    // Handle upload errors
    Log::error('Image upload failed: ' . $e->getMessage());
}
```

Common exceptions:
- Missing API credentials
- Invalid image files
- Network timeouts
- Cloudflare API errors

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. Make sure to:

1. Follow PSR-12 coding standards
2. Add tests for new features
3. Update documentation as needed
4. Ensure all tests pass

## Security

If you discover any security-related issues, please email the maintainer instead of using the issue tracker.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Upgrade Guide

For detailed upgrade instructions, see the [UPGRADE GUIDE](UPGRADE.md).

### From v2.x to v3.x

This version maintains backward compatibility with v2.x while adding support for Laravel 12. No breaking changes are introduced.

### From v1.x to v3.x

Please see the [UPGRADE GUIDE](UPGRADE.md) for detailed breaking changes and migration steps when upgrading from v1.x.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Alex Buckham](https://github.com/alexbuckham)
- [All Contributors](../../contributors)

---

Built with ❤️ for the Laravel community.