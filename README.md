# Municipal Campaign Website (DDEV Setup Branch)

This branch exists only for local DDEV setup wiring.
It intentionally stays small and focused.

## What This Branch Contains

- `.ddev/config.yaml` for local DDEV project configuration
- `web/wp-config.php` to load DDEV settings when running in DDEV

## Local Usage

1. Start the project:

```bash
ddev start
```

2. Open the local site URL shown by:

```bash
ddev describe
```

3. Stop the project when done:

```bash
ddev stop
```

## Notes

- This branch is not the CI/CD branch.
- Deployment workflow changes belong on separate branches.# TrellisCommons Municipal Campaign Website

This repository contains the Bedrock-based WordPress multisite platform used for TrellisCommons municipal campaign sites.

## Stack Overview

| Layer | Detail |
|---|---|
| Framework | [Bedrock](https://roots.io/bedrock/) |
| WordPress | 7.0 (managed via Composer) |
| PHP | 8.4 |
| Multisite | Subdomain network |
| Theme | `twentytwentyfive` scaffold (campaign theme in progress) |
| Deploys | GitHub Actions to SSH deploy script |

## Environment Map

| Environment | Domains | App root | Database |
|---|---|---|---|
| Production | `1.trelliscommons.ca`, `2.trelliscommons.ca` | `/var/www/municipal-campaign-website` | `wordpress` |
| Staging | `staging.trelliscommons.ca` | `/var/www/municipal-campaign-website-staging` | `wordpress_staging` |
| Local (DDEV) | `municipal-campaign-website.ddev.site` | local checkout | DDEV-managed |

## Local Setup (DDEV)

### Prerequisites
- DDEV installed and running
- Docker installed
- PHP 8.4 compatible tooling

### First-time setup
```bash
cp .env.example .env
ddev start
composer install
```

### Useful local commands
```bash
composer lint
composer test
ddev describe
```

## CI/CD Summary

- CI runs on pull requests and validates lint, tests, and theme build.
- Staging deploy runs on push to `main` (and can be triggered manually).
- Production deploy is manual-only via GitHub Actions.

## Key Files

| File | Purpose |
|---|---|
| `config/application.php` | Shared WordPress and multisite configuration |
| `config/environments/development.php` | Local/dev environment overrides |
| `config/environments/staging.php` | Staging environment overrides |
| `.env.example` | Environment variable template |
| `scripts/deploy_action.sh` | Server-side deploy process |
| `.github/workflows/ci.yml` | Pull request validation pipeline |
| `.github/workflows/deploy_staging.yml` | Staging deployment workflow |
| `.github/workflows/deploy_prod.yml` | Production deployment workflow |

## Environment Variables

Create `.env` from `.env.example` and define these at minimum:

- `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`
- `WP_HOME` (for example, `https://1.trelliscommons.ca`)
- `WP_SITEURL` (`${WP_HOME}/wp`)
- `WP_ENV` (`production`, `staging`, or `development`)
- `DOMAIN_CURRENT_SITE` (multisite network root domain)
- Auth keys and salts from [roots.io/salts.html](https://roots.io/salts.html)
