<?php


namespace Palasthotel\WordPress\MigrateToGutenberg;


use Palasthotel\WordPress\MigrateToGutenberg\Components\Component;
use WP_Error;
use WP_Post;

class Actions extends Component {

	/**
	 * @param string|int|WP_Post $post
	 *
	 * @return int|WP_Error
	 */
	public function migrate( $post ) {
		$post            = get_post( $post );

		if(!($post instanceof WP_Post)){
			return new WP_Error(
				404,
				"No post found."
			);
		}

		$content = $this->plugin->dbMigrations->getPostContentBackup( $post->ID );

		if(null === $content){
			// new migration
			$migratedContent = $this->plugin->migrationController->migrate( $post->post_content );
			$this->plugin->dbMigrations->setPostContentBackup( $post->ID, $post->post_content );

			return wp_update_post( [
				"ID"           => $post->ID,
				"post_content" => $migratedContent,
			] );
		}

		// update migration
		$migratedContent = $this->plugin->migrationController->migrate( $content );
		return wp_update_post( [
			"ID"           => $post->ID,
			"post_content" => $migratedContent,
		] );

	}

	/**
	 * @param string|int|WP_Post $post
	 *
	 * @return int|WP_Error
	 */
	public function rollback($post){

		$post = get_post($post);

		if(!($post instanceof WP_Post)){
			return new WP_Error(
				404,
				"No post found."
			);
		}

		$backup = $this->plugin->dbMigrations->getPostContentBackup($post->ID);

		if ( null === $backup ) {
			return new WP_Error(
				404,
				"There is no post content backup. Only posts that were already migrated can be restored."
			);
		}

		$success =  wp_update_post([
			"ID" => $post->ID,
			"post_content" => $backup,
		]);

		if(!($success instanceof WP_Error) && $success > 0){
			$this->plugin->dbMigrations->deletePostContentBackup($post->ID);
		}

		return $success;
	}

}