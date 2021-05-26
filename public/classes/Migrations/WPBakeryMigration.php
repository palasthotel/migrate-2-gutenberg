<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Migrations;


use Palasthotel\WordPress\MigrateToGutenberg\Interfaces\Migration;

class WPBakeryMigration implements Migration {

	public function id(): string {
		return "wpbakery";
	}

	public function description() {
		$posts = $this->postIds();
		$stats = $this->getStats();

		echo "<p><strong>Affected posts: ".count($posts)."</strong></p>";
		echo "<p><strong>Shortcodes: ".count($stats)."</strong></p>";

		$elements = [];
		foreach (  $stats as $shortcode => $count ) {
			$elements[] = "<nobr>$shortcode ($count)</nobr>";
		}
		echo "<p style='line-height: 2rem; max-width: 800px;'>".implode(", ", $elements)."</p>";
	}

	public function postIds(): array {
		global $wpdb;
		$query = "SELECT ID FROM " . $wpdb->posts . " WHERE 
		(post_content LIKE '%[vc_%' OR post_content LIKE '%[/vc_%') 
		AND (post_status ='publish' or post_status ='private' or post_status ='draft' OR post_status = 'future')
		";

		return $wpdb->get_col( $query );
	}

	public function analyze( $post_id ) {
		$post = get_post($post_id);
		$shortcodes = $this->getPostStats($post->post_content);
		$parts = [];
		foreach ($shortcodes as $shortcode => $count){
			$parts[] = "$shortcode ($count)";
		}
		echo implode(", ", $parts);
	}

	private function getPostStats($post_content): array{
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
		(post_content LIKE '%[vc_%' OR post_content LIKE '%[/vc_%') 
		AND (post_status ='publish' or post_status ='private' or post_status ='draft' OR post_status = 'future')
		";
		$results = $wpdb->get_results( $query );

		$shortcodes = [];
		foreach ( $results as $row ) {
			$posts[ $row->ID ] = [];
			$stats = $this->getPostStats($row->post_content);
			foreach ($stats as $shortcode => $count){
				if ( ! isset( $shortcodes[ $shortcode ] ) ) {
					$shortcodes[ $shortcode ] = 0;
				}
				$shortcodes[ $shortcode ] ++;
			}
		}

		ksort( $shortcodes );

		return $shortcodes;
	}

	public function run( $post_id, bool $dryRun ) {
		// TODO: Implement run() method.
	}
}