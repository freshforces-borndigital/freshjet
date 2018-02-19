<?php
namespace Freshjet\General;

defined('ABSPATH') or die('Can\'t access directly');

class Setup
{
	private $_dir;
	private $_url;

	function __construct($run_setup)
	{
		$this->_dir = FRESHJET_DIR . '/general';
		$this->_url = FRESHJET_URL . '/general';

		if (!$run_setup) {
			return;
		}

		$api_key      = get_field('freshjet__api_key', 'option');
		$secret_key   = get_field('freshjet__secret_key', 'option');
		$sender_email = get_field('freshjet__sender_email', 'option');
		$sender_name  = get_field('freshjet__sender_name', 'option');

		define('FRESHJET_API_KEY', $api_key);
		define('FRESHJET_SECRET_KEY', $secret_key);
		define('FRESHJET_SENDER_EMAIL', $sender_email);
		define('FRESHJET_SENDER_NAME', $sender_name);
	}
}

new Setup(true);
