<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AuthModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function check_login($user = false, $passHash = false)
    {
        $where = ['admin_user' => $user, 'admin_pass' => $passHash, 'admin_status' => '1'];
        return $this->db->where($where)->get('admin')->row_array();
    }

    public function checkRegisterEmail($email = false)
    {
        return $this->db->where('admin_email', $email)->get('admin')->row_array();
    }

    public function checkRegisterUser($username = false)
    {
        return $this->db->where('admin_user', $username)->get('admin')->row_array();
    }
}
