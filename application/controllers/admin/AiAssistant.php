<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Core_Controller.php');

class AiAssistant extends Core_Controller
{
    public function __construct()
    {
        parent::__construct();

        // ตรวจสอบสิทธิ์ (ผู้จัดการหรือเจ้าของร้านเท่านั้น)
        $permission = isset($this->session->userdata('_auth')['admin_permission']) ? (int)$this->session->userdata('_auth')['admin_permission'] : 0;
        if ($permission < 1) {
            show_error('คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ เฉพาะผู้จัดการและเจ้าของร้านเท่านั้น', 403, 'Access Denied');
        }

        $this->load->model('admin/AiModel', 'ai_model');
        $this->load->library('Gemini_api');
    }

    /**
     * หน้าจอหลัก AI Business Advisor
     */
    public function index()
    {
        $this->_data['title'] = 'AI วิเคราะห์ธุรกิจอัจฉริยะ - POS';
        $this->_data['menu_slug'] = 'ai_assistant';
        $this->_data['script'] = 'script_ai_assistant';
        $this->_data['content'] = 'page_ai_assistant';

        // เช็คว่ามี API Key หรือยัง
        $this->_data['has_api_key'] = $this->gemini_api->has_api_key();
        $this->_data['masked_api_key'] = $this->_mask_key($this->gemini_api->get_api_key());

        // ข้อมูลสถิติต้นทางสำหรับแสดงผลเบื้องต้น
        $this->_data['today_context'] = $this->ai_model->get_pos_context('today');

        $this->load->view('admin/index', $this->_data);
    }

    /**
     * ซ่อน API Key บางส่วนเพื่อความปลอดภัย
     */
    private function _mask_key($key)
    {
        if (empty($key)) return '';
        $len = strlen($key);
        if ($len <= 8) return '****';
        return substr($key, 0, 4) . str_repeat('*', $len - 8) . substr($key, -4);
    }

    /**
     * บันทึก Gemini API Key
     */
    public function ajax_save_key()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $api_key = trim($this->input->post('api_key'));
        if (empty($api_key)) {
            echo json_encode(['success' => false, 'message' => 'กรุณากรอก API Key']);
            return;
        }

        // บันทึกลง database
        $this->ai_model->save_setting('gemini_api_key', $api_key);
        $this->gemini_api->set_api_key($api_key);

        echo json_encode([
            'success' => true,
            'message' => 'บันทึก API Key สำเร็จ พร้อมใช้งาน AI',
            'masked_key' => $this->_mask_key($api_key)
        ]);
    }

    /**
     * สร้างบทวิเคราะห์ภาพรวมธุรกิจเชิงลึก (Executive Insights)
     */
    public function ajax_get_insights()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $period = $this->input->post('period') ?: 'today';
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');

        // ดึงข้อมูล POS Context
        $context = $this->ai_model->get_pos_context($period, $date_from, $date_to);

        $system_prompt = "คุณคือ 'AI Business Advisor' ที่ปรึกษาผู้เชี่ยวชาญระดับสูงด้านการบริหารร้านค้าปลีกและวิเคราะห์ข้อมูล POS
ภารกิจของคุณคือ วิเคราะห์ข้อมูลสรุปยอดขาย กำไร สินค้า และสต๊อกที่ได้รับ เพื่อสังเคราะห์เป็น 'รายงานสรุปภาพรวมและกลยุทธ์เชิงลึก'
แนวทางการตอบ:
1. ใช้ภาษาไทยที่เข้าใจง่าย กระชับ ตรงประเด็น ทรงพลัง และมีความเป็นมืออาชีพ
2. จัดหมวดหมู่ชัดเจนโดยใช้หัวข้อ และ Bullet points พร้อม Emoji ที่เหมาะสม
3. โครงสร้างคำตอบต้องครอบคลุม 3 ส่วน:
   - 📊 **สรุปสุขภาพผลประกอบการ**: วิเคราะห์ยอดขายเทียบกับบิล, กำไรสุทธิ, มาร์จิ้น %, ค่าเฉลี่ยต่อบิล ชี้ให้เห็นว่าร้านกำลังเติบโตหรือมีจุดใดน่าสังเกต
   - ⚠️ **จุดที่ต้องเฝ้าระวัง & สต๊อก**: สินค้าที่ขายดีแต่สต๊อกวิกฤต หรือสินค้าที่เสี่ยงของขาด
   - 💡 **3 ข้อเสนอแนะเชิงกลยุทธ์ที่ทำได้ทันที**: เช่น โปรโมชั่นจับคู่สินค้า (Cross-selling), การบริหารช่วงเวลาขายทอง, หรือการดันยอดสินค้ากำไรสูง
