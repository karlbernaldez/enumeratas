<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResidentUpdateRequestsTable extends Migration
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

            'resident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'request_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],

            'current_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'requested_data' => [
                'type' => 'TEXT',
            ],

            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'pending',
            ],

            'source_channel' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'web',
            ],

            'messenger_psid' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],

            'reviewed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'review_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('resident_id');
        $this->forge->addKey('request_type');
        $this->forge->addKey('status');
        $this->forge->addKey('source_channel');
        $this->forge->addKey('messenger_psid');
        $this->forge->addKey('reviewed_by');

        $this->forge->createTable('resident_update_requests');
    }

    public function down()
    {
        $this->forge->dropTable('resident_update_requests');
    }
}