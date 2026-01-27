<x-app-layout>
    {{--! ========================================= HEADER =====================================  --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                {{--! title --}}
                <h1 class="text-3xl md:text-4xl font-semibold text-white">{{ $jobVacancy->title }}</h1>
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
             {{--! Apply button  --}}
            <a href="{{ route('job-vacancies.apply',$jobVacancy->id) }}" class="inline-flex items-center rounded-lg bg-gradient-to-r from-indigo-500 to-pink-500 px-4 py-2 text-white text-sm font-semibold shadow hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                Apply Now
            </a>
        </div>
    </x-slot>
    {{--! back button  --}}
    <div class="md:p-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="text-sm text-indigo-300 hover:text-indigo-200">&larr; Back to Jobs</a>
    </div>
    {{--! ========================================= MAIN CONTENT =====================================  --}}
    <div>
        <div class="bg-black/90 border border-white/10 rounded-2xl p-6 md:p-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{--! Left: Description --}}
                <div class="lg:col-span-2">
                    <h3 class="text-xl font-semibold text-white mb-2">Job Description</h3>
                    <hr class="border-gray-700/60 mb-6">
                    <div class="prose prose-invert max-w-none text-gray-300">
                        {!! nl2br(e($jobVacancy->description)) !!}
                    </div>
                </div>

                {{--! Right: Overview Card --}}
                <aside>
                    <div class="rounded-2xl bg-slate-800/70 border border-slate-700/60 shadow-xl p-6">
                        <h4 class="text-lg font-semibold text-white mb-4">Job Overview</h4>
                        <dl class="divide-y divide-slate-700/60">
                            <div class="py-3 grid grid-cols-3 gap-2">
                                <dt class="col-span-1 text-sm text-gray-400">Published Date</dt>
                                <dd class="col-span-2 text-sm text-gray-200">{{ optional($jobVacancy->created_at)->format('M d, Y') }}</dd>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-2">
                                <dt class="col-span-1 text-sm text-gray-400">Company</dt>
                                <dd class="col-span-2 text-sm text-gray-200">{{ optional($jobVacancy->company)->name }}</dd>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-2">
                                <dt class="col-span-1 text-sm text-gray-400">Location</dt>
                                <dd class="col-span-2 text-sm text-gray-200">{{ $jobVacancy->location }}</dd>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-2">
                                <dt class="col-span-1 text-sm text-gray-400">Salary</dt>
                                <dd class="col-span-2 text-sm text-gray-200">@if($jobVacancy->salary) ${{ number_format($jobVacancy->salary,0) }} / Year @else Negotiable @endif</dd>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-2">
                                <dt class="col-span-1 text-sm text-gray-400">Type</dt>
                                <dd class="col-span-2 text-sm text-gray-200">{{ $jobVacancy->type }}</dd>
                            </div>
                            <div class="py-3 grid grid-cols-3 gap-2">
                                <dt class="col-span-1 text-sm text-gray-400">Category</dt>
                                <dd class="col-span-2 text-sm text-gray-200">{{ optional($jobVacancy->jobCategory)->name }}</dd>
                            </div>
                        </dl>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
