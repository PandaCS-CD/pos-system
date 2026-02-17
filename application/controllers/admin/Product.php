<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

class Product extends Core_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/ProductModel', 'product');
        $this->load->model('admin/CategoryModel', 'category');
    }

    public function ajax_list()
    {
        $list = $this->product->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $product) {
            $no++;
            $row = array();

            // สถานะ
            if ($product['product_status'] == 1) {
                $bg_color = '#007f0f17';
                $border_color = '1px solid #d5d5d5';
                $product_status = 'เปิดใช้งาน';
            } else {
                $bg_color = '#ffeded';
                $border_color = '1px solid #d5d5d5';
                $product_status = 'ปิดใช้งาน';
            }

            // ปุ่ม
            $actions = '<div class="d-flex justify-content-center input-group input-group-edit px-2">';
            $actions .= '<a href="product/edit/' . $product['product_id'] . '" class="btn btn-warning btn-sm px-3"><i class="fas fa-edit"></i></a> ';
            $actions .= '<a type="button" class="btn btn-danger btn-sm px-3"
                                                            data-bs-toggle="modal" data-bs-target="#modalDel"
                                                            data-id="' . $product['product_id'] . '" 
                                                            data-url="' . admin_url('product/del/' . $product['product_id']) . '">
                                                            <i class="far fa-trash-alt"></i>
                                                        </a>';
            $actions .= '</div>';

            $status = '<div class="d-flex justify-content-center input-group px-2">';
            $status .= '<input type="hidden" name="id[]" value=" ' . $product['product_id'] . '">';
            $status .= '<input type="text" class="form-control form-control-sm input-order" name="order_data[]" value="' . $product['product_sort'] . '" inputmode="numeric">';
            if ($product['product_status'] == 1) {
                $status .= '<a href="' . admin_url('product/status/' . $product['product_id'] . '/0') . '" class="form-control btn btn-info btn-sm pt-2" title="Active"><i class="fa fa-desktop"></i></a>';
            } else {
                $status .= '<a href="' . admin_url('product/status/' . $product['product_id'] . '/1') . '" class="form-control btn btn-danger btn-sm pt-2" title="Inactive"><i class="fa fa-eye-slash"></i></a>';
            }
            $status .= '</div>';

            $row[] = '<div class="text-center"><input type="checkbox" class="form-check-input product-checkbox" value="' . $product['product_id'] . '"></div>';
            $row[] = '<div class="text-center">' . $no . '</div>';

            // รูปสินค้า
            $image = '';
            if (!empty($product['product_img'])) {
                $image = '<div class="thumb mb-0">
                        <a href="' . base_url('uploads/product/') . $product['product_img'] . '" class="img-link">
                            <img src="' . base_url('uploads/product/') . $product['product_img'] . '" class="img-fluid" style="max-width: 80px;">
                        </a>
                    </div>';
            } else {
                $image = '<div class="thumb mb-0">
                        <a href="' . base_url('assets/images/imgs/No_Image.jpg') . '" class="img-link">
                            <img src="' . base_url('assets/images/imgs/No_Image.jpg') . '" class="img-fluid" style="max-width: 80px;">
                        </a>
                    </div>';
            }
            $row[] = '<div class="text-center">' . $image . '</div>';

            // รายละเอียด
            $row[] = '<div class="text-center">' . ($product['product_code'] ?? '-') . '</div>';
            $row[] = '<div class="text-start">' . ($product['product_name'] ?? '-') . '</div>';
            $row[] = '<div class="text-center">' . number_format($product['product_price'] ?? 0) . ' ฿</div>';
            $row[] = '<div class="text-center">' . $status . '</div>';
            // $row[] = '<div class="text-center"><b>' . $product_status . '</b></div>';
            $row[] = '<div class="text-center">' . $actions . '</div>';

            // สไตล์
            $row['DT_RowStyle'] = array(
                'data-id' => $product['product_id'],
                'border' => $border_color
            );

            $data[] = $row;
            unset($row, $product);
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->product->count_all(),
            "recordsFiltered" => $this->product->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }


    public function index()
    {
        if ($this->input->post('order') == 'submit-order') {
            $id = $this->input->post('id');
            if ($id) {
                $order = $this->input->post('order_data');

                for ($i = 0; $i < count($id); $i++) {
                    $result = $this->product->updateOrder($order[$i], $id[$i]);
                }
                if ($result == 'true') {
                    $this->session->set_flashdata('result', $result);
                    $this->session->set_flashdata('message', 'อัพเดทข้อมูลสำเร็จ.');
                    redirect(admin_url('product'));
                } else if ($result == 'false') {
                    $this->session->set_flashdata('result', $result);
                    $this->session->set_flashdata('message', 'อัพเดทข้อมูลไม่สำเร็จ.');
                    redirect(admin_url('product'));
                }
            } else {
                redirect(admin_url('product'));
            }
        }

        $this->_data = [
            'title' => 'สินค้า',
            'menu_slug' => 'product',
            'script' => 'script_product',
            'content' => 'page_product'
        ];
        $this->_data['product'] = $this->product->_getProduct();
        $this->_data['categories'] = $this->category->_getCategory();
        $this->load->view('admin/index', $this->_data);
    }

    public function create()
    {
        // Only allow POST requests for form submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $category_id = ($this->input->post('category_id') ? $this->input->post('category_id') : null);
            $product_barcode = ($this->input->post('product_barcode') ? $this->input->post('product_barcode') : null);
            $product_code = ($this->input->post('product_code') ? $this->input->post('product_code') : null);
            $product_name = ($this->input->post('product_name') ? $this->input->post('product_name') : null);
            $product_cost = ($this->input->post('product_cost') !== null ? $this->input->post('product_cost') : 0);
            $product_price = ($this->input->post('product_price') !== null ? $this->input->post('product_price') : 0);
            $product_unit = ($this->input->post('product_unit') ? $this->input->post('product_unit') : 'ชิ้น');
            $product_stock = ($this->input->post('product_stock') !== null ? $this->input->post('product_stock') : 0);
            $product_stock_min = ($this->input->post('product_stock_min') !== null ? $this->input->post('product_stock_min') : 5);


            // --- 1. Handle main image upload ---
            $image = null;
            if (!empty($_FILES['product_img']['name'])) {
                $image = upload_fileFix('product_img', 800, 800, './uploads/product/');
            }

            // --- 3. Prepare data for insertion (รวมข้อมูล) ---
            $max_order = $this->product->_getMaxOrder();
            $numrow = $max_order['max_row'] + 1;

            $_form = [
                'category_id'       => $category_id,
                'product_barcode'   => $product_barcode,
                'product_code'      => $product_code,
                'product_name'      => $product_name,
                'product_cost'      => $product_cost,
                'product_price'     => $product_price,
                'product_unit'      => $product_unit,
                'product_stock'     => $product_stock,
                'product_stock_min' => $product_stock_min,
                'product_img'       => $image,
                'product_sort'      => $numrow,
                'product_status'    => 1,
            ];
            // --- 4. Insert into database ---
            $product_id = $this->product->insert($_form);


            if ($product_id) {
                $this->session->set_flashdata('result', 'true');
                $this->session->set_flashdata('message', 'เพิ่มข้อมูลสำเร็จ.');
                redirect(admin_url('product'));
            } else {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'เพิ่มข้อมูลไม่สำเร็จ.');
                redirect(admin_url('product'));
            }
            return;
        }

        // --- ส่วนการแสดงผล (View) ---
        $this->_data = [
            'title' => 'เพิ่มสินค้า',
            'menu_slug' => 'product',
            'script' => '',
            'content' => 'page_product_add'
        ];
        $this->_data['categories'] = $this->category->_getCategory();
        $this->load->view('admin/index', $this->_data);
    }

    public function edit($id = false)
    {
        // --- 1. ส่วนบันทึกข้อมูล (POST Request) ---
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $category_id = ($this->input->post('category_id') ? $this->input->post('category_id') : null);
            $product_barcode = ($this->input->post('product_barcode') ? $this->input->post('product_barcode') : null);
            $product_code = ($this->input->post('product_code') ? $this->input->post('product_code') : null);
            $product_name = ($this->input->post('product_name') ? $this->input->post('product_name') : null);
            $product_cost = ($this->input->post('product_cost') !== null ? $this->input->post('product_cost') : 0);
            $product_price = ($this->input->post('product_price') !== null ? $this->input->post('product_price') : 0);
            $product_unit = ($this->input->post('product_unit') ? $this->input->post('product_unit') : 'ชิ้น');
            $product_stock = ($this->input->post('product_stock') !== null ? $this->input->post('product_stock') : 0);
            $product_stock_min = ($this->input->post('product_stock_min') !== null ? $this->input->post('product_stock_min') : 5);

            // (โค้ดอัปโหลดรูปหลัก
            $image = $this->input->post('old_image');
            if (!empty($_FILES['product_img']['name'])) {
                $new_image = upload_fileFix('product_img', 800, 800, './uploads/product/');
                if ($new_image && $new_image !== '') {
                    if ($image && file_exists('./uploads/product/' . $image)) {
                        unlink('./uploads/product/' . $image);
                    }
                    $image = $new_image;
                } else {
                    $this->session->set_flashdata('result', 'false');
                    $this->session->set_flashdata('message', 'อัพโหลดรูปภาพไม่สำเร็จ.');
                    redirect(admin_url('product/edit/' . $id));
                    return;
                }
            }


            // --- 1.3 Prepare data for update ---
            $_form = [
                'category_id'       => $category_id,
                'product_barcode'   => $product_barcode,
                'product_code'      => $product_code,
                'product_name'      => $product_name,
                'product_cost'      => $product_cost,
                'product_price'     => $product_price,
                'product_unit'      => $product_unit,
                'product_stock'     => $product_stock,
                'product_stock_min' => $product_stock_min,
                'product_img'       => $image,
            ];



            $result = $this->product->update($_form, $id);

            if ($result == 'true') {
                $this->session->set_flashdata('result', $result);
                $this->session->set_flashdata('message', 'แก้ไขข้อมูลสำเร็จ.');
                redirect(admin_url('product'));
            } else {
                // (Handle error)
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'แก้ไขข้อมูลไม่สำเร็จ.');
                redirect(admin_url('product/edit/' . $id));
            }
            return;
        }


        $this->_data = [
            'title'     => 'แก้ไขสินค้า',
            'menu_slug' => 'product',
            'script'    => 'script_product',
            'content'   => 'page_product_edit'
        ];
        $this->_data['productID'] = $this->product->_getProductID($id);
        $this->_data['categories'] = $this->category->_getCategory();
        $this->load->view('admin/index', $this->_data);
    }
    public function del($id = false)
    {
        if ($id != null) {
            // ดึงข้อมูลสินค้าก่อนลบ (เพื่อเอาชื่อไฟล์รูป)
            $product = $this->product->_getProductID($id);


            $result = $this->product->delete($id);

            if ($result == 'true') {
                // ถ้าลบ DB สำเร็จ ค่อยลบไฟล์รูปจริง
                if (isset($product['product_img']) && $product['product_img'] && file_exists('./uploads/product/' . $product['product_img'])) {
                    unlink('./uploads/product/' . $product['product_img']);
                }

                $this->session->set_flashdata('result', $result);
                $this->session->set_flashdata('message', 'ลบข้อมูลสำเร็จ.');
                redirect(admin_url('product'));
            } else {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'ลบข้อมูลไม่สำเร็จ.');
                redirect(admin_url('product'));
            }
        } else {
            redirect(admin_url('product'));
        }
    }

    public function status($id = false, $active = false)
    {
        $result = $this->product->updateStatus($id, $active);

        if ($result == 'true') {
            $this->session->set_flashdata('result', $result);
            $this->session->set_flashdata('message', 'อัพเดทข้อมูลสำเร็จ.');
            redirect(admin_url('product'));
        } else {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'อัพเดทข้อมูลไม่สำเร็จ.');
            redirect(admin_url('product'));
        }
    }

    public function delete_selected()
    {
        $selected_ids = $this->input->post('selected_ids');

        if (!empty($selected_ids) && is_array($selected_ids)) {
            $success_count = 0;
            $fail_count = 0;

            foreach ($selected_ids as $id) {
                // ดึงข้อมูลสินค้าก่อนลบ (เพื่อเอาชื่อไฟล์รูป)
                $product = $this->product->_getProductID($id);

                // ลบข้อมูลออกจากฐานข้อมูล
                $result = $this->product->delete($id);

                if ($result == 'true') {
                    // ลบไฟล์รูปหลัก
                    if (isset($product['product_img']) && $product['product_img'] && file_exists('./uploads/product/' . $product['product_img'])) {
                        unlink('./uploads/product/' . $product['product_img']);
                    }
                    $success_count++;
                } else {
                    $fail_count++;
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

        redirect(admin_url('product'));
    }



    public function show_home($id = false, $show = false)
    {
        $result = $this->product->updateShowHome($id, $show);

        if ($result == 'true') {
            $this->session->set_flashdata('result', $result);
            $this->session->set_flashdata('message', 'อัพเดทข้อมูลสำเร็จ.');
            redirect(admin_url('product'));
        } else {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'อัพเดทข้อมูลไม่สำเร็จ.');
            redirect(admin_url('product'));
        }
    }

    // public function update_content()
    // {
    //     if ($this->input->post()) {
    //         $product_title = $this->input->post('product_title');
    //         $product_subtitle = $this->input->post('product_subtitle');

    //         $contact_data = $this->contact->_getContact();

    //         $_form = [
    //             'product_title' => $product_title,
    //             'product_subtitle' => $product_subtitle
    //         ];

    //         if ($contact_data) {
    //             // Update existing record
    //             $contact_id = $contact_data['contact_id'];
    //             $result = $this->contact->update($_form, $contact_id);
    //         } else {
    //             // Insert new record
    //             $result = $this->contact->insert($_form);
    //         }

    //         if ($result == 'true') {
    //             $this->session->set_flashdata('result', $result);
    //             $this->session->set_flashdata('message', 'บันทึกเนื้อหาส่วนสินค้าสำเร็จ.');
    //         } else {
    //             $this->session->set_flashdata('result', 'false');
    //             $this->session->set_flashdata('message', 'บันทึกเนื้อหาส่วนสินค้าไม่สำเร็จ.');
    //         }
    //     }
    //     redirect(admin_url('product'));
    // }
}
