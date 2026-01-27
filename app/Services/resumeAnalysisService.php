<?php


namespace App\Services;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;
use Gemini\Data\Content;

class resumeAnalysisService{
      //!===================================================== EXTRACT RESUME INFORMATION ============================================*/
 
    public function extractResumeInfo(string $fileUrl){
        try{
            //! extract text from resume
            $text = $this->extractTextFromPdf($fileUrl);
            //? test in log
            //? .env -> LOG_CHANNEL=errorLog to show logs in terminal with laravel.log
            Log::debug('successfully extract text from pdf '.strlen($text).' character');


            //! clean text using gemini & get response
            $instructions=Content::parse('you are a precise resume partner.
                                    Extract information exactly as it appears in the resume without adding any interpretation or additional information.
                                    the output should be in json format '
                                    ) ; 

            $prompt = 'parse the following resume content and extract the information as a json object with the exact keys: "summary","skills","experience","education".
                        the resume content is:'. $text. '.
                        Return an empty string for key that if not found ';
            $parseResponse = $this->getGeminiJsonResponse($prompt,$instructions) ;
            Log::debug('gemini response is: ', $parseResponse);

            //! validate the response
            if (!is_array($parseResponse)) {
                throw new \Exception('Gemini returned an invalid response format');
            }
            $requiredKeys = ["summary","skills","experience","education"];
            $missingKeys = array_diff($requiredKeys,array_keys($parseResponse)) ; //? return the values in array that are not present in any of the other arrays
            if(count($missingKeys)>0){
                Log::error('Missing required keys: '.implode(',',$missingKeys));
                throw new \Exception('Missing required keys in the parsed result');
            }

            return [
                'summary'=>$parseResponse['summary']??'',
                'skills'=>$parseResponse['skills']??'',
                'experience'=>$parseResponse['experience']??'',
                'education'=>$parseResponse['education']??'',
                ];
                
        }catch(\Exception $e){
            Log::error('Error extracting resume information: ', $e->getMessage());
            return [
                'summary'=>'',
                'skills'=>'',
                'experience'=>'',
                'education'=>'',
                ];

        }

    }
    //!===================================================== ANALYZE RESUME ============================================*/
    public function analyzeResume($jobVacancy,$resumeData){
        try{
            $jobDetails=json_encode([
                'title'=>$jobVacancy->title,
                'description'=>$jobVacancy->description,
                'location'=>$jobVacancy->location,
                'salary'=>$jobVacancy->salary,
                'type'=>$jobVacancy->type,
            ]);
            $resumeDetails= json_encode($resumeData) ;

            //! using gemini to evaluate resume
            $instructions=Content::parse('you are an expert HR professional and job recruiter.
                                        you are given a job vacancy and resume. 
                                        your task is to analyze the resume and determine if the candidate is a good fit for the job.
                                        the output should be in json format.
                                        provide a score from 0 to 100 for candidate suitability for the job, and detailed feedback.
                                        Response should only be json format that has the following keys: "aiGeneratedScore","aiGeneratedFeedback".
                                        aiGeneratedFeedback should be detailed and specific to the job and the candidate resume') ; 

            $prompt = "Please evaluate this job application. Job Details: {$jobDetails} Resume Details: {$resumeDetails}" ;
            $parseResponse = $this->getGeminiJsonResponse($prompt,$instructions) ;
            Log::debug('gemini response is: ', $parseResponse);

            //! validate the response
            if (!is_array($parseResponse)) {
                throw new \Exception('Gemini returned an invalid response format for resume evaluation');
            }
            $requiredKeys = ["aiGeneratedScore","aiGeneratedFeedback"];
            $missingKeys = array_diff($requiredKeys,array_keys($parseResponse)) ; 
            if(count($missingKeys)>0){
                Log::error('Missing required keys: '.implode(',',$missingKeys));
                throw new \Exception('Missing required keys in the parsed result for resume evaluation');
            }
            return [
                'aiGeneratedScore'=>$parseResponse['aiGeneratedScore']??'',
                'aiGeneratedFeedback'=>$parseResponse['aiGeneratedFeedback']??'',
                ];
                
        }catch(\Exception $e){
            Log::error('Error resume evaluation: ', $e->getMessage());
            return [
                'aiGeneratedScore'=>0,
                'aiGeneratedFeedback'=>'An error occurred while analyzing the resume please try again later',
                ];

        }

    }
    

    //!===================================================== EXTRACT TEXT FROM PDF ============================================*/
    private function extractTextFromPdf(string $fileUrl): string {
        //!-----------------------reading the file from cloud to local disk storage in temp file ----------------------------*/

       //! get file data from url (path => name => storage path)
        $filePath = parse_url($fileUrl,PHP_URL_PATH) ;
        if(!$filePath){
            throw new \Exception('invalid file url');
        }
        $fileName = basename($filePath);
        //? use the path because we dont need to depend on the base url because it change depend on cloud
        $storagePath= "resumes/{$fileName}";
        //? check if file exist on storage
        if(!Storage::disk('cloud')->exists($storagePath)){
            throw new \Exception('File Not Found');
            }

        //! open file from storage for read (using storage path)
        $pdfContent = Storage::disk('cloud')->get($storagePath) ;  
        if(!$pdfContent){
            throw new \Exception('Failed to read file');
        }  

        //! file direction on sever
        //? tempnam() -> دي دالة بتعمل ملف جديد بأسم غير قابل للتكرار فاضي فى فايل معين
        //? sys_get_temp_dir() -> دي دالة  بترجع مسار فولدر الملفات المؤقتة في النظام. - المكان الآمن اللي السيستم بيستخدمه للحاجات المؤقتة
        //? 'resume' (prefix) -> ده بداية اسم الملف - /tmp/resumeA8f93k
        $tempFile = tempnam(sys_get_temp_dir(),'resume') ;

        //! put file content in temp file on server
        file_put_contents($tempFile,$pdfContent);


        //! check if pdf-to-text is installed on server
        //? pdfToTextPaths -> paths of temp file on diffrent operatting system
        $pdfToTextPaths = ['/opt/homebrew/bin/pdftotext','/usr/bin/pdftotext','/usr/local/bin/pdftotext'];
        $pdfToTextAvailable= false;

        foreach($pdfToTextPaths as $paths){
            if(file_exists($paths)){
                $pdfToTextAvailable = true ;
                break;
            }
        }

        if(!$pdfToTextAvailable){
           throw new \Exception('pdf-to-text is not installed'); 
        }

        //!----------------------------------------- Extract text from the pdf file -----------------------------------------*/

        $text=Pdf::getText($tempFile);
        //! clean up the temp file
        unlink($tempFile);

        return $text;
    }

    //!===================================================== GET GEMINI JSON RESPONSE ============================================*/
    private function getGeminiJsonResponse(string $prompt,$instructions){

        $config = new GenerationConfig(responseMimeType: ResponseMimeType::APPLICATION_JSON , temperature: 0.1);

        try {
            $result = Gemini::generativeModel(model: 'models/gemini-2.5-flash')
                ->withSystemInstruction($instructions)
                ->withGenerationConfig($config)
                ->generateContent($prompt);

            $jsonResponse = json_decode($result->text(), true);
            return $jsonResponse ;

        } catch (\Exception $e) {
            Log::error('Error gemini json response: ', $e->getMessage());

        }
    }

}


