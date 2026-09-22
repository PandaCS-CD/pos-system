<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Gemini_api Library
 * สำหรับเชื่อมต่อ Google Gemini REST API เพื่อวิเคราะห์ข้อมูลเชิงลึก
 */
class Gemini_api
{
    protected $ci;
    protected $api_key;
    protected $model;
    protected $base_url = 'https://generativelanguage.googleapis.com/v1beta/models/';
    protected $candidate_models = ['gemini-3.6-flash', 'gemini-flash-latest', 'gemini-3.8-flash'];

    public function __construct($params = [])
    {
        $this->ci = &get_instance();
        $this->ci->load->config('config');

        // อ่าน API Key จาก params -> database (ai_settings) -> config -> getenv
        $this->api_key = !empty($params['api_key']) ? $params['api_key'] : $this->_get_stored_api_key();
        $configured_model = $this->ci->config->item('gemini_model');
        
        // ถ้าเป็นรุ่นเก่าที่ Google เลิกให้บริการ ให้ปรับเป็น gemini-3.6-flash อัตโนมัติ
        if (empty($configured_model) || in_array($configured_model, ['gemini-1.5-flash', 'gemini-2.5-flash', 'gemini-pro'])) {
            $this->model = 'gemini-3.6-flash';
        } else {
            $this->model = $configured_model;
        }

        if (!empty($params['model'])) {
            $this->model = $params['model'];
        }
    }

    /**
     * ดึง API key จากตาราง ai_settings หรือ config
     */
    protected function _get_stored_api_key()
    {
        if ($this->ci->db->table_exists('ai_settings')) {
            $row = $this->ci->db->where('setting_key', 'gemini_api_key')->get('ai_settings')->row_array();
            if (!empty($row['setting_value'])) {
                return trim($row['setting_value']);
            }
        }
        $config_key = $this->ci->config->item('gemini_api_key');
        if (!empty($config_key)) {
            return trim($config_key);
        }
        return getenv('GEMINI_API_KEY') ?: '';
    }

    public function set_api_key($key)
    {
        $this->api_key = trim($key);
        return $this;
    }

    public function get_api_key()
    {
        return $this->api_key;
    }

    public function has_api_key()
    {
        return !empty($this->api_key);
    }

    /**
     * ส่งคำร้องขอไปยัง Gemini API พร้อมระบบ Auto-fallback หากโมเดลเก่าใช้งานไม่ได้
     *
     * @param string $user_prompt ข้อความคำสั่งหรือคำถาม
     * @param string $system_instruction คำแนะนำบทบาทของ AI
     * @param array $chat_history ประวัติการสนทนาก่อนหน้า (optional)
     * @return array [success => bool, message => string, text => string, error_detail => string]
     */
    public function generate_content($user_prompt, $system_instruction = '', $chat_history = [])
    {
        if (empty($this->api_key)) {
            return [
                'success' => false,
                'message' => 'ยังไม่ได้ระบุ Gemini API Key กรุณาตั้งค่า API Key เพื่อเริ่มใช้งาน AI',
                'needs_key' => true
            ];
        }

        $models_to_try = array_unique(array_merge([$this->model], $this->candidate_models));
        $last_error = '';

        foreach ($models_to_try as $current_model) {
            $result = $this->_call_api($current_model, $user_prompt, $system_instruction, $chat_history);
            
            if ($result['success']) {
                // บันทึกโมเดลที่ทำงานได้สำเร็จ
                $this->model = $current_model;
                return $result;
            }

            // ถ้าเป็น error 404 (model not found / no longer available) ให้ลองโมเดลถัดไป
            if (!empty($result['is_model_unavailable'])) {
                $last_error = $result['message'];
                continue;
            }

            // ถ้าเป็น error อื่นๆ เช่น invalid key, quota limit ให้ส่งออกทันที
            return $result;
        }

        return [
            'success' => false,
            'message' => $last_error ?: 'ไม่พบโมเดลที่สามารถตอบสนองคำสั่งได้ กรุณาลองใหม่อีกครั้ง'
        ];
    }

    /**
     * ส่ง HTTP cURL ไปยัง Google Gemini API
     */
    protected function _call_api($model_name, $user_prompt, $system_instruction = '', $chat_history = [])
    {
        $url = $this->base_url . urlencode($model_name) . ':generateContent?key=' . $this->api_key;

        $contents = [];

        // ใส่ chat history ถ้ามี
        if (!empty($chat_history) && is_array($chat_history)) {
            foreach ($chat_history as $msg) {
                if (!empty($msg['role']) && !empty($msg['content'])) {
                    $role = ($msg['role'] === 'user') ? 'user' : 'model';
                    $contents[] = [
                        'role' => $role,
                        'parts' => [['text' => (string)$msg['content']]]
                    ];
                }
            }
        }

        // เพิ่มคำถามล่าสุดของผู้ใช้
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => (string)$user_prompt]]
        ];

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048
            ]
        ];

        if (!empty($system_instruction)) {
            $payload['system_instruction'] = [
                'parts' => [['text' => (string)$system_instruction]]
            ];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        // ในสภาพแวดล้อม Local เช่น MAMP / XAMPP บางครั้ง CA Bundle ไม่สมบูรณ์ จึงอนุญาตให้เชื่อมต่อได้
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err = curl_error($ch);
        curl_close($ch);

        if ($curl_err) {
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อไปยังเซิร์ฟเวอร์ AI: ' . $curl_err
            ];
        }

        $result = json_decode($response, true);

        if ($http_code !== 200) {
            $err_msg = isset($result['error']['message']) ? $result['error']['message'] : 'HTTP Error ' . $http_code;

            // ตรวจจับกรณีโมเดลไม่มีอยู่หรือถูกยกเลิก (404 หรือ message แจ้ง model not found)
            if ($http_code === 404 || stripos($err_msg, 'not found') !== false || stripos($err_msg, 'no longer available') !== false) {
                return [
                    'success' => false,
                    'is_model_unavailable' => true,
                    'message' => "โมเดล {$model_name} ไม่พร้อมใช้งาน: " . $err_msg
                ];
            }
            
            // ตรวจจับข้อผิดพลาดคีย์ไม่ถูกต้อง
            if ($http_code === 400 && stripos($err_msg, 'API_KEY_INVALID') !== false) {
                return [
                    'success' => false,
                    'message' => 'Gemini API Key ไม่ถูกต้อง กรุณาตรวจสอบคีย์ที่กรอกใหม่อีกครั้ง',
                    'needs_key' => true
                ];
            }

            // ตรวจจับโควตา
            if ($http_code === 429) {
                return [
                    'success' => false,
                    'message' => 'โควตาการเรียกใช้งาน Gemini API ชั่วคราวเต็ม (Rate Limit) กรุณารอสักครู่แล้วลองใหม่'
                ];
            }

            return [
                'success' => false,
                'message' => 'AI ตอบกลับไม่สำเร็จ: ' . $err_msg,
                'http_code' => $http_code
            ];
        }

        if (!empty($result['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $result['candidates'][0]['content']['parts'][0]['text'];
            return [
                'success' => true,
                'text' => trim($text)
            ];
        }

        return [
            'success' => false,
            'message' => 'ไม่พบข้อความตอบกลับจาก AI'
        ];
    }
}
