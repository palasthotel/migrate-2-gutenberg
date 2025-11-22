<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

class VCColumnTextTransformation implements ShortcodeTransformation {

	function tag(): string {
		return "vc_column_text";
	}

	function transform($attrs, $content = ""): string {

		if(empty($content)) return "";

		if ( str_contains( $content, "<h" ) ) {
			$html = new \WP_HTML_Tag_Processor( $content );
			$html->next_tag() === true;
			$tag = $html->get_tag();

			$h_level = abs((int) filter_var($tag, FILTER_SANITIZE_NUMBER_INT));
			$heading_level = ( (int) $h_level + 1);

			$html->next_token();
			$heading_content = $html->get_modifiable_text();

			$migrated_content = sprintf(
				"<!-- wp:header %s -->\n<h%s>%s</h%s>\n<!-- /wp:header -->\n\n",
				'{"level":"h'.$heading_level.'"}',
				$heading_level,
				$heading_content,
				$heading_level,
			);
		} else {
			$migrated_content = "<!-- wp:paragraph -->\n<p>$content</p>\n<!-- /wp:paragraph -->\n\n";
		}

		return $migrated_content;
	}
}