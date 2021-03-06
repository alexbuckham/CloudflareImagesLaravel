# CloudflareImagesLaravel
Provides access to Cloudflare Images service for Laravel.

[![Latest Stable Version](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/v)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![Total Downloads](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/downloads)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![Latest Unstable Version](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/v/unstable)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![License](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/license)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![PHP Version Require](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/require/php)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel)

## Table of contents

* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)

## Installation

To get the latest version of `CloudflareImagesLaravel`, simply require the project using [Composer](https://getcomposer.org):

```
composer install alexbuckham/cloudflare-images-laravel
```

Or manually update the `require` block of `composer.json` and run `composer update`.

```json
{
    "require": {
        "alexbuckham/cloudflare-images-laravel": "^0.0.1"
    }
}
```

## Configuration
Set environment variables:

- `CF_IMAGES_ACCOUNT_ID` - Cloudflare account ID
- `CF_IMAGES_CF_IMAGES_TOKEN` - Cloudflare API token
- `CF_IMAGES_KEY` - Create a CF images key under the Images section of your Cloudflare account
- `CF_IMAGES_DELIVERY_URL` - Copy the images delivery base url from the Cloudflare images dashboard

## Usage
Create a variant

```php
use AlexBuckham\CloudflareImagesLaravel\CloudflareImages;
use AlexBuckham\CloudflareImagesLaravel\ImageVariant;

$variant = new ImageVariant('tiny');
$variant->fit('contain')
    ->width(50)
	->height(50)
	->metaData('keep');
	
$cfImages = new CloudflareImages();
$cfImages->createVariant($variant);
```

Upload an image
```php
use AlexBuckham\CloudflareImagesLaravel\CloudflareImages;

$cfImages = new CloudflareImages();
// Pass either a file path or a file resource as the first parameter.
// If you want the image to be private (always require signed urls), pass true as the second parameter.
$cfImages->upload('/path/to/image.jpg', true);
```

Generate a signed URL
```php
use AlexBuckham\CloudflareImagesLaravel\CloudflareImages;

$cfImages = new CloudflareImages();
$cfImages->getSignedUrl('image-uuid', new DateTime('+1 day'));
```

Overriding configuration

You can override the environment variables by passing new properties to the `CloudflareImages` constructor.
```php
use AlexBuckham\CloudflareImagesLaravel\CloudflareImages;

$cfImages = new CloudflareImages('CF_IMAGES_ACCOUNT_ID', 'CF_IMAGES_CF_IMAGES_TOKEN', 'CF_IMAGES_KEY', 'CF_IMAGES_DELIVERY_URL');
```
