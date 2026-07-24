<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Jobs\AnalyzeLogWithAi; // <-- Correct Job class name!
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Log::count(),
            'critical' => Log::where('level', 'critical')->count(),
            'errors' => Log::where('level', 'error')->count(),
            'analyzed' => Log::where('status', 'analyzed')->count(),
        ];

        $logs = Log::latest()->paginate(10);

        return view('dashboard', compact('stats', 'logs'));
    }

    public function simulateError()
    {
        $sampleErrors = [
            'Database connection timeout during user login query',
            'Insufficient funds for account transfer operation in PaymentGateway',
            'Stripe webhook signature validation failed - invalid API key',
            'Redis cache service unreachable on port 6379',
        ];

        $randomMessage = $sampleErrors[array_rand($sampleErrors)];
        $level = rand(0, 1) ? 'critical' : 'error';

        // 1. Create a log in the database
        $log = Log::create([
            'service_name' => 'TestService',
            'level' => $level,
            'message' => $randomMessage,
            'status' => 'pending',
        ]);

        // 2. Dispatch the job to the queue!
        AnalyzeLogWithAi::dispatch($log);

        return redirect()->route('dashboard')->with('success', 'Simulated error has been sent for AI analysis!');
    }

    public function clearLogs()
    {
        Log::truncate();
        return redirect()->route('dashboard')->with('success', 'Log database has been cleared!');
    }
}