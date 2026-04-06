<?php

namespace AlexBuckham\CloudflareImagesLaravel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;

class CloudflareImages
{
	private ?string $account_id;
	private ?string $token;
	private ?string $key;
	private ?string $delivery_url;

	/**
	 * CloudflareImages constructor.
	 *
	 * @param string|null $account_id
	 * @param string|null $token
	 * @param string|null $key
	 * @param string|null $delivery_url
	 */
	public function __construct(?string $account_id = null, ?string $token = null, ?string $key = null, ?string $delivery_url = null)
	{
		$this->account_id = $account_id ?: $this->getConfig('cloudflare-images.account_id');
		$this->token = $token ?: $this->getConfig('cloudflare-images.token');
		$this->key = $key ?: $this->getConfig('cloudflare-images.key');
		$this->delivery_url = $delivery_url ?: $this->getConfig('cloudflare-images.delivery_url');
	}

	/**
	 * Create a new image variant.
	 *
	 * @param ImageVariant $variant
	 * @return \stdClass
	 * @throws GuzzleException
	 */
	public function createVariant(ImageVariant $variant): \stdClass
	{
		$variant->validate();

		return $this->makeCall('POST', 'images/v1/variants', [
			'json' => [
				'id' => $variant->id,
				'options' => $variant->getOptions(),
				'neverRequireSignedURLs' => $variant->alwaysPublic,
			],
		]);
	}

	/**
	 * Generate a direct upload URL.
	 *
	 * @param bool $private
	 * @return \stdClass
	 * @throws GuzzleException
	 */
	public function generateUploadUrl(bool $private = false): \stdClass
	{
		return $this->makeCall('POST', 'images/v1/direct_upload', [
			'json' => [
				'requireSignedUrls' => $private,
			],
		]);
	}

	/**
	 * @param string $uuid
	 * @param string $variant
	 * @param \DateTime|null $expires_at
	 * @return string
	 * @throws \Exception
	 */
	public function getSignedUrl(string $uuid, string $variant, ?\DateTime $expires_at = null): string
	{
		if (!$this->key) {
			throw new \Exception('A key must be provided in the constructor.');
		}

		$variants = $this->getConfig('cloudflare-images.variants', []);
		if (!in_array($variant, array_keys($variants))) {
			throw new \Exception('Variant not found.');
		}

		$expiry = $expires_at ? $expires_at->getTimestamp() : now()->addDay()->timestamp;
		$account_hash = $this->getConfig('cloudflare-images.account_hash');
		$to_sign = "/{$account_hash}/{$uuid}/{$variant}?exp=$expiry";

		$signature = hash_hmac('sha256', $to_sign, $this->key);

		$custom_domain = $this->getConfig('cloudflare-images.custom_domain');
		$base_url = $custom_domain ? $custom_domain . '/cdn-cgi/imagedelivery' : 'imagedelivery.net';

		return 'https://' . $base_url . $to_sign . "&sig=$signature";
	}

	/**
	 * @param $file
	 * @param bool $private
	 * @return \stdClass
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function upload($file, $filename = null, bool $private = false): \stdClass
	{
		$guzzle = new Client([
			'headers' => [
				'Authorization' => 'Bearer ' . $this->token,
				'Content-Type'  => 'multipart/form-data',
			],
		]);

		$url = sprintf('https://api.cloudflare.com/client/v4/accounts/%s/%s', $this->account_id, 'images/v1');

		$file_payload = [
			'filename' => $filename,
			'name'     => 'file',
			'contents' => $file,
		];

		$payload = [
			$file_payload, [
				'name'     => 'requireSignedURLs',
				'contents' => $private ? 'true' : 'false',
			],
		];

		$response = $guzzle->request('POST', $url, [
			'multipart' => $payload,
		]);

		return json_decode($response->getBody()->getContents())->result;
	}

	/**
	 * @param $file
	 * @param bool $private
	 * @return \stdClass
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function uploadFromRequest(UploadedFile $file, bool $private = false): \stdClass
	{
		return $this->upload(file_get_contents($file), $file->getClientOriginalName(), $private);
	}

	/**
	 * Make an API call to Cloudflare.
	 *
	 * @param string $method
	 * @param string $url
	 * @param array $data
	 * @return \stdClass
	 * @throws GuzzleException
	 * @throws \Exception
	 */
	private function makeCall(string $method, string $url, array $data = []): \stdClass
	{
		if (!$this->account_id || !$this->token) {
			throw new \Exception('Account ID and token are required for API calls.');
		}

		$guzzle = new Client([
			'headers' => [
				'Authorization' => 'Bearer ' . $this->token,
				'Content-Type' => 'application/json',
			],
			'timeout' => $this->getConfig('cloudflare-images.http_options.timeout', 30),
			'connect_timeout' => $this->getConfig('cloudflare-images.http_options.connect_timeout', 10),
		]);

		$full_url = sprintf('https://api.cloudflare.com/client/v4/accounts/%s/%s', $this->account_id, $url);

		$response = $guzzle->request($method, $full_url, $data);
		$body = json_decode($response->getBody()->getContents());

		if (!$body->success) {
			throw new \Exception('API call failed: ' . ($body->errors[0]->message ?? 'Unknown error'));
		}

		return $body->result;
	}

	/**
	 * Get configuration value with fallback for non-Laravel context.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	private function getConfig(string $key, $default = null)
	{
		if (function_exists('config')) {
			return config($key, $default);
		}

		return $default;
	}

}
