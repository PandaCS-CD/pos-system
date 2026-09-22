<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

class Admin extends Core_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/AdminModel', 'admin');
	}

	public function index()
	{
		if ($this->input->post('order') == 'submit-order') {
			$id = $this->input->post('id');
			if ($id) {
				$order = $this->input->post('order_data');

				for ($i = 0; $i < count($id); $i++) {
					$result = $this->admin->updateOrder($order[$i], $id[$i]);
				}
				if ($result == 'true') {
					$this->session->set_flashdata('result', $result);
					$this->session->set_flashdata('message', 'อัพเดทข้อมูลสำเร็จ.');
					redirect(admin_url('admin'));
				} else if ($result == 'false') {
					$this->session->set_flashdata('result', $result);
					$this->session->set_flashdata('message', 'อัพเดทข้อมูลไม่สำเร็จ.');
					redirect(admin_url('admin'));
				}
			} else {
				redirect(admin_url('admin'));
			}
		}

		$this->_data = [
			'title' => 'Admin',
			'menu_slug' => 'admin',
			'script' => '',
			'content' => 'page_admin'
		];
		$this->_data['admins'] = $this->admin->_getAdmin();
		$this->load->view('admin/index', $this->_data);
	}

	public function create()
	{
		if ($this->input->post()) {
			$name = ($this->input->post('name') ? $this->input->post('name') : null);
			$username = ($this->input->post('username') ? $this->input->post('username') : null);
			$password = ($this->input->post('password') ? $this->input->post('password') : null);
			$permission = ($this->input->post('permission') ? $this->input->post('permission') : null);
			$pass_hash = $this->password_hash($password);

			$this->load->model('authModel', 'auth');
			$check_username = $this->auth->checkRegisterUser($username);
			if ($check_username) {
				$this->session->set_flashdata('result', 'false');
				$this->session->set_flashdata('message', 'Username นี้มีการใช้งานแล้ว.');
				redirect(admin_url('admin/create'));
				return;
			}

			$max_order = $this->admin->_getMaxOrder();
			$numrow =  $max_order['max_row'] + 1;
			$_form = [
				'admin_name' => $name,
				'admin_user' => $username,
				'admin_pass' => $pass_hash,
				'admin_realpass' => $password,
				'admin_permission' => $permission ?? 2,
				'admin_sort' => $numrow,
				'admin_status' => 1,
			];

			$result = $this->admin->insert($_form);
			if ($result == 'true') {
				$this->session->set_flashdata('result', $result);
				$this->session->set_flashdata('message', 'เพิ่มข้อมูลสำเร็จ.');
				redirect(admin_url('admin'));
			} else if ($result == 'false') {
				$this->session->set_flashdata('result', $result);
				$this->session->set_flashdata('message', 'เพิ่มข้อมูลไม่สำเร็จ.');
				redirect(admin_url('admin/create'));
			}
		}

		$this->_data = [
			'title' => 'เพิ่ม Admin',
			'menu_slug' => 'admin',
			'script' => 'script_admin',
			'content' => 'page_admin_add'
		];
		$this->load->view('admin/index', $this->_data);
	}

	public function edit($id = false)
	{
		if ($this->input->post()) {
			$name = ($this->input->post('name') ? $this->input->post('name') : null);
			$username = ($this->input->post('username') ? $this->input->post('username') : null);
			$permission = ($this->input->post('permission') ? $this->input->post('permission') : null);

			if ($this->input->post('password')) {
				$password = ($this->input->post('password') ? $this->input->post('password') : null);
				$pass_hash = $this->password_hash($password);

				$_form = [
					'admin_name' => $name,
					'admin_user' => $username,
					'admin_pass' => $pass_hash,
					'admin_realpass' => $password,
					'admin_permission' => $permission ?? 2,
				];
			} else {
				$_form = [
					'admin_name' => $name,
					'admin_user' => $username,
					'admin_permission' => $permission ?? 2,
				];
			}

			$result = $this->admin->update($_form, $id);
			if ($result == 'true') {
				$this->session->set_flashdata('result', $result);
				$this->session->set_flashdata('message', 'แก้ไขข้อมูลสำเร็จ.');
				redirect(admin_url('admin'));
			} else if ($result == 'false') {
				$this->session->set_flashdata('result', $result);
				$this->session->set_flashdata('message', 'แก้ไขข้อมูลไม่สำเร็จ.');
				redirect(admin_url('admin/edit/' . $id));
			}
		}

		$this->_data = [
			'title' => 'แก้ไข Admin',
			'menu_slug' => 'admin',
			'script' => '',
			'content' => 'page_admin_edit'
		];
		$this->_data['adminID'] = $this->admin->_getAdminID($id);
		$this->load->view('admin/index', $this->_data);
	}

	public function del($id = false)
	{
		if ($id != null) {
			$result = $this->admin->delete($id);
			if ($result == 'true') {
				$this->session->set_flashdata('result', $result);
				$this->session->set_flashdata('message', 'ลบข้อมูลสำเร็จ.');
				redirect(admin_url('admin'));
			} else if ($result == 'false') {
				$this->session->set_flashdata('result', $result);
				$this->session->set_flashdata('message', 'ลบข้อมูลไม่สำเร็จ.');
				redirect(admin_url('admin'));
			}
		} else {
			redirect(admin_url('admin'));
		}
	}

	public function status($id  = false, $active  = false)
	{
		$result = $this->admin->updateStatus($id, $active);

		if ($result == 'true') {
			$this->session->set_flashdata('result', $result);
			$this->session->set_flashdata('message', 'อัพเดทข้อมูลสำเร็จ.');
			redirect(admin_url('admin'));
		} else if ($result == 'false') {
			$this->session->set_flashdata('result', $result);
			$this->session->set_flashdata('message', 'อัพเดทข้อมูลไม่สำเร็จ.');
			redirect(admin_url('admin'));
		}
	}

	public function password_hash($password)
	{
		return md5($password);
	}
}
