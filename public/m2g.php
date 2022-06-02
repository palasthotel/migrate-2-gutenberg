<?php

namespace Palasthotel\WordPress\MigrateToGutenberg;

use Palasthotel\WordPress\MigrateToGutenberg\Store\MigrationsDatabase;

/**
 * Plugin Name: Migrate 2 Gutenberg
 * Plugin URI: https://github.com/palasthotel/blockX
 * Description: Migrate pre Gutenberg contents to Gutenberg blocks
 * Version: 0.1.0
 * Author: Palasthotel <rezeption@palasthotel.de> (in person: Edward Bock)
 * Author URI: http://www.palasthotel.de
 * Requires at least: 5.0
 * Tested up to: 5.9.3
 * Text Domain: m2g
 * License: http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @copyright Copyright (c) 2022, Palasthotel
 * @package Palasthotel\WordPress\MigrateToGutenberg
 *
 */

require_once dirname( __FILE__ ) . "/vendor/autoload.php";

/**
 * @property Menu menu
 * @property MigrationsController $migrationController
 * @property MigrationsDatabase dbMigrations
 * @property Ajax $ajax
 * @property Actions actions
 */
class Plugin extends Components\Plugin {

	const DOMAIN = "m2g";

	const ACTION_REGISTER_MIGRATIONS = "m2g_register_migrations";

	const FILTER_SHORTCODE_TRANSFORMATIONS = "m2g_shortcode_transformations";
	const FILTER_BLOCK_TRANSFORMATIONS = "m2g_block_transformations";

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
		}
	}

	public function onSiteActivation() {
		parent::onSiteActivation();
		$this->dbMigrations->createTables();
	}
}

Plugin::instance();

require_once __DIR__."/public-functions.php";