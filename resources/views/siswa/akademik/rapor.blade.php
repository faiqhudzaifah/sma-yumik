@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow-sm flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Akademik & Nilai</h1>
            <p class="text-gray-600 mt-1">Semester: <strong>{{ $activeSemester->nama }}</strong></p>
        </div>
    </div>

    <x-card>
        @if($grades->isEmpty())
            <x-empty-state message="Belum ada nilai yang dipublikasikan pada semester ini." />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Kategori</th>
                        <th>Judul Aktivitas</th>
                        <th>Nilai</th>
                        <th>Tanggal Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grades as $grade)
                        <tr>
                            <td class="font-medium">{{ $grade->subject->nama }}</td>
                            <td>
                                <x-badge color="{{ $grade->assignment_id ? 'blue' : 'purple' }}">
                                    {{ $grade->assignment_id ? 'Tugas' : 'Ujian/Kuis' }}
                                </x-badge>
                            </td>
                            <td>
                                {{ $grade->assignment_id ? $grade->assignment->judul : $grade->assessment->judul }}
                            </td>
                            <td class="font-bold text-lg text-gray-800">{{ $grade->nilai }}</td>
                            <td class="text-gray-500 text-sm">{{ $grade->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        @endif
    </x-card>
</div>
@endsection 