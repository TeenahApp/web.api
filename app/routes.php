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

/*
Event::listen("illuminate.query", function($sql){
	echo "$sql\n";
});
*/

// Route::filter();

/*
Route::get("api/keys/generate", function()
{
	$api_key = Str::random(30);
	$api_secret = Hash::make($api_key); // With probably mobile and time.

	return Response::json(array(
		"api_key" => $api_key,
		"api_secret" => $api_secret
	), 200);
});
*/

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

	// Get the member that is selected.
	Route::get("members/{id}", array("before" => "user.auth", "uses" => "MembersController@show"))->where("id", "[0-9]+");

	// Update the member information.
	Route::put("members/{id}", array("before" => "user.auth", "uses" => "MembersController@update"))->where("id", "[0-9]+");

	// Update the photo of a member.
	Route::put("members/{member_id}/photos", array("before" => "user.auth", "uses" => "MembersController@uploadPhoto"))->where("member_id", "[0-9]+");

	// Create a relationship between two members.
	Route::post("members/{member_a}/relations", array("before" => "user.auth", "uses" => "MemberRelationsController@store"))->where(array("member_a" => "[0-9]+"));

	// Delete a relationship between two members.
	Route::delete("members/{member_a}/relations", array("before" => "user.auth", "uses" => "MemberRelationsController@destroy"))->where(array("member_a" => "[0-9]+"));

	// Create an education for a member.
	Route::post("members/{id}/educations", array("before" => "user.auth", "uses" => "MemberEducationsController@store"))->where("id", "[0-9]+");

	// Get the educations for a member.
	Route::get("members/{id}/educations", array("before" => "user.auth", "uses" => "MemberEducationsController@index"))->where("id", "[0-9]+");

	// TODO: Update an education of a member.
	Route::put("members/{id}/educations", array("before" => "user.auth", "uses" => "MemberEducationsController@update"))->where("id", "[0-9]+");

	// TODO: Delete an education of a member.
	Route::delete("members/{id}/educations", array("before" => "user.auth", "uses" => "MemberEducationsController@store"))->where("id", "[0-9]+");

	// Create a job for a member.
	Route::post("members/{id}/jobs", array("before" => "user.auth", "uses" => "MemberJobsController@store"))->where("id", "[0-9]+");

	// Get the jobs for a member.
	Route::get("members/{id}/jobs", array("before" => "user.auth", "uses" => "MemberJobsController@index"))->where("id", "[0-9]+");

	// TODO: Update a job for a member.
	Route::post("members/{id}/jobs", array("before" => "user.auth", "uses" => "MemberJobsController@store"))->where("id", "[0-9]+");

	// TODO: Delete a job for a member.
	Route::get("members/{id}/jobs", array("before" => "user.auth", "uses" => "MemberJobsController@index"))->where("id", "[0-9]+");

	// Get the members of a circle.
	Route::get("circles/{id}/members", array("before" => "user.auth", "uses" => "MemberCirclesController@index"))->where("id", "[0-9]+");

	// Add a member to a circle.
	Route::post("circles/{id}/members", array("before" => "user.auth", "uses" => "MemberCirclesController@store"))->where("id", "[0-9]+");

	// Get circles of a member.
	Route::get("circles", array("before" => "user.auth", "uses" => "CirclesController@index"));

	// Create a circle; must invite at least one member.
	Route::post("circles", array("before" => "user.auth", "uses" => "CirclesController@store"));

	// Leave a circle.
	Route::get("circles/{id}/leave", array("before" => "user.auth", "uses" => "MemberCirclesController@leave"));

	// Get the events of a circle.
	Route::get("circles/{id}/events", array("before" => "user.auth", "uses" => "EventsController@index"))->where("id", "[0-9]+");

	// Create an event.
	Route::post("events", array("before" => "user.auth", "uses" => "EventsController@store"));

	// Get an event.
	Route::get("events/{id}", array("before" => "user.auth", "uses" => "EventsController@show"));

	// Update an event.
	Route::put("events/{id}", array("before" => "user.auth", "uses" => "EventsController@update"));
	
	// Delete an event.
	Route::delete("events/{id}", array("before" => "user.auth", "uses" => "EventsController@destroy"));
	
	// Upload a media to an event.
	Route::put("events/{id}/medias", array("before" => "user.auth", "uses" => "EventMediasController@upload"))->where("id", "[0-9]+");

	// Decisions on events.
	Route::put("events/{id}/decision/{decision}", array("before" => "user.auth", "uses" => "EventsController@decide"));
	
	// TODO:
	Route::get("events/{id}/decision", array("before" => "user.auth", "uses" => "EventsController@showDecision"));

	// Actions
	Route::get("events/{id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeEvent"));
	
	// TODO:
	Route::post("events/{id}/comment", array("before" => "user.auth", "uses" => "ActionsController@commentEvent"));

	// TODO: 
	Route::get("members/{id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMember"));

	// TODO: 
	Route::post("members/{id}/comment", array("before" => "user.auth", "uses" => "ActionsController@commentMember"));

	// TODO: Remember to make view action for a media.
	Route::get("medias/{id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMedia"));
	
	// TODO: 
	Route::post("medias/{id}/comment", array("before" => "user.auth", "uses" => "ActionsController@commentMedia"));

	// TODO: 
	Route::get("members/{member_id}/comments/{comment_id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMemberComment"));
	
	// TODO: 
	Route::get("events/{event_id}/comments/{comment_id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMemberComment"));

	// TODO:
	Route::get("medias/{media_id}/comments/{comment_id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMemberComment"));

	// Send a message to a circle or a group of circles.
	Route::post("messages/texts", array("before" => "user.auth", "uses" => "MessagesController@sendText"));

	// TODO:
	Route::put("messages/medias", array("before" => "user.auth", "uses" => "MessagesController@sendMedia"));

	// Fetch the unread messages.
	Route::get("circles/{id}/messages", array("before" => "user.auth", "uses" => "MessagesController@fetch"))->where("id", "[0-9]+");

	// 

});