<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceStatusManager;
use Illuminate\Contracts\View\View;

class PublicInvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceStatusManager $statusManager,
    ) {
    }

    public function __invoke(Invoice $invoice): View
    {
        $invoice->load(['client', 'items', 'payments']);

        if ($invoice->viewed_at === null) {
            $invoice->forceFill(['viewed_at' => now()])->save();
        }

        $this->statusManager->sync($invoice);

        return view('invoices.public', ['invoice' => $invoice]);
    }
}
