@extends('layouts.app')

@section('title', 'Manajemen Kategori & Tarif Admin')

@section('content')
<div class="space-y-4 md:space-y-6">

    <!-- Header Banner -->
    <div class="bg-slate-900 border-2 border-slate-800 rounded-2xl p-4 sm:p-5 md:p-6 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h2 class="text-lg sm:text-xl font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-xl sm:text-2xl">settings</span>
                <span>Panel Admin Tarif</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Kelola master kategori tugas dan nominal tarif (Berhasil / Gagal). Tarif ini digunakan sebagai rujukan snapshot saat membuat job order baru.
            </p>
        </div>

        <button 
            onclick="document.getElementById('addTarifForm').scrollIntoView({ behavior: 'smooth' })" 
            class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:bg-amber-300 transition-colors border-2 border-amber-300 flex items-center justify-center gap-1.5 min-h-[42px]"
        >
            <span class="material-symbols-outlined text-base">add</span>
            <span>Tambah Kategori Tarif</span>
        </button>
    </div>

    <!-- TARIF MASTER TABLE -->
    <div class="bg-slate-900 border-2 border-slate-800 rounded-2xl p-4 sm:p-5 md:p-6 shadow-xl space-y-4">
        <h3 class="text-base font-black uppercase text-white tracking-wider border-b border-slate-800 pb-3">
            Daftar Master Tarif Aktif ({{ $tarifs->count() }} Kategori)
        </h3>

        <!-- Desktop View Table -->
        <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">Kategori Tugas</th>
                        <th class="py-3.5 px-4 text-right">Tarif Berhasil (Rp)</th>
                        <th class="py-3.5 px-4 text-right">Tarif Gagal (Rp)</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-medium">
                    @forelse($tarifs as $index => $tarif)
                        <tr class="hover:bg-slate-850/60 transition-colors">
                            <td class="py-3.5 px-4 text-slate-500 font-mono-num">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-white text-sm">
                                {{ $tarif->kategori }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono-num font-black text-emerald-400 text-sm">
                                Rp {{ number_format($tarif->tarif_berhasil, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono-num font-black text-rose-300 text-sm">
                                @if(is_null($tarif->tarif_gagal) || $tarif->tarif_gagal == 0)
                                    <span class="text-slate-500 italic font-bold">Tidak dibayar</span>
                                @else
                                    Rp {{ number_format($tarif->tarif_gagal, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button 
                                        onclick="openEditTarifModal({{ json_encode($tarif) }})" 
                                        class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 border border-slate-700 font-bold text-xs uppercase tracking-wider transition-colors flex items-center gap-1 min-h-[34px]"
                                    >
                                        <span class="material-symbols-outlined text-xs">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ route('tarifs.destroy', $tarif->id) }}" method="POST" onsubmit="return confirm('Hapus kategori tarif ini? Job order lama yang sudah tercatat tidak akan terpengaruh.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-700/60 font-bold text-xs uppercase tracking-wider transition-colors flex items-center gap-1 min-h-[34px]">
                                            <span class="material-symbols-outlined text-xs">delete</span>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500 font-bold uppercase tracking-widest">
                                Belum ada data tarif master. Silakan tambahkan kategori di bawah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden space-y-3">
            @forelse($tarifs as $index => $tarif)
                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2.5">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-[10px] font-mono-num text-slate-500 font-bold">#{{ $index + 1 }}</span>
                        <h4 class="font-bold text-white text-xs truncate max-w-[200px]">{{ $tarif->kategori }}</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-900 p-2 rounded-lg border border-slate-800">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block">Berhasil</span>
                            <span class="font-mono-num font-black text-emerald-400">Rp {{ number_format($tarif->tarif_berhasil, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-slate-900 p-2 rounded-lg border border-slate-800">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block">Gagal</span>
                            <span class="font-mono-num font-black text-rose-300">
                                @if(is_null($tarif->tarif_gagal) || $tarif->tarif_gagal == 0)
                                    <span class="text-slate-500 italic text-[11px]">Tidak dibayar</span>
                                @else
                                    Rp {{ number_format($tarif->tarif_gagal, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-slate-800/80">
                        <button 
                            onclick="openEditTarifModal({{ json_encode($tarif) }})" 
                            class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-amber-400 border border-slate-700 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 min-h-[36px]"
                        >
                            <span class="material-symbols-outlined text-xs">edit</span>
                            <span>Edit</span>
                        </button>

                        <form action="{{ route('tarifs.destroy', $tarif->id) }}" method="POST" onsubmit="return confirm('Hapus kategori tarif ini? Job order lama yang sudah tercatat tidak akan terpengaruh.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 rounded bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-700/60 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 min-h-[36px]">
                                <span class="material-symbols-outlined text-xs">delete</span>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-xl bg-slate-950 border border-slate-800">
                    Belum ada data tarif master. Silakan tambahkan kategori di bawah.
                </div>
            @endforelse
        </div>
    </div>


    <!-- ADD NEW TARIF FORM -->
    <div id="addTarifForm" class="bg-slate-900 border-2 border-amber-400/80 rounded-2xl p-4 sm:p-5 md:p-6 shadow-xl space-y-4">
        <h3 class="text-base font-black uppercase text-white tracking-wider border-b border-slate-800 pb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-400 text-xl">add_circle</span>
            <span>Form Tambah Master Tarif Baru</span>
        </h3>

        <form action="{{ route('tarifs.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                <div class="space-y-1.5">
                    <label for="kategori" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Nama Kategori Tugas <span class="text-amber-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="kategori" 
                        id="kategori" 
                        required 
                        placeholder="Contoh: Maintenance Rutin" 
                        value="{{ old('kategori') }}"
                        class="w-full px-3.5 py-3 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-bold text-sm focus:border-amber-400 focus:outline-none min-h-[44px]"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="tarif_berhasil" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Tarif Berhasil (Rp) <span class="text-amber-400">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="tarif_berhasil" 
                        id="tarif_berhasil" 
                        required 
                        min="0" 
                        step="500"
                        placeholder="15000" 
                        value="{{ old('tarif_berhasil') }}"
                        class="w-full px-3.5 py-3 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-mono font-bold text-sm focus:border-amber-400 focus:outline-none min-h-[44px]"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="tarif_gagal" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Tarif Gagal (Rp) <span class="text-slate-500 font-normal">(Kosongkan jika "Tidak Dibayar")</span>
                    </label>
                    <input 
                        type="number" 
                        name="tarif_gagal" 
                        id="tarif_gagal" 
                        min="0" 
                        step="500"
                        placeholder="10000" 
                        value="{{ old('tarif_gagal') }}"
                        class="w-full px-3.5 py-3 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-mono font-bold text-sm focus:border-amber-400 focus:outline-none min-h-[44px]"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full md:w-auto py-3.5 px-6 rounded-xl bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:bg-amber-300 transition-colors border-2 border-amber-300 cursor-pointer flex items-center justify-center gap-1.5 min-h-[46px]"
            >
                <span class="material-symbols-outlined text-base">save</span>
                <span>Simpan Kategori Tarif Baru</span>
            </button>
        </form>
    </div>

</div>

<!-- EDIT TARIF MODAL -->
<div id="editTarifModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-slate-900 border-2 border-amber-400 rounded-2xl max-w-lg w-full p-4 sm:p-6 space-y-4 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400">edit</span>
                <span>Edit Master Tarif</span>
            </h3>
            <button onclick="closeEditTarifModal()" class="text-slate-400 hover:text-white font-black text-lg p-1">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="editTarifForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="modal_kategori" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Nama Kategori Tugas
                </label>
                <input type="text" name="kategori" id="modal_kategori" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div class="space-y-1.5">
                    <label for="modal_tarif_berhasil" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Tarif Berhasil (Rp)
                    </label>
                    <input type="number" name="tarif_berhasil" id="modal_tarif_berhasil" required min="0" step="500" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
                </div>

                <div class="space-y-1.5">
                    <label for="modal_tarif_gagal" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Tarif Gagal (Rp)
                    </label>
                    <input type="number" name="tarif_gagal" id="modal_tarif_gagal" min="0" step="500" placeholder="0 (Tidak dibayar)" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditTarifModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:bg-amber-300 border-2 border-amber-300">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditTarifModal(tarif) {
        document.getElementById('editTarifForm').action = `/tarifs/${tarif.id}`;
        document.getElementById('modal_kategori').value = tarif.kategori;
        document.getElementById('modal_tarif_berhasil').value = tarif.tarif_berhasil;
        document.getElementById('modal_tarif_gagal').value = (tarif.tarif_gagal !== null) ? tarif.tarif_gagal : '';
        document.getElementById('editTarifModal').classList.remove('hidden');
    }

    function closeEditTarifModal() {
        document.getElementById('editTarifModal').classList.add('hidden');
    }
</script>
@endpush
