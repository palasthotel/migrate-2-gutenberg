<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Interfaces;


interface ShortcodeTransformation {
	function tag(): string;

	/**
	 * @param string[]|string $attrs
	 * @param string $content
	 *
	 * @return string
	 */
	function transform( $attrs, string $content): string;
}