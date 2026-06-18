<?php

namespace App\Models;

use CodeIgniter\Model;

class MessengerUserModel extends Model
{
    protected $table = 'messenger_users';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'psid',
        'first_name',
        'last_name',
        'profile_pic',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'psid'       => 'required|max_length[100]',
        'first_name' => 'permit_empty|max_length[100]',
        'last_name'  => 'permit_empty|max_length[100]',
    ];
}