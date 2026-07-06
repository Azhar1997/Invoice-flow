<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $revenueThisMonth = (int) Payment::query()
            ->where('user_id', $user->id)
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $outstandingInvoices = Invoice::query()
            ->where('user_id', $user->id)
            ->where('balance_due_amount', '>', 0)
            ->count();

        $overdueInvoices = Invoice::query()
            ->where('user_id', $user->id)
            ->where('balance_due_amount', '>', 0)
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        $recentInvoices = Invoice::query()
            ->with('client')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::query()
            ->with('invoice.client')
            ->where('user_id', $user->id)
            ->latest('paid_at')
            ->take(5)
            ->get();

        return view('dashboard', [
            'revenueThisMonth' => Money::formatMinor($revenueThisMonth),
            'outstandingInvoices' => $outstandingInvoices,
            'overdueInvoices' => $overdueInvoices,
            'recentInvoices' => $recentInvoices,
            'recentPayments' => $recentPayments,
        ]);
    }
}
