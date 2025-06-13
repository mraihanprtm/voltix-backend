<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil semua data user, urutkan dari yang terbaru, pagination (10 user per halaman)
        $users = User::latest()->paginate(10);

        // Kirim data users ke view 'admin.dashboard'
        return view('admin.dashboard', ['users' => $users]);
    }
}