<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\SalaryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class SalaryTypeController extends BackendController
{
    // Index
    public function getListing(Request $request)
    {
        $SalaryType = array();
        if($request->has("search")) {
           $q = SalaryType::where('salary_type_name', 'LIKE', '%'.$request->search.'%'); 
        } else {
           $q = SalaryType::where('salary_type_name', 'LIKE', '%%');
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

        $recordsPerPage = $this->recordsPerPage("salarytype-listing");
        
        $SalaryType = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['salary_type_name','order'];

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
        return view('backend.salarytype.listing', [
            "SalaryType" => $SalaryType,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch
        ]);
    }

    public function postMoveOrder(Request $request, SalaryType $salarytype, $action) {

        if(!in_array($action, ['up','down'])) {
            return redirect()->back()->with(['error_message'=>"Unknown action, try again"]);
        }

        $current_order  = $salarytype->order;

        if(($current_order == SalaryType::getFirstOrder() && $action == "up") || ($current_order == SalaryType::getLastOrder() && $action=="down")) {
            return redirect()->back()->with(['error_message'=>"Invalid action, try again"]);
        }
        
        switch($action) {
            case "up":
                $new_order = SalaryType::getLastOrder($current_order);//$current_order - 1;
                $_salarytype = SalaryType::where('order', $new_order)->first();
                if($_salarytype) {
                    $_salarytype->order = $current_order;
                    $_salarytype->update();
                }
                $salarytype->order = $new_order;
                $salarytype->update();
                return redirect()->back()->with(['success_message' => "Moved"]);
            break;
            case "down":
                $new_order = SalaryType::getFirstOrder($current_order);//$current_order + 1;
                $_salarytype = SalaryType::where('order', $new_order)->first();
                if($_salarytype) {
                    $_salarytype->order = $current_order;
                    $_salarytype->update();
                }
                $salarytype->order = $new_order;
                $salarytype->update();
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
                    if(count($request["salarytype-ids"]) > 0) {
                        foreach ($request["salarytype-ids"] as $value) {
                                $val = SalaryType::find($value);
                                $val->delete();
                        }
                        //SalaryType::whereIn('id', $request["salarytype-ids"])->delete();
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

                return redirect()->route('admin-salary-type', ['search'=>$request["search_salarytypelist"]])->withInput(Input::all());
                break;
        }
    }


    public function getAddNewItem()
    {
        return view('backend.salarytype.add-new');
    }

    public function postAddNewItem(Request $request)
    {   
        $this->validate($request,[
            'salary_type_name' => "required|min:2"
        ]);
        if(SalaryType::where('salary_type_name', '=', $request->salary_type_name)->first()) {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=>"Already exists!!"]);
        }
        $SalaryType = new SalaryType();
        $SalaryType->order = SalaryType::getLastOrder() + 1;
        $SalaryType->salary_type_name = $request['salary_type_name'];
        if($SalaryType->save())
        {
            return redirect()->back()->with([
                'success_message' => 'New salary type added successfully!'
            ]);
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'There was an error while adding your data, try again !'
            ]);
        }
    }

    public function getEditSalaryType(Request $request, SalaryType $item)
    {
        return view("backend.salarytype.edit", ['item' => $item]);
    }

    public function postEditSalaryType(Request $request, SalaryType $item)
    {
        $this->validate($request,[
            'salary_type_name' => "required|min:2"
        ]);
        if(SalaryType::where('salary_type_name', '=', $request->salary_type_name)->where('id', '!=', $item->id)->first()) {
            return redirect()->back()->with(['error_message'=>"Already exists!!"]);
        }
        $item->salary_type_name = $request['salary_type_name'];

        if($item->update()) {
            return redirect()->route('admin-salary-type',['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "Salary type saved!"
            ]);
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while saving your item, try again"
            ]);
        }
    }

    public function getDeleteSalaryType(Request $request, SalaryType $item)
    {
        if($item->delete())
        {
            return redirect()->back()->with([
                'success_message' => 'Salary Type deleted successfully!'
            ]);
        }else{
            return redirect()->back()->with([
                'error_message' => 'There was an error while deleting your Salary Type, try again!'
            ]);
        }
    }
}
