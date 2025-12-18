<?php

namespace App\Http\Controllers\ATS;

use App\Http\Controllers\Controller;
use App\Models\JobRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class JobRoleController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company; // 既存の前提に合わせてOK

        $jobRoles = JobRole::where('company_id', $company->id)
            ->ordered()
            ->get();

        return view('ats.job_roles.index', compact('jobRoles'));
    }

    public function create()
{
    return view('ats.job_roles.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'display_name'  => 'required|string|max:255',
        'internal_name' => 'nullable|string|max:255',
        'description'   => 'nullable|string',
    ]);

    $company = Auth::user()->company;

    JobRole::create([
        'company_id'    => $company->id,
        'display_name'  => $validated['display_name'],
        'internal_name' => $validated['internal_name']
            ?? $validated['display_name'],
        'description'   => $validated['description'] ?? null,
        'sort_order'    => JobRole::where('company_id', $company->id)->count(),
        'is_active'     => true,
    ]);

    return redirect()
        ->route('ats.job_roles.index')
        ->with('status', '職種を追加しました');
}
}
