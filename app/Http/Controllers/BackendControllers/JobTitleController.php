<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\JobCategory;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class JobTitleController extends BackendController
{
    // Index
    public function getListing(Request $request)
    {

        $JobTitle = array();
        if($request->has("search")) {
           $q = JobTitle::select('job_categories.name','job_categories.id as catid','job_titles.*')->join('job_categories','job_titles.job_category_id','=','job_categories.id')->where('name', 'LIKE', '%'.$request->search.'%')->orWhere('title', 'LIKE', '%'.$request->search.'%');
        } else {
           $q = JobTitle::select('job_categories.name','job_categories.id as catid','job_titles.*')->join('job_categories','job_titles.job_category_id','=','job_categories.id');
        }

        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            //$q->orderBy('job_categories.name');
            $q->orderBy('job_titles.created_at','desc');
        }
        
        $recordsPerPage = $this->recordsPerPage("job-title-listing");

        $JobTitle = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['title', 'name'];

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
        return view('backend.jobtitle.listing', [
            "JobTitle" => $JobTitle,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch
        ]);
    }

    // Search & Bulk Action
    public function postListing(Request $request)
    {
        $action = $request->action;
        switch($action) {
            case "Apply":
                if($request["bulk_action"] == "delete")
                {   
                    if(count($request["jobtitle-ids"]) > 0) {
                        foreach ($request["jobtitle-ids"] as $value) {
                                $val = JobTitle::find($value);
                                $val->delete();
                        }
                        //JobTitle::whereIn('id', $request["jobtitle-ids"])->delete();
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

                return redirect()->route('admin-job-title', ['search'=>$request["search_joblist"]])->withInput(Input::all());
                break;
        }
    }


    public function getAddNewItem()
    {
        // Get Job Category
        $JobCategoryObj = JobCategory::select('id','name')->orderBy('name')->get();
        $JobCategory = array();
        foreach ($JobCategoryObj as $key => $value) {
                $JobCategory[$value->id] = $value->name;
        }
        
        return view('backend.jobtitle.add-new',['JobCategory'=>$JobCategory]);
    }

    public function postAddNewItem(Request $request)
    {   

        $this->validate($request, [
            'name' => 'required|findId:job_categories',
            'title' => 'required|min:2'
        ],[
            'name.required' => 'Please select job category first!',
            'title.required' => 'Job title required!'
          ]
        );

        $jobCategory = JobCategory::find($request['name']);
        if($jobCategory->jobtitles()->where('title', $request["title"])->first()) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'Job Title already exists'
            ]);
        }

        $JobTitle = new JobTitle();
        $JobTitle->job_category_id = $request['name'];
        $JobTitle->title = $request['title'];
        if($JobTitle->save())
        {
            return redirect()->back()->with([
                'success_message' => 'New job title added successfully!'
            ]);
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'There was an error while adding your data, try again !'
            ]);
        }
    }

    public function getEditJobTitle(Request $request, JobTitle $item)
    {
        // Get Job Category
        $JobCategoryObj = JobCategory::select('id','name')->orderBy('name')->get();
        $JobCategory = array();
        foreach ($JobCategoryObj as $key => $value) {
                $JobCategory[$value->id] = $value->name;
        }

        return view("backend.jobtitle.edit",['item' => $item,'JobCategory' => $JobCategory]);
    }

    public function postEditJobTitle(Request $request, JobTitle $item)
    {
        $this->validate($request, [
            'name' => 'required|findId:job_categories',
            'title' => 'required|min:2'
        ],[
            'name.required' => 'Please select job category first!',
            'title.required' => 'Job title required!'
          ]
        );

        $jobCategory = JobCategory::find($request['name']);
        
        if($jobCategory->jobtitles()->where('Title',$request->title)->where('id','!=',$item->id)->first()) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'Job Title already exists'
            ]);
        }

        $item->job_category_id = $request['name'];
        $item->title = $request['title'];

        if($item->update()) {
            return redirect()->route('admin-job-title',['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "Job title saved!"
            ]);
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while saving your Job title, try again"
            ]);
        }
    }

    public function getDeleteJobCategory(Request $request, JobCategory $item)
    {
        if($item->delete())
        {
            return redirect()->back()->with([
                'success_message' => 'Job tile deleted successfully!'
            ]);
        }else{
            return redirect()->back()->with([
                'error_message' => 'There was an error while deleting your Job title, try again!'
            ]);
        }
    }
}
