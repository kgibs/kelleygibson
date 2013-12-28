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
define('DB_NAME', 'inkling_muniSERVDev');

/** MySQL database username */
define('DB_USER', 'inkling_dev');

/** MySQL database password */
define('DB_PASSWORD', 'e;H7{1soSzH+');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

// ** PayPal API settings - Used by muniserv theme ** //
define('MS_PAYPAL_LIVE', 'true');
define('MS_PAYPAL_USERNAME', 'susanshannon_api1.xplornet.com');
define('MS_PAYPAL_PASSWORD', 'JGC6P6T9BNB25TCW');
define('MS_PAYPAL_SIGNATURE', 'ADq7iZGk1Pbp7Vm8xo6h6xP7G2llAyujuvTmYqtLPYSJQP4W8Hh79Vlg');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '<fVj }TJN^`Y+G_6vEq|UFZh)|K.$`@[ousn1O%(i({|y7Pq[>F6->--LB#lYr:r');
define('SECURE_AUTH_KEY',  'jBn3pyb>|U|@v/WGzbH4Cw=0iV_~IC}ohF{):}]N%)JU-_hWWK-%Z$eu~b_0*ne`');
define('LOGGED_IN_KEY',    '/QO0OlB2-HZLFb` 226wFIWbHy#Wkt-(z [E%uF|79^kkp}-{$Jx@{xTl~:jb$.K');
define('NONCE_KEY',        'zw}J0[}s{]w#=8 W-6O,uk8-ukw=|d=)1DF.dU%^Gf[hHo7P5OBPir|(87$q-SFD');
define('AUTH_SALT',        '22C6Z6EQhJH*#b9~{jOg:;;TSbs-5*$4|3Fe|J^fa2kB=IbmRkRrcky7a~e$X+-^');
define('SECURE_AUTH_SALT', '6#?F[wN~(t5#(bX5?@Fqai*0q#m-)L-uQY6clwH.yr=_@,8MS83awKU{U_b s<Ii');
define('LOGGED_IN_SALT',   '{ZbC*Nvq~P@GSAzxnEZ,+{wd`2ZR9cDyx/q~-tf[ /B-sh[qZ$>rR:v_y!O WnxB');
define('NONCE_SALT',       'cn/r8D]dua4M#c<;x2nI:ulqGW)+,Hud5k[Xvg>[o_K0szgIgrY#435Y_L;@sPnj');

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
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
