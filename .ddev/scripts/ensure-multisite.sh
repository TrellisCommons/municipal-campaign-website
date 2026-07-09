#!/bin/bash
set -e

ENV_FILE=".env"

# Add multisite vars to .env if missing
if ! grep -q "DOMAIN_CURRENT_SITE" "$ENV_FILE" 2>/dev/null; then
  echo "Adding multisite config to .env..."
  cat >> "$ENV_FILE" << 'ENV_VARS'
MULTISITE='true'
WP_ALLOW_MULTISITE='true'
SUBDOMAIN_INSTALL='true'
DOMAIN_CURRENT_SITE='municipal-campaign-website.ddev.site'
PATH_CURRENT_SITE='/'
SITE_ID_CURRENT_SITE='1'
BLOG_ID_CURRENT_SITE='1'
ENV_VARS
fi

# Check if multisite is actually installed in the DB; if not, install it
if ! ddev wp site list > /dev/null 2>&1; then
  echo "Multisite not installed yet — running multisite-install..."
  ddev wp core multisite-install \
    --url=https://municipal-campaign-website.ddev.site \
    --title="Municipal Campaign" \
    --admin_user=admin \
    --admin_password=admin \
    --admin_email=admin@example.com \
    --subdomains || echo "Multisite install skipped or already partially configured — check manually if needed."
fi
