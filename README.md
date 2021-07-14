# Mitgrate to Gutenberg
Migrate pre Gutenberg contents to Gutenberg blocks.

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