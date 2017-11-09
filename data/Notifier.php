<?php

namespace App\Helpers;

use App\Models\emailNotifications;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Utility;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class Notifier {

	//////////////////////
	/// Helper Calls /////
	//////////////////////

	const noReplyMailAddress = "no-reply@provalue.dev";

	private static function mail($view, $view_data, $subject, $from, $to, $bcc = null,$loginUser=array()) {
		
		if(!is_array($from)) {
			$from[0] = $from;
			$from[1] = $from;
		}

		if(!is_array($to)) {
			$to[0] = $to;
			$to[1] = $to;
		}

		//Log::useDailyFiles(storage_path().'/logs/mail.log');
    	Log::info("mail:", func_get_args());
	
		$mail=Mail::send('emails.'.$view, $view_data, function ($mail) use ($subject, $from, $to, $bcc) {
            $mail->from($from[0], $from[1]);
            if(isset($bcc)) {
            	$mail->bcc($bcc);
            }
            $mail->to($to[0], $to[1])->subject($subject);
        });
		
		//self::EmailNotificationSave($view,$view_data,$subject,$from,$to,$loginUser);	
	}

	public static function EmailNotificationSave($view,$view_data,$subject,$from,$to,$loginUser){
		
		if(!is_array($from)) {
			$from[0] = $from;
			$from[1] = $from;
		}

		if($loginUser){
			$from[0] = $loginUser->email_address;
		}

		if(!is_array($to)) {
			$to[0] = $to;
			$to[1] = $to;
		}

		$FromUser=PublicRepo::getUserUsingEmail($from[0]);
		$toUser=PublicRepo::getUserUsingEmail($to[0]);
		
		$fromId=0;
		$toId=0;
		if($toUser && $FromUser){
			$fromId=$FromUser->id;
			$toId=$toUser->id;
		}
		if($toUser){
			$toId=$toUser->id;	
		}
				
		$data=View::make('emails.'.$view, $view_data);
		$emailNotifications = new emailNotifications();
		$emailNotifications->from = $fromId;
		$emailNotifications->to = $toId;
		$emailNotifications->from_email = $from[0];
		$emailNotifications->to_email = $to[0];
		$emailNotifications->mailtype = '';
		$emailNotifications->subject = $subject;
		$emailNotifications->content = $data->render();
		return $emailNotifications->save();
	}

	private static function sms($view, $view_data, $mobile) {
		// Call Twillio API
		Log::info("sms", func_get_args());
	}

	////////////////////////
	/// Events /////////////
	////////////////////////

	public static function resetPasswordTokenGenerated($user, $passwordReset, $notification_type) {
		if($user) {
			$token = $passwordReset->token;
			$code = $passwordReset->code;
			if($notification_type == "mail") {
				if(Utility::email_validation($user->email_address)) {
					$link = route('api-public-resetpasswordlink', ["code"=>$code]);
					self::mail("resetPasswordTokenGenerated", ["user"=>$user, "token"=>$token, "link"=>$link], "Reset Password Request", [self::noReplyMailAddress, env("PROJECT_TITLE")], [$user->email_address, $user->getName()]);
				} else {
					Log::info("event:resetPasswordTokenGenerated", ["msg"=>"User doesn't have valid email address", "user"=>$user, "token"=>$token, "notification_type"=>$notification_type]);
				}
			} else if($notification_type == "sms") {
				if(Utility::mobile_validation($user->mobile_number)) {
					self::sms("resetPasswordTokenGenerated", ["user"=>$user, "token"=>$token], $user->mobile_number);
				} else {
					Log::info("event:resetPasswordTokenGenerated", ["msg"=>"User doesn't have valid mobile number", "user"=>$user, "token"=>$token, "notification_type"=>$notification_type]);
				}
			} else {
				Log::info("event:resetPasswordTokenGenerated", ["msg"=>"No notification_type", "user"=>$user, "token"=>$token, "notification_type"=>$notification_type]);
			}
		} else {
			Log::info("event:resetPasswordTokenGenerated", ["msg"=>"User not valid", "user"=>$user, "Token"=>$token, "notification_type"=>$notification_type]);
		}
	}

	public static function jobPosted($job,$user=null) { // Instant Job Matching
		$job_categories_id = $job->getCategoryId();
		$job_title_id = $job->job_title_id;
		$keywords = ""; // skip now for logic improvement
		$radius = 0; // skil now for logic improvement
		$city_id = $job->getCityId();
		$salary_type_id = $job->getSalaryTypeId();
		$salary_range_from = $job->salary;
		$job_type_id = $job->job_type_id;
		$industries_id = 0; // not in database now

		$jobAlerts = PublicRepo::searchJobAlerts($job_categories_id, $job_title_id, $keywords, $radius, $city_id, $salary_type_id, $salary_range_from, $job_type_id, $industries_id, $job->title, $job->getJobCoordinates());
		$userEmails = [];
		$userNames = [];

		$firstEmail = "test@webplanex.com";
		$firstName = env('PROJECT_TITLE');

		foreach($jobAlerts as $jobAlert) {
			$userEmails[] = $jobAlert->email_address;
			$userNames[] = $jobAlert->name;
		}

		self::mail("josPosted", ["job"=>$job], "New subscribed job posted", [self::noReplyMailAddress, env('PROJECT_TITLE')], [$firstEmail, $firstName], $userEmails,null,$user);
		
	}

	public static function applicationSubmitted($jobApp, $sendSms = true) {

		if($jobApp->user && $jobApp->job && $jobApp->job->employer && $jobApp->job->employer->user) {
			$job = $jobApp->job;
			$user = $jobApp->user;
			$employerUser = $jobApp->job->employer->user;

			// Send notification to applicant
			if($user->hasEmailAddress()) {
				self::mail(
					"applicationSubmitted",
					["applicantUser"=>$user, "job"=>$job, "app"=>$jobApp, "employerUser"=>$employerUser],
					"Job application submitted",
					[self::noReplyMailAddress, env('PROJECT_TITLE')],
					[$user->email_address, $user->getName()]
				);

				if($sendSms) {
					if($user->hasMobile()) {
						self::sms("applicationSubmitted", ["user"=>$user, "job"=>$job, "app"=>$jobApp], $user->mobile_number);
					} else {
						Log::info("event:applicationSubmitted", ["msg"=>"Applicant doesn't have mobile number.", "jobApp"=>$jobApp]);
					}
				}

			} else {
				Log::info("event:applicationSubmitted", ["msg"=>"Applicant doesn't have email address.", "jobApp"=>$jobApp]);
			}

			// Send notification to employer
			if($employerUser->hasEmailAddress()) {

				self::mail(
					"applicationReceived",
					["employerUser"=>$employerUser, "job"=>$job, "app"=>$jobApp, "applicantUser"=>$user],
					"Job application received",
					[self::noReplyMailAddress, env('PROJECT_TITLE')],
					[$employerUser->email_address, $employerUser->getName()]
				);

				if($sendSms) {
					if($employerUser->hasMobile()) {
						self::sms("applicationReceived", ["employerUser"=>$employerUser, "job"=>$job, "app"=>$jobApp, "applicantUser"=>$user], $user->mobile_number);
					} else {
						Log::info("event:applicationSubmitted", ["msg"=>"Employer doesn't have mobile number.", "jobApp"=>$jobApp]);
					}
				}

			} else {
				Log::info("event:applicationSubmitted", ["msg"=>"Employer doesn't have email address.", "jobApp"=>$jobApp]);
			}

		} else {
			Log::info("event:applicationSubmitted", ['msg'=>"User or Job not found", "jobApp"=>$jobApp]);
		}

	}

	public static function verificationHappens(
		$token, // generated token
		$user, // user modal target
		$notification_type // mail or sms or both(empty)
	) {

		if(isset($user)) {
			$valid_data = false;
			if($notification_type == "sms") {
				$valid_data = $user->hasMobile();
			} else if($notification_type == "mail") {
				$valid_data = $user->hasEmailAddress();
			} else {
				$valid_data = $user->hasEmailAddress() && $user->hasMobile();
			}

			if($valid_data) {
				if($notification_type == "mail" || $notification_type == "") {
					self::mail(
						"verificationHappens",
						["name"=>$user->getName(),"token"=>$token],
						"Please verify your acount!!!",
						[self::noReplyMailAddress, env('PROJECT_TITLE')],
						[$user->email_address, $user->getName()]
					);
				}

				if($notification_type == "sms" || $notification_type == "") {
					self::sms(
						"verificationHappens",
						["name"=>$user->getName(),"token"=>$token],
						$user->mobile_number
					);
				}
			} else {
				Log::info('event:verificationHappens', ["msg"=>"User doesn't have valid email/sms", "token"=>$token, "user"=>$user, "notification_type"=>$notification_type]);
			}

		} else {
			Log::info("event:verificationHappens", ["msg"=>"User not found", "token"=>$token, "user"=>$user, "notification_type"=>$notification_type]);
		}

	}

	public static function accountRegistered($user, $through = false) {
		if(isset($user) && $user->hasEmailAddress()) {
			self::mail(
				"accountRegistered", [
					'name' => $user->getName(),
					'through' => $through
				], 
				"Account successfully registered!!", 
				[self::noReplyMailAddress, env('PROJECT_TITLE')],
				[$user->email_address, $user->getName()]
			);
		} else {
			Log::info('event:accountRegistered', ["msg"=>"User or User mail address not found", "user"=>$user]);
		}
	}

	public static function tempPasswordGenerated($password, $user) {
		if(isset($user) && $user->hasEmailAddress()) {
			self::mail(
				"tempPasswordGenerated", [
					'name' => $user->getName(),
					'password' => $password
				], 
				"Your temporary password is here!!", 
				[self::noReplyMailAddress, env('PROJECT_TITLE')],
				[$user->email_address, $user->getName()]
			);
		} else {
			Log::info('event:tempPasswordGenerated', ["msg"=>"User or User mail address not found", "user"=>$user, "password"=>$password]);
		}
	}

	public static function employerActivationMail($user) {

		if(isset($user) && $user->hasEmailAddress()) {
			$link = route('recruiter-account-signin');
			self::mail(
				"employerAccountActive", [
					'name' => $user->getName(),
					'email'=>$user->email_address,
					'link'=>$link
				], 
				"Your Account is Activated!!", 
				[self::noReplyMailAddress, env('PROJECT_TITLE')],
				[$user->email_address, $user->getName()],null,
				MyAuth::user('admin')
			);
		} else {
			Log::info('event:EmployerAccountActive', ["msg"=>"User or User mail address not found", "user"=>$user]);
		}
	}

	public static function recruiterSendMailToApplicant($user,$content) {

		if(isset($user)) {
			self::mail(
				"recruiterSendmailtoApplicant", [
					'name' => $user->name,
					'email'=>$user->email_address,
					'content'=>$content
				], 
				"Recruiter send email!!", 
				[self::noReplyMailAddress, env('PROJECT_TITLE')],
				[$user->email_address, $user->name],null,
				MyAuth::user('recruiter')				
			);
		} else {
			Log::info('event:EmployerAccountActive', ["msg"=>"User or User mail address not found", "user"=>$user]);
		}
	}

}

