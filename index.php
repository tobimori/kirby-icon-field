<?php

use Kirby\Cms\App;
use tobimori\IconField\IconField;

if (
	version_compare(App::version() ?? '0.0.0', '6.0.0-rc.3', '<') === true ||
	version_compare(App::version() ?? '0.0.0', '7.0.0-alpha.1', '>=') === true
) {
	throw new Exception('Kirby Icon Field requires Kirby 6');
}

load([
	IconField::class => 'classes/IconField.php'
], __DIR__);

App::plugin(
	'tobimori/icon-field',
	extends: [
		'options' => [
			'folder' => null,
			'sprite' => null,
			'include' => [],
			'exclude' => [],
			'includeExtension' => true,
			'cache' => true
		],
		'fields' => [
			'icon' => IconField::class
		]
	]
);
