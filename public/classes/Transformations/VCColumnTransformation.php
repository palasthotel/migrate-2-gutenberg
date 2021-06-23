<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

class VCColumnTransformation implements ShortcodeTransformation {

	function tag(): string {
		return "vc_column";
	}

	function transform($attrs, $content = ""): string {

		return "<!-- wp:column -->\n<div class=\"wp-block-column\">$content</div>\n<!-- /wp:column -->";
	}
}