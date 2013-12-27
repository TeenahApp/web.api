<?php

class UsersController extends \Controller {

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
		print "helloworld";
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


	public function tokenize($mobile)
	{
		// Check if the user has already got SMS token.
		if (Session::has("sms_token"))
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}

		// Check if there is a user with the entered mobile number.
		$user = User::where("mobile", "=", $mobile)->first();
		
		if (is_null($user))
		{
			// Create the user if it does not exist.
			$user = User::create(array("mobile" => $mobile));
		}

		// Generate a random SMS token.
		$sms_token = rand("11111", "99999");

		// Save the token within the session.
		Session::put("sms_token", $sms_token);

		// Everything is okay.
		// TODO: This should be like:
		//return Response::json("", 204);

		return Response::json(array(
			"sms_token" => $sms_token
		), 200);
	}

	public function login()
	{
		// Check if the SMS token has been removed.
		if (!Session::has("sms_token"))
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}

		$validator = Validator::make(
			array(
				"mobile" => Input::get("mobile"),
				"sms_token" => Input::get("sms_token")
			),
			array(
				"mobile" => "required|numeric",
				"sms_token" => "required|numeric"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Wrong mobile number and/or SMS token."
			), 400);
		}

		// Check if the member does exist.
		$user = User::where("mobile", "=", Input::get("mobile"))->first();

		if (is_null($user))
		{
			return Response::json(array(
				"message" => "User cannot be found."
			), 404);
		}

		// Generate a new user token then return it.
		$datetime = new DateTime();
		$timestamp = $datetime->getTimestamp();

		// Un-hashed user token and hashed also.
		$unhashed_user_token = Input::get("mobile") . Input::get("sms_token") . $timestamp;
		$user_token = Hash::make("$unhashed_user_token");

		// Update the user token in the database.
		$user->token = $user_token;
		$user->last_time_tokenized = new DateTime();
		$user->save();

		// Remove the generated SMS token.
		Session::forget("sms_token");

		// Initially, the user has no member related to.
		$has_member = false;

		if ($user->member_id != 0)
		{
			$has_member = true;
		}

		// Save the access of the user.
		Access::create(array("user_id" => $user->id, "category" => "login"));

		// TODO: Return dashboard if the user has a member.
		return Response::json(array(
			"user_token" => $user_token,
			"has_member" => $has_member
		), 200);
	}
}