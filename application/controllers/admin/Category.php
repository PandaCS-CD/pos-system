<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

class Category extends Core_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/CategoryModel', 'category');
    }

    public function index()
    {
        if ($this->input->post('order') == 'submit-order') {
            $id = $this->input->post('id');
            if ($id) {
                $order = $this->input->post('order_data');

                for ($i = 0; $i < count($id); $i++) {
                    $result = $this->category->updateOrder($order[$i], $id[$i]);
                }
                if ($result == 'true') {
                    $this->session->set_flashdata('result', $result);
                    $this->session->set_flashdata('message', 'อัพเดทข้อมูลสำเร็จ.');
                    redirect(admin_url('category'));
                } else if ($result == 'false') {
                    $this->session->set_flashdata('result', $result);
                    $this->session->set_flashdata('message', 'อัพเดทข้อมูลไม่สำเร็จ.');
                    redirect(admin_url('category'));
                }
            } else {
                redirect(admin_url('category'));
            }
        }

        $this->_data = [
            'title' => 'หมวดหมู่',
            'menu_slug' => 'category',
            'script' => 'script_category',
            'content' => 'page_category'
        ];
        $this->_data['category'] = $this->category->_getCategory();
        $this->load->view('admin/index', $this->_data);
    }



    public function create()
    {
        // Only allow POST requests for form submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $category_name = ($this->input->post('category_name') ? $this->input->post('category_name') : null);

            $category_meta = ($this->input->post('category_meta') ? $this->input->post('category_meta') : null);

            $image = null;
            if (!empty($_FILES['category_img']['name'])) {
                $image = upload_fileFix('category_img', 500, 500, './uploads/category/');
            }

            $max_order = $this->category->_getMaxOrder();
            $numrow = $max_order['max_row'] + 1;

            $_form = [
                'category_name'     => $category_name,
                'category_img'     => $image,
                'category_meta'    => $category_meta,
                'category_sort' => $numrow,
                'category_status'     => 1,
            ];

            $category_id = $this->category->insert($_form);

            if ($category_id) {
                $this->session->set_flashdata('result', 'true');
                $this->session->set_flashdata('message', 'เพิ่มข้อมูลสำเร็จ.');
                redirect(admin_url('category'));
            } else {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'เพิ่มข้อมูลไม่สำเร็จ.');
                redirect(admin_url('category'));
            }
            return;
        }

        // --- ส่วนการแสดงผล (View) ---
        $this->_data = [
            'title' => 'เพิ่มหมวดหมู่',
            'menu_slug' => 'category',
            'script' => '',
            'content' => 'page_category_add'
        ];
        $this->load->view('admin/index', $this->_data);
    }

    public function edit($id = false)
    {
        // --- 1. ส่วนบันทึกข้อมูล (POST Request) ---
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $category_name = ($this->input->post('category_name') ? $this->input->post('category_name') : null);

            $category_meta = ($this->input->post('category_meta') ? $this->input->post('category_meta') : null);

            $image = $this->input->post('old_image');
            if (!empty($_FILES['category_img']['name'])) {
                $new_image = upload_fileFix('category_img', 500, 500, './uploads/category/');
                if ($new_image && $new_image !== '') {
                    if ($image && file_exists('./uploads/category/' . $image)) {
                        unlink('./uploads/category/' . $image);
                    }
                    $image = $new_image;
                } else {
                    $this->session->set_flashdata('result', 'false');
                    $this->session->set_flashdata('message', 'อัพโหลดรูปภาพไม่สำเร็จ.');
                    redirect(admin_url('category/edit/' . $id));
                    return;
                }
            }

            // --- 1.3 Prepare data for update ---
            $_form = [
                'category_img'   => $image,
                'category_name'   => $category_name,
                'category_meta'  => $category_meta,

            ];

            $result = $this->category->update($_form, $id);

            if ($result == 'true') {
                $this->session->set_flashdata('result', $result);
                $this->session->set_flashdata('message', 'แก้ไขข้อมูลสำเร็จ.');
                redirect(admin_url('category'));
            } else {
                // (Handle error)
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'แก้ไขข้อมูลไม่สำเร็จ.');
                redirect(admin_url('category/edit/' . $id));
            }
            return;
        }


        $this->_data = [
            'title'     => 'แก้ไขหมวดหมู่',
            'menu_slug' => 'category',
            'script'    => '',
            'content'   => 'page_category_edit'
        ];
        $category_data = $this->category->_getcategoryID($id);

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (empty($category_data)) {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'ไม่พบข้อมูลหมวดหมู่');
            redirect(admin_url('category'));
            return;
        }

        $gallery_array_of_strings = !empty($category_data['gallery']) ? json_decode($category_data['gallery'], true) : [];
        $gallery_images_for_view = [];
        if (!empty($gallery_array_of_strings) && is_array($gallery_array_of_strings)) {
            foreach ($gallery_array_of_strings as $index => $filename) {
                $gallery_images_for_view[] = [
                    'image'      => $filename,
                    'gallery_id' => $index
                ];
            }
        }

        // echo  '<pre>';
        // print_r($index);
        // echo  '<br>';
        // print_r($filename);
        // echo  '</pre>';
        // exit;
        $this->_data['categoryID'] = $category_data;
        $this->_data['categoryID']['gallery_images'] = $gallery_images_for_view;
        $this->load->view('admin/index', $this->_data);
    }
    public function del($id = false)
    {
        if ($id != null) {

            //ดึงข้อมูลหมวดหมู่ (รวมถึง JSON)
            // $category จะมีทั้ง 'image' (รูปหลัก) และ 'gallery' (JSON string)
            $category = $this->category->_getcategoryID($id);



            //ลบข้อมูลออกจากฐานข้อมูล
            $result = $this->category->delete($id);



            if ($result == 'true') {
                //ถ้าลบ DB สำเร็จ ค่อยลบไฟล์จริง
                if ($category['category_img'] && file_exists('./uploads/category/' . $category['category_img'])) {
                    unlink('./uploads/category/' . $category['category_img']);
                }
                //ตั้งค่า Flash message และ Redirect
                $this->session->set_flashdata('result', $result);
                $this->session->set_flashdata('message', 'ลบข้อมูลสำเร็จ.');
                redirect(admin_url('category'));
            } else {
                // (กรณีลบ DB ไม่สำเร็จ)
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'ลบข้อมูลไม่สำเร็จ.');
                redirect(admin_url('category'));
            }
        } else {
            redirect(admin_url('category'));
        }
    }

    public function status($id = false, $active = false)
    {
        $result = $this->category->updateStatus($id, $active);

        if ($result == 'true') {
            $this->session->set_flashdata('result', $result);
            $this->session->set_flashdata('message', 'อัพเดทข้อมูลสำเร็จ.');
            redirect(admin_url('category'));
        } else {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'อัพเดทข้อมูลไม่สำเร็จ.');
            redirect(admin_url('category'));
        }
    }


    public function delete_selected()
    {
        $selected_ids = $this->input->post('selected_ids');

        if (!empty($selected_ids) && is_array($selected_ids)) {
            $success_count = 0;
            foreach ($selected_ids as $id) {
                // ดึงข้อมูลหมวดหมู่เพื่อลบรูปภาพ
                $category = $this->category->_getcategoryID($id);

                $result = $this->category->delete($id);

                if ($result == 'true') {
                    // ลบไฟล์รูปภาพ
                    if (!empty($category['category_img']) && file_exists('./uploads/category/' . $category['category_img'])) {
                        @unlink('./uploads/category/' . $category['category_img']);
                    }
                    $success_count++;
                }
            }

            if ($success_count > 0) {
                $this->session->set_flashdata('result', 'true');
                $this->session->set_flashdata('message', 'ลบข้อมูลสำเร็จ ' . $success_count . ' รายการ');
            } else {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'ลบข้อมูลไม่สำเร็จ');
            }
        } else {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'กรุณาเลือกรายการที่ต้องการลบ');
        }

        redirect(admin_url('category'));
    }
}
