<x-app-layout>
    {{-- HEADER --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl md:text-4xl font-semibold text-white">My Applications</h1>
        </div>
    </x-slot>

    {{--! Success toast --}}
    <x-notification-message />
    {{-- Container --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-black/90 border border-white/10 rounded-2xl p-6 md:p-8">
                <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
                    {{-- Example application card (replace with a @foreach over user applications) --}}
                    @forelse($applications as $application)
                        <div class="rounded-2xl bg-slate-900/70 ring-1 ring-slate-800 shadow-xl">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-start justify-between gap-6">
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-semibold text-slate-100">{{$application->jobVacancy->title}}</h2>
                                    <p class="mt-1 text-slate-300">{{$application->jobVacancy->company->name}}</p>
                                    <p class="text-slate-400">{{$application->jobVacancy->location}}</p>
                                    <p class="mt-2 text-slate-400">{{$application->created_at->format('d M Y')}}</p>
                                    <p class="mt-3 text-slate-300">
                                        Applied With: <span class="font-medium">{{$application->resume->fileName}}</span>
                                        <a href="{{Storage::disk('cloud')->url($application->resume->fileUri)}}" target="_blank" class="ml-2 text-indigo-400 hover:text-indigo-300 underline underline-offset-2">View Resume</a>
                                    </p>
                                    <div class="mt-4 flex flex-wrap items-center gap-3">
                                        <span class="inline-flex items-center rounded-md bg-amber-500/20 text-amber-200 px-3 py-1 text-sm ring-1 ring-amber-500/30 {{ $application->status == 'pending' ? 'bg-amber-500/20 text-amber-200 ring-amber-500/30' : ($application->status == 'accepted' ? 'bg-green-500/20 text-green-200 ring-green-500/30': 'bg-red-500/20 text-red-200 ring-red-500/30')}}">Status: {{$application->status}}</span>
                                        <span class="inline-flex items-center rounded-md bg-indigo-500/20 text-indigo-200 px-3 py-1 text-sm ring-1 ring-indigo-500/30">Score: {{$application->aiGeneratedScore}}</span>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <span class="inline-flex items-center rounded-md bg-blue-600 text-white px-3 py-1 text-sm shadow">{{$application->jobVacancy->type}}</span>
                                </div>
                            </div>
            
                            <div class="mt-6 rounded-xl bg-slate-800/60 p-4 ring-1 ring-slate-700">
                                <p class="text-slate-200 font-semibold">AI Feedback:</p>
                                <p class="mt-2 text-slate-300 leading-relaxed">
                                {{$application->aiGeneratedFeedback}}
                                </p>
                            </div>
                        </div>
                        </div>          
                    @empty
                        <div class="py-12 flex w-full justify-center items-center">
                            <div class="bg-zinc-900/80 rounded-xl shadow-md p-8 flex flex-col items-center">
                                <p class="text-lg text-gray-400 text-center font-semibold">
                                    No applications found
                                </p>
                            </div>
                        </div>           
                    @endforelse
                </div>
                <div class="my-4">
                    {{ $applications->links() }}
                </div>

            </div>
        </div>
    </div>


</x-app-layout>
