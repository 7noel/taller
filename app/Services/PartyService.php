<?php

namespace App\Services;

use App\Models\Party;
use Illuminate\Support\Facades\Auth;

class PartyService
{
    /**
     * Create a new party.
     *
     * @param  array  $data
     * @return Party
     */
    public function create(array $data): Party
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return Party::create($this->normalizeData($data));
    }

    /**
     * Update an existing party.
     *
     * @param  Party  $party
     * @param  array  $data
     * @return Party
     */
    public function update(Party $party, array $data): Party
    {
        $data['updated_by'] = Auth::id();

        $party->update($this->normalizeData($data));

        return $party;
    }

    /**
     * Delete (soft delete) a party.
     *
     * @param  Party  $party
     * @return bool
     */
    public function delete(Party $party): bool
    {
        return $party->delete();
    }

    /**
     * Normalize data depending on the party type.
     *
     * @param  array  $data
     * @return array
     */
    protected function normalizeData(array $data): array
    {
        // RUC (6) = empresa sin nombre/apellido; los demás documentos son personas sin razón social
        if (($data['document_type'] ?? '1') === '6') {
            $data['first_name'] = null;
            $data['last_name'] = null;
        } else {
            $data['business_name'] = null;
        }

        $data['is_insurance_company'] = $data['is_insurance_company'] ?? false;
        $data['receive_promotions'] = $data['receive_promotions'] ?? true;

        return $data;
    }
}
