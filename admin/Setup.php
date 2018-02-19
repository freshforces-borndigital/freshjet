<?php
namespace Freshjet\Admin;

defined('ABSPATH') or die('Can\'t access directly');

class Setup
{
	private $_dir;
	private $_url;

	function __construct($run_setup)
	{
		$this->_dir = FRESHJET_DIR . '/admin';
		$this->_url = FRESHJET_URL . '/admin';

		if (!$run_setup) {
			return;
		}

		$this->_register_option_pages();
	}

	public function _register_option_pages()
	{
		acf_add_options_page([
			'page_title' => __('Freshjet', 'freshjet'),
			'menu_slug'  => 'freshjet',
			'icon_url'   => 'dashicons-email'
		]);

		require 'acf/acf-generated-code.php';
	}
}

new Setup(true);
