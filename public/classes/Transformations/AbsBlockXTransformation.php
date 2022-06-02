<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\BlockX\Model\BlockId;
use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

abstract class AbsBlockXTransformation implements ShortcodeTransformation {

	abstract function tag(): string;

	abstract function blockId(): BlockId;

	/**
	 * @param array $attrs shortcode attributes
	 *
	 * @return array block content
	 */
	function attributesToContent(array $attrs): array{
		return $attrs;
	}

	function modifyBlockProps(array $blockAttributes): array {
		return $blockAttributes;
	}

	function transform( $attrs, $content = "" ): string {
		$blockProps = $this->modifyBlockProps(
			[
				"content" => $this->attributesToContent($attrs)
			]
		);
		$json = json_encode($blockProps, JSON_UNESCAPED_UNICODE);
		$id = $this->blockId();
		return "<!-- wp:$id $json /-->\n\n";
	}
}