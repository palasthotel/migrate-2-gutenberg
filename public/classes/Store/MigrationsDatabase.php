<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Store;

use Palasthotel\WordPress\MigrateToGutenberg\Components\Database;

/**
 * @property string table
 */
class MigrationsDatabase extends Database {

	function init() {
		$this->table = $this->wpdb->prefix."m2g_migrations";
	}

	public function setPostContentBackup($post_id, $content){
		return $this->wpdb->insert(
			$this->table,
			[
				"post_id" => $post_id,
				"post_content_backup" => $content
			],
			["%d", "%s"]
		);
	}

	/**
	 * @param $post_id
	 *
	 * @return string|null
	 */
	public function getPostContentBackup($post_id){
		return $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT post_content_backup FROM $this->table WHERE post_id = %d",
				$post_id
			)
		);
	}

	public function deletePostContentBackup($post_id){
		return $this->wpdb->delete(
			$this->table,
			["post_id" => $post_id],
			["%d"]
		);
	}

	public function createTables() {
		parent::createTables();
		dbDelta("CREATE TABLE IF NOT EXISTS $this->table
			(
			 post_id bigint(20) unsigned NOT NULL,
			 post_content_backup TEXT,
			 primary key (post_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
}