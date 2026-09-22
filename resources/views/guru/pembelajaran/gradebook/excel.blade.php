<table>
    <thead>
        <tr>
            <th>NIS/NISN</th>
            <th>Nama Siswa</th>
            
            <!-- Header Kolom Tugas -->
            @foreach($assignments as $assignment)
                <th>Tugas: {{ $assignment->title }}</th>
            @endforeach
            <th>Rata-rata Tugas</th>

            <!-- Header Kolom Ujian -->
            @foreach($assessments as $assessment)
                <th>Ujian: {{ $assessment->title }}</th>
            @endforeach
            <th>Rata-rata Ujian</th>

            <!-- Nilai Akhir -->
            <th>Nilai Akhir Rapor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($gradeMatrix as $row)
            <tr>
                <td>{{ $row['siswa']->nis ?? $row['siswa']->nisn }}</td>
                <td>{{ $row['siswa']->user->name }}</td>
                
                @foreach($assignments as $assignment)
                    <td>{{ $row['assignments'][$assignment->id] }}</td>
                @endforeach
                <td>{{ number_format($row['avg_assignments'], 2) }}</td>

                @foreach($assessments as $assessment)
                    <td>{{ $row['assessments'][$assessment->id] }}</td>
                @endforeach
                <td>{{ number_format($row['avg_assessments'], 2) }}</td>

                <td><strong>{{ number_format($row['final_score'], 2) }}</strong></td>
            </tr>
        @endforeach
    </tbody>
</table>