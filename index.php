<?php

use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

App::plugin('tobimori/icon-field', [
  'options' => [
    'folder' => null,
    'cache' => true
  ],
  'fields' => [
    'icon' => [
      'extends' => 'tags',
      'props' => [
        /**
         * Unset inherited props
         */
        'accept' => null,
        /**
         * Custom icon to replace the arrow down.
         */
        'icon' => function (string $icon = null) {
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

        'folder' => function ($folder = null) {
          if (!$folder) {
            return option('tobimori.icon-field.folder', realpath(kirby()->roots()->index() . '/assets/icons'));
          }

          if (!Str::startsWith($folder, '/')) {
            $folder = realpath(kirby()->roots()->index() . '/' . $folder);
          }

          return $folder;
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
        'options' => function () {
          $folder = $this->folder();

          if (($cache = kirby()->cache('tobimori.icon-field'))->get($folder)) {
            return $cache->get($folder);
          }

          $dir = array_values(A::filter(A::map(
            A::filter(Dir::read($folder), fn ($file) => Str::endsWith($file, 'svg', true)),
            fn ($file) => [
              'text' => Str::before($file, '.svg'),
              'value' => $file,
              'svg' => F::read($folder . '/' . $file)
            ]
          ), fn ($file) => $file['svg']));

          $cache->set($folder, $dir);

          return $dir;
        }
      ]
    ]
  ]
]);
