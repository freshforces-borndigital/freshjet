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
	 * @param  string|array $recipients  Recipient email or array of recipient emails.
	 * @param  string       $subject     Message subject.
	 * @param  string       $body        Message body.
	 * @param  string|array $headers     Additional headers.
	 * @param  array        $attachments Files to attach.
	 * @param  array        $templates   Mailjet templates.
	 *
	 * @return bool                      Whether the email contents were sent successfully or not.
	 */
	public function send( $recipients, $subject = '', $body = '', $headers = '', $attachments = [], $templates = [] ) {
		if ( ! $recipients ) {
			return false;
		}

		$recipients = $this->handle_recipients( $recipients );

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

		if ( ! empty( $headers ) ) {
			$msg_item['Headers'] = $this->handle_headers( $headers );
		}

		if ( ! empty( $attachments ) ) {
			$msg_item['Attachments'] = $this->handle_attachments( $attachments );
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
			$recipients  = isset( $item['to'] ) ? $item['to'] : null;
			$subject     = isset( $item['subject'] ) ? $item['subject'] : null;
			$body        = isset( $item['body'] ) ? $item['body'] : null;
			$headers     = isset( $item['headers'] ) ? $item['headers'] : null;
			$attachments = isset( $item['attachments'] ) ? $item['attachments'] : [];
			$templates   = isset( $item['templates'] ) ? $item['templates'] : []; // TODO: Add support for $templates in bulk send.

			if ( ! $recipients ) {
				$recipients = isset( $item['recipient'] ) ? $item['recipient'] : null;
			}

			if ( $recipients && $subject && $body ) {
				$recipients = $this->handle_recipients( $recipients );

				$array = [
					'From'    => $sender,
					'To'      => $recipients,
					'Subject' => $subject,
				];

				if ( 'text/html' === apply_filters( 'wp_mail_content_type', 'text/plain' ) ) {
					$array['HTMLPart'] = $body;
				} else {
					$array['TextPart'] = $body;
				}

				if ( ! empty( $headers ) ) {
					$array['Headers'] = $this->handle_headers( $headers );
				}

				if ( ! empty( $attachments ) ) {
					$array['Attachments'] = $attachments;
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

	/**
	 * Convert WordPress's formatted recipients to Mailjet's format
	 *
	 * @param array $recipients Array of file path.
	 *
	 * @return array $mailjet_recipients Recipients in Mailjet format.
	 */
	private function handle_recipients( $recipients ) {
		// Mailjet formatted recipients.
		$mailjet_recipients = [];

		// If $recipients is a string (format 1 and 3).
		if ( is_string( $recipients ) ) {
			$string_with_commas = explode( ',', $recipient );

			// If $recipients is a single email.
			if ( count( $string_with_commas ) <= 1 ) {
				$recipient = trim( $recipient, ',' );

				array_push( $mailjet_recipients, [ 'Email' => $recipient ] );
			} else {
				// If $recipients is a comma separated emails.
				foreach ( $string_with_commas as $recipient_str ) {
					array_push( $mailjet_recipients, [ 'Email' => $recipient_str ] );
				}
			}

			return $mailjet_recipients;
		}

		// Only string or array is supported.
		if ( ! is_array( $recipients ) ) {
			return $mailjet_recipients;
		}

		foreach ( $recipients as $recipient ) {
			// If $recipients is array of email string (format 2).
			if ( is_string( $recipient ) ) {
				array_push( $mailjet_recipients, [ 'Email' => $recipient ] );
			} elseif ( is_array( $recipient ) ) {
				// if $recipients is array of associative array (format 3).
				$recipient_props = [];

				foreach ( $recipient as $key => $val ) {
					if ( array_key_exists( 'Email', $recipient ) ) {
						$recipient_props[ $key ] = $val;
					}
				}

				array_push( $mailjet_recipients, $recipient_props );
			}
		}

		return $mailjet_recipients;
	}

	/**
	 * Convert WordPress's formatted attachments to Mailjet's format
	 *
	 * @param array $attachments Array of file path.
	 *
	 * @return array $mailjet_attachments Attachments in Mailjet format.
	 */
	private function handle_attachments( $attachments ) {
		$mailjet_attachments = [];

		if ( ! is_array( $attachments ) ) {
			return $mailjet_attachments;
		}

		foreach ( $attachments as $file_path ) {
			// ! Think about the security of using this.
			$file_contents = file_get_contents( $file_path );

			// Make sure the file exists.
			if ( false !== $file_contents ) {
				$paths     = explode( '/', $file_path );
				$file_name = end( $paths );

				array_push(
					$mailjet_attachments,
					[
						'ContentType'   => 'application/octet-stream',
						'Filename'      => $file_name,
						'Base64Content' => base64_encode( $file_contents ), // ? Is this safe?
					]
				);
			}
		}

		return $mailjet_attachments;
	}

	/**
	 * Convert WordPress's formatted headers to Mailjet's format
	 *
	 * @param array $headers Array of file path.
	 *
	 * @return array $mailjet_headers Headers in Mailjet format.
	 */
	private function handle_headers( $headers ) {
		// Mailjet formatted headers.
		$mailjet_headers = [];

		/**
		 * Note: Mailjet uses array for the $headers parameter, while wp_mail can use either string or array.
		 * And there were some un-allowed types of $headers in mailjet (not sure whether this issue still exist or not).
		 */
		if ( is_array( $headers ) ) {
			// TODO: Check if there $headers contains un-allowed header(s).
			$mailjet_headers = $headers;
		} else {
			// TODO: Handle string formatted headers, convert to array.
		}

		return $mailjet_headers;
	}
}
