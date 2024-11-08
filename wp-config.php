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
define( 'DB_NAME', 'qs_gen_dashboard' );

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
define( 'AUTH_KEY',         '/R^3R PI}3kDY2)2>ikerMNR.dSKI5!D} {lWG;bPk$Sg/Zq,02ZV./%Kiq??=7R' );
define( 'SECURE_AUTH_KEY',  '~{S}PU9UR?~k(sTmN,TwAJJNUlK4s6}0/LS~?kQlfoa122aAwG1HnaCMax/m!;{$' );
define( 'LOGGED_IN_KEY',    'gC4%HiLM7tRSj)P<J|dy!0Z={0//;0R0$.e&{G=Dpx>Fc[YhMtbwY#Y,D;ZmkPmf' );
define( 'NONCE_KEY',        'yy|oM]HX~>d_[;K~e<B%mc<.%zne!i/{O^;6~MWmkK4A:g_s[rnGS,(wI UCEh.>' );
define( 'AUTH_SALT',        '0VZ/;3Cto((gPZmQyA$6v@<e?>Pn!O7]iLo02}iyBli3>5h}}A<Ai2(S8B#V(T`j' );
define( 'SECURE_AUTH_SALT', '<M52?x9S2AJ;R.j*^Z)Y}1}piuuE[zAn@$(slAC,a:PG kc+t#qn(vb,=a-p`]CM' );
define( 'LOGGED_IN_SALT',   'nm1_O ADA_`S*np? ~d@-9DV>BW)7,t?1*:=W-Iw_7[hA&OZ~hx8o@4cyVyh#gc0' );
define( 'NONCE_SALT',       'r +ubqrDw*YYEg*DBSWMCbn%7e/Qui` W,P/`v3MZF$E%{M It~40#GLk>nKQbJc' );

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
