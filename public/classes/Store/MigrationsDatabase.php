<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Store;

use Palasthotel\WordPress\MigrateToGutenberg\Components\Database;

/**
 * @property string table
 */
class MigrationsDatabase extends Database {

	function init() {
		$this->table = "m2g_migrations";
	}

	public function setPostRecoveryRevisionId($post_id, $revision_id){
		return $this->wpdb->insert(
			$this->table,
			[
				"post_id" => $post_id,
				"revision_id" => $revision_id
			],
			["%d", "%d"]
		);
	}

	public function getPostRecoveryRevisionId($post_id){
		return intval($this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT revision_id FROM $this->table WHERE post_id = %d",
				$post_id
			)
		));
	}

	public function createTables() {
		parent::createTables();
		dbDelta("CREATE TABLE IF NOT EXISTS $this->table
			(
			 post_id bigint(20) unsigned NOT NULL,
			 revision_id bigint (20) unsigned NOT NULL,
			 primary key restore_post_revision (post_id, revision_id),
			 key (post_id),
			 key (revision_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
}