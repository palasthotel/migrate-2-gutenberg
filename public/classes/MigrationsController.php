<?php


namespace Palasthotel\WordPress\MigrateToGutenberg;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\Migration;
use Palasthotel\WordPress\MigrateToGutenberg\Migrations\ShortcodesMigration;

class MigrationsController extends Components\Component {

	/**
	 * @var Migration[]
	 */
	private array $migrations = [];

	function onCreate() {
		add_action("init", function(){
			do_action(Plugin::ACTION_REGISTER_MIGRATIONS, $this);
			$this->register(new ShortcodesMigration());
		});
	}

	public function register(Migration $migration): bool {
		if(isset($this->migrations[$migration->id()])){
			error_log("Migration id is already registered: ".$migration->id());
			return false;
		}
		$this->migrations[$migration->id()] = $migration;
		return true;
	}

	/**
	 * @return Migration[]
	 */
	public function getMigrations(): array {
		return $this->migrations;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function migrate($content){
		$migratedContent = $content;
		foreach ($this->migrations as $migration){
			$migratedContent = $migration->transform($migratedContent, true);
		}
		return $migratedContent;
	}
}