<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Core_Controller extends CI_Controller
{
    protected $_data = [];

    public function __construct()
    {
        parent::__construct();

        if (empty($this->session->userdata('_auth'))) {
            $this->session->set_userdata('redirect_after_login', current_url());
            redirect(auth_url('login'));
        }
    }

    protected function setPageData($data)
    {
        $this->_data = array_merge($this->_data, $data);
    }
}
