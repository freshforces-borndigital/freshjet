<?php
defined('ABSPATH') or die('Can\'t access directly');

// setting up admin area
require 'admin/Setup.php';

// setting up general purpose
require 'general/Setup.php';

// if api & secret key is not ready yet
if (
	!FRESHJET_API_KEY ||
	!FRESHJET_SECRET_KEY ||
	!FRESHJET_SENDER_EMAIL ||
	!FRESHJET_SENDER_NAME
) {
	return;
}

require 'general/Email.php';
require 'general/functions.php';
