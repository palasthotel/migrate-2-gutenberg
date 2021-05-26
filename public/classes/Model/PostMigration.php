<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Model;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\Migration;

class PostMigration {

	var int $post_id;

	/**
	 * @var Migration[]
	 */
	var array $migrations = [];
}