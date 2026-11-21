<?php

use Kirby\Exception\InvalidArgumentException;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Form\Field;
use Kirby\Sane\Svg;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

return [
	'icon' => [
		'extends' => 'tags',
		'props' => [
			// Unset inherited props
			'accept' => 'options',

			// Custom icon
			'icon' => function (string|bool $icon = false) {
				return $icon;
			},
			/**
			 * Enable/disable the search in the dropdown
			 * Also limit displayed items (display: 20)
			 * and set minimum number of characters to search (min: 3)
			 */
			'search' => function (array|bool $search = true) {
				return $search;
			},

			// Folder option
			'folder' => function (string|null $folder = null) {
				/** @var Field $this */
				$kirby = kirby();
				$index = (string)$kirby->root('index');
				$folder = $folder ?: $kirby->option('tobimori.icon-field.folder', realpath($index . '/assets/icons'));

				if (is_callable($folder)) {
					$folder = $folder($this);
				}

				$folder = Str::template((string)$folder, [
					'kirby' => $kirby,
					'site' => $site = $kirby->site(),
					'page' => $site->page()
				]);

				if (!Str::startsWith($folder, '/')) {
					$folder = realpath($index . '/' . $folder);
				}

				return $folder;
			},

			// Sprite option
			'sprite' => function (string|null $sprite = null) {
				/** @var Field $this */
				$kirby = kirby();
				$sprite = $sprite ?: $kirby->option('tobimori.icon-field.sprite');

				if (is_callable($sprite)) {
					$sprite = $sprite($this);
				}

				$sprite = Str::template((string)$sprite, [
					'kirby' => $kirby,
					'site' => $site = $kirby->site(),
					'page' => $site->page()
				]);

				if (Str::endsWith($sprite, '.svg') === false) {
					$sprite .= '.svg';
				}

				if (F::exists((string)$this->folder() . '/' . $sprite) === false) {
					return null;
				}

				return $sprite;
			},

			'include' => function (array|callable $include = []) {
				/** @var Field $this */
				if (is_callable($include)) {
					$include = $include($this);
				}

				if ($include === []) {
					$include = kirby()->option('tobimori.icon-field.include', []);
				}

				return $include;
			},

			'exclude' => function (array|callable $exclude = []) {
				/** @var Field $this */
				if (is_callable($exclude)) {
					$exclude = $exclude($this);
				}

				if ($exclude === []) {
					$exclude = kirby()->option('tobimori.icon-field.exclude', []);
				}

				return $exclude;
			}
		],
		'methods' => [
			'toValues' => function (array|string|null $value) {
				/** @var Field $this */
				if ($value === null) {
					return [];
				}

				if (is_string($value)) {
					$value = Str::split($value, $this->separator());
				}

				return $this->sanitizeOptions($value);
			}
		],
		'computed' => [
			// Final options for the field
			'options' => function () {
				/** @var Field $this */
				$kirby = kirby();
				$cache = $kirby->cache('tobimori.icon-field');
				$folder = (string)$this->folder();
				$sprite = $this->sprite();
				$hash = md5(serialize([
					A::get($this->props(), ['folder', 'sprite', 'include', 'exclude']),
					is_dir($folder) ? Dir::modified($folder) : null
				]));

				// Return the cached data if available
				if ($data = $cache->get($hash)) {
					return $data;
				}

				$sanitize = function (string $file): string {
					$svg = (string)F::read($file);

					try {
						return $svg !== '' ? Svg::sanitize($svg) : '';
					} catch (InvalidArgumentException) {
						return '';
					}
				};

				if (!$sprite) {
					$files = A::filter(Dir::read($folder), fn (string $file) => Str::endsWith($file, 'svg', true));
					$data = A::map($files, fn (string $file) => [
						'text' => $kirby->option('tobimori.icon-field.includeExtension') ? $file : Str::before($file, '.'),
						'value' => $file,
						'svg' => $sanitize($folder . '/' . $file)
					]);
					$data = A::filter($data, fn (array $icon) => $icon['svg'] !== '');
				} else {
					$markup = $sanitize($folder . '/' . $sprite);
					$svg = $markup !== '' ? simplexml_load_string($markup) : false;
					$symbols = [];

					if ($svg !== false) {
						$svg->registerXPathNamespace('s', 'http://www.w3.org/2000/svg');
						$symbols = $svg->xpath('//s:symbol') ?: [];
					}

					// Try to find the sprite url
					$relative = Str::after($folder, (string)$kirby->root('index') . '/');
					$url = A::join(array_filter([Str::trim((string)$kirby->url(), '/'), $relative, $sprite]), '/');

					// Map symbols to options
					$data = A::map($symbols, function (SimpleXMLElement $symbol) use ($url) {
						$id = (string)$symbol['id'];

						// Generate SVG that consumes the icon
						$useSvg = new SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg"></svg>');
						$useSvg->addChild('use')?->addAttribute('xlink:href', "{$url}#{$id}", 'http://www.w3.org/1999/xlink');

						return [
							'text' => $id,
							'value' => $id,
							'svg' => $useSvg->asXML()
						];
					});
				}

				$include = $this->include();
				if ($include !== []) {
					$data = A::filter($data, fn (array $icon) => in_array(Str::replace((string)$icon['value'], '.svg', ''), $include, true));
				}

				$exclude = $this->exclude();
				if ($exclude !== []) {
					$data = A::filter($data, fn (array $icon) => !in_array(Str::replace((string)$icon['value'], '.svg', ''), $exclude, true));
				}

				$data = array_values($data);
				$cache->set($hash, $data);

				return $data;
			}
		]
	]
];
