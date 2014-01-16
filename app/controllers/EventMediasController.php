<?php

class EventMediasController extends \Controller {

	public function upload($event_id)
	{
		// Get the user whos logged in.
		$user = User::current();

		$validator = Validator::make(
			array(
				"category" => Input::get("category"),
				"data" => Input::get("data"),
				"extension" => Input::get("extension"),
			),
			array(
				"category" => "required",
				"data" => "required",
				"extension" => "required",
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the member is invited to the event.
		$invited = CircleEventMember::where("event_id", "=", $event_id)->where("member_id", "=", $user->member_id)->first();

		if (is_null($invited))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Try to upload a media.
		$media = Media::upload(Input::get("category"), Input::get("data"), Input::get("extension"));

		// Add this media to the event.
		// TODO: Notify the event invited members for this update.
		$event = EventMedia::create(array(
			"event_id" => $event_id,
			"media_id" => $media->id
		));

		// Done.
		return Media::with("creator")->find($media->id);
	}
}