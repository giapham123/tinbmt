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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'csl' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         'RE`wB%,G:Gw[#=q>g*@$]HfUxcQXR&[216HSN#[^uG`7exw4`H$R4wN{ZJ}~V@Db' );
define( 'SECURE_AUTH_KEY',  'eAd}gko3HI}z_xpS,A/&{M=9D/Y kSDpcmg!(mZhKv!Hq{ss_/dK,)lPhW&.[Fk-' );
define( 'LOGGED_IN_KEY',    'p8L(0(&{-I9c-Wp:bnx7e<UjtR&Yk/:,YE4#rO7Z1YDtnU$ V7,iIZxtsv?s7SSD' );
define( 'NONCE_KEY',        '7){]x[ YQCIASJ/eR|^FdQXR.flmJi`,RI%j`>-N.BSaBN2PS2CK;g Uw1?5/.0/' );
define( 'AUTH_SALT',        'al6*&{TAGoBu^JMj|!|XL]oo.%huV4W68|T4r0UP#9[1*,/n2o4H,2+aYBa$)0BF' );
define( 'SECURE_AUTH_SALT', 'uTu[w@sJaEzbITR[404/:m10w%JEH#mhs(;SdO;:(V8=?f_w0jx%~l4{!(BJC8_|' );
define( 'LOGGED_IN_SALT',   'N9_PFQqZ,i1]Fj$4RWJ$Ve-FDv60~EXyUZ=hRQ).<Z;I5A|Cs[4`%9Gj`$%@}|S#' );
define( 'NONCE_SALT',       'Ho@zEF6taMRbK#yq(nyfbWx(p.i02,80m(W8@#/g(QMmnACD X>)h?1FJo8ylDi2' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
// define( 'WP_DEBUG', false );
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

//define( 'WP_DEBUG', true );
//define( 'WP_DEBUG_LOG', true );
//define( 'WP_DEBUG_DISPLAY', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

// define('WP_HOME', 'http://localhost:8888/csl/');
// define('WP_SITEURL', 'http://localhost:8888/csl');
