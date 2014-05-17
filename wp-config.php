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
define('DB_NAME', 'llvtest');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'yj/oTd*]8d Y%HBsgMN/dx j5 tZt%J7~W@zer`Jsx,#(lAj/@3cR=ZE[.KQo=5-');
define('SECURE_AUTH_KEY',  '|zG?$ctpFXN%>=i_^GwY8]@:eH.)zQmZt%-jz|_-ua^1O[K7`{%z[HuxiL7FU@FJ');
define('LOGGED_IN_KEY',    ']pQM4<RE%s)(_a|=[8Fs4qi~,%1kd7jiD->F3O!xZm*qN|m&Gq2C.0NPQUU^/S.^');
define('NONCE_KEY',        ':N~Eg_N`0W@32$H=9?(0DDq/$3CH}5JC4V4j;`(RRR2y[A<dH_PO]`e;O^ |V0` ');
define('AUTH_SALT',        '=yIl4N^QF5mH-mIiCow,Nl52[Kl%%dsr/#1O1-J*0k:8b@Fx|.vB4o_R~8Dy[aue');
define('SECURE_AUTH_SALT', ',(S}X]tJ!HkPIBqTk1XH/%+1!gh_zf6n[JDL/x#Xn`t1seN4cut+vnM~C.Hu{-o(');
define('LOGGED_IN_SALT',   '(!9f3?n4dm_6p+^x7FPf:w`KwhHb+>2f}]>lw@Y^8z;A]-8n)tAOrjw{bg^M.E*5');
define('NONCE_SALT',       'b MtqKhIXIwu+|O1s5KD1])5Vw{JE<6c]PMp%gu-D#6FxD8C9[L.cjV5:[on<RU*');

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
