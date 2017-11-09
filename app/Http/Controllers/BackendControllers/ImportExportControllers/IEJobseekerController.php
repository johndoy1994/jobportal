<?php

namespace App\Http\Controllers\BackendControllers\ImportExportControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use App\Repos\AdminRepo;
use App\Repos\ImportRepo;
use App\Repos\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IEJobseekerController extends BackendController
{
    public function getImport(Request $request) {
        return view('backend.import-export.jobseeker.import');
    }

    public function postImport(Request $request, UserRepo $repo) {
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

    		if(isset($row[0],$row[1],$row[2],$row[4],$row[5],$row[6],$row[9],$row[11],$row[12],$row[13],$row[14],$row[15],$row[17],$row[18],$row[19],$row[20],$row[21]) && !empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[4]) && !empty($row[5]) && !empty($row[6]) && !empty($row[9]) && !empty($row[11]) && !empty($row[12]) && !empty($row[13]) && !empty($row[14]) && !empty($row[15]) && !empty($row[17]) && !empty($row[18]) && !empty($row[19]) && !empty($row[20]) && !empty($row[21])){
                	list($status[0],$message[0],$title)=[true,"succsess",null];
                    list($status[1],$message[1],$name)=ImportRepo::checkName($row[1]);
                    list($status[2],$message[2],$employee)=ImportRepo::checkEmail($row[2]);
                    list($status[3],$message[3],$Vacancies)=ImportRepo::checkMobile($row[3]);
                    list($status[4],$message[4],$country)=ImportRepo::getCountry($row[4]);
                    list($status[5],$message[5],$state)=ImportRepo::getState($row[5],$country);
                    list($status[6],$message[6],$city)=ImportRepo::getCity($row[6],$state);
                    list($status[7],$message[7],$education)=ImportRepo::getEducation($row[9]);
                    list($status[8],$message[8],$salaryType)=ImportRepo::getSalaryType($row[12]);
                    list($status[9],$message[9],$salaryRange)=ImportRepo::getsalaryRange($salaryType,$row[13]);
                    list($status[10],$message[10],$jobCategory)=ImportRepo::getJobCategory($row[14]);
                    list($status[11],$message[11],$Jobtitle)=ImportRepo::getJobTitle($jobCategory,$row[15]);
                    list($status[12],$message[12],$DesiredsalaryType)=ImportRepo::getSalaryType($row[17]);
                    list($status[13],$message[13],$DesiredRange)=ImportRepo::getsalaryRange($DesiredsalaryType,$row[18]);
                    list($status[14],$message[14],$jobType)=ImportRepo::checkJobType($row[19]);
                    list($status[15],$message[15],$experience)=ImportRepo::getExperience($row[20]);
                    list($status[16],$message[16],$experiencelevel)=ImportRepo::getExperienceLevel($row[21]);
                    list($status[17],$message[17],$privercy)=ImportRepo::checkPrivacy($row[23]);

                    if($status[1] && $status[2] && $status[3] && $status[4] && $status[5] && $status[6] && $status[7] && $status[8] && $status[9] && $status[10] && $status[11] && $status[12] && $status[13] && $status[14] && $status[15] && $status[16] && $status[17]){

                    	    $user = $repo->create([
					    		'name' => $row[1],
					    		'email_address' => $row[2],
					    		'mobile_number' => $row[3],
					    		'password' => bcrypt('sagar'),
					    		'type'	=> User::TYPE_SEEKER,
					    		'level' => User::LEVEL_FRONTEND_USER,
					    		'status' => User::STATUS_ACTIVATED
							]);	

						    if($user){
                  				$address_saved = UserRepo::updateUserAddress($user, "residance", array(
						            'city_id'       => $city->id,
						            'street'        => $row[7],
						            'postal_code'   => $row[8]
					        	));
					        
						        $experience_saved = UserRepo::updateUserExperience($user, array(
						            "education_id"              => $education->id,
						            "current_salary_range_id"   => $salaryRange->id,
						            "experinece_id"             => $experience->id,
						            "experinece_level_id"       => $experiencelevel->id,
						            "desired_job_title_id"      => $Jobtitle->id,
						            "desired_salary_range_id"   => $DesiredRange->id,
						            "recent_job_title"          => $row[11]
						        ));

						        // update user job types
						        $user_jobtypes = UserRepo::updateUserJobTypes($user, $jobType);

						        // user profile details
						        $profile_saved = UserRepo::updateUserProfile($user, array(
						            "person_title_id"   => self::convertNameTitle($row[0]),
						            "about_me"          => $row[22],
						            "profile_privacy"   => $row[23]
						        ));

						        $certificate_line = ($row[23]) ? $row[23] : "";
	        					$certificates = explode(",", $certificate_line);
	        					// save certificates
						        $certificates_saved = UserRepo::updateUserCertificates($user, $certificates);

						        $skill_line = ($row[16]) ? $row[16] : "";
	        					$skills = explode(",", $skill_line);
						        // save skills
						        $skills_saved = UserRepo::updateSkills($user, $Jobtitle->id, $skills);
						        $successCount++;
  	    					}else{
  	    						$blackfield="Please fill all required field";
                                $msgs[$count][] = $blackfield;
                                $skipedRows[$count][] = $row;
  	    					}
							
					        // save desired locations
					        //$desired_locations_saved = UserRepo::updateDesiredLocations($user, $desired_locations);

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
    	return view('backend.import-export.jobseeker.export');
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
        $q =User::where('name', 'like', "%%");
            $q->where('type','=','JOB_SEEKER');
            if($isDate){
                $q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
                $q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
            }
        $getAllJObseeker=$q->get();
        
        $data=array();
        foreach ($getAllJObseeker as $key => $value) {
	            $user_profile = $value->profile()->first();
	            $user_address = $value->addresses()->where('type','residance')->first();
	            $user_experience = $value->experiences()->first();
	            $user_certificates = $value->certificates;
	            $user_skills = $value->skills;
	            $user_jobtypes = $value->job_types;
	            //$user_desired_locations = $value->addresses()->where('type','desired')->get();

	        
                $certificate="";
                foreach ($user_certificates as $k1 => $v1) {
                    $certificate .= $v1['certificate'].",";
                }
                $certificateData=trim($certificate,",");

                $skill="";
                foreach ($user_skills as $key => $val) {
                	$skill .= ($val->tag) ? $val->tag->name."," : "";
                }
                $skillData=trim($skill,","); 

                if($user_experience){
                	$current_salary_range=$user_experience->current_salary_range()->first();   
	                if($current_salary_range){
	                	$current_salary_type=$current_salary_range->salaryType->salary_type_name;
	                	$current_salary_range=$current_salary_range->range_from."-".$current_salary_range->range_to;
	                }

	                $desired_salary_range=$user_experience->current_salary_range()->first();   
	                if($desired_salary_range){
	                	$desires_salary_type=$desired_salary_range->salaryType->salary_type_name;
	                	$desired_salary_range=$desired_salary_range->range_from."-".$desired_salary_range->range_to;
	                }	
                }else{
            		$current_salary_type="";
                	$current_salary_range="";
                	$desires_salary_type="";
                	$desired_salary_range="";
                }
                
               
                $jobType="";
                foreach ($user_jobtypes as $k => $v) {

                    $jobType .= ($v->jobType)? $v->jobType->name."," : "";
                }
                $jobTypeData=trim($jobType,",");

            $data[$key]['Title*'] =($user_profile && $user_profile->person_title_id)?self::convertNameTitle($user_profile->person_title_id):"";
            $data[$key]['Name*']=$value->name;
            $data[$key]['Email*']=$value->email_address;
            $data[$key]['Mobile Number']=$value->mobile_number;
            $data[$key]['Country*']=($user_address && $user_address->city &&  $user_address->city->State && $user_address->city->State->Country) ? $user_address->city->State->Country->name : "";
            $data[$key]['Province/State*']=($user_address && $user_address->city &&  $user_address->city->State && $user_address->city->State) ? $user_address->city->State->name : "";
            $data[$key]['City*']=($user_address && $user_address->city) ? $user_address->city->name : "";
            $data[$key]['Street']=($user_address) ? $user_address->street : "";
            $data[$key]['Postal Code']=($user_address) ? $user_address->postal_code : "";
            $data[$key]['education*']=($user_experience && $user_experience->education) ? $user_experience->education->name : "";
            $data[$key]['Certificates*']=$certificateData;
            $data[$key]['title*'] =$value->recent_job_title;
            $data[$key]['salary type*']=$current_salary_type;
            $data[$key]['salary range*']=$current_salary_range;
            $data[$key]['category*']=($user_experience && $user_experience->desired_job_title && $user_experience->desired_job_title->category) ? $user_experience->desired_job_title->category->name : "";
            $data[$key]['Desired job title*']=($user_experience && $user_experience->desired_job_title) ? $user_experience->desired_job_title->title : "";
            $data[$key]['Skills']=$skillData;
            $data[$key]['Desired Salary*']=$desires_salary_type;
            $data[$key]['Desired range*']=$desired_salary_range;
            $data[$key]['Job Type*']=$jobTypeData;
            $data[$key]['Experience*']=($user_experience && $user_experience->experience) ? $user_experience->experience->exp_name : "";
            $data[$key]['Level*']=($user_experience && $user_experience->experience_level) ? $user_experience->experience_level->level : "";
            $data[$key]['About Me'] =($user_profile) ?  $user_profile->about_me : "";
            $data[$key]['Profile privacy options(1 = I want my profile and CV to be visible to potential employers (Recommended), 2 = I want my profile to be visible to potential employers, but keep my personal information and CV hidden., 3 = Please do not make my profile searchable)']=($user_profile) ?  $user_profile->profile_privacy : "";;
            
            
        }
    	if($data){
            $filename = $this->writeCsv($data,true);

            $file = storage_path().'/app/'.$filename;
            $current_date = \Carbon\Carbon::now()->format('d_m_Y');
            return Response::download($file, "jobseeker_".$current_date.".csv", ['content-type' => 'text/csv']); 
        }else{
            return redirect()->back()->with([
                'error_message' => "No Record founds!"
            ]);
        } 
    }

    public function getSample(Request $request){
        
        $file = storage_path().'/app/import-sample/jobseeker_sample.csv';
        return Response::download($file, 'jobseeker_sample.csv', ['content-type' => 'text/csv']);        
    }
}
