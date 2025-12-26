<?php

namespace App\Http\Controllers;

use App\Domain\Learn\DashboardRepository;
use Illuminate\Http\JsonResponse;

final class DashboardController extends Controller
{
    public function __construct(
        private DashboardRepository $repository
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'kpi'     => $this->repository->getKpiSnapshot(),
            'funnel'  => $this->repository->getHiringFunnel(),
        ]);
    }
}
