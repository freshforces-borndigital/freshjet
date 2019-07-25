<?php
/**
 * Functions wrapper
 *
 * @package Freshjet
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

use Freshjet\Email;

if ( ! function_exists( 'wp_mail' ) ) {
	/**
	 * Currently we limit the default parameters support:
	 *
	 * $recipients: supported
	 * $subject: supported
	 * $body: supported
	 * $headers: not-supported yet
	 * $attachments: supported
	 *
	 * @param  string/array $recipients  Recipient email or array of recipient emails.
	 * @param  string       $subject     Message subject.
	 * @param  string       $body        Message body.
	 * @param  string/array $headers     Additional headers.
	 * @param  array        $attachments Files to attach.
	 *
	 * @return bool                      Whether the email contents were sent successfully or not.
	 */
	function wp_mail( $recipients, $subject = '', $body = '', $headers = '', $attachments = [] ) {
		$email = new Email();
		return $email->send( $recipients, $subject, $body, $headers, $attachments );
	}
}

/**
 * Bulk mail sending
 *
 * @param  array $msg_items array of array.
 * @return object           mailjet's return object.
 */
function bulk_mail( $msg_items ) {
	$email = new Email();
	return $email->send_bulk( $msg_items );
}
