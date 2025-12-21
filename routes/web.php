<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Job;

use App\Http\Controllers\{
    JobController,
    PipelineController,
    SharePipelineController,
    ShareApplicationController,
    ApplicationStepController,
    ApplicationController,
    EvaluationController,
    RecruiterController,
    CompanyController
};

use App\Http\Controllers\ATS\JobRoleController;
use App\Http\Controllers\CMS\PageController;
use App\Http\Controllers\CMS\PublicPageController;

/*
|--------------------------------------------------------------------------
| Top
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('jobs.index'));

/*
|--------------------------------------------------------------------------
| 認証（ログイン / ログアウト）
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => view('auth.login'))->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended(route('jobs.index'));
    }

    return back()->withErrors([
        'email' => 'メールアドレスまたはパスワードが違います。',
    ]);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| 認証必須：ATS / CMS 管理画面
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    | ダッシュボード / マイページ
    */
    Route::get('/dashboard', [RecruiterController::class, 'mypage'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ATS：求人管理
    |--------------------------------------------------------------------------
    */

    // 求人 CRUD（show は使わない）
    Route::resource('jobs', JobController::class)
        ->except(['show']);

    // 求人 Pipeline（実質 show）
    Route::get('/jobs/{job}/pipeline', [PipelineController::class, 'show'])
        ->name('jobs.pipeline');

    /*
    | 応募者
    */
    Route::get('/jobs/{job}/applications/create', [ApplicationController::class, 'create'])
        ->name('applications.create');

    Route::post('/jobs/{job}/applications', [ApplicationController::class, 'store'])
        ->name('applications.store');

    Route::patch('/applications/{application}/step', [ApplicationStepController::class, 'update'])
        ->name('applications.step.update');

    /*
    | 評価
    */
    Route::get('/applications/{application}/evaluations/create', [EvaluationController::class, 'create'])
        ->name('evaluations.create');

    Route::post('/applications/{application}/evaluations', [EvaluationController::class, 'store'])
        ->name('evaluations.store');

    /*
    | 共有トークン生成
    */
    Route::post('/jobs/{job}/share-token', function (Job $job) {
        $job->generateShareToken();
        return back();
    })->name('jobs.share-token.generate');

    /*
    |--------------------------------------------------------------------------
    | ATS：職種管理
    |--------------------------------------------------------------------------
    */
    Route::prefix('ats')->name('ats.')->group(function () {

        Route::get('/job-roles', [JobRoleController::class, 'index'])
            ->name('job_roles.index');

        Route::get('/job-roles/create', [JobRoleController::class, 'create'])
            ->name('job_roles.create');

        Route::post('/job-roles', [JobRoleController::class, 'store'])
            ->name('job_roles.store');

        Route::get('/job-roles/{jobRole}/edit', [JobRoleController::class, 'edit'])
            ->name('job_roles.edit');

        Route::patch('/job-roles/{jobRole}', [JobRoleController::class, 'update'])
            ->name('job_roles.update');
    });

/*
|--------------------------------------------------------------------------
| CMS：ページ管理
|--------------------------------------------------------------------------
*/
Route::prefix('cms')->name('cms.')->group(function () {

    Route::get('/pages', [PageController::class, 'index'])
        ->name('pages.index');

    Route::get('/pages/create', [PageController::class, 'create'])
        ->name('pages.create');

    Route::post('/pages', [PageController::class, 'store'])
        ->name('pages.store');

    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])
        ->name('pages.edit');

    Route::put('/pages/{page}', [PageController::class, 'update'])
        ->name('pages.update');

    Route::delete('/pages/{page}', [PageController::class, 'destroy'])
        ->name('pages.destroy');
});

Route::get('/job-pages/{slug}', [PublicPageController::class, 'show'])
    ->name('cms.pages.public');

    /*
    |--------------------------------------------------------------------------
    | 設定・情報
    |--------------------------------------------------------------------------
    */
    Route::get('/settings/company', [CompanyController::class, 'edit'])
        ->name('company.edit');

    Route::patch('/settings/company', [CompanyController::class, 'update'])
        ->name('company.update');

    Route::view('/settings/account', 'settings.account')
        ->name('settings.account');

    Route::view('/settings/billing', 'settings.billing')
        ->name('settings.billing');

    Route::view('/announcements', 'static.announcements')
        ->name('announcements');

    Route::view('/ai-policy', 'static.ai-policy')
        ->name('ai.policy');

    Route::view('/data-policy', 'static.data-policy')
        ->name('data.policy');
});

/*
|--------------------------------------------------------------------------
| 共有（ログイン不要・readonly）
|--------------------------------------------------------------------------
*/
Route::get('/share/jobs/{job}/{token}', [SharePipelineController::class, 'show'])
    ->name('jobs.pipeline.share');

Route::get('/share/applications/{application}/{token}', [ShareApplicationController::class, 'show'])
    ->name('applications.share');

/*
|--------------------------------------------------------------------------
| 法務・会社情報（ログイン不要）
|--------------------------------------------------------------------------
*/
Route::view('/terms', 'static.terms')->name('terms');
Route::view('/company', 'static.company')->name('company');
Route::view('/privacy', 'static.privacy')->name('privacy');

/*
|--------------------------------------------------------------------------
| Debug
|--------------------------------------------------------------------------
*/
Route::get('/__route_test', fn () => 'route ok');

Route::middleware('auth')->get(
    '/applications/{application}/status-test',
    [ApplicationController::class, 'updateStatus']
);