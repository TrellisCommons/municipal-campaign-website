#!/usr/bin/env bash
# Bash Strict Mode: http://redsymbol.net/articles/unofficial-bash-strict-mode/
set -euo pipefail
IFS=$'\n\t'

echo "Web Application Deploy"

echo "::group::Checking configuration"

# Check required environment variables
######################################

echo "Check required environment variables"
COMMIT_SHA=${COMMIT_SHA:=$1}
SITE_DIR=${SITE_DIR:=$2}

if [ -z "${SITE_DIR:-}" ] || [ -z "${COMMIT_SHA:-}" ]; then
	echo "ERROR: SITE_DIR or COMMIT_SHA environment variables are not set."
	exit 1
fi

# CD into Directory
###################

echo "checking directory exists..."
DEPLOY_DIR="/var/www/${SITE_DIR}"
if [ ! -d "$DEPLOY_DIR" ]; then
  echo "ERROR: Deployment directory $DEPLOY_DIR does not exist."
  exit 1
fi
cd $DEPLOY_DIR
echo "currently working from $PWD"

echo "::endgroup::"

# Assert: no git files have been modified
#########################################

echo "::group::Checking for modified files"
if git diff --quiet && git diff --cached --quiet; then
	echo "No git files modified"
else
	echo "ERROR: Git files have been modified. Deployment aborted."
	exit 1
fi
echo "::endgroup::"

# Fetch and Checkout Commit
###########################

echo "::group::Checkout Commit $COMMIT_SHA"
git fetch origin
git switch -f --detach $COMMIT_SHA
echo "  Checkout Complete"
echo "::endgroup::"

# Update Dependencies
#####################

echo "::group::Updating Composer"
composer install --no-dev --optimize-autoloader
echo "  Dependencies: updated"
echo "::endgroup::"

# Reload php-fpm config
#################

echo "::group::Restarting PHP-FPM..."
sudo service php8.4-fpm reload
echo "  PHP-FPM: restarted"
echo "::endgroup::"

# Update WordPress Database
###########################

echo "::group::Updating WordPress Database"
./vendor/bin/wp core update-db
echo "  WordPress Database: updated"
echo "::endgroup::"

# Enable All Plugins
####################

echo "::group::Enabling All Plugins"

INACTIVE_PLUGINS=$(./vendor/bin/wp plugin list --field=name --status=inactive | tr '\n' ' ' | xargs)

if [ -n "$INACTIVE_PLUGINS" ]; then
	./vendor/bin/wp plugin activate $INACTIVE_PLUGINS
fi

echo "::endgroup::"

# Flush WordPress Cache
#######################

echo "::group::Flushing WordPress Cache"
./vendor/bin/wp cache flush
echo "  Cache: flushed"
echo "::endgroup::"

echo "Deployment complete"
