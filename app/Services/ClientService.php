<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    /**
     * Create a new client.
     *
     * @param  array  $data
     * @return Client
     */
    public function create(array $data): Client
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return Client::create($data);
    }

    /**
     * Update an existing client.
     *
     * @param  Client  $client
     * @param  array  $data
     * @return Client
     */
    public function update(Client $client, array $data): Client
    {
        $data['updated_by'] = Auth::id();

        $client->update($data);

        return $client;
    }

    /**
     * Delete (soft delete) a client.
     *
     * @param  Client  $client
     * @return bool
     */
    public function delete(Client $client): bool
    {
        return $client->delete();
    }
}