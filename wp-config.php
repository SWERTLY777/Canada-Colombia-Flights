<?php
if ( defined( 'WP_CLI' ) ) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

if (isset($_SERVER['HTTP_HOST'])) {
    $http_protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('WP_HOME', $http_protocol . '://' . $_SERVER['HTTP_HOST']);
    define('WP_SITEURL', $http_protocol . '://' . $_SERVER['HTTP_HOST']);
}

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
define( 'DB_NAME', 'app_37501' );

/** Database username */
define( 'DB_USER', 'app_37501' );

/** Database password */
define( 'DB_PASSWORD', '6ccd519d-8c98-4888-a0c4-b10e022816f9' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define('AUTH_KEY',         '%+7p[ $$|2vN$#M0>0=J/OSQlm/f#+mgv4`!W]?U(Bi&qcB2 Wd7JkZ]<B+FGX58');
define('SECURE_AUTH_KEY',  'FAw74uJeOWg|~^E MyUv7d8T_SVXH$DOIQ8*W9R-<PPjrF-Zj3kJI+R<O.-+7B;I');
define('LOGGED_IN_KEY',    'ss@w,:A</(k {T!{O]:X}&T;a-?$#.;Ovp[0!!ue~0{Xq4G-XP3>9Leg2%1/;NfU');
define('NONCE_KEY',        'M&12QrpFOl5F1s:x9x#mNGl;3uh}??TGLL!-]+w&:^@D?mMUj)=T&xJ=*U6_b*fw');
define('AUTH_SALT',        '>|-,ma+[0z@U,GCS%!1oA43A-S561p7IQCUTe%!OT+>(*]j,WhQ @*rI;A@N[80E');
define('SECURE_AUTH_SALT', 'E]9M8Aj5)2abS-_+7m9z4wi{A[!r1dG>AMS<mP}MjgL}&QOz:.-r*B`mfz[FJ}-(');
define('LOGGED_IN_SALT',   'Lo1<x/-16Mm5i8]?dk&/]j5+Y}iC}:mf/~v-H|?,G7;LN;*gx+uNluKNz1F=:Jt?');
define('NONCE_SALT',       '~/~@x<,093KmDk-g*Z-d7MVDKs-:fCh490H HMNvJU<0&lvAJ~+> qk$&~B %P1p');

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
