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
}
