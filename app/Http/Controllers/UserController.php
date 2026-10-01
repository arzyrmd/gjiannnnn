<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $selectedBulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedBulan);

        $query = User::query();

        // Search query
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role') && in_array($request->role, ['admin', 'teknisi'])) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('role', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Attach statistics per user
        $users->getCollection()->transform(function ($userItem) use ($year, $month) {
            $userItem->total_job_bulan = JobOrder::where('user_id', $userItem->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('kategori', 'not like', 'Piket%')
                ->count();

            $userItem->total_piket_bulan = JobOrder::where('user_id', $userItem->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('kategori', 'like', 'Piket%')
                ->count();

            $userItem->pendapatan_bulan = JobOrder::where('user_id', $userItem->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('tarif');

            $userItem->total_job_overall = JobOrder::where('user_id', $userItem->id)->count();
            $userItem->pendapatan_overall = JobOrder::where('user_id', $userItem->id)->sum('tarif');

            $lastJob = JobOrder::where('user_id', $userItem->id)->orderBy('tanggal', 'desc')->first();
            $userItem->last_active = $lastJob ? $lastJob->tanggal->format('d/m/Y') : '-';

            return $userItem;
        });

        // Global User Counts
        $countAdmin = User::where('role', 'admin')->count();
        $countTeknisi = User::where('role', 'teknisi')->count();
        $countTotal = User::count();

        return view('admin.users.index', compact(
            'users',
            'selectedBulan',
            'countAdmin',
            'countTeknisi',
            'countTotal'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,teknisi'],
        ], [
            'name.required' => 'Nama user wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role user wajib dipilih.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->back()->with('success', "User '{$user->name}' (Role: " . strtoupper($user->role) . ") berhasil ditambahkan.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,teknisi'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama user wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'role.required' => 'Role user wajib dipilih.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', "Data user '{$user->name}' berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "User '{$userName}' telah berhasil dihapus.");
    }
}
