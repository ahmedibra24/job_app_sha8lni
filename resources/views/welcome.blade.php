<x-main-layout>
    <div x-data="{ show: false }" x-init="setTimeout(() => (show = true), 100)" class="px-6">
        <div x-show="show" x-transition.opacity.duration.2500ms class="flex flex-col items-center text-center">
            {{--! Pill Brand --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 ring-1 ring-white/10 text-sm text-gray-300 mb-6">
                <x-application-logo class="w-20 h-auto " />
            </span>

            {{--! Heading --}}
            <h1 class="leading-[1.05] tracking-tight">
                <span class="block text-5xl md:text-7xl font-extrabold text-white">Find your</span>
                <span class="block text-[52px] md:text-[100px] italic font-extrabold font-serif text-gray-400">Dream Job</span>
            </h1>

            {{--! Sub text --}}
            <p class="mt-6 text-base md:text-lg text-gray-400 max-w-2xl">
                connect with top employers, and find exciting opportunities
            </p>

            {{--! Actions --}}
            <div class="mt-8 flex items-center gap-4">
                <a href="{{ route('register') }}"
                   class="px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-white shadow-sm ring-1 ring-white/10 transition">
                    Create an Account
                </a>
                <a href="{{ route('login') }}"
                   class="px-5 py-3 rounded-xl text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-400 hover:to-pink-400 shadow-md transition">
                    Login
                </a>
            </div>
        </div>
    </div>
</x-main-layout>