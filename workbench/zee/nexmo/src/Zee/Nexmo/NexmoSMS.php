<?php namespace Zee\Nexmo;

class NexmoSMS {

	private $app_key = null;
	private $app_secret = null;
	private $sender = null;

	public function __construct($api_key, $api_secret, $sender)
	{
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
		$this->sender = $sender;
	}

	public function send($to, $text)
	{
		$attributes = array(
			"api_key" => $this->api_key,
			"api_secret" => $this->api_secret,
			"from" => $this->sender,
			"to" => $to,
			"text" => $text,
			"type" => "unicode"
		);

		return $this->request("https://rest.nexmo.com/sms/json", $attributes);
	}

	public function request($uri, $attributes = array())
	{
		$querystring = http_build_query($attributes);

		// Establish the connection.
		$connection = curl_init($uri . "?" . $querystring);

		// Set some options.
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
		
		// Execute the connection.
		$response = curl_exec($connection);

		// Close the connection eventually.
		curl_close($connection);

		// Return the response.
		return $response;
	}

}