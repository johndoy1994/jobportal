<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Helpers\MailHelper;
use App\Helpers\Notifier;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\Models\User;
use App\Models\UserVerification;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\UserRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AccountController extends FrontendController
{
    
    var $account_home = "account-home";

    public function __construct() {
        parent::__construct();
    	$this->middleware('role:frontend,job_seeker', [
    		'only' => [
                'getIndex', 'getVerification', 'postVerification'
            ]
    	]);

    }

    // Private Methods - Requires authentication

    public function getIndex() {
        return view('frontend.account.index', [
            'user' => MyAuth::user() 
        ]);
    }

    public function getVerification() {

    	return view('frontend.account.verification', [
    		'user' => MyAuth::user(),
		]);

    }

    public function postVerification(Request $request) {

    	$this->validate($request, [
    		'action' => 'required'
		],[
			'action.required' => "Invalid verification request, try again"
		]);

    	switch($request->action) {

            case "verify": // verify code

                $this->validate($request, [
                    'verification_code' => "required"
                ]);

                $user = MyAuth::user();

                if(strcmp($user->verification->token, $request->verification_code)==0) {
                    $user->verification->status = UserVerification::VERIFIED;
                    $user->verification->update();
                    return redirect()->back();
                } else {
                    return redirect()->back()->with(['error_message'=>"Provided verification code didn't matched, try again"]);
                }

            break;

            case "resend": // resend verification code

                $user = MyAuth::user();
                $success_message = "";

                if($user->verification->method == "mobile") {
                    //SMSHelper::sendVerificationCode($user->verification->token, $user);
                    Notifier::verificationHappens($user->verification->token, $user, "sms");
                    $success_message = "Please check your inbox for verification code.";
                } else if($user->verification->method == "email") {
                    Notifier::verificationHappens($user->verification->token, $user, "mail");
                    //MailHelper::sendVerificationCode($user->verification->token, $user);
                    $success_message = "Please check your mailbox for verification code.";
                } else {
                    return redirect()->back()->with(['error_message'=>"Invalid verification method, please try again"]);
                }

                return redirect()->back()->with(['success_message'=>$success_message]);

            break;

    		case "send": // send verification code

    			$this->validate($request, [
    				'verify-by' => "required|in:mobile,email"
				], [
					'verify-by.in' => "Invalid verification method, try again"
				]);

                $token = "";
                if($request["verify-by"] == "email") {
                    $token = md5(uniqid().MyAuth::user()->id);
                } else if($request["verify-by"] == "mobile") {
                    $token = rand(1000,9999);
                } else {
                    return redirect()->back()->with(['error_message'=>"Invalid verification method, please try again"]);
                }

    			$user = MyAuth::user();

				$uv = new UserVerification();
				$uv->method = $request["verify-by"];
				$uv->token = $token;
				$uv->status = UserVerification::NOT_VERIFIED;
				$uv->user_id = $user->id;

				if($uv->save()) {

					if($uv->method == "email") {
				        Notifier::verificationHappens($uv->token, MyAuth::user(), "mail");
						//MailHelper::sendVerificationCode($uv->token, MyAuth::user());
                        return redirect()->back()->with(['success_message'=>"Please check your mailbox and copy your token here to verify your identity"]);

					} else if($uv->method == "mobile") {
                        Notifier::verificationHappens($uv->token, MyAuth::user(), "sms");
                        //SMSHelper::sendVerificationCode($uv->token, MyAuth::user());
                        return redirect()->back()->with(['success_message'=>"Please check your messages and copy your token here to verify your identity"]);
						
					}

				} else {
					return redirect()->back()->with(['error_message'=>"There was an error while generating token for you, try again"]);					
				}

    		break;

    	}

    }

    public function getSignOut() {
    	$message = "You're successfully signed out.";
        $user=MyAuth::user();
        $type=0;
        if($user){
            $loginUser=explode("_", $user->through_id);
            if(isset($loginUser[0]) && $loginUser[0]=='facebook'){
             $type=1;   
            }elseif (isset($loginUser[0]) && $loginUser[0]=='linkedin') {
              $type=2;                  
            }
        }
        MyAuth::logout();
    	
        if(session('success_message')) {
            $message = session('success_message');
        }
    	return redirect()->route('front-home',['login_level'=>$type])->with([
    		'success_message' => $message
        ]);
    }

    public function getSignInWithCookieAccount(Request $request)
    {
        if(MyAuth::check()) {
            return redirect()->route($this->account_home);
        } else {
            $userData = $userDataVerified = array();
            if($request->has("key") && $request->has('type'))
            {
                $request->key=Crypt::decrypt($request->key);
                $checkUser = PublicRepo::getUser($request->key);
                if($checkUser && $request->type=='add'){
                        $userDataVerified = PublicRepo::getUser($request->key);
                }elseif($checkUser && $request->type=='remove'){
                    if(Cookie::has('user_accounts')){
                        $userAccountsArr = unserialize(Cookie::get('user_accounts'));
                        unset( $userAccountsArr[array_search( $request->key, $userAccountsArr )] );
                        $cookie = Cookie::forever('user_accounts', serialize($userAccountsArr));
                        return redirect()->route('account-signin')->withCookie($cookie);
                    }
                }else{
                    return redirect()->route('account-signin');    
                }
            }
            return view('frontend.account.signin',array("stored_cookies"=>$userData,"userDataVerified"=>$userDataVerified));
        }
    }

    // Public Methods - Not required to authenticate
    public function getSignIn() {
    	if(MyAuth::check()) {
    		return redirect()->route($this->account_home);
    	} else {
            $userAccountsArr = array();

            // Get user accounts stored in cookie history
            if(Cookie::has('user_accounts')){
                $userAccountsArr = unserialize(Cookie::get('user_accounts'));
            }
            $userData=[];
            foreach ($userAccountsArr as $value) {
                $userData[] = PublicRepo::getUser($value);
            }
            return view('frontend.account.signin',array("stored_cookies"=>$userData));
    	}
    }

    public function postSignIn(Request $request, UserRepo $repo) {

    	$this->validate($request, [
    		'email_address' => "required",
    		'password' => "required|min:5"
		]);

    	$authed = MyAuth::attempt(['email_address' => $request->email_address, 'password' => $request->password]);

        if(!$authed){
            $authed = MyAuth::attempt(['mobile_number' => $request->email_address, 'password' => $request->password]);
        }
    	if($authed) {

            $user = MyAuth::user();

            if($user && !$user->isJobSeeker()) {
                MyAuth::logout();
                return redirect()->route('front-home',['is_jobseeker'=>'1'])->withErrorMessage("Sorry, you're not allowed sign here.");
            }

            if($user && !$user->isActivated()) {
                MyAuth::logout();
                return redirect()->route('front-home')->withErrorMessage("Sorry, your account is not activated yet, please try again after sometime.");
            }

            PublicRepo::saveGuestSavedJobsIfAny();
            if($request->has("redirect")) {
                return redirect()->to($request->redirect);
            } else {
                 /*=== Set Cookie for logged in user ===*/
            
                $userAccountsArr = array();
                if(Cookie::has('user_accounts')){
                    $userAccountsArr = unserialize(Cookie::get('user_accounts'));
                }
                if(!in_array($user->id, $userAccountsArr))
                {
                    $userAccountsArr[] = $user->id;
                }
                $cookie = Cookie::forever('user_accounts', serialize($userAccountsArr));
                
                /*=== Set Cookie for logged in user end ===*/
                
                return redirect()->route($this->account_home)->withCookie($cookie);
            }
    	} else {
    		return redirect()->back()->withInput(Input::all())->with([
    			'error_message' => "Authentication failed, E-mail address or mobile number and password doesn't matched"
			]);
    	}

    }

    public function getRegister() {
        
    	return view('frontend.account.register',
            ['termAndcpndition'=>PublicRepo::getTermCondition()]
            );
    }

    public function postRegister(Request $request, UserRepo $repo) {

    	$this->validate($request, [
    		'name' => "required|min:3",
    		'email_address' => "required_without_all:mobile_number|email|unique:users",
    		'mobile_number' => 'required_without_all:email_address|min:10|numeric|unique:users',
    		"password" => "required|min:5|confirmed",
    		"password_confirmation" => "required|min:5",
    		"terms_accept" => "accepted"
		]);

    	$created = $repo->create([
    		'name' => $request->name,
    		'email_address' => $request->email_address,
    		'mobile_number' => $request->mobile_number,
    		'password' => bcrypt($request->password),
    		'type'	=> User::TYPE_SEEKER,
    		'level' => User::LEVEL_FRONTEND_USER,
    		'status' => User::STATUS_ACTIVATED
		]);

		if($created) {
            if(!empty($request->email_address)){
			    $authed = MyAuth::attempt(['email_address'=>$request->email_address, "password"=>$request->password]);
            }else{
                $authed = MyAuth::attempt(['mobile_number'=>$request->mobile_number, "password"=>$request->password]);
            } 

            $user = MyAuth::user();
            
            $status=Notifier::accountRegistered(MyAuth::user(),false,false);
            // if(!$status){
            //         $user->forceDelete();
            //     return redirect()->back()->withInput(Input::all())->with([
            //         'error_message' => "Please enter valid mobile number."
            //     ]);
            // }

            if($user && !$user->isJobSeeker()) {
                MyAuth::logout();
                return redirect()->route('front-home')->withErrorMessage("Sorry, you're not allowed sign here.");
            }

            if($user && !$user->isActivated()) {
                MyAuth::logout();
                return redirect()->route('front-home')->withErrorMessage("Sorry, your account is not activated yet, please try again after sometime.");
            }

			return redirect()->route($this->account_home)->with([
				'success_message' => "Account successfully created, now please verify your identity."
			]);

		} else {

			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while creating new account, try again"
			]);

		}

    }

    public function getThrough(Request $request, $provider) {

        if($request->has("action")) {
            session()->put('through-action', $request->action);
            session()->put('through-action-data', $request->all());
        } else {
            if(session("through-action")) {
                Session::forgot('through-action');
                Session::forgot('through-action-data');
            }
        }

        switch($provider) {
            case "facebook":
                return Socialite::driver($provider)->redirect();
            break;

            default:
                return Socialite::driver($provider)->redirect();
            break;
        }

    }

    public function getThroughCallback(Request $request, UserRepo $repo, $provider) {

        if($request->has('error')) {
             return redirect()->route('account-register')->with(['error_message' => "Sorry, there was an error while authorizing you using $provider, try again."]);
        }

        $user = null;

        try {

            $user = Socialite::driver($provider)->user();
            
        } catch(InvalidStateException $exception) {

        }

        Log::info("$provider callback details", ['user'=>$user]);

        if($user) {

            $success_redirect = false;
            $success_user_id = 0;

            $through_id = "";

            $name = $user->getName();
            $email = $user->getEmail();
            $through_id = $provider."_".$user->getId();

            Log::info("$provider primary-id", array($user->getId(), $through_id));

            if(!isset($email)) {
                $email = "";
            }

            if(!isset($name)) {
                $name = "";
            }

            if(isset($through_id) && !empty($through_id)) {

                $dbUser = User::where('through_id', $through_id)->first();

                if($dbUser) {

                    // if(MyAuth::loginUsingId($dbUser->id)) {
                        $success_user_id = $dbUser->id;
                        PublicRepo::saveGuestSavedJobsIfAny();
                        $success_redirect = true;
                        //return redirect()->route($this->account_home);
                    // } else {
                    //     return redirect()->route('account-register')->with([
                    //         'error_message' => "Failed to authorize you with $provider, try again."
                    //     ]);
                    // }

                } else {
                    if($email){
                        $UserData = User::where('email_address', $email)->first();    
                        if($UserData){
                           return redirect()->route('account-signin')->with([
                                    'error_message' => "You have already registered with this email address."
                            ]);
                        }
                    }
                    $tempPassword = uniqid();

                    $newUser = new User();
                    $newUser->name = $name;
                    $newUser->email_address = $email;
                    $newUser->mobile_number = 0;
                    $newUser->password = bcrypt($tempPassword);
                    $newUser->type  = User::TYPE_SEEKER;
                    $newUser->level = User::LEVEL_FRONTEND_USER;
                    $newUser->status = User::STATUS_ACTIVATED;
                    $newUser->through_id = $through_id;

                    if($newUser->save()) {

                        $dbUser = User::where('through_id', $through_id)->first();

                        if($dbUser) {

                            Notifier::accountRegistered($dbUser, true);

                            $uv = new UserVerification();
                            $uv->method = "through";
                            $uv->token = $through_id;
                            $uv->status = UserVerification::VERIFIED;
                            $uv->user_id = $dbUser->id;

                            if($uv->save()) {
                                Notifier::tempPasswordGenerated($tempPassword, $dbUser);
                                //MailHelper::sendThroughPassword($tempPassword, $dbUser);
                                // MyAuth::loginUsingId($dbUser->id);
                                $success_user_id = $dbUser->id;
                                PublicRepo::saveGuestSavedJobsIfAny();
                                $success_redirect = true;
                                //return redirect()->route($this->account_home);
                            } else {
                                return redirect()->route('account-register')->with([
                                    'error_message' => "There was an error in system while creating your account with us, try again."
                                ]);
                            }

                        } else {
                            return redirect()->route('account-register')->with([
                                'error_message' => "There was an error while creating your account with us, try again."
                            ]);    
                        }

                    } else {
                        return redirect()->route('account-register')->with([
                            'error_message' => "There was an error while creating your account, try again."
                        ]);
                    }

                }

            } else {

                return redirect()->route('front-home')->with([
                    'error_message' => "Unable authorize you through $provider, try again."
                ]);

            }

            if($success_redirect) {
                if(session("through-action")) {
                    $action = session('through-action');
                    $actionData = session('through-action-data');
                    if($action == "apply")  {
                        $jobId = $actionData["jobId"];
                        return redirect()->route('frontend-job-apply', ['job' => $jobId])->with([
                            'applicant_user_id' => $success_user_id
                        ]);
                    } else {
                        MyAuth::loginUsingId($success_user_id);
                        return redirect()->route($this->account_home);    
                    }
                } else {
                    MyAuth::loginUsingId($success_user_id);
                    return redirect()->route($this->account_home);
                }
            }

        } else {

            return redirect()->route('front-home')->with(['error_message' => "Something went wrong while authorizing you with $provider, try again"]);

        }

    }

    public function getForgotPassword() {
        return view("frontend.forgot-password");
    }

    public function postForgotPassword(Request $request) {

        if($request->has('code')) {

            return redirect()->route('api-public-resetpasswordlink', ['code' => Utility::hash_hmac($request->code),'type'=>1]);

        } else {
            list($success, $message) = PublicRepo::processResetPasswordRequest($request);
            if($success) {
                return redirect()->route('account-forgotpassword')->withSuccessMessage($message)->with([
                    'email_address' => $request->email_address,
                    'mobile_number' => $request->mobile_number
                ]);
            } else {
                return redirect()->route('account-forgotpassword')->withErrorMessage($message);
            }
        }

    }

}
