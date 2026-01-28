<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = Application::with(['candidate', 'job'])
            ->latest()
            ->get();

        return Inertia::render('Applications/Index', [
            'applications' => $applications,
        ]);
    }

    public function show(Request $request, $id)
    {
        $application = Application::with(['candidate', 'job'])
            ->findOrFail($id);

        return Inertia::render('Applications/Show', [
            'application' => $application,
        ]);
    }
}
