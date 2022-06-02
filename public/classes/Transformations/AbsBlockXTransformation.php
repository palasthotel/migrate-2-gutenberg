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

	/**
	 * @param string|array $attrs
	 * @param string $content
	 *
	 * @return string
	 */
	function transform( $attrs, string $content = "" ): string {
		$attrs = is_array($attrs) ? $attrs : [];
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