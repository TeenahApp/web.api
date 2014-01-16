<?php

class ActionsController extends \Controller {

	// Like an event.
	public function likeEvent($id)
	{
		$result = Action::like("event", $id);

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}

	// Comment on an event.
	public function commentEvent($id)
	{
		$validator = Validator::make(
			array(
				"comment" => Input::get("comment")
			),
			array(
				"comment" => "required"
			)
		);

		// Check if the validation fails.
		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$result = Action::comment("event", $id, Input::get("comment"));

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}

	public function likeEventComment($event_id, $comment_id)
	{
		
	}
}