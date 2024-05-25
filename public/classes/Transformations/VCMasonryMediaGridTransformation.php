<?php

namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;

use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Plugin;

class VCMasonryMediaGridTransformation implements ShortcodeTransformation {

    function tag(): string {
        return "vc_masonry_media_grid";
    }

    function transform($attrs, $content = ""): string {
        $output = "";

        // Check for necessary attributes
        if (isset($attrs['include']) && isset($attrs['element_width'])) {
            $ids = explode(',', $attrs['include']);
            $columns = $this->calculate_columns($attrs['element_width']);

            $output .= "<figure class='wp-block-gallery columns-{$columns} is-cropped'><ul class='blocks-gallery-grid'>";

            foreach ($ids as $id) {
                $img_src = wp_get_attachment_image_src($id, 'full');
                if (isset($img_src[0])) {
                    $img_url = $img_src[0];
                    $img_alt = get_post_meta($id, '_wp_attachment_image_alt', true);
                    $img_link = get_attachment_link($id);
                    $img_caption = wp_get_attachment_caption($id);

                    $output .= "<li class='blocks-gallery-item'><figure><img src='{$img_url}' alt='{$img_alt}' data-id='{$id}' data-full-url='{$img_url}' data-link='{$img_link}' class='wp-image-{$id}' />";
                    if ($img_caption) {
                        $output .= "<figcaption class='blocks-gallery-item__caption'>{$img_caption}</figcaption>";
                    }
                    $output .= "</figure></li>";
                }
            }

            $output .= "</ul></figure>";
        }

        return $output;
    }

    private function calculate_columns($element_width) {
        switch ($element_width) {
            case '12':
                return 1;
            case '6':
                return 2;
            case '4':
                return 3;
            case '3':
                return 4;
            case '2':
                return 6;
            default:
                return 2; // Default to 3 columns if an unknown value is provided
        }
    }
}
