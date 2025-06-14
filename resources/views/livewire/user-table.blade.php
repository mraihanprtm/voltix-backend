<div>
    {{-- Form Pencarian --}}
    <div class="mb-6"> {{-- Tambah margin-bottom lebih besar --}}
        <label for="search" class="sr-only">Search</label>
        <input type="text" wire:model.live.debounce.300ms="search" id="search" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search by name or email...">
    </div>

    {{-- Tabel User --}}
    <div class="flow-root">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg"> {{-- Tambahkan container shadow dan rounded --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50"> {{-- Beri warna pada table head --}}
                            <tr>
                                {{-- PERBAIKAN PADDING: Tambahkan padding lebih besar di header tabel --}}
                                <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-sm font-semibold text-gray-900">Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Joined At</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    {{-- PERBAIKAN PADDING: Tambahkan padding lebih besar di sel tabel --}}
                                    <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{-- PERBAIKAN: Warna label role baru yang lebih menarik --}}
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            {{-- PERBAIKAN: Tombol Edit dengan warna Kuning --}}
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center gap-1.5 rounded-md bg-yellow-100 px-2.5 py-1.5 text-xs font-semibold text-yellow-800 shadow-sm hover:bg-yellow-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-500">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25-1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                                                </svg>
                                                Edit
                                            </a>

                                            {{-- PERBAIKAN: Tombol Delete dengan warna Merah --}}
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-md bg-red-100 px-2.5 py-1.5 text-xs font-semibold text-red-800 shadow-sm hover:bg-red-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-500">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.58.22-2.365.468a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193v-.443A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="whitespace-nowrap px-3 py-10 text-sm text-gray-500 text-center">
                                        No users found matching your search.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <div class="mt-4 px-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>