<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatbotMessageModel extends Model
{
    protected $table = 'chatbot_messages';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'channel',
        'user_id',
        'messenger_psid',
        'user_message',
        'bot_reply',
        'matched_faq_id',
        'confidence',
        'ai_used',
        'source_type',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $validationRules = [
        'channel'      => 'required|max_length[30]',
        'user_message' => 'required',
        'bot_reply'    => 'required',
        'confidence'   => 'permit_empty|decimal',
        'ai_used'      => 'permit_empty|in_list[0,1]',
        'source_type'  => 'required|max_length[30]',
    ];
}