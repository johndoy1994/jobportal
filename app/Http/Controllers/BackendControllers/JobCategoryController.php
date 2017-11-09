<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class JobCategoryController extends BackendController
{
    public function getListing(Request $request)
    {
        $JobCategory = array();
    	if($request->has("search")) {
           $q = JobCategory::where('name', 'LIKE', '%'.$request->search.'%'); 
        } else {
    	   $q = JobCategory::where('name', 'LIKE', '%%');
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
        $recordsPerPage = $this->recordsPerPage("job-category-listing");
        $JobCategory = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name'];

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
		return view('backend.jobcategory.listing', [
			"JobCategory" => $JobCategory,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch
		]);
    }
    public function postListing(Request $request)
    {
        $action = $request->action;

        switch($action) {
            case "Apply":
                if($request["bulk_action"] == "delete")
                {   
                    if(count($request["category-id"]) > 0) {
                        foreach ($request["category-id"] as $value) {
                                $val = JobCategory::find($value);
                                $val->delete();
                        }
                        //JobCategory::whereIn('id', $request["category-id"])->delete();
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
                return redirect()->route('admin-job-category', ['search'=>$request->search_joblist])->withInput(Input::all());
                break;
        }
    }


    public function getAddNewItem()
    {
    	return view('backend.jobcategory.add-new');
    }

    public function postAddNewItem(Request $request)
    {	
    	$this->validate($request,[
    		'name' => "required|min:2|unique:job_categories"
    	]);

    	$JobCategory = new JobCategory();
    	$JobCategory->name = $request['name'];
    	if($JobCategory->save())
    	{
    		return redirect()->back()->with([
    			'success_message' => 'New job category added successfully!'
    		]);
    	}else{
    		return redirect()->back()->withInput(Input::all())->with([
    			'error_message'=> 'There was an error while adding your data, try again !'
    		]);
    	}
    }

    public function getEditJobCategory(Request $request, JobCategory $item)
    {
    	return view("backend.jobcategory.edit",['item' => $item]);
    }

    public function postEditJobCategory(Request $request, JobCategory $item)
    {
    	$this->validate($request, [
    		'name' => 'required|min:2'
		]);

		$item->name = $request["name"];
		if($item->update()) {
			return redirect()->route('admin-job-category',['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
				'success_message' => "Job category saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your job category, try again"
			]);
		}
    }

    public function getDeleteJobCategory(Request $request, JobCategory $item)
    {
    	if($item->delete())
    	{
    		return redirect()->back()->with([
    			'success_message' => 'Job category deleted successfully!'
    		]);
    	}else{
    		return redirect()->back()->with([
    			'error_message' => 'There was an error while deleting your job category, try again!'
    		]);
    	}
    }
}
