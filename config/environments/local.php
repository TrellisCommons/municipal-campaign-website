<?php

/**
 * Configuration overrides for WP_ENV === 'local'
 */

use Roots\WPConfig\Config;

// Allow plugin/theme install and updates from wp-admin in local only.
Config::define('DISALLOW_FILE_MODS', false);
