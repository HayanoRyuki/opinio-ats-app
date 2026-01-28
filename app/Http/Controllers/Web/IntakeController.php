<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApplicationIntake;
use App\Models\IntakeCandidateDraft;
use App\Services\IntakeConfirmService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntakeController extends Controller
{
    public function __construct(
        private IntakeConfirmService $confirmService
    ) {}

    /**
     * 取り込み一覧画面
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $intakes = ApplicationIntake::where('company_id', $user->company_id)
            ->with('draft')
            ->orderBy('received_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => ApplicationIntake::where('company_id', $user->company_id)
                ->where('status', 'pending')
                ->count(),
            'draft' => ApplicationIntake::where('company_id', $user->company_id)
                ->where('status', 'draft')
                ->count(),
            'confirmed' => ApplicationIntake::where('company_id', $user->company_id)
                ->where('status', 'confirmed')
                ->count(),
        ];

        return Inertia::render('Intake/Index', [
            'intakes' => $intakes,
            'stats' => $stats,
        ]);
    }

    /**
     * 候補者ドラフト一覧
     */
    public function drafts(Request $request): Response
    {
        $user = $request->user();

        $drafts = IntakeCandidateDraft::whereHas('applicationIntake', function ($query) use ($user) {
            $query->where('company_id', $user->company_id);
        })
            ->with(['applicationIntake'])
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Intake/Drafts', [
            'drafts' => $drafts,
        ]);
    }

    /**
     * ドラフト詳細・確認画面
     */
    public function showDraft(Request $request, IntakeCandidateDraft $draft): Response
    {
        $user = $request->user();
        $draft->load(['applicationIntake', 'matchedPerson', 'matchedCandidate']);

        // 重複候補を検索
        $duplicates = $this->confirmService->findDuplicates($draft, $user->company_id);

        return Inertia::render('Intake/DraftDetail', [
            'draft' => $draft,
            'duplicates' => $duplicates,
        ]);
    }

    /**
     * ドラフトを確定（SoTへ昇格）
     */
    public function confirmDraft(Request $request, IntakeCandidateDraft $draft)
    {
        $user = $request->user();

        $result = $this->confirmService->confirm($draft, $user);

        $message = "候補者「{$result['candidate']->person->name}」を登録しました。";
        if ($result['application']) {
            $message .= '（応募も作成）';
        }

        return redirect()->route('intake.drafts')
            ->with('success', $message);
    }

    /**
     * ドラフトを却下
     */
    public function rejectDraft(Request $request, IntakeCandidateDraft $draft)
    {
        $this->confirmService->reject($draft);

        return redirect()->route('intake.drafts')
            ->with('success', '候補者を却下しました。');
    }
}
