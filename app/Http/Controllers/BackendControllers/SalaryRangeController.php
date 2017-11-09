<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\SalaryRange;
use App\Models\SalaryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class SalaryRangeController extends BackendController
{
    // Index
    public function getListing(Request $request)
    {
        
        if($request->has("search")) {
           $q = SalaryRange::select('salary_types.salary_type_name','salary_types.id as salTypId','salary_ranges.*')->join("salary_types","salary_ranges.salary_type_id","=","salary_types.id")->where('range_from', 'LIKE', '%'.$request->search.'%')->orWhere('range_to', 'LIKE', '%'.$request->search.'%')->orWhere('salary_type_name', 'LIKE', '%'.$request->search.'%'); 
        } else {
           $q = SalaryRange::select('salary_types.salary_type_name','salary_types.id as salTypId','salary_ranges.*')->join("salary_types","salary_ranges.salary_type_id","=","salary_types.id");
        }

        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            $q->orderBy('salary_types.order');
            $q->orderBy('salary_ranges.range_from');
        }
        
        $recordsPerPage = $this->recordsPerPage("salaryrange-listing");
        $SalaryRange = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['order', 'range_from', 'range_to'];

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
        return view('backend.salaryrange.listing', [
            "SalaryRange" => $SalaryRange,
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
                    if(count($request["salaryrange-ids"]) > 0) {
                        foreach ($request["salaryrange-ids"] as $value) {
                                $val = SalaryRange::find($value);
                                $val->delete();
                        }
                        //SalaryRange::whereIn('id', $request["salaryrange-ids"])->delete();
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

                return redirect()->route('admin-salary-range', ['search'=>$request["search_salaryrangelist"]])->withInput(Input::all());
                break;
        }
    }


    public function getAddNewItem()
    {
        // Get Salary All Types
        $SalaryTypeObj = SalaryType::select('id','salary_type_name')->orderBy('salary_type_name')->get();
        $SalaryTypes = array();
        foreach ($SalaryTypeObj as $key => $value) {
                $SalaryTypes[$value->id] = $value->salary_type_name;
        }

        return view('backend.salaryrange.add-new',['SalaryTypes' => $SalaryTypes]);
    }

    public function postAddNewItem(Request $request)
    {   
        $this->validate($request,[
            'salary_type_id'=>"required|findId:salary_types",
            'range_from' => "required",
            'range_to' => "required"
        ]);
        if($request['range_from'] > $request['range_to'])
        {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'The Field range from should be less than to range to field!'
            ]);
        }
        $validateSalRange = SalaryRange::where("salary_type_id", $request['salary_type_id'])
                                       ->where("range_from", $request['range_from'])->first();
        if($validateSalRange)
        {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Salary range already exists with type and range from!'
            ]);
        }

        $SalaryRange = new SalaryRange();
        $SalaryRange->salary_type_id = $request['salary_type_id'];
        $SalaryRange->range_from = $request['range_from'];
        $SalaryRange->range_to = $request['range_to'];
        if($SalaryRange->save())
        {
            return redirect()->back()->with([
                'success_message' => 'New salary range added successfully!'
            ]);
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message'=> 'There was an error while adding your data, try again !'
            ]);
        }
    }

    public function getEditSalaryRange(Request $request, SalaryRange $item)
    {
        // Get Salary All Types
        $SalaryTypeObj = SalaryType::select('id','salary_type_name')->orderBy('salary_type_name')->get();
        $SalaryTypes = array();
        foreach ($SalaryTypeObj as $key => $value) {
                $SalaryTypes[$value->id] = $value->salary_type_name;
        }

        return view("backend.salaryrange.edit", ['item' => $item, 'SalaryTypes' => $SalaryTypes]);
    }

    public function postEditSalaryRange(Request $request, SalaryRange $item)
    {
        $this->validate($request,[
            'salary_type_id'=>"required|findId:salary_types",
            'range_from' => "required",
            'range_to' => "required"
        ]);

        if($request['range_from'] > $request['range_to'])
        {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'The Field range from should be less than to range to field!'
            ]);
        }
        $validateSalRange = SalaryRange::where("salary_type_id", $request['salary_type_id'])
                                       ->where("range_from", $request['range_from'])
                                       ->where("id", "!=", $item->id)->first();
        if($validateSalRange)
        {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Salary range already exists with type and range from!'
            ]);
        }

        $item->salary_type_id = $request['salary_type_id'];
        $item->range_from = $request['range_from'];
        $item->range_to = $request['range_to'];

        if($item->update()) {
            return redirect()->route('admin-salary-range',['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "Salary range saved!"
            ]);
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while saving your item, try again"
            ]);
        }
    }

    public function getDeleteSalaryType(Request $request, SalaryRange $item)
    {
        if($item->delete())
        {
            return redirect()->back()->with([
                'success_message' => 'Salary range deleted successfully!'
            ]);
        }else{
            return redirect()->back()->with([
                'error_message' => 'There was an error while deleting your salary range, try again!'
            ]);
        }
    }
}
