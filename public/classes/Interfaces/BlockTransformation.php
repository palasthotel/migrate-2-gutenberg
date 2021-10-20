<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Interfaces;


use Palasthotel\WordPress\BlockX\Model\BlockId;

interface BlockTransformation {
	function from(): string;
	function to(): BlockId;

	/**
	 * @param string[]|string $attrs
	 * @param string $content
	 *
	 * @return string
	 */
	function transform( $attrs, string $content): string;
}