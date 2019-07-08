<?php
/**
 * Check Freshjet backward compatibility
 *
 * @package Freshjet
 */

namespace Freshjet;

/**
 * Setup Freshjet backward compatibility
 */
class Compatibility {
	/**
	 * Settings meta key
	 *
	 * @var string $options_key
	 */
	private $options_key = 'freshjet_options';

	/**
	 * Setup actions & filters
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'check_meta' ] );
	}

	/**
	 * Check Freshjet meta keys
	 */
	public function check_meta() {
		$opts = get_option( $this->options_key );
		$opts = empty( $opts ) ? [] : $opts;

		$public_key = isset( $opts['public_key'] ) ? $opts['public_key'] : '';
		$secret_key = isset( $opts['secret_key'] ) ? $opts['secret_key'] : '';

		$sender_name  = isset( $opts['sender_name'] ) ? $opts['sender_name'] : '';
		$sender_email = isset( $opts['sender_email'] ) ? $opts['sender_email'] : '';

		if (
			! empty( $public_key ) &&
			! empty( $secret_key ) &&
			! empty( $sender_email ) &&
			! empty( $sender_name )
		) {
			return;
		}

		if ( empty( $public_key ) ) {
			$old_value          = get_option( 'options_freshjet__api_key' );
			$opts['public_key'] = $old_value;
		}

		if ( empty( $secret_key ) ) {
			$old_value          = get_option( 'options_freshjet__secret_key' );
			$opts['secret_key'] = $old_value;
		}

		if ( empty( $sender_name ) ) {
			$old_value           = get_option( 'options_freshjet__sender_name' );
			$opts['sender_name'] = $old_value;
		}

		if ( empty( $sender_email ) ) {
			$old_value            = get_option( 'options_freshjet__sender_email' );
			$opts['sender_email'] = $old_value;
		}

		// Update the value.
		update_option( $this->options_key, $opts );
	}
}
