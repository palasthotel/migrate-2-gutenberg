<?php

use Palasthotel\WordPress\MigrateToGutenberg\Plugin;

function migrate_to_gutenberg_plugin(): Plugin {
	return Plugin::instance();
}

function migrate_to_gutenberg_migrate_content($post_content): string {
	return migrate_to_gutenberg_plugin()->migrationController->migrate($post_content);
}