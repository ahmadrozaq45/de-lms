<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teacher Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <h4 class="text-gray-500 text-sm uppercase font-bold">Total Kursus</h4>
                    <p class="text-3xl font-bold">{{ $coursesCount ?? 0 }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <h4 class="text-gray-500 text-sm uppercase font-bold">Tugas Belum Dinilai</h4>
                    <p class="text-3xl font-bold">{{ $pendingReviews ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-bold mb-4">Aktivitas Terakhir</h3>
                    <p class="text-gray-500">Selamat datang kembali! Mulai kelola materi kursus Anda melalui menu kursus.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>