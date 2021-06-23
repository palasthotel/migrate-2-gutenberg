<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


class VCInnerColumnTransformation extends VCColumnTransformation {

	function tag(): string {
		return "vc_column_inner";
	}

}