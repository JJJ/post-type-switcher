# Post Type Switcher contributor guidance

## Compatibility

- Preserve PHP 8.0 and WordPress 6.2 compatibility unless a dedicated pull
  request explicitly changes the published minimums.
- Preserve the classic editor, block editor, Quick Edit, Bulk Edit, and WPML
  integration behavior.
- Treat post ownership, capabilities, nonces, post-type registration, and post
  metadata as security-sensitive and compatibility-sensitive.
- Preserve the public class methods, filters, actions, request field names,
  script handles, and stored `pts_original_type` and `pts_previous_type` keys.

## Tests and generated assets

- Add regression coverage before changing observed PHP or JavaScript behavior.
- Exercise users with different post and target-type capabilities when changing
  authorization behavior.
- Keep `src/` and the generated `assets/js/block.js` and
  `assets/js/block.asset.php` files synchronized.
- Use npm 10 or newer as the canonical JavaScript package manager. Keep the
  committed npm and pnpm lockfiles equivalent when dependency changes require
  both to move, and reject unrelated dependency-graph drift.
- Run `composer test`, `composer phpstan`, and `npm run build:check` before
  requesting review.

## Automation and releases

Follow the portfolio safety boundaries. AI-authored implementation must remain
a draft pull request and cannot modify workflows, release policy, ownership,
security policy, dependencies, or this file without a specific maintainer
decision. Releases require the protected `wordpress.org` environment and an
exact commit from `master`.
