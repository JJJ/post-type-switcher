# Security policy

## Reporting a vulnerability

Do not open a public issue for a suspected vulnerability. Use
[GitHub private vulnerability reporting](https://github.com/JJJ/post-type-switcher/security/advisories/new).
If that is unavailable, contact the repository owner privately through their
GitHub profile.

Include the affected version, security impact, minimum reproduction, required
WordPress configuration, and sanitized evidence. Never include credentials,
personal information, or production database contents.

## Supported versions

Security fixes are normally applied to the current release line. Older releases
may be unsupported when WordPress or PHP compatibility makes a safe backport
impractical.

## Automation and releases

GitHub Actions, dependency compromise, WordPress.org credentials, release tags,
generated assets, and unauthorized publication are security-sensitive. Release
credentials belong only in protected deployment jobs and must never be exposed
to pull request or AI implementation code.
