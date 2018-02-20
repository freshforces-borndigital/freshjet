<?php
namespace Freshjet\General;

defined('ABSPATH') or die('Can\'t access directly');

use Mailjet\Resources;
use Mailjet\Client;

class Email
{
	private $_dir;
	private $_url;

	function __construct()
	{
		$this->_dir = FRESHJET_DIR . '/general';
		$this->_url = FRESHJET_URL . '/general';
	}

	/**
	 * Send email via Mailjet
	 * https://dev.mailjet.com/guides/?php
	 * https://github.com/mailjet/mailjet-apiv3-php
	 * 
	 * @param  string/array $to         recipient's email
	 * @param  string       $subject    email's subject
	 * @param  string       $body       email's body
	 * @return object                   mailjet's return object
	 */
	public function send($to, $subject = '', $body = '', $headers = '', $attachments = [], $template = false)
	{
		if (!$to) {
			return;
		}

		// treat it as array
		if (!is_array($to)) {
			$to = [$to];
		}

		// final format (array of array)
		$recipients = [];

		// convert it to "array of array" format
		foreach($to as $receivers)
		{
			if (is_array($receivers)) {
				// if $recipients is array of array (format 3)

				$to_props = [];

				foreach ($receivers as $key => $val) {
					if (array_key_exists('Email', $receivers)) {
						if (array_key_exists('Email', $receivers) || array_key_exists('Name', $receivers)) {
							$to_props[$key] = $val;
						}
					}
				}

				array_push($recipients, $to_props);
			} else {
				// if the $recipients is a string OR if $recipients is array of string (format 1 or 2)
				array_push($recipients, ['Email' => $receivers]);
			}

		}

		$sender   = ['Email' => FRESHJET_SENDER_EMAIL, 'Name' => FRESHJET_SENDER_NAME];
		$mailjet  = new Client(FRESHJET_API_KEY, FRESHJET_SECRET_KEY, true, ['version' => 'v3.1']);

		$msg_item = [
			'From'     => $sender,
			'To'       => $recipients,
			'Subject'  => $subject,
			'HTMLPart' => $body
		];

		if ($headers) {
			$msg_item['Headers'] = $headers;
		}

		$mail_prop = [
			'Messages' => [
				$msg_item
			]
		];

		$response = $mailjet->post(
			Resources::$Email,
			['body' => $mail_prop]
		);

		if (method_exists($response, 'success')) {
			$is_success = $response->success();

			if (!$is_success) {
				// log it
				error_log( print_r($response->getData(), true) );
			}
		}

		return $response;
	}

	/**
	 * Bulk mail sending
	 *
	 * @param  array $items array of array
	 * @return object       mailjet's return object
	 */
	public function send_bulk($items)
	{
		$sender    = ['Email' => FRESHJET_SENDER_EMAIL, 'Name' => FRESHJET_SENDER_NAME];
		$mailjet   = new Client(FRESHJET_API_KEY, FRESHJET_SECRET_KEY, true, ['version' => 'v3.1']);
		$msg_items = [];

		foreach ($items as $item) {
			$subject = isset($item['subject']) ? $item['subject']: null;
			$body    = isset($item['body']) ? $item['body']:       null;
			$headers = isset($item['headers']) ? $item['headers']: null;
			$to      = isset($item['to']) ? $item['to']:           null;
			
			if (!$to) {
				$to = isset($item['recipient']) ? $item['recipient']: null;
			}

			if ($to && $subject && $body) {
				// treat it as array
				if (!is_array($to)) {
					$to = [$to];
				}

				// final format (array of array)
				$recipients = [];

				// convert it to "array of array" format
				foreach($to as $receivers)
				{
					if (is_array($receivers)) {
						// if $recipients is array of array (format 3)

						$to_props = [];

						foreach ($receivers as $key => $val) {
							if (array_key_exists('Email', $receivers)) {
								if (array_key_exists('Email', $receivers) || array_key_exists('Name', $receivers)) {
									$to_props[$key] = $val;
								}
							}
						}

						array_push($recipients, $to_props);
					} else {
						// if the $recipients is a string OR if $recipients is array of string (format 1 or 2)
						array_push($recipients, ['Email' => $receivers]);
					}

				}

				$array = [
					'From'     => $sender,
					'To'       => $recipients,
					'Subject'  => $subject,
					'HTMLPart' => $body
				];
	
				if ($headers) {
					$array['Headers'] = $headers;
				}
	
				array_push($msg_items, $array);
			}
		}


		$mail_prop = [
			'Messages' => $msg_items
		];

		// print_var($mail_prop);
		// exit;

		$response = $mailjet->post(
			Resources::$Email,
			['body' => $mail_prop]
		);

		if (method_exists($response, 'success')) {
			$is_success = $response->success();
			
			if (!$is_success) {
				// log it
				error_log( print_r($response->getData(), true) );
			}
		}

		return $response;
	}
}
