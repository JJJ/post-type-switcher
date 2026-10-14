# Contributing

Thanks for helping maintain Post Type Switcher.

## Before changing behavior

Describe the observable behavior, compatibility expectations, and acceptance
criteria in a GitHub issue. Report suspected vulnerabilities privately through
[GitHub Security Advisories](https://github.com/JJJ/post-type-switcher/security/advisories/new).

## Pull requests

- Keep each pull request focused and reversible.
- Add regression coverage for behavior changes and bug fixes.
- Preserve PHP 8.0 and WordPress 6.2 compatibility.
- Check the classic editor, block editor, Quick Edit, and Bulk Edit paths when
  changing user-visible behavior.
- Identify capability, nonce, dependency, generated-asset, automation, and
  release implications.
- Do not commit credentials, build caches, development databases, or generated
  release ZIP files.
- Run `composer test`, `composer phpstan`, and `npm run build:check` before
  requesting review.
- Wait for every required check and resolve review conversations before merge.

AI-assisted contributions are welcome, but the contributor remains responsible
for understanding and validating the result.

## Development requirements

The plugin and its Composer development toolchain require PHP 8.0 or newer.
Production Composer installs should omit development dependencies.

The block-editor build requires Node.js 22 or newer and npm 10 or newer. Use
`npm ci` so local builds use the versions recorded in `package-lock.json`.
