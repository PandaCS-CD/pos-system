<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ProductModel extends CI_Model
{
    private $table = 'product_index';
    private $column_order = array(null, null, 'product_img', 'product_code', 'product_name', 'product_price', 'product_sort', null);
    private $column_search = array('product_id', 'product_name', 'product_code');
    private $order = array('category_id' => 'asc', 'product_sort' => 'asc');


    public function __construct()
    {
        parent::__construct();
        $this->_table = 'product_index';
        $this->_pk = 'product_id';
        $this->_order = 'product_sort';
        $this->_active = 'product_status';
    }

    public function _getProduct()
    {
        return $this->db->order_by($this->_order, 'asc')->get($this->_table)->result_array();
    }

    public function _getProductActive()
    {
        return $this->db->where($this->_active, 1)
            ->order_by($this->_order, 'asc')
            ->get($this->_table)
            ->result_array();
    }

    public function _getproductHomeActive()
    {
        return $this->db->where($this->_active, 1)
            ->where('show_home', 1)
            ->order_by($this->_order, 'asc')
            ->get($this->_table)
            ->result_array();
    }

    public function _getproductActiveID($id = false)
    {
        return $this->db->where($this->_pk, $id)
            ->where($this->_active, 1)
            ->get($this->_table)
            ->row_array();
    }

    public function _getproductID($id = false)
    {
        return $this->db->where($this->_pk, $id)->get($this->_table)->row_array();
    }

    public function _getproductFrontID($id = false)
    {
        return $this->db->where($this->_pk, $id)->get($this->_table)->row_array();
    }

    public function _getMaxOrder()
    {
        return $this->db->select('MAX(' . $this->_order . ') as max_row')->get($this->_table)->row_array();
    }

    public function insert($_form = false)
    {
        $result = $this->db->insert($this->_table, $_form);

        if ($result) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function insert_gallery($_form = false)
    {
        $result = $this->db->insert('gallery', $_form);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function update($_form = false, $id = false)
    {
        $result = $this->db->where($this->_pk, $id)->update($this->_table, $_form);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function updateOrder($order = false, $id = false)
    {
        $order = [$this->_order => $order];
        $result = $this->db->where($this->_pk, $id)->update($this->_table, $order);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function updateStatus($id = false, $status = false)
    {
        $status = [$this->_active => $status];
        $result = $this->db->where($this->_pk, $id)->update($this->_table, $status);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function _getGallery($id = false)
    {
        return $this->db->order_by('gal_id', 'ASC')->where($this->_pk, $id)->get('gallery')->result_array();
    }

    public function  _getGalleryID($id = false)
    {
        return $this->db->order_by('gal_id', 'ASC')->where('gal_id', $id)->get('gallery')->row_array();
    }

    public function  delGalleryID($id = false)
    {
        $result = $this->db->where('gal_id', $id)->delete('gallery');

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function delete($id = null)
    {
        $result = $this->db->where($this->_pk, $id)->delete($this->_table);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    private function _get_datatables_query()
    {
        $this->db->from($this->table);

        // กรองตามหมวดหมู่ถ้ามีการเลือก
        if (isset($_POST['category_id']) && $_POST['category_id'] != '') {
            $this->db->where('category_id', $_POST['category_id']);
        }

        // รับค่า search จาก DataTables มาตรฐาน หรือ custom search_product
        $search_value = '';
        if (isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
            $search_value = $_POST['search']['value'];
        } elseif (isset($_POST['search_product']) && $_POST['search_product'] != '') {
            $search_value = $_POST['search_product'];
        }

        // ค้นหาด้วย LIKE รองรับภาษาไทย
        if ($search_value != '') {
            $search_value = trim($search_value);
            $this->db->group_start();
            foreach ($this->column_search as $i => $item) {
                if ($i === 0) {
                    $this->db->like($item, $search_value, 'both', false);
                } else {
                    $this->db->or_like($item, $search_value, 'both', false);
                }
            }
            $this->db->group_end();
        }

        if (isset($_POST['order']) && isset($_POST['order']['0']['column'])) {
            $col_index = $_POST['order']['0']['column'];
            if (isset($this->column_order[$col_index]) && $this->column_order[$col_index] != null) {
                $this->db->order_by($this->column_order[$col_index], $_POST['order']['0']['dir']);
            }
        } else if (isset($this->order)) {
            // ถ้าเลือกหมวดหมู่แล้ว sort ตาม product_sort อย่างเดียว
            if (isset($_POST['category_id']) && $_POST['category_id'] != '') {
                $this->db->order_by('product_sort', 'asc');
            } else {
                // ถ้าไม่ได้เลือกหมวดหมู่ sort ตาม category_id, product_sort
                foreach ($this->order as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            }
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table)
            ->order_by($this->_order, 'asc');

        // กรองตามหมวดหมู่ถ้ามีการเลือก
        if (isset($_POST['category_id']) && $_POST['category_id'] != '') {
            $this->db->where('category_id', $_POST['category_id']);
        }
        return $this->db->count_all_results();
    }
    public function getProductsByCategory($category_id)
    {
        return $this->db
            ->where('category_id', $category_id)
            ->order_by('product_sort', 'asc')
            ->get('product_index')
            ->result_array();
    }
    public function _getproductActivecat($category_id = false)
    {
        $this->db->where($this->_active, 1);

        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }

        return $this->db->get($this->_table)->result_array();
    }

    public function _getproductActivecat_pagination($category_id = false, $limit = 40, $offset = 0)
    {
        $this->db->where($this->_active, 1);

        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }

        $this->db->order_by($this->_order, 'asc');
        $this->db->limit($limit, $offset);

        return $this->db->get($this->_table)->result_array();
    }

    public function _getproductindexActive()
    {
        return $this->db->where($this->_active, 1)
            ->order_by($this->_order, 'asc')
            ->limit(8)
            ->get('product_index')
            ->result_array();
    }
}
