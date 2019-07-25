<?php
/**
 * Autoloading
 *
 * @package Freshjet
 */

namespace Freshjet;

defined( 'ABSPATH' ) || die( "Can't access directly" );

// setup classes.
require __DIR__ . '/class-setup.php';
require __DIR__ . '/class-compatibility.php';

// init classes.
new Setup();
new Compatibility();

// stop if api & secret key is not ready yet.
if (
	! FRESHJET_PUBLIC_KEY ||
	! FRESHJET_SECRET_KEY ||
	! FRESHJET_SENDER_EMAIL ||
	! FRESHJET_SENDER_NAME
) {
	return;
}

require 'class-email.php';
require 'functions.php';
