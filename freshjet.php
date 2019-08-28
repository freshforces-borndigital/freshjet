<?php
/**
 * Plugin Name: Freshjet
 * Plugin URI:  https://github.com/freshforces-borndigital/freshjet
 * Description: Mailjet <code>wp_mail()</code> drop-in replacement.
 * Version:     0.5.5
 * Author:      Fresh Forces - Born Digital
 * Author URI:  https://fresh-forces.com/
 * License:     GPL-3.0 License
 * License URI: https://oss.ninja/gpl-3.0?organization=Fresh-Forces
 * Text Domain: freshjet
 *
 * @package Freshjet
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

load_plugin_textdomain( 'freshjet', false, basename( dirname( __FILE__ ) ) . '/languages' );

// identities constants.
define( 'FRESHJET_PLUGIN_VERSION', '0.5.5' );
define( 'FRESHJET_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'FRESHJET_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );

// libraries using composer.
require_once FRESHJET_PLUGIN_DIR . '/vendor/autoload.php';

require_once FRESHJET_PLUGIN_DIR . '/autoload.php';
