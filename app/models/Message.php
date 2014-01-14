<?php

class Message extends Eloquent {

	protected $table = "messages";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	// category, content, circles, created_by
	// Returns either true or false.
	/*
	public static function send($category, $content, $circles, $media = null)
	{
		// Get the logged in user.
		$user = User::current();

		$validator = Validator::make(
			array(
				"category" => $category,
				"content" => $content,
			),
			array(
				"category" => "required|in:text,update",
				"content" => "size:500" // TODO: This should be in config file.
			)
		);

		// Check if the validation fails.
		if ($validator->fails())
		{
			return false;
		}

		// TODO: Check if the member is a member of all entered circles.
		$member_circles = MemberCircle::where("member_id", "=", $user->member_id)->whereIn("circle_id", $circles)->count();
	}
	*/

	// Usually used internally (not called by public).
	public static function sendText($content, $circles)
	{
		// Get the user information.
		$user = User::current();

		// Create a new message.
		$message = self::create(array(
			"category" => "text",
			"content" => $content,
			"created_by" => $user->member_id
		));

		// Distribute the message to the circles.
		self::distribute($message->id, $circles);

		return $message;
	}

	// Send an update.
	public static function sendUpdate($content, $circles)
	{
		//
	}

	// Upload and send a media as a message.
	public static function sendMedia($category, $data, $extension, $circles)
	{
		//
	}

	// This is triggered to send a specific message for a group circles.
	public static function distribute($message_id, $circles)
	{
		// Get the logged in user.
		$user = User::current();

		// Get the circles of the member and match them.
		$member_circles = DB::table("member_circles");

		foreach ($circles as $circle)
		{
			$member_circles->where(function($query) use ($user, $circle){
				$query->where("member_id", "=", $user->member_id)
					->where("circle_id", "=", $circle);
			});
		}

		dd($member_circles->count());
	}
}
