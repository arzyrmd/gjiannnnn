@extends('layouts.app')

@section('title', 'Manajemen Kategori & Tarif Admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 md:p-7 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative overflow-hidden">
        <div class="space-y-1 z-10">
            <h2 class="text-lg sm:text-xl font-black uppercase text-white tracking-wider flex items-center gap-2.5">
                <span class="material-symbols-outlined text-amber-400 text-2xl">settings</span>
                <span>Panel Admin Tarif</span>
            </h2>
            <p class="text-xs text-slate-400 max-w-2xl leading-relaxed">
                Kelola master kategori tugas dan nominal tarif (Berhasil / Gagal). Tarif ini digunakan sebagai rujukan snapshot saat membuat job order baru.
            </p>
        </div>

        <button 
            onclick="document.getElementById('addTarifForm').scrollIntoView({ behavior: 'smooth' })" 
            class="w-full md:w-auto px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:from-amber-300 hover:to-orange-300 transition-all shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 min-h-[46px] shrink-0 cursor-pointer"
        >
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Tambah Kategori Tarif</span>
        </button>
    </div>

    <!-- TARIF MASTER TABLE -->
    <div class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 md:p-7 shadow-2xl space-y-5">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
            <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400 text-xl">payments</span>
                <span>Daftar Master Tarif Aktif ({{ $tarifs->count() }} Kategori)</span>
            </h3>
        </div>

        <!-- Desktop View Table -->
        <div class="hidden sm:block overflow-x-auto rounded-2xl border border-slate-800/80 shadow-inner">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-4 px-5">#</th>
                        <th class="py-4 px-5">Kategori Tugas</th>
                        <th class="py-4 px-5 text-right">Tarif Berhasil (Rp)</th>
                        <th class="py-4 px-5 text-right">Tarif Gagal (Rp)</th>
                        <th class="py-4 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-medium">
                    @forelse($tarifs as $index => $tarif)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-5 text-slate-500 font-mono-num">{{ $index + 1 }}</td>
                            <td class="py-4 px-5 font-bold text-white text-sm">
                                {{ $tarif->kategori }}
                            </td>
                            <td class="py-4 px-5 text-right font-mono-num font-black text-emerald-400 text-sm">
                                Rp {{ number_format($tarif->tarif_berhasil, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-right font-mono-num font-black text-rose-300 text-sm">
                                @if(is_null($tarif->tarif_gagal) || $tarif->tarif_gagal == 0)
                                    <span class="text-slate-500 italic font-medium">Tidak dibayar</span>
                                @else
                                    Rp {{ number_format($tarif->tarif_gagal, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        onclick="openEditTarifModal({{ json_encode($tarif) }})" 
                                        class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 border border-slate-700/80 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-1 min-h-[34px]"
                                    >
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ route('tarifs.destroy', $tarif->id) }}" method="POST" onsubmit="return confirm('Hapus kategori tarif ini? Job order lama yang sudah tercatat tidak akan terpengaruh.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 border border-rose-800/60 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-1 min-h-[34px]">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-500 font-bold uppercase tracking-widest">
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
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-[10px] font-mono-num text-slate-500 font-bold">#{{ $index + 1 }}</span>
                        <h4 class="font-bold text-white text-xs truncate max-w-[200px]">{{ $tarif->kategori }}</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-900/90 p-2.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block">Berhasil</span>
                            <span class="font-mono-num font-black text-emerald-400">Rp {{ number_format($tarif->tarif_berhasil, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-slate-900/90 p-2.5 rounded-xl border border-slate-800">
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

                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-800/80">
                        <button 
                            onclick="openEditTarifModal({{ json_encode($tarif) }})" 
                            class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 border border-slate-700/80 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 min-h-[36px]"
                        >
                            <span class="material-symbols-outlined text-xs">edit</span>
                            <span>Edit</span>
                        </button>

                        <form action="{{ route('tarifs.destroy', $tarif->id) }}" method="POST" onsubmit="return confirm('Hapus kategori tarif ini? Job order lama yang sudah tercatat tidak akan terpengaruh.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 border border-rose-800/60 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 min-h-[36px]">
                                <span class="material-symbols-outlined text-xs">delete</span>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-2xl bg-slate-950/80 border border-slate-800">
                    Belum ada data tarif master. Silakan tambahkan kategori di bawah.
                </div>
            @endforelse
        </div>
    </div>


    <!-- ADD NEW TARIF FORM -->
    <div id="addTarifForm" class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 md:p-7 shadow-2xl space-y-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500"></div>

        <h3 class="text-base font-black uppercase text-white tracking-wider border-b border-slate-800/80 pb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-400 text-xl">add_circle</span>
            <span>Form Tambah Master Tarif Baru</span>
        </h3>

        <form action="{{ route('tarifs.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
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
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white font-bold text-sm focus:border-amber-400 focus:outline-none min-h-[48px]"
                    >
                </div>

                <div class="space-y-2">
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
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white font-mono font-bold text-sm focus:border-amber-400 focus:outline-none min-h-[48px]"
                    >
                </div>

                <div class="space-y-2">
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
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white font-mono font-bold text-sm focus:border-amber-400 focus:outline-none min-h-[48px]"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full md:w-auto py-3.5 px-8 rounded-2xl bg-gradient-to-r from-amber-400 via-amber-500 to-orange-400 hover:from-amber-300 hover:to-orange-300 text-slate-950 font-black text-xs uppercase tracking-wider transition-all cursor-pointer flex items-center justify-center gap-2 min-h-[48px] shadow-lg shadow-amber-500/20"
            >
                <span class="material-symbols-outlined text-lg">save</span>
                <span>Simpan Kategori Tarif Baru</span>
            </button>
        </form>
    </div>

</div>

<!-- EDIT TARIF MODAL -->
<div id="editTarifModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900/95 border border-amber-400/60 rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3.5">
            <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-xl">edit</span>
                <span>Edit Master Tarif</span>
            </h3>
            <button onclick="closeEditTarifModal()" class="text-slate-400 hover:text-white font-bold p-1 cursor-pointer">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="editTarifForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="modal_kategori" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Nama Kategori Tugas
                </label>
                <input type="text" name="kategori" id="modal_kategori" required class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="modal_tarif_berhasil" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Tarif Berhasil (Rp)
                    </label>
                    <input type="number" name="tarif_berhasil" id="modal_tarif_berhasil" required min="0" step="500" class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
                </div>

                <div class="space-y-1.5">
                    <label for="modal_tarif_gagal" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Tarif Gagal (Rp)
                    </label>
                    <input type="number" name="tarif_gagal" id="modal_tarif_gagal" min="0" step="500" placeholder="0 (Tidak dibayar)" class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3">
                <button type="button" onclick="closeEditTarifModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-700 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:from-amber-300 hover:to-orange-300 transition-all cursor-pointer shadow-lg shadow-amber-500/20">
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
