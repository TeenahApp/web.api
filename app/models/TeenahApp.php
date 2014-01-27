<?php

class TeenahApp extends Eloquent {

	protected $table = "teenah_apps";
	protected $guarded = array();

	public static function create(array $attributes)
	{
		// Set a random key and secret.
		$key = Str::random(30);
		$secret = Hash::make($key);

		$attributes["app_key"] = $key;
		$attributes["app_secret"] = $secret;

		return parent::create($attributes);
	}

}