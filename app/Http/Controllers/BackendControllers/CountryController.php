<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CountryController extends BackendController
{
    //Country listing
    public function getListing(Request $request)
    {	
    	if($request->has("search")) {
            $q =Country::where('name', 'like', "%".$request["search"]."%");
        } else {
            $q =Country::where('name', 'like', "%%");
        }

        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            //$q->orderBy('name');
            $q->orderBy('created_at','desc');

        }
        $recordsPerPage = $this->recordsPerPage("country-listing");
        $Country = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name', 'status'];

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
    	return view('backend.country.listing', [
    		"Countryes" => $Country,
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
                        if(count($request["countrymultiple"]) > 0) {
                            foreach ($request["countrymultiple"] as $value) {
                                $val = Country::find($value);
                                $val->delete();
                            }
                            //Country::whereIn('id', $request["countrymultiple"])->delete();
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
                        if(count($request["countrymultiple"]) > 0) {
                            $countries = Country::whereIn('id', $request["countrymultiple"])->update([
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
                        if(count($request["countrymultiple"]) > 0) {
                            $countries = Country::whereIn('id', $request["countrymultiple"])->update([
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
                return redirect()->route('admin-country', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }


    //add New Country
    Public function getNewCountry(Request $request){
    	return view('backend.country.new-country');
    }

    public function postNewCountry(Request $request) {

    	$this->validate($request, [
    		'name' => 'required|unique:countries',
    	],[
    		'name.required'=>'Country name must not be empty.'
    	]);
		$Countryes = new Country();
		$Countryes->name = $request["name"];
		if($Countryes->save()) {
			return redirect()->back()->with([
				'success_message' => "New country successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your country, try again"
			]);
		}

    }

    // Edit Country
    public function getEditCountry(Request $request, Country $Country) {
    	return view('backend.country.edit-country',[
    		"Countryes"=>$Country
		]);
    }	

     
    public function postEditCountry(Request $request, Country $Country) {
		$this->validate($request, [
    		'name' => 'required|unique:countries,name,'.$Country->id,
    	],[
    		'name.required'=>'Country name must not be empty.'
    	]);

		$Country->name = $request["name"];
        $Country->status = $request["status"];
		if($Country->update()) {
			return redirect()->route("admin-country", ['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "Country successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your Country, try again"
			]);
		}
    }

    //Delete Country
    public function postDeleteCountry(Request $request, Country $Country) {

    	if($Country->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "Country successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your Country, try again!"
			]);
		}

    }
}
