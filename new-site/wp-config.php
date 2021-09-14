<?php
define('WP_AUTO_UPDATE_CORE', 'minor');// This setting is required to make sure that WordPress updates can be properly managed in WordPress Toolkit. Remove this line if this WordPress website is not managed by WordPress Toolkit anymore.
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cmadvoca_cmadvocates' );

/** MySQL database username */
define( 'DB_USER', 'cmadvoca_cmallp' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Barizi@2021#' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'G1pFZE98RBKS36f9KYRoWXiyzX2iDnQM6F2qcJJCNIMqQCdSeqVZ2e7pMDtZhPRR');
define('SECURE_AUTH_KEY',  'XNsr1i5qT43923pCjZjoE8B9hPk7zrwuDx7ZhAPKJ0LpeyXBWVchIqGyB8M4cm4i');
define('LOGGED_IN_KEY',    'AdNxFoojKIHcMsyuCAa3So42Y85mge7lMdIFuLS9bHR6ZeK9A2tC86gIsF57LnjZ');
define('NONCE_KEY',        'AYIsoXTIhxh2yG5C2S96GV0nN4ZSpVxv28mHH4YBVR12MZvCD4eyRo5uVsGhiZed');
define('AUTH_SALT',        'zgATs04mIAFY2rhVI3bKHf7woUU3q8f92qVwRT9tyimdKjVb4YkQJMwMA6Qj1vxs');
define('SECURE_AUTH_SALT', 'dyF3rsjGEJfpGwm12IS36LewidJfZyMLYqAzCFj4SOdkSKG0Rw7P1DX2QMykdqKU');
define('LOGGED_IN_SALT',   'JjrlEOjNUrJ7v8a2jLCqKDc4peDQtyLv9xAzU8qliBLMlh2tKIAM8qSdOzlKnjfe');
define('NONCE_SALT',       'X0D4MafHApmRe9Aa1BjysPR2ENZkC05nq3My8EgU8FydORuxJ8kz2YmV5Bftulxh');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR',0755);
define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed externally by Installatron.
 * If you remove this define() to re-enable WordPress's automatic background updating
 * then it's advised to disable auto-updating in Installatron.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
