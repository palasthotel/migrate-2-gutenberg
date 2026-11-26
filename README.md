# Migrate 2 Gutenberg

Migrate pre Gutenberg contents to Gutenberg blocks.

## Installation

The plugin is not on wordpress.org. Download `migrate-2-gutenberg.zip` from the
[latest release](https://github.com/palasthotel/migrate-2-gutenberg/releases/latest)
and upload it in Plugins → Add New → Upload Plugin.

Requirements: WordPress 5.0 or newer, PHP 8.0 or newer.

The overview is in Tools → Migrate to Gutenberg (capability `manage_options`).
Every migrated post gets a backup of its previous content, which the rollback
restores. Check a few posts with **Diff** and **Preview** before migrating
everything.

### WP-CLI

```sh
wp m2g stats                                  # affected posts per migration
wp m2g migrate                                # migrate posts not migrated yet
wp m2g migrate --scope=full                   # migrate again from the backups
wp m2g migrate --id=123 --migration=shortcodes
wp m2g rollback                               # restore every backup
wp m2g rollback --id=123
```

## Features

 - Extendable for different migration sources and destinations
 - Restore from backups
 - cli integration
 - Migration analysis: Which contents will be affected and why

## Shortcode migration

The first and until now only migration path is `ShortcodesMigration`. You can migrate any shortcode to some other content structure like a gutenberg block. 

This migration path can be used for:

 - WPBakery Page Builder


### Transformations

The transformation of a shortcode to something different is handle by classes that implement the `ShortcodeTransformation` interface. Abstract class `AbsShortcodeTransformation` or `AbsBlockXTransformation` can be extended.

#### Existing transformations

`vc_column_text` will be transformed to gutenberg paragraph block.

`vc_column`, `vc_column_inner`, `vc_row_inner`, `vc_row` will be transformed to gutenberg groups. ( ⚠️ _this is still work in progress_ )

`vc_single_image` will be transformed to gutenberg image block.


#### Example with ShortcodeTransformation

For any shortcode transformation to whatever.

```php
class MyTransformation implements \Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation {
    
    function tag() : string{
        return "my-shortcode";
    }
    function transform($attrs,string $content) : string{
         return "<!-- wp:my/shortcode /-->";
    }
}
```

#### Example with AbsShortcodeTransformation

For shortcodes that should stay shortcodes with the shortcode gutenberg block. 

```php
class MyShortcodeTransformation extends \Palasthotel\WordPress\MigrateToGutenberg\Transformations\AbsShortcodeTransformation {
    function tag() : string{
        return "my-block-shortcode";
    }
}
```

#### Example with AbsBlockXTransformation

This will append all shortcode attributes to BlockX content attributes and create a block with the give blockId.

```php
class MyBlockXTransformation extends \Palasthotel\WordPress\MigrateToGutenberg\Transformations\AbsBlockXTransformation {
    function tag() : string{
        return "my-simple-shortcode";
    }
    function blockId() : \Palasthotel\WordPress\BlockX\Model\BlockId{
         return \Palasthotel\WordPress\BlockX\Model\BlockId::build("my", "simple-block");
    }
}
```

### Add transformation to migration

Add a new transformation to shortcode migration by using the filter in 'plugins_loaded' action.

```php
add_filter(\Palasthotel\WordPress\MigrateToGutenberg\Plugin::FILTER_SHORTCODE_TRANSFORMATIONS, function($transformations){
    return array_merge(
        $transformations,
        [
            new MyTransformation(),
            new MyShortcodeTransformation(),
            new MyBlockXTransformation(),      
        ]
    );
});
```

## Development

`public/` is the plugin as it ships. The `m2g.php` in the repository root is a
development wrapper (`Migrate 2 Gutenberg - DEV`, version `X.X.X`): symlink or clone
the whole repository into `wp-content/plugins/` and activate that entry. It never
ships. There is no build step and no `vendor/`: `public/m2g.php` registers its own
PSR-4 autoloader for `classes/`.

Pull requests run `php -l` on PHP 8.0 to 8.5 and pack the plugin as a release would
(shared workflows of [palasthotel/github-workflows](https://github.com/palasthotel/github-workflows)).
To pack locally next to a checkout of that repository:

```sh
SLUG=migrate-2-gutenberg bash ../github-workflows/wp-plugin/bin/pack.sh
```

## Releases

Versions and the changelog come from [release-please](https://github.com/googleapis/release-please)
and conventional commits - see [CONTRIBUTING.md](CONTRIBUTING.md). Merging the
release PR tags `vX.Y.Z`; the tag gets `migrate-2-gutenberg.zip` attached to its
GitHub release.

## License

GPL-3.0-or-later, see [LICENSE](LICENSE). Versions up to 0.1.3 were published
with a GPLv2 header and stay available under those terms.
