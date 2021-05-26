<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Store;


use Palasthotel\WordPress\Database;

/**
 * @property string table
 */
class MigrationsDatabase extends Database {

	function init() {
		$this->table = "m2g_migrations";
	}

	public function createTables() {
		parent::createTables();
//		dbDelta("
//		");
	}
}