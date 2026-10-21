@extends('layouts.app')

@section('title', 'Manajemen User & Teknisi')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-4 sm:p-5 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-0.5">
            <h2 class="text-base sm:text-lg font-bold text-white tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-xl">manage_accounts</span>
                <span>Manajemen Data User &amp; Teknisi</span>
            </h2>
            <p class="text-xs text-slate-400 max-w-2xl font-normal">
                Kelola akun pengguna sistem GajianARMN, hak akses role (Admin / Teknisi), serta pantau statistik pekerjaan masing-masing teknisi.
            </p>
        </div>

        <button 
            type="button"
            onclick="openAddUserModal()" 
            class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-semibold text-xs transition-all shadow-sm flex items-center justify-center gap-1.5 shrink-0 cursor-pointer"
        >
            <span class="material-symbols-outlined text-base">person_add</span>
            <span>Tambah User / Teknisi Baru</span>
        </button>
    </div>

    <!-- STATS SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <!-- Card Total User -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Total User Terdaftar</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">group</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-mono-num font-bold text-white">{{ $countTotal }}</span>
                <span class="text-xs text-slate-400 font-normal">Akun</span>
            </div>
        </div>

        <!-- Card Teknisi -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Teknisi Lapangan</span>
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">engineering</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-mono-num font-bold text-sky-400">{{ $countTeknisi }}</span>
                <span class="text-xs text-slate-400 font-normal">Teknisi</span>
            </div>
        </div>

        <!-- Card Admin -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Administrator</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">admin_panel_settings</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-mono-num font-bold text-amber-400">{{ $countAdmin }}</span>
                <span class="text-xs text-slate-400 font-normal">Admin</span>
            </div>
        </div>
    </div>

    <!-- FILTER & USER TABLE CONTAINER -->
    <div class="rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-4">
        
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full sm:w-auto flex-1">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}" 
                        placeholder="Cari nama atau email..." 
                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-normal text-xs focus:border-amber-400/60 focus:outline-none"
                    >
                </div>

                <!-- Role Selector -->
                <select name="role" onchange="this.form.submit()" class="w-full sm:w-40 px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 font-medium text-xs focus:border-amber-400/60 focus:outline-none cursor-pointer">
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
                    class="w-full sm:w-40 px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 font-mono font-medium text-xs focus:border-amber-400/60 focus:outline-none cursor-pointer"
                >
            </div>

            @if(request()->filled('q') || request()->filled('role'))
                <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-xs transition-colors shrink-0 border border-slate-700">
                    Reset Filter
                </a>
            @endif
        </form>

        <!-- Desktop View Table -->
        <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-800/80">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">User / Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4 text-center">Rekap Jobs Bulan Ini</th>
                        <th class="py-3 px-4 text-right">Pendapatan Bulan Ini</th>
                        <th class="py-3 px-4 text-center">Aktif Terakhir</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <!-- User & Email -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-semibold text-white text-xs truncate flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Anda</span>
                                            @endif
                                        </h4>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-3 px-4">
                                @if($user->isAdmin())
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-amber-400/10 text-amber-300 border border-amber-400/20 inline-flex items-center gap-1">
                                        <span>Admin</span>
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-sky-400/10 text-sky-300 border border-sky-400/20 inline-flex items-center gap-1">
                                        <span>Teknisi</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Rekap Jobs -->
                            <td class="py-3 px-4 text-center font-mono-num">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-200 font-semibold text-xs" title="Total Job Order">
                                        {{ $user->total_job_bulan }} JO
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-slate-800/60 text-slate-400 font-medium text-xs" title="Total Piket">
                                        {{ $user->total_piket_bulan }} Piket
                                    </span>
                                </div>
                            </td>

                            <!-- Pendapatan -->
                            <td class="py-3 px-4 text-right font-mono-num font-bold text-emerald-400 text-xs">
                                Rp {{ number_format($user->pendapatan_bulan, 0, ',', '.') }}
                            </td>

                            <!-- Last Active -->
                            <td class="py-3 px-4 text-center font-mono-num text-slate-400 text-xs">
                                {{ $user->last_active }}
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button 
                                        type="button"
                                        onclick="openEditUserModal({{ json_encode($user) }})" 
                                        class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-300 border border-slate-700 font-medium text-xs transition-all flex items-center gap-1 cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user {{ $user->name }}? Data pekerjaan yang pernah dibuat akan tetap tersimpan.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-rose-300 border border-slate-700 font-medium text-xs transition-all flex items-center gap-1 cursor-pointer">
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
                            <td colspan="6" class="py-8 text-center text-slate-500 font-medium text-xs">
                                Tidak ada data user yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden space-y-2.5">
            @forelse($users as $user)
                <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800 space-y-2.5">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs uppercase">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-white text-xs truncate max-w-[170px]">{{ $user->name }}</h4>
                                <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        @if($user->isAdmin())
                            <span class="px-2 py-0.5 rounded text-[9px] font-medium bg-amber-400/10 text-amber-300 border border-amber-400/20">Admin</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[9px] font-medium bg-sky-400/10 text-sky-300 border border-sky-400/20">Teknisi</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-900/90 p-2 rounded-lg border border-slate-800">
                            <span class="text-[10px] text-slate-400 block">Job &amp; Piket</span>
                            <span class="font-mono-num font-semibold text-slate-200 text-xs">{{ $user->total_job_bulan }} JO / {{ $user->total_piket_bulan }} Piket</span>
                        </div>
                        <div class="bg-slate-900/90 p-2 rounded-lg border border-slate-800">
                            <span class="text-[10px] text-slate-400 block">Pendapatan</span>
                            <span class="font-mono-num font-bold text-emerald-400 text-xs">Rp {{ number_format($user->pendapatan_bulan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-slate-800/80">
                        <span class="text-[10px] text-slate-500 font-mono-num">Aktif: {{ $user->last_active }}</span>

                        <div class="flex items-center gap-1.5">
                            <button 
                                type="button"
                                onclick="openEditUserModal({{ json_encode($user) }})" 
                                class="px-2.5 py-1 rounded-lg bg-slate-800 text-amber-300 border border-slate-700 font-medium text-xs cursor-pointer"
                            >
                                Edit
                            </button>

                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user {{ $user->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-800 text-rose-300 border border-slate-700 font-medium text-xs cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 font-medium text-xs rounded-xl bg-slate-950/80 border border-slate-800">
                    Tidak ada data user.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="pt-3 border-t border-slate-800/80">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- TAMBAH USER MODAL -->
<div id="addUserModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-5 sm:p-6 space-y-4 shadow-xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-lg">person_add</span>
                <span>Tambah User / Teknisi Baru</span>
            </h3>
            <button type="button" onclick="closeAddUserModal()" class="text-slate-400 hover:text-white p-1 cursor-pointer">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-3.5">
            @csrf

            <div class="space-y-1">
                <label for="add_name" class="block text-xs font-medium text-slate-400">
                    Nama Lengkap <span class="text-amber-400">*</span>
                </label>
                <input type="text" name="name" id="add_name" required placeholder="Contoh: Budi Teknisi" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-medium text-xs focus:border-amber-400/60 focus:outline-none" />
            </div>

            <div class="space-y-1">
                <label for="add_email" class="block text-xs font-medium text-slate-400">
                    Alamat Email <span class="text-amber-400">*</span>
                </label>
                <input type="email" name="email" id="add_email" required placeholder="teknisi@armn.id" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-medium text-xs focus:border-amber-400/60 focus:outline-none" />
            </div>

            <div class="space-y-1">
                <label for="add_role" class="block text-xs font-medium text-slate-400">
                    Role Hak Akses <span class="text-amber-400">*</span>
                </label>
                <select name="role" id="add_role" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-medium text-xs focus:border-amber-400/60 focus:outline-none cursor-pointer">
                    <option value="teknisi">Teknisi (Akses Form Input &amp; Dashboard)</option>
                    <option value="admin">Admin (Akses Penuh Kelola User &amp; Monitoring)</option>
                </select>
            </div>

            <div class="space-y-1">
                <label for="add_password" class="block text-xs font-medium text-slate-400">
                    Password <span class="text-amber-400">*</span>
                </label>
                <input type="password" name="password" id="add_password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono font-medium text-xs focus:border-amber-400/60 focus:outline-none" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800/80">
                <button type="button" onclick="closeAddUserModal()" class="px-3.5 py-2 rounded-xl bg-slate-800 text-slate-300 font-medium text-xs hover:bg-slate-700 transition-colors cursor-pointer border border-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-semibold text-xs transition-all cursor-pointer shadow-sm">
                    Simpan User Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div id="editUserModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-5 sm:p-6 space-y-4 shadow-xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-lg">manage_accounts</span>
                <span>Edit Data User</span>
            </h3>
            <button type="button" onclick="closeEditUserModal()" class="text-slate-400 hover:text-white p-1 cursor-pointer">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form id="editUserForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label for="edit_name" class="block text-xs font-medium text-slate-400">
                    Nama Lengkap
                </label>
                <input type="text" name="name" id="edit_name" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-medium text-xs focus:border-amber-400/60 focus:outline-none" />
            </div>

            <div class="space-y-1">
                <label for="edit_email" class="block text-xs font-medium text-slate-400">
                    Alamat Email
                </label>
                <input type="email" name="email" id="edit_email" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-medium text-xs focus:border-amber-400/60 focus:outline-none" />
            </div>

            <div class="space-y-1">
                <label for="edit_role" class="block text-xs font-medium text-slate-400">
                    Role Hak Akses
                </label>
                <select name="role" id="edit_role" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-medium text-xs focus:border-amber-400/60 focus:outline-none cursor-pointer">
                    <option value="teknisi">Teknisi (Akses Form Input &amp; Dashboard)</option>
                    <option value="admin">Admin (Akses Penuh Kelola User &amp; Monitoring)</option>
                </select>
            </div>

            <div class="space-y-1">
                <label for="edit_password" class="block text-xs font-medium text-slate-400">
                    Password Baru <span class="text-slate-500 font-normal">(Kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" id="edit_password" minlength="6" placeholder="Kosongkan jika tidak mau ganti" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono font-medium text-xs focus:border-amber-400/60 focus:outline-none" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800/80">
                <button type="button" onclick="closeEditUserModal()" class="px-3.5 py-2 rounded-xl bg-slate-800 text-slate-300 font-medium text-xs hover:bg-slate-700 transition-colors cursor-pointer border border-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-semibold text-xs transition-all cursor-pointer shadow-sm">
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
