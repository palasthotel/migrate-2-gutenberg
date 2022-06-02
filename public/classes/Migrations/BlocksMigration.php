<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Migrations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\Migration;
use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\BlockTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Plugin;

class BlocksMigration implements Migration {

	/**
	 * @var null | BlockTransformation[]
	 */
	private $blocks = null;

	public function id(): string {
		return "blocks";
	}

	public function description() {
		$posts = $this->postIds();
		$stats = $this->getStats();

		echo "<p><strong>Affected posts: " . count( $posts ) . "</strong></p>";
		echo "<p><strong>Blocks: " . count( $stats ) . "</strong></p>";

		$handled   = [];
		$unhandled = [];
		$tags      = array_map( function ( $transformation ) {
			return $transformation->from();
		}, $this->getBlocks() );

		foreach ( $stats as $block => $count ) {
			if ( in_array( $block, $tags ) ) {
				$handled[] = "<nobr>$block ($count)</nobr>";
			} else {
				$unhandled[] = "<nobr>$block ($count)</nobr>";
			}
		}

		$handledList   = implode( ", ", $handled );
		$unhandledList = implode( ", ", $unhandled );
		echo "<p style='line-height: 2rem; max-width: 800px;'><strong>With transformer</strong><br/>$handledList</p>";
		echo "<p style='line-height: 2rem; max-width: 800px;'><strong>Further candidates</strong><br/>$unhandledList</p>";
	}

	public function postIds(): array {
		global $wpdb;

		$blockNames = array_map( function ( $transformation ) {
			$blockName = $transformation->from();

			return "post_content LIKE '%<!-- wp:$blockName%' OR post_content LIKE '%<!-- /wp:$blockName%'";
		}, $this->getBlocks() );

		$likeTagsString = "";
		if(count($blockNames) > 0){
			$likeTags = implode( " OR ", $blockNames );
			$likeTagsString = "($likeTags) OR";
		}

		$migrationsTable = Plugin::instance()->dbMigrations->table;
		$query = "SELECT ID FROM " . $wpdb->posts . " WHERE 
		( $likeTagsString ( ID IN (SELECT post_id FROM $migrationsTable))) 
		AND (post_status ='publish' or post_status ='private' or post_status ='draft' OR post_status = 'future')
		ORDER BY post_date desc
		";

		return $wpdb->get_col( $query );
	}

	public function analyze( $post_id ) {
		$post       = get_post( $post_id );
		$blocks = $this->getPostStats( $post->post_content );
		$parts      = [];
		foreach ( $blocks as $block => $count ) {
			$parts[] = "$block ($count)";
		}
		echo implode( ", ", $parts );
	}

	private function getPostStats( $post_content ): array {

		// Match all blocks
		$blocks = parse_blocks( $post_content );
		$stats = [];
		$blockNames = array_map( function ( $transformation ) {
			return $transformation->from();
		}, $this->getBlocks() );
		foreach ( $blocks as $block ) {
			if ( in_array( $block['blockName'], $blockNames ) ) { // check if this block has a migration
				if ( isset( $stats[$block['blockName']] ) ) {
					$stats[$block['blockName']]++;
				} else {
					$stats[$block['blockName']] = 1;
				}
			}
		}

		return $stats;
	}

	private function getStats(): array {
		global $wpdb;
		$query   = "SELECT post_content, ID FROM " . $wpdb->posts . " WHERE 
		(post_content LIKE '%<!-- wp:%' OR post_content LIKE '%<!-- /wp:%') AND (post_status ='publish' or post_status ='private' or post_status ='draft' OR post_status = 'future')";
		$results = $wpdb->get_results( $query );

		$blocks = [];
		foreach ( $results as $row ) {
			$posts[ $row->ID ] = [];
			$stats             = $this->getPostStats( $row->post_content );
			foreach ( $stats as $block => $count ) {
				if ( ! isset( $blocks[ $block ] ) ) {
					$blocks[ $block ] = 0;
				}
				$blocks[ $block ] ++;
			}
		}

		ksort( $blocks );

		return $blocks;
	}

	/**
	 * @return BlockTransformation[]
	 */
	public function getBlocks(): array {
		if ( $this->blocks === null ) {
			$this->blocks = apply_filters( Plugin::FILTER_BLOCK_TRANSFORMATIONS, [
				//new CaptionTransformation(),
			] );
		}

		return $this->blocks;
	}

	public function transform( $post_content, $dryRun ): string {
		//$blocks = parse_blocks( $post_content );
		foreach ( $this->getBlocks() as $transformation ) {
			$from          = $transformation->from();
			$regex        = $this->getSelfclosingBlockRegex( $from );
			$post_content = preg_replace_callback( $regex, function ( $matches ) use ( $transformation ) {
				return call_user_func( [
					$transformation,
					'transform'
				], $matches[1], "" );
			}, $post_content );
			// @todo do it with enclosing blocks as well? no need at the moment
		}

		return $post_content;
	}

	private function getEnclosingBlockRegex( $blockName ) {
		$blockName = str_replace( "/", "\/", $blockName );
		return '/\<!--\s?wp:' . $blockName . '\s?-->((?s).*)\<!--\s?\/wp:' . $blockName . '\s?-->/miu';
	}

	private function getSelfclosingBlockRegex( $blockName ) {
		$blockName = str_replace( "/", "\/", $blockName );
		return '~<!-- wp:' . $blockName . ' (.*?) /-->~miu';
	}
}