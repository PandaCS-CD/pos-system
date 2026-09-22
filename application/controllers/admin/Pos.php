<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

/**
 * POS Terminal Controller
 * หน้าจอขายสินค้า (Cashier)
 */
class Pos extends Core_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/ProductModel', 'product');
        $this->load->model('admin/CategoryModel', 'category');
        $this->load->model('admin/SalesModel', 'sales');
    }

    /**
     * หน้า POS Terminal
     */
    public function index()
    {
        $this->_data = [
            'title' => 'ขายสินค้า (POS)',
            'menu_slug' => 'pos',
            'script' => 'script_pos',
            'content' => 'page_pos',
            'categories' => $this->category->_getCategory(),
            'products' => $this->product->_getProductActive(),
        ];
        $this->load->view('admin/index', $this->_data);
    }

    /**
     * API: ค้นหาสินค้าด้วย barcode
     */
    public function searchBarcode()
    {
        $barcode = $this->input->post('barcode');
        $product = $this->db->where('product_barcode', $barcode)
            ->where('product_status', 1)
            ->get('product_index')
            ->row_array();

        if ($product) {
            echo json_encode(['status' => 'success', 'data' => $product]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบสินค้า']);
        }
    }

    /**
     * API: ค้นหาสินค้าด้วยชื่อ/รหัส
     */
    public function searchProduct()
    {
        $keyword = $this->input->post('keyword');
        $category_id = $this->input->post('category_id');

        $this->db->where('product_status', 1);

        if ($keyword) {
            $this->db->group_start();
            $this->db->like('product_name', $keyword);
            $this->db->or_like('product_code', $keyword);
            $this->db->or_like('product_barcode', $keyword);
            $this->db->group_end();
        }

        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }

        $this->db->order_by('product_name', 'ASC');
        $products = $this->db->get('product_index')->result_array();

        echo json_encode(['status' => 'success', 'data' => $products]);
    }

    /**
     * บันทึกรายการขาย
     */
    public function checkout()
    {
        $items_json = $this->input->post('items');
        $items_arr = json_decode($items_json, true);

        if (empty($items_arr)) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่มีรายการสินค้า']);
            return;
        }

        $discount = floatval($this->input->post('discount') ?: 0);
        $received = floatval($this->input->post('received') ?: 0);
        $payment_method = $this->input->post('payment_method') ?: 'cash';
        $note = $this->input->post('note') ?: '';

        // คำนวณ subtotal
        $subtotal = 0;
        $saleDetails = [];

        foreach ($items_arr as $item) {
            $product = $this->db->where('product_id', $item['product_id'])->get('product_index')->row_array();
            if (!$product) continue;

            // ตรวจสอบสต๊อก
            if ($product['product_stock'] < $item['qty']) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'สินค้า "' . $product['product_name'] . '" คงเหลือไม่พอ (เหลือ ' . $product['product_stock'] . ')'
                ]);
                return;
            }

            $itemTotal = $item['qty'] * $product['product_price'];
            $itemDiscount = isset($item['discount']) ? floatval($item['discount']) : 0;
            $itemTotal -= $itemDiscount;

            $subtotal += $itemTotal;

            $saleDetails[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'product_price' => $product['product_price'],
                'product_cost' => $product['product_cost'],
                'qty' => $item['qty'],
                'discount' => $itemDiscount,
                'total' => $itemTotal,
            ];
        }

        $total = $subtotal - $discount;
        $change = $received - $total;

        if ($payment_method === 'cash' && $received < $total) {
            echo json_encode(['status' => 'error', 'message' => 'จำนวนเงินที่รับไม่เพียงพอ']);
            return;
        }

        $saleCode = $this->sales->generateSaleCode();

        $saleData = [
            'sale_code' => $saleCode,
            'sale_date' => date('Y-m-d'),
            'sale_time' => date('H:i:s'),
            'sale_subtotal' => $subtotal,
            'sale_discount' => $discount,
            'sale_total' => $total,
            'sale_received' => $received,
            'sale_change' => max(0, $change),
            'sale_payment_method' => $payment_method,
            'sale_note' => $note,
            'sale_status' => 1,
            'admin_id' => $this->session->userdata('_auth')['admin_id'],
        ];

        $saleId = $this->sales->createSale($saleData, $saleDetails);

        if ($saleId) {
            echo json_encode([
                'status' => 'success',
                'message' => 'บันทึกรายการขายสำเร็จ',
                'sale_id' => $saleId,
                'sale_code' => $saleCode,
                'total' => $total,
                'received' => $received,
                'change' => max(0, $change),
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
        }
    }

    /**
     * พิมพ์ใบเสร็จ
     */
    public function receipt($saleId = null)
    {
        if (!$saleId) {
            redirect(admin_url('pos'));
        }

        $sale = $this->sales->getSaleById($saleId);
        if (!$sale) {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'ไม่พบรายการขาย');
            redirect(admin_url('pos'));
        }

        $storeInfo = $this->db->get('information_index')->row_array();

        $this->_data = [
            'title' => 'ใบเสร็จ ' . $sale['sale_code'],
            'menu_slug' => 'pos',
            'script' => '',
            'content' => 'pos/receipt',
            'sale' => $sale,
            'saleDetails' => $this->sales->getSaleDetails($saleId),
            'storeInfo' => $storeInfo,
        ];
        $this->load->view('admin/index', $this->_data);
    }
}
