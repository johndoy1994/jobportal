<?php

namespace App\Http\Controllers\BackendControllers\ImportExportControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Repos\ImportRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IEJobTitleController extends BackendController
{
    public function getImport(Request $request) {
	 	
	 	$JobCategoryObj =ImportRepo::getAllJobTitlelist(); 
        $JobCategory = array();
        foreach ($JobCategoryObj as $key => $value) {
                $JobCategory[$value->id] = $value->name;
        }
	 	
	 	return view('backend.import-export.job-title.import',['JobCategory'=>$JobCategory]);
    }

    public function postImport(Request $request) {
    	$this->validate($request, [
    		'job_category_id' => 'required|findId:job_categories',
            'filename' => 'required|mimes:csv,txt'
        ],[
            'filename.required'=>'Please select csv file.',
            'filename.mimes' =>'File type must be csv.' 
        ]);

    	$csvFilePath = $request->file('filename')->getRealPath();

    	$rows = $this->readCsv($csvFilePath);
    	$successCount 	= 0;
    	$failCount 		= 0;
    	$skipedRows		= [];
    	foreach($rows as $row) {
    		if(isset($row[0]) && !empty($row[0])) {
                $name=Utility::alpha_numeric_space($row[0]);
                if($name){
        			if(ImportRepo::addJobTitle($row[0],$request['job_category_id'])) {
        				$successCount++;
        			} else {
        				$skipedRows[] = $row;
        			}
                }else{
                    $skipedRows[] = $row;
                }
    		} else {
    			$skipedRows[] = $row;
    		}
    	}
    	$failCount = count($rows) - $successCount;
    	return redirect()->back()->with([
				'successCount' => $successCount,
				'failCount' => $failCount,
				'skipedRows' => $skipedRows
			]);
    	
    	

   	}

    public function getExport() {
    	$JobCategoryObj =ImportRepo::getAllJobTitlelist(); 
        $JobCategory = array();
        foreach ($JobCategoryObj as $key => $value) {
                $JobCategory[$value->id] = $value->name;
        }
    	return view('backend.import-export.job-title.export',['JobCategory'=>$JobCategory]);
    }

    public function postExport(Request $request) {
    	$this->validate($request, [
    		'job_category_id' => 'required|findId:job_categories',
        ]);
        $startDate='';
        $endDate="";
        $isDate=false;
        if($request->has('export_type')){
            if($request->export_type==0){
                $isDate=false;
            }elseif($request->export_type==1){
                list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date,false);
                if(!$dateStatus[0]){
                    return redirect()->back()->with([
                        'error_message' => $dateMessage[0]
                    ]);
                }

                $startDate=$request->starting_date;
                $endDate=$request->ending_date;
                $isDate=true;

            }else{
                return redirect()->back()->with([
                        'error_message' => "please select option all or date"
                ]);
            }     
        }else{
            return redirect()->back()->with([
                    'error_message' => "please select option all or date"
            ]);
        }

    	$jobcategory = PublicRepo::getJobCategory($request->job_category_id);
        
    	if($jobcategory) {
            
            $jobcategory_name=$jobcategory->toArray();
            $data=ImportRepo::getAllJobTitle($request['job_category_id'],$isDate,$startDate,$endDate);
			$filename = $this->writeCsv($data);

			$file = storage_path().'/app/'.$filename;
			$current_date = \Carbon\Carbon::now()->format('d_m_Y');
		    return Response::download($file, "jobtitle_".$jobcategory_name['name'].$current_date.".csv", ['content-type' => 'text/csv']); 
    	} else {
    		return redirect()->back()->with([
				'error_message' => "No Record founds!"
			]);
    	}
    }

    public function getSample(Request $request){
        
        $file = storage_path().'/app/import-sample/jobtitle_sample.csv';
        return Response::download($file, 'jobtitle_sample.csv', ['content-type' => 'text/csv']);        
    }
}
