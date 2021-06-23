<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

class VCRowTransformation implements ShortcodeTransformation {

	function tag(): string {
		return "vc_row";
	}

	function transform($attrs, $content = ""): string {
		if(
			substr_count($content,"[vc_column]") > 1 ||
			substr_count($content,"[vc_row_inner]") > 1 ||
			substr_count($content,"[vc_column_inner]") > 1
		){
			return "<!-- wp:columns -->\n<div class=\"wp-block-columns\">$content</div>\n<!-- /wp:columns -->\n\n";
		}
		return str_replace(
			[
				"[vc_column]","[/vc_column]",
				"[vc_row_inner]", "[/vc_row_inner]",
				"[vc_column_inner]", "[/vc_column_inner]"
			],
			"",
			$content
		);
	}
}