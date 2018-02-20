<?php
defined('ABSPATH') or die('Can\'t access directly');

use Freshjet\General\Email;

// cache the Email object
$GLOBALS['freshjet__email_object'] = new Email();

/**
 * Currently we limit the default parameters support:
 * 
 * $recipients: supported
 * $subject: supported
 * $body: supported
 * $headers: not-supported yet
 * $attachments: not-supported yet
 * 
 * and extra parameters:
 * 
 * $template: not-supported yet
 *
 * @param  string/array $recipients recipient's email
 * @param  string       $subject    email's subject
 * @param  string       $body       email's body
 * @return object                   mailjet's return object
 */
if (!function_exists('wp_mail')) {
	function wp_mail($recipients, $subject = '', $body = '', $headers = '', $attachments = [], $template = false)
	{
		$email = $GLOBALS['freshjet__email_object'];
		return $email->send($recipients, $subject, $body, $headers, $attachments, $template);
	}
}

/**
 * Bulk mail sending
 *
 * @param  array $msg_items array of array
 * @return object           mailjet's return object
 */
function bulk_mail($msg_items)
{
	$email = $GLOBALS['freshjet__email_object'];
	return $email->send_bulk($msg_items);
}