<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Views;


use Palasthotel\WordPress\MigrateToGutenberg\Components\Component;

class PostMigrationDiff extends Component {

	public function onCreate() {
		parent::onCreate();
		add_action('wp_ajax_m2g_diff', [$this, 'diff' ]);
	}

	public static function getUrl($post_id){
		return add_query_arg(
			[
				"action"  => "m2g_diff",
				"post_id" => intval($post_id),
			],
			admin_url("admin-ajax.php")
		);
	}

	public function diff(){
		if(!isset($_GET["post_id"])){
			wp_die("Missing post id.");
		}
		$post = get_post(intval($_GET["post_id"]));
		if(!($post instanceof \WP_Post)){
			wp_die("Post not found.");
		}
		if(!current_user_can("manage_options") || !current_user_can("edit_post", $post->ID)){
			wp_die(esc_html__("Sorry, you are not allowed to view this post.", \Palasthotel\WordPress\MigrateToGutenberg\Plugin::DOMAIN), 403);
		}

		$backupContent = $this->plugin->dbMigrations->getPostContentBackup($post->ID);
		$content = is_string($backupContent) ? $backupContent : $post->post_content;

		$migratedContent = $this->plugin->migrationController->migrate($content, $post);

		echo "<div style='display: flex'>";
		$style = "width: 50%; height: 90vh";
		echo "<textarea style='$style' readonly>" . esc_textarea($content) . "</textarea>";
		echo "<textarea style='$style' readonly>" . esc_textarea($migratedContent) . "</textarea>";
		echo "</div>";
		exit;
	}

}