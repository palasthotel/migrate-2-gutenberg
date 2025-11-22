<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

class VCRowTransformation implements ShortcodeTransformation {

	function tag(): string {
		return "vc_row";
	}

	function transform($attrs, $content = ""): string {
		if(
			str_starts_with( $content, "<!-- wp:column" )
		){
			return "<!-- wp:columns -->\n<div class=\"wp-block-columns\">$content</div>\n<!-- /wp:columns -->\n\n";
		} else {
			$group = '{"layout":"type":"constrained"}}';
			return sprintf(
				"<!-- wp:group %s -->\n<div class=\"wp-block-group\">%s</div>\n<!-- /wp:group -->\n\n",
				$group,
				$content
			);
		}
	}
}