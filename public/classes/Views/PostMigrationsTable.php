<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Views;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\Migration;
use Palasthotel\WordPress\MigrateToGutenberg\Model\PostMigration;
use Palasthotel\WordPress\MigrateToGutenberg\Plugin;
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
			"status" => "Status",
			"migrations" => "Migrations",
			"actions" => "Actions",
		];
	}

	public function get_views() {
		$baseUrl = remove_query_arg(["status", "post_id"],$_SERVER['REQUEST_URI']);
		$allUrl = remove_query_arg("status",$baseUrl);
		$pendingUrl = add_query_arg(["status" => "pending"], $allUrl);
		$migratedUrl = add_query_arg(["status" => "migrated"], $allUrl);
		$status = $this->getStatusFilter();
		$classAll = $status === null ? "class='current'" : "";
		$classPending = $status === "pending" ? "class='current'" : "";
		$classMigrated = $status === "migrated" ? "class='current'" : "";
		return [
			"all" => "<a href='$allUrl' $classAll>All</a>",
			"pending" => "<a href='$pendingUrl' $classPending>Pending</a>",
			"migrated" => "<a href='$migratedUrl' $classMigrated>Migrated</a>",
		];
	}

	private function isMigrated($post_id){
		return is_string(Plugin::instance()->dbMigrations->getPostContentBackup($post_id));
	}

	private function getStatusFilter(){
		$filerIsActive = isset($_GET["status"]) && in_array($_GET["status"], ["migrated", "pending"]);
		return $filerIsActive ? $_GET["status"] : null;
	}

	private function getPostIdFilter(){
		return isset($_GET["post_id"]) && !empty($_GET["post_id"]) ? intval($_GET["post_id"]) : null;
	}

	/**
	 * @param PostMigration $item
	 * @param string $column_name
	 */
	protected function column_default( $item, $column_name ) {
		$baseUrl = remove_query_arg(["status", "post_id"],$_SERVER['REQUEST_URI']);
		switch ( $column_name ) {
			case "post_id":
				$url = add_query_arg(["post_id" => $item->post_id],$baseUrl);
				echo "<a href='$url'>$item->post_id</a>";
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
			case "status":
				$isMigrated = $this->isMigrated($item->post_id);
				$filerIsActive = $this->getStatusFilter() !== null;

				if($isMigrated){
					if($filerIsActive){
						echo "Migrated";
					} else {
						$url = add_query_arg(["status" => "migrated"],$baseUrl);
						echo "<a href='$url'>Migrated</a>";
					}

				} else {
					if($filerIsActive){
						echo "Pending";
					} else {
						$url = add_query_arg(["status" => "pending"],$baseUrl);
						echo "<a href='$url'>Pending</a>";
					}
				}
				break;
			case "actions":
				$content = Plugin::instance()->dbMigrations->getPostContentBackup($item->post_id);
				echo "<p style='line-height: 1.6rem; margin:0;' data-auto-reload-links>";

				$diffUrl = PostMigrationDiff::getUrl($item->post_id);
				echo "<a href='$diffUrl' target='_blank'>Diff</a><br/>";
				$previewUrl = PostMigrationPreview::getUrl($item->post_id);
				echo "<a href='$previewUrl' target='_blank'>Preview</a><br/>";
				if(is_string($content)){
					$rollbackUrl = Plugin::instance()->actions->getRunRollbackUrl($item->post_id);
					echo "<a href='$rollbackUrl' data-auto-reload-link target='_blank'>Rollback</a><br/>";
					$updateUrl = Plugin::instance()->actions->getRunUpdateUrl($item->post_id);
					echo "<a href='$updateUrl' target='_blank'>Update transform</a>";
				} else {
					$transformUrl = Plugin::instance()->actions->getRunTransformationsUrl($item->post_id);
					echo "<a href='$transformUrl' data-auto-reload-link target='_blank'>Transform</a>";
				}
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

		$filterStatus = $this->getStatusFilter();
		$filterPostID = $this->getPostIdFilter();
		if($filterPostID){
			$postMigrations = array_filter($postMigrations, function($key) use ($filterPostID){
				return $key == $filterPostID;
			},ARRAY_FILTER_USE_KEY);
		} else if( $filterStatus !== null){
			$postMigrations = array_filter($postMigrations, function($key) use ($filterStatus){
				$is = $this->isMigrated($key);
				return ($filterStatus === "pending" && !$is) || ($filterStatus === "migrated" && $is);
			},ARRAY_FILTER_USE_KEY);
		}

		$postMigrations = array_values($postMigrations);


		$pages = array_chunk($postMigrations, $per_page);

		$this->items = count($pages) > 0 ? $pages[$page-1] : [];

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