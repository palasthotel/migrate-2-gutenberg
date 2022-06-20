<?php


namespace Palasthotel\WordPress\MigrateToGutenberg;


/**
 * @property Plugin plugin
 */
class CommandLine {
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * @param array $assoc
	 *
	 * @return int|null
	 */
	private function getId( $assoc ) {
		return isset( $assoc["id"] ) && ! empty( $assoc["id"] ) ? intval( $assoc["id"] ) : null;
	}

	/**
	 * display stats
	 *
	 * ## EXAMPLES
	 *
	 *   wp m2g stats
	 *
	 * @when after_wp_load
	 */
	public function stats( $ars, $assoc_args ) {
		$migrations = $this->plugin->migrationController->getMigrations();

		$thMigrations = "- Migration ---";
		$thPosts      = "- Posts ---";
		$data         = [];
		foreach ( $migrations as $migration ) {
			$data[] = [
				$thMigrations => $migration->id(),
				$thPosts      => count( $migration->postIds() ),
			];
		}

		\WP_CLI\Utils\format_items( 'table', $data, array( $thMigrations, $thPosts ) );
	}

	/**
	 * migrate contents
	 *
	 * ## OPTIONS
	 *
	 * [--id=<id>]
	 * : migrate a single post
	 *
	 * [--migration=<migration>]
	 * : migration id
	 *
	 * ## EXAMPLES
	 *
	 *   wp m2g migrate --id=1 --migration=shortcodes
	 *
	 * @when after_wp_load
	 */
	public function migrate( $args, $assoc_args ) {
		$post_id      = $this->getId( $assoc_args );
		$migration_id = isset( $assoc_args["migration"] ) && ! empty( $assoc_args["migration"] ) ? $assoc_args["migration"] : null;

		$migrations = $this->plugin->migrationController->getMigrations();

		$count = 0;
		$errors = 0;
		foreach ( $migrations as $migration ) {
			if ( null !== $migration_id && $migration->id() !== $migration_id ) {
				\WP_CLI::line( "Migration " . $migration->id() . " was skipped." );
				continue;
			}

			if ( null !== $post_id ) {
				$count = 1;
				\WP_CLI::line( "Migration " . $migration->id() . " is migrating $post_id." );
				$response = $this->plugin->actions->migrate( $post_id );
				if($response instanceof \WP_Error){
					$errors++;
					\WP_CLI::error( $response->get_error_message() );
				}
			} else {
				$post_ids = $migration->postIds();
				$count    = count( $post_ids );
				\WP_CLI::line( "Migration " . $migration->id() . " is migrating $count posts." );
				$progress = \WP_CLI\Utils\make_progress_bar( 'Starting ' . $migration->id(), $count );
				$errors = 0;
				foreach ( $post_ids as $post_id ) {
					$response = $this->plugin->actions->migrate( $post_id );
					if($response instanceof \WP_Error){
						$errors++;
					}
					$progress->tick();
				}
				$progress->finish();

			}
		}
		if($errors == 0){
			\WP_CLI::success( "Done!" );
		} else {
			$migrated = $count-$errors;
			\WP_CLI::warning( "Migrated $migrated / $count" );
		}

	}

	/**
	 * rollback
	 *
	 * ## OPTIONS
	 *
	 * [--id=<id>]
	 * : rollback a single post
	 *
	 * ## EXAMPLES
	 *
	 *   wp m2g rollback --ID=1
	 *
	 * @when after_wp_load
	 */
	public function rollback( $ars, $assoc_args ) {
		$post_id = $this->getId( $assoc_args );

		// rollback single post
		if ( $post_id ) {
			$success = $this->plugin->actions->rollback( $post_id );
			if ( $success instanceof \WP_Error ) {
				\WP_CLI::error( $success );
				exit;
			}
			\WP_CLI::success( "Post $post_id was restored." );
			exit;
		}

		$post_ids = $this->plugin->dbMigrations->getPostIdsWithBackup();
		$count    = count( $post_ids );
		\WP_CLI::line( "Rollback for $count posts." );
		$progress = \WP_CLI\Utils\make_progress_bar( 'Starting rollback', $count );
		$errors = 0;
		foreach ($post_ids as $post_id){

			$response = $this->plugin->actions->rollback( $post_id );
			if($response instanceof \WP_Error){
				$errors++;
			}
			$progress->tick();

		}
		$progress->finish();

		if($errors == 0){
			\WP_CLI::success( "Done!" );
		} else {
			$rolledback = $count-$errors;
			\WP_CLI::warning( "Migrated $rolledback / $count" );
		}

	}

}