<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

class VCColumnTextTransformation implements ShortcodeTransformation {

	function tag(): string {
		return "vc_column_text";
	}

	function transform($attrs, $content = ""): string {

		if(empty($content)) return "";

		return "<!-- wp:paragraph -->\n<p>$content</p>\n<!-- /wp:paragraph -->\n\n";
	}
}