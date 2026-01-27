@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block mt-1 w-full bg-white/10 text-white focus:bprder-indigo-500 rounded-lg']) }}>
