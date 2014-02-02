<?php

class TeenahAppsController extends \Controller {

	public function make($email)
	{
		$validator = Validator::make(
			array(
				"email" => $email
			),
			array(
				"email" => "required|email"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the email does exist before.
		$teenah_app = TeenahApp::where("email", "=", $email)->count();

		if ($teenah_app > 0)
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}

		$teenah_app = TeenahApp::create(array(
			"email" => $email
		));

		return $teenah_app;
	}

}
