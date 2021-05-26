<?php


namespace Palasthotel\WordPress\MigrateToGutenberg;


/**
 * @property Plugin plugin
 */
abstract class _Component {

	public function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		$this->onCreate();
	}

	abstract function onCreate();
}