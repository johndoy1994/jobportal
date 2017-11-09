<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\ExperienceLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ExpLevelController extends BackendController
{
    public function getListing(Request $request)
    {
        $ExpLevel = array();
    	if($request->has("search")) {
           $q = ExperienceLevel::where('level', 'LIKE', '%'.$request->search.'%'); 
        } else {
    	   $q = ExperienceLevel::where('level', 'LIKE', '%%');
        }

        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            $q->orderBy('order');
        }
        $recordsPerPage = $this->recordsPerPage("explevel-listing");
        $ExpLevel = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['level','order'];

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
		return view('backend.explevel.listing', [
			"ExpLevel" => $ExpLevel,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch
		]);
    }

    public function postMoveOrder(Request $request, ExperienceLevel $item, $action) {

        if(!in_array($action, ['up','down'])) {
            return redirect()->back()->with(['error_message'=>"Unknown action, try again"]);
        }

        $current_order  = $item->order;

        if(($current_order == ExperienceLevel::getFirstOrder() && $action == "up") || ($current_order == ExperienceLevel::getLastOrder() && $action=="down")) {
            return redirect()->back()->with(['error_message'=>"Invalid action, try again"]);
        }
        
        switch($action) {
            case "up":
                $new_order = ExperienceLevel::getLastOrder($current_order);//$current_order - 1;
                $_item = ExperienceLevel::where('order', $new_order)->first();
                if($_item) {
                    $_item->order = $current_order;
                    $_item->update();
                }
                $item->order = $new_order;
                $item->update();
                return redirect()->back()->with(['success_message' => "Moved"]);
            break;
            case "down":
                $new_order = ExperienceLevel::getFirstOrder($current_order);//$current_order + 1;
                $_item = ExperienceLevel::where('order', $new_order)->first();
                if($_item) {
                    $_item->order = $current_order;
                    $_item->update();
                }
                $item->order = $new_order;
                $item->update();
                return redirect()->back()->with(['success_message' => "Moved"]);
            break;
        }

        return redirect()->back();

    }

    public function postListing(Request $request)
    {
        $action = $request->action;

        switch($action) {
            case "Apply":
                if($request["bulk_action"] == "delete")
                {   
                    if(count($request["explevel-ids"]) > 0) {
                        foreach ($request["explevel-ids"] as $value) {
                            $val = ExperienceLevel::find($value);
                            $val->delete();
                        }
                        //ExperienceLevel::whereIn('id', $request["explevel-ids"])->delete();
                        return redirect()->back()->with([
                            'success_message' => "Deleted successfully"
                        ]);
                    } else {
                        return redirect()->back()->with([
                            'error_message' => "No items selected to delete!!"
                        ]);
                    }
                }
                return redirect()->back()->with([
                        'error_message' => 'Please select any bulk action!'
                    ]);
                break;

            case "Search":
                return redirect()->route('admin-exp-level', ['search'=>$request->search_exp_level])->withInput(Input::all());
                break;
        }
    }


    public function getAddNewItem()
    {
    	return view('backend.explevel.add-new');
    }

    public function postAddNewItem(Request $request)
    {	
    	$this->validate($request,[
    		'level' => "required|min:2"
    	]);
        if(ExperienceLevel::where('level', '=', $request->level)->first()) {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=>"Already exists!!"]);
        }
    	$ExperienceLevel = new ExperienceLevel();
        $ExperienceLevel->order = ExperienceLevel::getLastOrder() + 1;
    	$ExperienceLevel->level = $request['level'];
    	if($ExperienceLevel->save())
    	{
    		return redirect()->back()->with([
    			'success_message' => 'Experience level added successfully!'
    		]);
    	}else{
    		return redirect()->back()->withInput(Input::all())->with([
    			'error_message'=> 'There was an error while adding your data, try again !'
    		]);
    	}
    }

    public function getEditExperienceLevel(Request $request, ExperienceLevel $item)
    {
    	return view("backend.explevel.edit",['item' => $item]);
    }

    public function postEditExperienceLevel(Request $request, ExperienceLevel $item)
    {
    	$this->validate($request, [
    		'level' => 'required|min:2'
		]);
        if(ExperienceLevel::where('level', '=', $request->level)->where('id', '!=', $item->id)->first()) {
            return redirect()->back()->with(['error_message'=>"Already exists!!"]);
        }
		$item->level = $request["level"];
		if($item->update()) {
			return redirect()->route('admin-exp-level',['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
				'success_message' => "Experience level saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your item, try again"
			]);
		}
    }

    public function getDeleteExperienceLevel(Request $request, ExperienceLevel $item)
    {
    	if($item->delete())
    	{
    		return redirect()->back()->with([
    			'success_message' => 'Experience level deleted successfully!'
    		]);
    	}else{
    		return redirect()->back()->with([
    			'error_message' => 'There was an error while deleting your experience level, try again!'
    		]);
    	}
    }
}
