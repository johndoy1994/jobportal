<?php

namespace App\Http\Controllers\BackendControllers\ImportExportControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Repos\AdminRepo;
use App\Repos\ImportRepo;
use App\Repos\UserRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IEEmployerController extends BackendController
{
    public function getImport() {
    	return view('backend.import-export.employer.import');
    }

    public function postImport(Request $request) {
    	$this->validate($request, [
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
    	$msg 			= [];
    	$status = [];
    	$message = [];
    	$count =0;
    	foreach($rows as $row) {
    		if(isset($row[0],$row[1],$row[2],$row[4],$row[5],$row[6],$row[7]) && !empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[4]) && !empty($row[5]) && !empty($row[6]) && !empty($row[7])) {
    			list($status[0],$message[0],$recruiter)=ImportRepo::getRecruiter($row[0]);
    			list($status[1],$message[1],$country)=ImportRepo::getCountry($row[5]);
    			list($status[2],$message[2],$state)=ImportRepo::getState($row[6],$country);
    			list($status[3],$message[3],$city)=ImportRepo::getCity($row[7],$state);
    			list($status[4],$message[4],$userdata)=ImportRepo::checkUserEmail($row[4]);
                list($status[5],$message[5],$userdata)=ImportRepo::checkvalidationUserphone($row[3]);
    			
    			if($status[0] && $status[1] && $status[2] && $status[3] && $status[4]){
					list($userstatus[0],$usermsg[0],$user)=ImportRepo::addEmployerUser($row[2],$row[3],$row[4]);
					if($user){
                        if($row[9]){
                            $postal_code=Utility::postalCode_validation($row[9]);
                        }else{
                            $postal_code=true;
                        }
                        if($postal_code){
                            list($userstatus[1],$usermsg[1]) = UserRepo::addUserAddress($user, "residance", array(
        			            'city_id'       => $city->id,
        			            'postal_code'   => $row[9],
        			            'street'        => $row[8]
        			        ));
                        }else{
                            list($userstatus[1],$usermsg[1])=[false, "Please enter valid postalcode, try again"];
                        }
                    }else{
                        list($userstatus[1],$usermsg[1])=[false, "There was an error while adding your User, try again"];
                    }
                    if($userstatus[0] && $userstatus[1]){	
    					if(ImportRepo::addEmployer($recruiter->id,$user->id,$row[1],$row[10])) {
    	    				$successCount++;
    	    			} else {
                            $user->delete();
                            $blackfield="There was an error while adding your employer, try again";
                            $msg[$count][] = $blackfield;
    	    				$skipedRows[$count][] = $row;
    	    			}    				
                    }else{
                        if($user){
                            $user->delete();
                        }
                        for($i=0;$i<count($userstatus);$i++) {
                            if(!$userstatus[$i]) {
                                $msg[$count][] = $usermsg[$i];
                            }
                        }
                        $skipedRows[$count][] = $row;
                    }
    			}else{
    				for($i=0;$i<count($status);$i++) {
    					if(!$status[$i]) {
    						$msg[$count][] = $message[$i];
    					}
    				}
    				$skipedRows[$count][] = $row;
    			}
    		} else {
                $blackfield="Please fill all required field";
                $msg[$count][] = $blackfield;
    			$skipedRows[$count][] = $row;
    		}
    		$count++;
            
    	}
    	
    	$failCount = count($rows) - $successCount;
    	return redirect()->back()->with([
				'successCount' => $successCount,
				'failCount'    => $failCount,
				'skipedRows'   => $skipedRows,
				'msg'		   =>$msg
			]);
    	
    	

   	}

    public function getExport() {
    	return view('backend.import-export.employer.export');
    }

    public function postExport(Request $request) {
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
    	$dataAll=ImportRepo::getEmployerAllRecord($isDate,$startDate,$endDate);
    	
    	$data= array();
    	foreach ($dataAll as $key => $value) {

    		$data[$key]['Recruiter Type'] =$value['recruiterName'];
    		$data[$key]['Company Name']=$value['company_name'];
            $data[$key]['Name']=$value['userName'];
    		$data[$key]['Phone']=$value['mobile_number'];
    		$data[$key]['Email']=$value['email_address'];
    		$data[$key]['County']=$value['countryname'];
    		$data[$key]['State']=$value['statename'];
    		$data[$key]['City']=$value['cityname'];
    		$data[$key]['Street']=$value['street'];
    		$data[$key]['Postal Code']=$value['postal_code'];
            $data[$key]['Company Description'] = "";
    	}
    	
    	//echo'<pre>';print_r($data);exit;
        if($data){
        	$filename = $this->writeCsv($data,true);

        	$file = storage_path().'/app/'.$filename;
        	$current_date = \Carbon\Carbon::now()->format('d_m_Y');
            return Response::download($file, "employer_".$current_date.".csv", ['content-type' => 'text/csv']); 
        }else{
            return redirect()->back()->with([
                'error_message' => "No Record founds!"
            ]);
        } 
    }

    public function getSample(Request $request){
        
        $file = storage_path().'/app/import-sample/employer_sample.csv';
        return Response::download($file, 'employer_sample.csv', ['content-type' => 'text/csv']);        
    }
}
