    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition:leave.duration.600ms x-init="setTimeout(() => show = false, 5000)" class="my-4 p-6 mx-auto max-w-7xl text-center text-sm overflow-hidden  rounded-md bg-indigo-600/90 text-white px-4 py-3 shadow-lg  relative" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
