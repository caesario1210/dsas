<?php

namespace App\Http\Controllers;

use App\Services\InsightService;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    private InsightService $insightService;

    public function __construct(InsightService $insightService)
    {
        $this->insightService = $insightService;
    }

    public function index(Request $request)
    {
        $year = $request->input('year');

        return response()->json([
            'status' => 'success',
            'data' => $this->insightService->generate($year ? (int) $year : null),
        ]);
    }
}
