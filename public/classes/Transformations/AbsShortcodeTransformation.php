<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Transformations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;

abstract class AbsShortcodeTransformation implements ShortcodeTransformation {

	abstract function tag(): string;

	function transform( $attrs, $content = "" ): string {
		$data = "";
		if(is_array($attrs)){
			$list = array_map( function ( $key ) use ( $attrs ) {
				$value = $attrs[ $key ];
				return "$key=\"$value\"";
			}, array_keys( $attrs ) );
			$data = implode(" ", $list);
		}
		if(!empty($data)){
			$data = " $data ";
		}

		$tag = $this->tag();
		$code = (!empty($content)) ? "[{$tag}{$data}]{$content}[/{$tag}]" : "[{$tag} {$data} ]";
		return "<!-- wp:shortcode -->\n$code\n<!-- /wp:shortcode -->\n\n";
	}
}