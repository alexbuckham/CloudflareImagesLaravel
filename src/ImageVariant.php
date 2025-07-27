<?php

namespace AlexBuckham\CloudflareImagesLaravel;

class ImageVariant
{
	public string $id;
	public bool $alwaysPublic = false;
	public ?int $width = null;
	public ?int $height = null;
	public ?int $blur = null;
	public ?string $metaData = null;
	public ?string $fit = null;

	/**
	 * Valid metadata options
	 */
	public const METADATA_KEEP = 'keep';
	public const METADATA_COPYRIGHT = 'copyright';
	public const METADATA_NONE = 'none';

	/**
	 * Valid fit options
	 */
	public const FIT_SCALE_DOWN = 'scale-down';
	public const FIT_CONTAIN = 'contain';
	public const FIT_COVER = 'cover';
	public const FIT_CROP = 'crop';
	public const FIT_PAD = 'pad';

	/**
	 * ImageVariant constructor.
	 *
	 * @param string $id
	 */
	public function __construct(string $id)
	{
		$this->id = $id;
	}

	/**
	 * Validate the variant configuration.
	 *
	 * @throws \Exception
	 */
	public function validate(): void
	{
		if (is_null($this->width)) {
			throw new \Exception('Width is required');
		}

		if (is_null($this->height)) {
			throw new \Exception('Height is required');
		}

		if (is_null($this->metaData)) {
			throw new \Exception('Metadata is required');
		}

		$validMetadata = [self::METADATA_KEEP, self::METADATA_COPYRIGHT, self::METADATA_NONE];
		if (!in_array($this->metaData, $validMetadata)) {
			throw new \Exception('Metadata value must be one of: ' . implode(', ', $validMetadata));
		}

		if (is_null($this->fit)) {
			throw new \Exception('Fit is required');
		}

		$validFit = [self::FIT_SCALE_DOWN, self::FIT_CONTAIN, self::FIT_COVER, self::FIT_CROP, self::FIT_PAD];
		if (!in_array($this->fit, $validFit)) {
			throw new \Exception('Fit value must be one of: ' . implode(', ', $validFit));
		}
	}

	/**
	 * Get the variant options for API submission.
	 *
	 * @return array
	 */
	public function getOptions(): array
	{
		$options = [
			'width' => $this->width,
			'height' => $this->height,
			'metadata' => $this->metaData,
			'fit' => $this->fit,
		];

		if ($this->blur !== null) {
			$options['blur'] = $this->blur;
		}

		return $options;
	}

	/**
	 * @param string $id
	 * @return ImageVariant
	 */
	public function setId(string $id): ImageVariant
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @param bool $alwaysPublic
	 * @return ImageVariant
	 */
	public function alwaysPublic(bool $alwaysPublic): ImageVariant
	{
		$this->alwaysPublic = $alwaysPublic;

		return $this;
	}

	/**
	 * @param int|null $width
	 * @return ImageVariant
	 */
	public function width(?int $width): ImageVariant
	{
		$this->width = $width;

		return $this;
	}

	/**
	 * @param int|null $height
	 * @return ImageVariant
	 */
	public function height(?int $height): ImageVariant
	{
		$this->height = $height;

		return $this;
	}

	/**
	 * @param int|null $blur
	 * @return ImageVariant
	 */
	public function blur(?int $blur): ImageVariant
	{
		if ($blur !== null && ($blur < 0 || $blur > 100)) {
			throw new \Exception('Blur must be between 0 and 100');
		}

		$this->blur = $blur;

		return $this;
	}

	/**
	 * @param string|null $metaData
	 * @return ImageVariant
	 */
	public function metaData(?string $metaData): ImageVariant
	{
		$this->metaData = $metaData;

		return $this;
	}

	/**
	 * @param string|null $fit
	 * @return ImageVariant
	 */
	public function fit(?string $fit): ImageVariant
	{
		$this->fit = $fit;

		return $this;
	}

	/**
	 * Create a variant from configuration array.
	 *
	 * @param string $id
	 * @param array $config
	 * @return static
	 */
	public static function fromConfig(string $id, array $config): self
	{
		$variant = new self($id);

		if (isset($config['width'])) {
			$variant->width($config['width']);
		}

		if (isset($config['height'])) {
			$variant->height($config['height']);
		}

		if (isset($config['fit'])) {
			$variant->fit($config['fit']);
		}

		if (isset($config['metadata'])) {
			$variant->metaData($config['metadata']);
		}

		if (isset($config['blur'])) {
			$variant->blur($config['blur']);
		}

		if (isset($config['always_public'])) {
			$variant->alwaysPublic($config['always_public']);
		}

		return $variant;
	}
}
