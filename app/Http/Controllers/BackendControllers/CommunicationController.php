<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function getListing(Request $request)
    {
    	$users =User::get();
    	return view('backend.communication.listing', [
    		"Users" => $users,
       ]);
    }
}
