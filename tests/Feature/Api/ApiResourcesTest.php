<?php

use App\Models\Client;
use App\Models\User;

test('authenticated users can list their clients over the api', function () {
    $user = User::factory()->create();
    Client::factory()->for($user)->count(2)->create();
    Client::factory()->create();

    $this->actingAs($user);

    $response = $this->getJson(route('api.clients.index'));

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

test('authenticated users can create clients over the api', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->postJson(route('api.clients.store'), [
        'name' => 'API Client',
        'email' => 'api@example.test',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.name', 'API Client');
});
