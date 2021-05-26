<?php

/**
 * Plugin Name:       Migrate to Gutenberg - DEV
 * Description:       Dev inc file
 * Version:           X.X.X
 * Requires at least: X.X
 * Tested up to:      X.X.X
 * Author:            PALASTHOTEL by Edward
 * Author URI:        http://www.palasthotel.de
 * Domain Path:       /plugin/languages
 */

include dirname( __FILE__ ) . "/public/migrate-to-gutenberg.php";

register_activation_hook(__FILE__, function($multisite){
	\Palasthotel\WordPress\MigrateToGutenberg\Plugin::instance()->onActivation($multisite);
});

register_deactivation_hook(__FILE__, function($multisite){
	\Palasthotel\WordPress\MigrateToGutenberg\Plugin::instance()->onDeactivation($multisite);
});