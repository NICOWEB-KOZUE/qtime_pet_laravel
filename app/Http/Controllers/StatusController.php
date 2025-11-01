<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    public function json(): JsonResponse
    {
        $today = Carbon::today();

        $queue = Ticket::whereDate('visit_date', $today)
            ->where('done', false)
            ->orderBy('seq_no')
            ->get(['id', 'seq_no', 'notified']);

        $nowServing = Ticket::whereDate('visit_date', $today)
            ->where('done', true)
            ->orderByDesc('seq_no')
            ->first(['id', 'seq_no']);

        return response()->json([
            'now_serving' => $nowServing ? [
                'id' => $nowServing->id,
                'seq_no' => $nowServing->seq_no,
            ] : null,
            'queue' => $queue->map(fn($ticket) => [
                'id' => $ticket->id,
                'seq_no' => $ticket->seq_no,
                'notified' => $ticket->notified,
            ]),
        ]);
    }
}
