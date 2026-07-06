<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceStatusManager;
use Illuminate\Http\RedirectResponse;

class InvoiceDispatchController extends Controller
{
    public function __construct(
        private readonly InvoiceStatusManager $statusManager,
    ) {
    }

    public function __invoke(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        $invoice->sent_at ??= now();
        $this->statusManager->sync($invoice);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', 'Invoice marked as sent.');
    }
}
