<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-4 pt-4 pb-2">
        <a href="{{ route('mobile.tabungan') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
            <x-heroicon-o-arrow-left class="w-4 h-4 text-gray-500" />
        </a>
        <span class="font-bold text-gray-800">Tambah Tabungan</span>
    </div>

    <form wire:submit="save" class="mx-4 mt-3 space-y-4">

        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm space-y-4">
            <div>
                <label class="text-xs font-medium text-gray-500">Nama Tabungan</label>
                <input type="text" wire:model="name" placeholder="e.g. Beli Sepeda Baru"
                       class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                @error('name') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-medium text-gray-500">Kategori</label>
                <select wire:model="category" class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                    @foreach ($categories as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-medium text-gray-500">Target Jumlah</label>
                <div class="mt-1 flex items-center rounded-lg border border-gray-200 focus-within:border-amber-500 focus-within:ring-1 focus-within:ring-amber-500">
                    <span class="pl-3 text-sm text-gray-400">Rp</span>
                    <input type="text" inputmode="numeric" placeholder="0"
                           x-data="{ display: '{{ $target_amount ? number_format((int)$target_amount, 0, ',', '.') : '' }}' }"
                           x-model="display"
                           x-on:input="
                               let raw = $el.value.replace(/\./g, '').replace(/\D/g, '');
                               display = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                               $wire.set('target_amount', raw);
                           "
                           class="w-full text-sm border-0 focus:ring-0 bg-transparent">
                </div>
                @error('target_amount') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-medium text-gray-500">Setoran Awal (Opsional)</label>
                <div class="mt-1 flex items-center rounded-lg border border-gray-200 focus-within:border-amber-500 focus-within:ring-1 focus-within:ring-amber-500">
                    <span class="pl-3 text-sm text-gray-400">Rp</span>
                    <input type="text" inputmode="numeric" placeholder="0"
                           x-data="{ display: '{{ $current_amount ? number_format((int)$current_amount, 0, ',', '.') : '' }}' }"
                           x-model="display"
                           x-on:input="
                               let raw = $el.value.replace(/\./g, '').replace(/\D/g, '');
                               display = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                               $wire.set('current_amount', raw);
                           "
                           class="w-full text-sm border-0 focus:ring-0 bg-transparent">
                </div>
            </div>

            <div>
                <label class="text-xs font-medium text-gray-500">Target Waktu (Opsional)</label>
                <input type="date" wire:model="target_date"
                       class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="text-xs font-medium text-gray-500">Deskripsi (Opsional)</label>
                <textarea wire:model="description" rows="3" placeholder="Tuliskan catatan atau detail lainnya..."
                          class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500"></textarea>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-amber-500 text-white text-sm font-semibold py-3 rounded-xl shadow-sm hover:bg-amber-600 transition">
            Simpan Tabungan
        </button>
    </form>

    <x-mobile.bottom-nav active="tabungan" />
</div>
