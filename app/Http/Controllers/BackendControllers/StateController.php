<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class StateController extends BackendController
{
    //State listing
    public function getListing(Request $request)
    {	
        
        if($request->has("search")) {
            $q = State::select('countries.name as country_name','states.*')->join('countries', 'states.country_id', "=", "countries.id")->where('states.name','like','%'.$request["search"].'%')->orWhere('countries.name','like','%'.$request["search"].'%');
        } else {
            $q =State::select('countries.name as country_name','states.*')->join('countries', 'states.country_id', "=", "countries.id");
        }

        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            //$q->orderBy('states.name');
            $q->orderBy('states.created_at','desc');
        }
        $recordsPerPage = $this->recordsPerPage("state-listing");
        $States = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name', 'country_name', 'status'];

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

    	return view('backend.state.listing', [
    		"States" => $States,
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
                        if(count($request["statemultiple"]) > 0) {
                            foreach ($request["statemultiple"] as $value) {
                                $val = state::find($value);
                                $val->delete();
                            }
                            //state::whereIn('id', $request["statemultiple"])->delete();
                            return redirect()->back()->with([
                                'success_message' => "Deleted successfully"
                            ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to delete!!"
                            ]);
                        }
                    break;

                    case "active":
                        if(count($request["statemultiple"]) > 0) {
                            $countries = state::whereIn('id', $request["statemultiple"])->update([
                                'status'=>0    
                            ]);
                            return redirect()->back()->with([
                                    'success_message' => "Active successfully"
                                ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to active!!"
                            ]);
                        }
                    break;                    

                    case "inactive":
                        if(count($request["statemultiple"]) > 0) {
                            $countries = state::whereIn('id', $request["statemultiple"])->update([
                                    'status'=>1    
                                ]);
                            return redirect()->back()->with([
                                        'success_message' => "Inactive successfully"
                                    ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to inactive!!"
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
                return redirect()->route('admin-state', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }


    //add New State
    Public function getNewState(Request $request){
    	 // Get Country name
        $CountryObj = Country::select('id','name')->orderBy('name')->get();
        $Country = array();
        foreach ($CountryObj as $key => $value) {
                $Country[$value->id] = $value->name;
        }
        return view('backend.state.new-state',['Countries'=>$Country]);
    }

    public function postNewState(Request $request) {

    	$this->validate($request, [
    		'country_id' => 'required|findId:countries',
            'name' => 'required',
    	],[
    		'country_id.required'=>'Please select country name.',
            'name.required'=>'State name must not be empty.'
    	]);

        $Country = Country::find($request['country_id']);
        if($Country->states()->where('name', $request["name"])->first()) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'State name already exists'
            ]);
        }

		$States = new State();
		$States->name = $request["name"];
        $States->country_id = $request["country_id"];
		if($States->save()) {
			return redirect()->back()->with([
				'success_message' => "New state successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your state, try again"
			]);
		}

    }

    // Edit State
    public function getEditState(Request $request, State $State) {
    	 // Get country name
        $CountryObj = Country::select('id','name')->orderBy('name')->get();
        $Country = array();
        foreach ($CountryObj as $key => $value) {
                $Country[$value->id] = $value->name;
        }
        return view('backend.state.edit-state',[
    		"States"=>$State,'Countries'=>$Country
		]);
    }	

     
    public function postEditState(Request $request, State $State) {
		$this->validate($request, [
            'country_id' => 'required|findId:countries',
    		'name' => 'required',
    	],[
            'country_id.required'=>'Please select country name.',
    		'name.required'=>'State name must not be empty.'
    	]);

        $Country = Country::find($request['country_id']);
        
        if($Country->States()->where('name',$request->name)->where('id','!=',$State->id)->first()) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'State name already exists'
            ]);
        }

		$State->name = $request["name"];
        $State->country_id = $request["country_id"];
        $State->status = $request["status"];
		if($State->update()) {
			return redirect()->route("admin-state", ['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "State successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your State, try again"
			]);
		}
    }

    //Delete State
    public function postDeleteState(Request $request, State $State) {

    	if($State->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "State successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your state, try again!"
			]);
		}

    }

}
