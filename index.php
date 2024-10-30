<?php

use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Sane\Svg;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

// shamelessly borrowed from distantnative/retour-for-kirby
if (
	version_compare(App::version() ?? '0.0.0', '4.0.1', '<') === true ||
	version_compare(App::version() ?? '0.0.0', '5.0.0', '>') === true
) {
	throw new Exception('Kirby Icon Field requires Kirby v4.0.1');
}

App::plugin('tobimori/icon-field', [
	'options' => [
		'folder' => null,
		'sprite' => null,
		'include' => [],
		'exclude' => [],
		'includeExtension' => true,
		'cache' => true
	],
	'fields' => [
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
				'search' => function ($search = true) {
					return $search;
				},

				// Folder option
				'folder' => function ($folder = null) {
					if (!$folder) {
						$folder = option('tobimori.icon-field.folder', realpath(kirby()->roots()->index() . '/assets/icons'));
					}

					if (is_callable($folder)) {
						$folder = $folder($this);
					}

					$folder = Str::template($folder, [
						'kirby' => ($kirby = kirby()),
						'site' => ($site = $kirby->site()),
						'page' => $site->page(),
					]);

					if (!Str::startsWith($folder, '/')) {
						$folder = realpath(kirby()->roots()->index() . '/' . $folder);
					}

					return $folder;
				},

				// Sprite option
				'sprite' => function ($sprite = null) {
					if (!$sprite) {
						$sprite = option('tobimori.icon-field.sprite');
					}

					if (is_callable($sprite)) {
						$sprite = $sprite($this);
					}

					$sprite = Str::template($sprite, [
						'kirby' => ($kirby = kirby()),
						'site' => ($site = $kirby->site()),
						'page' => $site->page(),
					]);

					if (Str::endsWith($sprite, '.svg') === false) {
						$sprite .= '.svg';
					}

					if (F::exists($this->folder() . '/' . $sprite) === false) {
						$sprite = null;
					}

					return $sprite;
				},

				'include' => function ($include = []) {
					if (is_callable($include)) {
						$include = $include($this);
					}

					if (count($include) === 0) {
						$include = option('tobimori.icon-field.include', []);
					}

					return $include;
				},

				'exclude' => function ($exclude = []) {
					if (is_callable($exclude)) {
						$exclude = $exclude($this);
					}

					if (count($exclude) === 0) {
						$exclude = option('tobimori.icon-field.exclude', []);
					}

					return $exclude;
				},
			],
			'methods' => [
				'toValues' => function ($value) {
					if (is_null($value) === true) {
						return [];
					}

					if (is_array($value) === false) {
						$value = Str::split($value, $this->separator());
					}

					return $this->sanitizeOptions($value);
				}
			],
			'computed' => [
				// Final options for the field
				'options' => function () {
					$kirby = kirby();
					$data = [];
					$hash = md5(serialize(A::get($this->props(), ['folder', 'sprite', 'include', 'exclude'])));

					// Return the cached data if available
					if ($cache = $kirby->cache('tobimori.icon-field')->get($hash)) {
						return $cache;
					}

					$folder = $this->folder();
					$sprite = $this->sprite();
					if (!$sprite) {
						$data = array_values(A::filter(A::map(
							A::filter(Dir::read($folder), fn($file) => Str::endsWith($file, 'svg', true)),
							fn($file) => [
								'text' => $kirby->option('tobimori.icon-field.includeExtension') ? $file : Str::before($file, '.'),
								'value' => $file,
								'svg' => Svg::sanitize(F::read($folder . '/' . $file))
							]
						), fn($file) => $file['svg']));
					} else {
						$svg = Svg::sanitize(F::read($folder . '/' . $sprite));
						$svg = simplexml_load_string($svg);
						$svg->registerXPathNamespace('s', 'http://www.w3.org/2000/svg');

						$symbols = $svg->xpath('//s:symbol');

						// Try to find the sprite url
						$relative = Str::after($this->folder(), $kirby->roots()->index() . '/');
						$url = A::join(A::filter([Str::trim($kirby->url(), '/'), $relative, $sprite], fn($e) => !!$e), '/');

						// Map symbols to options
						$data = A::map($symbols, function ($symbol) use ($url) {
							$id = (string)$symbol['id'];

							// Generate SVG that consumes the icon
							$useSvg = new SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg"></svg>');
							$use = $useSvg->addChild('use');
							$use->addAttribute('xlink:href', "{$url}#{$id}", 'http://www.w3.org/1999/xlink');

							return [
								'text' => $id,
								'value' => $id,
								'svg' => $useSvg->asXML()
							];
						});
					}

					if (count($this->include()) > 0) {
						$data = A::filter($data, function ($item) {
							return in_array(Str::replace($item['value'], '.svg', ''), $this->include());
						});
					}

					if (count($this->exclude()) > 0) {
						$data = A::filter($data, function ($item) {
							return !in_array(Str::replace($item['value'], '.svg', ''), $this->exclude());
						});
					}

					$data = array_values($data); // reset keys

					$kirby->cache('tobimori.icon-field')->set($hash, $data);
					return $data;
				}
			]
		]
	]
]);
