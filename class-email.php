<?php
/**
 * Mailjet wrapper
 *
 * @package Freshjet
 */

namespace Freshjet;

use Mailjet\Resources;
use Mailjet\Client;

/**
 * Mailjet wrapper class
 */
class Email {
	/**
	 * Send email via Mailjet
	 *
	 * @link https://dev.mailjet.com/guides/?php
	 * @link https://dev.mailjet.com/guides/?php#send-api-v3-1
	 *
	 * @param  string/array $to          Recipient email or array of recipient emails.
	 * @param  string       $subject     Message subject.
	 * @param  string       $body        Message body.
	 * @param  string/array $headers     Additional headers.
	 * @param  array        $attachments Files to attach.
	 * @param  array        $templates   Mailjet templates.
	 *
	 * @return bool                      Whether the email contents were sent successfully or not.
	 */
	public function send( $to, $subject = '', $body = '', $headers = '', $attachments = [], $templates = [] ) {
		if ( ! $to ) {
			return;
		}

		// treat it as array.
		if ( ! is_array( $to ) ) {
			$to = [ $to ];
		}

		// final format (array of array).
		$recipients = [];

		// convert it to "array of array" format.
		foreach ( $to as $receivers ) {
			if ( is_array( $receivers ) ) {
				// if $recipients is array of array (format 3).

				$to_props = [];

				foreach ( $receivers as $key => $val ) {
					if ( array_key_exists( 'Email', $receivers ) ) {
						if ( array_key_exists( 'Email', $receivers ) || array_key_exists( 'Name', $receivers ) ) {
							$to_props[ $key ] = $val;
						}
					}
				}

				array_push( $recipients, $to_props );
			} else {
				// if the $recipients is a string OR if $recipients is array of string (format 1 or 2).
				array_push( $recipients, [ 'Email' => $receivers ] );
			}
		}

		$sender  = [
			'Email' => FRESHJET_SENDER_EMAIL,
			'Name'  => FRESHJET_SENDER_NAME,
		];
		$mailjet = new Client( FRESHJET_PUBLIC_KEY, FRESHJET_SECRET_KEY, true, [ 'version' => 'v3.1' ] );

		$msg_item = [
			'From'    => $sender,
			'To'      => $recipients,
			'Subject' => $subject,
		];

		if ( ! empty( $attachments ) && is_array( $attachments ) ) {
			foreach ( $attachments as $key => $value ) {
				$msg_item[ $key ] = $value;
			}
		}

		if ( ! empty( $templates ) && is_array( $templates ) ) {
			foreach ( $templates as $key => $value ) {
				$msg_item[ $key ] = $value;
			}
		} else {
			if ( 'text/html' === apply_filters( 'wp_mail_content_type', 'text/plain' ) ) {
				$msg_item['HTMLPart'] = $body;
			} else {
				$msg_item['TextPart'] = $body;
			}
		}

		if ( $headers ) {
			/**
			 * Note: Mailjet uses array for the $headers parameter, while wp_mail can uses either string or array.
			 * And there were some un-allowed types of $headers in mailjet (not sure whether this issue still exist or not).
			 */
			if ( is_array( $headers ) ) {
				$msg_item['Headers'] = $headers;
			} else {
				// TODO: Handle string formatted headers, convert to array.
			}
		}

		$mail_prop = [
			'Messages' => [
				$msg_item,
			],
		];

		$response = $mailjet->post(
			Resources::$Email, // phpcs:ignore -- this is Mailjet format
			[ 'body' => $mail_prop ]
		);

		$is_success = false;

		if ( method_exists( $response, 'success' ) ) {
			$is_success = $response->success();

			if ( ! $is_success ) {
				// if error, log the data.
				error_log( print_r( $response->getData(), true ) );
			}
		}

		return $is_success;
	}

	/**
	 * Bulk mail sending
	 *
	 * @param  array $items Array of mail-set.
	 * @return bool         Whether the email contents were sent successfully or not.
	 */
	public function send_bulk( $items ) {
		$sender    = [
			'Email' => FRESHJET_SENDER_EMAIL,
			'Name'  => FRESHJET_SENDER_NAME,
		];
		$mailjet   = new Client( FRESHJET_PUBLIC_KEY, FRESHJET_SECRET_KEY, true, [ 'version' => 'v3.1' ] );
		$msg_items = [];

		foreach ( $items as $item ) {
			$subject = isset( $item['subject'] ) ? $item['subject'] : null;
			$body    = isset( $item['body'] ) ? $item['body'] : null;
			$headers = isset( $item['headers'] ) ? $item['headers'] : null;
			$to      = isset( $item['to'] ) ? $item['to'] : null;

			if ( ! $to ) {
				$to = isset( $item['recipient'] ) ? $item['recipient'] : null;
			}

			if ( $to && $subject && $body ) {
				// treat it as array.
				if ( ! is_array( $to ) ) {
					$to = [ $to ];
				}

				// final format (array of array).
				$recipients = [];

				// convert it to "array of array" format.
				foreach ( $to as $receivers ) {
					if ( is_array( $receivers ) ) {
						// if $recipients is array of array (format 3).

						$to_props = [];

						foreach ( $receivers as $key => $val ) {
							if ( array_key_exists( 'Email', $receivers ) ) {
								if ( array_key_exists( 'Email', $receivers ) || array_key_exists( 'Name', $receivers ) ) {
									$to_props[ $key ] = $val;
								}
							}
						}

						array_push( $recipients, $to_props );
					} else {
						// if the $recipients is a string OR if $recipients is array of string (format 1 or 2).
						array_push( $recipients, [ 'Email' => $receivers ] );
					}
				}

				$array = [
					'From'     => $sender,
					'To'       => $recipients,
					'Subject'  => $subject,
					'HTMLPart' => $body,
				];

				if ( $headers ) {
					/**
					 * Note: Mailjet uses array for the $headers parameter, while wp_mail can uses either string or array.
					 * And there were some un-allowed types of $headers in mailjet (not sure whether this issue still exist or not).
					 */
					if ( is_array( $headers ) ) {
						$array['Headers'] = $headers;
					} else {
						// TODO: Handle string formatted headers, convert to array.
					}
				}

				array_push( $msg_items, $array );
			}
		}

		$mail_prop = [
			'Messages' => $msg_items,
		];

		$response = $mailjet->post(
			Resources::$Email, // phpcs:ignore -- this is Mailjet format
			[ 'body' => $mail_prop ]
		);

		$is_success = false;

		if ( method_exists( $response, 'success' ) ) {
			$is_success = $response->success();

			if ( ! $is_success ) {
				// if error, log the data.
				error_log( print_r( $response->getData(), true ) );
			}
		}

		return $is_success;
	}
}
