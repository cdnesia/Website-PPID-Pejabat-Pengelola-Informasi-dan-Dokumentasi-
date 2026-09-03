<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): array
    {
        $from = $request->date('from') ?? now()->startOfYear();
        $to = $request->date('to') ?? now();

        $byStatus = InformationRequest::query()
            ->whereBetween('created_at', [$from, $to])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $byFormat = InformationRequest::query()
            ->whereBetween('created_at', [$from, $to])
            ->select('format_requested', DB::raw('count(*) as total'))
            ->groupBy('format_requested')
            ->pluck('total', 'format_requested');

        return [
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'by_status' => $byStatus,
            'by_format' => $byFormat,
        ];
    }
}
