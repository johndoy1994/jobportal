<?php

namespace App\Http\Controllers\BackendControllers\ImportExportControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Repos\ImportRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IESalaryRangeController extends BackendController
{
    public function getImport(Request $request) {
	 	$salarytype=ImportRepo::getAllSlaryTypelist();
        return view('backend.import-export.salary-range.import',['salarytype'=>$salarytype]);
    	
    }

    public function postImport(Request $request) {
    	$this->validate($request, [
    		'salary_type_id' => 'required|findId:salary_types',
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
    		if(isset($row[0]) && isset($row[1]) && is_numeric($row[0]) && is_numeric($row[1]) && !empty($row[0]) && !empty($row[1])){
    			if($row[0]<=$row[1]){
                    if(ImportRepo::addSalaryRange($row[0],$row[1],$request['salary_type_id'])) {
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
    	$salarytype=ImportRepo::getAllSlaryTypelist();
        return view('backend.import-export.salary-range.export',['salarytype'=>$salarytype]);
    }

    public function postExport(Request $request) {
    	$this->validate($request, [
    		'salary_type_id' => 'required|findId:salary_types',
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
    	$salarytype = PublicRepo::getSalaryType($request->salary_type_id);
        
    	if($salarytype) {
            
            $salarytype_name=$salarytype->toArray();
            
            $data=ImportRepo::getAllSalaryRange($request['salary_type_id'],$isDate,$startDate,$endDate);
			$filename = $this->writeCsv($data);

			$file = storage_path().'/app/'.$filename;
			$current_date = \Carbon\Carbon::now()->format('d_m_Y');
		    return Response::download($file, "salaryrange_".$salarytype_name['salary_type_name'].$current_date.".csv", ['content-type' => 'text/csv']); 
    	} else {
    		return redirect()->back()->with([
				'error_message' => "No Record founds!"
			]);
    	}
    }

    public function getSample(Request $request){
        
        $file = storage_path().'/app/import-sample/salaryrange_sample.csv';
        return Response::download($file, 'salaryrange_sample.csv', ['content-type' => 'text/csv']);        
    }
}
