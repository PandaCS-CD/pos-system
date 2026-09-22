<?php

defined('BASEPATH') or exit('No direct script access allowed');

class InformationModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'information_index';
        $this->_pk = 'info_id';
    }

    public function _getInformation()
    {
        return $this->db->get($this->_table)->row_array();
    }

    public function insert($_form = false)
    {
        $result = $this->db->insert($this->_table, $_form);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function update($_form = false, $id = false)
    {
        $result = $this->db->where($this->_pk, $id)->update($this->_table, $_form);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }
}
