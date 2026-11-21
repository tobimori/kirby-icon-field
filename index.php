<?php

use Kirby\Cms\App;

// shamelessly borrowed from distantnative/retour-for-kirby
if (
	version_compare(App::version() ?? '0.0.0', '4.0.1', '<') === true ||
	version_compare(App::version() ?? '0.0.0', '6.0.0', '>=') === true
) {
	throw new Exception('Kirby Icon Field requires Kirby v4 or Kirby v5');
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
	'fields' => require __DIR__ . '/config/fields.php'
]);
