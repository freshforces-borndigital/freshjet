<?php
/**
 * Plugin Name: Freshjet
 * Plugin URI:  https://github.com/freshforces-borndigital/freshjet
 * Description: Mailjet implementation for Fresh Forces. Including `wp_mail()` drop-in replacement.
 * Version:     0.2.3
 * Author:      Fresh Forces
 * Author URI:  https://github.com/freshforces-borndigital/
 * License:     MIT
 * License URI: https://oss.ninja/mit?organization=Fresh%20Forces
 * Text Domain: freshjet
 */
defined('ABSPATH') or die('Can\'t access directly');

load_plugin_textdomain('freshjet', false, basename( dirname( __FILE__ ) ) . '/languages');

// this plugin requires ACF Pro
if (!function_exists('acf_add_options_page')) {
	return;
}

// identities constants
define('FRESHJET_VERSION', '0.2.2');
define('FRESHJET_URL', rtrim(plugin_dir_url(__FILE__), '/' ));
define('FRESHJET_DIR', rtrim(plugin_dir_path(__FILE__), '/' ));

// libraries using composer
require_once FRESHJET_DIR . '/vendor/autoload.php';

// libraries non-composer (could be from dist folder)
// require_once them here

require_once FRESHJET_DIR . '/autoload.php';
