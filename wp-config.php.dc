<?php

// JA Tweaks
define ( 'BP_DEFAULT_COMPONENT', 'profile' );

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
$env = json_decode(file_get_contents("/home/dotcloud/environment.json"), true);
//echo "Application Name: {$env['DOTCLOUD_PROJECT']}\n";
$dbhost = $env['DOTCLOUD_DB_MYSQL_HOST'];
$dbuser = $env['DOTCLOUD_DB_MYSQL_LOGIN'];
$dbpass = $env['DOTCLOUD_DB_MYSQL_PASSWORD'];
$dbport = $env['DOTCLOUD_DB_MYSQL_PORT'];

#echo "Name: $dbhost, $dbuser, $dbpass, $dbport\n";


define('DB_NAME', 'journalismaccel');

/** MySQL database username */
define('DB_USER', $dbuser);

/** MySQL database password */
define('DB_PASSWORD', $dbpass);

/** MySQL hostname */
define('DB_HOST', "$dbhost:$dbport");
define('DB_PORT', $dbport);

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
define('AUTH_KEY',         'eVKo+M9+%HP!!lwNS/|cL^Ma(]M e}E2 &[C>>vXkcihf((p5TFR31N*2.S;B$k?');
define('SECURE_AUTH_KEY',  '%L6kWtkKF*,4qM`-wcy9! 1v$Kx7#jt&4x;-,E4RtQHkYjva+wT*:=Br&|Ryzg:w');
define('LOGGED_IN_KEY',    'HecbDmZ0]ZQ8OZyVW_`R9f8*Oa5id?j2K2ce8;xSEFy6o=$M8,Ua1G#d)6y{m}%d');
define('NONCE_KEY',        'xYGQV^?zVgZPoc`8z7]|HNPn2nyWczh|Ob2;&rOf7yxHb]MY =}eZR0+(fQ0Q8cv');
define('AUTH_SALT',        '$qsB5In--QlCD4r*|hQ4,&B*kQGaOoi=Tg0~]imFGWPj.wN,M7Q[rS-n=6cWMcdw');
define('SECURE_AUTH_SALT', '1nc?n{B&|`F!:2qn3/%z`]%80PP95rnwb?2YfWa11%y(`N/9,y`vALQ(4BKPYbGL');
define('LOGGED_IN_SALT',   'iePApwTy75o|J(9[(*K$=&%`?wg`Ajq:%Qmc&na.;{Wq$X]r0%/&2Pf:+0+7b3E+');
define('NONCE_SALT',       '.`M]n33m>G*r?)XzF+nAu-._Oe-al0EFV]YeB<jC*ExAh3:k5^<W1%|om+3|&qbw');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define( 'BP_DISABLE_ADMIN_BAR', true );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