4. อ้างอิงตัวเลขและชื่อสินค้าจากข้อมูลจริงที่ได้รับเท่านั้น ห้ามกุข้อมูลขึ้นเอง";

        $user_prompt = "ช่วยวิเคราะห์ข้อมูลของ {$context['shop_name']} สำหรับ {$context['period_label']} ต่อไปนี้อย่างละเอียด:\n\n" . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $response = $this->gemini_api->generate_content($user_prompt, $system_prompt);

        if ($response['success']) {
            echo json_encode([
                'success' => true,
                'insights' => $response['text'],
                'context'  => $context
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $response['message'] ?? 'ไม่สามารถสร้างบทวิเคราะห์ได้',
                'needs_key' => !empty($response['needs_key'])
            ]);
        }
    }

    /**
     * แชทถาม-ตอบกับ AI เกี่ยวกับข้อมูลร้านค้า
     */
    public function ajax_ask_chat()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $message = trim($this->input->post('message'));
        $period = $this->input->post('period') ?: 'today';
        $history_json = $this->input->post('history');
        $chat_history = !empty($history_json) ? json_decode($history_json, true) : [];

        if (empty($message)) {
            echo json_encode(['success' => false, 'message' => 'กรุณาระบุข้อความคำถาม']);
            return;
        }

        // ดึงข้อมูล Context ล่าสุดเพื่อแนบไปเป็นความรู้ปัจจุบัน
        $context = $this->ai_model->get_pos_context($period);

        $system_prompt = "คุณคือ 'AI Business Advisor' ผู้ช่วยอัจฉริยะส่วนตัวของเจ้าของร้านค้า POS ({$context['shop_name']})
คุณมีหน้าที่ตอบคำถาม ให้คำปรึกษา แนะนำการตั้งราคา โปรโมชั่น วิเคราะห์สินค้า และแก้ปัญหาการบริหารร้าน
นี่คือข้อมูลสถานะล่าสุดของร้านค้า ({$context['period_label']}):
" . json_encode($context, JSON_UNESCAPED_UNICODE) . "

กฎการตอบ:
1. ตอบเป็นภาษาไทยอย่างสุภาพ กระชับ ชัดเจน และมีหลักการเชิงธุรกิจค้าปลีก
2. ดึงข้อมูลตัวเลขและชื่อสินค้าจริงจาก context มาสนับสนุนเหตุผลเสมอ
3. หากถามคำแนะนำ ให้เสนอแนวทางที่สามารถนำไปทำตามได้จริงทันทีในร้าน";

        $response = $this->gemini_api->generate_content($message, $system_prompt, $chat_history);

        if ($response['success']) {
            echo json_encode([
                'success' => true,
                'reply' => $response['text']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $response['message'] ?? 'เกิดข้อผิดพลาดในการตอบคำถาม',
                'needs_key' => !empty($response['needs_key'])
            ]);
        }
    }

    /**
     * Quick summary สำหรับ Dashboard Card
     */
    public function ajax_get_dashboard_card()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $context = $this->ai_model->get_pos_context('today');

        $system_prompt = "คุณคือ AI ผู้ช่วยสรุปผลงานร้านค้า POS ประจำวัน
ให้สรุปภาพรวมวันนี้เป็นข้อความสั้นๆ สวยงาม ไม่เกิน 3-4 บรรทัด (Bullet points) ประกอบด้วย:
1. จุดเด่นยอดขาย/สินค้าขายดีวันนี้
2. แจ้งเตือนสินค้าสต๊อกต่ำที่ต้องรีบเติม
3. 1 คำแนะนำสั้นๆ สำหรับช่วงเวลาที่เหลือของวัน
ใช้ภาษาไทยกระชับ เป็นมิตร มี Emoji";

        $user_prompt = "สรุปข้อมูลวันนี้แบบรวดเร็ว:\n" . json_encode($context, JSON_UNESCAPED_UNICODE);

        $response = $this->gemini_api->generate_content($user_prompt, $system_prompt);

        if ($response['success']) {
            echo json_encode([
                'success' => true,
                'summary' => $response['text']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $response['message'] ?? 'ไม่สามารถดึงข้อมูลได้',
                'needs_key' => !empty($response['needs_key'])
            ]);
        }
    }
}
