<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


class VCInnerColumnTransformation extends VCColumnTransformation {

	function tag(): string {
		return "vc_column_inner";
	}

	function transform($attrs, $content = ""): string {
		$width = isset( $attrs['width'] ) ? self::calculate_columns( $attrs['width'] ) : '';
		$width_json = $width ? "{\"width\":\"$width\"}" : '';
		$width_style = $width ? " style=\"flex-basis:$width\"" : '';

		return sprintf(
			"<!-- wp:column %s -->\n<div class=\"wp-block-column\"%s>\n%s</div>\n<!-- /wp:column -->\n\n",
			$width_json,
			$width_style,
			$content
		);
	}

	private function calculate_columns( $width = "" ): string {
		if ( $width ) {
			$width_array = explode( "/", $width );
			$width_calc = $width_array[0] / $width_array[1];
			$width = round( (float)$width_calc * 100, 3 ) . '%';
		}
		return $width;
	}
}