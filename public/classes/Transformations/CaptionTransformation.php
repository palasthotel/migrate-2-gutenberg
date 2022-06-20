<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Plugin;

class CaptionTransformation implements ShortcodeTransformation {

	function tag(): string {
		return "caption";
	}

	function transform($attrs, $content = ""): string {

		if(empty($content) || empty($attrs["id"])) return "";

		$id = intval(str_replace("attachment_", "", $attrs["id"]));
		$id = apply_filters(Plugin::FILTER_ATTACHMENT_ID, $id);
		$json = [
			"id" => $id,
			"sizeSlug" => "full",
			"linkDestination" => "none",
		];
		$attrJson = json_encode($json);

		$closeTagPos = strpos($content, "/>");
		$caption = trim(substr($content, $closeTagPos+2));

		//$attachment = wp_get_attachment_metadata($id);
		$imageUrl = wp_get_attachment_image_url($id);


		return "<!-- wp:image $attrJson -->\n".
		       "<figure class=\"wp-block-image size-full\"><img src=\"$imageUrl\" class=\"wp-image-$id\" />\n".
		       "<figcaption>$caption</figcaption>".
		       "</figure>\n".
		       "<!-- /wp:image -->\n\n";
	}
}