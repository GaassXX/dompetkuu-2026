<div class="min-h-screen bg-gray-50 text-gray-900 pb-10">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-4 pt-4 pb-2">
        <a href="{{ route('mobile.history') }}" class="text-gray-500">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <span class="font-bold text-gray-800">Tambah Transaksi</span>
    </div>

    {{-- Step 1: Pilih tipe --}}
    @if (!$type)
        <div class="mx-4 mt-4 space-y-3">
            <button wire:click="selectType('Pemasukan')"
                class="w-full flex items-center gap-3 bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                <div class="w-11 h-11 rounded-full bg-emerald-100 flex items-center justify-center">
                    <x-heroicon-o-arrow-down class="w-5 h-5 text-emerald-600" />
                </div>
                <div class="text-left">
                    <p class="font-semibold text-sm text-gray-800">Pemasukan</p>
                    <p class="text-xs text-gray-400">Gaji, hadiah, transfer masuk, dll</p>
                </div>
            </button>

            <button wire:click="selectType('Pengeluaran')"
                class="w-full flex items-center gap-3 bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                <div class="w-11 h-11 rounded-full bg-red-100 flex items-center justify-center">
                    <x-heroicon-o-arrow-up class="w-5 h-5 text-red-500" />
                </div>
                <div class="text-left">
                    <p class="font-semibold text-sm text-gray-800">Pengeluaran</p>
                    <p class="text-xs text-gray-400">Belanja, makan, transport, dll</p>
                </div>
            </button>
        </div>
    @else
        {{-- Step 2: Form --}}
        <div class="mx-4 mt-4">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-xs font-medium px-3 py-1 rounded-full {{ $type === 'Pemasukan' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                    {{ $type }}
                </span>
                <button wire:click="selectType(null)" type="button" class="text-xs text-gray-400 underline">Ganti</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Kategori</label>
                    <select wire:model="category_id" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Jumlah (Rp)</label>
                    <input type="number" wire:model="amount" placeholder="0"
                           class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400">
                    @error('amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Tanggal</label>
                    <input type="date" wire:model="date"
                           class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400">
                    @error('date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Catatan (opsional)</label>
                    <textarea wire:model="description" rows="2" placeholder="Tulis catatan..."
                              class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-medium text-sm py-3 rounded-xl">
                    Simpan Transaksi
                </button>
            </form>
        </div>
    @endif
</div>
