<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

class Information extends Core_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/InformationModel', 'information');
    }

    public function index()
    {
        if ($this->input->post()) {
            $_form = [
                'info_name'           => $this->input->post('info_name') ?: null,
                'info_address'        => $this->input->post('info_address') ?: null,
                'info_phone'          => $this->input->post('info_phone') ?: null,
                'info_email'          => $this->input->post('info_email') ?: null,
                'info_line'           => $this->input->post('info_line') ?: null,
                'info_tax_id'         => $this->input->post('info_tax_id') ?: null,
                'info_receipt_footer' => $this->input->post('info_receipt_footer') ?: null,
            ];

            $information = $this->information->_getInformation();
            if ($information) {
                $result = $this->information->update($_form, $information['info_id']);
            } else {
                $result = $this->information->insert($_form);
            }

            if ($result == 'true') {
                $this->session->set_flashdata('result', $result);
                $this->session->set_flashdata('message', 'บันทึกข้อมูลสำเร็จ.');
            } else {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'บันทึกข้อมูลไม่สำเร็จ.');
            }
            redirect(admin_url('information'));
        }

        $this->_data = [
            'title' => 'ข้อมูลติดต่อ',
            'menu_slug' => 'information',
            'script' => 'script_information',
            'content' => 'page_information'
        ];

        $this->_data['information'] = $this->information->_getInformation();
        $this->load->view('admin/index', $this->_data);
    }
}
