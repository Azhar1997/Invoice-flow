<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Services\InvoiceCalculator;
use App\Services\InvoiceStatusManager;
use App\Support\Money;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(
        private readonly InvoiceCalculator $calculator,
        private readonly InvoiceStatusManager $statusManager,
    ) {
    }

    public function index(): View
    {
        return view('payments.index', [
            'payments' => auth()->user()
                ->payments()
                ->with('invoice.client')
                ->latest('paid_at')
                ->paginate(10),
        ]);
    }

    public function store(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        DB::transaction(function () use ($request, $invoice): void {
            $amount = Money::toMinor($request->validated('amount'));

            $invoice->payments()->create([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'paid_at' => $request->validated('paid_at'),
                'method' => $request->validated('method'),
                'reference' => $request->validated('reference'),
                'notes' => $request->validated('notes'),
            ]);

            $invoice->load('items');
            $recalculated = $this->calculator->calculateForInvoice(
                $invoice,
                $invoice->amount_paid + $amount,
            );

            $invoice->update([
                'amount_paid' => $invoice->amount_paid + $amount,
                'balance_due_amount' => $recalculated['balance_due_amount'],
            ]);

            $this->statusManager->sync($invoice);
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', 'Payment recorded successfully.');
    }
}
