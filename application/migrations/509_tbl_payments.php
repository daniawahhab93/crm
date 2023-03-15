<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Tbl_payments extends CI_Migration
{


    public function up()
    {
        $this->db->query("ALTER TABLE tbl_payments CHANGE account_id account_id JSON NULL DEFAULT NULL COMMENT 'account_id means tracking deposit from which account");
    }

    public function down()
    {
    }
}
