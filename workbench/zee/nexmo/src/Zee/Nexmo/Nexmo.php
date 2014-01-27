<?php namespace Zee\Nexmo;

class Nexmo {

	public $SMS = null;

	public function SMS($api_key, $api_secret, $sender)
	{
		if (is_null($this->SMS))
		{
			$this->SMS = new NexmoSMS($api_key, $api_secret, $sender);
		}
		
		return $this->SMS;
	}
}