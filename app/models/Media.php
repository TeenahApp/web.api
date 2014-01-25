<?php

class Media extends Eloquent {

	protected $table = "medias";
	protected $guarded = array();
	protected $hidden = array("signature");

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	// TODO: This uploaded file should be using S3.
	// Returns a Media object or null.
	public static function upload($category, $data, $extension)
	{
		// Get the logged in users.
		$user = User::current();

		$validator = Validator::make(
			array(
				"category" => $category,
				"data" => $data,
				"extension" => $extension
			),
			array(
				"category" => "required|in:image,video,sound",
				"data" => "required",
				"extension" => "required|in:jpg,png,jpeg,gif,mp4,mp3"
			)
		);

		if ($validator->fails())
		{
			return null;
		}

		$content_type = "application/octet-stream";

		// Set the content type.
		switch ($extension)
		{
			case "jpg" : case "png" : case "jpeg" : case "gif":
				$content_type = "image/{$extension}";
			break; 
		}

		// TODO: Check if the file is really a valid file; not a malware.

		// Set the file fullname.
		$filename = str_random(40) . ".{$extension}";

		// Save the decoded base64 of the file (image).
		$body = base64_decode($data);

		// Initialize S3 object.
		$s3 = AWS::get("s3");

		$object = $s3->putObject(
			array(
    			"Bucket"      => Config::get("aws::bucket"),
    			"Key"         => $filename,
    			"Body"        => $body,
    			"ACL"         => "public-read",
    			"ContentType" => $content_type
			)
		);

		// Check if the object has been put successfully.
		if (is_null($object))
		{
			return null;
		}

		// Make a unique hash.
		$signature = Hash::make($body);

		// TODO: Try to get a taste of the uploaded file.
		// Meaning: A thumb for the photo.

		// Set the photo with the url.
		$media_url = $object["ObjectURL"];

		// Create this media.
		return self::create(array(
			"category" => $category,
			"url" => $media_url,
			"signature" => $signature,
			"created_by" => $user->member_id
		));
	}
}
