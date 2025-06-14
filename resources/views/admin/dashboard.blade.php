<x-app-layout>
    {{-- Mendefinisikan Judul Halaman --}}
    <x-slot name="title">
        Dashboard - Voltix Admin
    </x-slot>

    {{-- Mendefinisikan Header Halaman --}}
    <x-slot name="header">
        Dashboard
    </x-slot>

    {{-- KONTEN UTAMA HALAMAN DASHBOARD --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Kartu 1: Total Users --}}
        <div class="rounded-xl shadow-sm bg-white p-6 border border-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 bg-indigo-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372m7.5-4.372a9.38 9.38 0 00-7.5-4.372m-7.5 4.372a9.38 9.38 0 007.5 4.372M3.375 19.128a9.38 9.38 0 002.625.372M16.5 3.375a9.38 9.38 0 00-7.5 4.372m7.5 4.372a9.38 9.38 0 00-7.5-4.372" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Users</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['totalUsers'] }}</div>
                </div>
            </div>
        </div>
        
        {{-- Kartu 2: New Users Today --}}
        <div class="rounded-xl shadow-sm bg-white p-6 border border-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                     <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">New Users Today</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['newToday'] }}</div>
                </div>
            </div>
        </div>

        {{-- Kartu 3: New Users This Week --}}
        <div class="rounded-xl shadow-sm bg-white p-6 border border-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">New Users This Week</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['newThisWeek'] }}</div>
                </div>
            </div>
        </div>

        {{-- Kartu 4: Total Admins --}}
        <div class="rounded-xl shadow-sm bg-white p-6 border border-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Admins</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['totalAdmins'] }}</div>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-base font-semibold leading-6 text-gray-900">New User Registration (Last 7 Days)</h3>
        <div class="mt-4">
            <canvas id="userChart" style="max-height: 250px;"></canvas>
        </div>
    </div>

    {{-- AKHIR DARI KONTEN UTAMA --}}


    {{-- Mendefinisikan script khusus untuk halaman ini --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('userChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'New Users',
                    data: @json($chartData['data']),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)', // Warna indigo
                    borderRadius: 4,
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>
    @endpush
</x-app-layout>