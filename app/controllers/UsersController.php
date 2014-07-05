<?php

class UsersController extends \Controller {


	// TODO: Do not login in a user that already logged in.
	public function tokenize($mobile)
	{
		//Session::forget("sms_token");

		// Check if the user has already got SMS token.
		if (Session::has("sms_token"))
		{
			// return Response::json(array(
			// 	"message" => "SMS verification code has been already sent."
			// ), 403);
			return Response::json("", 204);
		}

		$validator = Validator::make(
			array(
				"mobile" => $mobile
			),
			array(
				"mobile" => "required|min:10"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if there is a user with the entered mobile number.
		$user = User::where("mobile", "=", $mobile)->first();
		
		if (is_null($user))
		{
			// Create the user if it does not exist.
			$user = User::create(array("mobile" => $mobile));
		}

		// Generate a random SMS token.
		$sms_token = rand("1111", "9999");

		// Save the token within the session.
		Session::put("sms_token", $sms_token);

		$sms = Nexmo::SMS(Config::get("nexmo::api_key"), Config::get("nexmo::api_secret"), Config::get("nexmo::sender"));
		$text = "تينه - كلمة المرور المؤقتة: $sms_token";

		// Send the message.
		$sms->send($mobile, $text);

		// Everything is okay.
		return Response::json("", 204);
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

		// Remove the generated SMS token.
		Session::forget("sms_token");

		// Initially, the user has no member related to.
		$member_id = 0;
		$found_member = Member::where("mobile", "=", Input::get("mobile"))->first();

		if (!is_null($found_member))
		{
			$member_id = $found_member->id;
			$user->member_id = $found_member->id;

			// TODO: Update the fullname for the member.
		}

		// Save changes.
		$user->save();

		// Save the access of the user.
		Access::create(array(
			"user_id" => $user->id,
			"category" => "login"
		));

		// TODO: Return dashboard if the user has a member.
		return Response::json(array(
			"user_token" => $user_token,
			"member_id" => $member_id
		), 200);
	}

	public function logout()
	{
		$user = User::current();

		// Forget the token for the user that is logged in.
		$user->token = null;
		$user->save();

		// Done.
		return Response::json("", 204);
	}

	public function initialize()
	{
		$user = User::current();

		// Check if the user already has a member.
		if ($user->member_id != 0)
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}

		// Get the inputs from the user.
		$validator = Validator::make(
			array(
				"gender" => Input::get("gender"),
				"name" => Input::get("name"),
				"dob" => Input::get("dob")
			),
			array(
				"gender" => "required|in:male,female",
				"name" => "required",
				"dob" => "required|date" // TODO: Consider this again.
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Invalid inputs."
			), 400);
		}

		// Normalize the name.
		$name = Member::normalize(Input::get("name"));

		if (is_null($name))
		{
			return Response::json(array(
				"message" => "The entered name is not within the correct format."
			), 400);
		}

		// Otherwise, everything is fine and dandy.
		$member = Member::create(array(
			"gender" => Input::get("gender"),
			"name" => $name,
			"dob" => Input::get("dob"),
			"mobile" => $user->mobile
		));

		// Update the user's member id.
		$user->member_id = $member->id;
		$user->save();

		// Done.
		return Response::json(array(
			"message" => "Member has been created successfully.",
			"member_id" => $member->id,
			"user_id" => $user->id
		), 201);
	}

	public function dashboard()
	{
		$user = User::current();
		return Member::dashboard($user->member_id);
	}
}