<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Client::class);

        return view('clients.index', [
            'clients' => auth()->user()
                ->clients()
                ->withCount('invoices')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Client::class);

        return view('clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $this->authorize('create', Client::class);

        $client = auth()->user()->clients()->create($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('status', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        $this->authorize('view', $client);

        return view('clients.show', [
            'client' => $client,
            'recentInvoices' => $client->invoices()->latest()->take(5)->get(),
        ]);
    }

    public function edit(Client $client): View
    {
        $this->authorize('update', $client);

        return view('clients.edit', ['client' => $client]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('status', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        if ($client->invoices()->exists()) {
            return back()->withErrors([
                'client' => 'Delete or reassign this client\'s invoices before removing the client.',
            ]);
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('status', 'Client deleted successfully.');
    }
}
