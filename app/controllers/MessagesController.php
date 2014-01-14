<?php

class MessagesController extends \Controller {

	public function text()
	{
		$user = User::current();
		$member_circles = array_fetch($user->member->circles()->get(), "id");

		// TODO: Get the circles of the logged in user.
		// TODO: There has to be some validation.
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
	}

}