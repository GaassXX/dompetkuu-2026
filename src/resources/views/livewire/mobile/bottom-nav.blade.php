@props(['active' => 'dashboard'])

<div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t border-gray-100 px-2 py-2 shadow-[0_-2px_10px_rgba(0,0,0,0.04)]">
    <div class="flex justify-around items-center">
        <a href="{{ route('mobile.dashboard') }}" class="flex flex-col items-center text-xs {{ $active === 'dashboard' ? 'text-emerald-600' : 'text-gray-400' }}">
            <x-heroicon-o-home class="w-5 h-5" />
            Dashboard
        </a>
        <a href="{{ route('mobile.history') }}" class="flex flex-col items-center text-xs {{ $active === 'history' ? 'text-emerald-600' : 'text-gray-400' }}">
            <x-heroicon-o-clock class="w-5 h-5" />
            History
        </a>
        <a href="{{ route('mobile.ai-bot') }}" class="flex flex-col items-center text-xs {{ $active === 'ai-bot' ? 'text-emerald-600' : 'text-gray-400' }}">
            <div class="bg-emerald-600 rounded-full p-2 -mt-4 shadow-md shadow-emerald-200">
                <x-heroicon-o-cpu-chip class="w-5 h-5 text-white" />
            </div>
        </a>
        <a href="{{ route('mobile.profile') }}" class="flex flex-col items-center text-xs {{ $active === 'profile' ? 'text-emerald-600' : 'text-gray-400' }}">
            <x-heroicon-o-user class="w-5 h-5" />
            Profile
        </a>
    </div>
</div>
