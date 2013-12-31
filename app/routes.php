<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Route::filter();

Route::group(array("prefix" => "api/v1"), function()
{
	// Get the first factor to sign in.
	Route::get("users/token/{mobile}", "UsersController@tokenize")->where("mobile", "[0-9]+");

	// Sign in using two factors; mobile and SMS token.
	Route::post("users/login", "UsersController@login");

	// Sign out for a logged in user.
	Route::get("users/logout", array("before" => "user.auth", "uses" => "UsersController@logout"));

	// Initial login for the user, there must be a member related to.
	Route::post("users/members", array("before" => "user.auth", "uses" => "UsersController@initialize"));

	// Update the photo of a member.
	Route::put("members/{member_id}/photos", array("before" => "user.auth", "uses" => "MembersController@uploadPhoto"))->where("member_id", "[0-9]+");
});