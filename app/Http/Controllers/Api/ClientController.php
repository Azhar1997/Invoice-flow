<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Client::class);

        return ClientResource::collection(
            auth()->user()->clients()->withCount('invoices')->latest()->paginate(10)
        );
    }

    public function store(StoreClientRequest $request): ClientResource
    {
        $this->authorize('create', Client::class);

        return ClientResource::make(
            auth()->user()->clients()->create($request->validated())
        );
    }

    public function show(Client $client): ClientResource
    {
        $this->authorize('view', $client);

        return ClientResource::make($client->loadCount('invoices'));
    }

    public function update(UpdateClientRequest $request, Client $client): ClientResource
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return ClientResource::make($client->fresh()->loadCount('invoices'));
    }

    public function destroy(Client $client): Response
    {
        $this->authorize('delete', $client);

        abort_if($client->invoices()->exists(), 422, 'Delete or reassign invoices before deleting this client.');

        $client->delete();

        return response()->noContent();
    }
}
