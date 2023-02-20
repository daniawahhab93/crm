<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Tbl_users_warehouse extends CI_Migration
{
  
    
     public function up() { 
            $this->dbforge->add_field(array(
            'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'user_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
					
            ),'warehouse_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
					'null' => TRUE
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('tbl_users_warehouse');
    }

    public function down()
    {
        $this->dbforge->drop_table('tbl_users_warehouse');
    }
}
