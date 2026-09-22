<?php

namespace App\Exports;

use App\Models\Schedule;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GradebookExport implements FromView, ShouldAutoSize, WithStyles
{
    protected Schedule $schedule;
    protected array $data;

    public function __construct(Schedule $schedule, array $data)
    {
        $this->schedule = $schedule;
        $this->data = $data;
    }

    public function view(): View
    {
        return view('guru.pembelajaran.gradebook.excel', [
            'schedule'    => $this->schedule,
            'assignments' => $this->data['assignments'],
            'assessments' => $this->data['assessments'],
            'gradeMatrix' => $this->data['gradeMatrix'],
        ]);
    }

    // Tambahkan ": array" di sini untuk menyesuaikan dengan antarmuka WithStyles
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
