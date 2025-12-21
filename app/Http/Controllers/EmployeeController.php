<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * 入社者一覧（ダミー）
     */
    public function index()
    {
        $employees = Employee::limit(10)->get();

        return view('employees.index', compact('employees'));
    }
}
