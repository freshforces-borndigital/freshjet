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

		if (!is_array($to)) {
			$to = [$to];
		}

		$recipients = [];

		foreach($to as $email)
		{
			array_push($recipients, ['Email' => $email]);
		}
		

		$sender   = ['Email' => FRESHJET_SENDER_EMAIL, 'Name' => FRESHJET_SENDER_NAME];
		$mailjet  = new Client(FRESHJET_API_KEY, FRESHJET_SECRET_KEY, true, ['version' => 'v3.1']);
		$msg_args = [
			'From'     => $sender,
			'To'       => $recipients,
			'Subject'  => $subject,
			'HTMLPart' => $body
		];

		if ($headers) {
			$msg_args['Headers'] = $headers;
		}

		$mail_prop = [
			'Messages' => [
				$msg_args
			]
		];

		return $mailjet->post(
			Resources::$Email,
			['body' => $mail_prop]
		);
	}
}
