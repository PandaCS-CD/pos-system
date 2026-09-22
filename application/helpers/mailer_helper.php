<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ========================================
 * การตั้งค่า SMTP เริ่มต้น
 * ========================================
 */
if (!function_exists('get_default_smtp_config')) {
    function get_default_smtp_config()
    {
        return [
            'host'     => getenv('SMTP_HOST') ?: 'mail.ayaservice.com',
            'username' => getenv('SMTP_USERNAME') ?: 'sysytem@ayaservice.com',
            'password' => getenv('SMTP_PASSWORD') ?: '',
            'port'     => getenv('SMTP_PORT') ? (int) getenv('SMTP_PORT') : 587,
            // 'secure'   => 'tls'
        ];
    }
}

/**
 * ========================================
 * ข้อมูลผู้ส่งเริ่มต้น
 * ========================================
 */
if (!function_exists('get_default_sender')) {
    function get_default_sender()
    {
        return [
            'email' => getenv('MAIL_FROM_EMAIL') ?: 'sysytem@ayaservice.com',
            'name'  => getenv('MAIL_FROM_NAME') ?: 'AYA-SERVICE'
        ];
    }
}

/**
 * ========================================
 * การตั้งค่า SMTP สำหรับ MailHog (Local Testing)
 * ========================================
 */
if (!function_exists('get_mailhog_smtp_config')) {
    function get_mailhog_smtp_config()
    {
        return [
            'host'     => 'localhost',
            'username' => '',
            'password' => '',
            'port'     => 1025,
            'secure'   => false
        ];
    }
}

/**
 * ฟังก์ชันส่งอีเมลแบบง่าย
 * @param array|null $reply_to ['email' => '', 'name' => ''] (optional)
 * @param array|null $to ผู้รับ [['email' => '', 'name' => '']] (optional - default: sales@wearekkf.com)
 * @param bool $use_mailhog ใช้ MailHog สำหรับทดสอบ (optional)
 * @return bool|string true เมื่อสำเร็จ, error message เมื่อล้มเหลว
 * 
 * ตัวอย่าง: send_email('New Message', 'contact',  ['email' => 'john@example.com', 'name' => 'John'], $data);
 */
if (!function_exists('send_email')) {
    function send_email($subject, $view, $to, $data, $use_mailhog = false)
    {
        $CI = &get_instance();
        require_once(APPPATH . 'libraries/PHPMailer/class.phpmailer.php');

        if (empty($view) || empty($subject)) {
            return 'Error: View and subject are required';
        }

        $mail = new PHPMailer();
        $smtp = $use_mailhog ? get_mailhog_smtp_config() : get_default_smtp_config();

        $mail->CharSet = "utf-8";
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'];
        $mail->Password = $smtp['password'];
        $mail->Port = $smtp['port'];

        if (!empty($smtp['secure'])) {
            $mail->SMTPSecure = $smtp['secure'];
        }

        $default_sender = get_default_sender();
        $mail->setFrom($default_sender['email'], $default_sender['name']);
        $mail->addReplyTo($default_sender['email'], $default_sender['name']);

        // ===== Add recipients (FIXED) =====
        if (is_string($to)) {

            $mail->addAddress($to);
        } elseif (is_array($to)) {
            foreach ($to as $recipient) {
                if (is_array($recipient) && isset($recipient['email'])) {
                    $mail->addAddress(
                        $recipient['email'],
                        isset($recipient['name']) ? $recipient['name'] : ''
                    );
                } elseif (is_array($recipient) && isset($recipient[0])) {
                    $mail->addAddress($recipient[0]);
                } elseif (is_string($recipient)) {
                    $mail->addAddress($recipient);
                }
            }
        }

        $mail->Subject = $use_mailhog ? '[TEST] ' . $subject : $subject;

        $body = $CI->load->view('tpl/' . $view, $data, true);
        $mail->MsgHTML($body);

        if ($mail->send()) {
            return true;
        }

        return 'Error: ' . $mail->ErrorInfo;
    }
}


/**
 * ส่งอีเมลทดสอบผ่าน MailHog
 */
if (!function_exists('send_test_email')) {
    function send_test_email($subject, $view, $data)
    {
        return send_email($subject, $view, 'test@example.com', $data, true);
    }
}
