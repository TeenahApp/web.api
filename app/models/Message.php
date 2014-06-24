<?php

class Message extends Eloquent {

	protected $table = "messages";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function medias()
	{
		return $this->hasMany("MessageMedia")->with("media");
	}

	// Usually used internally (not called by public).
	// Returns an object of the message or null.
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

		// deliver the message to the members of circles.
		$status = self::broadcast($message->id, $circles);

		if ($status == false)
		{
			// TODO: May be delete the message.
			return null;
		}

		return $message;
	}

	// Send an update.
	public static function sendUpdate($content, $circles)
	{
		// TODO:
	}

	// Upload and send a media as a message.
	public static function sendMedia($category, $data, $extension, $circles)
	{
		// Get the user information.
		$user = User::current();

		// Try to upload the media.
		$media = Media::upload($category, $data, $extension);

		if (is_null($media))
		{
			return null;
		}

		// Create a new message.
		$message = self::create(array(
			"category" => "text",
			"created_by" => $user->member_id
		));

		// Add the media to the message.
		MessageMedia::create(array(
			"message_id" => $message->id,
			"media_id" => $media->id
		));

		// deliver the message to the members of circles.
		$status = self::broadcast($message->id, $circles);

		if ($status == false)
		{
			// TODO: May be delete the message.
			return null;
		}

		return $message;
	}

	// This is triggered to send a specific message for a group circles.
	// Returns an object of the message or null.
	public static function broadcast($message_id, $circles)
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

		if ($member_circles->count() == 0)
		{
			return false;
		}

		// Deliver the message.
		foreach ($circles as $circle)
		{
			// Get the active members in the current circle.
			$members = MemberCircle::where("circle_id", "=", $circle)->where("status", "=", "active")->get();

			foreach ($members as $member)
			{
				// Deliver this message to the member.
				// TODO: I don't know may be make sure that everybody is receiving the message.
				CircleMessageMember::broadcast($circle, $message_id, $member->member_id);
			}
		}

		return true;
	}

	public function delete()
	{
		// Delete every message with media.
		MessageMedia::where("media_id", "=", $this->id)->delete();

		// Delete every delivered message.
		CircleMessageMember::where("message_id", "=", $this->id)->delete();

		// Delete the event.
		return parent::delete();
	}
}
