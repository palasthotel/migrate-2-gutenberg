<?php

namespace Palasthotel\WordPress\MigrateToGutenberg;

use Palasthotel\WordPress\MigrateToGutenberg\Store\MigrationsDatabase;

/**
 * Plugin Name: Migrate 2 Gutenberg
 * Plugin URI: https://github.com/palasthotel/migrate-2-gutenberg
 * Description: Migrate pre Gutenberg contents to Gutenberg blocks
 * Version: 0.1.3
 * Author: Palasthotel <rezeption@palasthotel.de> (in person: Edward Bock)
 * Author URI: http://www.palasthotel.de
 * Requires at least: 5.0
 * Tested up to: 5.9.3
 * Requires PHP: 8.0
 * Text Domain: m2g
 * License: http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @copyright Copyright (c) 2022, Palasthotel
 * @package Palasthotel\WordPress\MigrateToGutenberg
 *
 */

// PSR-4 for this plugin's own classes - it has no dependencies, so it needs no vendor/
spl_autoload_register( function ( $class ) {
	$prefix = __NAMESPACE__ . "\\";
	if ( strncmp( $class, $prefix, strlen( $prefix ) ) !== 0 ) {
		return;
	}
	$file = __DIR__ . "/classes/" . str_replace( "\\", "/", substr( $class, strlen( $prefix ) ) ) . ".php";
	if ( is_file( $file ) ) {
		require_once $file;
	}
} );

/**
 * @property Menu menu
 * @property MigrationsController $migrationController
 * @property MigrationsDatabase dbMigrations
 * @property Ajax $ajax
 * @property Actions actions
 */
class Plugin extends Components\Plugin {
	public $menu;
	public $ajax;
	public $actions;
	public $dbMigrations;
	public $migrationController;

	const DOMAIN = "m2g";

	const ACTION_REGISTER_MIGRATIONS = "m2g_register_migrations";

	const FILTER_SHORTCODE_TRANSFORMATIONS = "m2g_shortcode_transformations";
	const FILTER_BLOCK_TRANSFORMATIONS = "m2g_block_transformations";

	const FILTER_ATTACHMENT_ID = "m2g_attachment_id";

	function onCreate() {

		$this->loadTextdomain( Plugin::DOMAIN, "languages" );

		$this->dbMigrations        = new MigrationsDatabase();
		$this->migrationController = new MigrationsController( $this );
		$this->actions             = new Actions( $this );
		$this->ajax                = new Ajax( $this );

		// wp cli
		if ( class_exists( "\WP_CLI" ) ) {
			\WP_CLI::add_command( 'm2g', new CommandLine( $this ) );
		}

		$this->menu = new Menu( $this );

		if ( WP_DEBUG ) {
			$this->dbMigrations->createTables();
		} else {
			add_action( 'admin_init', [ $this->dbMigrations, 'updateTablesIfNeeded' ] );
		}
	}

	public function onSiteActivation() {
		parent::onSiteActivation();
		$this->dbMigrations->createTables();
	}
}

Plugin::instance();

require_once __DIR__."/public-functions.php";