<?php

namespace AlexBuckham\CloudflareImagesLaravel\Tests;

use AlexBuckham\CloudflareImagesLaravel\CloudflareImages;
use AlexBuckham\CloudflareImagesLaravel\ImageVariant;
use Orchestra\Testbench\TestCase;

class CloudflareImagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            \AlexBuckham\CloudflareImagesLaravel\CloudflareImagesProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cloudflare-images.account_id', 'test-account-id');
        $app['config']->set('cloudflare-images.token', 'test-token');
        $app['config']->set('cloudflare-images.key', 'test-key');
        $app['config']->set('cloudflare-images.delivery_url', 'https://imagedelivery.net/test-hash');
    }

    public function test_service_provider_registers_service()
    {
        $service = $this->app->make(CloudflareImages::class);

        $this->assertInstanceOf(CloudflareImages::class, $service);
    }

    public function test_can_create_cloudflare_images_instance()
    {
        $images = new CloudflareImages();

        $this->assertInstanceOf(CloudflareImages::class, $images);
    }

    public function test_can_create_image_variant()
    {
        $variant = new ImageVariant('test-variant');
        $variant->width(400)
               ->height(400)
               ->fit(ImageVariant::FIT_COVER)
               ->metaData(ImageVariant::METADATA_NONE);

        $this->assertEquals('test-variant', $variant->id);
        $this->assertEquals(400, $variant->width);
        $this->assertEquals(400, $variant->height);
        $this->assertEquals('cover', $variant->fit);
        $this->assertEquals('none', $variant->metaData);
    }

    public function test_image_variant_validation_requires_width()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Width is required');

        $variant = new ImageVariant('test');
        $variant->height(400)
               ->fit(ImageVariant::FIT_COVER)
               ->metaData(ImageVariant::METADATA_NONE)
               ->validate();
    }

    public function test_image_variant_validation_requires_height()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Height is required');

        $variant = new ImageVariant('test');
        $variant->width(400)
               ->fit(ImageVariant::FIT_COVER)
               ->metaData(ImageVariant::METADATA_NONE)
               ->validate();
    }

    public function test_image_variant_validation_requires_metadata()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Metadata is required');

        $variant = new ImageVariant('test');
        $variant->width(400)
               ->height(400)
               ->fit(ImageVariant::FIT_COVER)
               ->validate();
    }

    public function test_image_variant_validation_requires_fit()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Fit is required');

        $variant = new ImageVariant('test');
        $variant->width(400)
               ->height(400)
               ->metaData(ImageVariant::METADATA_NONE)
               ->validate();
    }

    public function test_image_variant_validates_metadata_values()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Metadata value must be one of: keep, copyright, none');

        $variant = new ImageVariant('test');
        $variant->width(400)
               ->height(400)
               ->fit(ImageVariant::FIT_COVER)
               ->metaData('invalid')
               ->validate();
    }

    public function test_image_variant_validates_fit_values()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Fit value must be one of: scale-down, contain, cover, crop, pad');

        $variant = new ImageVariant('test');
        $variant->width(400)
               ->height(400)
               ->fit('invalid')
               ->metaData(ImageVariant::METADATA_NONE)
               ->validate();
    }

    public function test_image_variant_validates_blur_range()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Blur must be between 0 and 100');

        $variant = new ImageVariant('test');
        $variant->blur(150);
    }

    public function test_image_variant_from_config()
    {
        $config = [
            'width' => 800,
            'height' => 600,
            'fit' => ImageVariant::FIT_SCALE_DOWN,
            'metadata' => ImageVariant::METADATA_COPYRIGHT,
            'blur' => 5,
            'always_public' => true,
        ];

        $variant = ImageVariant::fromConfig('config-variant', $config);

        $this->assertEquals('config-variant', $variant->id);
        $this->assertEquals(800, $variant->width);
        $this->assertEquals(600, $variant->height);
        $this->assertEquals('scale-down', $variant->fit);
        $this->assertEquals('copyright', $variant->metaData);
        $this->assertEquals(5, $variant->blur);
        $this->assertTrue($variant->alwaysPublic);
    }

    public function test_get_signed_url_requires_key()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A key must be provided in the constructor.');

        // Override config to ensure null key
        config(['cloudflare-images.key' => null]);

        $images = new CloudflareImages('account', 'token', null, 'https://imagedelivery.net/test-hash');
        $images->getSignedUrl('test-uuid', new \DateTime('+1 day'));
    }

    public function test_get_signed_url_generates_correct_format()
    {
        $images = new CloudflareImages(
            'test-account',
            'test-token',
            'test-key',
            'https://imagedelivery.net/test-hash'
        );

        $expiry = new \DateTime('+1 day');
        $url = $images->getSignedUrl('test-uuid', $expiry);

        $this->assertStringContainsString('https://imagedelivery.net/test-hash/test-uuid', $url);
        $this->assertStringContainsString('exp=' . $expiry->getTimestamp(), $url);
        $this->assertStringContainsString('sig=', $url);
    }

    public function test_config_is_loaded()
    {
        $this->assertEquals('test-account-id', config('cloudflare-images.account_id'));
        $this->assertEquals('test-token', config('cloudflare-images.token'));
        $this->assertEquals('test-key', config('cloudflare-images.key'));
        $this->assertEquals('https://imagedelivery.net/test-hash', config('cloudflare-images.delivery_url'));
    }
}
