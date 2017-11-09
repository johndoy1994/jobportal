<?php

namespace App\Http\Controllers\BackendControllers\ImportExportControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;

class IEController extends BackendController
{

	public function getIndex()
    {
    	return view('backend.import-export.index');
    }
}
