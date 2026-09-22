<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

/**
 * Sales Controller
 * ประวัติการขาย
 */
class Sales extends Core_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/SalesModel', 'sales');
    }

    /**
     * DataTable AJAX
     */
    public function ajax_list()
    {
        $list = $this->sales->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $sale) {
            $no++;
            $row = array();

            // สถานะ
            if ($sale['sale_status'] == 1) {
                $statusBadge = '<span class="badge bg-success">สำเร็จ</span>';
            } else {
                $statusBadge = '<span class="badge bg-danger">ยกเลิก</span>';
            }

            // วิธีชำระ
            $paymentLabels = [
                'cash' => '<span class="badge bg-primary">เงินสด</span>',
                'transfer' => '<span class="badge bg-info">โอนเงิน</span>',
                'credit' => '<span class="badge bg-warning text-dark">บัตรเครดิต</span>',
            ];
            $paymentBadge = $paymentLabels[$sale['sale_payment_method']] ?? $sale['sale_payment_method'];

            // ปุ่ม
            $actions = '<div class="d-flex justify-content-center gap-1">';
            $actions .= '<a href="' . admin_url('sales/detail/' . $sale['sale_id']) . '" class="btn btn-info btn-sm px-2" title="ดูรายละเอียด"><i class="fas fa-eye"></i></a>';
            $actions .= '<a href="' . admin_url('pos/receipt/' . $sale['sale_id']) . '" class="btn btn-secondary btn-sm px-2" title="พิมพ์ใบเสร็จ" target="_blank"><i class="fas fa-print"></i></a>';
            if ($sale['sale_status'] == 1) {
                $actions .= '<a type="button" class="btn btn-danger btn-sm px-2" title="ยกเลิก"
                    data-bs-toggle="modal" data-bs-target="#modalDel"
                    data-id="' . $sale['sale_id'] . '"
                    data-url="' . admin_url('sales/cancel/' . $sale['sale_id']) . '">
                    <i class="fas fa-ban"></i></a>';
            }
            $actions .= '</div>';

            $row[] = '<div class="text-center">' . $no . '</div>';
            $row[] = '<div class="text-center"><strong>' . $sale['sale_code'] . '</strong></div>';
            $row[] = '<div class="text-center">' . date('d/m/Y', strtotime($sale['sale_date'])) . '<br><small>' . date('H:i', strtotime($sale['sale_time'])) . '</small></div>';
            $row[] = '<div class="text-end pe-3"><strong>' . number_format($sale['sale_total'], 2) . '</strong> ฿</div>';
            $row[] = '<div class="text-center">' . $paymentBadge . '</div>';
            $row[] = '<div class="text-center">' . ($sale['admin_name'] ?? '-') . '</div>';
            $row[] = '<div class="text-center">' . $statusBadge . '</div>';
            $row[] = '<div class="text-center">' . $actions . '</div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->sales->count_all(),
            "recordsFiltered" => $this->sales->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    /**
     * รายการขายทั้งหมด
     */
    public function index()
    {
        $this->_data = [
            'title' => 'ประวัติการขาย',
            'menu_slug' => 'sales',
            'script' => 'script_sales',
            'content' => 'page_sales',
        ];
        $this->load->view('admin/index', $this->_data);
    }

    /**
     * รายละเอียดรายการขาย
     */
    public function detail($saleId = null)
    {
        if (!$saleId) redirect(admin_url('sales'));

        $sale = $this->sales->getSaleById($saleId);
        if (!$sale) {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'ไม่พบรายการขาย');
            redirect(admin_url('sales'));
        }

        $this->_data = [
            'title' => 'รายละเอียดบิล ' . $sale['sale_code'],
            'menu_slug' => 'sales',
            'script' => '',
            'content' => 'sales/detail',
            'sale' => $sale,
            'saleDetails' => $this->sales->getSaleDetails($saleId),
        ];
        $this->load->view('admin/index', $this->_data);
    }

    /**
     * ยกเลิกรายการขาย
     */
    public function cancel($saleId = null)
    {
        if (!$saleId) redirect(admin_url('sales'));

        $adminId = $this->session->userdata('_auth')['admin_id'];
        $result = $this->sales->cancelSale($saleId, $adminId);

        if ($result) {
            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'ยกเลิกรายการขายสำเร็จ (สต๊อกคืนแล้ว)');
        } else {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'เกิดข้อผิดพลาดในการยกเลิก');
        }

        redirect(admin_url('sales'));
    }
}
