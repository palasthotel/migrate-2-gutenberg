<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

class VCColumnTransformation implements ShortcodeTransformation {

	function tag(): string {
		return "vc_column";
	}

	function transform($attrs, $content = ""): string {
		if( substr_count($content,"[vc_column_inner]") > 1 ){
			return "<!-- wp:columns -->\n<div class=\"wp-block-columns\">$content</div>\n<!-- /wp:columns -->\n\n";
		}
		$group = '{"layout":"type":"constrained"}}';
		return sprintf(
			"<!-- wp:group %s -->\n<div class=\"wp-block-group\">%s</div>\n<!-- /wp:group -->\n\n",
			$group,
			$content
		);
	}
}