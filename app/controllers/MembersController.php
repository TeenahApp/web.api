<?php

class MembersController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		$user = User::current();

		if (!Member::canUseResource($user->member_id, $id))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Get the member if exists.
		//$member = Member::with("jobs")->with("educations")->find($id);
		// Fixed an issue with displaying inner relations to this member.

		$member = Member::with("jobs")->with("educations")->with(array("inRelations" => function($query){
			$query->with("firstMember");
		}))->find($id);

		if (is_null($member))
		{
			return Response::json(array(
				"message" => "Cannot find the resource."
			), 404);
		}

		// TODO: Check if the current member liked the comment.
		$member_likes_count = Action::where("area", "=", "member")->where("action", "=", "like")->where("affected_id", "=", $member->id)->where("created_by", "=", $user->member_id)->count();
		$member->has_liked = ($member_likes_count > 0) ? 1 : 0;

		// Make an action for the logged in user (member); specifically "view".
		Action::view("member", $member->id);

		// Done.
		return $member;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// Get the current logged in user.
		$user = User::current();

		if (!Member::canUseResource($user->member_id, $id))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		$datetime = new DateTime("tomorrow");
		$tomorrow = $datetime->format("Y-m-d");

		$validator = Validator::make(
			array(
				"dob" => Input::get("dob"),
				"dod" => Input::get("dod"),
				"email" => Input::get("email"),
				"marital_status" => Input::get("marital_status")
			),
			array(
				"dob" => "date|before:$tomorrow",
				"dod" => "date|before:$tomorrow",
				"email" => "email",
				"marital_status" => "required|in:single,married,divorced,widow"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Get the chosen member.
		$member = Member::find($id);

		// Make dob and dod null.
		$dob = Input::get("dob") ? : null;
		$dod = Input::get("dod") ? : null;

		if (is_null($member))
		{
			return Response::json(array(
				"message" => "Cannot find the resource."
			), 404);
		}

		// Update the member.
		$member->update(array(
			"dob" => $dob,
			"pob" => Input::get("pob"),
			"dod" => $dod,
			"pod" => Input::get("pod"),
			"email" => Input::get("email"),
			"marital_status" => Input::get("marital_status")
		));

		// Done.
		return Response::json("", 204);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	// Base64 photo to be uploaded.
	public function uploadPhoto($member_id)
	{
		// Check if the member does exist and is a female.
		$member = Member::where("id", "=", $member_id)->where("gender", "=", "male")->first();

		if (is_null($member))
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}

		// Check if the (logged in) member is able to upload a photo for the chosen member.
		$logged_in_user = User::current();

		if (!Member::canUseResource($logged_in_user->member_id, $member->id))
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}
		
		$validator = Validator::make(
			array(
				"data" => Input::get("data"),
				"extension" => Input::get("extension"),
			),
			array(
				"data" => "required",
				"extension" => "required"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$media = Media::upload("image", Input::get("data"), Input::get("extension"));
		
		if (is_null($media))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Update the photo for the member that is chosen.
		$member->photo = $media->url;
		$member->save();

		// Done.
		return Response::json(array(
			"url" => $media->url
		), 200);
	}

	public function getComments($member_id)
	{
		$user = User::current();

		// TODO: Check if the user is able to access this resource.

		$comments = Action::get("comment", "member", $member_id);

		// Get the likes of the event.
		$comments->each(function($comment) use ($user)
		{
			$likes_count = Action::calculate("like", "member_comment", $comment->id);

			// Check if the current member liked the comment.
			$member_likes_count = Action::where("area", "=", "member_comment")->where("action", "=", "like")->where("affected_id", "=", $comment->id)->where("created_by", "=", $user->member_id)->count();

			$comment->likes_count = $likes_count;
			$comment->has_liked = ($member_likes_count > 0) ? 1 : 0;
		});

		return $comments;
	}

	public function getMemberByMobile($mobile)
	{
		$member = Member::where("mobile", "=", $mobile)->select(array("id", "name"))->first();

		if ($member == null)
		{
			return Response::json(array(
				"message" => "Member has not been found."
			), 404);
		}

		return $member;
	}

}
