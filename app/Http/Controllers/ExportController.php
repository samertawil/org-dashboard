<?php

namespace App\Http\Controllers;

use App\Exports\StudentFiltterExport;
 

class ExportController extends Controller
{
    public function exportStudentFiltter($params)
    {
        $decodedParams = base64_decode($params);
        $filters = json_decode($decodedParams, true) ?: [];

        return (new StudentFiltterExport($filters))->download('students_export_' . now()->format('Y-m-d_H-i') . '.xlsx');
    }
}
