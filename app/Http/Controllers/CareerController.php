<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Person;
use App\Models\Candidate;
use App\Models\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CareerController extends Controller
{
    /**
     * 公開キャリアページ表示（認証不要）
     */
    public function show(string $slug): Response
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->with('job')
            ->firstOrFail();

        return Inertia::render('Career/Show', [
            'page' => $page,
            'job' => $page->job,
        ]);
    }

    /**
     * 応募フォーム送信（認証不要）
     */
    public function apply(Request $request, string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->with('job')
            ->firstOrFail();

        $job = $page->job;

        if (!$job || $job->status !== 'open') {
            return back()->withErrors(['job' => 'この求人は現在募集していません。']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        // Person を取得 or 作成
        $person = Person::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
            ]
        );

        // Candidate を取得 or 作成
        $candidate = Candidate::firstOrCreate(
            [
                'company_id' => $job->company_id,
                'person_id' => $person->id,
            ],
            [
                'source_channel' => 'direct',
                'source_detail' => 'career_page:' . $page->slug,
            ]
        );

        // Application を作成（重複チェック）
        $existingApplication = Application::where('candidate_id', $candidate->id)
            ->where('job_id', $job->id)
            ->first();

        if ($existingApplication) {
            return back()->withErrors(['duplicate' => 'この求人にはすでに応募済みです。']);
        }

        Application::create([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'status' => 'active',
            'applied_at' => now(),
            'metadata' => [
                'source' => 'career_page',
                'page_slug' => $page->slug,
                'message' => $validated['message'] ?? null,
            ],
        ]);

        return back()->with('success', '応募を受け付けました。ありがとうございます。');
    }
}
