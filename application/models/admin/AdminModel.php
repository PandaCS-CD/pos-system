<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdminModel extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_table = 'admin';
		$this->_pk = 'admin_id';
		$this->_order = 'admin_sort';
		$this->_active = 'admin_status';
	}

	public function _getAdmin()
	{
		return $this->db->order_by($this->_order, 'asc')->where('admin_id != 1')->get($this->_table)->result_array();
	}

	public function _getMember()
	{
		return $this->db->where('admin_permission', '2')->where('admin_id != 1')->join('company', 'company.com_id = admin.com_id', 'left')->get($this->_table)->result_array();
	}

	public function _getMemberID($id = false)
	{
		return $this->db->where('admin.admin_id', $id)->join('company', 'company.com_id = admin.com_id', 'left')->get($this->_table)->row_array();
	}

	public function _getAdminID($id = false)
	{
		return $this->db->where($this->_pk, $id)->get($this->_table)->row_array();
	}

	public function _getAdminEmail($email = false)
	{
		return $this->db->where('admin_email', $email)->get($this->_table)->row_array();
	}

	public function _getMaxOrder()
	{
		return $this->db->select('MAX(' . $this->_order . ') as max_row')->get($this->_table)->row_array();
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

	public function updateOrder($order = false, $id = false)
	{
		$order  = [$this->_order => $order];
		$result = $this->db->where($this->_pk, $id)->update($this->_table, $order);

		if ($result) {
			return 'true';
		} else {
			return 'false';
		}
	}

	public function updateStatus($id = false, $status = false)
	{
		$status  = [$this->_active => $status];
		$result = $this->db->where($this->_pk, $id)->update($this->_table, $status);

		if ($result) {
			return 'true';
		} else {
			return 'false';
		}
	}

	public function delete($id = null)
	{
		$result = $this->db->where($this->_pk, $id)->delete($this->_table);

		if ($result) {
			return 'true';
		} else {
			return 'false';
		}
	}
}
