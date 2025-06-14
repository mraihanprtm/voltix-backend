<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    // Lifecycle Hook: akan dijalankan setiap kali properti $search diupdate
    public function updatingSearch(): void
    {
        // Reset paginasi ke halaman 1 setiap kali melakukan pencarian baru
        $this->resetPage();
    }

    public function render()
    {
        // Query untuk mencari user berdasarkan nama ATAU email
        $users = User::where(function($query) {
                        $query->where('name', 'like', '%'.$this->search.'%')
                              ->orWhere('email', 'like', '%'.$this->search.'%');
                    })
                    ->latest()
                    ->paginate(10); // Gunakan paginasi dari Livewire

        // Mengirim data users ke view komponen
        return view('livewire.user-table', [
            'users' => $users,
        ]);
    }
}