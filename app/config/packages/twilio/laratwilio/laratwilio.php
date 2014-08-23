<?php

return array(

	// Your Twilio account sid
	"accountSid" => getenv("TWILIO_ACCOUNT_SID"),

	// Your Twilio auth token
	"authToken" => getenv("TWILIO_AUTH_TOKEN"),

	// Your Default from number - for sending SMS messages out - must be a registered and sms-capable Twilio number
	"fromNumber" => getenv("TWILIO_FROM_NUMBER")
);