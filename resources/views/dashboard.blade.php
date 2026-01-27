<x-app-layout>
    {{--! ========================================= HEADER =====================================  --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Welcome back, ' ) }} {{ auth()->user()->name }}
        </h2>
    </x-slot>
    
    {{--! ========================================= MAIN CONTENT =====================================  --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-black/90 border border-white/10 rounded-2xl p-6 md:p-8">
                {{--! Top Bar: Search + Filters --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    {{--! Search --}}
                    <div class="w-96 md:max-w-lg">
                        <form action="{{ route('dashboard') }}" method="GET" class="flex ">
                            <div class="flex items-stretch overflow-hidden rounded-xl ring-1 ring-white/10 bg-zinc-900">
                                <input name="search" value="{{ request('search') }}" type="text" placeholder="Search for a job" class="w-full bg-transparent px-4 py-3 text-gray-200 placeholder-gray-500 focus:outline-none rounded-l-xl">
                                <button type="submit" class="px-5 text-white font-medium bg-gradient-to-r from-indigo-500 to-violet-500 hover:from-indigo-400 hover:to-violet-400">
                                    Search
                                </button>
                                
                                {{--? to keep the filter with search --}}
                                @if (request()->has('filter'))
                                <input type="hidden" name="filter" value="{{ request('filter') }}">                            
                                @endif
                            </div>

                            {{--! clear search --}}
                            @if (request()->has('search'))
                            <a href="{{route('dashboard',['filter'=>request('filter')])}}"
                             class="px-4 py-2  hover:cursor-pointer hover:border-spacing-1">
                             clear
                            </a>
                            @endif
                            
                        </form>
                    </div>
                    {{--! Filters --}}
                    <div class="flex items-center gap-3 self-end md:self-auto">
                        <a href="{{route('dashboard',['filter'=>'Full-time','search'=>request('search')]) }}"
                            class="px-4 py-2 rounded-xl ring-1 hover:cursor-pointer 
                            {{ request('filter') == 'Full-time' ? 'bg-indigo-500 text-white ring-indigo-500' : 'bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 ring-indigo-400/30' }}">
                            Full-Time
                        </a>
                        <a href="{{route('dashboard',['filter'=>'Remote','search'=>request('search')])}}"
                            class="px-4 py-2 rounded-xl ring-1 hover:cursor-pointer 
                            {{ request('filter') == 'Remote' ? 'bg-indigo-500 text-white ring-indigo-500' : 'bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 ring-indigo-400/30' }}">
                            Remote
                        </a>
                        <a href="{{route('dashboard',['filter'=>'Hybrid','search'=>request('search')])}}"
                            class="px-4 py-2 rounded-xl ring-1 hover:cursor-pointer 
                            {{ request('filter') == 'Hybrid' ? 'bg-indigo-500 text-white ring-indigo-500' : 'bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 ring-indigo-400/30' }}">
                            Hybrid
                        </a>
                        <a href="{{route('dashboard',['filter'=>'Contract','search'=>request('search')])}}"
                            class="px-4 py-2 rounded-xl ring-1 hover:cursor-pointer 
                            {{ request('filter') == 'Contract' ? 'bg-indigo-500 text-white ring-indigo-500' : 'bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 ring-indigo-400/30' }}">
                            Contract
                        </a>
                        
                        {{--! clear filter --}}
                        @if (request()->has('filter'))
                        <a href="{{route('dashboard',['search'=>request('search')])}}"
                             class="px-4 py-2  hover:cursor-pointer hover:border-spacing-1">
                             clear
                        </a>
                        @endif
                    </div>
                </div>

                {{--! List --}}
                <div class="mt-6 divide-y divide-white/10">
                    @forelse ( $jobs as $job )                    
                    <div class="py-6 flex items-start justify-between">
                        <div>
                            <a href="{{ route('job-vacancies.show',$job->id) }}" class="text-xl font-semibold text-indigo-300 hover:text-indigo-200">{{ $job->title }}</a>
                            <div class="mt-1 text-gray-400">{{ $job->location }}</div>
                            <div class="mt-1 text-gray-400">{{ $job->company->name }}</div>
                            <div class="text-gray-400">${{ number_format($job->salary) }} / Year</div>
                        </div>
                        <span class="px-4 py-2 rounded-xl bg-blue-500 text-white text-sm">{{ $job->type }}</span>
                    </div>

                    {{--! if no jobs found --}}
                    @empty
                    <div class="py-12 flex w-full justify-center items-center">
                        <div class="bg-zinc-900/80 rounded-xl shadow-md p-8 flex flex-col items-center">
                            <p class="text-lg text-gray-400 text-center font-semibold">
                                No jobs found
                            </p>
                            <p class="text-sm text-gray-500 mt-2 text-center">
                                Try adjusting your search or filters to find more jobs.
                            </p>
                        </div>
                    </div>           
                    @endforelse
                </div>
                
                {{--! ========================================= PAGINATION =====================================  --}}
                <div>
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
