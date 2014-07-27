<?php

return array(

	// Your Twilio account sid
	"accountSid" => $_SERVER["TWILIO_ACCOUNT_SID"],

	// Your Twilio auth token
	"authToken" => $_SERVER["TWILIO_AUTH_TOKEN"],

	// Your Default from number - for sending SMS messages out - must be a registered and sms-capable Twilio number
	"fromNumber" => $_SERVER["TWILIO_FROM_NUMBER"]
);