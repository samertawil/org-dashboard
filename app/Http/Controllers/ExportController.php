<?php

namespace App\Http\Controllers;

use App\Exports\StudentFiltterExport;
use App\Models\Student;
use Illuminate\Support\Facades\Gate;


class ExportController extends Controller
{
    public function exportStudentFiltter($params)
    {
        Gate::authorize('exportStudentList', Student::class);
        $decodedParams = base64_decode($params);
        $filters = json_decode($decodedParams, true) ?: [];

        return (new StudentFiltterExport($filters))->download('students_export_' . now()->format('Y-m-d_H-i') . '.xlsx');
    }
}
