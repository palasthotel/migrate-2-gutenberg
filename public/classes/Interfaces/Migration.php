<?php


namespace Palasthotel\WordPress\MigrateToGutenberg\Interfaces;


interface Migration {
	// about the migration in general
	public function id(): string;
	public function description();

	// find info about posts
	public function postIds(): array;
	public function analyze($post_id);

	// run the migration
	public function transform(string $post_content, bool $dryRun): string;
}