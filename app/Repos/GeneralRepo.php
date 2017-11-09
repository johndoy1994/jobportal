<?php

namespace App\Repos;

use App\Models\Tag;
use App\Repos\Repo;

class GeneralRepo extends Repo {

	public static function addTag($job_title_id, $tagName) {

		$tag = Tag::where('job_title_id', $job_title_id)->where('name', $tagName)->first();

		if($tag) {
			return [false, "Tag already exists."];
		} else {
			$tag = new Tag();
			$tag->job_title_id = $job_title_id;
			$tag->name = $tagName;
			if($tag->save()) {
				return [true, "Tag saved"];
			} else {
				return [false, "Unable to save tag, please try again"];
			}
		}

	}

	public static function findOrCreateTag($job_title_id, $tagName, $isId = false) {
	
		if(strlen(trim($tagName)) == 0) {
			return [false, "Tag name not valid", null];
		}

		$compareColumn = $isId ? "id" : "name";

		$tag = Tag::where('job_title_id', $job_title_id)->where($compareColumn, $tagName)->first();

		if($tag) {
			return [true, "Found", $tag];
		} else {
			$tag = new Tag();
			$tag->job_title_id = $job_title_id;
			$tag->name = $tagName;
			if($tag->save()) {
				return [true, "Created", $tag];
			} else {
				return [false, "Failed to create", null];
			}
		}		

	}

}