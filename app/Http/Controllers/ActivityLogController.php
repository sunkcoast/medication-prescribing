<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Mengambil log terbaru dengan relasi user (siapa yang beraksi)
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(15);

        return view('doctor.logs', compact('logs'));
    }
}