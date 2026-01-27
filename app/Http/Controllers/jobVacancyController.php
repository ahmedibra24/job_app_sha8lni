<?php

namespace App\Http\Controllers;

use App\Http\Requests\applyJobRequest;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Models\JobVacancy ;
use App\Models\Resume;
use App\Services\resumeAnalysisService;
use Gemini\Laravel\Facades\Gemini;
use Exception;
use Illuminate\Support\Facades\Auth;

class jobVacancyController extends Controller
{
   protected $resumeAnalysisService;

   public function __construct(resumeAnalysisService $resumeAnalysisService)
   {
      $this->resumeAnalysisService = $resumeAnalysisService ;
   }
   public function show(string $id){
    $jobVacancy = JobVacancy::findOrFail($id);
    return view('job-vacancies.show',compact('jobVacancy'));
    
   } 

 // create
   public function applyForm(string $id){
      $jobVacancy = JobVacancy::findOrFail($id);
      $resumes= Auth::user()->resumes;
      return view('job-vacancies.apply-form',compact('jobVacancy','resumes'));
   }


   // store
   public function applicationProcessing (applyJobRequest $request, string $id){

      $extractedInfo=null;
      $resumeId=null;
      $jobVacancy = JobVacancy::findOrFail($id) ;

      //! new resume uploaded
      if($request->input('resume_option')=='new_resume'){
         //! extract file data
         $file=$request->file('resume_file');//? file sent in request from form
         $extension=$file->getClientOriginalExtension();//? pdf
         $originalFileName= $file->getClientOriginalName();//? use to store in database
         $fileName= 'resume_' . time() . '.' . $extension;//? name that store in cloud should be unique to avoid override files
   
         //! store in laravel cloud
         //? resumes-> the name of folder on cloud
         //? cloud-> place you will store the file - its cloud by default when you edit it in .env file FILESYSTEM_DISK=cloud 
         $path=$file->storeAs('resumes',$fileName,'cloud');
         $fileUrl = config('filesystems.disks.cloud.url').'/'.$path ;//? final url to display the file from cloud

         //! extracted info by ai
         $extractedInfo = $this->resumeAnalysisService->extractResumeInfo($fileUrl);
            
         //! store or create the file record on database
         $resume = Resume::create([
           'fileName'=>$originalFileName,
           'fileUri'=> $path,//? should not contain the domain
           'applicant_id'=>Auth::id(),
           'contactDetails'=>json_encode([
            'name'=>Auth::user()->name,
            'email'=>Auth::user()->email,
           ]),
           'summary'=>$extractedInfo['summary'],
           'skills'=>$extractedInfo['skills'],
           'experience'=>$extractedInfo['experience'],
           'education'=>$extractedInfo['education'],
         ]);

         $resumeId = $resume->id;
   
      } //! existing resume
      else{

         $resumeId = $request->input('resume_option');
         $resume = Resume::findOrFail($resumeId) ;
          $extractedInfo=[
           'summary'=>$resume->summary,
           'skills'=>$resume->skills,
           'experience'=>$resume->experience,
           'education'=>$resume->education,
         ];
      }

      //! analyze resume by ai
      $evaluation = $this->resumeAnalysisService->analyzeResume($jobVacancy,$extractedInfo);


      //! create job application
      JobApplication::create([
        'status'=>'pending',
        'job_vacancy_id'=>$id,
        'resume_id'=>$resumeId,
        'applicant_id'=>Auth::id(),
        'aiGeneratedScore'=>$evaluation['aiGeneratedScore'],
        'aiGeneratedFeedback'=>$evaluation['aiGeneratedFeedback'],

      ]);



      return redirect()->route('job-applications.index',$id)->with('success','Application submitted successfully');
   }

   // test gemeni
   public function ask(){
       try {
            // to get all available models
            // $response = Gemini::models()->list();
            // foreach ($response->models as $model) {
            // dump("Name: " . $model->name . " | Display: " . $model->displayName);
            // }

            $result = Gemini::generativeModel(model: 'models/gemini-2.5-flash')->generateContent('hello');

            echo $result->text(); // Hello! How can I assist you today?            
        } catch (Exception $e) {
           // Handle the error appropriately
            echo 'Error: ' . $e->getMessage();
       }
   }
  
   
}
