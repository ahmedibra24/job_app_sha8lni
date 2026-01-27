<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class applyJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'resume_option'=> 'required|string',
            'resume_file'=> 'required_if:resume_option,new_resume|file|mimes:pdf|max:5120'
            //? resume_file will be required only if resume_option = new_resume
        ];
    }
    public function messages()
    {
        return [
            'resume_option.required'=>'p;ease select resume option',
            'resume_file.required'=>'the resume file is required',
            'resume_file.file'=>'the resume file must be a file',
        ];
    }
}
