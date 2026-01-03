<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard - Sistem Pengelolaan Karyawan & Hunian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Karyawan -->
                <div class="stats-card">
                    <div class="stats-label">TOTAL KARYAWAN</div>
                    <div class="stats-number text-blue-600" id="totalEmployees">
                        <span class="inline-block animate-pulse">...</span>
                    </div>
                    <div class="text-sm text-gray-600 mt-2">
                        <span class="text-green-600 font-semibold" id="activeEmployees">-</span> Aktif |
                        <span class="text-red-600 font-semibold" id="inactiveEmployees">-</span> Non-aktif
                    </div>
                </div>

                <!-- Total Kamar -->
                <div class="stats-card">
                    <div class="stats-label">TOTAL KAMAR</div>
                    <div class="stats-number text-indigo-600" id="totalRooms">
                        <span class="inline-block animate-pulse">...</span>
                    </div>
                    <div class="text-sm text-gray-600 mt-2">
                        Kapasitas 1: <span id="capacity1">-</span> | Kapasitas 2: <span id="capacity2">-</span>
                    </div>
                </div>

                <!-- Kamar Tersedia -->
                <div class="stats-card">
                    <div class="stats-label">KAMAR TERSEDIA</div>
                    <div class="stats-number text-green-600" id="availableRooms">
                        <span class="inline-block animate-pulse">...</span>
                    </div>
                    <div class="text-sm text-gray-600 mt-2">
                        Dari total <span id="totalRoomsText">-</span> kamar
                    </div>
                </div>

                <!-- Kamar Terisi -->
                <div class="stats-card">
                    <div class="stats-label">KAMAR TERISI</div>
                    <div class="stats-number text-orange-600" id="occupiedRooms">
                        <span class="inline-block animate-pulse">...</span>
                    </div>
                    <div class="text-sm text-gray-600 mt-2">
                        Total Penghuni: <span id="totalOccupancies">-</span> orang
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Menu Utama</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('employees.index') }}" class="block p-6 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                        <h4 class="font-semibold text-blue-900">Kelola Data Karyawan</h4>
                        <p class="text-sm text-blue-700 mt-2">Tambah, edit, dan hapus data karyawan</p>
                    </a>
                    <a href="{{ route('rooms.index') }}" class="block p-6 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                        <h4 class="font-semibold text-indigo-900">Kelola Kamar & Hunian</h4>
                        <p class="text-sm text-indigo-700 mt-2">Kelola kamar mess dan guest house</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load statistics from API
        function loadDashboardStats() {
            fetch("{{ route('dashboard.stats') }}")
                .then(response => response.json())
                .then(data => {
                    // Update statistics
                    document.getElementById('totalEmployees').textContent = data.total_employees;
                    document.getElementById('activeEmployees').textContent = data.active_employees;
                    document.getElementById('inactiveEmployees').textContent = data.inactive_employees;

                    document.getElementById('totalRooms').textContent = data.total_rooms;
                    document.getElementById('capacity1').textContent = data.capacity_1;
                    document.getElementById('capacity2').textContent = data.capacity_2;

                    document.getElementById('availableRooms').textContent = data.available_rooms;
                    document.getElementById('totalRoomsText').textContent = data.total_rooms;

                    document.getElementById('occupiedRooms').textContent = data.occupied_rooms;
                    document.getElementById('totalOccupancies').textContent = data.total_occupancies;
                })
                .catch(error => {
                    console.error('Error loading dashboard stats:', error);
                    // Show error state
                    document.querySelectorAll('.stats-number').forEach(el => {
                        el.innerHTML = '<span class="text-red-500">Error</span>';
                    });
                });
        }

        // Load stats on page load
        document.addEventListener('DOMContentLoaded', loadDashboardStats);

        // Optional: Auto-refresh every 30 seconds
        setInterval(loadDashboardStats, 30000);
    </script>
</x-app-layout>
