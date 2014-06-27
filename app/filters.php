<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	if (!Request::secure())
	{
		return Response::json(array(
			"message" => "The API could only be called using HTTPS."
		), 403);
	}
});

App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter("app.auth", function(){

	$app_key = Request::header("X-App-Key");
	$app_secret = Request::header("X-App-Secret");

	if (is_null($app_key) || is_null($app_secret))
	{
		return Response::json(array(
			"message" => "An API key and/or secret were either not sent or invalid."
		), 401);
	}

	// Check if the app credentials are there.
	$teenah_app = TeenahApp::where("app_key", "=", $app_key)->where("app_secret", "=", $app_secret)->where("active", "=", "1")->first();

	if (is_null($teenah_app))
	{
		return Response::json(array(
			"message" => "An API key and/or secret were either not sent or invalid."
		), 401);
	}

	// Otherwise, everything is fine and dandy.
	// Call the desired controller.

});

Route::filter("user.auth", function(){

	// Get the user token.
	$user_token = Request::header("X-User-Token");

	if (is_null($user_token))
	{
		return Response::json(array(
			"message" => "Not authorized to access this resource."
		), 403);
	}

	// Check if the token does exist in the users table.
	$user = User::where("token", "=", $user_token)->where("active", "=", "1")->first();
	
	if (is_null($user))
	{
		return Response::json(array(
			"message" => "Not authorized to access this resource."
		), 403);
	}

	// Otherwise, everything is fine and dandy.
	// Call the desired controller.
});
