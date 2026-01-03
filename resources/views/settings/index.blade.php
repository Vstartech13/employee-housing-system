<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Employee ID Auto-Generate Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">ID Karyawan Otomatis</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Sistem akan otomatis generate ID karyawan berdasarkan <strong>Kode Departemen</strong> + <strong>3 digit angka</strong>
                </p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-semibold text-blue-800">Cara Kerja:</p>
                    </div>
                    <ul class="text-sm text-blue-700 space-y-2 ml-7">
                        <li>• Pilih departemen saat menambah karyawan baru</li>
                        <li>• ID akan otomatis menggunakan kode departemen sebagai prefix</li>
                        <li>• Angka akan increment per departemen</li>
                    </ul>
                </div>

                <!-- Department List -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Daftar Departemen & Format ID:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @php
                            $departments = \App\Models\Department::withCount('employees')->get();
                        @endphp
                        @foreach($departments as $dept)
                            <div class="flex items-center justify-between bg-white px-3 py-2 rounded border border-gray-200">
                                <div>
                                    <span class="font-semibold text-gray-700">{{ $dept->name }}</span>
                                    <span class="text-xs text-gray-500 ml-2">({{ $dept->employees_count }} karyawan)</span>
                                </div>
                                <span class="font-mono text-sm font-bold text-indigo-600">
                                    {{ $dept->code }}{{ str_pad($dept->employees_count + 1, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-3">
                        * Angka di sebelah kanan menunjukkan ID yang akan digenerate untuk karyawan baru di departemen tersebut
                    </p>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-green-800">Keuntungan Sistem Ini</h4>
                        <ul class="text-sm text-green-700 mt-1 space-y-1">
                            <li>✓ ID mudah diidentifikasi departemennya</li>
                            <li>✓ Tidak perlu input manual</li>
                            <li>✓ Otomatis terurut per departemen</li>
                            <li>✓ Tidak ada duplikasi ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
