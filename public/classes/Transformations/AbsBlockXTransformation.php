<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\BlockX\Model\BlockId;
use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

abstract class AbsBlockXTransformation implements ShortcodeTransformation {

	abstract function tag(): string;

	abstract function blockId(): BlockId;

	function transform( $attrs, $content = "" ): string {
		$content = ["content" => $attrs];
		$json = json_encode($content, JSON_UNESCAPED_UNICODE);
		$id = $this->blockId();
		return "<!-- wp:$id $json /-->\n\n";
	}
}