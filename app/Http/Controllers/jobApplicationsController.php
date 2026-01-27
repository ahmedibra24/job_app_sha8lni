<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class jobApplicationsController extends Controller
{
    public function index (){
        $applications = JobApplication::where('applicant_id',Auth::user()->id)->latest()->paginate(7) ;
        return view('applications.index',compact('applications'));
    }
}
