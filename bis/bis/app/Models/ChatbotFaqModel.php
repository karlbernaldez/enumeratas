<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatbotFaqModel extends Model
{
    protected $table = 'chatbot_faqs';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'category',
        'question',
        'keywords',
        'answer',
        'language',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'category'  => 'required|max_length[100]',
        'question'  => 'required',
        'answer'    => 'required',
        'language'  => 'permit_empty|max_length[30]',
        'is_active' => 'permit_empty|in_list[0,1]',
    ];
}