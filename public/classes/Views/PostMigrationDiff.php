<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Views;


use Palasthotel\WordPress\MigrateToGutenberg\Components\Component;

class PostMigrationDiff extends Component {

	public function onCreate() {
		parent::onCreate();
		add_action('wp_ajax_m2g_diff', [$this, 'diff' ]);
	}

	public static function getUrl($post_id){
		return admin_url("admin-ajax.php?action=m2g_diff&post_id=$post_id");
	}

	public function diff(){
		if(!isset($_GET["post_id"])){
			wp_die("Missing post id.");
		}
		$post = get_post(intval($_GET["post_id"]));
		if(!($post instanceof \WP_Post)){
			wp_die("Post not found.");
		}

		$backupContent = $this->plugin->dbMigrations->getPostContentBackup($post->ID);
		$content = is_string($backupContent) ? $backupContent : $post->post_content;

		$migratedContent = $this->plugin->migrationController->migrate($content);

		echo "<div style='display: flex'>";
		$style = "width: 50%; height: 90vh";
		echo "<textarea style='$style' readonly>$content</textarea>";
		echo "<textarea style='$style' readonly>$migratedContent</textarea>";
		echo "</div>";
		exit;
	}

}