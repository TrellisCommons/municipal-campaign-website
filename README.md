# TrellisCommons Municipal Campaign Website

Bedrock-based WordPress multisite platform for TrellisCommons municipal campaign websites.

## Architecture

| Layer | Detail |
|---|---|
| Framework | [Bedrock](https://roots.io/bedrock/) (WordPress boilerplate) |
| WordPress | 7.0, installed via Composer |
| PHP | 8.4 |
| Multisite | Subdomain install |
| Theme | `twentytwentyfive` (scaffold — GPO theme in progress) |
| Deploys | GitHub Actions → SSH deploy script |

## Environments

| Environment | Domain | App root | Database |
|---|---|---|---|
| Production | `1.trelliscommons.ca`, `2.trelliscommons.ca` | `/var/www/municipal-campaign-website` | `wordpress` |
| Staging | `staging.trelliscommons.ca` | `/var/www/municipal-campaign-website-staging` | `wordpress_staging` |
| Local | `municipal-campaign-website.ddev.site` | `.ddev/` | DDEV managed |

## Local Development

```bash
# Install dependencies
composer install

# Start DDEV
ddev start

# Copy and configure env
cp .env.example .env
# Edit .env with your local DB credentials
```

## Deployment

### Staging
Auto-deploys on push to `main`.

### Production
Triggered manually via **Actions → Deploy to Production → Run workflow** in GitHub.

## Server Access

```bash
ssh root@138.197.128.199
```

## Key Files

| File | Purpose |
|---|---|
| `config/application.php` | Main WordPress + multisite config |
| `config/environments/staging.php` | Staging overrides (indexing disabled) |
| `config/environments/development.php` | Development overrides (debug on) |
| `.env.example` | Required environment variable reference |
| `scripts/deploy_action.sh` | Server-side deploy script |
| `.github/workflows/deploy_staging.yml` | Staging deploy workflow |
| `.github/workflows/deploy_prod.yml` | Production deploy workflow |

## Environment Variables

Copy `.env.example` to `.env` and fill in values. Required variables:

- `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`
- `WP_HOME` — full URL e.g. `https://1.trelliscommons.ca`
- `WP_SITEURL` — `${WP_HOME}/wp`
- `WP_ENV` — `production`, `staging`, or `development`
- `DOMAIN_CURRENT_SITE` — root domain of the multisite network
- Auth keys and salts — generate at [roots.io/salts.html](https://roots.io/salts.html)
