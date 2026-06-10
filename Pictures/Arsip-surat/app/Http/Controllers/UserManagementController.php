<?php

// app/Http/Controllers/UserManagementController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,user'
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => true
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Membuat user baru: ' . $request->email
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat');
    }
        public function toggleStatus(User $user)
{
    // cegah admin menonaktifkan dirinya sendiri
    if ($user->id === auth()->id()) {
        return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri');
    }

    $user->update([
        'is_active' => !$user->is_active
    ]);

    ActivityLog::create([
        'user_id' => auth()->id(),
        'activity' => ($user->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . 
                      ' user: ' . $user->email
    ]);

    return back()->with('success', 'Status user berhasil diperbarui');
}

    }

