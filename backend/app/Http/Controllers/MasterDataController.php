<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Dealer;
use App\Models\Product;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterDataController extends Controller
{
    private AuditService $auditService;
    private array $models = [];

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
        $this->models = [
            'dealers' => Dealer::class,
            'products' => Product::class,
            'branches' => Branch::class,
        ];
    }

    private function resolveModelFromUrl(Request $request): ?string
    {
        $segment = $request->segment(3);
        $map = ['dealers' => 'dealers', 'products' => 'products', 'branches' => 'branches'];
        return $map[$segment] ?? null;
    }

    private function resolveModel(string $type)
    {
        return $this->models[$type] ?? null;
    }

    public function index(Request $request)
    {
        $type = $this->resolveModelFromUrl($request);
        if (!$type) return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);

        $modelClass = $this->resolveModel($type);

        return response()->json([
            'status' => 'success',
            'data' => $modelClass::orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $type = $this->resolveModelFromUrl($request);
        if (!$type) return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);

        $modelClass = $this->resolveModel($type);
        if (!$modelClass) {
            return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);
        }

        $data = $request->all();
        $item = $modelClass::create($data);

        $this->auditService->log("create.{$type}", $type, $item->id, "Created {$type}: #{$item->id}", null, $data, $request);

        return response()->json(['status' => 'success', 'data' => $item], 201);
    }

    public function update(Request $request, int $id)
    {
        $type = $this->resolveModelFromUrl($request);
        if (!$type) return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);

        $modelClass = $this->resolveModel($type);
        if (!$modelClass) {
            return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);
        }

        $item = $modelClass::findOrFail($id);
        $old = $item->toArray();
        $item->update($request->all());

        $this->auditService->log("update.{$type}", $type, $id, "Updated {$type}: #{$id}", $old, $item->toArray(), $request);

        return response()->json(['status' => 'success', 'data' => $item]);
    }

    public function destroy(Request $request, int $id)
    {
        $type = $this->resolveModelFromUrl($request);
        if (!$type) return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);

        $modelClass = $this->resolveModel($type);
        if (!$modelClass) {
            return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);
        }

        $item = $modelClass::findOrFail($id);
        $item->delete();

        $this->auditService->log("delete.{$type}", $type, $id, "Deleted {$type}: #{$id}", $item->toArray(), null, $request);

        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}
