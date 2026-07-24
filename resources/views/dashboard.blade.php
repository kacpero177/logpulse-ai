<!DOCTYPE html>
<html lang="en" class="h-full bg-zinc-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogPulse AI — Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full bg-zinc-900 text-zinc-200 font-sans antialiased selection:bg-zinc-700">

    <div class="min-h-full">
        <!-- Top Navigation -->
        <header class="border-b border-zinc-700/60 bg-zinc-800/80">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-700/50 border border-zinc-600/50 rounded-lg text-zinc-100">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h1 class="text-base font-semibold text-zinc-100 tracking-tight">LogPulse AI</h1>
                        <p class="text-xs text-zinc-400">Log monitoring and analysis</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <form action="{{ route('dashboard.simulate') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white border border-indigo-500 px-3.5 py-1.5 rounded-md text-xs font-medium shadow-sm transition">
                            <i data-lucide="play" class="w-3.5 h-3.5"></i>
                            <span>Simulate Error</span>
                        </button>
                    </form>

                    <form action="{{ route('dashboard.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all logs?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 bg-zinc-700/40 hover:bg-rose-950/40 hover:text-rose-300 text-zinc-400 border border-zinc-700 hover:border-rose-800/50 px-3 py-1.5 rounded-md text-xs font-medium transition">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Clear</span>
                        </button>
                    </form>

                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-zinc-700/60 hover:bg-zinc-700 text-zinc-100 border border-zinc-600/80 px-3 py-1.5 rounded-md text-xs font-medium transition">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-zinc-300"></i>
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-8 space-y-6">

            <!-- Success Notification Alert -->
            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-4 py-3 rounded-lg text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Metrics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-zinc-800/90 border border-zinc-700/60 p-4 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-xs font-medium uppercase tracking-wider">Total logs</span>
                        <i data-lucide="layers" class="w-4 h-4 text-zinc-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-zinc-100 mt-2">{{ $stats['total'] }}</p>
                </div>

                <div class="bg-zinc-800/90 border border-zinc-700/60 p-4 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-xs font-medium uppercase tracking-wider text-rose-400">Critical</span>
                        <i data-lucide="alert-octagon" class="w-4 h-4 text-rose-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-rose-400 mt-2">{{ $stats['critical'] }}</p>
                </div>

                <div class="bg-zinc-800/90 border border-zinc-700/60 p-4 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-xs font-medium uppercase tracking-wider text-amber-400">Errors</span>
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-amber-400 mt-2">{{ $stats['errors'] }}</p>
                </div>

                <div class="bg-zinc-800/90 border border-zinc-700/60 p-4 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-xs font-medium uppercase tracking-wider text-emerald-400">Analyzed</span>
                        <i data-lucide="sparkles" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-emerald-400 mt-2">{{ $stats['analyzed'] }}</p>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="bg-zinc-800/90 border border-zinc-700/60 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-700/60 bg-zinc-800 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-zinc-200">Recent events</h2>
                    <span class="text-xs text-zinc-400">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-800/50 text-zinc-400 text-xs font-medium border-b border-zinc-700/60 uppercase tracking-wider">
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Service</th>
                                <th class="py-3 px-4">Level</th>
                                <th class="py-3 px-4">Message</th>
                                <th class="py-3 px-4">AI Analysis</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-700/50 text-xs">
                            @forelse($logs as $log)
                                <tr class="hover:bg-zinc-700/30 transition">
                                    <td class="py-3.5 px-4 font-mono text-zinc-400">#{{ $log->id }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-zinc-100">{{ $log->service_name }}</td>
                                    <td class="py-3.5 px-4">
                                        @if($log->level === 'critical')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30 uppercase">
                                                critical
                                            </span>
                                        @elseif($log->level === 'error')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 uppercase">
                                                error
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-700 text-zinc-300 border border-zinc-600 uppercase">
                                                {{ $log->level }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 max-w-xs font-mono text-zinc-300 truncate" title="{{ $log->message }}">
                                        {{ $log->message }}
                                    </td>
                                    <td class="py-3.5 px-4 max-w-md">
                                        @if($log->ai_summary)
                                            <div class="bg-zinc-900/60 p-3 rounded-lg border border-zinc-700/70 text-zinc-200 leading-relaxed font-sans text-xs space-y-1">
                                                {!! Str::markdown($log->ai_summary) !!}
                                            </div>
                                        @else
                                            <span class="text-zinc-500 italic">Pending analysis...</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($log->status === 'analyzed')
                                            <span class="inline-flex items-center gap-1.5 text-emerald-300 font-medium bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                Analyzed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-amber-300 font-medium bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-full text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-zinc-400 font-mono whitespace-nowrap">
                                        {{ $log->created_at->format('H:i:s d.m.Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-zinc-400">
                                        No logs recorded in the database. Click "Simulate Error" above!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="px-6 py-3 border-t border-zinc-700/60 bg-zinc-800">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>