<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Views;


use Palasthotel\WordPress\MigrateToGutenberg\Components\Component;
use Palasthotel\WordPress\MigrateToGutenberg\Plugin;

/**
 * @property PostMigrationDiff $diff
 * @property PostMigrationPreview preview
 */
class Menu extends Component {

	const SLUG = "m2g";

	function onCreate() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		$this->diff = new PostMigrationDiff($this->plugin);
		$this->preview = new PostMigrationPreview($this->plugin);
	}

	public function admin_menu() {
		add_management_page(
			__( 'Migrate to Gutenberg', Plugin::DOMAIN ),
			__( 'Migrate to Gutenberg', Plugin::DOMAIN ),
			'manage_options',
			self::SLUG,
			array( $this, 'render' )
		);
	}

	public function render() {
		$tab = $_GET["view"] ?? "";

		?>
        <div class="wrap">
        <h1>Migrate to Gutenberg</h1>
        <?php

		$this->renderTabs( $tab );

		switch ( $tab ) {
			case "migrations":
				$this->renderMigrationsList();
				break;
			default:
				$this->renderPostsTable();
		}
		?>
        </div>
        <?php
	}

	public function renderTabs( $active ) {
		$tabs = [
			""      => "Posts",
			"migrations" => "Migrations",
		];
		?>
        <nav class="nav-tab-wrapper">
            <?php
            foreach ($tabs as $slug => $label){
	            $url  = admin_url( "tools.php?page=" . Menu::SLUG . "&view=$slug" );
	            $activeClass = $active === $slug ? "nav-tab-active":"";
                echo "<a href='$url' class='nav-tab $activeClass'>$label</a>";
            }
            ?>
        </nav>
        <?php
	}

	public function renderPostsTable() {
		?>
        <style>
            .column-post_id {
                width: 5%;
                min-width: 60px;
            }

            .column-post_title {
                width: 25%;
            }

            .column-migrations ul {
                margin: 0;
            }
            .column-actions {
                width: 10%;
            }
        </style>
        <?php
        $migrations = $this->plugin->migrationController->getMigrations();
        $table      = new PostMigrationsTable( $migrations );
        $table->prepare_items();
        $table->display();

	}

	public function renderMigrationsList() {
		$migrations = $this->plugin->migrationController->getMigrations();
		foreach ( $migrations as $migration ) {
			echo "<h2>" . $migration->id() . "</h2>";
			$migration->description();
		}
	}
}