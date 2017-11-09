<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use App\Repos\API\PublicRepo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

        // $user=MyAuth::user();
        // $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user);
        // View::share("messageCount", $getNewMessagesCount);
        $footerLinks = CmsPage::select("id","page_title")->where('status','1')->get();
        
        View::share("cmsFooterLinks", $footerLinks);

        View::share('isUserLive', MyAuth::check());

        View::share('liveUser', MyAuth::check() ? MyAuth::user() : false);

        View::share('cvSearchCount', function($current, $updated=null, $route='recruiter-search-cv') {
            if($updated) {
                foreach ($updated as $key => $value) {
                    $current[$key] = $value;
                }
            }
            return PublicRepo::countSearchCvs($current);
        });

        View::share('cvSearchUri', function($current, $updated=null, $route='recruiter-search-cv') {
            if($updated) {
                foreach ($updated as $key => $value) {
                    $current[$key] = $value;
                }
            }
            return route($route, $current);
        });

    	View::share('jobSearchUri', function($current, $updated=null, $route='job-search') {
            if($updated) {
                foreach ($updated as $key => $value) {
                    $current[$key] = $value;
                }
            }
            return route($route, $current);
        });

        View::share('jobSearchCount', function($current, $updated=null) {
            if($updated) {
                foreach ($updated as $key => $value) {
                    $current[$key] = $value;
                }
            }
            return PublicRepo::countSearchJobs($current);
        });

        View::share('convertDayNumber', function($day, $reverse=false) {
            return Controller::convertDayTitle($day, $reverse);
        });

        View::share('ieModules', [
            'country' => "Countries",
            'state' => "States",
            'city' => "Cities",
            'jobcategory' => "Job Category",
            'jobtitle' => "Job Title",
            'salary-type' => "Salary Type",
            'experience' => "Experience",
            'experience-level' => "Experience Level",
            'job-type' => "Job Type",
            'industry' => "Industry",
            'education' => "Education",
            'degree' => "Degree",
            'tag' => "Tag",
            'salary-range' => "Salary Range",
            'recruitertype' => "Recruiter Type",
            'employer' => "Employer",
            'job' => "Job",
            'jobseeker' => "Jobseeker",
        ]);

        View::share('recordsPerPage', [
            '0' => "0",
            '1' => "10",
            '2' => "20",
            '3' => "50",
            '4' => "100",
            '5' => "200",
            '6' => "500",
            '7' => "1000"
        ]);

        View::share('isJobSaved', function($job_id) {
            return PublicRepo::isJobSaved($job_id);
        });
    }

    public static function convertDayTitle($day,$returnFullName=false) {
        $day = strtolower($day);

        $shortName = [
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
            7 => 'Sun'
        ];

        $fullName = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ];

        $name = $shortName;

        if($returnFullName) {
            $name = $fullName;
        }
        
        switch ($day) {
            case 1: return $name[1];
            case 2: return $name[2];
            case 3: return $name[3];
            case 4: return $name[4];
            case 5: return $name[5];
            case 6: return $name[6];
            case 7: return $name[7];

            case "mon": return 1;
            case "tue": return 2;
            case "wed": return 3;
            case "thu": return 4;
            case "fri": return 5;
            case "sat": return 6;
            case "sun": return 7;

            case "monday": return 1;
            case "tuesday": return 2;
            case "wednesday": return 3;
            case "thursday": return 4;
            case "friday": return 5;
            case "saturday": return 6;
            case "sunday": return 7;
            
            default:
                return "N-A";
                break;
        }
    }

     public static function convertNameTitle($title) {

        $title = strtolower($title);

        switch ($title) {
            case "mr": return 1;
            case "ms": return 2;
            case "mrs": return 3;
            case "dr": return 4;
            case "ph.d": return 5;
            case "phd": return 5;
            
            case 1: return "Mr";
            case 2: return "Ms";
            case 3: return "Mrs";
            case 4: return "Dr";
            case 5: return "Ph.d";
            

            default:
                return "N-A";
                break;
        }
    }

    public function readCsv($filepath) {
        $rows = [];
        $first=true;
        if (($handle = fopen($filepath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if($first){
                    $first=FALSE;
                    continue;
                }
                $num = count($data);
                $row = [];
                for ($c=0; $c < $num; $c++) {
                    $row[$c] = $data[$c];
                }
                $rows[] = $row;
            }
            fclose($handle);
        }
        return $rows;
    }

    public function writeCsv($data, $bypass=false) {
        $uniqid = uniqid();
        $list = $data;
        if(!$bypass) {
            $list = $data->toArray();
        }
        $cols = [];
        foreach($list as $rows) {
            $cols = array_keys($rows);
            break;
        }

        $file = storage_path().'/app/export-'.$uniqid.'.csv';
        $fp = fopen($file, 'w');
        $first=true;
        foreach ($list as $fields) {
            if($first) {
                $first=false;
                fputcsv($fp, $cols);
            }
            fputcsv($fp, $fields);
        }

        fclose($fp);

        return "export-".$uniqid.".csv";
    }

    public static function recordsPerPage($page, $new=null) {
        if(session()->has($page)) {
            if(isset($new)) {
                session()->put($page, $new);
            }
        } else {
            if(isset($new)) {
                session()->put($page, $new);
            } else {
                session()->put($page, env('DEFAULT_ROWSIZE_PERPAGE'));
            }
        }
        return session()->get($page) == 0 ? PHP_INT_MAX : session()->get($page);
    }
}
