<?php

namespace App\Http\Controllers\BackendControllers\ImportExportControllers;

use App\Helpers\Notifier;
use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Repos\ImportRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class IEJobController extends BackendController
{
    public function getImport(Request $request) {
        return view('backend.import-export.job.import');
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
        $msgs           = [];
        $status         = [];
        $message        = [];
        $count          =0;
    	foreach($rows as $row) {
    		if(isset($row[0],$row[1],$row[2],$row[3],$row[4],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[16],$row[17],$row[19],$row[20],$row[21],$row[22],$row[24],$row[25]) && !empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[4]) && !empty($row[6]) && !empty($row[7]) && !empty($row[8]) && !empty($row[9]) && !empty($row[10]) && !empty($row[11]) && !empty($row[12]) && !empty($row[16]) && !empty($row[17]) && !empty($row[19]) && !empty($row[20]) && !empty($row[21]) && !empty($row[22]) && !empty($row[24]) && !empty($row[25])){
                
                    list($status[0],$message[0],$jobCategory)=ImportRepo::getJobCategory($row[0]);
                    list($status[1],$message[1],$Jobtitle)=ImportRepo::getJobTitle($jobCategory,$row[1]);
                    list($status[2],$message[2],$employee)=ImportRepo::getEmployer($row[2]);
                    list($status[3],$message[3],$Vacancies)=ImportRepo::NumberofVacanciesvalidation($row[4]);
                    list($status[4],$message[4],$education)=ImportRepo::getEducation($row[6]);
                    list($status[5],$message[5],$experience)=ImportRepo::getExperience($row[7]);
                    list($status[6],$message[6],$experienceLevel)=ImportRepo::getExperienceLevel($row[8]);
                    list($status[7],$message[7],$jobType)=ImportRepo::getJobType($row[9]);
                    list($status[8],$message[8],$country)=ImportRepo::getCountry($row[10]);
                    list($status[9],$message[9],$state)=ImportRepo::getState($row[11],$country);
                    list($status[10],$message[10],$city)=ImportRepo::getCity($row[12],$state);
                    list($status[11],$message[11],$date)=ImportRepo::CheckStartDateEndDateValidation($row[17],$row[18]);
                    list($status[12],$message[12],$expiredate)=ImportRepo::CheckExpireDateValidation($row[19]);
                    list($status[13],$message[13],$starttime,$endtime)=ImportRepo::CheckStartTimeEndTimeValidation($row[20],$row[21]);
                    list($status[14],$message[14],$salaryType)=ImportRepo::getSalaryType($row[22]);
                    // list($status[15],$message[15],$salaryRange)=ImportRepo::getsalaryRange($salaryType,$row[23]);
                    list($status[15],$message[15],$salary)=ImportRepo::salaryValaidation($row[23]);
                    list($status[16],$message[16],$PayPeriod)=ImportRepo::getPayPeriod($row[25]);
                    list($status[17],$message[17],$PayBy)=ImportRepo::getPayBy($row[24]);

                    $employeeId=($employee) ? $employee->id : "";
                    $cityId=($city) ? $city->id : "";
                    
                    list($status[18],$message[18],$IsUnicJob)=PublicRepo::IsUnicJob($employeeId,$row[3],$cityId,$row[17]);
                    list($status[19],$message[19],$partTimeDays)=ImportRepo::checkPartTimeDays($row[29],$jobType);
                    
                    if($status[0] && $status[1] && $status[2] && $status[3] && $status[4] &&  $status[6] && $status[7] && $status[8] && $status[9] && $status[10] && $status[11] && $status[12] && $status[13] && $status[14] && $status[16] && $status[17] && $status[18] && $status[19]) {
                        
                        list($jobstatus,$jobmessage,$jobrec) = ImportRepo::addJobs($employee->id,$row[3],$row[4],$Jobtitle->id,$education->id,$experience->id,$jobType->id,$experienceLevel->id,$row[17],$row[18],$row[20],$row[21],$salaryType->id,$row[23],$PayBy->id,$PayPeriod->id,$row[26],$row[27],$row[19],$partTimeDays);
                            
                            if($jobstatus){
                                $certificate_line = (isset($row[15])) ? $row[15] : "";
                                $certificates = explode(",", $certificate_line);

                                // save certificates
                                $certificatesData = AdminRepo::addJobCertificates($jobrec, $certificates);

                                $cities = PublicRepo::searchLocations(null, null, $city->id);
                                $searchAddress = $cities[0]->full_address." - ".$request->postal_code;
                                list($pointSuccess, $point, $correctedAddress) = PublicRepo::getGeoLocationPoint($searchAddress);
                                // save Address
                                if($row[14]){
                                    $postal_code=Utility::postalCode_validation($row[14]);
                                }else{
                                    $postal_code=true;   
                                }
                                if($postal_code){
                                    list($stus[0],$mess[0]) = AdminRepo::addJobAddress($jobrec, array(
                                        'city_id'       => $city->id,
                                        'postal_code'   => $row[14],
                                        'street'        => $row[13],
                                        'latitude'      => $point[0],
                                        'longitude'      => $point[1]
                                    ));
                                }else{
                                    list($stus[0],$mess[0])=[false,"Please enter valid postal code"];
                                }

                                // save Skills
                                $skills = (isset($row[5])) ? $row[5] : [];
                                $skillsData = explode(",", $skills);
                                $skillData= AdminRepo::addSkills($jobrec, $skillsData,false);

                                // save Keyword
                                $keyword_line = (isset($row[16])) ? $row[16] : [];
                                $keyword = explode(",", $keyword_line);
                                list($stus[1],$mess[1]) = AdminRepo::addkeyword($jobrec, $keyword);

                                // save weekly
                                $weekly = (isset($row[28])) ? $row[28] : [];
                                $weekly_line = explode(",", $weekly);
                                $weekly_data=[];

                                foreach ($weekly_line as $key => $value) {
                                    $weekly_data[$key]=self::convertDayTitle($value,true);
                                }
                                $keywordData = AdminRepo::addweekly($jobrec, $weekly_data);
                                
                                if($stus[0] && $stus[1]){
                                    Notifier::jobPosted($jobrec,MyAuth::user('admin'));
                                    $successCount++;    
                                }else{
                                    for($i=0;$i<count($stus);$i++) {
                                        if(!$stus[$i]) {
                                            $msgs[$count][] = $mess[$i];
                                        }
                                    }
                                    $jobrec->delete();
                                    $skipedRows[$count][] = $row;    
                                }
                            }else{
                                $blackfield="Please fill all required field";
                                $msgs[$count][] = $blackfield;
                                $skipedRows[$count][] = $row;
                            }    
                    }else{
                        for($i=0;$i<count($status);$i++) {
                            if(!$status[$i]) {
                                $msgs[$count][] = $message[$i];
                            }
                        }
                        $skipedRows[$count][] = $row;
                    }
    		} else {
                $blackfield="Please fill all required field";
                $msgs[$count][] = $blackfield;
                $skipedRows[$count][] = $row;
    		}
            $count++;
    	}
        //print_r($msgs);exit;
    	$failCount = count($rows) - $successCount;
    	return redirect()->back()->with([
				'successCount' => $successCount,
				'failCount' => $failCount,
				'skipedRows' => $skipedRows,
                'msg'          =>$msgs
			]);
    }

    public function getExport() {
    	return view('backend.import-export.job.export');
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
        $getAllJObs = ImportRepo::getAllJobs($isDate,$startDate,$endDate);

        $data=array();
        foreach ($getAllJObs as $key => $value) {
            $JobsSkill = ImportRepo::getJobsSkill($value['id']);
                $skill="";
                foreach ($JobsSkill as $k => $v) {
                    $skill .= $v['tagName'].",";
                }
                $skillData=trim($skill,",");

            $Jobscertificate = ImportRepo::getJobscertificate($value['id']);
                $certificate="";
                foreach ($Jobscertificate as $k1 => $v1) {
                    $certificate .= $v1['certificate'].",";
                }
                $certificateData=trim($certificate,",");

            $JobsKeyword = ImportRepo::getJobsKeyword($value['id']);
                $Keyword="";
                foreach ($JobsKeyword as $k2 => $v2) {
                    $Keyword .= $v2['keyword'].",";
                }
                $KeywordData=trim($Keyword,",");

            $JobsWeekdays = ImportRepo::getJobsWeekdays($value['id']);
                $Weekdays="";
                foreach ($JobsWeekdays as $k3 => $v3) {
                    $Weekdays .= self::convertDayTitle($v3['day']).",";
                }
                $WeekdaysData=trim($Weekdays,",");

            $partTimeDays=  ImportRepo::jMetaDecode($value['meta']);  
                $partTimeDate="";
                if(isset($partTimeDays['days'])){
                    foreach ($partTimeDays['days'] as $k4 => $v4) {
                        $partTimeDate .= $v4.",";
                    }
                }
                $partTimeDateData=trim($partTimeDate,",");

            $data[$key]['JobCategory'] =$value['category_name'];
            $data[$key]['JobTitle']=$value['jobTitle'];
            $data[$key]['Employer Email']=$value['email_address'];
            $data[$key]['Title']=$value['title'];
            $data[$key]['Number of Vacancies']=$value['vacancies'];
            $data[$key]['Job Skills']=$skillData;
            $data[$key]['Education']=$value['educationName'];
            $data[$key]['Experience']=$value['exp_name'];
            $data[$key]['Experience Level']=$value['level'];
            $data[$key]['Job Type']=$value['jobTypeName'];
            $data[$key]['Country'] =$value['countryname'];
            $data[$key]['State']=$value['statename'];
            $data[$key]['City']=$value['cityname'];
            $data[$key]['Street']=$value['street'];
            $data[$key]['Postal code']=$value['postal_code'];
            $data[$key]['Certificates']=$certificateData;
            $data[$key]['Job Keyword']=$KeywordData;
            $data[$key]['StartDate']=\Carbon\Carbon::parse($value['starting_date'])->format('Y-m-d');
            $data[$key]['EndDate']=\Carbon\Carbon::parse($value['ending_date'])->format('Y-m-d');
            $data[$key]['PostExpirationDate']=\Carbon\Carbon::parse($value['expiration_date'])->format('Y-m-d');;
            $data[$key]['WorkScheduleFrom']=$value['work_schedule_from'];
            $data[$key]['WorkScheduleTo'] =$value['work_schedule_to'];
            $data[$key]['SalaryType']=$value['salary_type_name'];
            //$data[$key]['SalaryRange']=$value['range_from']."-".$value['range_to'];
            $data[$key]['Salary']=$value['salary'];
            $data[$key]['PayBy']=$value['PayByName'];
            $data[$key]['PayPeriod']=$value['PayperiodsName'];
            $data[$key]['Benefits']=$value['benefits'];
            $data[$key]['Description']="";//urlencode($value['description']);
            $data[$key]['Weekdays']=$WeekdaysData;
            $data[$key]['PartTimeDays']=$partTimeDateData;
            
        }
    	if($data){
            $filename = $this->writeCsv($data,true);

            $file = storage_path().'/app/'.$filename;
            $current_date = \Carbon\Carbon::now()->format('d_m_Y');
            return Response::download($file, "jobs_".$current_date.".csv", ['content-type' => 'text/csv']); 
        }else{
            return redirect()->back()->with([
                'error_message' => "No Record founds!"
            ]);
        } 
    }

    public function getSample(Request $request){
        
        $file = storage_path().'/app/import-sample/jobs_sample.csv';
        return Response::download($file, 'jobs_sample.csv', ['content-type' => 'text/csv']);        
    }
}
