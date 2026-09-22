<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('id')->get();
        $query = AcademicCalendar::with('academicYear');

        // Filter berdasarkan Tahun Ajaran
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Diurutkan berdasarkan tanggal pelaksanaan terdekat
        $calendars = $query->orderBy('start_date', 'asc')->paginate(15)->withQueryString();

        return view('admin.akademik.kalender.index', compact('calendars', 'academicYears'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderByDesc('id')->get();
        return view('admin.akademik.kalender.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'event_name'       => 'required|string|max:255',
            'type'             => 'required|in:Libur,Ujian,Kegiatan,Lainnya',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'description'      => 'nullable|string',
        ], [
            'end_date.after_or_equal' => 'Tanggal berakhir tidak boleh mendahului tanggal mulai.',
        ]);

        try {
            AcademicCalendar::create($request->all());
            return redirect()->route('admin.kalender.index')->with('success', 'Agenda kalender akademik berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan agenda: ' . $e->getMessage());
        }
    }

    public function edit(AcademicCalendar $kalender_akademik)
    {
        $academicYears = AcademicYear::orderByDesc('id')->get();

        // Peringatan: Nama variabel $kalender_akademik menyesuaikan parameter default dari route resource Laravel.
        return view('admin.akademik.kalender.edit', compact('kalender_akademik', 'academicYears'));
    }

    public function update(Request $request, AcademicCalendar $kalender_akademik)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'event_name'       => 'required|string|max:255',
            'type'             => 'required|in:Libur,Ujian,Kegiatan,Lainnya',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'description'      => 'nullable|string',
        ], [
            'end_date.after_or_equal' => 'Tanggal berakhir tidak boleh mendahului tanggal mulai.',
        ]);

        try {
            $kalender_akademik->update($request->all());
            return redirect()->route('admin.kalender.index')->with('success', 'Agenda kalender akademik berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui agenda: ' . $e->getMessage());
        }
    }

    public function destroy(AcademicCalendar $kalender_akademik)
    {
        try {
            $kalender_akademik->delete();
            return back()->with('success', 'Agenda berhasil dihapus dari kalender.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus agenda.');
        }
    }
}
