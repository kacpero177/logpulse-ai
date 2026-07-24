<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeLogWithAi;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'level'        => 'required|in:info,warning,error,critical',
            'message'      => 'required|string',
            'stack_trace'  => 'nullable|string',
        ]);

        $log = Log::create([
            'service_name' => $validated['service_name'],
            'level'        => $validated['level'],
            'message'      => $validated['message'],
            'stack_trace'  => $validated['stack_trace'] ?? null,
            'status'       => 'pending',
        ]);

        // Przekazanie analizy do Joba
        AnalyzeLogWithAi::dispatch($log);

        return response()->json([
            'success' => true,
            'message' => 'Log received and queued for AI analysis.',
            'log_id'  => $log->id,
        ], 202);
    }

    public function index(): JsonResponse
    {
        $logs = Log::latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $logs
        ]);
    }

    public function show(Log $log): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $log
        ]);
    }
}