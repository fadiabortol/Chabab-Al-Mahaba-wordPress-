<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'chabab_al_mahaba' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '1v,_j}@Y!E-DA,q^~nr.?2O:Ndofl-ZtMe5R;-6BNn[Buu^l=#NI3t}9kZ6;8N4Q' );
define( 'SECURE_AUTH_KEY',  '/[ZQMAZ2~v{dB[J] /&AW,@q]:@6W5LSbGY+~lI~sXx D^~8`f1*paVP_-ZRp|.i' );
define( 'LOGGED_IN_KEY',    'F} U l.?.9%r_V[XngK_:TPv[`_t8joV#Sj5b;2< XzU*oO1+0PXyoA2(Azpq$Kw' );
define( 'NONCE_KEY',        'XzyH&fB)1cF((4.b*^mpkdjSu~pgFg@nC1MUry;v$cp2Uw~6x-0~AVsc,!lcby>_' );
define( 'AUTH_SALT',        'bv!o+eeW)44LhQdT gIRQZIHg~)[4h5Vu*5:bP^kiXO!>%C0*TNtgu1Gr%`*0CQT' );
define( 'SECURE_AUTH_SALT', 'PawtZn2lMbdPxOOrxDC/}91p1NH,M`-oJH$ih&B3-+-W.yV%@(jJ-u-jcEd,AXjs' );
define( 'LOGGED_IN_SALT',   '0CIoo9!FaPasu.7L$hVLIs-d>OFkEdHK,K5.e%Jb }pP!pIGA+D-nhO)b@?1*7i%' );
define( 'NONCE_SALT',       '=@HRfY2&rZN4;k5ZT*q&@H(GA+zp!@Tm3F}y`uKI]Z4&!3-6h_8EOBB]*G_WI7yJ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */

/* Add any custom values between this line and the "stop editing" line. */


define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', true);
@ini_set('display_errors', 0);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
