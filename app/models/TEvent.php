<?php

class TEvent extends Eloquent {

	protected $table = "events";
	protected $guarded = array();
	protected $appends = array("views_count", "likes_count", "comments_count", "comings_count");

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function members()
	{
		return $this->hasMany("CircleEventMember", "event_id")->with("member");
	}

	public function medias()
	{
		return $this->hasMany("EventMedia", "event_id")->with("media");
	}

	public function delete()
	{
		// Delete every invitation.
		CircleEventMember::where("event_id", "=", $this->id)->delete();

		// Delete every action.
		Action::where("area", "=", "event")->where("affected_id", "=", $this->id)->delete();

		// Delete the event.
		return parent::delete();
	}

	public function getViewsCountAttribute()
	{
		return Action::calculate("view", "event", $this->id);
	}

	public function getLikesCountAttribute()
	{
		return Action::calculate("like", "event", $this->id);
	}

	public function getCommentsCountAttribute()
	{
		return Action::calculate("comment", "event", $this->id);
	}

	public function getComingsCountAttribute()
	{
		return CircleEventMember::where("event_id", "=", $this->id)->where("decision", "=", "willcome")->count();
	}
}
