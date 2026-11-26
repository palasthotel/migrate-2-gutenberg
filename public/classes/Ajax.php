<?php


namespace Palasthotel\WordPress\MigrateToGutenberg;


use WP_Post;

class Ajax extends Components\Component {

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
		return $this->getActionUrl(self::RUN_TRANSFORMATIONS, $post_id);
	}

	public function getRunRollbackUrl($post_id){
		return $this->getActionUrl(self::RUN_ROLLBACK, $post_id);
	}

	public function getRunUpdateUrl($post_id){
		return $this->getActionUrl(self::RUN_UPDATE, $post_id);
	}

	private function getActionUrl(string $action, $post_id): string {
		$post_id = intval($post_id);
		return add_query_arg(
			[
				"action"   => $action,
				"post_id"  => $post_id,
				"_wpnonce" => wp_create_nonce(self::nonceAction($action, $post_id)),
			],
			admin_url("admin-ajax.php")
		);
	}

	public static function nonceAction(string $action, int $post_id): string {
		return "m2g_{$action}_{$post_id}";
	}

	/**
	 * @return WP_Post
	 */
	private function securityCheck(string $action): WP_Post {
		if(!isset($_GET["post_id"])){
			exit;
		}
		$postId = intval($_GET["post_id"]);
		check_admin_referer(self::nonceAction($action, $postId));

		$post = get_post($postId);
		if(!($post instanceof WP_Post)){
			exit;
		}

		if(!current_user_can("manage_options") || !current_user_can("edit_post", $post->ID)){
			wp_die(esc_html__("Sorry, you are not allowed to migrate this post.", Plugin::DOMAIN), 403);
		}
		return $post;
	}

	public function run_transformations(){
		$post = $this->securityCheck(self::RUN_TRANSFORMATIONS);

		$response = $this->plugin->actions->migrate($post, true);

		if($response instanceof \WP_Error){
			wp_die($response);
		}

		wp_redirect(get_edit_post_link($post->ID, ''));

		exit;
	}

	public function run_update(){
		$post = $this->securityCheck(self::RUN_UPDATE);

		$response = $this->plugin->actions->migrate($post, true);

		if($response instanceof \WP_Error){
			wp_die($response);
		}

		wp_redirect(get_edit_post_link($post->ID, ''));

		exit;
	}

	public function run_rollback(){
		$post = $this->securityCheck(self::RUN_ROLLBACK);


		$success = $this->plugin->actions->rollback($post);

		if($success instanceof \WP_Error){
			wp_die($success);
		}

		wp_redirect(get_edit_post_link($post->ID, ''));

		exit;
	}
}