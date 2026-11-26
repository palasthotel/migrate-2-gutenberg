=== Migrate 2 Gutenberg ===
Contributors: palasthotel
Tags: gutenberg, migration, shortcodes, wpbakery, blocks
Requires at least: 5.0
Tested up to: 7.2
Requires PHP: 8.2
Stable tag: 0.1.3
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Migrate pre Gutenberg contents, such as WPBakery Page Builder shortcodes, to Gutenberg blocks.

== Description ==

Migrate 2 Gutenberg rewrites the content of posts that still use shortcodes (for
example from the WPBakery Page Builder) into Gutenberg blocks.

* Extendable for different migration sources and destinations
* A backup of every migrated post, restorable at any time
* WP-CLI integration: `wp m2g stats`, `wp m2g migrate`, `wp m2g rollback`
* Migration analysis: which contents will be affected and why

The overview lives in Tools → Migrate to Gutenberg and needs the `manage_options`
capability.

Developers add their own transformations with the `m2g_shortcode_transformations`
and `m2g_block_transformations` filters - see the README on GitHub:
https://github.com/palasthotel/migrate-2-gutenberg

== Installation ==

1. Download `migrate-2-gutenberg.zip` from the latest GitHub release and upload it
   in Plugins → Add New → Upload Plugin.
2. Activate the plugin.
3. Open Tools → Migrate to Gutenberg, check the diff of a post, then migrate it, or
   run `wp m2g migrate`.

== Changelog ==

= 0.1.3 =
* Last version before the release pipeline. See CHANGELOG.md on GitHub for later versions.
