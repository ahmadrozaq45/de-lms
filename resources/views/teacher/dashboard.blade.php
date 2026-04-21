<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Kursus Baru</h3>
                <form action="{{ route('teacher.courses.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="title" value="Judul Kursus" />
                        <x-text-input name="title" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <x-primary-button>Simpan Kursus</x-primary-button>
                </form>
            </div>

            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Kursus Anda</h3>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 px-4">Judul</th>
                            <th class="py-2 px-4">Deskripsi</th>
                            <th class="py-2 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        <tr class="border-b">
                            <td class="py-2 px-4">{{ $course->title }}</td>
                            <td class="py-2 px-4">{{ $course->description }}</td>
                            <td class="py-2 px-4">
                                <a href="#" class="text-blue-600 hover:underline">Kelola</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">Belum ada kursus.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>