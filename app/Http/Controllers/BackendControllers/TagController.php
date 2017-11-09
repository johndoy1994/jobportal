<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\JobCategory;
use App\Models\JobTitle;
use App\Models\Tag;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class TagController extends BackendController
{
    //tag listing
    public function getListing(Request $request)
    {	
        
        if($request->has("search")) {
            $q = Tag::select('job_titles.title as job_titles','tags.*')->join('job_titles', 'tags.job_title_id', "=", "job_titles.id")->where('tags.name','like','%'.$request["search"].'%')->orWhere('job_titles.title','like','%'.$request["search"].'%');
        } else {
            $q =Tag::select('job_titles.title as job_titles','tags.*')->join('job_titles', 'tags.job_title_id', "=", "job_titles.id");
        }

    	if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            //$q->orderBy('tags.name');
            $q->orderBy('tags.created_at','desc');
        }
        
        $recordsPerPage = $this->recordsPerPage("tag-listing");

        $Tags = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name', 'job_titles'];

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

    	return view('backend.tag.listing', [
    		"Tags" => $Tags,
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
                        if(count($request["tagmultiple"]) > 0) {
                            foreach ($request["tagmultiple"] as $value) {
                                $val = Tag::find($value);
                                $val->delete();
                            }
                            //Tag::whereIn('id', $request["tagmultiple"])->delete();
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
                return redirect()->route('admin-tag', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }


    //add New tag
    Public function getNewTag(Request $request){
        $oldJobTitle=null;
        if(old()) {
            if(old("job_title_id")) {
                $jobTitleId = old("job_title_id");

                $jobTitle = PublicRepo::getJobTitle($jobTitleId);
                if($jobTitle) {
                    $oldJobTitle = [$jobTitle->id, $jobTitle->getTitle()];
                }
            }
        }
        $Category = JobCategory::orderBy('name')->get();
    	$JobTitleObj = JobTitle::select('id','title')->get();
        $JobTitle = array();
        foreach ($JobTitleObj as $key => $value) {
                $JobTitle[$value->id] = $value->title;
        }
        return view('backend.tag.new-tag',[
            'JobTitle'=>$JobTitle,
            'Categories'=>$Category,
            'oldJobTitle'=>$oldJobTitle
            ]);
    }

    public function postNewTag(Request $request) {

    	$this->validate($request, [
            'job_category_id'=>'required|findId:job_categories',
    		'job_title_id' => 'required|findId:job_titles,,job_category_id,'.$request->job_category_id,
            'name' => 'required',
    	],[
    		'job_title_id.required'=>'Please select Job Title.',
            'name.required'=>'Tag name must not be empty.'
    	]);

        $JobTitle = JobTitle::find($request['job_title_id']);
        if($JobTitle->tags()->where('name', $request["name"])->first()) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'Tag name already exists'
            ]);
        }

		$Tags = new Tag();
		$Tags->name = $request["name"];
        $Tags->job_title_id = $request["job_title_id"];
		if($Tags->save()) {
			return redirect()->back()->with([
				'success_message' => "New tag successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your tag, try again"
			]);
		}

    }

    // Edit Tag
    public function getEditTag(Request $request, Tag $Tag) {
    	 // Get Job title
        $Category = JobCategory::orderBy('name')->get();
        $JobTitleObj = JobTitle::select('id','title')->get();
        $JobTitle = array();
        foreach ($JobTitleObj as $key => $value) {
                $JobTitle[$value->id] = $value->title;
        }
        return view('backend.tag.edit-tag',[
    		"Tags"=>$Tag,'JobTitle'=>$JobTitle,'categorys'=>$Category
		]);
    }	

     
    public function postEditTag(Request $request, Tag $Tag) {
		$this->validate($request, [
            'job_category_id'=>'required|findId:job_categories',
            'job_title_id' => 'required|findId:job_titles,,job_category_id,'.$request->job_category_id,
            'name' => 'required',
    	],[
            'job_title_id.required'=>'Please select Job Title.',
    		'name.required'=>'Tag name must not be empty.'
    	]);

        $JobTitle = JobTitle::find($request['job_title_id']);
        
        if($JobTitle->tags()->where('name',$request->name)->where('id','!=',$Tag->id)->first()) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'Tag name already exists'
            ]);
        }

		$Tag->name = $request["name"];
        $Tag->job_title_id = $request["job_title_id"];
		if($Tag->update()) {
			return redirect()->route("admin-tag",['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "Tag successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your Tag, try again"
			]);
		}
    }

    //Delete Tag
    public function postDeleteTag(Request $request, Tag $Tag) {

    	if($Tag->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "Tag successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your Tag, try again!"
			]);
		}

    }

}
