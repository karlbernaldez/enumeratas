<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds registered_voter flag to household_members so every family member's
 * voter registration status can be recorded alongside the household head's.
 */
class AddRegisteredVoterToHouseholdMembers extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('household_members');

        if (! in_array('registered_voter', $fields)) {
            $this->forge->addColumn('household_members', [
                'registered_voter' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'philhealth_no',
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('household_members');

        if (in_array('registered_voter', $fields)) {
            $this->forge->dropColumn('household_members', 'registered_voter');
        }
    }
}
