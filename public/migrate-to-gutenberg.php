<?php

namespace Palasthotel\WordPress\MigrateToGutenberg;

use Palasthotel\WordPress\MigrateToGutenberg\Views\Menu;
use Palasthotel\WordPress\MigrateToGutenberg\Views\MenuAnalytics;

/**
 * Plugin Name: Migrate to Gutenberg
 * Plugin URI: https://github.com/palasthotel/blockX
 * Description: Migrate pre Gutenberg contents to Gutenberg blocks
 * Version: 0.0.1
 * Author: Palasthotel <rezeption@palasthotel.de> (in person: Edward Bock)
 * Author URI: http://www.palasthotel.de
 * Requires at least: 5.0
 * Tested up to: 5.7.2
 * Text Domain: mtg
 * License: http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @copyright Copyright (c) 2021, Palasthotel
 * @package Palasthotel\WordPress\MigrateToGutenberg
 *
 */

require_once dirname(__FILE__)."/vendor/autoload.php";

const DOMAIN = "mtg";

/**
 * @property Menu menu
 * @property MigrationHandler migrationHandler
 */
class Plugin extends \Palasthotel\WordPress\Plugin {

	const ACTION_REGISTER_MIGRATIONS = "m2g_register_migrations";

	function onCreate() {
		$this->migrationHandler = new MigrationHandler($this);
		$this->menu = new Menu($this);
	}
}

Plugin::instance();