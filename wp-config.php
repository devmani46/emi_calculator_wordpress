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
define( 'DB_NAME', 'emi' );

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
define( 'AUTH_KEY',         'z`irDcgi>_V1K`(RIA7t&~Yu5{LDWCHgj+X*6]x,x|f~ir[G`*f[)=w*){EvhmEG' );
define( 'SECURE_AUTH_KEY',  '/?T7zjr?0&0%?pz7$wD:1w~b?U_`]weCU%m)o7|ieR@xY_Q`$DS1XiOSRz_H{wSN' );
define( 'LOGGED_IN_KEY',    'o&BPc8;U# $~oEBHShkiZfq}.NhNQ;$`AwfCLH@;N{!J8h};c;F6kn 0>zqhI6mf' );
define( 'NONCE_KEY',        '!w30FTEWMw4O1SNvRFf8D*}LWt&MB`4=]0k=-v$3n*DV.jsU}6pa/^U!);N]?BZM' );
define( 'AUTH_SALT',        '[@!m(b~RXueiKB7J1:jwld_asO93,ktqv:n/:}zI[=71gy*zfqqt&hMhxbCvXYf0' );
define( 'SECURE_AUTH_SALT', '9VMB3_(xr.P 5hMSqlKPIkP$4`r4yGN[}0bg4L 8A[i1v_Qds2ujbGS*/C(</h9o' );
define( 'LOGGED_IN_SALT',   '=%JIA=>K^c[9?tu=uY.C9cCt9,$MDeD;6h=PvgfK]$],md+#HfVX:zYb<i?1s&.g' );
define( 'NONCE_SALT',       '4ep>32!-84w05 yGOQyv OY43w}ldQ^-l#NfaBk_9}1{~ ;cG@5LnS)M81]9cYpK' );

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
