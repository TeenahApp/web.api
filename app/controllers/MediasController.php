<?php

class MediasController extends \Controller {

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
		// TODO: Check if the user is allowed to see this media.
		$user = User::current();

		$validator = Validator::make(
			array(
				"id" => $id
			),
			array(
				"id" => "required|integer"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$media = Media::with("creator")->find($id);

		if (is_null($media))
		{
			return Response::json(array(
				"message" => "Cannot find the resource."
			), 404);
		}

		Action::view("media", $media->id);

		// Check if the current member liked the comment.
		$member_likes_count = Action::where("area", "=", "media")->where("action", "=", "like")->where("affected_id", "=", $media->id)->where("created_by", "=", $user->member_id)->count();
		$media->has_liked = ($member_likes_count > 0) ? 1 : 0;

		// Done.
		return $media;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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

	public function getComments($media_id)
	{
		$user = User::current();

		// TODO: Check if the user is able to access this resource.

		$comments = Action::get("comment", "media", $media_id);

		// Get the likes of the event.
		$comments->each(function($comment) use ($user)
		{
			$likes_count = Action::calculate("like", "media_comment", $comment->id);

			// Check if the current member liked the comment.
			$member_likes_count = Action::where("area", "=", "media_comment")->where("action", "=", "like")->where("affected_id", "=", $comment->id)->where("created_by", "=", $user->member_id)->count();

			$comment->likes_count = $likes_count;
			$comment->has_liked = ($member_likes_count > 0) ? 1 : 0;
		});

		return $comments;
	}

}
