<?php

namespace App\Http\Controllers;

use App\Models\Orphan;
use App\Models\Setting;

class OrphanReportController extends Controller
{
    public function generateReport()
    {
        $records = Orphan::with(['sponsorships.sponsor', 'branch'])->get();

        return view('reports.group-orphans', compact('records'));
    }
}
