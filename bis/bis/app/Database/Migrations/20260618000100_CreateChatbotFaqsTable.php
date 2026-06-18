<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatbotFaqsTable extends Migration
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
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'question' => [
                'type' => 'TEXT',
            ],
            'keywords' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'answer' => [
                'type' => 'TEXT',
            ],
            'language' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'mixed',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('category');
        $this->forge->addKey('language');
        $this->forge->addKey('is_active');
        $this->forge->createTable('chatbot_faqs');
    }

    public function down()
    {
        $this->forge->dropTable('chatbot_faqs');
    }
}