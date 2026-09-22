<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

class Dashboard extends Core_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/SalesModel', 'sales');
	}
	public function index()
	{
		$this->_data['title'] = 'Dashboard - POS ร้านเบ็ดเตล็ด';
		$this->_data['menu_slug'] = 'dashboard';
		$this->_data['script'] = 'script_dashboard';
		$this->_data['content'] = 'page_dashboard';
		$this->_data['today'] = $this->sales->getTodaySummary();
		$this->_data['monthly'] = $this->sales->getMonthlySummary();
		$this->_data['last7days'] = $this->sales->getLast7DaysSales();
		$this->_data['top_products'] = $this->sales->getTopProducts(5);
		$this->_data['low_stock'] = $this->sales->getLowStockProducts(5);
		// load theme
		$this->load->view('admin/index', $this->_data);
	}
}
