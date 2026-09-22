<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

/**
 * Report Controller
 * รายงานและสรุปยอด
 */
class Report extends Core_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/SalesModel', 'sales');
    }

    /**
     * หน้ารายงาน
     */
    public function index()
    {
        $date_from = $this->input->get('date_from') ?: date('Y-m-01');
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');

        $this->_data = [
            'title' => 'รายงานสรุป',
            'menu_slug' => 'report',
            'script' => 'script_report',
            'content' => 'page_report',
            'date_from' => $date_from,
            'date_to' => $date_to,
            'profit_report' => $this->sales->getProfitReport($date_from, $date_to),
            'top_products' => $this->sales->getTopProducts(10, $date_from, $date_to),
            'last7days' => $this->sales->getLast7DaysSales($date_from, $date_to),
            'low_stock' => $this->sales->getLowStockProducts(),
        ];
        $this->load->view('admin/index', $this->_data);
    }
}
