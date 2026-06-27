<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\SlaPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;

        // 1. Counts by Status
        $statusCounts = Ticket::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses are initialized
        $allStatuses = ['open', 'pending', 'on_hold', 'resolved', 'closed'];
        foreach ($allStatuses as $status) {
            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }
        }

        // 2. Counts by Priority
        $priorityCounts = Ticket::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority')
            ->toArray();

        $allPriorities = ['low', 'medium', 'high', 'urgent'];
        foreach ($allPriorities as $priority) {
            if (!isset($priorityCounts[$priority])) {
                $priorityCounts[$priority] = 0;
            }
        }

        // 3. Average Resolution Time (in minutes)
        $resolvedTickets = Ticket::whereNotNull('resolved_at')->get();
        
        $totalMinutes = 0;
        $resolvedCount = $resolvedTickets->count();

        foreach ($resolvedTickets as $ticket) {
            $createdAt = Carbon::parse($ticket->created_at);
            $resolvedAt = Carbon::parse($ticket->resolved_at);
            $totalMinutes += $createdAt->diffInMinutes($resolvedAt);
        }

        $avgResolutionMinutes = $resolvedCount > 0 ? round($totalMinutes / $resolvedCount, 2) : 0.0;

        // 4. SLA Breach Rate
        $policies = SlaPolicy::where('organization_id', $orgId)
            ->get()
            ->keyBy('priority');

        $tickets = Ticket::all();
        $totalEvaluated = 0;
        $breachedCount = 0;

        foreach ($tickets as $ticket) {
            $policy = $policies->get($ticket->priority);
            if ($policy) {
                $totalEvaluated++;
                $createdAt = Carbon::parse($ticket->created_at);
                $resolvedAt = $ticket->resolved_at ? Carbon::parse($ticket->resolved_at) : Carbon::now();

                $durationMinutes = $createdAt->diffInMinutes($resolvedAt);

                if ($durationMinutes > $policy->resolution_minutes) {
                    $breachedCount++;
                }
            }
        }

        $breachRatePercent = $totalEvaluated > 0 ? round(($breachedCount / $totalEvaluated) * 100, 2) : 0.0;

        return response()->json([
            'data' => [
                'counts_by_status' => $statusCounts,
                'counts_by_priority' => $priorityCounts,
                'average_resolution_minutes' => $avgResolutionMinutes,
                'sla' => [
                    'breached_count' => $breachedCount,
                    'total_evaluated' => $totalEvaluated,
                    'breach_rate_percent' => $breachRatePercent,
                ]
            ]
        ]);
    }
}
