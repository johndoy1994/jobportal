<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\Models\User;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\UserRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AccountController extends RecruiterController
{   
    public function __construct() {
        parent::__construct();
        $this->middleware("role:FRONTEND,EMPLOYER,recruiter,recruiter-home",[
            'only'=>['getIndex','getSignOut','postSaveProfilePicture','getCompanyProfile','postCompanyProfile']
            ]);
    }

    var $account_home = "recruiter-account-home";

    public function getIndex() {
    	return view('recruiter.account.index',[
        'user' => MyAuth::user('recruiter')

            ]);
    }

    public function getSignInWithCookieAccount(Request $request)
    {
        if(MyAuth::check('recruiter')) {
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
                    if(Cookie::has('user_accounts_recruiter')){
                        $userAccountsArr = unserialize(Cookie::get('user_accounts_recruiter'));
                        unset( $userAccountsArr[array_search( $request->key, $userAccountsArr )] );
                        $cookie = Cookie::forever('user_accounts_recruiter', serialize($userAccountsArr));
                        return redirect()->route('recruiter-account-signin')->withCookie($cookie);
                    }
                }else{
                    return redirect()->route('recruiter-account-signin');    
                }
            }
            return view('recruiter.signin',array("stored_cookies"=>$userData,"userDataVerified"=>$userDataVerified));
        }
    }

    public function getSignIn() {
        $userAccountsArr = array();

        // Get user accounts stored in cookie history
        if(Cookie::has('user_accounts_recruiter')){
            $userAccountsArr = unserialize(Cookie::get('user_accounts_recruiter'));
        }
        $userData=[];
        foreach ($userAccountsArr as $value) {
            $userData[] = PublicRepo::getUser($value);
        }
        return view('recruiter.signin',array("stored_cookies"=>$userData));
    }

    public function postSignIn(Request $request, UserRepo $repo) {

        $this->validate($request, [
            'email_address' => "required|email",
            'password' => "required|min:5"
        ]);

        $authed = MyAuth::attempt(['email_address' => $request->email_address, 'password' => $request->password], "recruiter");
        if($authed) {
            $user = MyAuth::user("recruiter");
            if($user && !$user->isEmployer()) {
                MyAuth::logout("recruiter");
                return redirect()->route('recruiter-home',['is_recruiter'=>'1'])->withErrorMessage("Sorry, you're not allowed sign here.");
            }
            if($user && !$user->isActivated()) {
                MyAuth::logout("recruiter");
                return redirect()->route('recruiter-home')->withErrorMessage("Sorry, your account is not activated yet, please try again after sometime.");
            }
            if($request->has("redirect")) {
                return redirect()->to($request->redirect);
            } else {
                /*=== Set Cookie for logged in user ===*/
            
                $userAccountsArr = array();
                if(Cookie::has('user_accounts_recruiter')){
                    $userAccountsArr = unserialize(Cookie::get('user_accounts_recruiter'));
                }
                if(!in_array($user->id, $userAccountsArr))
                {
                    $userAccountsArr[] = $user->id;
                }
                $cookie = Cookie::forever('user_accounts_recruiter', serialize($userAccountsArr));
                
                /*=== Set Cookie for logged in user end ===*/
                
                return redirect()->route($this->account_home)->withCookie($cookie);;
            }
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "Authentication failed, E-mail address and password doesn't matched"
            ]);
        }

    }

    public function getRegister() {
        $recruiterType=PublicRepo::allRecruiterTypes();
        return view('recruiter.register',['recruiterTypes'=>$recruiterType,'termAndcpndition'=>PublicRepo::getTermCondition()]);
    }

    public function postRegister(Request $request, UserRepo $repo) {

        $this->validate($request, [
            'recruiterType' => "required|findId:recruiter_types",
            'company_name' => "required",
            'name' => "required|min:3",
            'email_address' => "required|email|unique:users",
            'mobile_number' => 'min:10|numeric|unique:users',
            "password" => "required|min:5|confirmed",
            "password_confirmation" => "required|min:5",
            "terms_accept" => "accepted"
        ]);
        $newUser = null;
        $created = $repo->create([
            'name' => $request->name,
            'email_address' => $request->email_address,
            'mobile_number' => $request->mobile_number,
            'password' => bcrypt($request->password),
            'type'  => User::TYPE_EMPLOYER,
            'level' => User::LEVEL_FRONTEND_USER,
            'status' => User::STATUS_DEACTIVATED
        ], $newUser);
        
        if($created) {
            $employer=$repo->addEmployer($newUser,$request->recruiterType,$request->company_name);
            if($employer){
                return redirect()->route($this->account_home)->with([
                    'success_message' => "Account successfully created, You will get notification once your account get activated."
                ]);
            }else{
                $newUser->delete();
                return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => "There was an error while creating new account, try again"
                ]);

            }

        } else {

            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while creating new account, try again"
            ]);

        }

    }

     public function getSignOut() {
        MyAuth::logout('recruiter');
        $message = "You're successfully signed out.";
        if(session('success_message')) {
            $message = session('success_message');
        }
        return redirect()->route('recruiter-home')->with([
            'success_message' => $message
        ]);
    }

    public function postSaveProfilePicture(Request $request){
        
        // and for max size max:10000
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,gif,png'
        ]);

        $user = MyAuth::user('recruiter');
        
        $imageName = $user->id . '.png';// . $request->file('image')->getClientOriginalExtension();

        $result=Storage::put(
            'avatars/'.$imageName,
            file_get_contents($request->file('image')->getRealPath())
        );

        if($result) {
            //Resize image to 100x100 and 200x200
            PublicRepo::resizeImageTo100x100($imageName, $request);
            PublicRepo::resizeImageTo200x200($imageName, $request);
            
            return redirect()->route('recruiter-account-home')->with([
                'success_message' => "Profile Picture uploaded successfull!"
            ]);
        } else {
            return redirect()->route('recruiter-account-home')->with([
                'error_message' => "There was an error please try again"
            ]);
        }
        
    }

    public function getForgotPassword() {
        return view("recruiter.forgot-password");
    }

    public function postForgotPassword(Request $request) {

        if($request->has('code')) {

            return redirect()->route('api-public-resetpasswordlink', ['code' => Utility::hash_hmac($request->code),'type'=>2]);

        } else {
            list($success, $message) = PublicRepo::processResetPasswordRequest($request);
            if($success) {
                return redirect()->route('recruiter-account-forgotpassword')->withSuccessMessage($message)->with([
                    'email_address' => $request->email_address,
                    'mobile_number' => $request->mobile_number
                ]);
            } else {
                return redirect()->route('recruiter-account-forgotpassword')->withErrorMessage($message);
            }
        }

    }

    public function getCompanyProfile(Request $request){
        $user=MyAuth::user('recruiter');
        $user_address = PublicRepo::getEmployerUserAddress($user->id);
        $Employer = PublicRepo::getEmployer($user->id);
        $Recruitertype = PublicRepo::allRecruiterTypes();
        
        $current_values = [
            "recruiter_type_id"=>0,
            "company_name" => "",
            "first_name" => $user->getFirstName(),
            "surname" => $user->getLastName(),
            "email_address" => $user->getEmailAddress(),
            "mobile_number" => $user->getMobileNumber(),

            "country_id" => 0,
            "state" =>  null,
            "city" => null,
            "street" => "",
            "postalcode" => "",
            "cmp_description" => ""
        ];

        if($user_address) {
            if($user_address->country()) {
                $current_values["country_id"] = $user_address->country()->id;
            }
            if($user_address->country() && $user_address->country()->status == 0) {
                if($user_address->state()) {
                    $state = $user_address->state();
                    if($state->status == 0) {
                        $current_values["state"] = [$state->id, $state->name];
                        if($user_address->city && $user_address->city->status == 0) {
                            $current_values["city"] = [$user_address->city->id, $user_address->city->name];
                        }
                    }
                }
            }
            $current_values["street"] = $user_address->street;
            $current_values["postalcode"] = $user_address->postal_code;
        }

        if($Employer){
            if($Employer->recruiter_type_id) {
                $current_values["recruiter_type_id"] = $Employer->recruiter_type_id;
            }
            $current_values["company_name"] = $Employer->company_name;
            $current_values["cmp_description"] = $Employer->description;
        }

        if($request->old()) {

            $current_values["recruiter_type_id"] = old('recruiter_type_id');
            $current_values["company_name"] = old('company_name');
            $current_values["cmp_description"] = old('cmp_description');
            $current_values["first_name"] = old('first_name');
            $current_values["surname"] = old('surname');
            $current_values["email_address"] = old('email_address');
            $current_values["mobile_number"] = old('mobile_number');
            $current_values["country_id"] = old('country');
            $oldState = PublicRepo::getState(old('state'));
            if($oldState) {
                $current_values["state"] = [$oldState->id, $oldState->getName()];
            }
            $oldCity = PublicRepo::getCity(old('city'));
            if($oldCity) {
                $current_values["city"] = [$oldCity->id, $oldCity->getName()];
            }
            $current_values["street"] = old('street');
            $current_values["postalcode"] = old('postalcode');

        }

        return view("recruiter.account.company-profile.index",[
            'user'          => $user,
            'countries'     => PublicRepo::allCountries(0),
            'countries'     => PublicRepo::allCountries(0),
            'values' => $current_values,
            'recruiterTypes'=>PublicRepo::allRecruiterTypes()

        ]);   
    }

     public function postCompanyProfile(Request $request) {

        $user = MyAuth::user('recruiter');

        $this->validate($request, [
            'recruiter_type_id'     => 'required|findId:recruiter_types',
            "company_name"          => 'required',     
            'first_name'            => 'required|min:1|alpha_single_name',
            'surname'               => 'required|min:1|alpha_single_name',
            'email_address'         => 'required|email|unique:users,email_address,'.$user->id,
            'mobile_number'         => 'digits:10|unique:users,mobile_number,'.$user->id,

            'country' => 'required|findId:countries',
            'state' => 'required|findId:states,,country_id,'.$request->country,
            'city' => 'required|findId:cities,,state_id,'.$request->state,
            //'street'                => 'required',
            'postalcode'            => 'postalcode',

        ]);
        
        Log::info("postCompanyProfile", array($request->all()));

        // Update logic
        // update basic details
        $user_saved = UserRepo::updateUser($user, array(
            "name"          => $request->first_name." ".$request->surname,
            "email_address"         => $request->email_address,
            "mobile_number" => $request->mobile_number,
        ));

        // city, street, postal_code, type=residance
        $address_saved = UserRepo::updateUserAddress($user, "residance", array(
            'city_id'       => $request->city,
            'postal_code'   => $request->postalcode,
            'street'        => $request->street
        ));
        $employer_saved = UserRepo::updateEmployer($user, array(
            'recruiter_type_id'       => $request->recruiter_type_id,
            'company_name'   => $request->company_name,
            'description'   => $request->cmp_description
        ));

        $messages = [];

        $messages[] = $user_saved[0] ? "Employer details saved" : $user_saved[1];
        $messages[] = $employer_saved[0] ? "Company details saved" : $employer_saved[1];
        $messages[] = $address_saved[0] ? "Address saved" : $address_saved[1];

        $finalStatus = $user_saved[0] && $address_saved[0] && $employer_saved[0];
        $messageKey = $finalStatus ? "success_message" : "error_message";

        return redirect()->back()->withInput(Input::all())->with([
            "$messageKey" => $messages
        ]);
    }

}
