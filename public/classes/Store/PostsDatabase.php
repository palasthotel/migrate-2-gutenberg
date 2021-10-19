<?php

namespace Palasthotel\WordPress\MigrateToGutenberg\Store;

class PostsDatabase {

	static function updatePost( $postID, $post_content ) {
		global $wpdb;
		return $wpdb->update(
			$wpdb->posts,
			[
				"post_content" => $post_content,
			],
			[
				"ID" => $postID,
			]
		);
	}

}