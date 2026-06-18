<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatbotMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'channel' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'web',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'messenger_psid' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'user_message' => [
                'type' => 'TEXT',
            ],
            'bot_reply' => [
                'type' => 'TEXT',
            ],
            'matched_faq_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'confidence' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'ai_used' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'source_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'fallback',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('channel');
        $this->forge->addKey('user_id');
        $this->forge->addKey('messenger_psid');
        $this->forge->addKey('matched_faq_id');
        $this->forge->addKey('source_type');
        $this->forge->createTable('chatbot_messages');
    }

    public function down()
    {
        $this->forge->dropTable('chatbot_messages');
    }
}