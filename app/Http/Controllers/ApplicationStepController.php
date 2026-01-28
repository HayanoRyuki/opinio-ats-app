<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\SelectionStep;

class ApplicationStepController extends Controller
{
    /**
     * 応募の選考ステップを更新（AJAX）
     */
    public function update(Request $request, Application $application)
    {
        // JWT middleware から company_id
        $companyId = $request->attributes->get('company_id');

        if (! $companyId) {
            abort(403, 'company_id not found');
        }

        // application が同一 company か
        if ($application->company_id !== $companyId) {
            abort(403, 'invalid company');
        }

        $request->validate([
            'selection_step_id' => ['required', 'integer'],
        ]);

        // 選考ステップが同一 company か
        $step = SelectionStep::where('id', $request->selection_step_id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $application->selection_step_id = $step->id;
        $application->save();

        return response()->json([
            'status' => 'ok',
            'application_id' => $application->id,
            'selection_step_id' => $step->id,
        ]);
    }
}
