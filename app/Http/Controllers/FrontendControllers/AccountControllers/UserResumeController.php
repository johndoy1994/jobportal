<?php

namespace App\Http\Controllers\FrontendControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\Models\User;
use App\Models\UserResume;
use App\MyAuth;
use App\Repos\ResumeRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class UserResumeController extends FrontendController
{
    public function __construct() {
    	parent::__construct();
    }

    //User Resumes
    public function getIndex(Request $request, ResumeRepo $repo) {
        $addResume=$repo->find();

        return view('frontend.account.user-resumes.index', [
    		'user'=>Auth::user(),'userResume'=>$addResume
		]);
    }

    public function postIndex(Request $request, ResumeRepo $repo) {

        $this->validate($request, [
            'resume' => 'required|mimes:txt,rtf,doc,pdf,docx,doc,zip'
        ],[
            'resume.mimes' => "Upload failed: Only file type: txt, rtf, doc, docx and pdf allowed, Please try again"
        ]);
        
        $filename=uniqid(rand(10,100)).'.'.$request->file('resume')->getClientOriginalExtension();

    	$user=MyAuth::user();
    	$result=Storage::put(
        	'resumes/'.$filename,file_get_contents($request->file('resume')->getRealPath())
        );
        
        if($result) {
    	    
            list($resumeAddUpdate,$message)=ResumeRepo::createOrUpdateResume($user->id,$filename);
    		  
                if($resumeAddUpdate){
                    return redirect()->Route('account-user-resumes')->with([
                            'success_message' => $message
                    ]);
                }else{
                    return redirect()->Route('account-user-resumes')->with([
                            'error_message' => $message
                    ]);
                }

    		} else {
    			return redirect()->Route('account-user-resumes')->with([
    				'error_message' => "There was an error please try again"
    			]);
		}
    }

    public function getResumeDownload(Request $request,User $id){
        $resume = UserResume::where('user_id', $id->id)->first();
        if($resume){
            $file = storage_path().'/app/resumes/'.$resume->filename;
            return Response::download($file, $id->getName()."_".$resume->filename, ['content-type' => $resume->mime]);        
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'User CV is not available.'
            ]);
        }
    }
}
