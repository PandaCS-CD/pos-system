<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

/**
 * StockManage Controller
 * จัดการสต๊อกสินค้า - รับเข้า/ปรับปรุง
 */
class StockManage extends Core_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/ProductModel', 'product');
    }

    /**
     * หน้ารายการสต๊อก
     */
    public function index()
    {
        $this->_data = [
            'title' => 'จัดการสต๊อก',
            'menu_slug' => 'stockmanage',
            'script' => 'script_stockmanage',
            'content' => 'page_stockmanage',
        ];
        $this->_data['products'] = $this->db
            ->select('p.*, c.category_name')
            ->from('product_index p')
            ->join('category_index c', 'c.category_id = p.category_id', 'left')
            ->where('p.product_status', 1)
            ->order_by('p.product_name', 'ASC')
            ->get()->result_array();

        $this->load->view('admin/index', $this->_data);
    }

    /**
     * รับสินค้าเข้าสต๊อก
     */
    public function stockIn()
    {
        $product_id = $this->input->post('product_id');
        $qty = intval($this->input->post('qty'));
        $note = $this->input->post('note') ?: 'รับสินค้าเข้า';

        if (!$product_id || $qty <= 0) {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'ข้อมูลไม่ถูกต้อง');
            redirect(admin_url('stockManage'));
        }

        $product = $this->db->where('product_id', $product_id)->get('product_index')->row_array();
        if (!$product) {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'ไม่พบสินค้า');
            redirect(admin_url('stockManage'));
        }

        $stockBefore = $product['product_stock'];
        $stockAfter = $stockBefore + $qty;

        $this->db->trans_begin();

        // อัพเดทสต๊อก
        $this->db->where('product_id', $product_id)->update('product_index', ['product_stock' => $stockAfter]);

        // บันทึกประวัติ
        $this->db->insert('stock_history', [
            'product_id' => $product_id,
            'stock_type' => 'in',
            'stock_qty' => $qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'stock_note' => $note,
            'admin_id' => $this->session->userdata('_auth')['admin_id'],
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'เกิดข้อผิดพลาด');
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'รับสินค้าเข้าสต๊อกสำเร็จ (+' . $qty . ')');
        }

        redirect(admin_url('stockManage'));
    }

    /**
     * ปรับปรุงสต๊อก
     */
    public function adjust()
    {
        $product_id = $this->input->post('product_id');
        $new_stock = intval($this->input->post('new_stock'));
        $note = $this->input->post('note') ?: 'ปรับปรุงสต๊อก';

        if (!$product_id || $new_stock < 0) {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'ข้อมูลไม่ถูกต้อง');
            redirect(admin_url('stockManage'));
        }

        $product = $this->db->where('product_id', $product_id)->get('product_index')->row_array();
        $stockBefore = $product['product_stock'];
        $diff = $new_stock - $stockBefore;

        $this->db->trans_begin();

        $this->db->where('product_id', $product_id)->update('product_index', ['product_stock' => $new_stock]);

        $this->db->insert('stock_history', [
            'product_id' => $product_id,
            'stock_type' => 'adjust',
            'stock_qty' => $diff,
            'stock_before' => $stockBefore,
            'stock_after' => $new_stock,
            'stock_note' => $note,
            'admin_id' => $this->session->userdata('_auth')['admin_id'],
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'เกิดข้อผิดพลาด');
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'ปรับปรุงสต๊อกสำเร็จ (' . $stockBefore . ' → ' . $new_stock . ')');
        }

        redirect(admin_url('stockManage'));
    }

    /**
     * ประวัติสต๊อก
     */
    public function history($product_id = null)
    {
        $where = [];
        if ($product_id) {
            $where['product_id'] = $product_id;
        }

        $this->db->select('sh.*, p.product_name, p.product_code, a.admin_name');
        $this->db->from('stock_history sh');
        $this->db->join('product_index p', 'p.product_id = sh.product_id', 'left');
        $this->db->join('admin a', 'a.admin_id = sh.admin_id', 'left');

        if ($product_id) {
            $this->db->where('sh.product_id', $product_id);
        }

        $this->db->order_by('sh.stock_id', 'DESC');
        $this->db->limit(200);
        $history = $this->db->get()->result_array();

        $this->_data = [
            'title' => 'ประวัติสต๊อก',
            'menu_slug' => 'stockmanage',
            'script' => '',
            'content' => 'stock/history',
            'history' => $history,
            'product_id' => $product_id,
        ];
        $this->load->view('admin/index', $this->_data);
    }
}
