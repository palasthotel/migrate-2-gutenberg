<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\BlockX\Model\BlockId;
use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\BlockTransformation;

abstract class AbsBlock2BlockXTransformation implements BlockTransformation {

	abstract function from(): string;

	abstract function to(): BlockId;

	function transform( $attrs, $content = "" ): string {
		$content = '{"content":' . $attrs . '}';
		$blockId = $this->to();
		return "<!-- wp:$blockId->namespace/$blockId->name $content /-->\n\n";
	}

}