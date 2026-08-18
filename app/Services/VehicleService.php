<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class VehicleService
{
    /**
     * Create a new vehicle with optional relationships.
     *
     * @param  array  $data
     * @return Vehicle
     */
    public function create(array $data): Vehicle
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $relationships = $data['relationships'] ?? [];
        unset($data['relationships']);

        $vehicle = Vehicle::create($data);

        if (! empty($relationships)) {
            $this->syncRelationships($vehicle, $relationships);
        }

        return $vehicle;
    }

    /**
     * Update an existing vehicle with optional relationships.
     *
     * @param  Vehicle  $vehicle
     * @param  array  $data
     * @return Vehicle
     */
    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $data['updated_by'] = Auth::id();

        $relationships = $data['relationships'] ?? null;
        unset($data['relationships']);

        $vehicle->update($data);

        if ($relationships !== null) {
            $this->syncRelationships($vehicle, $relationships);
        }

        return $vehicle;
    }

    /**
     * Delete (soft delete) a vehicle and its relationships.
     *
     * @param  Vehicle  $vehicle
     * @return bool
     */
    public function delete(Vehicle $vehicle): bool
    {
        $vehicle->relationships()->delete();

        return $vehicle->delete();
    }

    /**
     * Sync the vehicle relationships (delete existing and recreate).
     *
     * @param  Vehicle  $vehicle
     * @param  array  $relationships
     * @return void
     */
    protected function syncRelationships(Vehicle $vehicle, array $relationships): void
    {
        $vehicle->relationships()->delete();

        foreach ($relationships as $relationship) {
            // Enforce only one primary commercial contact
            $relationship['is_primary_commercial'] = $relationship['is_primary_commercial'] ?? false;

            $vehicle->relationships()->create([
                'party_id' => $relationship['party_id'],
                'role' => $relationship['role'],
                'is_primary_commercial' => $relationship['is_primary_commercial'],
                'notes' => $relationship['notes'] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }
}