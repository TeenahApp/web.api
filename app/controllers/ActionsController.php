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

	// Like a member.
	public function likeMember($id)
	{
		$result = Action::like("member", $id);

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}

	// Comment on a member.
	public function commentMember($id)
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

		$result = Action::comment("member", $id, Input::get("comment"));

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}

	// Like a media.
	public function likeMedia($id)
	{
		$result = Action::like("media", $id);

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}

	// Comment on a media.
	public function commentMedia($id)
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

		$result = Action::comment("media", $id, Input::get("comment"));

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}

	// Like a comment on a member.
	public function likeMemberComment($member_id, $comment_id)
	{
		$result = Action::like("member_comment", $comment_id);

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}

	// Like a comment on an event.
	public function likeEventComment($event_id, $comment_id)
	{
		$result = Action::like("event_comment", $comment_id);

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}

	// Like a comment on an event.
	public function likeMediaComment($media_id, $comment_id)
	{
		$result = Action::like("media_comment", $comment_id);

		if ($result == false)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		return Response::json("", 204);
	}
}
