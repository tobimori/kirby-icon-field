![Kirby Icon Field Banner](/.github/banner.png)

# Kirby Icon Field

A simple Icon field for Kirby 4+ - throw your Icon pack in a folder, add the field to your blueprints and you're good to go.

If you're using Kirby 3.9+, please try using [v1.0.3](https://github.com/tobimori/kirby-icon-field/releases/tag/1.0.3).

## Installation

#### Recommended: Composer

```
composer require tobimori/kirby-icon-field
```

#### Manual installation

Download and copy this repository to `/site/plugins/kirby-icon-field`, or apply this repository as Git submodule.

## Usage

This plugin relies on having your SVG icons as separate files in a folder for display in the panel - of course you're free to do whatever you want with the field's value in your templates.

Icons will always be displayed in single-color white or black.

#### Add the field to your blueprint:

```yaml
fields:
  icon:
    label: Icon
    type: icon
    folder: assets/icons # path to your icon folder, relative to the `index` kirby root (you can use the Kirby query language)
    max: 1 # max number of icons to select - 1 will look like a 'select field', none or more like a 'multiselect' field
    sprite: svg-sprite.svg # optional, path to your svg sprite relative to folder option, if you want to use a sprite instead of individual files (you can use the Kirby query language)
    # [more settings...] - same as multi-select field, e.g. disabling search, limiting icons, etc.
```

> [!NOTE]
> If you're using a sprite, make sure the file is available for the user to access at the set path. The plugin does not copy or move the file, it only references it. The `#id` reference will be stored without a `.svg` extension unlike the individual files mode.

#### Use the field value in your template

```php
<?= svg('/assets/icons/' . $page->icon()) ?>
```

## Options

| Option   | Default        | Description                                              |
| -------- | -------------- | -------------------------------------------------------- |
| `cache`  | `true`         | Enable cache for reading from icons directory            |
| `folder` | `assets/icons` | Default folder for icon field, can also be a function    |
| `sprite` | `null`         | Default file for SVG sprite file, can also be a function |

Options allow you to fine tune the behaviour of the plugin. You can set them in your `config.php` file:

```php
return [
    'tobimori.icon-field' => [
        'cache' => true,
        'folder' => 'assets/icons'
    ],
];
```

## Support

> [!NOTE]
> This plugin is provided free of charge & published under the permissive MIT License. If you use it in a commercial project, please consider to [sponsor me on GitHub](https://github.com/sponsors/tobimori) to support further development and continued maintenance of Kirby Icon Field.

## License

[MIT License](./LICENSE)
Copyright © 2023-2024 Tobias Möritz

The icons in the preview image are part of [Chunk Icons](https://www.figma.com/community/file/889863427421594653/Chunk-Icons) by [Noah Jacobus](https://twitter.com/Jabronus). <3
