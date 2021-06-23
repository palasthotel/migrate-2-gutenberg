<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Views;


use Palasthotel\WordPress\MigrateToGutenberg\Components\Component;

class PostMigrationPreview extends Component {

	public function onCreate() {
		parent::onCreate();
		add_filter('the_content', [$this, 'the_content'], 0);
	}

	public static function getUrl($post_id){
		return get_permalink($post_id)."?m2g_preview=true";
	}

	public function the_content($content){
		if(!isset($_GET["m2g_preview"]) || $_GET["m2g_preview"] !== "true" ){
			return $content;
		}
		if(!current_user_can("edit_posts")){
			return $content;
		}

		return $this->plugin->migrationController->migrate($content);
	}

}