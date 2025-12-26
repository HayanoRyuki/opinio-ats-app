<?php

namespace App\Http\Controllers;

use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        return Job::all(); // 仮。あとで直す
    }
}
