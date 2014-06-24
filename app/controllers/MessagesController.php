<?php

class MessagesController extends \Controller {

	public function sendText()
	{
		$user = User::current();
		$member_circles = array_fetch($user->member->circles()->get(), "id");

		$validator = Validator::make(
			array(
				"content" => Input::get("content"),
				"circles" => Input::get("circles")
			),
			array(
				"content" => "required",
				"circles" => "required|regex:/\[(\d)+(,\d+)*\]/"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Get the passed circles.
		$circles = explode(",", substr(Input::get("circles"), 1, -1));
		$arrays_diff = array_diff($circles, $member_circles);

		if (count($arrays_diff) > 0)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Send the message.
		$message = Message::sendText(Input::get("content"), $circles);

		if (is_null($message))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Otherwise, everything is fine and dandy.
		return Response::json("", 204);
	}

	public function sendMedia()
	{
		$user = User::current();
		$member_circles = array_fetch($user->member->circles()->get(), "id");

		$validator = Validator::make(
			array(
				"category" => Input::get("category"),
				"data" => Input::get("data"),
				"extension" => Input::get("extension"),
				"circles" => Input::get("circles")
			),
			array(
				"category" => "required",
				"data" => "required",
				"extension" => "required",
				"circles" => "required|regex:/\[(\d)+(,\d+)*\]/"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Get the passed circles.
		$circles = explode(",", substr(Input::get("circles"), 1, -1));
		$arrays_diff = array_diff($circles, $member_circles);

		if (count($arrays_diff) > 0)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Send the message.
		$message = Message::sendMedia(Input::get("category"), Input::get("data"), Input::get("extension"), $circles);

		if (is_null($message))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Otherwise, everything is fine and dandy.
		return Response::json("", 204);
	}

	public function getLatestUnread($circle_id)
	{
		// Get the logged in user information.
		$user = User::current();
		
		// Get the member circles.
		$member_circles = array_fetch($user->member->circles()->get(), "id");
		
		if (!in_array($circle_id, $member_circles))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Get the messages that have not been read.
		$query = CircleMessageMember::where("circle_id", "=", $circle_id)
									->where("member_id", "=", $user->member_id)
									->where("status", "!=", "read");

		$messages = $query->with(array("message" => function($nested_query){
											$nested_query->with("creator");
										}))->get();

		// Update them as read.
		$query->update(array("status" => "read"));

		// Done.
		return $messages;
	}

	public function getLatestRead($circle_id)
	{
		// Get the logged in user information.
		$user = User::current();
		
		// Get the member circles.
		$member_circles = array_fetch($user->member->circles()->get(), "id");
		
		if (!in_array($circle_id, $member_circles))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Get the messages that have not been read.
		$messages = CircleMessageMember::where("circle_id", "=", $circle_id)
										->where("member_id", "=", $user->member_id)
										->where("status", "=", "read")
										->with(array("message" => function($query){
											$query->with("creator");
										}))->limit(50)->get();

		// Done.
		return $messages;
	}
}