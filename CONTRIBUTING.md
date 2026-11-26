# Contributing

## Branching

`main` is the default branch and always reflects what is released (or about to be
released). Work on a feature branch and open a pull request against `main`.

## Commit messages

Releases and the changelog are generated from the commit history, so commit messages
follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>[optional scope][!]: <description>
```

While the plugin is below 1.0.0:

| Type | Effect on the version | Appears in changelog |
|---|---|---|
| `fix:` | patch (0.1.3 → 0.1.4) | yes, "Bug Fixes" |
| `feat:` | minor (0.1.3 → 0.2.0) | yes, "Features" |
| `feat!:` or `BREAKING CHANGE:` footer | minor as well, until 1.0.0 | yes, highlighted |
| `docs:`, `refactor:`, `chore:`, `ci:`, `build:`, `deps:`, `test:` | none | no |

A pull request that should trigger a release needs at least one `fix:` or `feat:`
commit. When squash-merging, the squash commit message itself has to be a
conventional commit - that is the message release-please reads.

### Which changes get `fix:` or `feat:`

Only changes that matter to someone using the plugin: anything in `public/` that
changes behaviour, the admin screens, the WP-CLI commands or the filters and actions
other code hooks into. Workflows, repository documentation and refactoring without a
visible change take a type that releases nothing. As a rule of thumb, a change
outside `public/` is almost never a `fix:`.

## Versions

Never edit version numbers by hand. `version.txt`, `CHANGELOG.md`, the `Version:`
header of `public/m2g.php` and the `Stable tag:` in `public/readme.txt` are
maintained by the release pipeline. Content changes to `public/readme.txt`
(description, tested-up-to) are done by hand; leave `Stable tag:` and the
`== Changelog ==` entries alone.

## Local development

Clone or symlink the repository into `wp-content/plugins/` and activate
"Migrate 2 Gutenberg - DEV". WordPress only finds plugin files one directory level
deep; the root `m2g.php` includes `public/m2g.php` and registers the activation
hooks against the file WordPress knows about.

## Checks

Every pull request runs `php -l` on PHP 8.2 to 8.5 (the plugin declares
`Requires PHP: 8.2`), packs the plugin and checks the payload. If you raise the PHP
requirement, change the plugin header, `public/readme.txt` and the `php-versions` in
`.github/workflows/pr.yml` in the same pull request.

## Security

Please don't report vulnerabilities in public issues - see [SECURITY.md](SECURITY.md).
