<?php

use Illuminate\Support\Facades\Route;
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
    CompanyController,
    DashboardController,
    MyPageController
};

use App\Http\Controllers\Decide\HiringDecisionController;
use App\Http\Controllers\ATS\JobRoleController;
use App\Http\Controllers\CMS\PageController;
use App\Http\Controllers\CMS\PublicPageController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| 認証必須：ATS / CMS 管理画面（JWT 前提）
|--------------------------------------------------------------------------
*/
Route::middleware('verify.jwt')->group(function () {

    Route::get('/', fn () => redirect()->route('jobs.index'));

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage');

    Route::resource('jobs', JobController::class)->except(['show']);
    Route::get('/jobs/{job}/pipeline', [PipelineController::class, 'show'])->name('jobs.pipeline');

    Route::get('/jobs/{job}/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/jobs/{job}/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::patch('/applications/{application}/step', [ApplicationStepController::class, 'update'])->name('applications.step.update');

    Route::get('/applications/{application}/status-test', [ApplicationController::class, 'updateStatus']);

    Route::post(
        '/applications/{application}/decision',
        [HiringDecisionController::class, 'store']
    )->name('applications.decision.store');

    Route::get('/applications/{application}/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/applications/{application}/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');

    Route::post('/jobs/{job}/share-token', function (Job $job) {
        $job->generateShareToken();
        return back();
    })->name('jobs.share-token.generate');

    Route::prefix('ats')->name('ats.')->group(function () {
        Route::get('/job-roles', [JobRoleController::class, 'index'])->name('job_roles.index');
        Route::get('/job-roles/create', [JobRoleController::class, 'create'])->name('job_roles.create');
        Route::post('/job-roles', [JobRoleController::class, 'store'])->name('job_roles.store');
        Route::get('/job-roles/{jobRole}/edit', [JobRoleController::class, 'edit'])->name('job_roles.edit');
        Route::patch('/job-roles/{jobRole}', [JobRoleController::class, 'update'])->name('job_roles.update');
    });

    Route::prefix('cms')->name('cms.')->group(function () {
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    });

    Route::get('/settings/company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::patch('/settings/company', [CompanyController::class, 'update'])->name('company.update');

    Route::view('/settings/account', 'settings.account')->name('settings.account');
    Route::view('/settings/billing', 'settings.billing')->name('settings.billing');

    Route::view('/announcements', 'static.announcements')->name('announcements');
    Route::view('/ai-policy', 'static.ai-policy')->name('ai.policy');
    Route::view('/data-policy', 'static.data-policy')->name('data.policy');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
});

/*
|--------------------------------------------------------------------------
| 公開ページ
|--------------------------------------------------------------------------
*/
Route::get('/job-pages/{slug}', [PublicPageController::class, 'show'])->name('cms.pages.public');

Route::get('/share/jobs/{job}/{token}', [SharePipelineController::class, 'show'])->name('jobs.pipeline.share');
Route::get('/share/applications/{application}/{token}', [ShareApplicationController::class, 'show'])->name('applications.share');

Route::view('/terms', 'static.terms')->name('terms');
Route::view('/company', 'static.company')->name('company');
Route::view('/privacy', 'static.privacy')->name('privacy');

Route::get('/__route_test', fn () => 'route ok');

/*
|--------------------------------------------------------------------------
| SSO callback（JWT を session に保存する）
|--------------------------------------------------------------------------
*/
Route::get('/sso/callback', function (Request $request) {
    $jwt = $request->query('token');

    if (! $jwt) {
        abort(401);
    }

    session(['jwt' => $jwt]);

    return redirect('/');
});