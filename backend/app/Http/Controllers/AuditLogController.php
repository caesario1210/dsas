<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    private AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 50);

        return response()->json([
            'status' => 'success',
            'data' => $this->auditService->getAll((int) $limit),
        ]);
    }
}
