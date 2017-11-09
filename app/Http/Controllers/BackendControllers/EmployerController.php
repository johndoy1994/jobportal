<?php

namespace App\Http\Controllers\BackendControllers;

use App\Helpers\Notifier;
use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Employer;
use App\Models\User;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Repos\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class EmployerController extends BackendController
{	
	public function getListing(Request $request)
    {
        $q = Employer::select(
            "employers.*",
            DB::raw('users.mobile_number'),
            DB::raw('users.email_address'),
            DB::raw('users.name'),
            DB::raw('users.status'),
            DB::raw('count(jobs.id) as job_count'),
            DB::raw('cities.id as cityId'),
            DB::raw('cities.name as cityname'),
            DB::raw('states.name as statename'),
            DB::raw('countries.name as countryname'),
            DB::raw('user_addresses.street'),
            DB::raw('user_addresses.postal_code')
        );
        $q->join('users', 'users.id', '=', 'employers.user_id');
        $q->leftJoin('user_addresses', function($join) {
            $join->on('user_addresses.user_id', "=", "employers.user_id");
            $join->whereNull('user_addresses.deleted_at');
        });
        $q->leftJoin('cities', function($join) {
            $join->on('cities.id', "=", "user_addresses.city_id");
            $join->whereNull("cities.deleted_at");
        });
        $q->leftJoin('states', function($join) {
            $join->on('states.id', "=", "cities.state_id");
            $join->whereNull('states.deleted_at');
        });
        $q->leftJoin('countries', function($join) {
            $join->on('countries.id', "=", "states.country_id");
            $join->whereNull('countries.deleted_at');
        });

        $q->leftJoin("jobs", function($join) {
            $join->on("jobs.employer_id", '=', 'employers.id');
            $join->whereNull("jobs.deleted_at");
        });
        
        if($request->has("search")) {   
            $search=$request["search"];
            $q->where(function($q) use($search) {
                $q->orWhere('employers.company_name','like','%'.$search.'%');
                $q->orWhere('cities.name','like','%'.$search.'%');
                $q->orWhere('users.mobile_number','like','%'.$search.'%');
                $q->orWhere('users.email_address','like','%'.$search.'%');
                $q->orWhere('users.name','like','%'.$search.'%');
            });
        }

        if($request->has("cityId")) {   
            $search=$request["cityId"];

            $q->where(function($q) use($search) {
                $q->orWhere('cities.id','like','%'.$search.'%');
            });
        }

        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            //$q->orderBy('company_name');
            $q->orderBy('created_at','desc');
        }

        $q->groupBy("employers.id");
        
        $Employers = $q->paginate(env('DEFAULT_ROWSIZE_PERPAGE'));
        
        if($request->has("search")) {   
            $search=$request["search"];
            $q->where(function($q) use($search) {
                $q->orWhere('employers.company_name','like','%'.$search.'%');
                $q->orWhere('cities.name','like','%'.$search.'%');
                $q->orWhere('users.mobile_number','like','%'.$search.'%');
                $q->orWhere('users.email_address','like','%'.$search.'%');
                $q->orWhere('users.name','like','%'.$search.'%');
            });
        }

        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            $q->orderBy('company_name');
        }

        $q->groupBy("employers.id");
        $recordsPerPage = $this->recordsPerPage("employer-listing");
        $Employers = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name','company_name','cityname','mobile_number','email_address','job_count'];

        $sort_columns = [];
        foreach ($columns as $column) {
            $sort_columns[$column]["params"] = [
                'page' => $page,
                'sortBy' => $column
            ];
            if($request->has('sortOrder')) {
                $sort_columns[$column]["params"]["sortOrder"] = $request["sortOrder"] == "asc" ? "desc" : "asc";
                if($sort_columns[$column]["params"]["sortOrder"] == "asc") {
                    $sort_columns[$column]["angle"] = "up";
                } else {
                    $sort_columns[$column]["angle"] = "down";
                }
            } else {
                $sort_columns[$column]["params"]["sortOrder"] = "desc";
                $sort_columns[$column]["angle"] = "down";
            }

            if($request->has("search")) {
                $sort_columns[$column]["params"]["search"] = $request->search;
            }
        }
        $isRequestSearch=$request->has('search');
        return view('backend.employer.listing', [
	    		"Employers" => $Employers,
                'sort_columns' => $sort_columns,
                'isRequestSearch'=>$isRequestSearch
	        ]);
	}
    public function postListing(Request $request)
    {
        $action = $request->submit;

        switch($action) {
            case "Apply":
                switch($request["bulkid"]) {
                    case "deleted":
                        if(count($request["employermultiple"]) > 0) {
                            foreach ($request["employermultiple"] as $value) {
                                $emp = Employer::find($value);
                                $emp->delete();
                            }
                            return redirect()->back()->with([
                                'success_message' => "Deleted successfully"
                            ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to delete!!"
                            ]);
                        }
                    break;

                    default:
                        return redirect()->back()->with([
                                'error_message' => "Please select any bulk action!!"
                            ]);
                    break;
                }

            break;
        }
    }

	//add New City
    Public function getNewEmployer(Request $request){
    	
        $Countries = PublicRepo::allCountries(0);
        $Recruitertype = PublicRepo::allRecruiterTypes();
        $oldstateTitle = null;
        $oldCityTitle = null;
        if(old()) {
            if(old("state_id")) {
                $stateId = old("state_id");
                $stateTitle = PublicRepo::getState($stateId);
                if($stateTitle) {
                    $oldstateTitle = [$stateTitle->id, $stateTitle->getName()];
                }
            }

            if(old("city_id")) {
                $cityId = old("city_id");
                $cityTitle = PublicRepo::getCity($cityId);
                if($cityTitle) {
                    $oldCityTitle = [$cityTitle->id, $cityTitle->getName()];
                }
            }
        }

        return view('backend.employer.new-employer',['countries'=>$Countries,'Recruitertypes'=>$Recruitertype,'oldstateTitle'=>$oldstateTitle,'oldCityTitle'=>$oldCityTitle]);
    }

    public function postNewEmployer(Request $request) {
        
        $this->validate($request, [
            'recruiter_type_id' => 'required|findId:recruiter_types',
            'company_name' => 'required',
            'name' => 'required',
            'mobile_number' => 'min:10|numeric|unique:users',
            'email_address' => 'required|email|unique:users',
            "password" => "required|min:5|confirmed",
            "password_confirmation" => "required|min:5",
            'country_id' => 'required|findId:countries',
            'state_id' => 'required|findId:states,,country_id,'.$request->country_id,
            'city_id' => 'required|findId:cities,,state_id,'.$request->state_id,
            //'street' => 'required',
            'postal_code' => 'postalcode'
        ]);

        list($status[0],$message[0],$user)=AdminRepo::addEmployerUser($request->all());
        
            list($status[1],$message[1]) = UserRepo::addUserAddress($user, "residance", array(
            'city_id'       => $request->city_id,
            'postal_code'   => $request->postal_code,
            'street'        => $request->street
        ));
        if($status[0] && $status[1]){    
            list($employerStatus,$employerStatus,$employer)=AdminRepo::addEmployer($user,$request->all());
            if($employerStatus) {
                return redirect()->back()->with([
                    'success_message' => "New Employer successfully added!"
                ]);
            } else {
                $user->delete();
                return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => "There was an error while adding your employer, try again"
                ]);
            }
        }else{
            if($user){
                $user->delete();
            }
            return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => "There was an error while adding your employer, try again"
            ]);
        }
    }

    //Edit Employer
    Public function getEditEmployer(Request $request, Employer $Employer){
        
        $userAddress = PublicRepo::getEmployerUserAddress($Employer->user_id);
        $User = PublicRepo::getEmployerUser($Employer->user_id);
        $Countries = PublicRepo::allCountries(0);
        $Recruitertype = PublicRepo::allRecruiterTypes();
        
        $filename = 'avatars/100x100/'.$Employer->user_id.'.png';
        if(Storage::exists($filename)) {
            $image_vallid=true;
        } else {
            $image_vallid=false;
        }
        return view('backend.employer.edit-employer',[
            'employers'=>$Employer,
            'userAddress'=>$userAddress,
            'User'=>$User,
            'countries'=>$Countries,
            'Recruitertypes'=>$Recruitertype,
            'image_vallid'=>$image_vallid
            ]);
    }

    public function postEditEmployer(Request $request, Employer $Employer) {
        
        $this->validate($request, [
            'recruiter_type_id' => 'required|findId:recruiter_types',
            'company_name' => 'required',
            'name' => 'required',
            'mobile_number' => 'min:10|numeric|unique:users,mobile_number,'.$Employer->user_id,
            'email_address' => 'required|email|email|unique:users,email_address,'.$Employer->user_id,
            'country_id' => 'required|findId:countries',
            'state_id' => 'required|findId:states,,country_id,'.$request->country_id,
            'city_id' => 'required|findId:cities,,state_id,'.$request->state_id,
            //'street' => 'required',
            'postal_code' => 'postalcode'
        ]);
        $user = PublicRepo::getEmployerUser($Employer->user_id);
        list($status[0],$message[0],$user)=AdminRepo::updateEmployerUser($Employer->user_id,$request->all());
        
            list($status[1],$message[1]) = UserRepo::updateUserAddress($user, "residance", array(
            'city_id'       => $request->city_id,
            'postal_code'   => $request->postal_code,
            'street'        => $request->street
        ));

        list($employerStatus,$employerStatus,$employer)=AdminRepo::updateEmployer($Employer,$request->all());
        if($employerStatus) {
            if($status[0] && $status[1]){
                return redirect()->route('admin-employer',['cityId'=>$request->cityId,'page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "Employer successfully saved!"
                ]);
            }else{
                return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while adding your user or address field, try again"
                ]);
            }
            
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while adding your employyer, try again"
            ]);
        }
    }

    // Change Employer Profile Picture
    public function postSaveProfilePicture(Request $request){

        // and for max size max:10000
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,gif,png'
        ]);
        $user = PublicRepo::getEmployerUser($request->userId);
        if(isset($user))
        {
            $imageName = $user->id . '.png';// . $request->file('image')->getClientOriginalExtension();

            $result=Storage::put(
                'avatars/'.$imageName,
                file_get_contents($request->file('image')->getRealPath())
            );

            if($result) {
                //Resize image to 100x100 and 200x200
                PublicRepo::resizeImageTo100x100($imageName, $request);
                PublicRepo::resizeImageTo200x200($imageName, $request);
                
                return redirect()->back()->with([
                    'success_message' => "Profile Picture uploaded successfull!"
                ]);
            } else {
                return redirect()->back()->with([
                    'error_message' => "There was an error please try again"
                ]);
            }
        }else{
            return redirect()->back()->with([
                'error_message' => "Employer user ID is not valid"
            ]); 
        }
        
    }

    //add employer
    public function getActiveInactiveJob(Request $request){
        
        if($request->action=='active'){
           $status=User::STATUS_ACTIVATED;
        }elseif ($request->action=="inactive") {
            $status=User::STATUS_DEACTIVATED;
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while update your employer status, try again"
            ]);   
        }

        list($status,$message,$jobrec) = AdminRepo::updateEmployerAction($request->user_id, $status);
        if($status){
                $user=PublicRepo::getUser($request->user_id);
                if($user->status=="ACTIVATED"){
                    Notifier::employerActivationMail($user);
                    

                }
            
           return redirect()->route('admin-employer',['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "Status successfully saved!"
            ]); 
       }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while update your employer, try again"
            ]);
       }
    }

    Public function getDeleteImageEmployer(Request $request){
        $filename = 'avatars/'.$request->user_id.'.png';
        $filename_100x100 = 'avatars/100x100/'.$request->user_id.'.png';
        $filename_200x200 = 'avatars/200x200/'.$request->user_id.'.png';
        if(Storage::exists($filename)) {
           Storage::delete($filename);
           Storage::delete($filename_100x100);
           Storage::delete($filename_200x200);
           return redirect()->back()->with([
                'success_message' => "image deleted successfully!."
            ]);
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while delete image, try again"
            ]);
        }
    }

}
