<?php

class TrusteesController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Get the logged in user.
		$user = User::current();

		// Done.
		return Response::json(
			array_fetch(Trustee::where("member_id", "=", $user->member_id)->where("active", "=", 1)->with("trustee")->get()->toArray(), "trustee")
		, 200);
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
				"id" => Input::get("id")
			),
			array(
				"id" => "required|numeric"
			)
		);

		// Check if that fails.
		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the trustee is the same with the logged in member.
		if ($user->member_id == Input::get("id"))
		{
			// Cannot be a trustee for yourself.
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the trust exists before.
		$found_trustee = Trustee::where("member_id", "=", $user->member_id)->where("trustee_id", "=", Input::get("id"))->first();

		if (!is_null($found_trustee))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the trustee does exist.
		$found_trustee = Member::find(Input::get("id"));

		if (is_null($found_trustee))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Everything else is okay.
		Trustee::insert(array(
			"member_id" => $user->member_id,
			"trustee_id" => Input::get("id")
		));

		// Done.
		return Response::json("", 204);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function activate($id)
	{
		// Get the logged in user.
		$user = User::current();

		// Check if the trust found.
		$trustee = Trustee::where("member_id", "=", $user->member_id)->where("trustee_id", "=", $id)->first();

		if (is_null($trustee))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Update the trust.
		$trustee->update(array(
			"active" => 1
		));

		// Done.
		return Response::json(array(
			"message" => "The trustee has been activated successfully."
		), 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deactivate($id)
	{
		// Get the logged in user.
		$user = User::current();

		// Check if the trust found.
		$trustee = Trustee::where("member_id", "=", $user->member_id)->where("trustee_id", "=", $id)->first();

		if (is_null($trustee))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Update the trust.
		$trustee->update(array(
			"active" => 0
		));

		// Done.
		return Response::json(array(
			"message" => "The trustee has been deactivated successfully."
		), 200);
	}

}
