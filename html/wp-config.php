<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wibwabweb-com');

/** MySQL database username */
define('DB_USER', 'aq_wibwabweb-com');

/** MySQL database password */
define('DB_PASSWORD', 'wibwdo4gcz8');

/** MySQL hostname */
define('DB_HOST', 'dbs.arrowquick.net');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
define('SUBDOMAIN_INSTALL', true);
$base = '/';
define('DOMAIN_CURRENT_SITE', 'www.wibwabweb.com' );
define('PATH_CURRENT_SITE', $base );
define('SITE_ID_CURRENT_SITE', 1);
define('BLOGID_CURRENT_SITE', '1' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'a6dc0f317be240b260689f0722f99d7fefe194f7b8b97cf2e55697096d3266b7');
define('SECURE_AUTH_KEY', '95e63822db26be110429757dffebb7f5c68859d9399c443cb960853a956df33d');
define('LOGGED_IN_KEY', '40a2e637f79e0e8b86372385eafbe83c70818dd3f9660aea0c4b0a92181cd2f3');
define('NONCE_KEY', 'b5ec1385f2b023512843a069ac854b33a5f435f1afa15589af5f9bddfd44dceb');
define('AUTH_SALT', '85f659a47864f35c0550f68cf46a297025b764434d1f6a6b2fe81727709788c1');
define('LOGGED_IN_SALT', '44bee77f699151dd23cc7c476932ad27840b08ab16253dc3afb9ef0a70cd8f0b');
define('SECURE_AUTH_SALT', 'f652410266a540969c81b0eb4bd091364b1a767bfd7ff0daf11fdbfa65896ead');
define( 'NONCE_SALT', 'a3)xW;#T-(>-(GdbO@ch512S/| ,MvUi4z/sQkw|VXizEw&^]Gx%+nSqjGUIKVUP' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

//limit revisions in the db to 10 per post (autosave is not counted)
define('WP_POST_REVISIONS', 10);

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', 'aq-wibwabweb');

// Uncomment this to enable wp-content/sunrise.php support
// for the Domain Mapping plugin.
define( 'SUNRISE', 'on' );

define(EMPTY_TRASH_DAYS, 0);

// Turn on SSL. (see http://codex.wordpress.org/Administration_Over_SSL)
define('FORCE_SSL_LOGIN', true);
define('FORCE_SSL_ADMIN', true);

// ArrowQuick-specific definitions. --Eric@AQ
require_once dirname(__FILE__).'/wp-includes/aq-defs.php';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

// For production server, log PHP errors instead of displaying them.
if (!defined('WP_DEBUG')
or  !WP_DEBUG)
{
	// Suppress PHP errors.
	ini_set("display_errors", false);
	ini_set("html_errors", false);

	// Enable PHP error logging.
	ini_set("log_errors", true);
	ini_set("error_log",  dirname(dirname(__FILE__))."/logfiles/PHP_errors.log");

	// Disable repeated error logging.
	ini_set("ignore_repeated_errors", true);
	ini_set("ignore_repeated_source", true);
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
