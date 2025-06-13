<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        // Validasi data yang masuk
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,user',
        ]);

        // Update data user
        $user->name = $request->name;
        $user->role = $request->role;
        $user->save();

        // Arahkan kembali ke dashboard dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.dashboard')->with('error', 'You cannot delete your own account.');
        }

        // Hapus user dari database
        $user->delete();

        // Arahkan kembali ke dashboard dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'User has been deleted successfully.');
    }
}
