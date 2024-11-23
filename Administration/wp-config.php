<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

// Really Simple SSL settings
define('RSSSL_KEY', 'W7fUF1SwE54dyTclCVYCR6etpgCpsLeVcBcskcLptFXf4myczZFeeB0SBNxSDAAv');
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);

/** WordPress Base Path */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Define WP_CONTENT_DIR */
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}

/** MySQL settings */
define('DB_NAME', getenv('DB_NAME') ?: 'getoutdoor_dk_db_Test');
define('DB_USER', getenv('DB_USER') ?: 'getoutdoor_dk');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'prk3mxbd');
define('DB_HOST', getenv('DB_HOST') ?: 'mysql48.unoeuro.com');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

/** WordPress Database Table prefix. */
$table_prefix = 'wp_';

/** Debug settings */
define('WP_DEBUG', true);                // Enable debug mode
define('WP_DEBUG_LOG', true);            // Log errors to wp-content/debug.log
define('WP_DEBUG_DISPLAY', false);       // Hide errors on frontend
@ini_set('display_errors', 1);           // Display errors
@ini_set('log_errors', 'On');
@ini_set('error_log', WP_CONTENT_DIR . '/debug.log'); // Debug log file

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

