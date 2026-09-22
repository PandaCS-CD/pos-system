<?php

defined('BASEPATH') or exit('No direct script access allowed');

function upload_fileFix($pic, $w, $h, $path)
{
    $CI = &get_instance();
    // Set your config up
    $config['upload_path'] = $path;
    $config['allowed_types'] = '*';
    $config['file_name'] = $pic;

    $CI->load->library('upload', $config);
    $CI->upload->initialize($config);

    if ($CI->upload->do_upload($pic)) {
        $data =  $CI->upload->data();
        $name_pic =  date("YmdHis") . '_' . gen_namepic(5);
        rename($data['full_path'], $data['file_path'] . $name_pic . $data['file_ext']);
        $config['image_library']    = "gd2";
        $config['source_image']     = $data['file_path'] . $name_pic . $data['file_ext'];
        $config['create_thumb']     = TRUE;
        $config['maintain_ratio']   = false;
        $config['new_image'] = $data['file_path'] . $name_pic . $data['file_ext'];
        $config['width'] = $w;
        $config['height'] = $h;
        $config['thumb_marker'] = FALSE;
        $config['quality'] = '75%';
        $CI->load->library('image_lib');
        $CI->image_lib->initialize($config);
        // Do your manipulation
        if (!$CI->image_lib->resize()) {
            $picname = '';
            echo  $CI->upload->display_errors();
        }
        $CI->image_lib->clear();

        $picname = $name_pic . $data['file_ext']; // ชื่อรูปภาพ

    } else {
        $picname = '';
        echo  $CI->upload->display_errors();
    }

    return $picname;
}


function upload_file($pic, $w, $h, $path)
{
    $CI = &get_instance();
    // Set your config up
    $config['upload_path'] = $path;
    $config['allowed_types'] = '*';
    $config['file_name'] = $pic;

    $CI->load->library('upload', $config);
    $CI->upload->initialize($config);

    if ($CI->upload->do_upload($pic)) {
        $data =  $CI->upload->data();
        $name_pic =  date("YmdHis") . '_' . gen_namepic(5);
        rename($data['full_path'], $data['file_path'] . $name_pic . $data['file_ext']);
        $config['image_library']    = "gd2";
        $config['source_image']     = $data['file_path'] . $name_pic . $data['file_ext'];
        $config['create_thumb']     = TRUE;
        $config['maintain_ratio']   = TRUE;
        $config['new_image'] = $data['file_path'] . $name_pic . $data['file_ext'];
        $config['width'] = $w;
        $config['height'] = $h;
        $config['thumb_marker'] = FALSE;
        $config['quality'] = '75%';
        $CI->load->library('image_lib');
        $CI->image_lib->initialize($config);
        // Do your manipulation
        if (!$CI->image_lib->resize()) {
            $picname = '';
            echo  $CI->upload->display_errors();
        }
        $CI->image_lib->clear();

        $picname = $name_pic . $data['file_ext']; // ชื่อรูปภาพ

    } else {
        $picname = '';
        echo  $CI->upload->display_errors();
    }

    return $picname;
}

if (!function_exists('upload_file_array')) {
    function upload_file_array($input_name, $max_files, $w, $h, $path)
    {
        $picname = [];

        $CI = &get_instance();

        // ตรวจสอบ path หากยังไม่มีให้สร้าง
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $files = $_FILES[$input_name];
        $count = count($files['name']);

        // จำกัดตาม $max_files
        $limit = min($count, $max_files);

        for ($i = 0; $i < $limit; $i++) {
            if (empty($files['name'][$i])) {
                continue; // ข้ามไฟล์ที่ไม่มีชื่อ
            }

            $_FILES['nameUpload']['name']     = $files['name'][$i];
            $_FILES['nameUpload']['type']     = $files['type'][$i];
            $_FILES['nameUpload']['tmp_name'] = $files['tmp_name'][$i];
            $_FILES['nameUpload']['error']    = $files['error'][$i];
            $_FILES['nameUpload']['size']     = $files['size'][$i];

            $config['upload_path']   = $path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
            $config['file_name']     = uniqid('img_');
            $config['overwrite']     = false;

            $CI->load->library('upload');
            $CI->upload->initialize($config);

            if ($CI->upload->do_upload('nameUpload')) {
                $data = $CI->upload->data();

                // ตั้งชื่อใหม่ให้เป็นชื่อแบบ timestamp + สุ่ม
                $name_pic = date("YmdHis") . '_' . gen_namepic(5);
                $new_path = $data['file_path'] . $name_pic . $data['file_ext'];

                if (!rename($data['full_path'], $new_path)) {
                    log_message('error', "❌ Rename failed: " . $data['full_path'] . " → " . $new_path);
                    continue; // ข้ามไฟล์นี้
                }

                // Resize ภาพ
                $CI->load->library('image_lib');
                $resize_config = [
                    'image_library'  => 'gd2',
                    'source_image'   => $new_path,
                    'create_thumb'   => false,
                    'maintain_ratio' => true,
                    'width'          => $w,
                    'height'         => $h,
                    'quality'        => '75%',
                ];

                $CI->image_lib->initialize($resize_config);

                if (!$CI->image_lib->resize()) {
                    log_message('error', "❌ Resize failed: " . $CI->image_lib->display_errors());
                    // ไม่ต้อง return false, แค่ข้าม
                }

                $CI->image_lib->clear();
                gc_collect_cycles(); // เคลียร์ memory ที่สะสมจาก GD image lib

                $picname[] = $name_pic . $data['file_ext'];
            } else {
                log_message('error', "❌ Upload failed: " . $CI->upload->display_errors());
            }
        }

        return $picname; // คืน array เสมอ
    }
}
