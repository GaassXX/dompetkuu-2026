<div>
    {{-- Header --}}
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-5 pt-12 pb-8 -mt-2">
        <h1 class="text-2xl font-extrabold text-white">📖 Panduan</h1>
        <p class="text-amber-100 text-sm mt-1">Pelajari cara menggunakan DompetKuu</p>
    </div>

    {{-- Accordion --}}
    <div class="px-4 -mt-4 pb-28 space-y-2">
        @forelse ($sections as $index => $section)
                <div x-data="{ open: false }"
                 class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-4 text-left active:bg-gray-50 transition"
                    :class="{ 'border-b border-gray-100': open }">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">{{ $section->icon }}</span>
                        <span class="font-semibold text-gray-800">{{ $section->title }}</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="px-4 pb-4 text-sm text-gray-600 leading-relaxed">
                    {!! $section->content !!}
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-400">
                <p class="text-4xl mb-3">📭</p>
                <p>Belum ada panduan tersedia.</p>
            </div>
        @endforelse
    </div>

</div>
