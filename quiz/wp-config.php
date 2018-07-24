<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'proamtv_wp9');

/** MySQL database username */
define('DB_USER', 'proamtv_wp9');

/** MySQL database password */
define('DB_PASSWORD', 'X(Flgo^[IcGe#@ofH5.36[[1');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'TNzHH1sZwwgXkC5s9H53wbclyS5GqnJTkNYEt5VQItXAoZwoSNhQ0a99TBI2BaN1');
define('SECURE_AUTH_KEY',  '8RpSp1kZEvTxjz4NJ2Ur5kBOX6RrDTufE0oCDVaKrjy89q4srDp6EhzbOMaMbnng');
define('LOGGED_IN_KEY',    'iQvl8N5t5UuoHzAtLOLzn28ZZzpRAkUvJk8aoXTkUKgRaPqt2hV9Si0d2xvxpgpb');
define('NONCE_KEY',        'tOUzj9t15RfzOEc3wO2JGeMjFDsddLL2HyEcQr4nb1kRAkY9APSY0DZdpGWfbeIX');
define('AUTH_SALT',        'twkBBaix4Uczz0bLaLMCy2Ou6XvFXtndWezZYNonULakUiPQs0zXOcVl7hmJ5ibj');
define('SECURE_AUTH_SALT', '23Jrbu3n7jmNTIMSXdoF0KHHMUZ1SoC5eIbFhjTAU4Z91YRQnIXGDw0QJJKZG70F');
define('LOGGED_IN_SALT',   'pI5Cn0DmrNxNWxhAHOGj9N258Bq3j897fJ7B1WIGsa94BDZwRXGC74hKO2tvponh');
define('NONCE_SALT',       'HTs5BB87lGbz72r4kEzPi1gFW9sWRR2ofFZzgYA4GgK26f8BoBPqkq3K6tUjaMjD');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
