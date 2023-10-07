<?php

use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Sane\Svg;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

// shamelessly borrowed from distantnative/retour-for-kirby
if (
	version_compare(App::version() ?? '0.0.0', '4.0.0-beta.2', '<') === true ||
	version_compare(App::version() ?? '0.0.0', '5.0.0', '>') === true
) {
	throw new Exception('Kirby Icon Field requires Kirby 4');
}

App::plugin('tobimori/icon-field', [
	'options' => [
		'folder' => null,
		'sprite' => null,
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

					if (Str::endsWith($sprite, '.svg') === false) {
						$sprite .= '.svg';
					}

					if (F::exists($this->folder() . '/' . $sprite) === false) {
						$sprite = null;
					}

					return $sprite;
				}
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
				// Load svg sprite
				'spriteOptions' => function () {
					$folder = $this->folder();
					$sprite = $this->sprite();

					$svg = Svg::sanitize(F::read($folder . '/' . $sprite));
					$svg = simplexml_load_string($svg);
					$svg->registerXPathNamespace('s', 'http://www.w3.org/2000/svg');

					$symbols = $svg->xpath('//s:symbol');

					// Try to find the sprite url
					$relative = Str::after($this->folder(), kirby()->roots()->index() . '/');
					$url = kirby()->url() . '/' . $relative . '/' . $sprite;

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

					kirby()->cache('tobimori.icon-field')->set($folder . $sprite, $data);

					return $data;
				},

				// Load all svg files in folder
				'dirOptions' => function () {
					$folder = $this->folder();

					$dir = array_values(A::filter(A::map(
						A::filter(Dir::read($folder), fn ($file) => Str::endsWith($file, 'svg', true)),
						fn ($file) => [
							'text' => Str::before($file, '.svg'),
							'value' => $file,
							'svg' => Svg::sanitize(F::read($folder . '/' . $file))
						]
					), fn ($file) => $file['svg']));

					kirby()->cache('tobimori.icon-field')->set($folder, $dir);

					return $dir;
				},

				// Final options for the field
				'options' => function () {
					$folder = $this->folder();
					$sprite = $this->sprite();

					// Return the cached data if available
					if (($cache = kirby()->cache('tobimori.icon-field'))->get($folder . $sprite)) {
						return $cache->get($folder . $sprite);
					}

					return !$sprite ? $this->dirOptions() : $this->spriteOptions();
				}
			]
		]
	]
]);
