<?php

use App\Models\Client;
use App\Models\User;

test('authenticated users can create and update clients', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->post(route('clients.store'), [
        'name' => 'Acme Studio',
        'company_name' => 'Acme Studio LLC',
        'email' => 'billing@acme.test',
        'phone' => '+31 555 1234',
        'billing_address_line_1' => 'Example Street 10',
        'billing_city' => 'Amsterdam',
        'billing_country' => 'Netherlands',
        'notes' => 'Priority client',
    ])->assertRedirect();

    $client = Client::first();

    expect($client)->not->toBeNull();
    expect($client->name)->toBe('Acme Studio');

    $this->put(route('clients.update', $client), [
        'name' => 'Acme Studio Updated',
        'company_name' => 'Acme Studio LLC',
        'email' => 'billing@acme.test',
        'phone' => '+31 555 1234',
        'billing_address_line_1' => 'Example Street 10',
        'billing_city' => 'Amsterdam',
        'billing_country' => 'Netherlands',
        'notes' => 'Updated note',
    ])->assertRedirect(route('clients.show', $client));

    expect($client->fresh()->name)->toBe('Acme Studio Updated');
});
