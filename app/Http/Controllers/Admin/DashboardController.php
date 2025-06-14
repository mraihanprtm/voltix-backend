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
    public function index(Request $request)
    {
        $stats = [
            'totalUsers' => User::count(),
            'newToday' => User::whereDate('created_at', today())->count(),
            'newThisWeek' => User::where('created_at', '>=', now()->subWeek())->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
        ];

        $dailyNewUsers = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subWeek())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartData = [
            'labels' => $dailyNewUsers->pluck('date')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('D, M d');
            }),
            'data' => $dailyNewUsers->pluck('count'),
        ];

        $query = User::query();
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        $users = $query->latest()->paginate(10);

        return view('admin.dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }
}