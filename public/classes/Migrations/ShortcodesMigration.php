<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Migrations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\Migration;
use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\ShortcodeTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Plugin;
use Palasthotel\WordPress\MigrateToGutenberg\Transformations\CaptionTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Transformations\VCColumnTextTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Transformations\VCColumnTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Transformations\VCInnerColumnTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Transformations\VCInnerRowTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Transformations\VCRowTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Transformations\VCSingleImageTransformation;
use Palasthotel\WordPress\MigrateToGutenberg\Transformations\VCMasonryMediaGridTransformation;

class ShortcodesMigration implements Migration {

	/**
	 * @var null | ShortcodeTransformation[]
	 */
	private $shortcodes = null;

	public function id(): string {
		return "shortcodes";
	}

	public function description() {
		$posts = $this->postIds();
		$stats = $this->getStats();

		echo "<p><strong>Affected posts: " . count( $posts ) . "</strong></p>";
		echo "<p><strong>Shortcodes: " . count( $stats ) . "</strong></p>";

		$handled   = [];
		$unhandled = [];
		$tags      = array_map( function ( $transformation ) {
			return $transformation->tag();
		}, $this->getShortcodes() );

		foreach ( $stats as $shortcode => $count ) {
			if ( in_array( $shortcode, $tags ) ) {
				$handled[] = "<nobr>$shortcode ($count)</nobr>";
			} else {
				$unhandled[] = "<nobr>$shortcode ($count)</nobr>";
			}
		}

		$handledList   = implode( ", ", $handled );
		$unhandledList = implode( ", ", $unhandled );
		echo "<p style='line-height: 2rem; max-width: 800px;'><strong>With transformer</strong><br/>$handledList</p>";
		echo "<p style='line-height: 2rem; max-width: 800px;'><strong>Further candidates</strong><br/>$unhandledList</p>";
	}

	public function postIds(): array {
		global $wpdb;

		$tags = array_map( function ( $transformation ) {
			$tag = $transformation->tag();

			return "post_content LIKE '%[$tag%' OR post_content LIKE '%[/$tag%'";
		}, $this->getShortcodes() );

		$likeTags = implode( " OR ", $tags );

		$migrationsTable = Plugin::instance()->dbMigrations->table;
		$query = "SELECT ID FROM " . $wpdb->posts . " WHERE 
		(($likeTags) OR ( ID IN (SELECT post_id FROM $migrationsTable))) 
		AND (post_status ='publish' or post_status ='private' or post_status ='draft' OR post_status = 'future')
		ORDER BY post_date desc
		";

		return $wpdb->get_col( $query );
	}

	public function analyze( $post_id ) {
		$post       = get_post( $post_id );
		$shortcodes = $this->getPostStats( $post->post_content );
		$parts      = [];
		foreach ( $shortcodes as $shortcode => $count ) {
			$parts[] = "$shortcode ($count)";
		}
		echo implode( ", ", $parts );
	}

	private function getPostStats( $post_content ): array {
		$prefix     = "";
		$shortcodes = [];

		// Match all shortcodes with attributes
		$matches = [];
		preg_match_all( "/\[" . $prefix . "[A-Za-z_\-]* /", $post_content, $matches );
		foreach ( $matches as $match ) {
			foreach ( $match as $item ) {
				$sc_name = str_replace( "[", "", trim( $item ) );
				if ( ! isset( $shortcodes[ $sc_name ] ) ) {
					$shortcodes[ $sc_name ] = 0;
				}
				$shortcodes[ $sc_name ] ++;
			}
		}
		// Match all shortcodes without attributes
		$matches = array();
		preg_match_all( "/\[" . $prefix . "[A-Za-z_\-]*]/", $post_content, $matches );
		foreach ( $matches as $match ) {
			foreach ( $match as $item ) {
				$sc_name = str_replace( "[", "", trim( $item ) );
				$sc_name = str_replace( "]", "", trim( $sc_name ) );
				if ( ! isset( $shortcodes[ $sc_name ] ) ) {
					$shortcodes[ $sc_name ] = 0;
				}
				$shortcodes[ $sc_name ] ++;
			}
		}
		// Match all closing shortcodes
		$matches = array();
		preg_match_all( "/\[\/" . $prefix . "[A-Za-z_\-]*]/", $post_content, $matches );
		foreach ( $matches as $match ) {
			foreach ( $match as $item ) {
				$sc_name = str_replace( "[/", "", trim( $item ) );
				$sc_name = str_replace( "]", "", trim( $sc_name ) );
				if ( ! isset( $shortcodes[ $sc_name ] ) ) {
					$shortcodes[ $sc_name ] = 0;
				}
				$shortcodes[ $sc_name ] ++;
			}
		}

		return $shortcodes;
	}

	private function getStats(): array {
		global $wpdb;
		$query   = "SELECT post_content, ID FROM " . $wpdb->posts . " WHERE 
		(post_content LIKE '%[%' OR post_content LIKE '%[/%') 
		AND (post_status ='publish' or post_status ='private' or post_status ='draft' OR post_status = 'future')
		";
		$results = $wpdb->get_results( $query );

		$shortcodes = [];
		foreach ( $results as $row ) {
			$posts[ $row->ID ] = [];
			$stats             = $this->getPostStats( $row->post_content );
			foreach ( $stats as $shortcode => $count ) {
				if ( ! isset( $shortcodes[ $shortcode ] ) ) {
					$shortcodes[ $shortcode ] = 0;
				}
				$shortcodes[ $shortcode ] ++;
			}
		}

		ksort( $shortcodes );

		return $shortcodes;
	}

	/**
	 * @return ShortcodeTransformation[]
	 */
	public function getShortcodes(): array {
		if ( $this->shortcodes === null ) {
			$this->shortcodes = apply_filters( Plugin::FILTER_SHORTCODE_TRANSFORMATIONS, [
				new CaptionTransformation(),
				new VCRowTransformation(),
				new VCInnerRowTransformation(),
				new VCColumnTransformation(),
				new VCInnerColumnTransformation(),
				new VCSingleImageTransformation(),
				new VCColumnTextTransformation(),
				new VCMasonryMediaGridTransformation(),
			] );
		}

		return $this->shortcodes;
	}

	public function transform( $post_content, $dryRun ): string {

		foreach ( $this->getShortcodes() as $transformation ) {
			$tag          = $transformation->tag();
			$regex        = get_shortcode_regex( array( $tag ) );
			$post_content = preg_replace_callback( '/' . $regex . '/', function ( $matches ) use ( $transformation ) {
				return call_user_func( [
					$transformation,
					'transform'
				], shortcode_parse_atts( $matches[3] ), $matches[5] );
			}, $post_content );
		}

		return $post_content;
	}
}
