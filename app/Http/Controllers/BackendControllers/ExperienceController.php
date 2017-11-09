<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ExperienceController extends BackendController
{
    // Index
    public function getListing(Request $request)
    {

        $Experience = array();
        if($request->has("search")) {
           $q = Experience::where('exp_name', 'LIKE', '%'.$request->search.'%');
        } else {
           $q = Experience::where('exp_name', 'LIKE', '%%');
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
        $recordsPerPage = $this->recordsPerPage("experience-listing");
        $Experience = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['exp_name','order'];

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
        return view('backend.experience.listing', [
            "Experience" => $Experience,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch
        ]);
    }

    public function postMoveOrder(Request $request, Experience $experience, $action) {

        if(!in_array($action, ['up','down'])) {
            return redirect()->back()->with(['error_message'=>"Unknown action, try again"]);
        }

        $current_order  = $experience->order;

        if(($current_order == Experience::getFirstOrder() && $action == "up") || ($current_order == Experience::getLastOrder() && $action=="down")) {
            return redirect()->back()->with(['error_message'=>"Invalid action, try again"]);
        }
        
        switch($action) {
            case "up":
                $new_order = Experience::getLastOrder($current_order);//$current_order - 1;
                $_experience = Experience::where('order', $new_order)->first();
                if($_experience) {
                    $_experience->order = $current_order;
                    $_experience->update();
                }
                $experience->order = $new_order;
                $experience->update();
                return redirect()->back()->with(['success_message' => "Moved"]);
            break;
            case "down":
                $new_order = Experience::getFirstOrder($current_order);//$current_order + 1;
                $_experience = Experience::where('order', $new_order)->first();
                if($_experience) {
                    $_experience->order = $current_order;
                    $_experience->update();
                }
                $experience->order = $new_order;
                $experience->update();
                return redirect()->back()->with(['success_message' => "Moved"]);
            break;
        }

        return redirect()->back();

    }

    // Search & Bulk Action
    public function postListing(Request $request)
    {
        $action = $request->action;
        switch($action) {
            case "Apply":
                if($request["bulk_action"] == "delete")
                {   
                    if(count($request["expl-ids"]) > 0) {
                        foreach ($request["expl-ids"] as $value) {
                            $val = Experience::find($value);

                            $val->delete();

                        }

                        //Experience::whereIn('id', $request["expl-ids"])->delete();
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

                return redirect()->route('admin-experience', ['search'=>$request["search_experience"]])->withInput(Input::all());
                break;
        }
    }


    public function getAddNewItem()
    {
        return view('backend.experience.add-new');
    }

    public function postAddNewItem(Request $request)
    {   
        $this->validate($request, [
            'exp_name' => 'required|min:1'
        ]);
        if(Experience::where('exp_name', '=', $request->exp_name)->first()) {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=>"Already exists!!"]);
        }
        $Experience = new Experience();
        $Experience->order = Experience::getLastOrder() + 1;
        $Experience->exp_name = $request['exp_name'];
        if($Experience->save())
        {
            return redirect()->back()->with([
                'success_message' => 'Experience added successfully!'
            ]);
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'There was an error while adding your data, try again !'
            ]);
        }
    }

    public function getEditExperience(Request $request, Experience $item)
    {
        return view("backend.experience.edit",['item' => $item]);
    }

    public function postEditExperience(Request $request, Experience $item)
    {
        $this->validate($request, [
            'exp_name' => 'required|min:1'
        ]);
        if(Experience::where('exp_name', '=', $request->exp_name)->where('id', '!=', $item->id)->first()) {
            return redirect()->back()->with(['error_message'=>"Already exists!!"]);
        }
        $item->exp_name = $request['exp_name'];
        if($item->update()) {
            return redirect()->route('admin-experience',['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "Experience saved!"
            ]);
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while saving your experience, try again"
            ]);
        }
    }

    public function getDeleteExperience(Request $request, Experience $item)
    {
        if($item->delete())
        {
            return redirect()->back()->with([
                'success_message' => 'Experience deleted successfully!'
            ]);
        }else{
            return redirect()->back()->with([
                'error_message' => 'There was an error while deleting your experience, try again!'
            ]);
        }
    }
}
