<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', ['users' => $users]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,user',
        ]);

        $user->name = $request->name;
        $user->role = $request->role;
        $user->save();

        // PERBAIKI BARIS INI
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            // PERBAIKI BARIS INI
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        // PERBAIKI BARIS INI JUGA
        return redirect()->route('admin.users.index')->with('success', 'User has been deleted successfully.');
    }
}
