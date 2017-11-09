<?php

namespace App\Repos;

use App\Models\User;
use App\Models\UserResume;
use App\MyAuth;
use App\Repos\Repo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ResumeRepo extends Repo {

	public static function createOrUpdateResume($id,$filename) {

		$resume = UserResume::where('user_id', $id)->first();

		if($resume) {

			$old_filename = $resume->filename;

			// delete $old_filename
			Storage::delete('resumes/'.$old_filename);
			$mimetype = Storage::mimeType("resumes/".$filename);
         
			$resume->filename = $filename;
			$resume->mime = $mimetype;

			if($resume->update()) {
				return [true, "Resume updated"];
			} else {
				return [false, "Resume not updated"];
			}
		} else {
			$mimetype = Storage::mimeType("resumes/".$filename);
			$resume = new UserResume();
			$resume->user_id = $id;
			$resume->filename = $filename;
			$resume->mime = $mimetype;
			if($resume->save()) {
				return [true, "Resume added"];
			} else {
				return [false, "Resume not added"];
			}
		}

	}

	public static function find(){
		$user=MyAuth::user();
		return $userResume=userResume::where('user_id',$user->id)->first();
	}

	public static function ResumeDownload(){
		$user=MyAuth::user();
		return $userResume=userResume::where('user_id',$user->id)->first();
	}
}