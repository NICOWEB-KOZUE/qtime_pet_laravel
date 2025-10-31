<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService,
        private NotificationService $notificationService
    ) {}

    public function next(): RedirectResponse
    {
        $today = Carbon::today();

        $ticket = Ticket::whereDate('visit_date', $today)
            ->where('done', false)
            ->orderBy('seq_no')
            ->first();

        if ($ticket) {
            $ticket->update(['done' => true]);

            // 「あと2人」の患者に通知
            $this->notificationService->notifyIfTwoAhead();
        }

        return back();
    }

    public function undo(): RedirectResponse
    {
        $today = Carbon::today();

        $ticket = Ticket::whereDate('visit_date', $today)
            ->where('done', true)
            ->orderByDesc('seq_no')
            ->first();

        if ($ticket) {
            $ticket->update(['done' => false]);
        }

        return back();
    }

    public function manual(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $today = Carbon::today();

        Ticket::create([
            'name' => $validated['name'],
            'visit_date' => $today,
            'session' => $this->ticketService->currentSession(),
            'seq_no' => $this->ticketService->nextSeqNoForDay($today),
            'done' => false,
        ]);

        return back();
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');

        return redirect()->route('admin.login');
    }
}
