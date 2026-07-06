<?php

namespace App\Services;

use App\Models\User;

class InvoiceNumberGenerator
{
    public function nextForUser(User $user): string
    {
        $lastNumber = $user->invoices()
            ->latest('id')
            ->value('number');

        if (! $lastNumber) {
            return 'INV-0001';
        }

        $numericPart = (int) preg_replace('/\D+/', '', $lastNumber);

        return sprintf('INV-%04d', $numericPart + 1);
    }
}
