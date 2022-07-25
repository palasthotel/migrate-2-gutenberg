<?php


namespace Palasthotel\WordPress\MigrateToGutenberg;


use Palasthotel\WordPress\MigrateToGutenberg\Components\Component;
use Palasthotel\WordPress\MigrateToGutenberg\Store\PostsDatabase;
use WP_Error;
use WP_Post;

class Actions extends Component {

	/**
	 * @param string|int|WP_Post $post
	 * @param bool $update
	 *
	 * @return int|bool|WP_Error
	 */
	public function migrate( $post, $update = false ) {
		$post            = get_post( $post );

		if(!($post instanceof WP_Post)){
			return new WP_Error(
				404,
				"No post found."
			);
		}

		$content = $this->plugin->dbMigrations->getPostContentBackup( $post->ID );
		$isUpdate = false;

		if(null === $content){
			// new migration
			$migratedContent = $this->plugin->migrationController->migrate( $post->post_content );
			$this->plugin->dbMigrations->setPostContentBackup( $post->ID, $post->post_content );
		} else if( !$update ){
			// no update please
			return true;
		} else {
			// update migration
			$migratedContent = $this->plugin->migrationController->migrate( $content );
			$isUpdate = true;
		}

		$success = PostsDatabase::updatePost( $post->ID, $migratedContent );

		if ( false === $success || (!$isUpdate && $success <= 0) ) {
			return new \WP_Error(
				500,
				'Couldn’t update post: ' . $post->ID,
			);
		}
		return true;
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

		$success = PostsDatabase::updatePost( $post->ID, $backup );

		if(!($success instanceof WP_Error) && false !== $success && $success > 0){
			$this->plugin->dbMigrations->deletePostContentBackup($post->ID);
		}

		return $success;
	}

}