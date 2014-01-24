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

Route::group(array("prefix" => "api/v1", "before" => "app.auth"), function()
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

	// Update an education of a member.
	Route::put("members/{member_id}/educations/{education_id}", array("before" => "user.auth", "uses" => "MemberEducationsController@update"))->where(array("member_id" => "[0-9]+", "education_id" => "[0-9]+"));

	// Delete an education of a member.
	Route::delete("members/{member_id}/educations/{education_id}", array("before" => "user.auth", "uses" => "MemberEducationsController@destroy"))->where(array("member_id" => "[0-9]+", "education_id" => "[0-9]+"));

	// Create a job for a member.
	Route::post("members/{id}/jobs", array("before" => "user.auth", "uses" => "MemberJobsController@store"))->where("id", "[0-9]+");

	// Get the jobs for a member.
	Route::get("members/{id}/jobs", array("before" => "user.auth", "uses" => "MemberJobsController@index"))->where("id", "[0-9]+");

	// Update a job for a member.
	Route::put("members/{member_id}/jobs/{job_id}", array("before" => "user.auth", "uses" => "MemberJobsController@update"))->where(array("member_id" => "[0-9]+", "job_id" => "[0-9]+"));

	// Delete a job for a member.
	Route::delete("members/{member_id}/jobs/{job_id}", array("before" => "user.auth", "uses" => "MemberJobsController@destroy"))->where(array("member_id" => "[0-9]+", "job_id" => "[0-9]+"));

	// Get the members of a circle.
	Route::get("circles/{id}/members", array("before" => "user.auth", "uses" => "MemberCirclesController@index"))->where("id", "[0-9]+");

	// Add a member to a circle.
	Route::post("circles/{id}/members", array("before" => "user.auth", "uses" => "MemberCirclesController@store"))->where("id", "[0-9]+");

	// Get circles of a member.
	Route::get("circles", array("before" => "user.auth", "uses" => "CirclesController@index"));

	// Create a circle; must invite at least one member.
	Route::post("circles", array("before" => "user.auth", "uses" => "CirclesController@store"));

	// Leave a circle.
	Route::get("circles/{id}/leave", array("before" => "user.auth", "uses" => "MemberCirclesController@leave"))->where("id", "[0-9]+");

	// Get the events of a circle.
	Route::get("circles/{id}/events", array("before" => "user.auth", "uses" => "EventsController@index"))->where("id", "[0-9]+");

	// Get the events of a circle.
	Route::get("circles/{id}/stats", array("before" => "user.auth", "uses" => "CirclesController@stats"))->where("id", "[0-9]+");

	// Create an event.
	Route::post("events", array("before" => "user.auth", "uses" => "EventsController@store"));

	// Get an event.
	Route::get("events/{id}", array("before" => "user.auth", "uses" => "EventsController@show"))->where("id", "[0-9]+");

	// Update an event.
	Route::put("events/{id}", array("before" => "user.auth", "uses" => "EventsController@update"))->where("id", "[0-9]+");
	
	// Delete an event.
	Route::delete("events/{id}", array("before" => "user.auth", "uses" => "EventsController@destroy"))->where("id", "[0-9]+");
	
	// Upload a media to an event.
	Route::put("events/{id}/medias", array("before" => "user.auth", "uses" => "EventMediasController@upload"))->where("id", "[0-9]+");

	// Decisions on events.
	Route::put("events/{id}/decision/{decision}", array("before" => "user.auth", "uses" => "EventsController@decide"))->where("id", "[0-9]+");
	
	// Get the decision of the member for the event.
	Route::get("events/{id}/decision", array("before" => "user.auth", "uses" => "EventsController@showDecision"))->where("id", "[0-9]+");

	// Like an event.
	Route::get("events/{id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeEvent"))->where("id", "[0-9]+");
	
	// Comment on an event.
	Route::post("events/{id}/comment", array("before" => "user.auth", "uses" => "ActionsController@commentEvent"))->where("id", "[0-9]+");

	// Like a member.
	Route::get("members/{id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMember"))->where("id", "[0-9]+");

	// Comment on a member.
	Route::post("members/{id}/comment", array("before" => "user.auth", "uses" => "ActionsController@commentMember"))->where("id", "[0-9]+");

	// Like a media.
	Route::get("medias/{id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMedia"))->where("id", "[0-9]+");
	
	// Comment on a media.
	Route::post("medias/{id}/comment", array("before" => "user.auth", "uses" => "ActionsController@commentMedia"))->where("id", "[0-9]+");

	// Like a comment on a member.
	Route::get("members/{member_id}/comments/{comment_id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMemberComment"));
	
	// Like a comment on an event.
	Route::get("events/{event_id}/comments/{comment_id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeEventComment"));

	// Like a comment on a media.
	Route::get("medias/{media_id}/comments/{comment_id}/like", array("before" => "user.auth", "uses" => "ActionsController@likeMediaComment"))->where("id", "[0-9]+");

	// Send a message to a circle or a group of circles.
	Route::post("messages/texts", array("before" => "user.auth", "uses" => "MessagesController@sendText"));

	// Send a media to a circle or a group of circles.
	Route::put("messages/medias", array("before" => "user.auth", "uses" => "MessagesController@sendMedia"));

	// Fetch the unread messages.
	Route::get("circles/{id}/messages", array("before" => "user.auth", "uses" => "MessagesController@fetch"))->where("id", "[0-9]+");

	// Get the social medias of the member.
	Route::get("members/{member_id}/socialmedias", array("before" => "user.auth", "uses" => "MemberSocialMediasController@index"))->where("member_id", "[0-9]+");

	// Create a member social media.
	Route::post("socialmedias", array("before" => "user.auth", "uses" => "MemberSocialMediasController@store"));
	
	// Update a social media for a member.
	Route::put("socialmedias/{id}", array("before" => "user.auth", "uses" => "MemberSocialMediasController@update"))->where("id", "[0-9]+");
	
	// Delete a social media for a member.
	Route::delete("socialmedias/{id}", array("before" => "user.auth", "uses" => "MemberSocialMediasController@destroy"))->where("id", "[0-9]+");

	// Create a trustee for the logged in member.
	Route::post("trustees", array("before" => "user.auth", "uses" => "TrusteesController@store"));

	// Get the list of trustees.
	Route::get("trustees", array("before" => "user.auth", "uses" => "TrusteesController@index"));

	// Activate a trustee.
	Route::get("trustees/{id}/activate", array("before" => "user.auth", "uses" => "TrusteesController@activate"))->where("id", "[0-9]+");

	// Deactivate a trustee.
	Route::get("trustees/{id}/deactivate", array("before" => "user.auth", "uses" => "TrusteesController@deactivate"))->where("id", "[0-9]+");

	// Get the dashboard of the current user.
	Route::get("users/dashboard", array("before" => "user.auth", "uses" => "UsersController@dashboard"));

	// Auto-complete companies.
	Route::get("companies/autocomplete/{query}", array("before" => "user.auth", "uses" => "AutoCompletesController@companies"));

	// Auto-complete majors.
	Route::get("majors/autocomplete/{query}", array("before" => "user.auth", "uses" => "AutoCompletesController@majors"));
});