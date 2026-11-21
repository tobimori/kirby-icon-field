<?php

namespace tobimori\IconField;

use Closure;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Form\Field\MultiselectField;
use Kirby\Reflection\Attributes\Prop;
use Kirby\Sane\Svg;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use SimpleXMLElement;

class IconField extends MultiselectField
{
	/**
	 * Folder with the SVG icons, relative to the index root.
	 * Supports query templates and closures in the config.
	 */
	#[Prop(export: false)]
	protected Closure|string|null $folder = null;

	/**
	 * SVG sprite file in the folder, each symbol becomes an icon.
	 * Supports query templates and closures in the config.
	 */
	#[Prop(export: false)]
	protected Closure|string|null $sprite = null;

	/**
	 * Names of the icons to show, without the `.svg` extension
	 */
	#[Prop(export: false)]
	protected Closure|array $include = [];

	/**
	 * Names of the icons to hide, without the `.svg` extension
	 */
	#[Prop(export: false)]
	protected Closure|array $exclude = [];

	protected string|null $icon = null;

	/**
	 * @var list<array{text: string, value: string, svg: string}>|null
	 */
	protected array|null $icons = null;

	public function config(): array
	{
		$kirby = $this->kirby();

		return [
			'folder' => $kirby->option('tobimori.icon-field.folder'),
			'sprite' => $kirby->option('tobimori.icon-field.sprite'),
			'include' => $kirby->option('tobimori.icon-field.include'),
			'exclude' => $kirby->option('tobimori.icon-field.exclude')
		];
	}

	public function exclude(): array
	{
		$exclude = $this->exclude instanceof Closure ? ($this->exclude)($this) : $this->exclude;

		return is_array($exclude) ? $exclude : [];
	}

	/**
	 * Absolute path of the icon folder, `null` if it does not exist
	 */
	public function folder(): string|null
	{
		$folder = $this->folder instanceof Closure ? ($this->folder)($this) : $this->folder;
		$folder = $this->stringTemplate(is_string($folder) && $folder !== '' ? $folder : 'assets/icons', safe: false);

		if (Str::startsWith($folder, '/') === false) {
			$folder = realpath((string)$this->kirby()->root('index') . '/' . $folder);
		}

		return is_string($folder) && is_dir($folder) ? $folder : null;
	}

	public function include(): array
	{
		$include = $this->include instanceof Closure ? ($this->include)($this) : $this->include;

		return is_array($include) ? $include : [];
	}

	/**
	 * @return list<array{text: string, value: string, svg: string}>
	 */
	public function options(): array
	{
		return $this->icons ??= $this->loadIcons();
	}

	/**
	 * File name of the sprite, `null` if the field does not use a sprite
	 */
	public function sprite(): string|null
	{
		$sprite = $this->sprite instanceof Closure ? ($this->sprite)($this) : $this->sprite;
		$folder = $this->folder();

		if (!is_string($sprite) || $sprite === '' || $folder === null) {
			return null;
		}

		$sprite = $this->stringTemplate($sprite, safe: false);

		if (Str::endsWith($sprite, '.svg') === false) {
			$sprite .= '.svg';
		}

		return F::exists($folder . '/' . $sprite) ? $sprite : null;
	}

	/**
	 * @return list<array{text: string, value: string, svg: string}>
	 */
	protected function loadIcons(): array
	{
		$kirby = $this->kirby();
		$folder = $this->folder();

		if ($folder === null) {
			return [];
		}

		$sprite = $this->sprite();
		$include = $this->include();
		$exclude = $this->exclude();
		$extension = $kirby->option('tobimori.icon-field.includeExtension', true) !== false;

		$cache = $kirby->cache('tobimori.icon-field');
		$hash = md5(serialize([$folder, $sprite, $include, $exclude, $extension, Dir::modified($folder)]));
		$icons = $cache->get($hash);

		if (is_array($icons)) {
			/** @var list<array{text: string, value: string, svg: string}> $icons */
			return $icons;
		}

		$icons = $sprite === null ? $this->iconsFromFiles($folder, $extension) : $this->iconsFromSprite($folder, $sprite);
		$icons = array_values(A::filter($icons, static function (array $icon) use ($include, $exclude) {
			$name = Str::replace($icon['value'], '.svg', '');

			return ($include === [] || in_array($name, $include, true)) && !in_array($name, $exclude, true);
		}));

		$cache->set($hash, $icons);

		return $icons;
	}

	/**
	 * @return list<array{text: string, value: string, svg: string}>
	 */
	protected function iconsFromFiles(string $folder, bool $extension): array
	{
		$icons = [];

		foreach (Dir::read($folder) as $file) {
			if (Str::endsWith($file, '.svg', true) === false) {
				continue;
			}

			$svg = $this->sanitize($folder . '/' . $file);

			if ($svg !== '') {
				$icons[] = ['text' => $extension ? $file : Str::before($file, '.'), 'value' => $file, 'svg' => $svg];
			}
		}

		return $icons;
	}

	/**
	 * @return list<array{text: string, value: string, svg: string}>
	 */
	protected function iconsFromSprite(string $folder, string $sprite): array
	{
		$markup = $this->sanitize($folder . '/' . $sprite);
		$xml = $markup !== '' ? simplexml_load_string($markup) : false;

		if ($xml === false) {
			return [];
		}

		$xml->registerXPathNamespace('s', 'http://www.w3.org/2000/svg');
		$kirby = $this->kirby();
		$relative = Str::after($folder, (string)$kirby->root('index') . '/');
		$url = A::join(array_filter([Str::trim((string)$kirby->url(), '/'), $relative, $sprite]), '/');
		$icons = [];

		foreach ($xml->xpath('//s:symbol') ?: [] as $symbol) {
			$id = (string)$symbol['id'];
			$svg = new SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg"></svg>');
			$svg->addChild('use')?->addAttribute('xlink:href', "{$url}#{$id}", 'http://www.w3.org/1999/xlink');
			$icons[] = ['text' => $id, 'value' => $id, 'svg' => (string)$svg->asXML()];
		}

		return $icons;
	}

	/**
	 * Sanitized markup of an SVG file, an empty string if it cannot be parsed
	 */
	protected function sanitize(string $file): string
	{
		$svg = (string)F::read($file);

		try {
			return $svg !== '' ? Svg::sanitize($svg) : '';
		} catch (InvalidArgumentException) {
			return '';
		}
	}
}
