<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SalesModel extends CI_Model
{
    private $table = 'sales_index';
    private $table_detail = 'sales_detail';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * สร้างเลขที่ใบเสร็จ POS + ปี พ.ศ. + running 4 หลัก
     */
    public function generateSaleCode()
    {
        $year = date('y') + 43; // พ.ศ. 2 หลัก
        $prefix = 'POS' . $year;

        $this->db->select('sale_code');
        $this->db->like('sale_code', $prefix, 'after');
        $this->db->order_by('sale_id', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get($this->table)->row_array();

        if ($result) {
            $lastNum = intval(substr($result['sale_code'], strlen($prefix)));
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * บันทึกรายการขาย
     */
    public function createSale($saleData, $items)
    {
        $this->db->trans_begin();

        // Insert sale header
        $this->db->insert($this->table, $saleData);
        $saleId = $this->db->insert_id();

        // Insert sale details & update stock
        foreach ($items as $item) {
            $item['sale_id'] = $saleId;
            $this->db->insert($this->table_detail, $item);

            // ลดสต๊อกสินค้า
            $product = $this->db->where('product_id', $item['product_id'])->get('product_index')->row_array();
            $stockBefore = $product['product_stock'];
            $stockAfter = $stockBefore - $item['qty'];

            $this->db->where('product_id', $item['product_id']);
            $this->db->update('product_index', ['product_stock' => $stockAfter]);

            // บันทึกประวัติสต๊อก
            $this->db->insert('stock_history', [
                'product_id' => $item['product_id'],
                'stock_type' => 'out',
                'stock_qty' => -$item['qty'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'stock_note' => 'ขาย ' . $saleData['sale_code'],
                'sale_id' => $saleId,
                'admin_id' => $saleData['admin_id'],
            ]);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return $saleId;
    }

    /**
     * ยกเลิกรายการขาย (คืนสต๊อก)
     */
    public function cancelSale($saleId, $adminId)
    {
        $this->db->trans_begin();

        // ดึงรายละเอียด
        $details = $this->db->where('sale_id', $saleId)->get($this->table_detail)->result_array();

        foreach ($details as $item) {
            $product = $this->db->where('product_id', $item['product_id'])->get('product_index')->row_array();
            $stockBefore = $product['product_stock'];
            $stockAfter = $stockBefore + $item['qty'];

            // คืนสต๊อก
            $this->db->where('product_id', $item['product_id']);
            $this->db->update('product_index', ['product_stock' => $stockAfter]);

            // บันทึกประวัติ
            $this->db->insert('stock_history', [
                'product_id' => $item['product_id'],
                'stock_type' => 'in',
                'stock_qty' => $item['qty'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'stock_note' => 'ยกเลิกบิล ' . $saleId,
                'sale_id' => $saleId,
                'admin_id' => $adminId,
            ]);
        }

        // อัพเดทสถานะ
        $this->db->where('sale_id', $saleId)->update($this->table, ['sale_status' => 0]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    /**
     * ดึงรายการขายทั้งหมด
     */
    public function getSales($date_from = null, $date_to = null, $limit = null, $offset = 0)
    {
        $this->db->select('s.*, a.admin_name');
        $this->db->from('sales_index s');
        $this->db->join('admin a', 'a.admin_id = s.admin_id', 'left');

        if ($date_from) {
            $this->db->where('s.sale_date >=', $date_from);
        }
        if ($date_to) {
            $this->db->where('s.sale_date <=', $date_to);
        }

        $this->db->order_by('s.sale_id', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result_array();
    }

    /**
     * ดึงรายการขายตาม ID
     */
    public function getSaleById($id)
    {
        $this->db->select('s.*, a.admin_name');
        $this->db->from('sales_index s');
        $this->db->join('admin a', 'a.admin_id = s.admin_id', 'left');
        $this->db->where('s.sale_id', $id);
        return $this->db->get()->row_array();
    }

    /**
     * ดึงรายละเอียดรายการขาย
     */
    public function getSaleDetails($saleId)
    {
        return $this->db->where('sale_id', $saleId)->get($this->table_detail)->result_array();
    }

    /**
     * สรุปยอดขายวันนี้
     */
    public function getTodaySummary()
    {
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total_bills, COALESCE(SUM(sale_total),0) as total_amount, COALESCE(SUM(sale_discount),0) as total_discount');
        $this->db->where('sale_date', $today);
        $this->db->where('sale_status', 1);
        return $this->db->get($this->table)->row_array();
    }

    /**
     * สรุปยอดขายรายเดือน
     */
    public function getMonthlySummary($year = null, $month = null)
    {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');

        $this->db->select('COUNT(*) as total_bills, COALESCE(SUM(sale_total),0) as total_amount, COALESCE(SUM(sale_discount),0) as total_discount');
        $this->db->where('YEAR(sale_date)', $year);
        $this->db->where('MONTH(sale_date)', $month);
        $this->db->where('sale_status', 1);
        return $this->db->get($this->table)->row_array();
    }

    /**
     * ยอดขาย 7 วันล่าสุด (สำหรับกราฟ)
     */
    public function getLast7DaysSales($date_from = null, $date_to = null)
    {
        $this->db->select('sale_date, COUNT(*) as total_bills, SUM(sale_total) as total_amount');
        if ($date_from && $date_to) {
            $this->db->where('sale_date >=', $date_from);
            $this->db->where('sale_date <=', $date_to);
        } else {
            $this->db->where('sale_date >=', date('Y-m-d', strtotime('-6 days')));
        }
        $this->db->where('sale_status', 1);
        $this->db->group_by('sale_date');
        $this->db->order_by('sale_date', 'ASC');
        return $this->db->get($this->table)->result_array();
    }

    /**
     * สินค้าขายดี top N
     */
    public function getTopProducts($limit = 10, $date_from = null, $date_to = null)
    {
        $this->db->select('sd.product_id, sd.product_name, SUM(sd.qty) as total_qty, SUM(sd.total) as total_amount');
        $this->db->from('sales_detail sd');
        $this->db->join('sales_index s', 's.sale_id = sd.sale_id');
        $this->db->where('s.sale_status', 1);

        if ($date_from) {
            $this->db->where('s.sale_date >=', $date_from);
        }
        if ($date_to) {
            $this->db->where('s.sale_date <=', $date_to);
        }

        $this->db->group_by('sd.product_id, sd.product_name');
        $this->db->order_by('total_qty', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    /**
     * สินค้าใกล้หมด
     */
    public function getLowStockProducts($limit = 20)
    {
        $this->db->select('p.*, c.category_name');
        $this->db->from('product_index p');
        $this->db->join('category_index c', 'c.category_id = p.category_id', 'left');
        $this->db->where('p.product_stock <= p.product_stock_min', null, FALSE);
        $this->db->where('p.product_status', 1);
        $this->db->order_by('p.product_stock', 'ASC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    /**
     * DataTable server-side: sales list
     */
    private $dt_column_order = array(null, 'sale_code', 'sale_date', 'sale_total', 'sale_payment_method', 'admin_name', 'sale_status', null);
    private $dt_column_search = array('sale_code', 'admin_name');
    private $dt_order = array('sale_id' => 'DESC');

    private function _get_datatables_query()
    {
        $this->db->select('s.*, a.admin_name');
        $this->db->from('sales_index s');
        $this->db->join('admin a', 'a.admin_id = s.admin_id', 'left');

        // Filter by date
        if ($this->input->post('date_from')) {
            $this->db->where('s.sale_date >=', $this->input->post('date_from'));
        }
        if ($this->input->post('date_to')) {
            $this->db->where('s.sale_date <=', $this->input->post('date_to'));
        }

        $i = 0;
        foreach ($this->dt_column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->dt_column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->dt_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            foreach ($this->dt_order as $key => $val) {
                $this->db->order_by($key, $val);
            }
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result_array();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->db->count_all_results();
    }

    public function count_all()
    {
        return $this->db->count_all('sales_index');
    }

    /**
     * รายงานกำไรขาดทุน
     */
    public function getProfitReport($date_from, $date_to)
    {
        $this->db->select('
            COUNT(DISTINCT s.sale_id) as total_bills,
            COALESCE(SUM(sd.qty), 0) as total_items,
            COALESCE(SUM(sd.total), 0) as total_revenue,
            COALESCE(SUM(sd.product_cost * sd.qty), 0) as total_cost,
            COALESCE(SUM(sd.total) - SUM(sd.product_cost * sd.qty), 0) as total_profit
        ', FALSE);
        $this->db->from('sales_detail sd');
        $this->db->join('sales_index s', 's.sale_id = sd.sale_id');
        $this->db->where('s.sale_status', 1);
        $this->db->where('s.sale_date >=', $date_from);
        $this->db->where('s.sale_date <=', $date_to);
        return $this->db->get()->row_array();
    }
}
