<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

class VCSingleImageTransformation implements ShortcodeTransformation {

	function tag(): string {
		return "vc_single_image";
	}

	function transform( $attrs, $content = "" ): string {
		$output = "";
		// Render Image here …
		if ( isset( $attrs['image'] ) && is_numeric( $attrs['image'] ) ) {
			$block_data                    = array();
			$block_data['id']              = $attrs['image'];
			$block_data['sizeSlug']        = "large";
			$block_data['linkDestination'] = "none";
			if ( isset( $attrs['img_size'] ) ) {
				$block_data['sizeSlug'] = $attrs['img_size'];
			}
			$block_json = json_encode( $block_data );
			$output     .= "<!-- wp:image " . $block_json . " -->\n";
			$img_src    = wp_get_attachment_image_src( $attrs['image'], 'full' );
			if ( isset( $img_src[0] ) ) {
				$output .= "<figure class='wp-block-image size-full attachment-full'><img src='" . $img_src[0] . "'></figure>";
			}

			if ( isset( $attrs['add_caption'] ) and $attrs['add_caption'] == "yes" ) {
				$output .= "<div class='caption'>" . wp_get_attachment_caption( $attrs['image'] ) . "</figure></div>";
			}
			$output .= "\n<!-- /wp:image -->\n\n";
		}

		return $output;

	}
}