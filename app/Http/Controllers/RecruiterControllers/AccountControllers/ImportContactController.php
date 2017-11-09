<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Helpers\Notifier;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\Models\UserContacts;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;
use Illuminate\Http\withInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ImportContactController extends RecruiterController
{   
    public function getlisting(Request $request){
        $user=MyAuth::user('recruiter');
        $q = UserContacts::select(
                "user_contacts.*",
                DB::raw('users.name as user_name')
        );
        $q->join('users', 'users.id', "=", "user_contacts.user_id");
        $q->where('user_contacts.user_id','=',$user->id);
        
        if($request->has("search")) {   
            $search=$request["search"];
            $search=$request["search"];
            $q->where(function($q) use($search) {
                $q->orWhere('user_contacts.name','like','%'.$search.'%');
                $q->orWhere('user_contacts.email','like','%'.$search.'%');
            });
        }
        
        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            $q->orderBy('created_at','desc');
        }
        $recordsPerPage = $this->recordsPerPage("userContact-listing");
        $UserContacts = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name', 'email'];

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
        $isPageUrl=($request->has('url')) ? $request->url : null;
        $isPageTitle=PublicRepo::getPageTitle($isPageUrl);
        if($isPageUrl && $isPageTitle){
            $title="Sharing with my contacts";
        }else{
            $title="Gmail Contact Listing";    
        }
        return view('recruiter.import-contact.listing', [
            "UserContacts" => $UserContacts,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch,
            'user'=>$user,
            'pageTitle'=>$isPageTitle,
            'pageUrl'=>$isPageUrl,
            'title'=>$title
        ]);
    }
    
    public function postListing(Request $request)
    {
        $action = $request->submit;

        switch($action) {
            case "Apply":
                switch($request["bulkid"]) {
                    case "deleted":
                        if(count($request["usercontactmultiple"]) > 0) {
                           foreach ($request["usercontactmultiple"] as $value) {
                                $val = UserContacts::find($value);
                                $val->delete();
                            }
                           // City::whereIn('id', $request["citymultiple"])->delete();
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

            case "Search":
                return redirect()->route('get-recruiter-import-contact', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }      
    
    public function getIndex() {
    	return view('recruiter.import-contact.index');
    }

    public function getGmailContacts() {
    	$UrlData=(session()->has('through-action-data')) ? session()->get('through-action-data') : [] ;
        $url="";
        if($UrlData){
            $url=(isset($UrlData['url']))? $UrlData['url'] : "";
        }
        $googleContacts = session()->has("googleContacts") ? session()->get('googleContacts') : [];
        $data= PublicRepo::addUserContacts($googleContacts,MyAuth::user('recruiter'),0);
        return redirect()->route('get-recruiter-import-contact',['url'=>$url])->with([
            'success_message' => "Import and merge contact successfully!!"
        ]);
    	//return view('recruiter.import-contact.gmail', ['googleContacts'=>$googleContacts]);
    }

    public function postGmailContacts(Request $Request){
    	$this->validate($Request, [
    		'emails'=> 'required',
            'subject' => 'required',
            'message' => 'required',
    	]);

    	$user=MyAuth::user('recruiter');
    	Notifier::sendInvitation($Request->emails,$Request->subject,$Request->message,$user);
    	
        return redirect()->route('recruiter-account-home')->withInput(Input::all())->with([
                'success_message' => "Invitetion Send successfull!"
        ]);
    }
}
