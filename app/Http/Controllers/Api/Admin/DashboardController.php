<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformationRequest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): array
    {
        $statusCounts = InformationRequest::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthExpression = match (DB::connection()->getDriverName()) {
            'mysql' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
            default => "strftime('%Y-%m', created_at)",
        };

        $monthlyTrend = InformationRequest::query()
            ->selectRaw("{$monthExpression} as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $nearingDueDate = InformationRequest::query()
            ->with('user')
            ->whereNotIn('status', ['answered', 'rejected'])
            ->whereDate('due_date', '<=', now()->addDays(2))
            ->orderBy('due_date')
            ->limit(10)
            ->get(['id', 'request_number', 'user_id', 'status', 'due_date']);

        $recentRequests = InformationRequest::query()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get(['id', 'request_number', 'user_id', 'status', 'created_at']);

        return [
            'summary' => [
                'total' => $statusCounts->sum(),
                'submitted' => $statusCounts->get('submitted', 0),
                'in_review' => $statusCounts->get('in_review', 0),
                'in_process' => $statusCounts->get('in_process', 0),
                'answered' => $statusCounts->get('answered', 0),
                'rejected' => $statusCounts->get('rejected', 0),
            ],
            'monthly_trend' => $monthlyTrend,
            'nearing_due_date' => $nearingDueDate,
            'recent_requests' => $recentRequests,
        ];
    }
}
