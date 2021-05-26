<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Views;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\Migration;
use Palasthotel\WordPress\MigrateToGutenberg\Model\PostMigration;
use WP_List_Table;

class PostMigrationsTable extends WP_List_Table {

	/**
	 * @var Migration[]
	 */
	private array $migrations;

	public function __construct( $migrations ) {
		parent::__construct( [] );
		$this->migrations = $migrations;
	}

	public function get_columns() {
		return [
			"post_id"       => "ID",
			"post_title" => "Title",
			"migrations" => "Migrations",
			"actions" => "Actions",
		];
	}

	/**
	 * @param PostMigration $item
	 * @param string $column_name
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case "post_id":
				echo $item->post_id;
				break;
			case "post_title":
				$url = get_edit_post_link($item->post_id, );
				echo "<a href='$url'>".get_the_title($item->post_id)."</a>";
				break;
			case "migrations":
				echo "<ul>";
				foreach ($item->migrations as $migration){

					echo "<li>";
					echo "<div><strong>".$migration->id()."</strong></div>";
					echo "<div>";
					$migration->analyze($item->post_id);
					echo "</div>";
					echo "</li>";
				}
				echo "</ul>";
				break;
			case "actions":
				echo "<p style='line-height: 1.6rem; margin:0;'>";
				echo "<a href='#preview'>Preview</a><br/>";
				echo "<a href='#run'>Run</a>";
				echo "</p>";
				break;
		}
	}

	protected function get_sortable_columns() {
		return [];
	}

	private function get_hidden_columns(){
		return [];
	}

	public function prepare_items() {

		$per_page = 50;
		$page = $this->get_pagenum();

		/**
		 * @var PostMigration[] $postMigrations
		 */
		$postMigrations = [];
		foreach ($this->migrations as $migration){
			foreach ($migration->postIds() as $post_id){
				if(!isset($postMigrations[$post_id])){
					$postMigration = new PostMigration();
					$postMigration->post_id = $post_id;
					$postMigrations[$post_id] = $postMigration;
				}
				$postMigrations[$post_id]->migrations[] = $migration;
			}
		}

		$postMigrations = array_values($postMigrations);

		$pages = array_chunk($postMigrations, $per_page);

		$this->items = $pages[$page-1];

		$this->_column_headers = [
			$this->get_columns(),
			$this->get_hidden_columns(),
			$this->get_sortable_columns()
		];

		$this->set_pagination_args( [
			"total_items" => count($postMigrations),
			"total_pages" => ceil( count($postMigrations) / $per_page ),
			"per_page"    => $per_page,
		] );


	}

}