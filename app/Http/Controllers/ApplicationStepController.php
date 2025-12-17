<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationStepController extends Controller
{
    public function update(Request $request, Application $application)
    {
        $request->validate([
            'selection_step_id' => 'required|exists:selection_steps,id',
        ]);

        $application->update([
            'selection_step_id' => $request->selection_step_id,
        ]);

        return back();
    }
}
