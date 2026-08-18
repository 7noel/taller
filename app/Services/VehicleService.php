<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleContact;
use Illuminate\Support\Facades\Auth;

class VehicleService
{
    /**
     * Create a new vehicle with optional contacts.
     *
     * @param  array  $data
     * @return Vehicle
     */
    public function create(array $data): Vehicle
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $contacts = $data['contacts'] ?? [];
        unset($data['contacts']);

        $vehicle = Vehicle::create($data);

        if (! empty($contacts)) {
            $this->syncContacts($vehicle, $contacts);
        }

        return $vehicle;
    }

    /**
     * Update an existing vehicle with optional contacts.
     *
     * @param  Vehicle  $vehicle
     * @param  array  $data
     * @return Vehicle
     */
    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $data['updated_by'] = Auth::id();

        $contacts = $data['contacts'] ?? null;
        unset($data['contacts']);

        $vehicle->update($data);

        if ($contacts !== null) {
            $this->syncContacts($vehicle, $contacts);
        }

        return $vehicle;
    }

    /**
     * Delete (soft delete) a vehicle and its contacts.
     *
     * @param  Vehicle  $vehicle
     * @return bool
     */
    public function delete(Vehicle $vehicle): bool
    {
        $vehicle->contacts()->delete();

        return $vehicle->delete();
    }

    /**
     * Sync the vehicle contacts (delete existing and recreate).
     *
     * @param  Vehicle  $vehicle
     * @param  array  $contacts
     * @return void
     */
    protected function syncContacts(Vehicle $vehicle, array $contacts): void
    {
        $vehicle->contacts()->delete();

        foreach ($contacts as $contact) {
            $contact['created_by'] = Auth::id();
            $contact['updated_by'] = Auth::id();

            if (empty($contact['company_name'])) {
                $contact['company_name'] = null;
            }

            $vehicle->contacts()->create($contact);
        }
    }
}