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
        $companyId = $request->attributes->get('company_id');

        $intakes = ApplicationIntake::where('company_id', $companyId)
            ->with('draft')
            ->orderBy('received_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => ApplicationIntake::where('company_id', $companyId)
                ->where('status', 'pending')
                ->count(),
            'draft' => ApplicationIntake::where('company_id', $companyId)
                ->where('status', 'draft')
                ->count(),
            'confirmed' => ApplicationIntake::where('company_id', $companyId)
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
        $companyId = $request->attributes->get('company_id');
        $channel = $request->query('channel');
        $sort = $request->query('sort', 'newest'); // newest, oldest

        $query = IntakeCandidateDraft::whereHas('applicationIntake', function ($q) use ($companyId, $channel) {
            $q->where('company_id', $companyId);
            if ($channel) {
                $q->where('channel', $channel);
            }
        })
            ->with(['applicationIntake'])
            ->where('status', 'draft');

        // ソート
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $drafts = $query->paginate(20)->withQueryString();

        // チャネル別の件数を取得
        $channelCounts = IntakeCandidateDraft::whereHas('applicationIntake', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
            ->where('status', 'draft')
            ->join('application_intakes', 'intake_candidate_drafts.intake_id', '=', 'application_intakes.id')
            ->selectRaw('application_intakes.channel, count(*) as count')
            ->groupBy('application_intakes.channel')
            ->pluck('count', 'channel')
            ->toArray();

        return Inertia::render('Intake/Drafts', [
            'drafts' => $drafts,
            'filters' => [
                'channel' => $channel,
                'sort' => $sort,
            ],
            'channelCounts' => $channelCounts,
        ]);
    }

    /**
     * ドラフト詳細・確認画面
     */
    public function draftDetail(Request $request, $id): Response
    {
        $companyId = $request->attributes->get('company_id');
        $draft = IntakeCandidateDraft::findOrFail($id);
        $draft->load(['applicationIntake', 'matchedPerson', 'matchedCandidate']);

        // 重複候補を検索
        $duplicates = $this->confirmService->findDuplicates($draft, $companyId);

        return Inertia::render('Intake/DraftDetail', [
            'draft' => $draft,
            'duplicates' => $duplicates,
        ]);
    }

    /**
     * ドラフトを確定（SoTへ昇格）
     *
     * 仮応募（is_preliminary = true）の場合はエラーになる。
     * その場合は confirmAndPromoteDraft を使用すること。
     */
    public function confirmDraft(Request $request, IntakeCandidateDraft $draft)
    {
        $companyId = $request->attributes->get('company_id');
        $userId = $request->attributes->get('auth_user_id');

        // 簡易的なユーザーオブジェクトを作成
        $user = (object) [
            'id' => $userId,
            'company_id' => $companyId,
        ];

        try {
            $result = $this->confirmService->confirm($draft, $user);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }

        $message = "候補者「{$result['candidate']->person->name}」を登録しました。";
        if ($result['application']) {
            $message .= '（応募も作成）';
        }

        return redirect()->route('intake.drafts')
            ->with('success', $message);
    }

    /**
     * 仮応募を正式応募に昇格してから確定
     *
     * スカウト反応などの仮応募を、面談確定後に正式候補者として登録する場合に使用。
     */
    public function confirmAndPromoteDraft(Request $request, IntakeCandidateDraft $draft)
    {
        $companyId = $request->attributes->get('company_id');
        $userId = $request->attributes->get('auth_user_id');

        $user = (object) [
            'id' => $userId,
            'company_id' => $companyId,
        ];

        $result = $this->confirmService->confirmAndPromote($draft, $user);

        $message = "仮応募を正式応募に昇格し、候補者「{$result['candidate']->person->name}」を登録しました。";
        if ($result['application']) {
            $message .= '（応募も作成）';
        }

        return redirect()->route('intake.drafts')
            ->with('success', $message);
    }

    /**
     * 仮応募を正式応募に昇格のみ（候補者登録はしない）
     */
    public function promoteDraft(Request $request, IntakeCandidateDraft $draft)
    {
        try {
            $this->confirmService->promote($draft);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }

        return redirect()->back()
            ->with('success', '仮応募を正式応募に昇格しました。引き続き確認・登録を行ってください。');
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
