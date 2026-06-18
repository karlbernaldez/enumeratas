<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentUpdateRequestModel extends Model
{
    protected $table = 'resident_update_requests';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'resident_id',
        'request_type',
        'current_data',
        'requested_data',
        'reason',
        'status',
        'source_channel',
        'messenger_psid',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'request_type'   => 'required|max_length[50]',
        'requested_data' => 'required',
        'status'         => 'required|max_length[30]',
        'source_channel' => 'required|max_length[30]',
    ];
}