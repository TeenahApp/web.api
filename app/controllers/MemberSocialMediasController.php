<?php

class MemberSocialMediasController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($member_id)
	{
		// Get the logged in user.
		$user = User::current();

		if (!Member::canUseResource($user->member_id, $member_id))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Get the member if exists.
		$member = Member::find($member_id);

		if (is_null($member))
		{
			return Response::json(array(
				"message" => "Cannot find the resource."
			), 404);
		}

		// Done.
		return MemberSocialMedia::accounts($member_id)->get();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Get the logged in user.
		$user = User::current();

		$validator = Validator::make(
			array(
				"name" => Input::get("name"),
				"account" => Input::get("account")
			),
			array(
				"name" => "required",
				"account" => "required"
			)
		);

		// Check if the validation fails.
		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the social media is listed.
		$found_social_media = SocialMedia::where("name", "=", Input::get("name"))->first();

		if (is_null($found_social_media))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the social name already exists for the member.
		$member_social_medias = array_fetch(MemberSocialMedia::accounts($user->member_id)->get(), "social_media");

		if (in_array(Input::get("name"), $member_social_medias))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Create a social media for the user.
		$member_social_media = MemberSocialMedia::create(
			array(
				"member_id" => $user->member_id,
				"social_media_id" => $found_social_media->id,
				"account" => Input::get("account")
			)
		);

		// Done.
		return Response::json(array(
			"message" => "Social media account has been successfully created."
		), 201);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// Get the logged in user.
		$user = User::current();

		$validator = Validator::make(
			array(
				"account" => Input::get("account")
			),
			array(
				"account" => "required"
			)
		);

		// Check if the validation fails.
		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$found_member_social_media = MemberSocialMedia::where("member_id", "=", $user->member_id)->where("id", "=", $id)->first();

		if (is_null($found_member_social_media))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Update the record.
		$found_member_social_media->update(array(
			"account" => Input::get("account")
		));

		return Response::json(array(
			"message" => "Social media account has been updated successfully."
		), 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// Get the logged in user.
		$user = User::current();

		$found_member_social_media = MemberSocialMedia::where("member_id", "=", $user->member_id)->where("id", "=", $id)->first();

		if (is_null($found_member_social_media))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$found_member_social_media->delete();

		return Response::json("", 204);
	}

}