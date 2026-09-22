@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Mata Pelajaran Saya</h1>
    </div>

    @if($courses->isEmpty())
        <x-empty-state message="Anda belum terdaftar dalam mata pelajaran apapun di semester ini." />
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <x-card class="hover:shadow-lg transition-shadow">
                    <div class="flex flex-col h-full">
                        <div class="flex-grow">
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $course->subject->nama }}</h3>
                            <p class="text-gray-600 text-sm mb-4">Guru: {{ $course->guru->nama }}</p>
                        </div>
                        <div class="pt-4 border-t mt-auto">
                            <x-button color="primary" class="w-full justify-center" href="{{ route('siswa.course.show', $course->id) }}">
                                Masuk Kelas
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>
@endsection