<?php


namespace Palasthotel\WordPress\MigrateToGutenberg;


use WP_Post;

class Actions extends Components\Component {

	const RUN_TRANSFORMATIONS = "run_transformations";
	const RUN_ROLLBACK = "run_rollback";
	const RUN_UPDATE = "run_update";

	public function onCreate() {
		parent::onCreate();
		add_action('wp_ajax_'.self::RUN_TRANSFORMATIONS, [$this, 'run_transformations']);
		add_action('wp_ajax_'.self::RUN_ROLLBACK, [$this, 'run_rollback']);
		add_action('wp_ajax_'.self::RUN_UPDATE, [$this, 'run_update']);
	}

	public function getRunTransformationsUrl($post_id){
		return admin_url("/admin-ajax.php?post_id=$post_id&action=".self::RUN_TRANSFORMATIONS);
	}

	public function getRunRollbackUrl($post_id){
		return admin_url("/admin-ajax.php?post_id=$post_id&action=".self::RUN_ROLLBACK);
	}

	public function getRunUpdateUrl($post_id){
		return admin_url("/admin-ajax.php?post_id=$post_id&action=".self::RUN_UPDATE);
	}

	/**
	 * @return WP_Post
	 */
	private function securityCheck(): WP_Post {
		if(!isset($_GET["post_id"])){
			exit;
		}
		$postId = intval($_GET["post_id"]);
		$post = get_post($postId);
		if(!($post instanceof WP_Post)){
			exit;
		}

		if(!current_user_can("edit_posts")){
			exit;
		}
		return $post;
	}

	public function run_transformations(){
		$post = $this->securityCheck();

		$migratedContent = $this->plugin->migrationController->migrate($post->post_content);
		$this->plugin->dbMigrations->setPostContentBackup($post->ID, $post->post_content);

		$success = wp_update_post([
			"ID" => $post->ID,
			"post_content" => $migratedContent,
		]);

		if($success instanceof \WP_Error){
			wp_die($success);
		}

		wp_redirect(get_edit_post_link($post->ID, ''));

		exit;
	}

	public function run_update(){
		$post = $this->securityCheck();

		$content = $this->plugin->dbMigrations->getPostContentBackup($post->ID);
		$migratedContent = $this->plugin->migrationController->migrate($content);

		$success = wp_update_post([
			"ID" => $post->ID,
			"post_content" => $migratedContent,
		]);

		if($success instanceof \WP_Error){
			wp_die($success);
		}

		wp_redirect(get_edit_post_link($post->ID, ''));

		exit;
	}

	public function run_rollback(){
		$post = $this->securityCheck();


		$backup = $this->plugin->dbMigrations->getPostContentBackup($post->ID);

		$success = wp_update_post([
			"ID" => $post->ID,
			"post_content" => $backup,
		]);

		if($success instanceof \WP_Error){
			wp_die($success);
		}

		$this->plugin->dbMigrations->deletePostContentBackup($post->ID);

		wp_redirect(get_edit_post_link($post->ID, ''));

		exit;
	}
}