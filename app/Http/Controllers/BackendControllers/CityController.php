<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CityController extends BackendController
{
    //City listing
    public function getListing(Request $request)
    {	
        if($request->has("search")) {                   
            $q = City::select('countries.name as country_name','states.name as state_name','cities.*')->join('states', 'states.id', "=", "cities.state_id")->join('countries', 'states.country_id', "=", "countries.id")->where('cities.name','like','%'.$request["search"].'%')->orWhere('states.name','like','%'.$request["search"].'%')->orWhere('countries.name','like','%'.$request["search"].'%');
        } else {
            $q =City::select('countries.name as country_name','states.name as state_name','cities.*')->join('states', 'states.id', "=", "cities.state_id")->join('countries', 'states.country_id', "=", "countries.id");
        }
        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            //$q->orderBy('cities.name');
            $q->orderBy('cities.created_at','desc');
        }
        $recordsPerPage = $this->recordsPerPage("city-listing");
        $Cities = $q->paginate($recordsPerPage);
    	
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name', 'state_name', 'country_name', 'status'];

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
      	return view('backend.city.listing', [
    		"Cities" => $Cities,
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
                        if(count($request["citymultiple"]) > 0) {
                           foreach ($request["citymultiple"] as $value) {
                                $val = City::find($value);
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

                    case "active":
                        if(count($request["citymultiple"]) > 0) {
                            $countries = City::whereIn('id', $request["citymultiple"])->update([
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
                        if(count($request["citymultiple"]) > 0) {
                            $countries = City::whereIn('id', $request["citymultiple"])->update([
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
                return redirect()->route('admin-city', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }


    //add New City
    Public function getNewCity(Request $request){
    	
        $Countries = Country::orderBy('name')->get();
        $oldstateTitle = null;
        if(old()) {
            if(old("state_id")) {
                $stateId = old("state_id");
                $stateTitle = PublicRepo::getState($stateId);
                if($stateTitle) {
                    $oldstateTitle = [$stateTitle->id, $stateTitle->getName()];
                }
            }
        }
        return view('backend.city.new-city',[
            'countries'=>$Countries,
            'oldstateTitle'=>$oldstateTitle
            ]);
    }

    public function postNewCity(Request $request) {

    	$this->validate($request, [
    		'country_id'=> 'required|findId:countries',
            'state_id' => 'required|findId:states,,country_id,'.$request->country_id,
            'name' => 'required',
    	],[
    		'country_id.required'=>'Please select Country name.',
            'state_id.required'=>'Please select state name.',
            'name.required'=>'City name must not be empty.'
    	]);

        $State = State::find($request['state_id']);
        if($State->Cities()->where('name', $request["name"])->first()) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'City name already exists'
            ]);
        }
        $stateData=PublicRepo::getState($request->state_id);
        $CityData=PublicRepo::getCountry($request->country_id);
        list($valid_location, $location_point, $clearAddress)=PublicRepo::getGeoLocationPoint($request->name.", ".$stateData->name.", ".$CityData->name);
		$latitude=0;
        $longitude=0;
        if($valid_location){
            $latitude=$location_point[0];
            $longitude=$location_point[1];
        }
        $Cities = new City();
		$Cities->name = $request["name"];
        $Cities->state_id = $request["state_id"];
        $Cities->latitude = $latitude;
        $Cities->longitude = $longitude;
        if($Cities->save()) {
			return redirect()->back()->with([
				'success_message' => "New city successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your City, try again"
			]);
		}

    }

    // Edit City
    public function getEditCity(Request $request, City $City) {
    	
        $Countries = Country::orderBy('name')->get();
        return view('backend.city.edit-city',[
    		"Cities"=>$City,'countries'=>$Countries
		]);
    }	

     
    public function postEditCity(Request $request, City $City) {
		$this->validate($request, [
            'country_id'=> 'required|findId:countries',
            'state_id' => 'required|findId:states,,country_id,'.$request->country_id,
    		'name' => 'required',
    	],[
            'state_id.required'=>'Please select state name.',
    		'name.required'=>'City name must not be empty.'
    	]);

        $State = State::find($request['state_id']);
        
        if($State->cities()->where('name',$request->name)->where('id','!=',$City->id)->first()) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'City name already exists'
            ]);
        }
        $stateData=PublicRepo::getState($request->state_id);
        $CityData=PublicRepo::getCountry($request->country_id);
        list($valid_location, $location_point, $clearAddress)=PublicRepo::getGeoLocationPoint($request->name.", ".$stateData->name.", ".$CityData->name);
        $latitude=0;
        $longitude=0;
        if($valid_location){
            $latitude=$location_point[0];
            $longitude=$location_point[1];
        }
		$City->name = $request["name"];
        $City->state_id = $request["state_id"];
        $City->status = $request["status"];
        $City->latitude = $latitude;
        $City->longitude = $longitude;
		if($City->update()) {
			return redirect()->route("admin-city",['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "City successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your City, try again"
			]);
		}
    }

    //Delete City
    public function postDeleteCity(Request $request, City $City) {

    	if($City->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "City successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your city, try again!"
			]);
		}

    }
}
