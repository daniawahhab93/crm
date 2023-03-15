<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Tbl_accounts extends CI_Migration
{


    public function up()
    {
        $data = array(
            array(
                'account_name' => 'فيزا',
                'permission' => 'all',
            ),
            array(
                'account_name' => 'حوالة بنكية',
                'permission' => 'all',
            ),
            array(
                'account_name' => 'STC Pay',
                'permission' => 'all',
            ),

        );
        $this->db->insert_batch('tbl_accounts', $data);
    }

    public function down()
    {
    }
}
