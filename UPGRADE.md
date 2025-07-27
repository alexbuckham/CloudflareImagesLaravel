# Upgrade Guide

This guide covers upgrading to version 3.x of the CloudflareImagesLaravel package.

## Upgrading to 3.x from 2.x

Version 3.x is a **non-breaking** upgrade from 2.x that adds Laravel 12 support. No code changes are required.

### What's New in 3.x

- ✅ Laravel 12 support
- ✅ Enhanced error handling
- ✅ Improved configuration validation
- ✅ Better type safety
- ✅ Comprehensive test suite

### Steps to Upgrade

1. Update your `composer.json`:

```json
{
    "require": {
        "alexbuckham/cloudflare-images-laravel": "^3.0"
    }
}
```

2. Run the update command:

```bash
composer update alexbuckham/cloudflare-images-laravel
```

3. Optionally republish the configuration file to get the latest features:

```bash
php artisan vendor:publish --tag="cloudflare-images-config" --force
```

That's it! Your existing code will continue to work without any changes.

## Upgrading to 3.x from 1.x

If you're upgrading from version 1.x, there are several breaking changes to be aware of.

### Breaking Changes

#### 1. Minimum PHP Version

- **Before (1.x)**: PHP 7.4+
- **After (3.x)**: PHP 8.1+

Make sure your application is running PHP 8.1 or higher.

#### 2. Laravel Version Support

- **Before (1.x)**: Laravel 8.x
- **After (3.x)**: Laravel 10.x, 11.x, 12.x

You'll need to upgrade your Laravel application to version 10 or higher.

#### 3. Facade Namespace

- **Before (1.x)**: `AlexBuckham\CloudflareImagesLaravel\CloudflareImagesFacade`
- **After (3.x)**: `AlexBuckham\CloudflareImagesLaravel\Facades\CloudflareImagesFacade`

If you're manually importing the facade class, update your imports:

```php
// Before
use AlexBuckham\CloudflareImagesLaravel\CloudflareImagesFacade;

// After
use AlexBuckham\CloudflareImagesLaravel\Facades\CloudflareImagesFacade;
```

However, if you're using the auto-registered alias, no changes are needed:

```php
use CloudflareImages; // This still works
```

#### 4. Service Container Registration

The service is now properly registered in Laravel's container. If you were manually binding the service, you can remove that code:

```php
// You can remove this if you had it
$this->app->singleton(CloudflareImages::class, function() {
    return new CloudflareImages();
});
```

#### 5. Error Handling

API calls now throw exceptions instead of returning false on errors:

```php
// Before (1.x)
$result = $cloudflareImages->upload('/path/to/image.jpg');
if ($result === false) {
    // Handle error
}

// After (3.x)
try {
    $result = $cloudflareImages->upload('/path/to/image.jpg');
    // Handle success
} catch (Exception $e) {
    // Handle error
    Log::error('Upload failed: ' . $e->getMessage());
}
```

#### 6. ImageVariant Validation

Variant validation is now stricter and throws exceptions for invalid configurations:

```php
// Before (1.x) - might silently fail
$variant = new ImageVariant('test');
$variant->width(400);
// Missing required fields would be ignored

// After (3.x) - throws exceptions
$variant = new ImageVariant('test');
$variant->width(400)
       ->height(400)
       ->fit(ImageVariant::FIT_COVER)
       ->metaData(ImageVariant::METADATA_NONE);
$variant->validate(); // Will throw if incomplete
```

### Migration Steps

1. **Update PHP version** to 8.1 or higher
2. **Upgrade Laravel** to version 10, 11, or 12
3. **Update composer.json**:

```json
{
    "require": {
        "alexbuckham/cloudflare-images-laravel": "^3.0"
    }
}
```

4. **Run composer update**:

```bash
composer update
```

5. **Update your code** to handle the breaking changes listed above

6. **Test thoroughly** to ensure everything works as expected

7. **Publish the new configuration**:

```bash
php artisan vendor:publish --tag="cloudflare-images-config" --force
```

### Configuration Changes

The configuration file has been enhanced with better documentation and new options:

```php
// New in 3.x
'defaults' => [
    'require_signed_urls' => env('CF_IMAGES_DEFAULT_PRIVATE', false),
    'metadata' => env('CF_IMAGES_DEFAULT_METADATA', 'none'),
],

'http_options' => [
    'timeout' => env('CF_IMAGES_HTTP_TIMEOUT', 30),
    'connect_timeout' => env('CF_IMAGES_HTTP_CONNECT_TIMEOUT', 10),
],
```

Add these to your `.env` file if you want to customize them:

```env
CF_IMAGES_DEFAULT_PRIVATE=false
CF_IMAGES_DEFAULT_METADATA=none
CF_IMAGES_HTTP_TIMEOUT=30
CF_IMAGES_HTTP_CONNECT_TIMEOUT=10
```

### Testing Your Upgrade

After upgrading, make sure to test:

1. **Image uploads** from both file paths and UploadedFile objects
2. **Variant creation** with proper validation
3. **Direct upload URL generation**
4. **Signed URL generation** for private images
5. **Error handling** to ensure exceptions are caught properly

### Getting Help

If you encounter issues during the upgrade:

1. Check the [CHANGELOG](CHANGELOG.md) for detailed changes
2. Review the updated [README](README.md) for current usage examples
3. Look at the [examples](examples/) directory for implementation patterns
4. Open an issue on the GitHub repository if you need assistance

### Rollback Plan

If you need to rollback to a previous version:

```bash
# Rollback to 2.x
composer require "alexbuckham/cloudflare-images-laravel:^2.0"

# Rollback to 1.x (if compatible with your Laravel version)
composer require "alexbuckham/cloudflare-images-laravel:^1.0"
```

Note that rolling back to 1.x will only work if you're still on Laravel 8.x.