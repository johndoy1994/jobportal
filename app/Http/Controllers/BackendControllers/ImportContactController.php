<?php

namespace App\Http\Controllers\BackendControllers;

use App\Helpers\Notifier;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use App\Models\UserContacts;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;
use Illuminate\Http\withInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ImportContactController extends BackendController
{

    public function getlisting(Request $request){

        $user=MyAuth::user('admin');
        $q = UserContacts::select(
                "user_contacts.*",
                DB::raw('users.name as user_name')
        );
        $q->join('users', 'users.id', "=", "user_contacts.user_id");
        
        if($request->has("search")) {   
            $search=$request["search"];
            $search=$request["search"];
            $q->where(function($q) use($search) {
                $q->orWhere('users.name','like','%'.$search.'%');
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

        $columns = ['user_name','name', 'email'];

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
        return view('backend.import-contact.listing', [
            "UserContacts" => $UserContacts,
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
                return redirect()->route('get-admin-import-contact', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }       
    
    public function getIndex() {
    	return view('backend.import-contact.index');
    }

    public function getGmailContacts() {
    	$googleContacts = session()->has("googleContacts") ? session()->get('googleContacts') : [];
        
        $data= PublicRepo::addUserContacts($googleContacts,MyAuth::user('admin'),0);
        
        return redirect()->route('get-admin-import-contact')->with([
            'success_message' => "Import and merge contact successfully!!"
        ]);

    }

    public function postGmailContacts(Request $Request){
    	$this->validate($Request, [
    		'emails'=> 'required',
            'subject' => 'required',
            'message' => 'required',
    	]);

    	$user=MyAuth::user('admin');
    	Notifier::sendInvitation($Request->emails,$Request->subject,$Request->message,$user);
    	
        return redirect()->route('admin-home')->withInput(Input::all())->with([
                'success_message' => "Invitetion Send successfull!"
        ]);
    }

}
