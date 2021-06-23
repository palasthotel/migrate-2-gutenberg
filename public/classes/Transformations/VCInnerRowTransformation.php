<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


class VCInnerRowTransformation extends VCRowTransformation {

	function tag(): string {
		return "vc_row_inner";
	}

}