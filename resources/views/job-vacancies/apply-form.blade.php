<x-app-layout>
    {{--! ========================================= HEADER =====================================  --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                {{--! title --}}
                <h1 class="text-3xl md:text-4xl font-semibold text-white">{{ $jobVacancy->title }} - Apply</h1>
                {{--! job details --}}
                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-300">
                    <span>{{ optional($jobVacancy->company)->name }}</span>
                    <span class="text-gray-500">•</span>
                    <span>{{ $jobVacancy->location }}</span>
                    @if($jobVacancy->salary)
                        <span class="text-gray-500">•</span>
                        <span>${{ number_format($jobVacancy->salary, 0) }}</span>
                    @endif
                    @if($jobVacancy->type)
                        <span class="ml-2 inline-block rounded-lg bg-indigo-600/90 px-3 py-1 text-xs font-medium text-white">{{ $jobVacancy->type }}</span>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>
    {{--! back button  --}}
    <div class="md:p-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('job-vacancies.show',$jobVacancy->id) }}" class="text-sm text-indigo-300 hover:text-indigo-200">&larr; Back to Job details</a>
    </div>
    {{--! ========================================= MAIN CONTENT =====================================  --}}
        <div class="bg-black/90 border border-white/10 rounded-2xl p-6 md:p-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form action="{{ route('job-vacancies.application-processing',$jobVacancy) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    {{--! resume section --}}
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-4"> Choose Your Resume</h3>
                        <div class="mb-6">
                            <x-input-label for="resume" value="select from your existing resumes:" />
                            {{--! radio options --}}
                            <div class="space-y-4 mt-2">
                                @forelse($resumes as $resume)
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="resume_option" id="{{ $resume->id }}" value="{{ $resume->id }}"
                                                @error('resume_option') class="border-red-500" @else class="border-gray-600" @enderror >
                                        <x-input-label for="{{ $resume->id }}" class="text-white cursor-pointer">
                                            {{ $resume->fileName }}
                                            <span class="text-gray-400 text-sm"> ( Last Updated {{ $resume->updated_at->format('M d,Y') }} )</span>
                                        </x-input-label>
                                    </div>
                                @empty
                                <span class="text-gray-400 text-sm">No Resumes Found</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{--! upload new resume section --}}
                    {{--? define the file name and error variables --}}
                    <div x-data="{fileName:'',hasError:{{ $errors->has('resume_file')?'true':'false' }} }">
                        {{--! radio option --}}
                        <div class="flex items-center gap-2 mb-2">
                            {{--? x-ref => reference to element in dom (alpine)  --}}
                            <input x-ref="newResumeRadio" type="radio" name="resume_option" id="new_resume" value="new_resume"
                                    @error('resume_option') class="border-red-500" @else class="border-gray-600" @enderror >
                            <x-input-label class="text-white cursor-pointer" for="new_resume" value="upload a new resume:" />               
                        </div>
                        {{--! upload  --}}
                        <div class="flex items-center">
                            <div class="flex-1">
                                <label for="new_resume_file" class="block text-white cursor-pointer ">                                    
                                    {{--! border style --}}
                                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 hover:border-blue-500 transition"
                                         :class="{'border-blue-500':fileName, 'border-red-500':hasError}">
                                         {{--! input--}}
                                         <input @change="fileName=$event.target.files[0].name; $refs.newResumeRadio.checked = true "  type="file"
                                          name="resume_file" id="new_resume_file" class="hidden" accept=".pdf">
                                          {{--! inside text--}}
                                          <div class="text-center">

                                            {{--? template -> to show one of them --}}
                                            {{--? if file not uploaded--}}
                                            <template x-if="!fileName">
                                                <p class="text-gray-400">Click to upload PDF (Max 5MB)</p>
                                            </template>
                                            
                                            {{--? if file uploaded--}}
                                            <template x-if="fileName">
                                                <div>
                                                    <p x-text="fileName" class="mt-2 text-blue-400 "></p>
                                                    <p class="text-gray-400 text-sm mt-1 ">Click to change file</p>
                                                </div>
                                            </template>
                                          </div>
                                    </div>
                                </label>
                                 <x-input-error :messages="$errors->get('resume_file')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{--! submit button --}}
                    <div>
                        <x-primary-button class="w-full">
                            Apply Now
                        </x-primary-button>
                    </div>
                    
                </form>
        </div>
</x-app-layout>
