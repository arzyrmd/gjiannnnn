@extends('layouts.app')

@section('title', 'Manajemen User & Teknisi')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 md:p-7 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative overflow-hidden">
        <div class="space-y-1 z-10">
            <h2 class="text-lg sm:text-xl font-black uppercase text-white tracking-wider flex items-center gap-2.5">
                <span class="material-symbols-outlined text-amber-400 text-2xl">manage_accounts</span>
                <span>Manajemen Data User &amp; Teknisi</span>
            </h2>
            <p class="text-xs text-slate-400 max-w-2xl leading-relaxed">
                Kelola akun pengguna sistem GajianARMN, hak akses role (Admin / Teknisi), serta pantau statistik pekerjaan masing-masing teknisi.
            </p>
        </div>

        <button 
            onclick="openAddUserModal()" 
            class="w-full md:w-auto px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:from-amber-300 hover:to-orange-300 transition-all shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 min-h-[46px] shrink-0 cursor-pointer"
        >
            <span class="material-symbols-outlined text-lg">person_add</span>
            <span>Tambah User / Teknisi Baru</span>
        </button>
    </div>

    <!-- STATS SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Card Total User -->
        <div class="p-5 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-2 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Total User Terdaftar</span>
                <div class="w-9 h-9 rounded-2xl bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">group</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-mono-num font-black text-white">{{ $countTotal }}</span>
                <span class="text-xs font-bold text-slate-500">Akun</span>
            </div>
        </div>

        <!-- Card Teknisi -->
        <div class="p-5 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-2 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Teknisi Lapangan</span>
                <div class="w-9 h-9 rounded-2xl bg-cyan-500/20 border border-cyan-500/30 text-cyan-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">engineering</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-mono-num font-black text-cyan-400">{{ $countTeknisi }}</span>
                <span class="text-xs font-bold text-slate-500">Teknisi</span>
            </div>
        </div>

        <!-- Card Admin -->
        <div class="p-5 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-2 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Administrator</span>
                <div class="w-9 h-9 rounded-2xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-mono-num font-black text-amber-400">{{ $countAdmin }}</span>
                <span class="text-xs font-bold text-slate-500">Admin</span>
            </div>
        </div>
    </div>

    <!-- FILTER & USER TABLE CONTAINER -->
    <div class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 md:p-7 shadow-2xl space-y-5">
        
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto flex-1">
                <!-- Search Input -->
                <div class="relative w-full sm:w-72">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}" 
                        placeholder="Cari nama atau email..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white font-medium text-xs focus:border-amber-400 focus:outline-none min-h-[42px]"
                    >
                </div>

                <!-- Role Selector -->
                <select name="role" onchange="this.form.submit()" class="w-full sm:w-44 px-3.5 py-2.5 rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px] cursor-pointer">
                    <option value="">Semua Role</option>
                    <option value="teknisi" {{ request('role') == 'teknisi' ? 'selected' : '' }}>Role: Teknisi</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Role: Admin</option>
                </select>

                <!-- Month Selector -->
                <input 
                    type="month" 
                    name="bulan" 
                    value="{{ $selectedBulan }}" 
                    onchange="this.form.submit()"
                    class="w-full sm:w-44 px-3.5 py-2.5 rounded-2xl bg-slate-950/80 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px] cursor-pointer"
                >
            </div>

            @if(request()->filled('q') || request()->filled('role'))
                <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs uppercase tracking-wider transition-colors shrink-0">
                    Reset Filter
                </a>
            @endif
        </form>

        <!-- Desktop View Table -->
        <div class="hidden sm:block overflow-x-auto rounded-2xl border border-slate-800/80 shadow-inner">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-4 px-5">User / Email</th>
                        <th class="py-4 px-5">Role</th>
                        <th class="py-4 px-5 text-center">Rekap Jobs Bulan Ini</th>
                        <th class="py-4 px-5 text-right">Pendapatan Bulan Ini</th>
                        <th class="py-4 px-5 text-center">Aktif Terakhir</th>
                        <th class="py-4 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <!-- User & Email -->
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 text-amber-400 flex items-center justify-center font-black uppercase text-sm shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-white text-sm truncate flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Anda</span>
                                            @endif
                                        </h4>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-4 px-5">
                                @if($user->isAdmin())
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/40 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">admin_panel_settings</span>
                                        <span>ADMIN</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-cyan-400/20 text-cyan-300 border border-cyan-400/40 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">engineering</span>
                                        <span>TEKNISI</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Rekap Jobs -->
                            <td class="py-4 px-5 text-center font-mono-num">
                                <div class="inline-flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-200 font-bold text-xs" title="Total Job Order">
                                        {{ $user->total_job_bulan }} JO
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-amber-950/60 text-amber-300 border border-amber-800/40 font-bold text-xs" title="Total Piket">
                                        {{ $user->total_piket_bulan }} Piket
                                    </span>
                                </div>
                            </td>

                            <!-- Pendapatan -->
                            <td class="py-4 px-5 text-right font-mono-num font-black text-emerald-400 text-sm">
                                Rp {{ number_format($user->pendapatan_bulan, 0, ',', '.') }}
                            </td>

                            <!-- Last Active -->
                            <td class="py-4 px-5 text-center font-mono-num text-slate-400 text-xs">
                                {{ $user->last_active }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        onclick="openEditUserModal({{ json_encode($user) }})" 
                                        class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 border border-slate-700/80 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-1 min-h-[34px] cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user {{ $user->name }}? Data pekerjaan yang pernah dibuat akan tetap tersimpan.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 border border-rose-800/60 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-1 min-h-[34px] cursor-pointer">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-500 font-bold uppercase tracking-widest">
                                Tidak ada data user yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden space-y-3">
            @forelse($users as $user)
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 text-amber-400 flex items-center justify-center font-black text-xs uppercase">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-extrabold text-white text-xs truncate max-w-[170px]">{{ $user->name }}</h4>
                                <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        @if($user->isAdmin())
                            <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/40">ADMIN</span>
                        @else
                            <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-cyan-400/20 text-cyan-300 border border-cyan-400/40">TEKNISI</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-900/90 p-2.5 rounded-xl border border-slate-800">
                            <span class="text-[9px] font-extrabold uppercase text-slate-400 block">Job &amp; Piket Bulan Ini</span>
                            <span class="font-mono-num font-bold text-slate-200 text-xs">{{ $user->total_job_bulan }} JO / {{ $user->total_piket_bulan }} Piket</span>
                        </div>
                        <div class="bg-slate-900/90 p-2.5 rounded-xl border border-slate-800">
                            <span class="text-[9px] font-extrabold uppercase text-slate-400 block">Pendapatan Bulan Ini</span>
                            <span class="font-mono-num font-black text-emerald-400 text-xs">Rp {{ number_format($user->pendapatan_bulan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-slate-800/80">
                        <span class="text-[10px] text-slate-500 font-mono-num">Aktif: {{ $user->last_active }}</span>

                        <div class="flex items-center gap-2">
                            <button 
                                onclick="openEditUserModal({{ json_encode($user) }})" 
                                class="px-3 py-1 rounded-lg bg-slate-800 text-amber-400 border border-slate-700 font-bold text-[10px] uppercase cursor-pointer"
                            >
                                Edit
                            </button>

                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user {{ $user->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-rose-950 text-rose-300 border border-rose-800 font-bold text-[10px] uppercase cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-2xl bg-slate-950/80 border border-slate-800">
                    Tidak ada data user.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="pt-3">
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- TAMBAH USER MODAL -->
<div id="addUserModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900/95 border border-amber-400/60 rounded-3xl max-w-md w-full p-6 sm:p-7 space-y-5 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3.5">
            <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-xl">person_add</span>
                <span>Tambah User / Teknisi Baru</span>
            </h3>
            <button onclick="closeAddUserModal()" class="text-slate-400 hover:text-white font-bold p-1 cursor-pointer">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="add_name" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Nama Lengkap <span class="text-amber-400">*</span>
                </label>
                <input type="text" name="name" id="add_name" required placeholder="Contoh: Budi Teknisi" class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="space-y-1.5">
                <label for="add_email" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Alamat Email <span class="text-amber-400">*</span>
                </label>
                <input type="email" name="email" id="add_email" required placeholder="teknisi@armn.id" class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="space-y-1.5">
                <label for="add_role" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Role Hak Akses <span class="text-amber-400">*</span>
                </label>
                <select name="role" id="add_role" required class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px] cursor-pointer">
                    <option value="teknisi">TEKNISI (Akses Form Input &amp; Dashboard Saya)</option>
                    <option value="admin">ADMIN (Akses Penuh Kelola User, Tarif &amp; Monitoring Global)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="add_password" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Password <span class="text-amber-400">*</span>
                </label>
                <input type="password" name="password" id="add_password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800/80">
                <button type="button" onclick="closeAddUserModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-700 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:from-amber-300 hover:to-orange-300 transition-all cursor-pointer shadow-lg shadow-amber-500/20">
                    Simpan User Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div id="editUserModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900/95 border border-amber-400/60 rounded-3xl max-w-md w-full p-6 sm:p-7 space-y-5 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3.5">
            <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-xl">manage_accounts</span>
                <span>Edit Data User</span>
            </h3>
            <button onclick="closeEditUserModal()" class="text-slate-400 hover:text-white font-bold p-1 cursor-pointer">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="editUserForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="edit_name" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Nama Lengkap
                </label>
                <input type="text" name="name" id="edit_name" required class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="space-y-1.5">
                <label for="edit_email" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Alamat Email
                </label>
                <input type="email" name="email" id="edit_email" required class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="space-y-1.5">
                <label for="edit_role" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Role Hak Akses
                </label>
                <select name="role" id="edit_role" required class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px] cursor-pointer">
                    <option value="teknisi">TEKNISI (Akses Form Input &amp; Dashboard Saya)</option>
                    <option value="admin">ADMIN (Akses Penuh Kelola User, Tarif &amp; Monitoring Global)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="edit_password" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Password Baru <span class="text-slate-500 font-normal">(Kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" id="edit_password" minlength="6" placeholder="Kosongkan jika tidak mau ganti" class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800/80">
                <button type="button" onclick="closeEditUserModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-700 transition-colors cursor-pointer">
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
    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
    }

    function openEditUserModal(user) {
        document.getElementById('editUserForm').action = `/users/${user.id}`;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_password').value = '';
        document.getElementById('editUserModal').classList.remove('hidden');
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.add('hidden');
    }
</script>
@endpush
