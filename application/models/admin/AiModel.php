<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AiModel extends CI_Model
{
    private $table_settings = 'ai_settings';

    public function __construct()
    {
        parent::__construct();
        $this->_ensure_table_exists();
    }

    /**
     * สร้างตาราง ai_settings อัตโนมัติหากยังไม่มี
     */
    private function _ensure_table_exists()
    {
        if (!$this->db->table_exists($this->table_settings)) {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table_settings}` (
                `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
                `setting_value` TEXT DEFAULT NULL,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql);
        }
    }

    public function get_setting($key, $default = null)
    {
        $this->_ensure_table_exists();
        $row = $this->db->where('setting_key', $key)->get($this->table_settings)->row_array();
        return $row ? $row['setting_value'] : $default;
    }

    public function save_setting($key, $value)
    {
        $this->_ensure_table_exists();
        $exists = $this->db->where('setting_key', $key)->get($this->table_settings)->row_array();
        if ($exists) {
            return $this->db->where('setting_key', $key)->update($this->table_settings, [
                'setting_value' => $value
            ]);
        } else {
            return $this->db->insert($this->table_settings, [
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }
    }

    /**
     * คำนวณช่วงวันที่ตาม period
     */
    public function parse_date_range($period = 'today', $custom_from = null, $custom_to = null)
    {
        $today = date('Y-m-d');
        switch ($period) {
            case 'today':
                return [
                    'start' => $today,
                    'end'   => $today,
                    'label' => 'วันนี้ (' . date('d/m/Y') . ')'
                ];
            case 'yesterday':
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                return [
                    'start' => $yesterday,
                    'end'   => $yesterday,
                    'label' => 'เมื่อวาน (' . date('d/m/Y', strtotime('-1 day')) . ')'
                ];
            case 'last7days':
                return [
                    'start' => date('Y-m-d', strtotime('-6 days')),
                    'end'   => $today,
                    'label' => '7 วันล่าสุด'
                ];
            case 'last30days':
                return [
                    'start' => date('Y-m-d', strtotime('-29 days')),
                    'end'   => $today,
                    'label' => '30 วันล่าสุด'
                ];
            case 'this_month':
                return [
                    'start' => date('Y-m-01'),
                    'end'   => date('Y-m-t'),
                    'label' => 'เดือนนี้ (' . date('m/Y') . ')'
                ];
            case 'custom':
                $start = !empty($custom_from) ? $custom_from : $today;
                $end   = !empty($custom_to) ? $custom_to : $today;
                return [
                    'start' => $start,
                    'end'   => $end,
                    'label' => "ช่วงวันที่ {$start} ถึง {$end}"
                ];
            default:
                return [
                    'start' => $today,
                    'end'   => $today,
                    'label' => 'วันนี้'
                ];
        }
    }

    /**
     * ดึงข้อมูลภาพรวมร้านค้าแบบกระชับ รัดกุม เพื่อเป็น Context ให้กับ AI
     */
    public function get_pos_context($period = 'today', $custom_from = null, $custom_to = null)
    {
        $range = $this->parse_date_range($period, $custom_from, $custom_to);
        $start = $range['start'];
        $end   = $range['end'];

        // ข้อมูลร้านค้า
        $shop_info = $this->db->get('information_index')->row_array();
        $shop_name = !empty($shop_info['info_name']) ? $shop_info['info_name'] : 'ร้านขายของเบ็ดเตล็ด';

        // 1. สรุปยอดขาย กำไร และจำนวนบิล
        $this->db->select('
            COUNT(DISTINCT s.sale_id) as total_bills,
            COALESCE(SUM(s.sale_total), 0) as total_revenue,
            COALESCE(SUM(s.sale_subtotal), 0) as total_subtotal,
            COALESCE(SUM(s.sale_discount), 0) as total_discount
        ');
        $this->db->from('sales_index s');
        $this->db->where('s.sale_status', 1);
        $this->db->where('s.sale_date >=', $start);
        $this->db->where('s.sale_date <=', $end);
        $sales_stat = $this->db->get()->row_array();

        // ต้นทุนและกำไร
        $this->db->select('
            COALESCE(SUM(sd.qty), 0) as total_items_sold,
            COALESCE(SUM(sd.product_cost * sd.qty), 0) as total_cost,
            COALESCE(SUM(sd.total) - SUM(sd.product_cost * sd.qty), 0) as total_profit
        ', FALSE);
        $this->db->from('sales_detail sd');
        $this->db->join('sales_index s', 's.sale_id = sd.sale_id');
        $this->db->where('s.sale_status', 1);
        $this->db->where('s.sale_date >=', $start);
        $this->db->where('s.sale_date <=', $end);
        $profit_stat = $this->db->get()->row_array();

        $total_revenue = (float)($sales_stat['total_revenue'] ?? 0);
        $total_bills   = (int)($sales_stat['total_bills'] ?? 0);
        $total_cost    = (float)($profit_stat['total_cost'] ?? 0);
        $total_profit  = (float)($profit_stat['total_profit'] ?? 0);
        $items_sold    = (int)($profit_stat['total_items_sold'] ?? 0);

        $avg_bill = ($total_bills > 0) ? round($total_revenue / $total_bills, 2) : 0;
        $profit_margin = ($total_revenue > 0) ? round(($total_profit / $total_revenue) * 100, 2) : 0;

        // 2. สินค้าขายดี Top 10
        $this->db->select('
            sd.product_name,
            SUM(sd.qty) as qty,
            SUM(sd.total) as revenue,
            SUM(sd.total - (sd.product_cost * sd.qty)) as profit
        ', FALSE);
        $this->db->from('sales_detail sd');
        $this->db->join('sales_index s', 's.sale_id = sd.sale_id');
        $this->db->where('s.sale_status', 1);
        $this->db->where('s.sale_date >=', $start);
        $this->db->where('s.sale_date <=', $end);
        $this->db->group_by('sd.product_id, sd.product_name');
        $this->db->order_by('qty', 'DESC');
        $this->db->limit(10);
        $top_products = $this->db->get()->result_array();

        // 3. ยอดขายแยกตามหมวดหมู่
        $this->db->select('
            COALESCE(c.category_name, "ไม่ระบุ") as category_name,
            SUM(sd.qty) as qty,
            SUM(sd.total) as revenue
        ');
        $this->db->from('sales_detail sd');
        $this->db->join('sales_index s', 's.sale_id = sd.sale_id');
        $this->db->join('product_index p', 'p.product_id = sd.product_id', 'left');
        $this->db->join('category_index c', 'c.category_id = p.category_id', 'left');
        $this->db->where('s.sale_status', 1);
        $this->db->where('s.sale_date >=', $start);
        $this->db->where('s.sale_date <=', $end);
        $this->db->group_by('c.category_id, c.category_name');
        $this->db->order_by('revenue', 'DESC');
        $categories_summary = $this->db->get()->result_array();

        // 4. สินค้าสต๊อกต่ำ/วิกฤตที่ต้องเฝ้าระวัง
        $this->db->select('product_name, product_stock, product_stock_min, product_price');
        $this->db->from('product_index');
        $this->db->where('product_stock <= product_stock_min', null, FALSE);
        $this->db->where('product_status', 1);
        $this->db->order_by('product_stock', 'ASC');
        $this->db->limit(8);
        $low_stock = $this->db->get()->result_array();

        // 5. พฤติกรรมการชำระเงิน
        $this->db->select('sale_payment_method, COUNT(*) as count, SUM(sale_total) as amount');
        $this->db->from('sales_index');
        $this->db->where('sale_status', 1);
        $this->db->where('sale_date >=', $start);
        $this->db->where('sale_date <=', $end);
        $this->db->group_by('sale_payment_method');
        $payments = $this->db->get()->result_array();

        // 6. ช่วงเวลาขาย (Hourly Sales distribution)
        $this->db->select('
            HOUR(sale_time) as sale_hour,
            COUNT(*) as bills,
            SUM(sale_total) as amount
        ');
        $this->db->from('sales_index');
        $this->db->where('sale_status', 1);
        $this->db->where('sale_date >=', $start);
        $this->db->where('sale_date <=', $end);
        $this->db->group_by('HOUR(sale_time)');
        $this->db->order_by('sale_hour', 'ASC');
        $hourly = $this->db->get()->result_array();

        return [
            'shop_name'           => $shop_name,
            'period_label'        => $range['label'],
            'date_start'          => $start,
            'date_end'            => $end,
            'summary'             => [
                'total_revenue'   => $total_revenue,
                'total_bills'     => $total_bills,
                'items_sold'      => $items_sold,
                'total_cost'      => $total_cost,
                'total_profit'    => $total_profit,
                'profit_margin'   => $profit_margin,
                'avg_bill_amount' => $avg_bill,
                'total_discount'  => (float)($sales_stat['total_discount'] ?? 0)
            ],
            'top_products'        => $top_products,
            'categories'          => $categories_summary,
            'low_stock_items'     => $low_stock,
            'payment_methods'     => $payments,
            'hourly_distribution'=> $hourly
        ];
    }
}
