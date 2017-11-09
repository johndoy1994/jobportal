<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\CmsPage;
use App\Repos\AdminRepo;
use Illuminate\Http\Request;

class CmsPagesController extends BackendController
{
	public $pages_name = array();

	public function __construct()
	{
        parent::__construct();
		$this->pages_name = ['aboutus'=>'About Us','contactus' => 'Contact Us', 'faqs'=>'FAQs'];
	}
    
	public function getCMSIndex(Request $request)
	{
		$q = CmsPage::select("*");
		if($request->has("search")) {   
        	$search=$request["search"];
            $q->where(function($q) use($search) {
                $q->orWhere('page_title','like','%'.$search.'%');
                $q->orWhere('page_name','like','%'.$search.'%');
                $q->orWhere('page_content','like','%'.$search.'%');
            });
        }
        if($request->has("PageId")) {   
            $search=$request["PageId"];

            $q->where(function($q) use($search) {
                $q->orWhere('id','like','%'.$search.'%');
            });
        }
        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            //$q->orderBy('page_title');
            $q->orderBy('created_at','desc');

        }
        $recordsPerPage = $this->recordsPerPage("cms-page-listing");

        $data = $q->paginate($recordsPerPage);
        $page = $request->has('page') ? $request->page : 1;
        
        $columns = ['page_title', 'page_name'];
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
		return view("backend.cms-pages.index", [
			'data'				=> $data,
			'sort_columns' 		=> $sort_columns,
			'isRequestSearch'	=> $isRequestSearch,
			'pages_name'		=> $this->pages_name
		]);
	}

	public function postIndexBulkAction(Request $request)
	{
		$action = $request->submit;
		switch($action) {
            case "Apply":
                switch($request["bulkid"]) {
                    case "deleted":
                    	if(count($request["cmspagemultiple"]) > 0) {
                            foreach ($request["cmspagemultiple"] as $value) {
                                $val = CmsPage::find($value);
                                $val->delete();
                            }
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
                        if(count($request["cmspagemultiple"]) > 0) {
                            $jobs = CmsPage::whereIn('id', $request["cmspagemultiple"])->update([
                                'status'=>'1'
                            ]);
                            return redirect()->back()->with([
                                    'success_message' => "Active successfully !!"
                                ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to active!!"
                            ]);
                        }
                    break;                    

                    case "inactive":
                        if(count($request["cmspagemultiple"]) > 0) {
                            $jobs = CmsPage::whereIn('id', $request["cmspagemultiple"])->update([
                                    'status'=>'0'    
                                ]);
                            return redirect()->back()->with([
                                        'success_message' => "Inactive successfully !!"
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
        }
	}

	public function getAddCmsPage(Request $request)
    {
    	return view('backend.cms-pages.add',['pages_name'=>$this->pages_name]);
    }

    public function getEditCmsPage(Request $request)
    {
    	$data = CmsPage::select("*")
    			->where("id",$request->id)
    			->where("page_name",$request->page)->first();
    	return view('backend.cms-pages.edit',['data'=> $data,'pages_name'=>$this->pages_name]);
    }

    public function postSaveCmsPage(Request $request)
    {
    	$rules = [
    		'page_title' => 'required',
    		'page_content' => 'required'
    	];
    	if($request->action == "add")
    	{
    		$rules['page_name'] = ['required','unique:cms_pages'];
    	}
    	$this->validate($request, $rules);

    	$page_title 	= $request->input('page_title');
    	$page_content 	= $request->input('page_content');
    	$page_name 		= $request->input('page_name');
    	
    	$data = array(
    			'page_title' => $page_title,
    			'page_name'  => $page_name,
    			'page_content' => $page_content
    		);
    	$result = AdminRepo::saveCmsPageByPageName($page_name, $data);
    	if($result)
    		return redirect()->route('admin-cms-page')->with([
                        'success_message' => "Data saved successfully !!"
                    ]);
    	else
    		return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => "Could not save data, try again !!"
                ]);
	}

	public function getActiveInactiveCmsPage(Request $request)
	{
		$pageId = $request->get('PageId');
		$pageNewStatus = $request->get('action');

		$CmsPage = CmsPage::where("id",$pageId)->first();
		if(!empty($CmsPage))
		{
			$CmsPage->status = $pageNewStatus;
			if($CmsPage->update())
				return redirect()->route('admin-cms-page')->with([
	                        'success_message' => "Status changed successfully !!"
	                    ]);
	    	else
	    		return redirect()->back()->with([
	                    'error_message' => "Could not change status, try again !!"
	                ]);
		}
	}
}
