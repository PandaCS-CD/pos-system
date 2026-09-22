<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CategoryModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'category_index';
        $this->_pk = 'category_id';
        $this->_order = 'category_sort';
        $this->_active = 'category_status';
        $this->meta_content = 'category_meta';
    }

    public function _getCategory()
    {
        return $this->db->order_by($this->_order, 'asc')->get($this->_table)->result_array();
    }

    public function _getCategoryActive()
    {
        return $this->db->where($this->_active, 1)
            ->order_by($this->_order, 'asc')
            ->get($this->_table)
            ->result_array();
    }

    public function _getcategoryHomeActive()
    {
        return $this->db->where($this->_active, 1)
            ->where('show_home', 1)
            ->order_by($this->_order, 'asc')
            ->get($this->_table)
            ->result_array();
    }

    public function _getcategoryActiveID($id = false)
    {
        return $this->db->where($this->_pk, $id)
            ->where($this->_active, 1)
            ->get($this->_table)
            ->row_array();
    }

    public function _getcategoryID($id = false)
    {
        return $this->db->where($this->_pk, $id)->get($this->_table)->row_array();
    }

    public function _getcategoryFrontID($id = false)
    {
        return $this->db->where($this->_pk, $id)->get($this->_table)->row_array();
    }

    public function getOtherCategorys($id)
    {
        return $this->db->where($this->_pk . ' !=', $id)
            ->order_by($this->_order, 'asc')
            ->get($this->_table)
            ->result_array();
    }




    public function _getMaxOrder()
    {
        return $this->db->select('MAX(' . $this->_order . ') as max_row')->get($this->_table)->row_array();
    }

    public function _getGallery($category_id = false)
    {
        return $this->db->where('category_id', $category_id)->get($this->_gallery_table)->result_array();
    }

    public function _getcategoryGallery($category_id = false)
    {
        return $this->_getGallery($category_id);
    }

    public function _getGalleryID($gallery_id = false)
    {
        return $this->db->where($this->_gallery_pk, $gallery_id)->get($this->_gallery_table)->row_array();
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

    public function insertGallery($_form = false)
    {
        $result = $this->db->insert($this->_gallery_table, $_form);

        if ($result) {
            return $this->db->insert_id();
        } else {
            return false;
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

    public function updateShowHome($id = false, $show_home = false)
    {
        $show_home_data = ['show_home' => $show_home];
        $result = $this->db->where($this->_pk, $id)->update($this->_table, $show_home_data);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function delete($id = null)
    {
        if ($id) {
            $result = $this->db->where($this->_pk, $id)->delete($this->_table);
            return $result ? 'true' : 'false';
        }
        return 'false';
    }

    public function deleteGallery($gallery_id = null)
    {
        $result = $this->db->where($this->_gallery_pk, $gallery_id)->delete($this->_gallery_table);

        if ($result) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function _getCountCategory()
    {
        return $this->db->order_by('category_sort', 'asc')->group_by('category_id')->where('product_index.product_status', 1)
            ->where('category_index.category_status', 1)
            ->join('category_index', 'category_index.category_id = product_index.category_id')
            ->select('category_index.category_id, category_index.category_name,category_index.category_img, COUNT(product_id) as count_product')
            ->get('product_index')->result_array();
    }
}
