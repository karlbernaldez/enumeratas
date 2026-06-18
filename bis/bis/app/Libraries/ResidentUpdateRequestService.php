<?php

namespace App\Libraries;

use App\Models\ResidentUpdateRequestModel;

class ResidentUpdateRequestService
{
    private ResidentUpdateRequestModel $requestModel;

    private array $allowedRequestTypes = [
        'new_resident',
        'change_address',
        'change_household',
        'newborn',
        'death_record',
        'contact_update',
        'civil_status_update',
        'other',
    ];

    public function __construct()
    {
        $this->requestModel = new ResidentUpdateRequestModel();
    }

    public function detectRequestType(string $message): ?string
    {
        $message = strtolower(trim($message));

        if ($message === '') {
            return null;
        }

        /*
     * Important:
     * Put more specific request types first.
     * Example: "May bagong panganak po sa household namin"
     * contains "household", but it should be newborn, not change_household.
     */
        $patterns = [
            'newborn' => [
                'new born',
                'newborn',
                'bagong panganak',
                'kapapanganak',
                'baby',
                'anak ko',
                'may ipinanganak',
                'ipinanganak',
                'panganak',
                'new baby',
                'new child',
            ],

            'death_record' => [
                'namatay',
                'death',
                'deceased',
                'patay',
                'pumanaw',
                'death record',
                'passed away',
                'yumao',
            ],

            'new_resident' => [
                'bagong lipat',
                'new resident',
                'lumipat dito',
                'newly moved',
                'transfer dito',
                'bagong residente',
            ],

            'change_address' => [
                'change address',
                'palit address',
                'bagong address',
                'lumipat ng bahay',
                'changed address',
                'update address',
            ],

            'contact_update' => [
                'change number',
                'update number',
                'phone number',
                'contact number',
                'palit number',
                'bagong number',
                'cellphone number',
                'mobile number',
            ],

            'civil_status_update' => [
                'civil status',
                'married',
                'single',
                'widowed',
                'separated',
                'kasal',
                'balo',
                'hiwalay',
            ],

            'change_household' => [
                'household',
                'sambahayan',
                'kasama sa bahay',
                'family member',
                'household member',
                'change household',
                'update household',
            ],
        ];

        foreach ($patterns as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    return $type;
                }
            }
        }

        return null;
    }

    public function createPendingRequest(
        string $requestType,
        string $requestedData,
        ?int $residentId = null,
        ?string $currentData = null,
        ?string $reason = null,
        string $sourceChannel = 'web',
        ?string $messengerPsid = null
    ): array {
        if (! in_array($requestType, $this->allowedRequestTypes, true)) {
            $requestType = 'other';
        }

        $requestId = $this->requestModel->insert([
            'resident_id'     => $residentId,
            'request_type'    => $requestType,
            'current_data'    => $currentData,
            'requested_data'  => $requestedData,
            'reason'          => $reason,
            'status'          => 'pending',
            'source_channel'  => $sourceChannel,
            'messenger_psid'  => $messengerPsid,
        ], true);

        return [
            'success' => $requestId !== false,
            'request_id' => $requestId,
            'request_type' => $requestType,
            'status' => 'pending',
        ];
    }

    public function formatRequestTypeLabel(string $requestType): string
    {
        return match ($requestType) {
            'new_resident' => 'new resident / bagong lipat',
            'change_address' => 'address update',
            'change_household' => 'household information update',
            'newborn' => 'newborn record',
            'death_record' => 'death record',
            'contact_update' => 'contact number update',
            'civil_status_update' => 'civil status update',
            default => 'resident information update',
        };
    }
}
